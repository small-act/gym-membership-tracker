<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reports | Gym Membership Tracker</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #071426; color: #eaf1fb; font-family: Arial, sans-serif; }
        .layout { display: flex; min-height: 100vh; }
        .sidebar { width: 235px; flex-shrink: 0; background: #0b1c32; border-right: 1px solid #203650; padding: 26px 16px; }
        .brand { font-size: 18px; font-weight: 800; margin: 0 8px 30px; }
        .brand small { display: block; margin-top: 7px; color: #8da5c4; font-size: 11px; }
        .nav-link { display: block; padding: 12px; margin: 5px 0; color: #b7c8df; text-decoration: none; border-radius: 8px; font-size: 14px; }
        .nav-link:hover, .nav-link.active { background: #173657; color: white; }
        .main { flex: 1; min-width: 0; padding: 30px; }
        h1 { margin: 0 0 8px; font-size: 27px; }
        h2 { margin: 0 0 18px; font-size: 17px; }
        .muted { color: #93a8c3; font-size: 13px; }
        .panel, .stat { background: #0d2038; border: 1px solid #213b59; border-radius: 13px; padding: 20px; margin-bottom: 22px; }
        .stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 15px; margin: 24px 0; }
        .stat { margin: 0; }
        .stat-label { color: #9bb0ca; font-size: 13px; }
        .stat-value { font-size: 25px; font-weight: 800; margin-top: 11px; overflow-wrap: anywhere; }
        .filters { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 13px; align-items: end; }
        label { display: block; color: #c5d4e7; font-size: 13px; margin-bottom: 7px; }
        input, select { width: 100%; padding: 11px 12px; border-radius: 8px; background: #08182b; border: 1px solid #304967; color: #edf5ff; font: inherit; font-size: 14px; }
        button, .btn { display: inline-block; border: 0; border-radius: 8px; padding: 11px 15px; background: #2876d2; color: white; cursor: pointer; text-decoration: none; font-weight: 700; font-size: 13px; text-align: center; }
        button:hover, .btn:hover { background: #3688e8; }
        .green { background: #147653; }
        .green:hover { background: #198b62; }
        .message { padding: 12px 14px; border-radius: 8px; margin-bottom: 16px; background: #451f2a; border: 1px solid #8a3445; color: #ffc3cb; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 720px; }
        th, td { text-align: left; padding: 13px 11px; border-bottom: 1px solid #213750; font-size: 13px; vertical-align: top; }
        th { color: #9fb5d0; font-weight: 600; white-space: nowrap; }
        .badge { display: inline-block; border-radius: 20px; padding: 5px 9px; font-size: 11px; font-weight: 700; background: #253a55; color: #d3e3f7; }
        .pagination { margin-top: 16px; }
        .pagination a, .pagination span { color: #dceaff; margin-right: 8px; }
        .empty { padding: 24px; text-align: center; color: #93a8c3; }
        .section-head { display: flex; justify-content: space-between; align-items: center; gap: 15px; flex-wrap: wrap; margin-bottom: 18px; }
        @media(max-width: 1050px) { .stats { grid-template-columns: repeat(2, minmax(0, 1fr)); } .filters { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        @media(max-width: 800px) { .layout { display: block; } .sidebar { width: 100%; padding: 14px; } .sidebar nav { display: flex; flex-wrap: wrap; } .brand { margin-bottom: 12px; } .main { padding: 18px; } }
        @media(max-width: 480px) { .stats, .filters { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <div class="brand">GYM TRACKER<small>MEMBERSHIP MANAGEMENT</small></div>
        <nav>
            <a class="nav-link" href="{{ route('dashboard') }}">▦ Dashboard</a>
            <a class="nav-link" href="{{ route('members.index') }}">♙ Members</a>
            <a class="nav-link" href="{{ route('memberships.index') }}">▣ Memberships</a>
            <a class="nav-link" href="{{ route('membership-plans.index') }}">▤ Membership Plans</a>
            <a class="nav-link" href="{{ route('attendance.index') }}">◷ Attendance / QR</a>
            <a class="nav-link" href="{{ route('payments.index') }}">₱ Payments</a>
            <a class="nav-link active" href="{{ route('reports.index') }}">▥ Reports</a>
        </nav>
    </aside>

    <main class="main">
        <h1>Reports & Analytics</h1>
        <div class="muted">Review gym activity, membership records, and recorded collections.</div>

        @if($errors->any())
            <div class="message" style="margin-top:18px">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <section class="stats">
            <div class="stat">
                <div class="stat-label">Total Members</div>
                <div class="stat-value">{{ number_format($totalMembers) }}</div>
            </div>
            <div class="stat">
                <div class="stat-label">Currently Active Memberships</div>
                <div class="stat-value">{{ number_format($activeMemberships) }}</div>
            </div>
            <div class="stat">
                <div class="stat-label">Visits in Selected Period</div>
                <div class="stat-value">{{ number_format($attendanceCount) }}</div>
            </div>
            <div class="stat">
                <div class="stat-label">Paid Collections in Period</div>
                <div class="stat-value">₱{{ number_format((float) $revenue, 2) }}</div>
            </div>
        </section>

        <section class="panel">
            <h2>Report Filters</h2>
            <form method="GET" action="{{ route('reports.index') }}" class="filters">
                <div>
                    <label for="type">Report type</label>
                    <select id="type" name="type">
                        <option value="attendance" @selected($type === 'attendance')>Attendance</option>
                        <option value="memberships" @selected($type === 'memberships')>Memberships</option>
                        <option value="payments" @selected($type === 'payments')>Payments</option>
                    </select>
                </div>
                <div>
                    <label for="from">From date</label>
                    <input type="date" id="from" name="from" value="{{ $from }}" required>
                </div>
                <div>
                    <label for="to">To date</label>
                    <input type="date" id="to" name="to" value="{{ $to }}" required>
                </div>
                <button type="submit">Generate Report</button>
            </form>
        </section>

        <section class="panel">
            <div class="section-head">
                <div>
                    <h2 style="margin-bottom:7px">
                        @if($type === 'memberships')
                            Membership Report
                        @elseif($type === 'payments')
                            Payment Report
                        @else
                            Attendance Report
                        @endif
                    </h2>
                    <div class="muted">{{ $from }} through {{ $to }}</div>
                </div>
                <a class="btn green" href="{{ route('reports.export', ['type' => $type, 'from' => $from, 'to' => $to]) }}">↓ Export CSV</a>
            </div>

            <div class="table-wrap">
                <table>
                    @if($type === 'memberships')
                        <thead>
                        <tr><th>Member</th><th>Plan</th><th>Start Date</th><th>End Date</th><th>Price</th><th>Status</th></tr>
                        </thead>
                        <tbody>
                        @forelse($records as $record)
                            @php
                                $status = $record->status === 'cancelled'
                                    ? 'Cancelled'
                                    : ($record->start_date->isFuture()
                                        ? 'Upcoming'
                                        : ($record->end_date->lt(today()) ? 'Expired' : 'Active'));
                            @endphp
                            <tr>
                                <td>{{ $record->member?->full_name ?? '—' }}<br><span class="muted">{{ $record->member?->member_number }}</span></td>
                                <td>{{ $record->plan?->name ?? '—' }}</td>
                                <td>{{ $record->start_date->format('M d, Y') }}</td>
                                <td>{{ $record->end_date->format('M d, Y') }}</td>
                                <td>₱{{ number_format((float) $record->price, 2) }}</td>
                                <td><span class="badge">{{ $status }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="empty">No membership records for this period.</td></tr>
                        @endforelse
                        </tbody>
                    @elseif($type === 'payments')
                        <thead>
                        <tr><th>Date</th><th>Member</th><th>Plan</th><th>Method</th><th>Amount</th><th>Status</th></tr>
                        </thead>
                        <tbody>
                        @forelse($records as $record)
                            <tr>
                                <td>{{ $record->payment_date?->format('M d, Y') ?? '—' }}</td>
                                <td>{{ $record->membership?->member?->full_name ?? '—' }}</td>
                                <td>{{ $record->membership?->plan?->name ?? '—' }}</td>
                                <td>{{ str_replace('_', ' ', ucfirst($record->payment_method)) }}</td>
                                <td>₱{{ number_format((float) $record->amount, 2) }}</td>
                                <td><span class="badge">{{ ucfirst($record->status) }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="empty">No payment records for this period.</td></tr>
                        @endforelse
                        </tbody>
                    @else
                        <thead>
                        <tr><th>Date</th><th>Member</th><th>Member No.</th><th>Check-in</th><th>Check-out</th><th>Method</th></tr>
                        </thead>
                        <tbody>
                        @forelse($records as $record)
                            <tr>
                                <td>{{ $record->attendance_date?->format('M d, Y') ?? '—' }}</td>
                                <td>{{ $record->member?->full_name ?? '—' }}</td>
                                <td>{{ $record->member?->member_number ?? '—' }}</td>
                                <td>{{ $record->check_in_at?->format('h:i A') ?? '—' }}</td>
                                <td>{{ $record->check_out_at?->format('h:i A') ?? '—' }}</td>
                                <td>{{ strtoupper($record->check_in_method) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="empty">No attendance records for this period.</td></tr>
                        @endforelse
                        </tbody>
                    @endif
                </table>
            </div>

            <div class="pagination">{{ $records->links() }}</div>
        </section>
    </main>
</div>
</body>
</html>
