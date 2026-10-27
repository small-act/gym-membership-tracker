<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#071426">
    <title>Dashboard | Gym Membership Tracker</title>

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            background: #081426;
            color: #edf4ff;
            font-family: Inter, "Segoe UI", Arial, sans-serif;
        }

        .app { display: flex; min-height: 100vh; }

        .sidebar {
            width: 250px;
            flex-shrink: 0;
            background: #0b1b30;
            border-right: 1px solid #20324b;
            padding: 26px 16px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 8px 28px;
            border-bottom: 1px solid #20324b;
        }

        .brand-icon {
            display: grid;
            place-items: center;
            width: 43px;
            height: 43px;
            border-radius: 12px;
            background: #173b66;
            color: #86c2ff;
            font-size: 24px;
        }

        .brand strong { display: block; font-size: 14px; letter-spacing: 1px; }
        .brand small { color: #91a6c2; font-size: 10px; }

        .nav-label {
            margin: 27px 10px 10px;
            color: #7188a7;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1.5px;
        }

        .nav-link {
            display: flex;
            gap: 12px;
            align-items: center;
            padding: 12px;
            margin: 5px 0;
            color: #aabbd2;
            border-radius: 9px;
            text-decoration: none;
            font-size: 13px;
            cursor: pointer;
            transition: background .2s ease, color .2s ease;
        }

        .nav-link:hover {
            background: #173253;
            color: #ffffff;
        }

        .nav-link.active {
            background: #17375d;
            color: #fff;
            border: 1px solid #244c79;
        }

        .nav-link .symbol { width: 22px; text-align: center; }

        .nav-link.reset-link {
            background: #351b29;
            border: 1px solid #713047;
            color: #ffb4bd;
        }

        .nav-link.reset-link:hover {
            background: #572033;
            color: #ffffff;
        }

        .sidebar-note {
            margin-top: 35px;
            padding: 13px;
            border: 1px solid #253a54;
            border-radius: 10px;
            color: #8fa7c5;
            font-size: 11px;
            line-height: 1.6;
        }

        main { min-width: 0; flex: 1; padding: 30px; }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            margin-bottom: 32px;
        }

        h1 { font-size: 27px; margin: 0 0 7px; }
        .muted { color: #8fa4bf; font-size: 13px; }
        .user-area { display: flex; align-items: center; gap: 12px; }

        .avatar {
            width: 39px;
            height: 39px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            background: #20466f;
            font-weight: 700;
        }

        .user-name { font-size: 13px; font-weight: 600; }
        .user-role { font-size: 11px; color: #8fa4bf; margin-top: 4px; }

        .logout {
            border: 1px solid #30445e;
            background: #10233b;
            color: #dbe9fb;
            padding: 9px 12px;
            border-radius: 8px;
            cursor: pointer;
        }

        .logout:hover { background: #193452; }

        .welcome {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            padding: 25px;
            margin-bottom: 24px;
            border: 1px solid #254365;
            border-radius: 15px;
            background: linear-gradient(115deg, #132f50, #102039);
        }

        .welcome h2 { font-size: 21px; margin: 0 0 9px; }
        .welcome p { margin: 0; color: #a8bed9; font-size: 13px; line-height: 1.6; }
        .welcome-mark { font-size: 48px; opacity: .85; padding: 0 12px; }

        .section-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin: 28px 0 15px;
        }

        h2.section-title { font-size: 16px; margin: 0; }

        .cards {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 15px;
        }

        .stat {
            background: #0d1e33;
            border: 1px solid #20344e;
            border-radius: 13px;
            padding: 19px;
            min-width: 0;
        }

        .stat-top { display: flex; justify-content: space-between; gap: 8px; align-items: center; }
        .stat-label { color: #9db0c9; font-size: 12px; }

        .stat-icon {
            display: grid;
            place-items: center;
            width: 35px;
            height: 35px;
            border-radius: 10px;
            background: #173253;
            font-size: 17px;
        }

        .stat-value { font-size: 27px; font-weight: 700; margin-top: 17px; overflow-wrap: anywhere; }
        .stat-foot { color: #8097b4; font-size: 11px; margin-top: 7px; }
        .green { color: #5be0ac; }
        .amber { color: #ffcf79; }
        .blue { color: #83bdff; }
        .purple { color: #c1a5ff; }

        .panel {
            background: #0d1e33;
            border: 1px solid #20344e;
            border-radius: 13px;
            overflow: hidden;
        }

        .panel-head {
            padding: 18px 20px;
            border-bottom: 1px solid #20344e;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .panel-head h2 { font-size: 14px; margin: 0; }
        .panel-head span { color: #849bb8; font-size: 11px; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; text-align: left; }

        th {
            color: #8198b6;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .8px;
            padding: 13px 20px;
            font-weight: 600;
        }

        td { padding: 15px 20px; border-top: 1px solid #1c3048; font-size: 12px; }
        .member-name { font-weight: 600; }
        .member-id { color: #8097b4; font-size: 11px; margin-top: 4px; }
        .empty { text-align: center; padding: 35px 20px; color: #93a8c3; line-height: 1.7; }

        .pill {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 20px;
            background: #173c36;
            color: #69dfb3;
            font-size: 10px;
        }

        .footer { margin-top: 26px; color: #7187a4; font-size: 11px; text-align: center; }

        @media(max-width: 1100px) {
            .cards { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .sidebar { width: 220px; }
        }

        @media(max-width: 700px) {
            .app { display: block; }
            .sidebar { width: 100%; padding: 16px; }
            .brand { padding-bottom: 15px; }
            .nav-label, .sidebar-note { display: none; }
            .sidebar nav { display: flex; flex-wrap: wrap; gap: 4px; margin-top: 12px; }
            .nav-link { padding: 9px; margin: 0; }
            main { padding: 20px 14px; }
            .topbar { align-items: flex-start; }
            h1 { font-size: 22px; }
            .user-area { flex-wrap: wrap; justify-content: flex-end; }
            .user-name, .user-role { display: none; }
            .welcome { padding: 19px; }
            .welcome-mark { display: none; }
            .cards { gap: 10px; }
            .stat { padding: 14px; }
            .stat-value { font-size: 22px; }
        }
    </style>
</head>

<body>
<div class="app">
    <aside class="sidebar">
        <div class="brand">
            <div class="brand-icon">🙍‍♂️</div>
            <div>
                <strong>GYM TRACKER</strong>
                <small>MEMBERSHIP MANAGEMENT</small>
            </div>
        </div>

        <div class="nav-label">MAIN MENU</div>

        <nav>
            <a class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}"
               href="{{ route('dashboard') }}">
                <span class="symbol">▦</span> Dashboard
            </a>

            <a class="nav-link {{ request()->is('members*') ? 'active' : '' }}"
               href="{{ route('members.index') }}">
                <span class="symbol">♙</span> Members
            </a>

            <a class="nav-link {{ request()->is('memberships') ? 'active' : '' }}"
               href="{{ url('/memberships') }}">
                <span class="symbol">▣</span> Memberships
            </a>

            <a class="nav-link {{ request()->is('membership-plans*') ? 'active' : '' }}"
               href="{{ route('membership-plans.index') }}">
                <span class="symbol">▤</span> Membership Plans
            </a>

            <a class="nav-link {{ request()->is('attendance') ? 'active' : '' }}"
               href="{{ url('/attendance') }}">
                <span class="symbol">◷</span> Attendance / QR
            </a>

            <a class="nav-link {{ request()->is('payments') ? 'active' : '' }}"
               href="{{ url('/payments') }}">
                <span class="symbol">₱</span> Payments
            </a>

            <a class="nav-link {{ request()->is('reports') ? 'active' : '' }}"
               href="{{ route('reports.index') }}">
                <span class="symbol">▥</span> Reports
            </a>
@if(in_array(auth()->user()->role, ['super_admin', 'admin'], true))
    <a class="nav-link {{ request()->is('data-transfer*') ? 'active' : '' }}"
       href="{{ route('data-transfer.index') }}">
        <span class="symbol">⇅</span> Import / Export Data
    </a>
@endif
            @if(auth()->user()->role === 'super_admin')
                <a class="nav-link {{ request()->is('staff*') ? 'active' : '' }}"
                   href="{{ route('staff.index') }}">
                    <span class="symbol">♙</span> Staff & Admin Management
                </a>

                <a class="nav-link reset-link {{ request()->is('system-reset*') ? 'active' : '' }}"
                   href="{{ route('system-reset.index') }}">
                    <span class="symbol">⚠️</span> Reset All System Data
                </a>
            @endif
        </nav>

        <div class="nav-label">ACCOUNT</div>

        <nav>
            <a class="nav-link" href="{{ route('profile.edit') }}">
                <span class="symbol">⚙️</span> My Profile
            </a>
        </nav>

        <div class="sidebar-note">
            <strong>LOCAL SYSTEM</strong><br>
            Gym Membership Tracker<br>
            Currency: Philippine Peso (PHP)
        </div>
    </aside>

    <main>
        <header class="topbar">
            <div>
                <h1>Dashboard</h1>
                <div class="muted">{{ now()->format('l, F j, Y') }} Membership overview</div>
            </div>

            <div class="user-area">
                <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div>
                    <div class="user-name">{{ auth()->user()->name }}</div>
                    <div class="user-role">Signed in</div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="logout" type="submit">Log out</button>
                </form>
            </div>
        </header>

        <section class="welcome">
            <div>
                <h2>Welcome back, {{ auth()->user()->name }}!</h2>
                <p>
                    Here is your gym's membership overview.<br>
                    Manage members, renewals, payments, and daily attendance from one place.
                </p>
            </div>
            <div class="welcome-mark">🙍‍♂️</div>
        </section>

        <div class="section-heading">
            <h2 class="section-title">Membership overview</h2>
            <span class="muted">Live database totals</span>
        </div>

        <section class="cards">
            <article class="stat">
                <div class="stat-top">
                    <span class="stat-label">Total Members</span>
                    <span class="stat-icon blue">♙</span>
                </div>
                <div class="stat-value">{{ number_format($members) }}</div>
                <div class="stat-foot">Registered member records</div>
            </article>

            <article class="stat">
                <div class="stat-top">
                    <span class="stat-label">Active Memberships</span>
                    <span class="stat-icon green">🟢</span>
                </div>
                <div class="stat-value green">{{ number_format($activeMemberships) }}</div>
                <div class="stat-foot">Currently active, when dates/status exist</div>
            </article>

            <article class="stat">
                <div class="stat-top">
                    <span class="stat-label">Expired Memberships</span>
                    <span class="stat-icon amber">⏳</span>
                </div>
                <div class="stat-value amber">{{ number_format($expiredMemberships) }}</div>
                <div class="stat-foot">Memberships past their end date</div>
            </article>

            <article class="stat">
                <div class="stat-top">
                    <span class="stat-label">Today's Collections</span>
                    <span class="stat-icon purple">💵</span>
                </div>
                <div class="stat-value purple">₱ {{ number_format($todayCollections, 2) }}</div>
                <div class="stat-foot">{{ number_format($todayAttendance) }} attendance records today</div>
            </article>
        </section>

        <div class="section-heading">
            <h2 class="section-title">Recently added members</h2>
            <span class="muted">Latest 5 records</span>
        </div>

        <section class="panel">
            <div class="panel-head">
                <h2>Member directory preview</h2>
                <span>{{ number_format($members) }} total records</span>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>Member</th>
                        <th>Member ID</th>
                        <th>Registered</th>
                        <th>Status</th>
                    </tr>
                    </thead>

                    <tbody>
                    @forelse ($recentMembers as $member)
                        <tr>
                            <td class="member-name">
                                {{ $member->full_name ?? $member->name ?? 'Member record' }}
                            </td>
                            <td>{{ $member->member_code ?? ('MEM-' . str_pad($member->id, 5, '0', STR_PAD_LEFT)) }}</td>
                            <td>
                                {{ $member->created_at
                                    ? \Illuminate\Support\Carbon::parse($member->created_at)->format('M j, Y')
                                    : '—' }}
                            </td>
                            <td><span class="pill">{{ $member->status ?? 'Registered' }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="empty">
                                <strong>No members registered yet</strong><br>
                                Your member records will appear here after the member registration module is installed.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <div class="footer">
            Gym Membership Tracker Local Laravel Application · PHP currency
        </div>
    </main>
</div>
</body>
</html>
