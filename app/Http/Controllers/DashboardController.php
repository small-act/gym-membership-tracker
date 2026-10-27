<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        $members = Schema::hasTable('members')
            ? DB::table('members')->count()
            : 0;

        $memberships = Schema::hasTable('memberships')
            ? DB::table('memberships')->count()
            : 0;

        $activeMemberships = 0;

        if (Schema::hasTable('memberships')) {
            if (Schema::hasColumn('memberships', 'status')) {
                $activeMemberships = DB::table('memberships')
                    ->where('status', 'active')
                    ->count();
            } elseif (
                Schema::hasColumn('memberships', 'start_date') &&
                Schema::hasColumn('memberships', 'end_date')
            ) {
                $activeMemberships = DB::table('memberships')
                    ->whereDate('start_date', '<=', today())
                    ->whereDate('end_date', '>=', today())
                    ->count();
            }
        }

        $expiredMemberships = 0;

        if (
            Schema::hasTable('memberships') &&
            Schema::hasColumn('memberships', 'end_date')
        ) {
            $expiredMemberships = DB::table('memberships')
                ->whereDate('end_date', '<', today())
                ->count();
        }

        $todayAttendance = 0;

        if (Schema::hasTable('attendances')) {
            $dateColumn = Schema::hasColumn('attendances', 'check_in_at')
                ? 'check_in_at'
                : 'created_at';

            $todayAttendance = DB::table('attendances')
                ->whereDate($dateColumn, today())
                ->count();
        }

        // All-time and today's confirmed collections.
        $totalCollections = 0;
        $todayCollections = 0;

        if (
            Schema::hasTable('payments') &&
            Schema::hasColumn('payments', 'amount')
        ) {
            $paidQuery = DB::table('payments');

            if (Schema::hasColumn('payments', 'status')) {
                $paidQuery->where('status', 'paid');
            }

            $totalCollections = (float) (clone $paidQuery)->sum('amount');

            if (Schema::hasColumn('payments', 'payment_date')) {
                $todayCollections = (float) (clone $paidQuery)
                    ->whereDate('payment_date', today())
                    ->sum('amount');
            } elseif (Schema::hasColumn('payments', 'paid_at')) {
                $todayCollections = (float) (clone $paidQuery)
                    ->whereDate('paid_at', today())
                    ->sum('amount');
            } elseif (Schema::hasColumn('payments', 'created_at')) {
                $todayCollections = (float) (clone $paidQuery)
                    ->whereDate('created_at', today())
                    ->sum('amount');
            }
        }

        $recentMembers = Schema::hasTable('members')
            ? DB::table('members')->orderByDesc('id')->limit(5)->get()
            : collect();

        return view('dashboard', compact(
            'members',
            'memberships',
            'activeMemberships',
            'expiredMemberships',
            'todayAttendance',
            'totalCollections',
            'todayCollections',
            'recentMembers'
        ));
    }
}
