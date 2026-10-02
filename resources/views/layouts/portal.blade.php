@php
    $staffUser = auth('staff')->user();
    $customerUser = auth('customer')->user();
    $role = $staffUser ? $staffUser->role_slug : 'customer';
    $account = $staffUser ?? $customerUser;
    $roleLabel = [
        'owner' => 'Owner Portal',
        'manager' => 'Manager Portal',
        'staff' => 'Staff Portal',
        'customer' => 'Customer Portal',
    ][$role];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $roleLabel) - {{ config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    @vite(['resources/css/style.css', 'resources/js/app.js'])
</head>
<body>
<div class="dashboard-container">
    <div class="sidebar">
        <div class="brand">
            <div class="brand-name">Janet Angel's Beauty Salon</div>
            <div class="brand-role">{{ $roleLabel }}</div>
        </div>
        <nav class="nav-menu">
            @foreach (\App\Support\Nav::for($role) as $item)
                @if (Route::has($item['route']))
                    <a href="{{ route($item['route']) }}" class="nav-item {{ request()->routeIs($item['route']) ? 'active' : '' }}">
                        <i class="fas {{ $item['icon'] }}"></i> <span>{{ $item['label'] }}</span>
                    </a>
                @else
                    <span class="nav-item" style="opacity:.45;cursor:default" title="Coming in a later phase">
                        <i class="fas {{ $item['icon'] }}"></i> <span>{{ $item['label'] }}</span>
                    </span>
                @endif
            @endforeach
        </nav>
        <div class="sidebar-footer">
            <div class="user-info">
                <span>{{ $account->full_name }}</span>
                <small>{{ ucfirst($role) }}</small>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn" style="border:0;cursor:pointer;width:100%">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
    </div>

    <main class="main-content owner-stack">
        @if (session('status'))
            <div style="background:var(--success-bg);color:var(--success);padding:12px 16px;border-radius:10px">{{ session('status') }}</div>
        @endif
        @if (session('error'))
            <div style="background:var(--error-bg);color:var(--error);padding:12px 16px;border-radius:10px">{{ session('error') }}</div>
        @endif

        @yield('content')
    </main>
</div>
</body>
</html>
