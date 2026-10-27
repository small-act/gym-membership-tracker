<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    public function index()
    {
        $user = request()->user();

        $paymentsQuery = Payment::with([
            'membership.member',
            'membership.plan',
            'processedBy',
        ]);

        // Cashiers can view only their own transactions.
        if ($user->role === 'cashier') {
            $paymentsQuery->where('processed_by', $user->id);
        }

        $payments = $paymentsQuery
            ->orderByDesc('payment_date')
            ->orderByDesc('id')
            ->paginate(15);

        $totalsQuery = Payment::query();

        if ($user->role === 'cashier') {
            $totalsQuery->where('processed_by', $user->id);
        }

        $totalCollected = (clone $totalsQuery)
            ->where('status', 'paid')
            ->sum('amount');

        $todayCollected = (clone $totalsQuery)
            ->where('status', 'paid')
            ->whereDate('payment_date', today())
            ->sum('amount');

        $pendingAmount = (clone $totalsQuery)
            ->where('status', 'pending')
            ->sum('amount');

        $pendingPaymentsQuery = Payment::with([
            'membership.member',
            'membership.plan',
            'processedBy',
        ])->where('status', 'pending');

        if ($user->role === 'cashier') {
            $pendingPaymentsQuery->where('processed_by', $user->id);
        }

        $pendingPayments = $pendingPaymentsQuery
            ->orderByDesc('payment_date')
            ->orderByDesc('id')
            ->get();

        return view('payments.index', compact(
            'payments',
            'pendingPayments',
            'totalCollected',
            'todayCollected',
            'pendingAmount'
        ));
    }

    public function create()
    {
        $memberships = Membership::with(['member', 'plan'])
            ->orderByDesc('id')
            ->get();

        return view('payments.create', compact('memberships'));
    }

    public function store(Request $request)
    {
        // GCash must use the separate PayMongo checkout flow.
        // Never allow the ordinary form to declare a GCash payment paid.
        if ($request->input('payment_method') === 'gcash') {
            throw ValidationException::withMessages([
                'payment_method' =>
                    'GCash payments must use the PayMongo checkout process. '
                    . 'Do not record GCash as paid through this form.',
            ]);
        }

        $validated = $request->validate([
            'membership_id' => [
                'required',
                Rule::exists('memberships', 'id'),
            ],
            'amount' => [
                'nullable',
                'numeric',
                'gt:0',
                'max:99999999.99',
            ],
            'payment_date' => [
                'required',
                'date',
                'before_or_equal:today',
            ],
            'payment_method' => [
                'required',
                Rule::in([
                    'cash',
                    'bank_transfer',
                    'card',
                    'other',
                ]),
            ],
            'reference_number' => [
                'nullable',
                'string',
                'max:100',
            ],
            'status' => [
                'required',
                Rule::in(['paid', 'pending', 'failed']),
            ],
            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $payment = DB::transaction(function () use ($validated, $request) {
            $membership = Membership::with('plan')
                ->whereKey($validated['membership_id'])
                ->lockForUpdate()
                ->firstOrFail();

            // Use the saved membership price, not a browser-submitted amount.
            $membershipPrice = $membership->price;

            if (! is_numeric($membershipPrice) || (float) $membershipPrice <= 0) {
                throw ValidationException::withMessages([
                    'membership_id' =>
                        'This membership has no valid saved price. '
                        . 'Please check its membership record before accepting payment.',
                ]);
            }

            $amount = number_format((float) $membershipPrice, 2, '.', '');

            if ($validated['status'] === 'paid') {
                $alreadyPaid = Payment::where(
                    'membership_id',
                    $membership->id
                )
                    ->where('status', 'paid')
                    ->exists();

                if ($alreadyPaid) {
                    throw ValidationException::withMessages([
                        'membership_id' =>
                            'This membership already has a recorded paid transaction. '
                            . 'No duplicate payment was saved.',
                    ]);
                }
            }

            // Always derive the cashier identity from the authenticated session.
            return Payment::create([
                'membership_id' => $membership->id,
                'processed_by' => $request->user()->id,
                'amount' => $amount,
                'payment_date' => $validated['payment_date'],
                'payment_method' => $validated['payment_method'],
                'reference_number' => $validated['reference_number'] ?? null,
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
            ]);
        });

        $message = $payment->status === 'paid'
            ? 'Payment confirmed and saved successfully. It is now in payment history.'
            : 'Payment record saved. Its status is ' . $payment->status . '.';

        return redirect()
            ->route('payments.index')
            ->with('success', $message);
    }
}
