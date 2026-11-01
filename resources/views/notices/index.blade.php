@extends('layouts.app')

@section('title', 'Company Notices & Announcements | Bhumi Technocare')
@section('page_title', 'Staff Notices & Announcements')

@section('content')
<div class="w-full space-y-6" x-data="{ showNoticeForm: false }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-borderMain">
        <div>
            <h1 class="text-2xl font-bold text-textMain flex items-center gap-2.5">
                <span class="w-9 h-9 rounded-2xl bg-accent/15 text-accent flex items-center justify-center shadow-sm">
                    <i data-lucide="bell" class="w-5 h-5"></i>
                </span>
                Company Notices & Announcements
            </h1>
            <p class="text-xs text-textMuted mt-0.5">Broadcast operational advisories, safety instructions, and company notices to all field engineers</p>
        </div>

        @if(auth()->user()->isAdmin())
        <button type="button" @click="showNoticeForm = !showNoticeForm" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary hover:bg-slate-800 text-white text-xs font-bold shadow-md transition-all active:scale-95">
            <i data-lucide="megaphone" class="w-4 h-4 text-accent"></i>
            <span x-text="showNoticeForm ? 'Close Form' : 'Publish New Notice'">Publish New Notice</span>
        </button>
        @endif
    </div>

    @if(auth()->user()->isAdmin())
    <!-- IN-PAGE PUBLISH NOTICE ACCORDION CARD -->
    <div x-show="showNoticeForm" x-cloak class="bg-surface rounded-3xl border-2 border-accent/60 shadow-lg p-6 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-borderMain">
            <div>
                <h3 class="text-sm font-bold text-textMain flex items-center gap-2">
                    <i data-lucide="send" class="w-4 h-4 text-accent"></i> Create & Publish Company Broadcast
                </h3>
                <p class="text-xs text-textMuted">Broadcasted notices will immediately appear in the field app and admin portal.</p>
            </div>
            <button type="button" @click="showNoticeForm = false" class="text-textMuted hover:text-textMain p-1 rounded-lg">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form action="{{ route('notices.store') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @csrf
            <div class="sm:col-span-2">
                <label class="block text-xs font-bold text-textMain mb-1">Headline / Title <span class="text-rose-600">*</span></label>
                <input type="text" name="title" required placeholder="e.g. Mandatory Safety Helmets & Safety Shoes for CNC Machine Installations" class="w-full text-xs rounded-xl border border-slate-300 bg-white focus:ring-2 focus:ring-secondary py-2.5 px-3">
            </div>

            <div>
                <label class="block text-xs font-bold text-textMain mb-1">Expiry Date (Optional)</label>
                <input type="date" name="expires_at" min="{{ date('Y-m-d') }}" class="w-full text-xs rounded-xl border border-slate-300 bg-white focus:ring-2 focus:ring-secondary py-2.5 px-3">
            </div>

            <div class="sm:col-span-3">
                <label class="block text-xs font-bold text-textMain mb-1">Notice Content & Instructions <span class="text-rose-600">*</span></label>
                <textarea name="notice" rows="3" required placeholder="Write clear instructions or attach links for field engineers..." class="w-full text-xs rounded-xl border border-slate-300 bg-white focus:ring-2 focus:ring-secondary p-3"></textarea>
            </div>

            <div class="sm:col-span-3 flex justify-end gap-2 pt-2 border-t border-borderMain">
                <button type="button" @click="showNoticeForm = false" class="px-4 py-2 text-xs font-semibold rounded-xl border border-borderMain text-textMuted hover:bg-bg">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 text-xs font-bold rounded-xl bg-primary hover:bg-slate-800 text-white shadow-md flex items-center gap-1.5 active:scale-95 transition">
                    <i data-lucide="check" class="w-3.5 h-3.5 text-accent"></i> Publish Notice
                </button>
            </div>
        </form>
    </div>
    @endif

    <!-- Notice Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @forelse($notices as $notice)
        <div class="bg-surface rounded-2xl border border-borderMain p-5 shadow-sm space-y-3 relative hover:border-secondary/40 transition">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-9 h-9 rounded-xl bg-accent/15 text-accent flex items-center justify-center shrink-0">
                        <i data-lucide="bell" class="w-4 h-4"></i>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-sm font-bold text-textMain truncate">{{ $notice->title }}</h3>
                        <p class="text-[11px] text-textMuted">
                            By {{ $notice->creator->name ?? 'Admin' }} • {{ $notice->created_at->format('d M Y, h:i A') }}
                        </p>
                    </div>
                </div>

                @if(auth()->user()->isAdmin())
                <div class="flex items-center gap-1.5 shrink-0">
                    @if($notice->is_active)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Active</span>
                    @else
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">Inactive</span>
                    @endif
                    
                    <form action="{{ route('notices.toggle', $notice) }}" method="POST">
                        @csrf
                        <button type="submit" title="Toggle Active Status" class="p-1.5 rounded-lg border border-borderMain hover:bg-bg text-textMuted hover:text-textMain transition">
                            <i data-lucide="power" class="w-3.5 h-3.5"></i>
                        </button>
                    </form>

                    <form action="{{ route('notices.destroy', $notice) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this notice?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" title="Delete Notice" class="p-1.5 rounded-lg border border-rose-200 text-rose-600 hover:bg-rose-50 transition">
                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                        </button>
                    </form>
                </div>
                @endif
            </div>

            <div class="text-xs text-textMain leading-relaxed bg-bg p-3.5 rounded-xl border border-borderMain">
                @linkify($notice->notice)
            </div>

            <div class="flex items-center justify-between text-[11px] text-textMuted pt-1 border-t border-borderMain/50">
                @if($notice->expires_at)
                    <span class="flex items-center gap-1 text-amber-700 font-semibold">
                        <i data-lucide="clock" class="w-3.5 h-3.5"></i> Valid until {{ \Carbon\Carbon::parse($notice->expires_at)->format('d M Y') }}
                    </span>
                @else
                    <span class="text-textMuted">Permanent Announcement</span>
                @endif
                <span class="text-[10px] text-textMuted">Notice #{{ $notice->id }}</span>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-surface rounded-3xl border border-borderMain p-12 text-center">
            <i data-lucide="bell-off" class="w-10 h-10 text-textMuted/40 mx-auto mb-2"></i>
            <p class="text-sm font-bold text-textMain">No Active Notices</p>
            <p class="text-xs text-textMuted mt-0.5">There are currently no broadcast announcements for staff.</p>
        </div>
        @endforelse
    </div>

    @if($notices->hasPages())
    <div class="p-4 bg-surface rounded-2xl border border-borderMain">
        {{ $notices->links() }}
    </div>
    @endif
</div>
@endsection
