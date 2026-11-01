<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice_{{ $bill->bill_number }}</title>
    <style>
        @page {
            margin: 20mm 15mm;
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
            margin-bottom: 16px;
        }
        .brand-logo {
            font-size: 22px;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -0.5px;
            text-transform: uppercase;
        }
        .brand-sub {
            font-size: 10px;
            color: #64748b;
            font-weight: bold;
            margin-top: 2px;
        }
        .invoice-title {
            text-align: right;
            font-size: 18px;
            font-weight: 800;
            color: #0369a1;
            text-transform: uppercase;
        }
        .invoice-no {
            text-align: right;
            font-size: 13px;
            font-weight: bold;
            color: #0f172a;
            font-family: monospace;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .meta-col {
            width: 50%;
            vertical-align: top;
            padding: 10px 14px;
            background: #f8fafc;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
        .meta-title {
            font-size: 9px;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .meta-name {
            font-size: 13px;
            font-weight: 800;
            color: #0f172a;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 20px;
        }
        .items-table th {
            background: #0f172a;
            color: #ffffff;
            font-weight: 800;
            font-size: 10px;
            text-transform: uppercase;
            padding: 8px 10px;
            text-align: left;
        }
        .items-table td {
            padding: 10px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 11px;
        }
        .items-table tr:nth-child(even) td {
            background: #f8fafc;
        }
        .total-box {
            width: 260px;
            float: right;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 10px 14px;
            background: #f8fafc;
            margin-bottom: 20px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
            font-size: 11px;
        }
        .grand-total {
            border-top: 2px solid #0f172a;
            padding-top: 6px;
            margin-top: 6px;
            font-size: 15px;
            font-weight: 900;
            color: #0f172a;
        }
        .stamp {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
        }
        .stamp-paid {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }
        .stamp-credit {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }
        .signatures {
            width: 100%;
            margin-top: 45px;
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
            margin-top: 30px;
            padding-top: 12px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 9px;
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
                <div class="brand-sub">Machinery Maintenance & Field Service Operations</div>
                <div style="font-size: 9.5px; color: #475569; margin-top: 4px;">
                    Surat, Gujarat, India • Phone: +91 99999 99999<br>
                    GSTIN: 24AAFCB1234F1Z5 • support@bhumitechnocare.com
                </div>
            </td>
            <td style="vertical-align: top; text-align: right;">
                <div class="invoice-title">
                    {{ $bill->bill_type === 'bill' ? 'TAX INVOICE' : ($bill->bill_type === 'credit' ? 'CREDIT BILL' : 'CASH RECEIPT') }}
                </div>
                <div class="invoice-no">{{ $bill->bill_number }}</div>
                <div style="font-size: 10px; color: #64748b; margin-top: 4px;">
                    Date: <strong>{{ $bill->created_at->format('d M Y, h:i A') }}</strong>
                </div>
                <div style="margin-top: 6px;">
                    @if($bill->payment_status === 'collected')
                        <span class="stamp stamp-paid">✓ PAID & COLLECTED</span>
                    @else
                        <span class="stamp stamp-credit">⏳ PAYMENT PENDING</span>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <!-- Billing Details Grid -->
    <table class="meta-table">
        <tr>
            <td class="meta-col" style="margin-right: 10px;">
                <div class="meta-title">Billed To (Customer / Client):</div>
                <div class="meta-name">{{ $bill->customer?->name ?? 'Walk-in Client' }}</div>
                <div style="font-weight: bold; color: #334155;">{{ $bill->organization?->name }}</div>
                @if($bill->customer?->address)
                    <div style="color: #64748b; margin-top: 2px;">{{ $bill->customer->address }}</div>
                @endif
                @if($bill->customer?->contact_no)
                    <div style="color: #0369a1; font-weight: bold; margin-top: 3px;">Ph: {{ $bill->customer->contact_no }}</div>
                @endif
                @if($bill->organization?->gst_no)
                    <div style="color: #475569; font-size: 10px; margin-top: 2px;">Client GSTIN: <strong>{{ $bill->organization->gst_no }}</strong></div>
                @endif
            </td>
            <td style="width: 2%;"></td>
            <td class="meta-col">
                <div class="meta-title">Service Details & Attending Engineer:</div>
                <div class="meta-name">{{ $bill->engineer?->name ?? 'Field Technician' }}</div>
                <div style="color: #64748b;">{{ $bill->engineer?->post ?? 'Senior Service Engineer' }}</div>
                @if($bill->engineer?->phone_no)
                    <div style="color: #475569; margin-top: 2px;">Contact: {{ $bill->engineer->phone_no }}</div>
                @endif
                @if($bill->complaint)
                    <div style="margin-top: 6px; padding-top: 4px; border-top: 1px dashed #cbd5e1;">
                        <span style="font-size: 9px; font-weight: bold; color: #64748b; text-transform: uppercase;">Linked Work Order:</span>
                        <div style="font-weight: bold; color: #0369a1;">#{{ $bill->complaint->ticket_number }}</div>
                        <div style="font-size: 10px; color: #475569;">{{ $bill->complaint->productType?->name }} • {{ $bill->complaint->serviceType?->name }}</div>
                    </div>
                @endif
            </td>
        </tr>
    </table>

    <!-- Line Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 8%; text-align: center;">#</th>
                <th style="width: 52%;">Service / Machinery Description</th>
                <th style="width: 20%; text-align: center;">Payment Mode</th>
                <th style="width: 20%; text-align: right;">Amount (INR)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: center; font-weight: bold;">1</td>
                <td>
                    <div style="font-weight: 800; color: #0f172a;">
                        {{ $bill->complaint?->productType?->name ?? 'Industrial Machinery Maintenance' }}
                    </div>
                    <div style="color: #64748b; font-size: 10px; margin-top: 2px;">
                        Service Type: {{ $bill->complaint?->serviceType?->name ?? 'On-Site Machine Service' }}
                    </div>
                    @if($bill->remarks)
                        <div style="margin-top: 4px; color: #334155; font-style: italic; font-size: 10px;">
                            "{{ $bill->remarks }}"
                        </div>
                    @endif
                </td>
                <td style="text-align: center; text-transform: uppercase; font-weight: bold; color: #475569;">
                    {{ $bill->bill_type }}
                </td>
                <td style="text-align: right; font-weight: 800; font-size: 12px; color: #0f172a;">
                    ₹{{ number_format($bill->amount, 2) }}
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Totals Summary Box -->
    <div style="width: 100%; overflow: hidden;">
        <table style="width: 300px; float: right; border-collapse: collapse; border: 1px solid #cbd5e1; border-radius: 8px; background: #f8fafc;">
            <tr>
                <td style="padding: 6px 12px; font-size: 11px; color: #64748b;">Subtotal:</td>
                <td style="padding: 6px 12px; font-size: 11px; font-weight: bold; text-align: right;">₹{{ number_format($bill->amount, 2) }}</td>
            </tr>
            @if($bill->gst_bill_number)
            <tr>
                <td style="padding: 4px 12px; font-size: 10px; color: #64748b;">GST Invoice Ref:</td>
                <td style="padding: 4px 12px; font-size: 10px; font-weight: bold; text-align: right; font-family: monospace;">{{ $bill->gst_bill_number }}</td>
            </tr>
            @endif
            <tr style="border-top: 2px solid #0f172a; background: #f1f5f9;">
                <td style="padding: 8px 12px; font-size: 13px; font-weight: 900; color: #0f172a;">Net Total:</td>
                <td style="padding: 8px 12px; font-size: 15px; font-weight: 900; text-align: right; color: #0369a1;">₹{{ number_format($bill->amount, 2) }}</td>
            </tr>
        </table>
    </div>

    <!-- Signatures -->
    <table class="signatures">
        <tr>
            <td class="sig-box" style="float: left;">
                <strong>Customer / Client Authorized Signature</strong><br>
                <span>Received services & machine handed in working condition</span>
            </td>
            <td style="width: 10%;"></td>
            <td class="sig-box" style="float: right;">
                <strong>For Bhumi Technocare</strong><br>
                <span>Authorized Field Engineer Signature</span>
            </td>
        </tr>
    </table>

    <!-- Footer Note -->
    <div class="footer-note">
        This is a computer generated invoice issued by Bhumi Technocare Enterprise Service ERP.<br>
        For inquiries regarding this invoice, contact support@bhumitechnocare.com or call +91 99999 99999.
    </div>

</body>
</html>
