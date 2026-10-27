<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Memberships | Gym Membership Tracker</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #081426; color: #edf4ff; font-family: Arial, sans-serif; }
        header { padding: 22px 5%; background: #0b1b30; border-bottom: 1px solid #20324b; display: flex; align-items: center; justify-content: space-between; gap: 15px; flex-wrap: wrap; }
        h1 { margin: 0; font-size: 24px; }
        main { max-width: 1250px; margin: 30px auto; padding: 0 20px; }
        .muted { color: #91a6c2; font-size: 13px; }
        .button { display: inline-block; padding: 11px 15px; background: #2563eb; color: white; text-decoration: none; border: 0; border-radius: 8px; cursor: pointer; font-weight: bold; font-size: 13px; }
        .secondary { background: #263b56; }
        .danger { background: #b91c1c; }
        .button:hover { filter: brightness(1.12); }
        .panel { margin-top: 20px; background: #0d1e33; border: 1px solid #20344e; border-radius: 12px; overflow-x: auto; }
        .message { padding: 13px 16px; border-radius: 8px; margin-bottom: 14px; background: #123d32; color: #b9f6d9; }
        .message.error { background: #4b2933; color: #fecdd3; }
        table { width: 100%; min-width: 950px; border-collapse: collapse; text-align: left; }
        th, td { padding: 14px 16px; border-bottom: 1px solid #20344e; }
        th { color: #91a6c2; font-size: 11px; text-transform: uppercase; }
        td { font-size: 13px; }
        .status { display: inline-block; padding: 5px 9px; border-radius: 20px; font-size: 11px; }
        .active { background: #164e3e; color: #a7f3d0; }
        .expired { background: #4b2933; color: #fecdd3; }
        .upcoming { background: #3f351a; color: #fde68a; }
        .empty { text-align: center; padding: 35px; color: #91a6c2; }
        .actions { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
        form { margin: 0; }
        .warning { color: #fbbf24; font-size: 12px; margin-top: 5px; }
    </style>
</head>
<body>
<header>
    <div>
        <h1>Memberships</h1>
        <div class="muted" style="margin-top:6px">
            Register, track, renew, and correct gym memberships
        </div>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap">
        <a class="button secondary" href="{{ route('dashboard') }}">Dashboard</a>
        <a class="button" href="{{ route('memberships.create') }}">+ Register Membership</a>
    </div>
</header>

<main>
    @if(session('success'))
        <div class="message">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="message error">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="message error">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <section class="panel">
        <table>
            <thead>
                <tr>
                    <th>Member</th>
                    <th>Plan</th>
                    <th>Start Date</th>
                    <th>Expiry Date</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($memberships as $membership)
                    @php
                        $today = now()->startOfDay();
                        $start = $membership->start_date->copy()->startOfDay();
                        $end = $membership->end_date->copy()->startOfDay();

                        if ($membership->status === 'cancelled') {
                            $status = 'Cancelled';
                            $statusClass = 'expired';
                        } elseif ($start->gt($today)) {
                            $status = 'Upcoming';
                            $statusClass = 'upcoming';
                        } elseif ($end->lt($today)) {
                            $status = 'Expired';
                            $statusClass = 'expired';
                        } else {
                            $status = 'Active';
                            $statusClass = 'active';
                        }
                    @endphp

                    <tr>
                        <td>
                            <strong>{{ $membership->member?->full_name ?? 'Member unavailable' }}</strong>
                            <div class="muted">
                                {{ $membership->member?->member_number ?? '' }}
                            </div>
                        </td>
                        <td>{{ $membership->plan?->name ?? 'Plan unavailable' }}</td>
                        <td>{{ $membership->start_date->format('M j, Y') }}</td>
                        <td>{{ $membership->end_date->format('M j, Y') }}</td>
                        <td>₱{{ number_format((float) $membership->price, 2) }}</td>
                        <td>
                            <span class="status {{ $statusClass }}">{{ $status }}</span>
                        </td>
                        <td>
                            <div class="actions">
                                <form method="POST"
                                      action="{{ route('memberships.renew', $membership) }}"
                                      onsubmit="return confirm('Create a new membership period for this member? The current record will be preserved.');">
                                    @csrf
                                    <button class="button secondary" type="submit">Renew</button>
                                </form>

                                <form method="POST"
                                      action="{{ route('memberships.destroy', $membership) }}"
                                      onsubmit="return confirm('WARNING: Are you sure you want to delete this membership record? This action cannot be undone. If a payment is linked to this membership, deletion should be blocked to protect financial records.');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="button danger" type="submit">Delete</button>
                                </form>
                            </div>
                            <div class="warning">Check the member and dates before deleting.</div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty">
                            <strong>No memberships registered yet.</strong><br>
                            Create a membership for a registered member to get started.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="padding:18px">
            {{ $memberships->links() }}
        </div>
    </section>
</main>
</body>
</html>
