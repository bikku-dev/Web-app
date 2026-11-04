<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Bill;
use App\Models\Complaint;
use App\Models\Customer;
use App\Models\Organization;
use App\Models\CreditCollection;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class BillingController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Bill::with([
            'complaint.productType', 
            'complaint.customer.area', 
            'organization', 
            'customer.area', 
            'engineer', 
            'handedOverTo', 
            'creditCollections'
        ]);

        if ($user->isEngineer()) {
            $query->where('created_by', $user->id);
        } elseif ($request->filled('engineer_id')) {
            $query->where('created_by', $request->engineer_id);
        }

        if ($request->filled('bill_type')) {
            $query->where('bill_type', $request->bill_type);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->has('is_handed') && $request->is_handed !== '') {
            $query->where('is_handed', $request->boolean('is_handed'));
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('bill_number', 'like', "%{$search}%")
                  ->orWhere('gst_bill_number', 'like', "%{$search}%")
                  ->orWhereHas('complaint', function($cq) use ($search) {
                      $cq->where('ticket_number', 'like', "%{$search}%")
                        ->orWhere('issue_description', 'like', "%{$search}%");
                  })
                  ->orWhereHas('customer', function($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%")
                        ->orWhere('contact_no', 'like', "%{$search}%")
                        ->orWhereHas('area', function($aq) use ($search) {
                            $aq->where('name', 'like', "%{$search}%");
                        });
                  })
                  ->orWhereHas('organization', function($oq) use ($search) {
                      $oq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $bills = $query->latest()->paginate(12)->withQueryString();

        // Calculate summary metrics with a single efficient grouped query, scoped to engineer if applicable
        $summaryQuery = Bill::query();
        if ($user->isEngineer()) {
            $summaryQuery->where('created_by', $user->id);
        } elseif ($request->filled('engineer_id')) {
            $summaryQuery->where('created_by', $request->engineer_id);
        }

        if ($request->filled('from_date')) {
            $summaryQuery->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $summaryQuery->whereDate('created_at', '<=', $request->to_date);
        }

        $totals = $summaryQuery->selectRaw("
            COALESCE(SUM(amount), 0) as total_billed,
            COALESCE(SUM(CASE WHEN bill_type = 'cash' THEN amount ELSE 0 END), 0) as cash_collected,
            COALESCE(SUM(CASE WHEN bill_type = 'credit' THEN amount ELSE 0 END), 0) as credit_pending,
            COALESCE(SUM(CASE WHEN bill_type = 'bill' THEN amount ELSE 0 END), 0) as gst_total,
            COALESCE(SUM(CASE WHEN bill_type = 'cash' AND is_handed = 0 THEN amount ELSE 0 END), 0) as cash_pending_handover
        ")->first();

        $totalBilled = (float)$totals->total_billed;
        $cashCollected = (float)$totals->cash_collected;
        $creditPending = (float)$totals->credit_pending;
        $gstTotal = (float)$totals->gst_total;
        $cashPendingHandover = (float)$totals->cash_pending_handover;
        $engineers = User::where('role', 'engineer')->orderBy('name')->get();

        return view('billing.index', compact('bills', 'totalBilled', 'cashCollected', 'creditPending', 'gstTotal', 'cashPendingHandover', 'engineers'));
    }

    public function exportCsv(Request $request)
    {
        $user = Auth::user();
        if ($user->isEngineer()) {
            abort(403, 'Unauthorized. Billing spreadsheet export is restricted to administrators.');
        }

        $query = Bill::with([
            'complaint.productType', 
            'complaint.customer.area', 
            'customer.area', 
            'customer.organization', 
            'organization', 
            'engineer'
        ]);

        if ($request->filled('engineer_id')) {
            $query->where('created_by', $request->engineer_id);
        }

        if ($request->filled('bill_type')) {
            $query->where('bill_type', $request->bill_type);
        }

        if ($request->has('is_handed') && $request->is_handed !== '') {
            $query->where('is_handed', $request->boolean('is_handed'));
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('bill_number', 'like', "%{$search}%")
                  ->orWhere('gst_bill_number', 'like', "%{$search}%")
                  ->orWhereHas('complaint', function($cq) use ($search) {
                      $cq->where('ticket_number', 'like', "%{$search}%")
                        ->orWhere('issue_description', 'like', "%{$search}%");
                  })
                  ->orWhereHas('customer', function($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%")
                        ->orWhere('contact_no', 'like', "%{$search}%")
                        ->orWhereHas('area', function($aq) use ($search) {
                            $aq->where('name', 'like', "%{$search}%");
                        });
                  })
                  ->orWhereHas('organization', function($oq) use ($search) {
                      $oq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $bills = $query->latest()->get();

        $filename = "Bhumi_Billing_Ledger_" . date('Y-m-d_His') . ".csv";
        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=\"$filename\"",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        // Simplified Billing Ledger Export (ONLY 6 Required Columns):
        // 1. COMPLAINT ID, 2. CUSTOMER NAME, 3. AREA, 4. MOBILE NUMBER, 5. PROBLEM, 6. DATE (DD-MM-YYYY)
        $columns = [
            'COMPLAINT ID',
            'CUSTOMER NAME',
            'AREA',
            'MOBILE NUMBER',
            'PROBLEM',
            'DATE'
        ];

        $callback = function() use ($bills, $columns) {
            $file = fopen('php://output', 'w');
            // Add UTF-8 BOM for Microsoft Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, $columns);

            foreach ($bills as $bill) {
                $complaint = $bill->complaint;
                $customer = $bill->customer ?: $complaint?->customer;

                // 1. COMPLAINT ID (Must show original complaint ID, not bill number)
                $complaintId = $complaint?->ticket_number ?? 'N/A';

                // 2. CUSTOMER NAME
                $customerName = $customer?->name ?? ($bill->organization?->name ?? 'Walk-in / Direct');
                if ($bill->organization && $customer && $customer->name !== $bill->organization->name) {
                    $customerName .= ' (' . $bill->organization->name . ')';
                } elseif ($customer?->organization && $customer->name !== $customer->organization->name) {
                    $customerName .= ' (' . $customer->organization->name . ')';
                }

                // 3. AREA
                $area = $customer?->area?->name ?? ($complaint?->customer?->area?->name ?? 'N/A');

                // 4. MOBILE NUMBER
                $mobileNumber = $customer?->contact_no ?? ($complaint?->customer?->contact_no ?? 'N/A');

                // 5. PROBLEM
                $problem = $complaint?->issue_description ?? ($bill->remarks ?? 'N/A');

                // 6. DATE (Properly formatted as DD-MM-YYYY)
                $date = $bill->created_at ? $bill->created_at->format('d-m-Y') : ($complaint?->created_at ? $complaint->created_at->format('d-m-Y') : '');

                fputcsv($file, [
                    $complaintId,
                    $customerName,
                    $area,
                    $mobileNumber,
                    $problem,
                    $date
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $complaintId = $request->input('complaint_id');
        $complaint = null;

        if ($complaintId) {
            $complaint = Complaint::with(['customer', 'organization', 'productType'])->findOrFail($complaintId);
            if ($user->isEngineer() && $complaint->engineer_id !== $user->id) {
                abort(403, 'Unauthorized. You can only generate bills for your assigned service calls.');
            }
        }

        // For engineers, fetch their assigned complaints that don't have a bill yet
        $myComplaints = collect();
        if ($user->isEngineer()) {
            $myComplaints = Complaint::where('engineer_id', $user->id)
                ->whereNotIn('status', ['cancelled'])
                ->whereNull('bill_id')
                ->with(['customer', 'organization', 'productType'])
                ->latest()
                ->get();
        }

        $customers = Customer::where('is_active', true)->with('organization')->get();
        $firms = Organization::where('is_active', true)->get();

        return view('billing.create', compact('complaint', 'customers', 'firms', 'myComplaints'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // If user is engineer, complaint_id is mandatory and MUST be assigned to this engineer
        if ($user->isEngineer()) {
            $request->validate([
                'complaint_id' => 'required|exists:complaints,id',
            ], [
                'complaint_id.required' => 'Please select the assigned service ticket you are billing for.',
            ]);

            $assignedComplaint = Complaint::where('id', $request->complaint_id)
                ->where('engineer_id', $user->id)
                ->first();

            if (!$assignedComplaint) {
                return back()->with('error', 'Unauthorized: You can only generate bills for your assigned service tickets.')->withInput();
            }
        }

        $validated = $request->validate([
            'complaint_id' => 'nullable|exists:complaints,id',
            'customer_id' => 'required|exists:customers,id',
            'firm_id' => 'nullable|exists:organizations,id',
            'amount' => 'required|numeric|min:0',
            'bill_type' => 'required|in:cash,credit,bill',
            'bill_number' => 'nullable|string|max:50',
            'remarks' => 'nullable|string',
            'gst_bill_number' => 'nullable|string|max:50',
            'photo' => 'nullable|image|max:8192',
            'asset_photo' => 'nullable|image|max:8192',
        ]);

        $billNumber = $validated['bill_number'] ?? null;
        if (empty($billNumber)) {
            $count = Bill::count() + 1;
            $billNumber = 'BILL-' . date('Y') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('bills/receipts', 'public');
        }

        $assetPhotoPath = null;
        if ($request->hasFile('asset_photo')) {
            $assetPhotoPath = $request->file('asset_photo')->store('bills/assets', 'public');
        }

        $paymentStatus = match ($validated['bill_type']) {
            'cash' => 'collected',
            'credit' => 'pending',
            'bill' => 'collected',
        };

        // If admin creates cash bill, it is automatically marked as handed over
        $isHanded = ($user->isAdmin() && $validated['bill_type'] === 'cash');

        DB::transaction(function() use ($validated, $billNumber, $photoPath, $assetPhotoPath, $paymentStatus, $isHanded, &$bill) {
            $bill = Bill::create([
                'bill_number' => $billNumber,
                'complaint_id' => $validated['complaint_id'] ?? null,
                'customer_id' => $validated['customer_id'],
                'firm_id' => $validated['firm_id'] ?? null,
                'created_by' => Auth::id(),
                'amount' => $validated['amount'],
                'bill_type' => $validated['bill_type'],
                'payment_status' => $paymentStatus,
                'is_handed' => $isHanded,
                'handed_over_at' => $isHanded ? now() : null,
                'handed_over_to' => $isHanded ? Auth::id() : null,
                'gst_bill_number' => $validated['gst_bill_number'] ?? null,
                'photo' => $photoPath,
                'asset_photo' => $assetPhotoPath,
                'remarks' => $validated['remarks'] ?? null,
            ]);

            // If attached to complaint, mark complaint resolved and link bill
            if (!empty($validated['complaint_id'])) {
                $complaint = Complaint::find($validated['complaint_id']);
                if ($complaint) {
                    $complaint->update([
                        'bill_id' => $bill->id,
                        'is_resolved' => true,
                        'resolved_at' => now(),
                        'status' => 'completed',
                    ]);
                }
            }
        });

        AuditLog::log('CREATE_BILL', 'Billing & Finance', (string)$bill->id, "Generated {$bill->bill_type} bill {$bill->bill_number} for ₹{$bill->amount}");

        return redirect()->route('billing.show', $bill)->with('success', "Bill {$bill->bill_number} generated successfully and service marked as completed!");
    }

    public function show(Bill $bill)
    {
        if (Auth::user()->isEngineer() && $bill->created_by !== Auth::id()) {
            abort(403, 'Unauthorized. You can only view bills generated by you.');
        }

        $bill->load(['complaint', 'organization', 'customer', 'engineer', 'handedOverTo', 'creditCollections.collectedBy']);
        return view('billing.show', compact('bill'));
    }

    public function verifyHandover(Request $request, Bill $bill)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'handover_remarks' => 'nullable|string',
            'gst_bill_number' => 'nullable|string',
        ]);

        $bill->update([
            'is_handed' => true,
            'handed_over_at' => now(),
            'handed_over_to' => Auth::id(),
            'payment_status' => 'handed_over',
            'handover_remarks' => $request->handover_remarks,
            'gst_bill_number' => $request->gst_bill_number ?? $bill->gst_bill_number,
        ]);

        AuditLog::log('VERIFY_CASH_HANDOVER', 'Billing & Finance', (string)$bill->id, "Admin verified cash handover of ₹{$bill->amount} for bill {$bill->bill_number}");

        return back()->with('success', "Cash handover verified and recorded.");
    }

    public function recordCreditCollection(Request $request, Bill $bill)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1|max:' . $bill->amount,
            'notes' => 'nullable|string',
            'receipt_photo' => 'nullable|image|max:5120',
        ]);

        $receiptPath = null;
        if ($request->hasFile('receipt_photo')) {
            $receiptPath = $request->file('receipt_photo')->store('bills/credit_receipts', 'public');
        }

        CreditCollection::create([
            'bill_id' => $bill->id,
            'amount' => $validated['amount'],
            'collected_by' => Auth::id(),
            'collected_at' => now(),
            'notes' => $validated['notes'] ?? null,
            'receipt_photo' => $receiptPath,
        ]);

        // If collected fully, update bill type to cash or collected
        $totalCollected = $bill->creditCollections()->sum('amount') + $validated['amount'];
        if ($totalCollected >= $bill->amount) {
            $bill->update(['payment_status' => 'collected']);
        }

        AuditLog::log('COLLECT_CREDIT', 'Billing & Finance', (string)$bill->id, "Recorded collection of ₹{$validated['amount']} for credit bill {$bill->bill_number}");

        return back()->with('success', "Payment collection of ₹{$validated['amount']} recorded.");
    }

    public function downloadPdf(Bill $bill)
    {
        $bill->load(['complaint.productType', 'complaint.serviceType', 'organization', 'customer', 'engineer', 'handedOverTo']);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.invoice', compact('bill'));
        return $pdf->download("Invoice-{$bill->bill_number}.pdf");
    }

    public function printView(Bill $bill)
    {
        $bill->load(['complaint.productType', 'complaint.serviceType', 'organization', 'customer', 'engineer', 'handedOverTo']);
        return view('pdf.invoice', compact('bill'));
    }
}
