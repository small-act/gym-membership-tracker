<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Member;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $date = $request->query('date', today()->toDateString());

        // Assign unique QR tokens to existing members that do not have one.
        Member::whereNull('qr_token')
            ->orderBy('id')
            ->chunkById(100, function ($members) {
                foreach ($members as $member) {
                    do {
                        $token = Str::random(64);
                    } while (Member::where('qr_token', $token)->exists());

                    $member->qr_token = $token;
                    $member->save();
                }
            });

        $query = Attendance::with('member')
            ->whereDate('attendance_date', $date)
            ->orderByDesc('check_in_at')
            ->orderByDesc('id');

        if ($search !== '') {
            $query->whereHas('member', function ($memberQuery) use ($search) {
                $memberQuery->where('full_name', 'like', "%{$search}%")
                    ->orWhere('member_number', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $attendances = $query->paginate(15)->withQueryString();

        $todayCount = Attendance::whereDate(
            'attendance_date',
            today()
        )->count();

        $checkedInCount = Attendance::whereDate(
            'attendance_date',
            today()
        )->whereNotNull('check_in_at')
            ->whereNull('check_out_at')
            ->count();

        $checkedOutCount = Attendance::whereDate(
            'attendance_date',
            today()
        )->whereNotNull('check_out_at')->count();

        $members = Member::where('status', 'active')
            ->orderBy('full_name')
            ->get();

        return view('attendance.index', compact(
            'attendances',
            'members',
            'search',
            'date',
            'todayCount',
            'checkedInCount',
            'checkedOutCount'
        ));
    }

    public function checkIn(Request $request)
    {
        $validated = $request->validate([
            'member_id' => [
                'required',
                Rule::exists('members', 'id')->where(
                    fn ($query) => $query->where('status', 'active')
                ),
            ],
            'check_in_method' => [
                'required',
                Rule::in(['manual', 'qr']),
            ],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $member = Member::findOrFail($validated['member_id']);

        return $this->recordCheckIn(
            $member,
            $validated['check_in_method'],
            $validated['notes'] ?? null
        );
    }

    public function qrCheckIn(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string', 'size:64'],
            'mode' => ['required', 'in:check-in,check-out'],
        ]);

        $member = Member::where('qr_token', $validated['token'])->first();

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'QR code not recognized. Check the member QR code.',
            ], 404);
        }

        if ($member->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'This member account is inactive.',
            ], 422);
        }

        $attendance = Attendance::where('member_id', $member->id)
            ->whereDate('attendance_date', today())
            ->first();

        if ($validated['mode'] === 'check-in') {
            $eligible = $member->memberships()
                ->where('status', 'active')
                ->whereDate('start_date', '<=', today())
                ->whereDate('end_date', '>=', today())
                ->exists();

            if (!$eligible) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active membership valid for today.',
                ], 422);
            }

            if ($attendance) {
                return response()->json([
                    'success' => false,
                    'message' => $attendance->check_out_at
                        ? 'This member has already checked in and checked out today.'
                        : 'This member is already checked in. Select QR Check-out to record checkout.',
                    'member' => $member->full_name,
                    'member_number' => $member->member_number,
                ], 409);
            }

            try {
                $attendance = DB::transaction(function () use ($member) {
                    return Attendance::create([
                        'member_id' => $member->id,
                        'attendance_date' => today()->toDateString(),
                        'check_in_at' => now(),
                        'check_in_method' => 'qr',
                        'notes' => null,
                    ]);
                });
            } catch (QueryException $exception) {
                report($exception);

                return response()->json([
                    'success' => false,
                    'message' => 'Check-in could not be recorded. Please scan again.',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'action' => 'check-in',
                'message' => 'CHECK-IN successful!',
                'member' => $member->full_name,
                'member_number' => $member->member_number,
                'time' => $attendance->check_in_at->format('h:i A'),
            ]);
        }

        if (!$attendance || !$attendance->check_in_at) {
            return response()->json([
                'success' => false,
                'message' => 'No check-in is recorded today. Select QR Check-in first.',
                'member' => $member->full_name,
                'member_number' => $member->member_number,
            ], 409);
        }

        if ($attendance->check_out_at) {
            return response()->json([
                'success' => false,
                'message' => 'This member has already checked out today.',
                'member' => $member->full_name,
                'member_number' => $member->member_number,
            ], 409);
        }

        try {
            DB::transaction(function () use ($attendance) {
                $attendance->update([
                    'check_out_at' => now(),
                ]);
            });
        } catch (QueryException $exception) {
            report($exception);

            return response()->json([
                'success' => false,
                'message' => 'Check-out could not be recorded. Please scan again.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'action' => 'check-out',
            'message' => 'CHECK-OUT successful!',
            'member' => $member->full_name,
            'member_number' => $member->member_number,
            'time' => $attendance->fresh()->check_out_at->format('h:i A'),
        ]);
    }
    private function recordCheckIn(
        Member $member,
        string $method,
        ?string $notes = null
    ) {
        if ($method === 'qr') {
            $eligible = $member->memberships()
                ->where('status', 'active')
                ->whereDate('start_date', '<=', today())
                ->whereDate('end_date', '>=', today())
                ->exists();

            if (!$eligible) {
                return back()->with(
                    'error',
                    'This member has no active membership valid for today.'
                );
            }
        }

        $existing = Attendance::where('member_id', $member->id)
            ->whereDate('attendance_date', today())
            ->first();

        if ($existing) {
            return back()->with(
                'error',
                'This member already has an attendance record today.'
            );
        }

        try {
            Attendance::create([
                'member_id' => $member->id,
                'attendance_date' => today()->toDateString(),
                'check_in_at' => now(),
                'check_in_method' => $method,
                'notes' => $notes,
            ]);
        } catch (QueryException $exception) {
            report($exception);

            return back()->with(
                'error',
                'Attendance could not be recorded. Please refresh and try again.'
            );
        }

        return redirect()->route('attendance.index')
            ->with('success', 'Member checked in successfully.');
    }

    public function checkOut(Attendance $attendance)
    {
        if ($attendance->check_out_at !== null) {
            return back()->with(
                'error',
                'This attendance record is already checked out.'
            );
        }

        if (!$attendance->check_in_at) {
            return back()->with(
                'error',
                'This member has no check-in time recorded.'
            );
        }

        $attendance->update([
            'check_out_at' => now(),
        ]);

        return redirect()->route('attendance.index')
            ->with('success', 'Member checked out successfully.');
    }
}
