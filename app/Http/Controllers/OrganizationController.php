<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Organization;
use App\Models\Area;
use App\Models\City;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Storage;

class OrganizationController extends Controller
{
    public function index(Request $request)
    {
        $query = Organization::with(['area', 'city'])->withCount('customers', 'complaints');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('contact_no', 'like', "%{$search}%")
                  ->orWhere('gst_no', 'like', "%{$search}%");
            });
        }

        if ($request->filled('area_id')) {
            $query->where('area_id', $request->area_id);
        }

        $organizations = $query->latest()->paginate(10)->withQueryString();
        $areas = Area::where('is_active', true)->get();

        return view('organizations.index', compact('organizations', 'areas'));
    }

    public function create()
    {
        $areas = Area::where('is_active', true)->get();
        $cities = City::where('is_active', true)->get();
        return view('organizations.create', compact('areas', 'cities'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'contact_no' => 'nullable|string|max:20',
            'area_id' => 'nullable|exists:areas,id',
            'city_id' => 'nullable|exists:cities,id',
            'address' => 'nullable|string',
            'pincode' => 'nullable|string|max:10',
            'gst_no' => 'nullable|string|max:25',
            'remarks' => 'nullable|string',
            'photo' => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('organizations', 'public');
        }

        $validated['is_active'] = true;
        $org = Organization::create($validated);

        AuditLog::log('CREATE_ORGANIZATION', 'Organizations', (string)$org->id, "Created firm: {$org->name}");

        return redirect()->route('organizations.index')->with('success', "Organization '{$org->name}' created successfully.");
    }

    public function show(Organization $organization)
    {
        $organization->load(['area', 'city', 'customers', 'complaints.engineer', 'complaints.productType', 'bills']);
        return view('organizations.show', compact('organization'));
    }

    public function edit(Organization $organization)
    {
        $areas = Area::where('is_active', true)->get();
        $cities = City::where('is_active', true)->get();
        return view('organizations.edit', compact('organization', 'areas', 'cities'));
    }

    public function update(Request $request, Organization $organization)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'contact_no' => 'nullable|string|max:20',
            'area_id' => 'nullable|exists:areas,id',
            'city_id' => 'nullable|exists:cities,id',
            'address' => 'nullable|string',
            'pincode' => 'nullable|string|max:10',
            'gst_no' => 'nullable|string|max:25',
            'remarks' => 'nullable|string',
            'photo' => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('photo')) {
            if ($organization->photo) {
                Storage::disk('public')->delete($organization->photo);
            }
            $validated['photo'] = $request->file('photo')->store('organizations', 'public');
        }

        $organization->update($validated);

        AuditLog::log('UPDATE_ORGANIZATION', 'Organizations', (string)$organization->id, "Updated firm: {$organization->name}");

        return redirect()->route('organizations.show', $organization)->with('success', "Organization details updated.");
    }

    public function toggleStatus(Organization $organization)
    {
        $organization->update(['is_active' => !$organization->is_active]);
        return back()->with('success', "Organization status changed.");
    }
}
