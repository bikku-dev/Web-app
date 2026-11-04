<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Complaint;
use App\Models\Organization;
use App\Models\Customer;
use App\Models\ProductType;
use App\Models\ServiceType;
use App\Models\Area;
use App\Models\City;
use App\Models\User;
use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ComplaintController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Complaint::with(['customer', 'organization', 'productType', 'serviceType', 'engineer', 'bill']);

        // Engineer only sees their assigned jobs
        if ($user->isEngineer()) {
            $query->where('engineer_id', $user->id);
        } elseif ($request->filled('engineer_id')) {
            $query->where('engineer_id', $request->engineer_id);
        }

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('product_type_id')) {
            $query->where('product_type_id', $request->product_type_id);
        }

        if ($request->filled('firm_id')) {
            $query->where('firm_id', $request->firm_id);
        }

        if ($request->boolean('overdue')) {
            $query->whereNotIn('status', ['completed', 'cancelled'])
                  ->where('due_date', '<', now());
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('issue_description', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%")->orWhere('contact_no', 'like', "%{$search}%");
                  })
                  ->orWhereHas('organization', function($oq) use ($search) {
                      $oq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('engineer', function($eq) use ($search) {
                      $eq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $complaints = $query->orderByRaw("CASE WHEN status = 'pending' AND due_date < NOW() THEN 1 WHEN priority = 'urgent' THEN 2 ELSE 3 END")
            ->latest('due_date')
            ->paginate(12)
            ->withQueryString();

        $engineers = User::where('role', 'engineer')->where('is_active', true)->get();
        $productTypes = ProductType::where('is_active', true)->get();
        $firms = Organization::where('is_active', true)->get();

        return view('complaints.index', compact('complaints', 'engineers', 'productTypes', 'firms'));
    }

    public function create()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized. Only administrators can create service tickets.');
        }

        $customers = Customer::where('is_active', true)->with(['organization', 'area', 'city'])->get();
        $firms = Organization::where('is_active', true)->get();
        $productTypes = ProductType::where('is_active', true)->get();
        $serviceTypes = ServiceType::where('is_active', true)->get();
        $engineers = User::where('role', 'engineer')->where('is_active', true)->get();
        $areas = Area::where('is_active', true)->with('cities')->get();

        // Calculate live predicted ticket ID for each product type (e.g. C2611001, P2611001, S2611001, O2611001)
        $predictedIds = [];
        foreach ($productTypes as $pt) {
            $predictedIds[$pt->id] = Complaint::generateTicketNumber($pt->id);
        }

        return view('complaints.create', compact('customers', 'firms', 'productTypes', 'serviceTypes', 'engineers', 'areas', 'predictedIds'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized. Only administrators can create service tickets.');
        }

        $customerMode = $request->input('customer_mode', 'existing');

        if ($customerMode === 'new') {
            $validated = $request->validate([
                'new_customer_name' => 'required|string|max:255',
                'new_customer_phone' => 'required|string|max:25',
                'new_customer_department' => 'nullable|string|max:100',
                'new_customer_address' => 'nullable|string|max:500',
                'area_id' => 'nullable',
                'new_area_name' => 'nullable|string|max:100',
                'firm_id' => 'nullable',
                'new_firm_name' => 'nullable|string|max:255',
                'product_type_id' => 'nullable',
                'new_product_type' => 'nullable|string|max:100',
                'service_type_id' => 'nullable',
                'new_service_type' => 'nullable|string|max:100',
                'engineer_id' => 'required|exists:users,id',
                'due_date' => 'required',
                'priority' => 'required|in:low,medium,high,urgent',
                'issue_description' => 'required|string',
                'photo' => 'nullable|image|max:8192',
            ]);

            // 1. Resolve or create Area
            $areaId = null;
            if ($request->filled('new_area_name')) {
                $area = Area::firstOrCreate(['name' => trim($request->new_area_name)]);
                $areaId = $area->id;
            } elseif ($request->filled('area_id')) {
                $areaId = $request->input('area_id');
            }

            // 2. Resolve or create Firm/Organization
            $firmId = null;
            if ($request->filled('new_firm_name')) {
                $firm = Organization::firstOrCreate(
                    ['name' => trim($request->new_firm_name)],
                    [
                        'area_id' => $areaId,
                        'contact_person' => trim($request->new_customer_name),
                        'contact_no' => trim($request->new_customer_phone),
                        'address' => $request->new_customer_address,
                        'is_active' => true,
                    ]
                );
                $firmId = $firm->id;
            } elseif ($request->filled('firm_id')) {
                $firmId = $request->input('firm_id');
            }

            // 3. Create New Customer
            $customer = Customer::create([
                'name' => trim($request->new_customer_name),
                'contact_no' => trim($request->new_customer_phone),
                'department' => $request->new_customer_department,
                'address' => $request->new_customer_address,
                'area_id' => $areaId,
                'firm_id' => $firmId,
                'is_active' => true,
            ]);
            $customerId = $customer->id;
        } else {
            $validated = $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'firm_id' => 'nullable|exists:organizations,id',
                'product_type_id' => 'nullable',
                'new_product_type' => 'nullable|string|max:100',
                'service_type_id' => 'nullable',
                'new_service_type' => 'nullable|string|max:100',
                'engineer_id' => 'required|exists:users,id',
                'due_date' => 'required',
                'priority' => 'required|in:low,medium,high,urgent',
                'issue_description' => 'required|string',
                'photo' => 'nullable|image|max:8192',
            ]);

            $customerId = $validated['customer_id'];
            $customer = Customer::find($customerId);
            $firmId = $validated['firm_id'] ?? $customer?->firm_id;
        }

        // 4. Resolve Product / Machinery Type
        $productTypeId = null;
        if ($request->filled('new_product_type')) {
            $prod = ProductType::firstOrCreate(['name' => trim($request->new_product_type)]);
            $productTypeId = $prod->id;
        } elseif ($request->filled('product_type_id')) {
            $productTypeId = $request->input('product_type_id');
        }
        if (!$productTypeId) {
            $firstProduct = ProductType::first();
            $productTypeId = $firstProduct ? $firstProduct->id : ProductType::create(['name' => 'General Machinery'])->id;
        }

        // 5. Resolve Service Type
        $serviceTypeId = null;
        if ($request->filled('new_service_type')) {
            $srv = ServiceType::firstOrCreate(['name' => trim($request->new_service_type)]);
            $serviceTypeId = $srv->id;
        } elseif ($request->filled('service_type_id')) {
            $serviceTypeId = $request->input('service_type_id');
        }
        if (!$serviceTypeId) {
            $firstService = ServiceType::first();
            $serviceTypeId = $firstService ? $firstService->id : ServiceType::create(['name' => 'Breakdown Service'])->id;
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('complaints/initial', 'public');
        }

        // Generate Product & Month-wise Ticket Number (e.g. S2611001, C2611001, P2611001, O2611001)
        $ticketNumber = Complaint::generateTicketNumber($productTypeId);

        $complaint = Complaint::create([
            'ticket_number' => $ticketNumber,
            'firm_id' => $firmId,
            'customer_id' => $customerId,
            'product_type_id' => $productTypeId,
            'service_type_id' => $serviceTypeId,
            'engineer_id' => $validated['engineer_id'],
            'due_date' => $validated['due_date'],
            'priority' => $validated['priority'],
            'issue_description' => $validated['issue_description'],
            'photo' => $photoPath,
            'status' => 'pending',
            'created_by' => Auth::id(),
        ]);

        $logMsg = $customerMode === 'new' 
            ? "Quick-created customer '{$customer->name}' and booked ticket {$ticketNumber}"
            : "Logged ticket {$ticketNumber} for customer {$customer->name}";

        AuditLog::log('CREATE_COMPLAINT', 'Service Complaints', (string)$complaint->id, $logMsg);

        return redirect()->route('complaints.show', $complaint)->with('success', "Service Complaint {$ticketNumber} booked successfully!");
    }

    public function show(Complaint $complaint)
    {
        $user = Auth::user();
        if ($user->isEngineer() && $complaint->engineer_id !== $user->id) {
            abort(403, 'Unauthorized. You can only view tickets assigned to you.');
        }

        $complaint->load(['customer.organization', 'organization.city', 'productType', 'serviceType', 'engineer', 'creator', 'bill', 'expenses.engineer']);

        return view('complaints.show', compact('complaint'));
    }

    public function startService(Complaint $complaint)
    {
        $user = Auth::user();
        if ($user->isEngineer() && $complaint->engineer_id !== $user->id) {
            abort(403);
        }

        $complaint->update([
            'is_started' => true,
            'started_at' => now(),
            'status' => 'in_progress',
        ]);

        AuditLog::log('START_SERVICE', 'Service Complaints', (string)$complaint->id, "Engineer {$user->name} started service on ticket {$complaint->ticket_number}");

        return back()->with('success', "Service has been started on ticket {$complaint->ticket_number}.");
    }

    public function resolve(Request $request, Complaint $complaint)
    {
        $user = Auth::user();
        if ($user->isEngineer() && $complaint->engineer_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'resolution_notes' => 'required|string',
        ]);

        $complaint->update([
            'is_resolved' => true,
            'resolved_at' => now(),
            'status' => 'completed',
            'resolution_notes' => $request->resolution_notes,
        ]);

        AuditLog::log('RESOLVE_SERVICE', 'Service Complaints', (string)$complaint->id, "Service resolved for ticket {$complaint->ticket_number}");

        return back()->with('success', "Service complaint marked as resolved.");
    }

    public function cancel(Request $request, Complaint $complaint)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $complaint->update([
            'status' => 'cancelled',
            'is_deleted' => true,
        ]);

        AuditLog::log('CANCEL_COMPLAINT', 'Service Complaints', (string)$complaint->id, "Cancelled ticket {$complaint->ticket_number}");

        return redirect()->route('complaints.index')->with('success', "Complaint {$complaint->ticket_number} has been cancelled.");
    }

    public function edit(Complaint $complaint)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $customers = Customer::where('is_active', true)->with('organization')->get();
        $firms = Organization::where('is_active', true)->get();
        $productTypes = ProductType::where('is_active', true)->get();
        $serviceTypes = ServiceType::where('is_active', true)->get();
        $engineers = User::where('role', 'engineer')->where('is_active', true)->get();

        return view('complaints.edit', compact('complaint', 'customers', 'firms', 'productTypes', 'serviceTypes', 'engineers'));
    }

    public function update(Request $request, Complaint $complaint)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'firm_id' => 'nullable|exists:organizations,id',
            'product_type_id' => 'required|exists:product_types,id',
            'service_type_id' => 'required|exists:service_types,id',
            'engineer_id' => 'required|exists:users,id',
            'due_date' => 'required|date',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'issue_description' => 'required|string',
            'resolution_notes' => 'nullable|string',
            'photo' => 'nullable|image|max:8192',
        ]);

        if (empty($validated['firm_id'])) {
            $customer = Customer::find($validated['customer_id']);
            $validated['firm_id'] = $customer?->firm_id;
        }

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('complaints', 'public');
        }

        if ($validated['status'] === 'completed') {
            $validated['is_resolved'] = true;
            $validated['resolved_at'] = $complaint->resolved_at ?? now();
        }

        $complaint->update($validated);

        AuditLog::log('UPDATE_COMPLAINT', 'Service Complaints', (string)$complaint->id, "Updated ticket {$complaint->ticket_number}");

        return redirect()->route('complaints.show', $complaint)->with('success', "Ticket {$complaint->ticket_number} updated successfully.");
    }

    public function downloadPdf(Complaint $complaint)
    {
        $complaint->load(['customer', 'organization', 'engineer', 'productType', 'serviceType', 'bill']);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.ticket', compact('complaint'));
        return $pdf->download("JobSheet-{$complaint->ticket_number}.pdf");
    }

    public function printView(Complaint $complaint)
    {
        $complaint->load(['customer', 'organization', 'engineer', 'productType', 'serviceType', 'bill']);
        return view('pdf.ticket', compact('complaint'));
    }

}
