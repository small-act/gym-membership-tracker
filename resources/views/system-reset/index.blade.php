<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-900 text-xl">
                🙍‍♂️
            </div>
            <div>
                <h2 class="text-lg font-bold text-slate-900">GYM TRACKER</h2>
                <p class="text-xs text-slate-500">System administration</p>
            </div>
        </div>
    </x-slot>

    <style>
        .reset-page {
            min-height: calc(100vh - 100px);
            background: #f8fafc;
            color: #202124;
            padding: 55px 20px;
        }

        .reset-card {
            max-width: 620px;
            margin: 0 auto;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 38px 42px;
            box-shadow: 0 12px 40px rgba(15, 23, 42, .06);
        }

        .reset-brand {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 11px;
            margin-bottom: 30px;
        }

        .reset-brand-icon {
            width: 45px;
            height: 45px;
            display: grid;
            place-items: center;
            background: #102746;
            border-radius: 12px;
            font-size: 24px;
        }

        .reset-brand-name {
            color: #102746;
            font-size: 14px;
            font-weight: 800;
            letter-spacing: 1.5px;
        }

        .reset-brand-subtitle {
            color: #718096;
            font-size: 10px;
            letter-spacing: 1px;
        }

        .reset-title {
            text-align: center;
            font-size: 25px;
            font-weight: 750;
            color: #202124;
            margin-bottom: 14px;
        }

        .reset-description {
            text-align: center;
            font-size: 14px;
            line-height: 1.8;
            color: #6b7280;
            margin-bottom: 24px;
        }

        .reset-warning {
            padding: 14px 16px;
            border-left: 4px solid #dc2626;
            border-radius: 5px;
            background: #fff7f7;
            color: #7f1d1d;
            font-size: 13px;
            line-height: 1.7;
            margin-bottom: 25px;
        }

        .reset-warning strong {
            display: block;
            margin-bottom: 4px;
            color: #991b1b;
        }

        .reset-confirm-label {
            text-align: center;
            margin-bottom: 14px;
            color: #374151;
            font-size: 13px;
        }

        .reset-keyword {
            background: #fff1a8;
            padding: 2px 7px;
            font-weight: 800;
            color: #292524;
        }

        .reset-input {
            display: block;
            width: 100%;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 12px 13px;
            color: #111827;
            background: #fff;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }

        .reset-input:focus {
            border-color: #173b66;
            box-shadow: 0 0 0 3px rgba(23, 59, 102, .12);
        }

        .reset-field-label {
            display: block;
            margin-bottom: 8px;
            color: #374151;
            font-size: 13px;
            font-weight: 650;
        }

        .reset-actions {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 24px;
        }

        .reset-confirm-button {
            border: 1px solid #b91c1c;
            background: #b91c1c;
            color: white;
            padding: 11px 21px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
        }

        .reset-confirm-button:hover {
            background: #991b1b;
        }

        .reset-cancel-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #d1d5db;
            color: #4b5563;
            padding: 11px 21px;
            border-radius: 999px;
            font-size: 13px;
            text-decoration: none;
            background: #fff;
        }

        .reset-cancel-button:hover {
            background: #f3f4f6;
        }

        .reset-message {
            padding: 13px 15px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 20px;
        }

        @media(max-width: 600px) {
            .reset-page { padding: 25px 12px; }
            .reset-card { padding: 27px 20px; }
            .reset-title { font-size: 22px; }
        }
    </style>

    <div class="reset-page">
        <div class="reset-card">

            <div class="reset-brand">
                <div class="reset-brand-icon">🙍‍♂️</div>
                <div>
                    <div class="reset-brand-name">GYM TRACKER</div>
                    <div class="reset-brand-subtitle">MEMBERSHIP MANAGEMENT</div>
                </div>
            </div>

            <h1 class="reset-title">Confirm system reset</h1>

            <p class="reset-description">
                This action will return your gym membership records to a fresh
                starting state. Please review the details carefully before continuing.
            </p>

            @if (session('success'))
                <div class="reset-message"
                     style="background:#ecfdf5;color:#166534;border:1px solid #bbf7d0;">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="reset-message"
                     style="background:#fef2f2;color:#991b1b;border:1px solid #fecaca;">
                    <strong>Reset was not completed.</strong>
                    <ul style="list-style:disc;padding-left:20px;margin-top:8px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="reset-warning">
                <strong>⚠ Permanent deletion warning</strong>
                The reset permanently deletes:
                <ul style="list-style:disc;padding-left:20px;margin-top:7px;">
                    <li>Payment and transaction history</li>
                    <li>Attendance and check-in records</li>
                    <li>Membership registrations and renewals</li>
                    <li>Member profiles</li>
                    <li>Membership plans</li>
                </ul>
                <p style="margin-top:9px;">
                    User accounts, including Super Admin accounts, will be preserved.
                    The application code and database structure will remain.
                </p>
            </div>

            <p class="reset-confirm-label">
                To confirm the reset, type
                <span class="reset-keyword">RESET</span>
                below:
            </p>

            <form
                method="POST"
                action="{{ route('system-reset.store') }}"
                onsubmit="return confirm('FINAL WARNING: This permanently deletes the listed gym records and transaction history. User accounts remain. Are you sure you want to continue?');"
            >
                @csrf

                <div style="margin-bottom:20px;">
                    <label for="confirmation" class="reset-field-label">
                        Confirmation word
                    </label>
                    <input
                        id="confirmation"
                        name="confirmation"
                        type="text"
                        required
                        autocomplete="off"
                        placeholder="Type RESET"
                        value="{{ old('confirmation') }}"
                        class="reset-input"
                    >
                    @error('confirmation')
                        <p style="color:#b91c1c;font-size:12px;margin-top:6px;">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="current_password" class="reset-field-label">
                        Super Admin current password
                    </label>
                    <input
                        id="current_password"
                        name="current_password"
                        type="password"
                        required
                        autocomplete="current-password"
                        placeholder="Enter your current password"
                        class="reset-input"
                    >
                    @error('current_password')
                        <p style="color:#b91c1c;font-size:12px;margin-top:6px;">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="reset-actions">
                    <button type="submit" class="reset-confirm-button">
                        Confirm reset
                    </button>

                    <a href="{{ route('dashboard') }}"
                       class="reset-cancel-button">
                        Cancel
                    </a>
                </div>
            </form>

            <p style="text-align:center;color:#9ca3af;font-size:11px;margin-top:25px;line-height:1.6;">
                GYM TRACKER · SUPER ADMIN CONTROL<br>
                Your account will remain active after the reset.
            </p>
        </div>
    </div>
</x-app-layout>
