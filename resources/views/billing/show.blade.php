@extends('layouts.app')

@section('title', 'Invoice #' . $bill->bill_number)
@section('page_title', 'Billing & Collection Details')

@section('content')
<div class="w-full space-y-6" x-data="{ handoverCardOpen: false, creditCardOpen: false }">

    <!-- INVOICE CARD -->
    <div class="bg-surface rounded-3xl p-6 sm:p-8 border border-borderMain shadow-sm">
        <!-- Invoice Header -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-6 border-b border-borderMain">
            <div>
                <span class="text-xs uppercase font-extrabold text-secondary tracking-wider">Service Invoice & Cash Receipt</span>
                <h2 class="text-2xl font-black text-primary mt-0.5">{{ $bill->bill_number }}</h2>
                <p class="text-xs text-textMuted mt-1">Recorded on {{ $bill->created_at->format('d M Y, h:i A') }}</p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('billing.pdf', $bill) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold bg-primary text-white hover:bg-slate-800 transition flex items-center space-x-1.5 shadow-sm">
                    <i data-lucide="file-down" class="w-3.5 h-3.5"></i>
                    <span>Download PDF</span>
                </a>
                <a href="{{ route('billing.print', $bill) }}" target="_blank" class="px-3 py-1.5 rounded-xl text-xs font-bold border border-borderMain bg-bg hover:bg-slate-100 text-textMain transition flex items-center space-x-1.5">
                    <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                    <span>Print</span>
                </a>

                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase {{ $bill->bill_type == 'cash' ? 'bg-emerald-100 text-emerald-800' : ($bill->bill_type == 'credit' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800') }}">
                    {{ $bill->bill_type == 'bill' ? 'GST Invoice' : $bill->bill_type }}
                </span>

                @if($bill->is_handed)
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 flex items-center space-x-1">
                        <i data-lucide="check-check" class="w-3.5 h-3.5"></i>
                        <span>Handover Verified</span>
                    </span>
                @else
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 flex items-center space-x-1">
                        <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                        <span>Handover Pending</span>
                    </span>
                @endif
            </div>
        </div>

        <!-- Billing Info Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 py-6 border-b border-borderMain text-xs">
            <div>
                <span class="text-textMuted uppercase font-bold text-[10px] block mb-1">Billed To (Client):</span>
                <strong class="text-sm text-textMain block">{{ $bill->customer?->name }}</strong>
                <p class="text-textMuted">{{ $bill->organization?->name ?? 'Individual Client' }}</p>
                <p class="text-textMuted mt-1">{{ $bill->customer?->address }}</p>
                @if($bill->customer?->contact_no)
                    <div class="flex items-center gap-2 mt-2">
                        <a href="tel:{{ $bill->customer->contact_no }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white text-[11px] font-bold border border-emerald-200 transition" title="Call Client">
                            <i data-lucide="phone" class="w-3 h-3"></i>
                            <span>{{ $bill->customer->contact_no }}</span>
                        </a>
                        <a href="https://wa.me/91{{ preg_replace('/[^0-9]/', '', $bill->customer->contact_no) }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-[11px] font-bold transition shadow-sm" title="WhatsApp Client">
                            <i data-lucide="message-circle" class="w-3 h-3"></i>
                            <span>WhatsApp</span>
                        </a>
                    </div>
                @endif
                @if($bill->organization?->gst_no)
                    <p class="text-textMuted font-mono text-[11px] mt-1.5">Client GST: {{ $bill->organization->gst_no }}</p>
                @endif
            </div>

            <div>
                <span class="text-textMuted uppercase font-bold text-[10px] block mb-1">Service Engineer:</span>
                <strong class="text-sm text-textMain block">{{ $bill->engineer?->name }}</strong>
                <p class="text-textMuted">{{ $bill->engineer?->post ?? 'Field Engineer' }}</p>
                @if($bill->engineer?->phone_no)
                    <div class="flex items-center gap-2 mt-2">
                        <a href="tel:{{ $bill->engineer->phone_no }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 text-slate-800 hover:bg-slate-200 text-[11px] font-bold border border-borderMain transition" title="Call Engineer">
                            <i data-lucide="phone" class="w-3 h-3 text-secondary"></i>
                            <span>{{ $bill->engineer->phone_no }}</span>
                        </a>
                        <a href="https://wa.me/91{{ preg_replace('/[^0-9]/', '', $bill->engineer->phone_no) }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-[11px] font-bold transition shadow-sm" title="WhatsApp Engineer">
                            <i data-lucide="message-circle" class="w-3 h-3"></i>
                            <span>WhatsApp</span>
                        </a>
                    </div>
                @endif

                @if($bill->complaint)
                    <div class="mt-3 pt-2 border-t border-borderMain">
                        <span class="text-[10px] text-textMuted font-bold uppercase block">Linked Complaint:</span>
                        <a href="{{ route('complaints.show', $bill->complaint) }}" class="text-secondary font-bold hover:underline">
                            {{ $bill->complaint->ticket_number }} &rarr;
                        </a>
                    </div>
                @endif
            </div>

            <div class="bg-bg p-4 rounded-2xl border border-borderMain flex flex-col justify-between">
                <div>
                    <span class="text-textMuted uppercase font-bold text-[10px] block">Invoice Total Amount:</span>
                    <div class="text-3xl font-black text-primary mt-1">₹{{ number_format($bill->amount, 2) }}</div>
                    @if($bill->gst_bill_number)
                        <div class="mt-2 text-xs font-semibold text-blue-700">
                            GST No: <strong class="font-mono">{{ $bill->gst_bill_number }}</strong>
                        </div>
                    @endif
                </div>

                <div class="pt-3 border-t border-borderMain/80 text-[11px] text-textMuted">
                    Status: <strong class="capitalize text-textMain">{{ $bill->payment_status }}</strong>
                </div>
            </div>
        </div>

        <!-- Remarks -->
        @if($bill->remarks)
            <div class="py-4 border-b border-borderMain text-xs">
                <span class="text-textMuted font-bold uppercase text-[10px] block mb-1">Work Description / Remarks:</span>
                <div class="text-textMain leading-relaxed font-medium">@linkify($bill->remarks)</div>
            </div>
        @endif

        <!-- Handover Audit Details -->
        @if($bill->is_handed)
            <div class="p-4 my-4 rounded-2xl bg-emerald-50/70 border border-emerald-200 text-xs text-emerald-950 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-700 flex-shrink-0">
                        <i data-lucide="shield-check" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <div class="font-bold">Physical Cash Handover Verified</div>
                        <div class="text-[11px] text-emerald-800">
                            Verified by <strong>{{ $bill->handedOverTo?->name ?? 'Admin' }}</strong> on {{ $bill->handed_over_at?->format('d M Y, h:i A') }}
                        </div>
                    </div>
                </div>
                @if($bill->handover_remarks)
                    <div class="text-[11px] text-emerald-900 bg-white/70 px-3 py-1.5 rounded-xl border border-emerald-300">
                        Remarks: {{ $bill->handover_remarks }}
                    </div>
                @endif
            </div>
        @endif

        <!-- Photos: Receipt Voucher & Machine Asset Photo -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4">
            @if($bill->photo)
                <div>
                    <span class="text-[11px] font-bold text-textMuted uppercase block mb-1.5">Customer Signed Voucher</span>
                    <a href="{{ asset('storage/' . $bill->photo) }}" target="_blank" class="block rounded-2xl overflow-hidden border border-borderMain hover:opacity-90 transition shadow-sm max-w-sm">
                        <img src="{{ asset('storage/' . $bill->photo) }}" alt="Voucher" class="w-full h-44 object-cover">
                    </a>
                </div>
            @endif

            @if($bill->asset_photo)
                <div>
                    <span class="text-[11px] font-bold text-textMuted uppercase block mb-1.5">Serviced Machine / Equipment Photo</span>
                    <a href="{{ asset('storage/' . $bill->asset_photo) }}" target="_blank" class="block rounded-2xl overflow-hidden border border-borderMain hover:opacity-90 transition shadow-sm max-w-sm">
                        <img src="{{ asset('storage/' . $bill->asset_photo) }}" alt="Machine Asset" class="w-full h-44 object-cover">
                    </a>
                </div>
            @endif
        </div>

        <!-- ACTION CONTROLS: VERIFY HANDOVER & CREDIT COLLECTION -->
        <div class="pt-6 mt-6 border-t border-borderMain flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('billing.index') }}" class="px-4 py-2.5 rounded-xl border border-borderMain text-xs font-bold text-textMain hover:bg-bg transition">
                    &larr; Back to Ledger
                </a>
                <a href="{{ route('billing.pdf', $bill) }}" class="px-4 py-2.5 rounded-xl bg-primary text-white hover:bg-slate-800 transition text-xs font-bold shadow-sm flex items-center space-x-1.5">
                    <i data-lucide="file-down" class="w-4 h-4"></i>
                    <span>Download PDF Invoice</span>
                </a>
            </div>

            <div class="flex items-center space-x-2">
                @if($bill->bill_type === 'credit')
                    <button type="button" @click="creditCardOpen = !creditCardOpen" class="px-4 py-2.5 rounded-xl bg-accent hover:bg-amber-500 text-primary font-bold text-xs shadow-md transition flex items-center space-x-1.5">
                        <i data-lucide="hand-coins" class="w-4 h-4"></i>
                        <span x-text="creditCardOpen ? 'Close Payment Form' : 'Record Payment Collection'">Record Payment Collection</span>
                    </button>
                @endif

                @if(!$bill->is_handed && auth()->user()->isAdmin())
                    <button type="button" @click="handoverCardOpen = !handoverCardOpen" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md transition flex items-center space-x-1.5">
                        <i data-lucide="check-check" class="w-4 h-4"></i>
                        <span x-text="handoverCardOpen ? 'Close Handover Form' : 'Verify Cash Handover'">Verify Cash Handover</span>
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- CREDIT COLLECTIONS HISTORY (IF CREDIT BILL) -->
    @if($bill->bill_type === 'credit')
        <div class="bg-surface rounded-3xl p-6 border border-borderMain shadow-sm">
            <h3 class="font-bold text-sm text-textMain mb-3 flex items-center space-x-2">
                <i data-lucide="history" class="w-4 h-4 text-secondary"></i>
                <span>Credit Payment Collections Log</span>
            </h3>

            <div class="space-y-2">
                @forelse($bill->creditCollections as $col)
                    <div class="p-3.5 rounded-2xl bg-bg border border-borderMain flex items-center justify-between text-xs">
                        <div>
                            <span class="font-bold text-sm text-emerald-600">+₹{{ number_format($col->amount, 2) }}</span>
                            <span class="text-textMuted ml-2">collected by <strong>{{ $col->collectedBy?->name }}</strong></span>
                            @if($col->notes)
                                <p class="text-textMuted mt-0.5">{{ $col->notes }}</p>
                            @endif
                        </div>
                        <span class="text-[11px] text-textMuted">{{ $col->collected_at->format('d M Y, h:i A') }}</span>
                    </div>
                @empty
                    <p class="text-xs text-textMuted text-center py-4">No payments recorded against this credit bill yet.</p>
                @endforelse
            </div>
        </div>
    @endif

    <!-- IN-PAGE VERIFY HANDOVER SECTION (ADMIN) -->
    <div x-show="handoverCardOpen" x-cloak class="bg-surface rounded-2xl p-6 border-2 border-emerald-400 shadow-sm space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-app-border">
            <div>
                <h3 class="font-bold text-sm text-primary-text flex items-center gap-2">
                    <i data-lucide="check-check" class="w-4 h-4 text-emerald-600"></i> Verify Physical Cash Handover
                </h3>
                <p class="text-xs text-secondary-text">Confirm receipt of ₹{{ number_format($bill->amount, 2) }} physical cash in company office treasury.</p>
            </div>
            <button type="button" @click="handoverCardOpen = false" class="text-secondary-text hover:text-primary-text"><i data-lucide="x" class="w-4 h-4"></i></button>
        </div>

        <form action="{{ route('billing.handover', $bill) }}" method="POST" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-primary-text mb-1">Official GST Invoice No. (Optional)</label>
                <input type="text" name="gst_bill_number" value="{{ $bill->gst_bill_number }}" placeholder="e.g. GST-INV-2026-904" class="w-full px-3 py-2 rounded-lg border border-app-border text-xs bg-white">
            </div>

            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-primary-text mb-1">Handover Verification Remarks</label>
                <input type="text" name="handover_remarks" placeholder="Received physical currency notes from engineer with signed challan..." class="w-full px-3 py-2 rounded-lg border border-app-border text-xs bg-white">
            </div>

            <div class="sm:col-span-3 flex items-center justify-end gap-2 pt-2 border-t border-app-border">
                <button type="button" @click="handoverCardOpen = false" class="px-4 py-2 rounded-lg border border-app-border text-xs font-medium text-secondary-text hover:bg-app-bg">Cancel</button>
                <button type="submit" class="px-5 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-sm">Confirm Cash Received</button>
            </div>
        </form>
    </div>

    <!-- IN-PAGE CREDIT COLLECTION SECTION -->
    <div x-show="creditCardOpen" x-cloak class="bg-surface rounded-2xl p-6 border-2 border-accent shadow-sm space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-app-border">
            <div>
                <h3 class="font-bold text-sm text-primary-text flex items-center gap-2">
                    <i data-lucide="hand-coins" class="w-4 h-4 text-accent"></i> Collect Outstanding Customer Credit
                </h3>
                <p class="text-xs text-secondary-text">Record cash, online, or cheque collection against Bill #{{ $bill->bill_number }}.</p>
            </div>
            <button type="button" @click="creditCardOpen = false" class="text-secondary-text hover:text-primary-text"><i data-lucide="x" class="w-4 h-4"></i></button>
        </div>

        <form action="{{ route('billing.credit-collect', $bill) }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-primary-text mb-1">Amount Collected (₹) <span class="text-app-error">*</span></label>
                <input type="number" step="0.01" name="amount" max="{{ $bill->amount }}" value="{{ $bill->amount }}" required class="w-full px-3 py-2 rounded-lg border border-app-border text-xs bg-white font-bold text-primary">
            </div>

            <div>
                <label class="block text-xs font-semibold text-primary-text mb-1">Payment Proof Image (Optional)</label>
                <input type="file" name="receipt_photo" accept="image/*" class="w-full text-xs text-secondary-text file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-primary file:text-white">
            </div>

            <div>
                <label class="block text-xs font-semibold text-primary-text mb-1">Cheque / Voucher Notes</label>
                <input type="text" name="notes" placeholder="Cheque # or NEFT ref details..." class="w-full px-3 py-2 rounded-lg border border-app-border text-xs bg-white">
            </div>

            <div class="sm:col-span-3 flex items-center justify-end gap-2 pt-2 border-t border-app-border">
                <button type="button" @click="creditCardOpen = false" class="px-4 py-2 rounded-lg border border-app-border text-xs font-medium text-secondary-text hover:bg-app-bg">Cancel</button>
                <button type="submit" class="px-5 py-2 rounded-lg bg-accent hover:bg-amber-500 text-primary font-bold text-xs shadow-sm">Save Payment Collection</button>
            </div>
        </form>
    </div>

</div>
@endsection
