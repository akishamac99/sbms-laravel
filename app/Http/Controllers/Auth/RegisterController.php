<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\SystemLog;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function __construct(private OtpService $otp)
    {
    }

    // Step 1 shows the form. Step 2 (after a code was sent) shows the code box.
    public function show(Request $request)
    {
        $step = $request->session()->has('reg') ? 2 : 1;

        return view('auth.register', [
            'step' => $step,
            'email' => $request->session()->get('reg.email'),
        ]);
    }

    public function sendOtp(Request $request)
    {
        $request->merge(['email' => Str::lower(trim((string) $request->input('email')))]);

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:100', 'unique:customer,email'],
            'contact_number' => ['required', 'string', 'max:20', 'unique:customer,contactNumber'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'email.unique' => 'Email already registered',
            'contact_number.unique' => 'Contact number already registered',
        ]);

        $code = $this->otp->generate($data['email'], 'registration');

        try {
            $this->otp->send($data['email'], $code);
        } catch (\Throwable $e) {
            report($e);

            return back()->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['email' => 'OTP could not be sent to your email. Please try again.']);
        }

        // Only a hash of the password is kept in the session.
        $request->session()->put('reg', [
            'firstName' => $data['first_name'],
            'lastName' => $data['last_name'],
            'email' => $data['email'],
            'contactNumber' => $data['contact_number'],
            'password' => Hash::make($data['password']),
        ]);

        return redirect()->route('register')->with('status', 'OTP sent to your email. Please verify below.');
    }

    public function verify(Request $request)
    {
        $reg = $request->session()->get('reg');

        if (! $reg) {
            return redirect()->route('register')
                ->withErrors(['otp_code' => 'Registration session expired. Please register again.']);
        }

        $request->validate(['otp_code' => ['required', 'digits:6']]);

        $result = $this->otp->verify($reg['email'], $request->input('otp_code'));

        if (! $result['success']) {
            return back()->withErrors(['otp_code' => $result['message']]);
        }

        $customer = Customer::create([
            'firstName' => $reg['firstName'],
            'lastName' => $reg['lastName'],
            'email' => $reg['email'],
            'contactNumber' => $reg['contactNumber'],
            'password' => $reg['password'],   // already hashed; the model will not hash it twice
            'accountStatus' => 'active',
            'is_verified' => 1,
        ]);

        SystemLog::record('customer', $customer->getKey(), 'register', 'Customer registered via email OTP');
        $request->session()->forget('reg');

        return redirect()->route('login')->with('status', 'Registration complete. Please sign in with your email.');
    }

    public function resend(Request $request)
    {
        $reg = $request->session()->get('reg');

        if (! $reg) {
            return redirect()->route('register');
        }

        try {
            $this->otp->send($reg['email'], $this->otp->generate($reg['email'], 'registration'));
        } catch (\Throwable $e) {
            report($e);

            return back()->withErrors(['otp_code' => 'OTP could not be sent. Please try again.']);
        }

        return back()->with('status', 'A new OTP has been sent.');
    }

    public function reset(Request $request)
    {
        $request->session()->forget('reg');

        return redirect()->route('register');
    }
}
