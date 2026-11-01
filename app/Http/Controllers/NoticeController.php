<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notice;
use App\Models\AuditLog;

class NoticeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->isAdmin()) {
            $notices = Notice::with('creator')->latest()->paginate(15);
        } else {
            $notices = Notice::where('is_active', true)
                ->where(function($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>=', now()->toDateString());
                })
                ->latest()
                ->paginate(15);
        }

        return view('notices.index', compact('notices'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'notice' => 'required|string',
            'expires_at' => 'nullable|date|after_or_equal:today',
        ]);

        $notice = Notice::create([
            'title' => $validated['title'],
            'notice' => $validated['notice'],
            'created_by' => Auth::id(),
            'is_active' => true,
            'expires_at' => $validated['expires_at'] ?? null,
        ]);

        AuditLog::log('CREATE_NOTICE', 'Notice Board', (string)$notice->id, "Published company notice: {$notice->title}");

        return back()->with('success', "Notice '{$notice->title}' published.");
    }

    public function toggle(Notice $notice)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $notice->update(['is_active' => !$notice->is_active]);

        AuditLog::log('TOGGLE_NOTICE', 'Notice Board', (string)$notice->id, "Toggled notice active status to {$notice->is_active}");

        return back()->with('success', "Notice status updated.");
    }

    public function destroy(Notice $notice)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $notice->delete();

        AuditLog::log('DELETE_NOTICE', 'Notice Board', (string)$notice->id, "Deleted notice: {$notice->title}");

        return back()->with('success', "Notice deleted successfully.");
    }

    public function markAsRead()
    {
        Auth::user()->update([
            'last_read_notices_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notices marked as seen',
        ]);
    }
}
