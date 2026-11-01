<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\User;
use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Attendance::with(['user', 'approver']);

        if ($user->isEngineer()) {
            $query->where('user_id', $user->id);
        } elseif ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('recorded_at', $request->date);
        }

        if ($request->has('is_approved') && $request->is_approved !== '') {
            $query->where('is_approved', $request->boolean('is_approved'));
        }

        $attendances = $query->latest('recorded_at')->paginate(15)->withQueryString();
        $engineers = User::where('role', 'engineer')->where('is_active', true)->get();

        // Check current status today for logged-in user
        $todayLatest = Attendance::where('user_id', $user->id)
            ->whereDate('recorded_at', Carbon::today())
            ->latest('recorded_at')
            ->first();

        $isClockedIn = $todayLatest && $todayLatest->is_clock_in;

        return view('attendance.index', compact('attendances', 'engineers', 'todayLatest', 'isClockedIn'));
    }

    private function saveSelfiePhoto(Request $request): string
    {
        if ($request->filled('photo_base64')) {
            $base64Image = $request->input('photo_base64');
            $ext = 'jpg';
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
                $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
                $detected = strtolower($type[1]);
                if (in_array($detected, ['jpg', 'jpeg', 'png', 'webp'])) {
                    $ext = $detected;
                }
            }

            $imageData = base64_decode($base64Image);
            if ($imageData === false) {
                throw new \Exception('Invalid image data received.');
            }

            $fileName = 'attendance/selfies/selfie_' . Auth::id() . '_' . time() . '_' . uniqid() . '.' . $ext;
            Storage::disk('public')->put($fileName, $imageData);
            return $fileName;
        }

        if ($request->hasFile('photo')) {
            return $request->file('photo')->store('attendance/selfies', 'public');
        }

        throw new \Exception('Live selfie photo is required to mark attendance.');
    }

    public function clockIn(Request $request)
    {
        $user = Auth::user();

        // Check if already clocked in today
        $lastAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('recorded_at', Carbon::today())
            ->latest('recorded_at')
            ->first();

        if ($lastAttendance && $lastAttendance->is_clock_in) {
            return back()->with('error', 'You are already clocked in for today!');
        }

        $request->validate([
            'photo' => 'required_without:photo_base64|nullable|image|max:8192',
            'photo_base64' => 'required_without:photo|nullable|string',
        ]);

        try {
            $photoPath = $this->saveSelfiePhoto($request);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'photo' => $photoPath,
            'is_clock_in' => true,
            'is_approved' => false,
            'recorded_at' => now(),
        ]);

        AuditLog::log('ATTENDANCE_CLOCK_IN', 'Attendance', (string)$attendance->id, "Engineer {$user->name} clocked in with live selfie");

        return back()->with('success', 'Clock-in successfully recorded with live selfie proof.');
    }

    public function clockOut(Request $request)
    {
        $user = Auth::user();

        $lastAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('recorded_at', Carbon::today())
            ->latest('recorded_at')
            ->first();

        if (!$lastAttendance || !$lastAttendance->is_clock_in) {
            return back()->with('error', 'You must clock in first before clocking out!');
        }

        $request->validate([
            'photo' => 'required_without:photo_base64|nullable|image|max:8192',
            'photo_base64' => 'required_without:photo|nullable|string',
        ]);

        try {
            $photoPath = $this->saveSelfiePhoto($request);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'photo' => $photoPath,
            'is_clock_in' => false,
            'is_approved' => false,
            'recorded_at' => now(),
        ]);

        AuditLog::log('ATTENDANCE_CLOCK_OUT', 'Attendance', (string)$attendance->id, "Engineer {$user->name} clocked out with live selfie");

        return back()->with('success', 'Clock-out recorded. Thank you for your service today!');
    }

    public function review(Request $request, Attendance $attendance)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'is_approved' => 'required|boolean',
            'admin_remarks' => 'nullable|string',
        ]);

        $attendance->update([
            'is_approved' => $validated['is_approved'],
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'admin_remarks' => $validated['admin_remarks'] ?? null,
        ]);

        AuditLog::log('REVIEW_ATTENDANCE', 'Attendance', (string)$attendance->id, "Admin set approval to {$validated['is_approved']} for attendance record");

        return back()->with('success', 'Attendance record review updated.');
    }

    public function leaderboard(Request $request)
    {
        $days = (int)$request->input('days', 30);
        $sinceDate = Carbon::now()->subDays($days);

        $engineers = User::where('role', 'engineer')->where('is_active', true)->get();

        $leaderboard = [];

        foreach ($engineers as $engineer) {
            // Find all attendance records in window
            $records = Attendance::where('user_id', $engineer->id)
                ->where('recorded_at', '>=', $sinceDate)
                ->orderBy('recorded_at', 'asc')
                ->get();

            $totalSeconds = 0;
            $clockInTime = null;

            foreach ($records as $record) {
                if ($record->is_clock_in) {
                    $clockInTime = $record->recorded_at;
                } elseif ($clockInTime && !$record->is_clock_in) {
                    // Clock out found
                    $diffInSeconds = $record->recorded_at->diffInSeconds($clockInTime);
                    // Cap daily session at 8 hours (28800 seconds)
                    $sessionSeconds = min($diffInSeconds, 8 * 3600);
                    $totalSeconds += $sessionSeconds;
                    $clockInTime = null;
                }
            }

            $hours = round($totalSeconds / 3600, 1);
            $totalDaysWorked = $records->where('is_clock_in', true)->groupBy(function($r) {
                return $r->recorded_at->toDateString();
            })->count();

            $leaderboard[] = [
                'engineer' => $engineer,
                'total_hours' => $hours,
                'total_seconds' => $totalSeconds,
                'days_worked' => $totalDaysWorked,
            ];
        }

        // Sort by total hours descending
        usort($leaderboard, fn($a, $b) => $b['total_seconds'] <=> $a['total_seconds']);

        return view('attendance.leaderboard', compact('leaderboard', 'days'));
    }
}
