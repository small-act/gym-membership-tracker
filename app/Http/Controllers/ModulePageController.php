<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ModulePageController extends Controller
{
    public function show(string $module)
    {
        $modules = [
            'members' => 'Members',
            'memberships' => 'Memberships',
            'membership-plans' => 'Membership Plans',
            'attendance' => 'Attendance / QR',
            'payments' => 'Payments',
            'reports' => 'Reports',
        ];

        abort_unless(isset($modules[$module]), 404);

        return view('modules.placeholder', [
            'title' => $modules[$module],
        ]);
    }
}
