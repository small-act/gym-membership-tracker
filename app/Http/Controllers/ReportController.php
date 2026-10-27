<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Member;
use App\Models\Membership;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;

class ReportController extends Controller
{
    private function filters(Request $request): array
    {
        $validated = $request->validate([
            'type' => ['nullable', 'in:attendance,memberships,payments'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        return [
            'type' => $validated['type'] ?? 'attendance',
            'from' => $validated['from'] ?? now()->startOfMonth()->toDateString(),
            'to' => $validated['to'] ?? today()->toDateString(),
        ];
    }

    public function index(Request $request)
    {
        $filters = $this->filters($request);
        $type = $filters['type'];
        $from = $filters['from'];
        $to = $filters['to'];

        $totalMembers = Member::count();

        $activeMemberships = Membership::whereDate('start_date', '<=', today())
            ->whereDate('end_date', '>=', today())
            ->count();

        $attendanceCount = Attendance::whereBetween('attendance_date', [$from, $to])
            ->count();

        $revenue = Payment::where('status', 'paid')
            ->whereBetween('payment_date', [$from, $to])
            ->sum('amount');

        if ($type === 'memberships') {
            $records = Membership::with(['member', 'plan'])
                ->whereBetween('start_date', [$from, $to])
                ->orderByDesc('start_date')
                ->paginate(20)
                ->withQueryString();
        } elseif ($type === 'payments') {
            $records = Payment::with(['membership.member', 'membership.plan'])
                ->whereBetween('payment_date', [$from, $to])
                ->orderByDesc('payment_date')
                ->orderByDesc('id')
                ->paginate(20)
                ->withQueryString();
        } else {
            $records = Attendance::with('member')
                ->whereBetween('attendance_date', [$from, $to])
                ->orderByDesc('attendance_date')
                ->orderByDesc('check_in_at')
                ->paginate(20)
                ->withQueryString();
        }

        return view('reports.index', compact(
            'filters',
            'type',
            'from',
            'to',
            'records',
            'totalMembers',
            'activeMemberships',
            'attendanceCount',
            'revenue'
        ));
    }

    public function export(Request $request)
    {
        $filters = $this->filters($request);
        $type = $filters['type'];
        $from = $filters['from'];
        $to = $filters['to'];

        $filename = $type . '-report-' . $from . '-to-' . $to . '.csv';

        return Response::streamDownload(function () use ($type, $from, $to) {
            $output = fopen('php://output', 'w');

            // Help spreadsheet programs display UTF-8 correctly.
            fwrite($output, "\xEF\xBB\xBF");

            if ($type === 'memberships') {
                fputcsv($output, [
                    'Membership ID', 'Member Number', 'Member Name',
                    'Plan', 'Start Date', 'End Date', 'Price', 'Status',
                ]);

                Membership::with(['member', 'plan'])
                    ->whereBetween('start_date', [$from, $to])
                    ->orderBy('start_date')
                    ->chunk(200, function ($rows) use ($output) {
                        foreach ($rows as $row) {
                            $status = $row->status === 'cancelled'
                                ? 'Cancelled'
                                : ($row->start_date->isFuture()
                                    ? 'Upcoming'
                                    : ($row->end_date->lt(today())
                                        ? 'Expired'
                                        : 'Active'));

                            fputcsv($output, [
                                $row->id,
                                $row->member?->member_number,
                                $row->member?->full_name,
                                $row->plan?->name,
                                $row->start_date?->toDateString(),
                                $row->end_date?->toDateString(),
                                $row->price,
                                $status,
                            ]);
                        }
                    });
            } elseif ($type === 'payments') {
                fputcsv($output, [
                    'Payment ID', 'Payment Date', 'Member',
                    'Plan', 'Method', 'Reference', 'Amount', 'Status',
                ]);

                Payment::with(['membership.member', 'membership.plan'])
                    ->whereBetween('payment_date', [$from, $to])
                    ->orderBy('payment_date')
                    ->chunk(200, function ($rows) use ($output) {
                        foreach ($rows as $row) {
                            fputcsv($output, [
                                $row->id,
                                $row->payment_date?->toDateString(),
                                $row->membership?->member?->full_name,
                                $row->membership?->plan?->name,
                                $row->payment_method,
                                $row->reference_number,
                                $row->amount,
                                $row->status,
                            ]);
                        }
                    });
            } else {
                fputcsv($output, [
                    'Attendance ID', 'Date', 'Member Number',
                    'Member Name', 'Check In', 'Check Out', 'Method',
                ]);

                Attendance::with('member')
                    ->whereBetween('attendance_date', [$from, $to])
                    ->orderBy('attendance_date')
                    ->chunk(200, function ($rows) use ($output) {
                        foreach ($rows as $row) {
                            fputcsv($output, [
                                $row->id,
                                $row->attendance_date?->toDateString(),
                                $row->member?->member_number,
                                $row->member?->full_name,
                                $row->check_in_at?->format('Y-m-d H:i:s'),
                                $row->check_out_at?->format('Y-m-d H:i:s'),
                                $row->check_in_method,
                            ]);
                        }
                    });
            }

            fclose($output);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
