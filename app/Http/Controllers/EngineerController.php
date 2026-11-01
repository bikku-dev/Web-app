<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class EngineerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'engineer');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone_no', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%")
                  ->orWhere('post', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->boolean('status'));
        }

        $engineers = $query->withCount([
            'assignedComplaints as total_complaints',
            'assignedComplaints as completed_complaints' => function ($q) {
                $q->where('status', 'completed');
            },
            'assignedComplaints as pending_complaints' => function ($q) {
                $q->whereIn('status', ['pending', 'in_progress']);
            }
        ])->latest()->paginate(10)->withQueryString();

        return view('engineers.index', compact('engineers'));
    }

    public function create()
    {
        return view('engineers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone_no' => 'required|string|max:20|unique:users,phone_no',
            'country_code' => 'nullable|string|max:10',
            'email' => 'nullable|email|max:255',
            'password' => 'required|string|min:6',
            'address' => 'nullable|string',
            'dob' => 'nullable|string',
            'blood_group' => 'nullable|string|max:10',
            'department' => 'nullable|string|max:100',
            'post' => 'nullable|string|max:100',
            'family_contact_no' => 'nullable|string|max:20',
            'remarks' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:20480',
            'resume' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:20480',
        ]);

        $validated['country_code'] = $request->input('country_code', '+91') ?: '+91';

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('engineers/photos', 'public');
        }

        $resumePath = null;
        if ($request->hasFile('resume')) {
            $resumePath = $request->file('resume')->store('engineers/resumes', 'public');
        }

        $engineer = User::create([
            'name' => $validated['name'],
            'phone_no' => $validated['phone_no'],
            'country_code' => $validated['country_code'] ?? '+91',
            'email' => $validated['email'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => 'engineer',
            'address' => $validated['address'] ?? null,
            'dob' => $validated['dob'] ?? null,
            'blood_group' => $validated['blood_group'] ?? null,
            'department' => $validated['department'] ?? null,
            'post' => $validated['post'] ?? null,
            'family_contact_no' => $validated['family_contact_no'] ?? null,
            'remarks' => $validated['remarks'] ?? null,
            'photo' => $photoPath,
            'resume' => $resumePath,
            'is_verified' => true,
            'is_active' => true,
        ]);

        AuditLog::log('CREATE_ENGINEER', 'Engineer Management', (string)$engineer->id, "Created engineer profile for {$engineer->name}");

        return redirect()->route('engineers.index')->with('success', "Engineer {$engineer->name} created successfully.");
    }

    public function show(User $engineer)
    {
        $complaints = $engineer->assignedComplaints()
            ->with(['customer', 'organization', 'productType', 'serviceType', 'bill'])
            ->latest()
            ->take(15)
            ->get();

        $attendances = $engineer->attendances()
            ->latest('recorded_at')
            ->take(10)
            ->get();

        $expenses = $engineer->expenses()
            ->latest()
            ->take(10)
            ->get();

        return view('engineers.show', compact('engineer', 'complaints', 'attendances', 'expenses'));
    }

    public function edit(User $engineer)
    {
        return view('engineers.edit', compact('engineer'));
    }

    public function update(Request $request, User $engineer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone_no' => ['required', 'string', 'max:20', Rule::unique('users')->ignore($engineer->id)],
            'country_code' => 'nullable|string|max:10',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'dob' => 'nullable|string',
            'blood_group' => 'nullable|string|max:10',
            'department' => 'nullable|string|max:100',
            'post' => 'nullable|string|max:100',
            'family_contact_no' => 'nullable|string|max:20',
            'remarks' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:20480',
            'resume' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:20480',
        ]);

        $validated['country_code'] = $request->input('country_code', $engineer->country_code ?? '+91') ?: '+91';

        if ($request->hasFile('photo')) {
            if ($engineer->photo) {
                Storage::disk('public')->delete($engineer->photo);
            }
            $validated['photo'] = $request->file('photo')->store('engineers/photos', 'public');
        }

        if ($request->hasFile('resume')) {
            if ($engineer->resume) {
                Storage::disk('public')->delete($engineer->resume);
            }
            $validated['resume'] = $request->file('resume')->store('engineers/resumes', 'public');
        }

        if ($request->filled('new_password')) {
            $validated['password'] = Hash::make($request->new_password);
        }

        $engineer->update($validated);

        AuditLog::log('UPDATE_ENGINEER', 'Engineer Management', (string)$engineer->id, "Updated details for engineer {$engineer->name}");

        return redirect()->route('engineers.show', $engineer)->with('success', "Engineer details updated successfully.");
    }

    public function destroy(User $engineer)
    {
        $engineer->update(['is_active' => false]);
        $engineer->delete();

        AuditLog::log('DELETE_ENGINEER', 'Engineer Management', (string)$engineer->id, "Soft deleted engineer {$engineer->name}");

        return redirect()->route('engineers.index')->with('success', "Engineer has been removed and soft-deleted.");
    }

    public function toggleStatus(User $engineer)
    {
        $engineer->update(['is_active' => !$engineer->is_active]);

        AuditLog::log('TOGGLE_ENGINEER_STATUS', 'Engineer Management', (string)$engineer->id, "Toggled status to " . ($engineer->is_active ? 'Active' : 'Inactive'));

        return back()->with('success', "Status for {$engineer->name} updated.");
    }
}
