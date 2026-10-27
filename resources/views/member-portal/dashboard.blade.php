<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Member Dashboard | Inabanga Wellness Center</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; background: #f2f5fa; color: #172b4d; }
        header { background: #0b1e36; color: white; padding: 20px 5%; display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap; }
        header strong { font-size: 18px; }
        header button { background: #244b77; color: white; border: 0; padding: 10px 15px; border-radius: 8px; cursor: pointer; }
        main { max-width: 1200px; margin: 28px auto; padding: 0 20px; }
        .welcome { margin-bottom: 22px; }
        .muted { color: #68788d; }
        .cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 16px; }
        .card, section { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 3px 14px #142b4910; margin-bottom: 20px; overflow-x: auto; }
        .metric { font-size: 28px; font-weight: bold; margin-top: 10px; color: #174c87; }
        h2 { font-size: 19px; margin-top: 0; }
        table { border-collapse: collapse; width: 100%; min-width: 650px; }
        th, td { padding: 12px 10px; border-bottom: 1px solid #e6ebf2; text-align: left; font-size: 14px; }
        th { background: #f7f9fc; }
        .message { padding: 12px; background: #eaf8ef; color: #176534; border-radius: 8px; margin-bottom: 16px; }
        .profile { display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 12px; }
        a { color: #174c87; }
        .empty { color: #68788d; padding: 12px 0; }
        @media (max-width: 600px) { header { padding: 16px 20px; } main { padding: 0 12px; } }
    </style>
</head>
<body>
<header>
    <strong>INABANGA WELLNESS CENTER · MEMBER PORTAL</strong>
    <form method="POST" action="{{ route('member.logout') }}">
        @csrf
        <button type="submit">Sign Out</button>
    </form>
</header>

<main>
    @if (session('success'))
        <div class="message">{{ session('success') }}</div>
    @endif

    <div class="welcome">
        <h1>Welcome, {{ $member->full_name }}</h1>
        <p class="muted">Your personal membership information and history.</p>
    </div>

    <div class="cards">
        <div class="card">
            <div class="muted">Member Number</div>
            <div class="metric" style="font-size:20px">{{ $member->member_number }}</div>
        </div>
        <div class="card">
            <div class="muted">Total Paid</div>
            <div class="metric">₱{{ number_format((float) $totalPaid, 2) }}</div>
        </div>
        <div class="card">
            <div class="muted">Paid Transactions</div>
            <div class="metric">{{ $paidCount }}</div>
        </div>
        <div class="card">
            <div class="muted">Account Status</div>
            <div class="metric" style="font-size:20px">{{ ucfirst($member->status) }}</div>
        </div>
    </div>

    <section>
        <h2>My Profile</h2>
        <div class="profile">
            <div><strong>Full name</strong><br>{{ $member->full_name }}</div>
            <div><strong>Email</strong><br>{{ $member->email }}</div>
            <div><strong>Phone</strong><br>{{ $member->phone ?: 'Not provided' }}</div>
            <div><strong>Member status</strong><br>{{ ucfirst($member->status) }}</div>
        </div>
    </section>

    <section>
        <h2>Membership History</h2>
        @forelse ($memberships as $membership)
            <p>
                <strong>{{ $membership->plan->name ?? 'Membership plan' }}</strong>
                — {{ $membership->start_date?->format('M d, Y') ?? '—' }}
                to {{ $membership->end_date?->format('M d, Y') ?? '—' }}
                — {{ ucfirst($membership->status ?? 'unknown') }}
            </p>
        @empty
            <p class="empty">No membership records found.</p>
        @endforelse
    </section>

    <section>
        <h2>Attendance History</h2>
        <table>
            <thead>
                <tr><th>Date</th><th>Check In</th><th>Check Out</th><th>Method</th></tr>
            </thead>
            <tbody>
                @forelse ($attendances as $attendance)
                    <tr>
                        <td>{{ $attendance->attendance_date?->format('M d, Y') ?? '—' }}</td>
                        <td>{{ $attendance->check_in_at?->format('h:i A') ?? '—' }}</td>
                        <td>{{ $attendance->check_out_at?->format('h:i A') ?? '—' }}</td>
                        <td>{{ ucfirst($attendance->check_in_method ?? 'manual') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4">No attendance records found.</td></tr>
                @endforelse
            </tbody>
        </table>
        <p>{{ $attendances->links() }}</p>
    </section>

    <section>
        <h2>Payment History</h2>
        <table>
            <thead>
                <tr><th>Date</th><th>Reference</th><th>Method</th><th>Amount</th><th>Status</th></tr>
            </thead>
            <tbody>
                @forelse ($payments as $payment)
                    <tr>
                        <td>{{ $payment->payment_date?->format('M d, Y') ?? '—' }}</td>
                        <td>{{ $payment->reference_number ?: '—' }}</td>
                        <td>{{ ucfirst($payment->payment_method ?? '—') }}</td>
                        <td>₱{{ number_format((float) $payment->amount, 2) }}</td>
                        <td>{{ ucfirst($payment->status ?? 'unknown') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5">No payment records found.</td></tr>
                @endforelse
            </tbody>
        </table>
        <p>{{ $payments->links() }}</p>
    </section>
</main>
</body>
</html>
