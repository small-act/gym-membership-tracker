	<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Membership Plans | Gym Membership Tracker</title>

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #071426;
            color: #eaf1fb;
        }

        header {
            padding: 22px 5%;
            background: #0c1e36;
            border-bottom: 1px solid #203754;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        h1 { margin: 0; font-size: 24px; }

        main {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .muted { color: #9fb1c9; }

        .button {
            display: inline-block;
            background: #2563eb;
            color: white;
            padding: 11px 15px;
            border-radius: 8px;
            text-decoration: none;
            border: 0;
            cursor: pointer;
            font-weight: bold;
        }

        .button.secondary { background: #263b56; }

        .panel {
            background: #10233c;
            border: 1px solid #203754;
            border-radius: 12px;
            padding: 20px;
            margin-top: 20px;
            overflow-x: auto;
        }

        .success {
            background: #123d32;
            border: 1px solid #28785d;
            color: #b9f6d9;
            padding: 13px;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 650px;
        }

        th, td {
            padding: 14px 12px;
            text-align: left;
            border-bottom: 1px solid #263b56;
        }

        th {
            color: #9fb1c9;
            font-size: 12px;
            text-transform: uppercase;
        }

        .status {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 20px;
            font-size: 12px;
        }

        .active { background: #164e3e; color: #a7f3d0; }
        .inactive { background: #4b2933; color: #fecdd3; }

        .duration {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 7px;
            background: #1b3658;
            color: #bfdbfe;
            font-weight: bold;
            white-space: nowrap;
        }

        .empty {
            text-align: center;
            padding: 30px;
            color: #9fb1c9;
        }

        .actions { display: flex; gap: 8px; }

        @media(max-width:600px) {
            header { align-items: flex-start; }
            h1 { font-size: 20px; }
        }
    </style>
</head>

<body>
<header>
    <div>
        <h1>Membership Plans</h1>
        <div class="muted" style="margin-top:6px">
            Manage gym packages, prices, and durations in days or hours
        </div>
    </div>

    <div style="display:flex;gap:10px;flex-wrap:wrap">
        <a class="button secondary" href="{{ route('dashboard') }}">
            Dashboard
        </a>

        <a class="button" href="{{ route('membership-plans.create') }}">
            + Add Plan
        </a>
    </div>
</header>

<main>
    @if(session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="success"
             style="background:#4b2933;border-color:#9f4256;color:#fecdd3">
            Please correct the following:
            <ul style="margin-bottom:0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="panel">
        <table>
            <thead>
                <tr>
                    <th>Plan</th>
                    <th>Price</th>
                    <th>Duration</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($plans as $plan)
                    @php
                        $durationUnit = strtolower(
                            (string) ($plan->duration_unit ?? 'days')
                        );

                        $durationLabel = $durationUnit === 'hours'
                            ? 'hours'
                            : 'days';
                    @endphp

                    <tr>
                        <td>
                            <strong>
                                {{ $plan->name ?: 'Unnamed plan' }}
                            </strong>

                            @if($plan->description)
                                <div class="muted" style="margin-top:5px">
                                    {{ $plan->description }}
                                </div>
                            @endif
                        </td>

                        <td>
                            ₱{{ number_format((float) $plan->price, 2) }}
                        </td>

                        <td>
                            <span class="duration">
                                {{ $plan->duration_days }}
                                {{ $durationLabel }}
                            </span>
                        </td>

                        <td>
                            <span class="status {{ $plan->is_active ? 'active' : 'inactive' }}">
                                {{ $plan->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>

                        <td>
                            <div class="actions">
                                <a class="button secondary"
                                   href="{{ route('membership-plans.edit', $plan) }}">
                                    Edit
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="empty">
                            No membership plans yet. Select “Add Plan”
                            to create your first package.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top:20px">
            {{ $plans->links() }}
        </div>
    </div>
</main>
</body>
</html>