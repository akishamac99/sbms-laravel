<?php

namespace App\Support;

use App\Models\Permission;

class Nav
{
    // Sidebar links for each role. Each item is: [label, route name ending, icon, manager permission module].
    // A link whose route does not exist yet is shown greyed out, and switches on by itself
    // once you add a route with that name.
    public static function for(string $role): array
    {
        $all = [
            'owner' => [
                ['Dashboard', 'dashboard', 'fa-chart-line', null],
                ['Manage Bookings', 'bookings', 'fa-calendar', null],
                ['Walk-In Booking', 'walkin', 'fa-user-plus', null],
                ['Payments', 'payments', 'fa-credit-card', null],
                ['Reports', 'reports', 'fa-chart-bar', null],
                ['Attendance', 'attendance', 'fa-check-circle', null],
                ['Customers', 'customers', 'fa-users', null],
                ['Services', 'services', 'fa-scissors', null],
                ['Staff', 'staff', 'fa-id-card', null],
                ['Staff Availability', 'staff-availability', 'fa-clock', null],
                ['Promos', 'promos', 'fa-tags', null],
                ['Policies', 'policies', 'fa-gavel', null],
                ['Permissions', 'permissions', 'fa-user-shield', null],
                ['System Logs', 'logs', 'fa-list', null],
                ['My Profile', 'profile', 'fa-user', null],
            ],
            'manager' => [
                ['Dashboard', 'dashboard', 'fa-chart-line', null],
                ['Manage Bookings', 'bookings', 'fa-calendar', 'bookings'],
                ['Walk-In Booking', 'walkin', 'fa-user-plus', 'walkin'],
                ['Payments', 'payments', 'fa-credit-card', 'payments'],
                ['Reports', 'reports', 'fa-chart-bar', 'reports'],
                ['Attendance', 'attendance', 'fa-check-circle', 'attendance'],
                ['Customers', 'customers', 'fa-users', 'customers'],
                ['Services', 'services', 'fa-scissors', 'services'],
                ['Staff Directory', 'staff', 'fa-id-card', 'staff'],
                ['My Profile', 'profile', 'fa-user', null],
            ],
            'staff' => [
                ['Dashboard', 'dashboard', 'fa-chart-line', null],
                ['My Bookings', 'bookings', 'fa-calendar', null],
                ['Walk-In Booking', 'walkin', 'fa-user-plus', null],
                ['Attendance', 'attendance', 'fa-check-circle', null],
                ['My Availability', 'availability', 'fa-clock', null],
                ['My Profile', 'profile', 'fa-user', null],
            ],
            'customer' => [
                ['Dashboard', 'dashboard', 'fa-home', null],
                ['Services', 'services', 'fa-scissors', null],
                ['My Bookings', 'bookings', 'fa-calendar', null],
                ['Notifications', 'notifications', 'fa-bell', null],
                ['My Profile', 'profile', 'fa-user', null],
            ],
        ];

        return collect($all[$role] ?? [])
            // Managers only see the sections the owner has allowed them to view.
            ->filter(fn ($item) => $role !== 'manager'
                || $item[3] === null
                || Permission::allows('Manager', $item[3], 'view'))
            ->map(fn ($item) => [
                'label' => $item[0],
                'route' => $role . '.' . $item[1],
                'icon' => $item[2],
            ])
            ->values()
            ->all();
    }
}
