<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Complaint;
use App\Models\Bill;
use App\Models\Expense;
use App\Models\Attendance;
use App\Models\User;
use App\Models\Notice;
use App\Models\ProductType;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $days = (int)$request->input('days', 30);
        $sinceDate = Carbon::now()->subDays($days);

        if ($user->isAdmin()) {
            return $this->adminDashboard($sinceDate, $days);
        }

        return $this->engineerDashboard($user, $sinceDate, $days);
    }

    public function menu()
    {
        return view('menu.index');
    }

    private function adminDashboard(Carbon $sinceDate, int $days)
    {
        // 1. Complaint Metrics (Single combined aggregate query, filtered by duration if specified)
        $cmpQuery = Complaint::query();
        $billQuery = Bill::query();
        $prodQuery = DB::table('complaints')
            ->join('product_types', 'complaints.product_type_id', '=', 'product_types.id');

        if ($days > 0 && $days < 3650) {
            $cmpQuery->where('complaints.created_at', '>=', $sinceDate);
            $billQuery->where('bills.created_at', '>=', $sinceDate);
            $prodQuery->where('complaints.created_at', '>=', $sinceDate);
        }

        $complaintStats = $cmpQuery->selectRaw("
            COUNT(*) as total,
            COALESCE(SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END), 0) as pending,
            COALESCE(SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END), 0) as in_progress,
            COALESCE(SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END), 0) as completed,
            COALESCE(SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END), 0) as cancelled,
            COALESCE(SUM(CASE WHEN status NOT IN ('completed', 'cancelled') AND due_date < NOW() THEN 1 ELSE 0 END), 0) as overdue
        ")->first();

        $totalComplaints = (int)($complaintStats->total ?? 0);
        $pendingComplaints = (int)($complaintStats->pending ?? 0);
        $inProgressComplaints = (int)($complaintStats->in_progress ?? 0);
        $completedComplaints = (int)($complaintStats->completed ?? 0);
        $cancelledComplaints = (int)($complaintStats->cancelled ?? 0);
        $overdueComplaints = (int)($complaintStats->overdue ?? 0);

        // 2. Financial Metrics (Single combined aggregate query)
        $billStats = $billQuery->selectRaw("
            COALESCE(SUM(amount), 0) as total,
            COALESCE(SUM(CASE WHEN bill_type = 'cash' THEN amount ELSE 0 END), 0) as cash,
            COALESCE(SUM(CASE WHEN bill_type = 'credit' THEN amount ELSE 0 END), 0) as credit,
            COALESCE(SUM(CASE WHEN bill_type = 'bill' THEN amount ELSE 0 END), 0) as gst,
            COALESCE(SUM(CASE WHEN bill_type = 'cash' AND is_handed = 0 THEN amount ELSE 0 END), 0) as pending_handover
        ")->first();

        $totalBilled = (float)($billStats->total ?? 0);
        $cashCollected = (float)($billStats->cash ?? 0);
        $creditOutstanding = (float)($billStats->credit ?? 0);
        $gstBilled = (float)($billStats->gst ?? 0);
        $pendingHandover = (float)($billStats->pending_handover ?? 0);

        // 3. Operational Counts
        $totalEngineers = User::where('role', 'engineer')->where('is_active', true)->count();
        $todayAttendanceCount = Attendance::whereDate('recorded_at', Carbon::today())
            ->where('is_clock_in', true)
            ->distinct('user_id')
            ->count('user_id');
        $pendingExpenses = Expense::where('status', 'pending')->count();

        // 4. Product Type distribution (Issues for each product)
        $productWise = $prodQuery
            ->select('product_types.name', DB::raw('count(complaints.id) as total'))
            ->groupBy('product_types.name')
            ->orderBy('total', 'desc')
            ->take(6)
            ->get();

        // 5. Recent Complaints (urgent or overdue priority)
        $recentComplaints = Complaint::with(['customer', 'organization', 'engineer', 'productType'])
            ->orderByRaw("CASE WHEN status = 'pending' AND due_date < NOW() THEN 1 WHEN priority = 'urgent' THEN 2 ELSE 3 END")
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        // 6. Active Notices
        $activeNotices = Notice::where('is_active', true)
            ->where(function($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now()->toDateString());
            })
            ->latest()
            ->take(3)
            ->get();

        return view('dashboard.admin', compact(
            'totalComplaints',
            'pendingComplaints',
            'inProgressComplaints',
            'completedComplaints',
            'cancelledComplaints',
            'overdueComplaints',
            'totalBilled',
            'cashCollected',
            'creditOutstanding',
            'gstBilled',
            'pendingHandover',
            'totalEngineers',
            'todayAttendanceCount',
            'pendingExpenses',
            'productWise',
            'recentComplaints',
            'activeNotices',
            'days'
        ));
    }

    private function engineerDashboard(User $engineer, Carbon $sinceDate, int $days)
    {
        // Combined aggregate stats for engineer
        $myStats = Complaint::where('engineer_id', $engineer->id)
            ->selectRaw("
                COUNT(*) as total,
                COALESCE(SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END), 0) as pending,
                COALESCE(SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END), 0) as in_progress,
                COALESCE(SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END), 0) as completed,
                COALESCE(SUM(CASE WHEN status NOT IN ('completed', 'cancelled') AND due_date < NOW() THEN 1 ELSE 0 END), 0) as overdue
            ")->first();

        $myTotal = (int)$myStats->total;
        $myPending = (int)$myStats->pending;
        $myInProgress = (int)$myStats->in_progress;
        $myCompleted = (int)$myStats->completed;
        $myOverdue = (int)$myStats->overdue;

        // My Attendance Status today
        $todayAttendance = Attendance::where('user_id', $engineer->id)
            ->whereDate('recorded_at', Carbon::today())
            ->latest('recorded_at')
            ->first();

        $isClockedIn = $todayAttendance && $todayAttendance->is_clock_in;

        // My Financial Metrics for Payment Graph
        $myBillStats = Bill::where('created_by', $engineer->id)
            ->selectRaw("
                COALESCE(SUM(amount), 0) as total,
                COALESCE(SUM(CASE WHEN bill_type = 'cash' THEN amount ELSE 0 END), 0) as cash,
                COALESCE(SUM(CASE WHEN bill_type = 'credit' THEN amount ELSE 0 END), 0) as credit,
                COALESCE(SUM(CASE WHEN bill_type = 'bill' THEN amount ELSE 0 END), 0) as gst
            ")->first();

        $myTotalBilled = (float)($myBillStats->total ?? 0);
        $myCashCollected = (float)($myBillStats->cash ?? 0);
        $myCreditPending = (float)($myBillStats->credit ?? 0);
        $myGstBilled = (float)($myBillStats->gst ?? 0);

        // My Pending Handover Cash
        $myCashToHandover = Bill::where('created_by', $engineer->id)
            ->where('bill_type', 'cash')
            ->where('is_handed', false)
            ->sum('amount');

        // My assigned tickets
        $assignedTickets = Complaint::where('engineer_id', $engineer->id)
            ->with(['customer', 'organization', 'productType', 'serviceType'])
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->orderByRaw("CASE WHEN due_date < NOW() THEN 1 WHEN priority = 'urgent' THEN 2 ELSE 3 END")
            ->orderBy('due_date', 'asc')
            ->get();

        // Notices
        $notices = Notice::where('is_active', true)
            ->where(function($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now()->toDateString());
            })
            ->latest()
            ->take(4)
            ->get();

        return view('dashboard.engineer', compact(
            'engineer',
            'myTotal',
            'myPending',
            'myInProgress',
            'myCompleted',
            'myOverdue',
            'myTotalBilled',
            'myCashCollected',
            'myCreditPending',
            'myGstBilled',
            'todayAttendance',
            'isClockedIn',
            'myCashToHandover',
            'assignedTickets',
            'notices',
            'days'
        ));
    }
}
