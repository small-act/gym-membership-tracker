<?php

namespace App\Http\Controllers\MemberPortal;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Membership;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $account = Auth::guard('member')->user();

        abort_unless($account && $account->member, 403);

        $member = $account->member;

        $memberships = Membership::query()
            ->with('plan')
            ->where('member_id', $member->id)
            ->orderByDesc('start_date')
            ->orderByDesc('id')
            ->get();

        $attendances = Attendance::query()
            ->where('member_id', $member->id)
            ->orderByDesc('attendance_date')
            ->orderByDesc('check_in_at')
            ->paginate(10, ['*'], 'attendance_page');

        $payments = Payment::query()
            ->with('membership.plan')
            ->whereHas('membership', function ($query) use ($member) {
                $query->where('member_id', $member->id);
            })
            ->orderByDesc('payment_date')
            ->orderByDesc('id')
            ->paginate(10, ['*'], 'payment_page');

        $totalPaid = Payment::query()
            ->where('status', 'paid')
            ->whereHas('membership', function ($query) use ($member) {
                $query->where('member_id', $member->id);
            })
            ->sum('amount');

        $paidCount = Payment::query()
            ->where('status', 'paid')
            ->whereHas('membership', function ($query) use ($member) {
                $query->where('member_id', $member->id);
            })
            ->count();

        return view('member-portal.dashboard', compact(
            'member',
            'memberships',
            'attendances',
            'payments',
            'totalPaid',
            'paidCount'
        ));
    }
}
