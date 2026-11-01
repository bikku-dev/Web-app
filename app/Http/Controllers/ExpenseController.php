<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Expense;
use App\Models\Complaint;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Expense::with(['complaint', 'organization', 'customer', 'engineer', 'reviewer']);

        if ($user->isEngineer()) {
            $query->where('created_by', $user->id);
        } elseif ($request->filled('engineer_id')) {
            $query->where('created_by', $request->engineer_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('expense_type')) {
            $query->where('expense_type', $request->expense_type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('details', 'like', "%{$search}%")
                  ->orWhereHas('engineer', function($eq) use ($search) {
                      $eq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $expenses = $query->latest()->paginate(15)->withQueryString();

        // Single grouped query for summary stats, scoped to engineer if applicable
        $expenseStatsQuery = Expense::query();
        if ($user->isEngineer()) {
            $expenseStatsQuery->where('created_by', $user->id);
        }
        $expenseTotals = $expenseStatsQuery->selectRaw("
            COALESCE(SUM(CASE WHEN status = 'approved' THEN amount ELSE 0 END), 0) as approved,
            COALESCE(SUM(CASE WHEN status = 'pending' THEN amount ELSE 0 END), 0) as pending,
            COALESCE(SUM(CASE WHEN expense_type = 'Travel' THEN amount ELSE 0 END), 0) as travel,
            COALESCE(SUM(CASE WHEN expense_type = 'Component' THEN amount ELSE 0 END), 0) as components
        ")->first();

        $totalApproved = (float)$expenseTotals->approved;
        $totalPending = (float)$expenseTotals->pending;
        $totalTravel = (float)$expenseTotals->travel;
        $totalComponents = (float)$expenseTotals->components;

        $activeComplaints = Complaint::where('status', 'in_progress')
            ->when($user->isEngineer(), fn($q) => $q->where('engineer_id', $user->id))
            ->get();

        return view('expenses.index', compact('expenses', 'totalApproved', 'totalPending', 'totalTravel', 'totalComponents', 'activeComplaints'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'complaint_id' => 'nullable|exists:complaints,id',
            'expense_type' => 'required|in:Travel,Component,Other',
            'amount' => 'required|numeric|min:1',
            'details' => 'required|string',
            'photo' => 'nullable|image|max:8192',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('expenses/receipts', 'public');
        }

        $complaint = null;
        if (!empty($validated['complaint_id'])) {
            $complaint = Complaint::find($validated['complaint_id']);
        }

        $expense = Expense::create([
            'complaint_id' => $validated['complaint_id'] ?? null,
            'firm_id' => $complaint?->firm_id,
            'customer_id' => $complaint?->customer_id,
            'created_by' => Auth::id(),
            'expense_type' => $validated['expense_type'],
            'amount' => $validated['amount'],
            'details' => $validated['details'],
            'photo' => $photoPath,
            'status' => 'pending',
        ]);

        AuditLog::log('SUBMIT_EXPENSE', 'Expense Management', (string)$expense->id, "Engineer submitted {$expense->expense_type} claim of ₹{$expense->amount}");

        return back()->with('success', "Expense claim of ₹{$expense->amount} submitted for approval.");
    }

    public function review(Request $request, Expense $expense)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:approved,declined',
            'admin_remarks' => 'required_if:status,declined|nullable|string',
        ]);

        $expense->update([
            'status' => $validated['status'],
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'admin_remarks' => $validated['admin_remarks'] ?? null,
        ]);

        AuditLog::log('REVIEW_EXPENSE', 'Expense Management', (string)$expense->id, "Admin {$validated['status']} expense ID {$expense->id} of ₹{$expense->amount}");

        return back()->with('success', "Expense claim marked as " . ucfirst($validated['status']) . ".");
    }
}
