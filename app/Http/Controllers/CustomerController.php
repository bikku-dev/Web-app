<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Organization;
use App\Models\Area;
use App\Models\City;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::with(['organization', 'area', 'city'])->withCount('complaints', 'bills');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('contact_no', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%");
            });
        }

        if ($request->filled('firm_id')) {
            $query->where('firm_id', $request->firm_id);
        }

        $customers = $query->latest()->paginate(10)->withQueryString();
        $organizations = Organization::where('is_active', true)->get();

        return view('customers.index', compact('customers', 'organizations'));
    }

    public function create()
    {
        $organizations = Organization::where('is_active', true)->get();
        $areas = Area::where('is_active', true)->get();
        $cities = City::where('is_active', true)->get();
        return view('customers.create', compact('organizations', 'areas', 'cities'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_no' => 'required|string|max:20',
            'firm_id' => 'nullable|exists:organizations,id',
            'department' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'area_id' => 'nullable|exists:areas,id',
            'city_id' => 'nullable|exists:cities,id',
            'remarks' => 'nullable|string',
            'photo' => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('customers', 'public');
        }

        $validated['is_active'] = true;
        $customer = Customer::create($validated);

        AuditLog::log('CREATE_CUSTOMER', 'Customers', (string)$customer->id, "Created customer contact: {$customer->name}");

        return redirect()->route('customers.index')->with('success', "Customer '{$customer->name}' added.");
    }

    public function show(Customer $customer)
    {
        $customer->load(['organization', 'area', 'city', 'complaints.engineer', 'complaints.productType', 'bills']);
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        $organizations = Organization::where('is_active', true)->get();
        $areas = Area::where('is_active', true)->get();
        $cities = City::where('is_active', true)->get();
        return view('customers.edit', compact('customer', 'organizations', 'areas', 'cities'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_no' => 'required|string|max:20',
            'firm_id' => 'nullable|exists:organizations,id',
            'department' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'area_id' => 'nullable|exists:areas,id',
            'city_id' => 'nullable|exists:cities,id',
            'remarks' => 'nullable|string',
            'photo' => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('photo')) {
            if ($customer->photo) {
                Storage::disk('public')->delete($customer->photo);
            }
            $validated['photo'] = $request->file('photo')->store('customers', 'public');
        }

        $customer->update($validated);

        AuditLog::log('UPDATE_CUSTOMER', 'Customers', (string)$customer->id, "Updated customer: {$customer->name}");

        return redirect()->route('customers.show', $customer)->with('success', "Customer details updated.");
    }

    public function toggleStatus(Customer $customer)
    {
        $customer->update(['is_active' => !$customer->is_active]);
        return back()->with('success', "Customer status changed.");
    }
}
