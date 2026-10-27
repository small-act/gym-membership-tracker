<x-app-layout>
    <div style="max-width:1200px;margin:30px auto;padding:20px;color:#111827;">

        <div style="background:#ffffff;border:1px solid #d1d5db;border-radius:10px;padding:24px;margin-bottom:24px;">
            <h1 style="font-size:26px;font-weight:700;margin:0 0 8px;">
                Gym Tracker — Data Protection
            </h1>
            <p style="margin:0;color:#4b5563;">
                Export and restore your membership management data.
            </p>
        </div>

        @if (session('success'))
            <div style="padding:14px;margin-bottom:16px;background:#dcfce7;color:#166534;border:1px solid #86efac;border-radius:6px;">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div style="padding:14px;margin-bottom:16px;background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;border-radius:6px;">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div style="padding:14px;margin-bottom:16px;background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;border-radius:6px;">
                <strong>Please correct the following:</strong>
                <ul style="list-style:disc;padding-left:24px;margin-top:8px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section style="background:#ffffff;border:1px solid #d1d5db;border-radius:10px;padding:24px;margin-bottom:24px;">
            <h2 style="font-size:21px;font-weight:700;margin:0 0 12px;">
                Export All Data
            </h2>

            <p style="margin-bottom:16px;color:#4b5563;">
                Download a JSON backup of membership plans, members,
                memberships, attendance, and payment records.
                User accounts and passwords are excluded.
            </p>

            <a
                href="{{ route('data-transfer.export') }}"
                style="display:inline-block;background:#1d4ed8;color:#ffffff;padding:13px 22px;border-radius:6px;text-decoration:none;font-weight:700;border:2px solid #1e40af;cursor:pointer;"
            >
                DOWNLOAD BACKUP
            </a>
        </section>

        <section style="background:#ffffff;border:1px solid #d1d5db;border-radius:10px;padding:24px;margin-bottom:24px;">
            <h2 style="font-size:21px;font-weight:700;margin:0 0 12px;">
                Import / Restore Data
            </h2>

            <div style="padding:14px;background:#fffbeb;border:1px solid #fcd34d;border-radius:6px;margin-bottom:18px;">
                <strong>Important</strong>
                <ul style="list-style:disc;padding-left:24px;margin-top:8px;">
                    <li>All operational data tables must be empty before restoring.</li>
                    <li>Existing user accounts are preserved.</li>
                    <li>Only a valid JSON backup created by this system is supported.</li>
                    <li>Back up important current records before any reset.</li>
                </ul>
            </div>

            <form
                action="{{ route('data-transfer.import') }}"
                method="POST"
                enctype="multipart/form-data"
                onsubmit="return confirm('Are you sure you want to restore this backup?');"
            >
                @csrf

                <div style="margin-bottom:18px;">
                    <label for="backup_file" style="display:block;font-weight:700;margin-bottom:8px;">
                        Select Backup File (.json)
                    </label>

                    <input
                        id="backup_file"
                        type="file"
                        name="backup_file"
                        accept=".json,application/json"
                        required
                        style="display:block;width:100%;padding:10px;border:1px solid #9ca3af;border-radius:6px;background:#ffffff;"
                    >
                </div>

                <div style="margin-bottom:18px;">
                    <label for="restore_confirmation" style="display:block;font-weight:700;margin-bottom:8px;">
                        Type RESTORE to confirm
                    </label>

                   <input
    id="restore_confirmation"
    type="text"
    name="confirmation"
    placeholder="Type RESTORE"
    required
    pattern="RESTORE"
    title="Type RESTORE in capital letters."
    style="display:block;width:100%;padding:12px;border:1px solid #9ca3af;border-radius:6px;"
>
                </div>

                <button
                    type="submit"
                    style="display:inline-block;background:#b91c1c;color:#ffffff;padding:13px 22px;border:2px solid #991b1b;border-radius:6px;font-weight:700;cursor:pointer;"
                >
                    IMPORT / RESTORE BACKUP
                </button>
            </form>
        </section>

        <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;background:#ffffff;border:1px solid #d1d5db;border-radius:10px;padding:18px;">
            <span>Access: Admin and Super Admin</span>

            <a
                href="{{ route('dashboard') }}"
                style="display:inline-block;background:#374151;color:#ffffff;padding:11px 18px;border-radius:6px;text-decoration:none;font-weight:700;"
            >
                BACK TO DASHBOARD
            </a>
        </div>

    </div>
</x-app-layout>
