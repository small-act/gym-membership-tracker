<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#081426">
    <title>Members | Gym Membership Tracker</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 30px; background: #081426; color: #edf4ff; font-family: Arial, sans-serif; }
        .container { max-width: 1250px; margin: auto; }
        .top, .actions { display: flex; align-items: center; justify-content: space-between; gap: 15px; flex-wrap: wrap; }
        .panel { margin-top: 22px; padding: 22px; background: #11213a; border: 1px solid #263a56; border-radius: 14px; }
        .muted { color: #9fb0c9; }
        a { color: #7dd3fc; text-decoration: none; }
        a:hover { text-decoration: underline; }
        .btn { display: inline-block; padding: 11px 15px; border: 0; border-radius: 8px; background: #1677e8; color: white; cursor: pointer; font-size: 13px; font-weight: bold; text-decoration: none; }
        .btn:hover { background: #3688e8; text-decoration: none; }
        .secondary { background: #263a56; }
        .secondary:hover { background: #354e70; }
        .danger { background: #b42336; }
        .danger:hover { background: #d12e43; }
        input { padding: 11px; border-radius: 8px; border: 1px solid #334967; background: #081426; color: white; min-width: 230px; }
        input:focus { outline: 2px solid #2876d2; outline-offset: 1px; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 18px; }
        th, td { padding: 14px 12px; text-align: left; vertical-align: middle; border-bottom: 1px solid #263a56; }
        th { color: #9fb0c9; font-size: 12px; text-transform: uppercase; white-space: nowrap; }
        td { font-size: 13px; }
        .status { display: inline-block; padding: 5px 9px; border-radius: 20px; background: #123e35; color: #86efac; font-size: 12px; }
        .inactive { background: #49303a; color: #fda4af; }
        .notice, .error { padding: 13px; border-radius: 8px; margin-top: 12px; }
        .notice { background: #123e35; color: #86efac; }
        .error { background: #49303a; color: #fda4af; }
        .pagination { margin-top: 20px; }
        .member-actions { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .member-actions form { margin: 0; }
        .member-actions .btn { padding: 8px 11px; }
        .empty-state { text-align: center; padding: 28px; }
        @media (max-width: 600px) {
            body { padding: 14px; }
            input { min-width: 100%; width: 100%; }
            .panel { padding: 14px; }
            .top { align-items: flex-start; }
            .actions { align-items: stretch; }
            .actions .btn { text-align: center; }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="top">
        <div>
            <a href="{{ route('dashboard') }}">&#8592; Home</a>
            <h1>Members Management</h1>
            <p class="muted">Register, search, edit, and delete gym member records.</p>
        </div>
        <a class="btn" href="{{ route('members.create') }}">+ Register Member</a>
    </div>

    @if(session('success'))
        <div class="notice" role="status">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="error" role="alert">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="error" role="alert">
            <strong>Please correct these errors:</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="panel">
        <form class="actions" method="GET" action="{{ route('members.index') }}">
            <input type="search" name="search"
                   value="{{ $search ?? request('search', '') }}"
                   placeholder="Search name, member no., email, phone"
                   aria-label="Search members">
            <button class="btn" type="submit">Search</button>
            <a class="btn secondary" href="{{ route('members.index') }}">Clear</a>
            <span class="muted">{{ $members->total() }} member(s)</span>
        </form>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Member No.</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Registered</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($members as $member)
                    <tr>
                        <td>{{ $member->member_number ?? 'Pending' }}</td>
                        <td>{{ $member->full_name ?? 'Unnamed member' }}</td>
                        <td>{{ $member->email ?? '—' }}</td>
                        <td>{{ $member->phone ?? '—' }}</td>
                        <td>
                            <span class="status {{ strtolower($member->status ?? '') === 'inactive' ? 'inactive' : '' }}">
                                {{ ucfirst($member->status ?? 'unknown') }}
                            </span>
                        </td>
                        <td>{{ $member->created_at?->format('M d, Y') ?? '—' }}</td>
                        <td>
                            <div class="member-actions">
                                <a class="btn"
                                   href="{{ route('members.edit', $member) }}"
                                   aria-label="Edit {{ $member->full_name }}">
                                    Edit
                                </a>
                                <form method="POST"
                                      action="{{ route('members.destroy', $member) }}"
                                      onsubmit="return confirm('Permanently delete this member? Related records may prevent deletion.');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn danger" type="submit"
                                            aria-label="Delete {{ $member->full_name }}">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="muted empty-state">
                            No members found. Select Register Member to add a member or try a different search.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">
            {{ $members->withQueryString()->links() }}
        </div>
    </div>
</div>
</body>
</html>
