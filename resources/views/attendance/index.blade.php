<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Attendance | Gym Membership Tracker</title>

    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            background: #071426;
            color: #eaf1fb;
            font-family: Arial, sans-serif;
        }
        .layout { display: flex; min-height: 100vh; }
        .sidebar {
            width: 235px;
            flex-shrink: 0;
            background: #0b1c32;
            border-right: 1px solid #203650;
            padding: 26px 16px;
        }
        .brand { font-size: 18px; font-weight: 800; margin: 0 8px 30px; }
        .brand small {
            display: block; margin-top: 7px; color: #8da5c4; font-size: 11px;
        }
        .nav-link {
            display: block; padding: 12px; margin: 5px 0;
            color: #b7c8df; text-decoration: none; border-radius: 8px;
            font-size: 14px;
        }
        .nav-link:hover, .nav-link.active { background: #173657; color: white; }
        .main { flex: 1; min-width: 0; padding: 30px; }
        .top {
            display: flex; justify-content: space-between; align-items: center;
            gap: 15px; flex-wrap: wrap; margin-bottom: 24px;
        }
        h1 { margin: 0 0 7px; font-size: 27px; }
        h2 { font-size: 17px; margin: 0 0 18px; }
        h3 { font-size: 15px; margin: 0 0 10px; }
        .muted { color: #93a8c3; font-size: 13px; }
        .panel, .stat {
            background: #0d2038; border: 1px solid #213b59;
            border-radius: 13px; padding: 20px; margin-bottom: 22px;
        }
        .stats {
            display: grid; grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 15px; margin-bottom: 22px;
        }
        .stat { margin: 0; }
        .stat-label { color: #9bb0ca; font-size: 13px; }
        .stat-value { font-size: 28px; font-weight: 800; margin-top: 10px; }
        .form-grid {
            display: grid; grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }
        label { display: block; color: #c5d4e7; font-size: 13px; margin-bottom: 7px; }
        input, select, textarea {
            width: 100%; padding: 11px 12px; border-radius: 8px;
            background: #08182b; border: 1px solid #304967; color: #edf5ff;
            font: inherit; font-size: 14px;
        }
        textarea { min-height: 75px; resize: vertical; }
        .full { grid-column: 1 / -1; }
        button, .btn {
            display: inline-block; border: 0; border-radius: 8px; padding: 11px 15px;
            background: #2876d2; color: white; cursor: pointer;
            text-decoration: none; font-weight: 700; font-size: 13px;
        }
        button:hover, .btn:hover { background: #3688e8; }
        button:disabled { opacity: .55; cursor: not-allowed; }
        .btn-secondary { background: #29415e; }
        .btn-green { background: #147d5a; }
        .btn-small { padding: 8px 10px; }
        .message {
            padding: 12px 14px; border-radius: 8px;
            margin-bottom: 16px; font-size: 14px;
        }
        .success { background: #123b32; border: 1px solid #287a61; color: #aaf2d6; }
        .error { background: #451f2a; border: 1px solid #8a3445; color: #ffc3cb; }
        .info { background: #142f4d; border: 1px solid #2a5c8d; color: #cce6ff; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 780px; }
        th, td {
            text-align: left; padding: 13px 11px;
            border-bottom: 1px solid #213750; font-size: 13px;
        }
        th { color: #9fb5d0; font-weight: 600; }
        .badge {
            display: inline-block; border-radius: 20px; padding: 5px 9px;
            font-size: 11px; font-weight: 700;
        }
        .badge-in { background: #164637; color: #a9f1d0; }
        .badge-out { background: #253a55; color: #c5d8f2; }
        .filters {
            display: flex; gap: 10px; align-items: end; flex-wrap: wrap;
            margin-bottom: 18px;
        }
        .filters > div { min-width: 180px; }
        .pagination { margin-top: 16px; }
        .pagination a, .pagination span { color: #dceaff; margin-right: 8px; }
        .empty { padding: 24px; text-align: center; color: #93a8c3; }
        .scanner-grid {
            display: grid; grid-template-columns: minmax(0, 1.2fr) minmax(0, 1fr);
            gap: 20px; align-items: start;
        }
        #qr-reader {
            width: 100%; max-width: 560px; overflow: hidden;
            border: 1px solid #304967; border-radius: 10px;
            background: #06101e;
        }
        #qr-reader video { border-radius: 8px; }
        .scanner-controls { display: flex; flex-wrap: wrap; gap: 10px; margin: 12px 0; }
        .result-box { min-height: 54px; margin-top: 12px; }
        .qr-cards {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
            gap: 14px; margin-top: 18px;
        }
        .qr-card {
            background: #f8fbff; color: #132238; border-radius: 10px;
            padding: 15px; text-align: center; overflow-wrap: anywhere;
        }
        .qr-card .qr-image {
            min-height: 150px; display: flex; align-items: center;
            justify-content: center; margin: 12px 0;
        }
        .qr-card canvas, .qr-card img { max-width: 100%; height: auto; }
        .qr-name { font-weight: 800; font-size: 14px; }
        .qr-number { color: #53657c; font-size: 12px; margin-top: 5px; }
        .qr-help { margin-top: 12px; }
        .section-actions { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 12px; }
        .hidden { display: none !important; }
        @media(max-width: 950px) {
            .scanner-grid { grid-template-columns: 1fr; }
        }
        @media(max-width: 850px) {
            .layout { display: block; }
            .sidebar { width: 100%; padding: 14px; }
            .sidebar nav { display: flex; flex-wrap: wrap; }
            .brand { margin-bottom: 12px; }
            .main { padding: 18px; }
            .stats { grid-template-columns: 1fr; }
        }
        @media(max-width: 560px) {
            .form-grid { grid-template-columns: 1fr; }
            .full { grid-column: auto; }
            .filters > div { min-width: 100%; }
        }
        @media print {
            body { background: white; color: black; }
            .sidebar, .top, .stats, .no-print, .panel:not(#printable-qrs),
            #printable-qrs > h2, #printable-qrs > p { display: none !important; }
            .layout, .main { display: block; padding: 0; margin: 0; }
            #printable-qrs { display: block !important; background: white; border: 0; padding: 0; }
            .qr-cards { grid-template-columns: repeat(3, 1fr); gap: 10px; }
            .qr-card { border: 1px solid #bbb; break-inside: avoid; }
            .qr-card .qr-image { min-height: 130px; }
            .qr-card button { display: none; }
        }
    </style>
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <div class="brand">GYM TRACKER<small>MEMBERSHIP MANAGEMENT</small></div>
        <nav>
            <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
            <a class="nav-link" href="{{ route('members.index') }}">Members</a>
            <a class="nav-link" href="{{ route('memberships.index') }}">Memberships</a>
            <a class="nav-link" href="{{ route('membership-plans.index') }}">Membership Plans</a>
            <a class="nav-link active" href="{{ route('attendance.index') }}">Attendance / QR</a>
            <a class="nav-link" href="{{ route('payments.index') }}">Payments</a>
            <a class="nav-link" href="{{ url('/reports') }}">Reports</a>
        </nav>
    </aside>

    <main class="main">
        <div class="top">
            <div>
                <h1>Attendance / Check-in</h1>
                <div class="muted">Manual attendance, QR scanning, and member QR cards.</div>
            </div>
            <div class="muted">{{ now()->format('l, F j, Y') }}</div>
        </div>

        @if(session('success'))
            <div class="message success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="message error">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="message error">
                <strong>Please correct the following:</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="stats">
            <div class="stat">
                <div class="stat-label">Today's Visits</div>
                <div class="stat-value">{{ $todayCount }}</div>
            </div>
            <div class="stat">
                <div class="stat-label">Currently Checked In</div>
                <div class="stat-value">{{ $checkedInCount }}</div>
            </div>
            <div class="stat">
                <div class="stat-label">Checked Out Today</div>
                <div class="stat-value">{{ $checkedOutCount }}</div>
            </div>
        </section>

        <section class="panel no-print">
            <h2>QR Camera Scanner</h2>
            <p class="muted">
                Select the attendance action before scanning a member's QR code.
                QR scanning requires an active membership valid today.
            </p>

            <div class="section-actions" aria-label="QR attendance mode">
                <button type="button" id="mode-check-in" aria-pressed="true">
                    QR Check-in
                </button>
                <button type="button" id="mode-check-out" class="btn-secondary" aria-pressed="false">
                    QR Check-out
                </button>
            </div>

            <p id="selected-mode-label" class="message info">
                Selected mode: QR Check-in
            </p>

            <div class="scanner-grid">
                <div>
                    <div id="qr-reader"></div>
                    <div class="scanner-controls">
                        <button type="button" id="start-scanner">Start Camera Scanner</button>
                        <button type="button" id="stop-scanner" class="btn-secondary" disabled>Stop Camera</button>
                    </div>
                    <div id="scanner-status" class="message info result-box" role="status" aria-live="polite">
                        Scanner is stopped. Press Start Camera Scanner to begin.
                    </div>
                </div>
                <div>
                    <h3>Scanner instructions</h3>
                    <ol class="muted">
                        <li>Allow camera permission when your browser asks.</li>
                        <li>Use the member QR card displayed below.</li>
                        <li>Wait for the check-in result before scanning another member.</li>
                        <li>Use manual check-in if the camera or network is unavailable.</li>
                    </ol>
                    <p class="muted">
                        A QR code can only check a member in once per day. A valid active
                        membership is required for QR check-in.
                    </p>
                    <div id="scan-result" aria-live="polite"></div>
                </div>
            </div>
        </section>

        <section class="panel no-print">
            <h2>Manual Member Check-in</h2>
            <form method="POST" action="{{ route('attendance.check-in') }}">
                @csrf
                <div class="form-grid">
                    <div>
                        <label for="member_id">Active member *</label>
                        <select name="member_id" id="member_id" required>
                            <option value="">Select a member</option>
                            @foreach($members as $member)
                                <option value="{{ $member->id }}" @selected(old('member_id') == $member->id)>
                                    {{ $member->full_name }} — {{ $member->member_number }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="check_in_method">Check-in method *</label>
                        <select name="check_in_method" id="check_in_method" required>
                            <option value="manual" @selected(old('check_in_method', 'manual') === 'manual')>Manual</option>
                        </select>
                    </div>
                    <div class="full">
                        <label for="notes">Notes (optional)</label>
                        <textarea name="notes" id="notes" maxlength="1000" placeholder="Optional notes about this visit">{{ old('notes') }}</textarea>
                    </div>
                    <div class="full">
                        <button type="submit" class="btn-green" style="min-width:190px;padding:14px 20px;font-size:15px;">CHECK IN MEMBER</button>
                    </div>
                </div>
            </form>
            @if($members->isEmpty())
                <p class="muted">No active members found. Add or activate a member before checking them in.</p>
            @endif
        </section>

        <section class="panel no-print">
            <h2>Member QR Cards</h2>
            <p class="muted">
                Each QR code contains a random member token, not the member's database ID.
                Print the cards and give each member only their own card.
            </p>
            <div class="section-actions">
                <button type="button" id="toggle-qrs" class="btn-secondary">Show Member QR Codes</button>
                <button type="button" id="print-qrs" class="btn-green">Print QR Cards</button>
            </div>
            <div id="qr-library" class="hidden">
                @if($members->isEmpty())
                    <p class="muted">There are no active members to display.</p>
                @else
                    <div class="qr-cards" id="qr-cards">
                        @foreach($members as $member)
                            <div class="qr-card">
                                <div class="qr-name">{{ $member->full_name }}</div>
                                <div class="qr-number">Member No: {{ $member->member_number }}</div>
                                <div class="qr-image"
                                     data-qr-token="{{ $member->qr_token }}"
                                     aria-label="QR code for {{ $member->full_name }}"></div>
                                <div class="qr-number">GYM MEMBERSHIP</div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            <div class="qr-help muted">
                Keep QR cards private. If a card is lost, the member's QR token should be replaced.
            </div>
        </section>

        <section class="panel">
            <h2>Attendance Records</h2>
            <form method="GET" action="{{ route('attendance.index') }}" class="filters">
                <div>
                    <label for="date">Attendance date</label>
                    <input type="date" id="date" name="date" value="{{ $date }}">
                </div>
                <div>
                    <label for="search">Search member</label>
                    <input type="text" id="search" name="search" value="{{ $search }}" placeholder="Name, member number, phone">
                </div>
                <button type="submit">Search</button>
                <a class="btn btn-secondary" href="{{ route('attendance.index') }}">Today</a>
            </form>

            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>Member</th>
                        <th>Member No.</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($attendances as $attendance)
                        <tr>
                            <td>{{ $attendance->member?->full_name ?? 'Member unavailable' }}</td>
                            <td>{{ $attendance->member?->member_number ?? '—' }}</td>
                            <td>{{ $attendance->check_in_at?->format('h:i A') ?? '—' }}</td>
                            <td>{{ $attendance->check_out_at?->format('h:i A') ?? '—' }}</td>
                            <td>{{ strtoupper($attendance->check_in_method) }}</td>
                            <td>
                                @if($attendance->check_out_at)
                                    <span class="badge badge-out">Checked out</span>
                                @else
                                    <span class="badge badge-in">Checked in</span>
                                @endif
                            </td>
                            <td>
                                @if(!$attendance->check_out_at && $attendance->check_in_at)
                                    <form method="POST" action="{{ route('attendance.check-out', $attendance) }}" onsubmit="return confirm('Confirm check-out for this member?');">
                                        @csrf
                                        <button type="submit" class="btn-small btn-green" style="min-width:105px;">CHECK OUT</button>
                                    </form>
                                @else
                                    <span class="muted">Completed</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="empty">No attendance records found for this date.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="pagination">{{ $attendances->links() }}</div>
        </section>
    </main>
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const scannerStatus = document.getElementById('scanner-status');
    const scanResult = document.getElementById('scan-result');
    const startButton = document.getElementById('start-scanner');
    const stopButton = document.getElementById('stop-scanner');
    const qrLibrary = document.getElementById('qr-library');
    const toggleQrsButton = document.getElementById('toggle-qrs');
    const printQrsButton = document.getElementById('print-qrs');

    let selectedScanMode = 'check-in';
    const checkInModeButton = document.getElementById('mode-check-in');
    const checkOutModeButton = document.getElementById('mode-check-out');
    const selectedModeLabel = document.getElementById('selected-mode-label');

    let scanner = null;
    let scannerRunning = false;
    let processingScan = false;
    let lastScannedToken = '';
    let lastScannedAt = 0;

    function showMessage(element, message, type) {
        element.className = 'message ' + type;
        element.textContent = message;
    }

    function setScannerButtons(running) {
        scannerRunning = running;
        startButton.disabled = running;
        stopButton.disabled = !running;
    }

    function createQrCards() {
        if (typeof QRCode === 'undefined') {
            showMessage(scannerStatus, 'QR display library failed to load. Check your internet connection and refresh.', 'error');
            return;
        }

        document.querySelectorAll('.qr-image[data-qr-token]').forEach(function (container) {
            if (container.dataset.generated === '1') return;

            const token = container.dataset.qrToken;
            if (!token) {
                container.textContent = 'QR token unavailable';
                return;
            }

            container.innerHTML = '';
            new QRCode(container, {
                text: token,
                width: 150,
                height: 150,
                colorDark: '#071426',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.M
            });
            container.dataset.generated = '1';
        });
    }

    toggleQrsButton.addEventListener('click', function () {
        const willShow = qrLibrary.classList.contains('hidden');
        qrLibrary.classList.toggle('hidden', !willShow);
        toggleQrsButton.textContent = willShow ? 'Hide Member QR Codes' : 'Show Member QR Codes';

        if (willShow) createQrCards();
    });

    printQrsButton.addEventListener('click', function () {
        qrLibrary.classList.remove('hidden');
        toggleQrsButton.textContent = 'Hide Member QR Codes';
        createQrCards();

        if (typeof QRCode === 'undefined') {
            showMessage(scannerStatus, 'QR display library is unavailable. Check your internet connection.', 'error');
            return;
        }

        window.setTimeout(function () {
            window.print();
        }, 500);
    });

    async function stopScanner() {
        if (!scanner || !scannerRunning) return;

        try {
            await scanner.stop();
            scanner.clear();
        } catch (error) {
            console.warn('Scanner stop:', error);
        }

        setScannerButtons(false);
        showMessage(scannerStatus, 'Camera scanner stopped.', 'info');
    }

    async function submitScannedToken(token) {
        const nowMs = Date.now();

        if (processingScan) return;
        if (token === lastScannedToken && nowMs - lastScannedAt < 5000) return;

        lastScannedToken = token;
        lastScannedAt = nowMs;
        processingScan = true;

        showMessage(scannerStatus, 'QR code detected. Checking membership and recording attendance...', 'info');
        scanResult.innerHTML = '';

        try {
            if (scanner && scannerRunning) {
                await scanner.stop();
                scanner.clear();
                setScannerButtons(false);
            }

            const response = await fetch(@json(route('attendance.qr-check-in')), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ token: token, mode: selectedScanMode })
            });

            let data = {};
            try {
                data = await response.json();
            } catch (parseError) {
                data = { message: 'The server returned an unexpected response. Refresh and try again.' };
            }

            if (response.ok && data.success) {
                const actionLabel = data.action === 'check-out'
                    ? 'CHECK-OUT'
                    : 'CHECK-IN';

                showMessage(
                    scannerStatus,
                    'SUCCESS: ' + actionLabel + ' — ' +
                    (data.member || 'Member') + ' (' +
                    (data.member_number || '') + ') at ' +
                    (data.time || ''),
                    'success'
                );

                const detail = document.createElement('p');
                detail.className = 'message success';
                detail.textContent = 'Attendance saved. You can scan another member.';
                scanResult.replaceChildren(detail);

                const retryButton = document.createElement('button');
                retryButton.type = 'button';
                retryButton.textContent = 'Scan Another Member';
                retryButton.addEventListener('click', startScanner);
                scanResult.appendChild(retryButton);
            } else {
                const statusType = response.status === 409 ? 'info' : 'error';
                showMessage(scannerStatus, data.message || 'QR check-in failed.', statusType);

                const retryButton = document.createElement('button');
                retryButton.type = 'button';
                retryButton.textContent = 'Scan Another Member';
                retryButton.addEventListener('click', startScanner);
                scanResult.replaceChildren(retryButton);
            }
        } catch (error) {
            console.error(error);
            showMessage(
                scannerStatus,
                'Could not contact the server. Check your connection, then try again.',
                'error'
            );

            const retryButton = document.createElement('button');
            retryButton.type = 'button';
            retryButton.textContent = 'Try Scanner Again';
            retryButton.addEventListener('click', startScanner);
            scanResult.replaceChildren(retryButton);
        } finally {
            processingScan = false;
        }
    }

    async function startScanner() {
        if (scannerRunning || processingScan) return;

        if (typeof Html5Qrcode === 'undefined') {
            showMessage(
                scannerStatus,
                'Scanner library did not load. Check your internet connection and refresh this page.',
                'error'
            );
            return;
        }

        try {
            scanResult.innerHTML = '';
            scanner = new Html5Qrcode('qr-reader');
            showMessage(scannerStatus, 'Requesting camera permission...', 'info');

            await scanner.start(
                { facingMode: 'environment' },
                { fps: 10, qrbox: { width: 240, height: 240 }, aspectRatio: 1.0 },
                function (decodedText) {
                    submitScannedToken(decodedText);
                },
                function () {
                    // Normal frame-by-frame scan misses are expected.
                }
            );

            setScannerButtons(true);
            showMessage(scannerStatus, 'Camera active. Point it at a member QR card.', 'info');
        } catch (error) {
            console.error(error);
            setScannerButtons(false);

            let message = 'Unable to start camera. Allow camera permission and try again.';
            if (window.location.protocol !== 'https:' && window.location.hostname !== 'localhost' && window.location.hostname !== '127.0.0.1') {
                message += ' Camera access on this address may require HTTPS.';
            }

            showMessage(scannerStatus, message, 'error');
        }
    }

    function selectScanMode(mode) {
        if (scannerRunning || processingScan) {
            showMessage(scannerStatus, 'Stop the camera before changing attendance mode.', 'info');
            return;
        }

        selectedScanMode = mode;
        const checkingIn = mode === 'check-in';

        checkInModeButton.setAttribute('aria-pressed', String(checkingIn));
        checkOutModeButton.setAttribute('aria-pressed', String(!checkingIn));
        checkInModeButton.classList.toggle('btn-secondary', !checkingIn);
        checkOutModeButton.classList.toggle('btn-secondary', checkingIn);

        selectedModeLabel.textContent = checkingIn
            ? 'Selected mode: QR Check-in'
            : 'Selected mode: QR Check-out';

        showMessage(
            scannerStatus,
            checkingIn
                ? 'QR Check-in selected. Start the camera to scan a member.'
                : 'QR Check-out selected. Start the camera to scan a member who has checked in.',
            'info'
        );
    }

    checkInModeButton.addEventListener('click', function () {
        selectScanMode('check-in');
    });

    checkOutModeButton.addEventListener('click', function () {
        selectScanMode('check-out');
    });

    startButton.addEventListener('click', startScanner);
    stopButton.addEventListener('click', stopScanner);

    if (typeof QRCode === 'undefined') {
        console.warn('QR code library has not loaded yet. QR cards will be generated when opened.');
    }
});
</script>
</body>
</html>

