<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#081426">
    <title>Record Payment | Gym Membership Tracker</title>

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            padding: 25px 15px;
            background: #081426;
            color: #edf4ff;
            font-family: Arial, sans-serif;
        }

        .panel {
            max-width: 780px;
            margin: 20px auto;
            padding: 28px;
            background: #0d1e33;
            border: 1px solid #20344e;
            border-radius: 16px;
        }

        h1 { margin-top: 0; font-size: 26px; }
        h2 { margin-top: 0; font-size: 21px; }

        .muted {
            color: #91a6c2;
            font-size: 13px;
            line-height: 1.6;
        }

        label {
            display: block;
            margin: 18px 0 7px;
            font-size: 13px;
            font-weight: bold;
        }

        input, select, textarea {
            width: 100%;
            padding: 12px;
            background: #081426;
            color: #edf4ff;
            border: 1px solid #38516e;
            border-radius: 8px;
            font: inherit;
        }

        input:focus, select:focus, textarea:focus {
            outline: 2px solid #3b82f6;
            outline-offset: 1px;
        }

        textarea { min-height: 85px; resize: vertical; }

        .button {
            display: inline-block;
            padding: 12px 16px;
            background: #2563eb;
            color: #fff;
            border: 0;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            font-weight: bold;
            font-size: 13px;
        }

        .button:disabled {
            opacity: .55;
            cursor: not-allowed;
        }

        .secondary { background: #263b56; }
        .success-button { background: #15803d; }

        .error {
            color: #fecdd3;
            font-size: 13px;
            margin-top: 6px;
        }

        .notice {
            padding: 14px;
            margin-top: 15px;
            background: #172d49;
            border-radius: 8px;
            color: #bfd7f7;
            font-size: 13px;
            line-height: 1.6;
        }

        .actions {
            display: flex;
            gap: 10px;
            margin-top: 24px;
            flex-wrap: wrap;
        }

        .form-error {
            padding: 13px;
            margin-bottom: 18px;
            background: #4b2933;
            border-radius: 8px;
            color: #fecdd3;
            font-size: 13px;
        }

        /* Confirmation modal */
        .modal-backdrop {
            position: fixed;
            inset: 0;
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 18px;
            background: rgba(0, 0, 0, .76);
        }

        .modal-backdrop.open { display: flex; }

        .modal {
            width: 100%;
            max-width: 540px;
            max-height: 90vh;
            overflow-y: auto;
            padding: 26px;
            background: #0d1e33;
            border: 1px solid #38516e;
            border-radius: 16px;
            box-shadow: 0 25px 80px rgba(0, 0, 0, .5);
        }

        .modal-subtitle {
            margin-top: -8px;
            margin-bottom: 20px;
            color: #91a6c2;
            font-size: 13px;
            line-height: 1.6;
        }

        .summary {
            padding: 15px;
            border: 1px solid #263b56;
            border-radius: 10px;
            background: #081426;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 18px;
            padding: 11px 0;
            border-bottom: 1px solid #20344e;
            font-size: 13px;
        }

        .summary-row:last-child { border-bottom: 0; }

        .summary-label { color: #91a6c2; }

        .summary-value {
            text-align: right;
            font-weight: bold;
            overflow-wrap: anywhere;
        }

        .summary-amount {
            font-size: 22px;
            color: #86efac;
        }

        .confirmation-check {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin: 18px 0;
            padding: 14px;
            background: #172d49;
            border-radius: 9px;
            font-size: 13px;
            line-height: 1.6;
            cursor: pointer;
        }

        .confirmation-check input {
            width: 17px;
            height: 17px;
            flex-shrink: 0;
            margin-top: 2px;
            accent-color: #22c55e;
        }

        .confirmation-check span { flex: 1; }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        @media(max-width: 540px) {
            .panel { padding: 20px; }
            .modal { padding: 20px; }
            .summary-row { gap: 10px; }
        }
    </style>
</head>
<body>

<div class="panel">
    <h1>Record Payment</h1>
    <p class="muted">
        Enter the payment details, review them carefully, and confirm before
        saving the transaction.
    </p>

    @if ($errors->any())
        <div class="form-error">
            <strong>Please correct the following:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($memberships->isEmpty())
        <div class="notice">
            There are no memberships to charge yet.
            <a href="{{ route('memberships.create') }}" style="color:#93c5fd">
                Register a membership first.
            </a>
        </div>
    @else
        <form id="paymentForm" method="POST" action="{{ route('payments.store') }}">
            @csrf

            <label for="membership_id">Member and Membership *</label>
            <select id="membership_id" name="membership_id" required>
                <option value="">Select a membership</option>

                @foreach ($memberships as $membership)
                    <option
                        value="{{ $membership->id }}"
                        data-member="{{ $membership->member?->full_name ?? 'Member unavailable' }}"
                        data-plan="{{ $membership->plan?->name ?? 'Plan unavailable' }}"
                        data-price="{{ $membership->price }}"
                        data-end="{{ $membership->end_date?->format('M j, Y') ?? 'Not available' }}"
                        @selected((string) old('membership_id') === (string) $membership->id)
                    >
                        {{ $membership->member?->full_name ?? 'Member unavailable' }}
                        — {{ $membership->plan?->name ?? 'Plan unavailable' }}
                        — ₱{{ number_format((float) $membership->price, 2) }}
                        (ends {{ $membership->end_date?->format('M j, Y') ?? 'N/A' }})
                    </option>
                @endforeach
            </select>
            @error('membership_id')
                <div class="error">{{ $message }}</div>
            @enderror

            <label for="amount">Amount (PHP) *</label>
            <input
                id="amount"
                type="number"
                name="amount"
                min="0.01"
                max="99999999.99"
                step="0.01"
                value="{{ old('amount') }}"
                required
            >
            @error('amount')
                <div class="error">{{ $message }}</div>
            @enderror

            <label for="payment_date">Payment Date *</label>
            <input
                id="payment_date"
                type="date"
                name="payment_date"
                max="{{ now()->toDateString() }}"
                value="{{ old('payment_date', now()->toDateString()) }}"
                required
            >
            @error('payment_date')
                <div class="error">{{ $message }}</div>
            @enderror

            <label for="payment_method">Payment Method *</label>
            <select id="payment_method" name="payment_method" required>
                <option value="cash" @selected(old('payment_method', 'cash') === 'cash')>Cash</option>
                <option value="gcash" @selected(old('payment_method') === 'gcash')>GCash</option>
                <option value="bank_transfer" @selected(old('payment_method') === 'bank_transfer')>Bank Transfer</option>
                <option value="card" @selected(old('payment_method') === 'card')>Card</option>
                <option value="other" @selected(old('payment_method') === 'other')>Other</option>
            </select>
            @error('payment_method')
                <div class="error">{{ $message }}</div>
            @enderror

            <label for="reference_number">Reference Number (optional)</label>
            <input
                id="reference_number"
                name="reference_number"
                maxlength="100"
                value="{{ old('reference_number') }}"
                placeholder="Official receipt or transaction reference"
            >
            @error('reference_number')
                <div class="error">{{ $message }}</div>
            @enderror

            <label for="status">Payment Status *</label>
            <select id="status" name="status" required>
                <option value="paid" @selected(old('status', 'paid') === 'paid')>
                    Paid — money received
                </option>
                <option value="pending" @selected(old('status') === 'pending')>
                    Pending — awaiting confirmation
                </option>
                <option value="failed" @selected(old('status') === 'failed')>
                    Failed — money not received
                </option>
            </select>
            @error('status')
                <div class="error">{{ $message }}</div>
            @enderror

            <label for="notes">Notes (optional)</label>
            <textarea id="notes" name="notes" maxlength="2000">{{ old('notes') }}</textarea>
            @error('notes')
                <div class="error">{{ $message }}</div>
            @enderror

            <div class="notice">
                Your account is recorded as the person processing this transaction.
                Check all details before confirming.
            </div>

            <div class="actions">
                <button class="button" type="submit" id="reviewButton">
                    Review Payment
                </button>
                <a class="button secondary" href="{{ route('payments.index') }}">
                    Cancel
                </a>
            </div>
        </form>
    @endif
</div>

<!-- Confirmation popup -->
<div
    class="modal-backdrop"
    id="confirmationModal"
    role="dialog"
    aria-modal="true"
    aria-labelledby="modalTitle"
    aria-hidden="true"
>
    <div class="modal">
        <h2 id="modalTitle">Confirm Payment Details</h2>
        <p class="modal-subtitle" id="modalDescription">
            Review the transaction details before saving.
        </p>

        <div class="summary">
            <div class="summary-row">
                <span class="summary-label">Member</span>
                <span class="summary-value" id="reviewMember">—</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Membership plan</span>
                <span class="summary-value" id="reviewPlan">—</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Membership ends</span>
                <span class="summary-value" id="reviewEnd">—</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Amount</span>
                <span class="summary-value summary-amount" id="reviewAmount">₱0.00</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Payment date</span>
                <span class="summary-value" id="reviewDate">—</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Payment method</span>
                <span class="summary-value" id="reviewMethod">—</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Reference number</span>
                <span class="summary-value" id="reviewReference">—</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Status</span>
                <span class="summary-value" id="reviewStatus">—</span>
            </div>
        </div>

        <label class="confirmation-check" for="confirmDetails">
            <input type="checkbox" id="confirmDetails">
            <span id="confirmationText">
                I have checked the member, amount, payment method, and other
                details, and confirm that they are correct.
            </span>
        </label>

        <div class="modal-actions">
            <button class="button secondary" type="button" id="cancelReview">
                Go Back
            </button>
            <button class="button success-button" type="button" id="confirmPayment" disabled>
                Confirm and Save
            </button>
        </div>
    </div>
</div>

<script>
    const paymentForm = document.getElementById('paymentForm');

    if (paymentForm) {
        const membershipSelect = document.getElementById('membership_id');
        const amountInput = document.getElementById('amount');
        const statusSelect = document.getElementById('status');
        const modal = document.getElementById('confirmationModal');
        const confirmationCheckbox = document.getElementById('confirmDetails');
        const confirmButton = document.getElementById('confirmPayment');
        const cancelButton = document.getElementById('cancelReview');
        const reviewButton = document.getElementById('reviewButton');

        let submissionConfirmed = false;

        const peso = value => {
            const number = Number(value);
            return Number.isFinite(number)
                ? '₱' + number.toLocaleString('en-PH', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                })
                : '₱0.00';
        };

        const selectedText = id => {
            const element = document.getElementById(id);
            return element.options[element.selectedIndex]?.text.trim() || '—';
        };

        membershipSelect.addEventListener('change', function () {
            const option = this.options[this.selectedIndex];

            if (option && option.dataset.price) {
                amountInput.value = Number(option.dataset.price).toFixed(2);
            }
        });

        // Show the review dialog instead of saving immediately.
        paymentForm.addEventListener('submit', function (event) {
            if (submissionConfirmed) {
                return;
            }

            event.preventDefault();

            const option = membershipSelect.options[membershipSelect.selectedIndex];
            const status = statusSelect.value;

            document.getElementById('reviewMember').textContent =
                option?.dataset.member || '—';

            document.getElementById('reviewPlan').textContent =
                option?.dataset.plan || '—';

            document.getElementById('reviewEnd').textContent =
                option?.dataset.end || '—';

            document.getElementById('reviewAmount').textContent =
                peso(amountInput.value);

            document.getElementById('reviewDate').textContent =
                document.getElementById('payment_date').value || '—';

            document.getElementById('reviewMethod').textContent =
                selectedText('payment_method');

            document.getElementById('reviewReference').textContent =
                document.getElementById('reference_number').value.trim() || 'Not provided';

            document.getElementById('reviewStatus').textContent =
                status.charAt(0).toUpperCase() + status.slice(1);

            const isPaid = status === 'paid';

            document.getElementById('modalTitle').textContent =
                isPaid ? 'Confirm Receipt of Payment' : 'Confirm Payment Record';

            document.getElementById('modalDescription').textContent =
                isPaid
                    ? 'Verify these details and confirm that the money has actually been received.'
                    : 'Review these details before recording this payment status.';

            document.getElementById('confirmationText').textContent =
                isPaid
                    ? 'I verified the member and amount, checked the payment details, and confirm that the money has actually been received.'
                    : 'I checked the member, amount, payment method, and other details, and confirm that this record is correct.';

            confirmationCheckbox.checked = false;
            confirmButton.disabled = true;
            confirmButton.textContent = isPaid ? 'Confirm Receipt & Save' : 'Confirm and Save';

            modal.classList.add('open');
            modal.setAttribute('aria-hidden', 'false');
            confirmationCheckbox.focus();
        });

        confirmationCheckbox.addEventListener('change', function () {
            confirmButton.disabled = !this.checked;
        });

        cancelButton.addEventListener('click', function () {
            modal.classList.remove('open');
            modal.setAttribute('aria-hidden', 'true');
            confirmationCheckbox.checked = false;
            reviewButton.focus();
        });

        // Only submit after the cashier explicitly confirms.
        confirmButton.addEventListener('click', function () {
            if (!confirmationCheckbox.checked || submissionConfirmed) {
                return;
            }

            submissionConfirmed = true;
            confirmButton.disabled = true;
            cancelButton.disabled = true;
            confirmButton.textContent = 'Saving...';

            paymentForm.requestSubmit(reviewButton);
        });

        // If the user tries to close the dialog using Escape, return to the form.
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && modal.classList.contains('open')) {
                if (!submissionConfirmed) {
                    cancelButton.click();
                }
            }
        });
    }
</script>

</body>
</html>