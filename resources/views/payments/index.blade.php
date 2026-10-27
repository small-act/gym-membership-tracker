<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payments | Gym Membership Tracker</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #081426; color: #edf4ff; font-family: Arial, sans-serif; }
        header { padding: 22px 5%; background: #0b1b30; border-bottom: 1px solid #20324b; display: flex; justify-content: space-between; align-items: center; gap: 15px; flex-wrap: wrap; }
        h1 { margin: 0; font-size: 24px; }
        main { max-width: 1300px; margin: 28px auto; padding: 0 20px; }
        .muted { color: #91a6c2; font-size: 12px; }
        .button { display: inline-block; padding: 11px 15px; background: #2563eb; color: white; text-decoration: none; border: 0; border-radius: 8px; font-weight: bold; }
        .secondary { background: #263b56; }
        .cards { display: grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap: 14px; }
        .card, .panel { background: #0d1e33; border: 1px solid #20344e; border-radius: 12px; }
        .card { padding: 20px; }
        .card-title { color: #9db0c9; font-size: 12px; }
        .amount { font-size: 25px; font-weight: bold; margin-top: 12px; overflow-wrap: anywhere; }
        .panel { margin-top: 22px; overflow-x: auto; }
        table { width: 100%; min-width: 900px; border-collapse: collapse; text-align: left; }
        th, td { padding: 14px 16px; border-bottom: 1px solid #20344e; font-size: 12px; }
        th { color: #91a6c2; font-size: 10px; text-transform: uppercase; }
        .message { padding: 13px 16px; border-radius: 8px; margin-bottom: 15px; background: #123d32; color: #b9f6d9; }
        .status { display: inline-block; padding: 5px 9px; border-radius: 20px; font-size: 10px; text-transform: capitalize; }
        .paid { background: #164e3e; color: #a7f3d0; }
        .pending { background: #3f351a; color: #fde68a; }
        .failed { background: #4b2933; color: #fecdd3; }
        .empty { text-align: center; padding: 35px; color: #91a6c2; }
        @media(max-width:700px) { .cards { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<header>
    <div>
        <h1>Payments</h1>
        <div class="muted" style="margin-top:6px">Payment records and collections in Philippine pesos</div>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap">
        <a class="button secondary" href="{{ route('dashboard') }}">Dashboard</a>
        <a class="button" href="{{ route('payments.create') }}">+ Record Payment</a>
    </div>
</header>

<main>
    @if(session('success'))
        <div class="message">{{ session('success') }}</div>
    @endif

    <section class="cards">
        <article class="card">
            <div class="card-title">Total Payments Received</div>
            <div class="amount">₱{{ number_format((float) $totalCollected, 2) }}</div>
            <div class="muted">All records marked paid</div>
        </article>
        <article class="card">
            <div class="card-title">Collected Today</div>
            <div class="amount">₱{{ number_format((float) $todayCollected, 2) }}</div>
            <div class="muted">Paid records dated today</div>
        </article>
        <article class="card">
            <div class="card-title">Pending Payments</div>
            <div class="amount">₱{{ number_format((float) $pendingAmount, 2) }}</div>
            <div class="muted">Records awaiting confirmation</div>
        </article>
    </section>

    <section class="panel">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Member</th>
                    <th>Plan</th>
                    <th>Method</th>
                    <th>Reference</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                    <tr>
                        <td>{{ $payment->payment_date->format('M j, Y') }}</td>
                        <td>{{ $payment->membership?->member?->full_name ?? 'Member unavailable' }}</td>
                        <td>{{ $payment->membership?->plan?->name ?? 'Plan unavailable' }}</td>
                        <td>{{ ucwords(str_replace('_', ' ', $payment->payment_method)) }}</td>
                        <td>{{ $payment->reference_number ?: '—' }}</td>
                        <td><strong>₱{{ number_format((float) $payment->amount, 2) }}</strong></td>
                        <td><span class="status {{ $payment->status }}">{{ ucfirst($payment->status) }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty">No payment records yet. Select “Record Payment” to get started.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div style="padding:18px">{{ $payments->links() }}</div>
    </section>
</main>
</body>
</html>
