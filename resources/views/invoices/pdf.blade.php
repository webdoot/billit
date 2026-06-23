<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice - {{ $invoice->invoice_no }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            font-size: 14px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            border: 1px solid #eee;
        }
        table {
            width: 100%;
            line-height: inherit;
            text-align: left;
            border-collapse: collapse;
        }
        table td {
            padding: 8px;
            vertical-align: top;
        }
        .header-table td {
            padding-bottom: 20px;
        }
        .title {
            font-size: 28px;
            line-height: 28px;
            font-weight: bold;
            color: #6366f1;
        }
        .meta-title {
            text-align: right;
            font-weight: bold;
        }
        .meta-value {
            text-align: right;
        }
        .details-table {
            margin-bottom: 30px;
            border-bottom: 2px solid #6366f1;
            padding-bottom: 20px;
        }
        .items-header th {
            background: #f8fafc;
            border-bottom: 2px solid #cbd5e1;
            font-weight: bold;
            text-align: left;
            padding: 10px 8px;
            font-size: 12px;
            text-transform: uppercase;
        }
        .item td {
            border-bottom: 1px solid #f1f5f9;
            padding: 12px 8px;
        }
        .total-row td {
            padding: 6px 8px;
            text-align: right;
        }
        .grand-total {
            font-size: 18px;
            font-weight: bold;
            color: #6366f1;
            border-top: 2px solid #6366f1;
            padding-top: 10px !important;
        }
        .notes-section {
            margin-top: 40px;
            font-size: 12px;
            color: #64748b;
        }
        .badge {
            background-color: #6366f1;
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <!-- Header -->
        <table class="header-table">
            <tr>
                <td>
                    <span class="title">BILLIT</span><br>
                    <small style="color: #64748b;">Billing & Renewal Solutions</small>
                </td>
                <td style="text-align: right;">
                    <span class="badge">{{ $invoice->status }}</span><br><br>
                    <strong>Invoice No:</strong> {{ $invoice->invoice_no }}<br>
                    <strong>Date:</strong> {{ $invoice->invoice_date->format('Y-m-d') }}<br>
                    <strong>Due Date:</strong> {{ $invoice->due_date->format('Y-m-d') }}
                </td>
            </tr>
        </table>

        <!-- Details -->
        <table class="details-table">
            <tr>
                <td style="width: 50%;">
                    <strong style="color: #6366f1;">Billed To:</strong><br>
                    <strong>{{ $invoice->customer->company_name }}</strong><br>
                    Attn: {{ $invoice->customer->contact_person }}<br>
                    {{ $invoice->customer->address }}<br>
                    {{ $invoice->customer->city }}{{ $invoice->customer->state ? ', ' . $invoice->customer->state : '' }} {{ $invoice->customer->pin_code }}<br>
                    Email: {{ $invoice->customer->email }} | Mob: {{ $invoice->customer->mobile }}
                </td>
                <td style="width: 50%; text-align: right;">
                    <strong style="color: #6366f1;">From:</strong><br>
                    <strong>Billit Technologies Pvt Ltd</strong><br>
                    101, Business Towers, Bandra East<br>
                    Mumbai, Maharashtra, 400051<br>
                    GSTIN: 27AAAAA0000A1Z5<br>
                    Email: billing@billit.com
                </td>
            </tr>
        </table>

        <!-- Line Items -->
        <table>
            <thead>
                <tr class="items-header">
                    <th>Description</th>
                    <th style="width: 60px; text-align: center;">Qty</th>
                    <th style="width: 120px; text-align: right;">Rate (₹)</th>
                    <th style="width: 120px; text-align: right;">Amount (₹)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->invoiceItems as $item)
                    <tr class="item">
                        <td>
                            {{ $item->description }}
                        </td>
                        <td style="text-align: center;">{{ $item->qty }}</td>
                        <td style="text-align: right;">{{ number_format($item->rate, 2) }}</td>
                        <td style="text-align: right; font-weight: bold;">{{ number_format($item->amount, 2) }}</td>
                    </tr>
                @endforeach
                
                <!-- Totals -->
                <tr class="total-row">
                    <td colspan="2"></td>
                    <td style="color: #64748b;">Subtotal:</td>
                    <td><strong>₹{{ number_format($invoice->subtotal, 2) }}</strong></td>
                </tr>
                @if($invoice->discount > 0)
                <tr class="total-row">
                    <td colspan="2"></td>
                    <td style="color: #ef4444;">Discount:</td>
                    <td style="color: #ef4444;"><strong>-₹{{ number_format($invoice->discount, 2) }}</strong></td>
                </tr>
                @endif
                <tr class="total-row">
                    <td colspan="2"></td>
                    <td style="color: #64748b;">Tax (GST):</td>
                    <td><strong>₹{{ number_format($invoice->tax, 2) }}</strong></td>
                </tr>
                <tr class="total-row">
                    <td colspan="2"></td>
                    <td class="grand-total">Total:</td>
                    <td class="grand-total">₹{{ number_format($invoice->total, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td colspan="2"></td>
                    <td style="font-weight: bold; color: #ef4444;">Outstanding Balance:</td>
                    <td style="font-weight: bold; color: #ef4444;">₹{{ number_format($invoice->balance, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Notes -->
        @if($invoice->notes)
        <div class="notes-section">
            <strong>Notes / Instructions:</strong>
            <p style="margin-top: 5px; font-style: italic;">
                {!! nl2br(e($invoice->notes)) !!}
            </p>
        </div>
        @endif
        
        <div style="text-align: center; margin-top: 60px; font-size: 11px; color: #94a3b8; border-top: 1px solid #f1f5f9; padding-top: 20px;">
            Thank you for your business! This is a system-generated invoice statement.
        </div>
    </div>
</body>
</html>
