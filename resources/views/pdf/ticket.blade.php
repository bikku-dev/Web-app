<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Job_Sheet_{{ $complaint->ticket_number }}</title>
    <style>
        @page {
            margin: 18mm 15mm;
            size: a4 portrait;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #1e293b;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }
        .brand-logo {
            font-size: 20px;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -0.5px;
            text-transform: uppercase;
        }
        .brand-sub {
            font-size: 9.5px;
            color: #64748b;
            font-weight: bold;
            margin-top: 1px;
        }
        .report-title {
            text-align: right;
            font-size: 16px;
            font-weight: 800;
            color: #0284c7;
            text-transform: uppercase;
        }
        .ticket-no {
            text-align: right;
            font-size: 13px;
            font-weight: bold;
            color: #0f172a;
            font-family: monospace;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 14px;
            border-collapse: collapse;
        }
        .meta-col {
            width: 50%;
            vertical-align: top;
            padding: 10px 12px;
            background: #f8fafc;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }
        .meta-title {
            font-size: 9px;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }
        .meta-name {
            font-size: 12px;
            font-weight: 800;
            color: #0f172a;
        }
        .section-box {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            margin-bottom: 14px;
            background: #ffffff;
            overflow: hidden;
        }
        .section-header {
            background: #0f172a;
            color: #ffffff;
            font-weight: 800;
            font-size: 10px;
            text-transform: uppercase;
            padding: 6px 12px;
            letter-spacing: 0.5px;
        }
        .section-body {
            padding: 10px 12px;
            font-size: 11px;
        }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
        }
        .badge-resolved {
            background: #dcfce7;
            color: #166534;
        }
        .badge-progress {
            background: #fef3c7;
            color: #92400e;
        }
        .badge-pending {
            background: #e0f2fe;
            color: #0369a1;
        }
        .signatures {
            width: 100%;
            margin-top: 40px;
            clear: both;
        }
        .sig-box {
            width: 45%;
            text-align: center;
            border-top: 1px dashed #94a3b8;
            padding-top: 8px;
            font-size: 10px;
            color: #475569;
        }
        .footer-note {
            margin-top: 25px;
            padding-top: 10px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 8.5px;
            color: #94a3b8;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <table class="header-table">
        <tr>
            <td style="vertical-align: top;">
                <div class="brand-logo">Bhumi Technocare</div>
                <div class="brand-sub">Industrial Machinery Engineering & Breakdown Support</div>
                <div style="font-size: 9px; color: #475569; margin-top: 3px;">
                    Surat, Gujarat, India • Phone: +91 99999 99999 • support@bhumitechnocare.com
                </div>
            </td>
            <td style="vertical-align: top; text-align: right;">
                <div class="report-title">SERVICE JOB SHEET</div>
                <div class="ticket-no">#{{ $complaint->ticket_number }}</div>
                <div style="font-size: 10px; color: #64748b; margin-top: 3px;">
                    Logged: <strong>{{ $complaint->created_at->format('d M Y, h:i A') }}</strong>
                </div>
                <div style="margin-top: 4px;">
                    @if($complaint->status === 'resolved' || $complaint->status === 'closed')
                        <span class="badge badge-resolved">✓ COMPLETED / RESOLVED</span>
                    @elseif($complaint->status === 'in_progress')
                        <span class="badge badge-progress">⚡ IN PROGRESS</span>
                    @else
                        <span class="badge badge-pending">⏳ PENDING</span>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <!-- Metadata Grid -->
    <table class="meta-table">
        <tr>
            <td class="meta-col">
                <div class="meta-title">Client / Factory Information:</div>
                <div class="meta-name">{{ $complaint->customer?->name ?? 'Walk-in Client' }}</div>
                <div style="font-weight: bold; color: #334155;">{{ $complaint->organization?->name }}</div>
                @if($complaint->customer?->address)
                    <div style="color: #64748b; margin-top: 2px;">{{ $complaint->customer->address }}</div>
                @endif
                @if($complaint->customer?->contact_no)
                    <div style="color: #0369a1; font-weight: bold; margin-top: 2px;">Phone: {{ $complaint->customer->contact_no }}</div>
                @endif
            </td>
            <td style="width: 2%;"></td>
            <td class="meta-col">
                <div class="meta-title">Assigned Field Service Engineer:</div>
                <div class="meta-name">{{ $complaint->engineer?->name ?? 'Unassigned' }}</div>
                <div style="color: #64748b;">{{ $complaint->engineer?->post ?? 'Service Technician' }}</div>
                @if($complaint->engineer?->phone_no)
                    <div style="color: #475569; margin-top: 2px;">Mobile: {{ $complaint->engineer->phone_no }}</div>
                @endif
                <div style="margin-top: 4px; font-size: 9.5px; color: #64748b;">
                    Priority: <strong style="text-transform: uppercase;">{{ $complaint->priority }}</strong> •
                    Due Date: <strong>{{ $complaint->due_date?->format('d M Y, h:i A') ?? 'N/A' }}</strong>
                </div>
            </td>
        </tr>
    </table>

    <!-- Machine & Equipment Specifications -->
    <div class="section-box">
        <div class="section-header">1. Machinery & Equipment Details</div>
        <div class="section-body">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 50%; vertical-align: top;">
                        <span style="font-size: 9px; color: #64748b; font-weight: bold; text-transform: uppercase;">Product / Machinery Type:</span>
                        <div style="font-size: 12px; font-weight: bold; color: #0f172a; margin-top: 2px;">
                            {{ $complaint->productType?->name ?? 'Industrial Machinery' }}
                        </div>
                    </td>
                    <td style="width: 50%; vertical-align: top;">
                        <span style="font-size: 9px; color: #64748b; font-weight: bold; text-transform: uppercase;">Service Request Category:</span>
                        <div style="font-size: 12px; font-weight: bold; color: #0284c7; margin-top: 2px;">
                            {{ $complaint->serviceType?->name ?? 'Maintenance Service' }}
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Issue Reported by Customer -->
    <div class="section-box">
        <div class="section-header">2. Problem / Defect Reported by Customer</div>
        <div class="section-body" style="background: #f8fafc; font-style: italic; color: #334155;">
            "{{ $complaint->issue_description }}"
        </div>
    </div>

    <!-- Service Resolution & Work Performed -->
    <div class="section-box">
        <div class="section-header">3. Engineering Diagnosis & Resolution Notes</div>
        <div class="section-body">
            @if($complaint->resolution_notes)
                <p style="margin: 0; line-height: 1.6; color: #0f172a;">
                    {{ $complaint->resolution_notes }}
                </p>
            @else
                <p style="margin: 0; color: #94a3b8; font-style: italic;">
                    Work is currently underway by technician. Diagnostic findings and replaced spares will be updated upon completion.
                </p>
            @endif

            @if($complaint->resolved_at)
                <div style="margin-top: 8px; font-size: 10px; color: #166534; font-weight: bold;">
                    ✓ Resolved & Tested on: {{ $complaint->resolved_at->format('d M Y, h:i A') }}
                </div>
            @endif
        </div>
    </div>

    <!-- Commercials / Bill Linked (If generated) -->
    @if($complaint->bill)
    <div class="section-box">
        <div class="section-header">4. Linked Billing & Payment Summary</div>
        <div class="section-body">
            <table style="width: 100%;">
                <tr>
                    <td>
                        Invoice Ref: <strong>#{{ $complaint->bill->bill_number }}</strong>
                        @if($complaint->bill->gst_bill_number) (GST: {{ $complaint->bill->gst_bill_number }}) @endif
                    </td>
                    <td>Payment Mode: <strong style="text-transform: uppercase;">{{ $complaint->bill->bill_type }}</strong></td>
                    <td style="text-align: right; font-size: 13px; font-weight: 900; color: #0369a1;">
                        Total: ₹{{ number_format($complaint->bill->amount, 2) }}
                    </td>
                </tr>
            </table>
        </div>
    </div>
    @endif

    <!-- Dual Signatures -->
    <table class="signatures">
        <tr>
            <td class="sig-box" style="float: left;">
                <strong>Client Representative Signature</strong><br>
                <span>Machine inspected, work approved & satisfied</span>
            </td>
            <td style="width: 10%;"></td>
            <td class="sig-box" style="float: right;">
                <strong>Bhumi Technocare Engineer Signature</strong><br>
                <span>Certified by: {{ $complaint->engineer?->name ?? 'Field Technician' }}</span>
            </td>
        </tr>
    </table>

    <div class="footer-note">
        Bhumi Technocare FSM System • This field job sheet serves as official documentation for equipment maintenance.<br>
        For service guarantee and questions, please reach out to service@bhumitechnocare.com.
    </div>

</body>
</html>
