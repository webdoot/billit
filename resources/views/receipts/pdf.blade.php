<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt - {{ $receipt->receipt_no }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            font-size: 14px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .receipt-box {
            max-width: 600px;
            margin: auto;
            padding: 30px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            background-color: #fff;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            color: #10b981;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table td {
            padding: 8px;
            vertical-align: top;
        }
        .header-section {
            border-bottom: 2px solid #cbd5e1;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .amount-card {
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
            padding: 20px;
            text-align: center;
            border-radius: 6px;
            margin-bottom: 25px;
        }
        .amount-val {
            font-size: 26px;
            font-weight: bold;
            color: #166534;
        }
        .label-cell {
            font-weight: bold;
            color: #64748b;
            width: 180px;
        }
        .detail-row td {
            border-bottom: 1px solid #f1f5f9;
            padding: 12px 8px;
        }
    </style>
</head>
<body>
    <div class="receipt-box">
        <!-- Header -->
        <table class="header-section">
            <tr>
                <td>
                    <span class="title">Official Receipt</span><br>
                    <small style="color: #64748b;">Billit Technologies</small>
                </td>
                <td style="text-align: right;">
                    <strong>Receipt No:</strong> {{ $receipt->receipt_no }}<br>
                    <strong>Date:</strong> {{ $receipt->receipt_date->format('Y-m-d') }}
                </td>
            </tr>
        </table>

        <!-- Amount Collected Card -->
        <div class="amount-card">
            <span style="color: #166534; font-size: 12px; text-transform: uppercase; font-weight: bold; display: block; margin-bottom: 5px;">Amount Collected</span>
            <span class="amount-val">₹{{ number_format($receipt->amount, 2) }}</span>
        </div>

        <!-- Receipt Details Table -->
        <table>
            <tr class="detail-row">
                <td class="label-cell">Received From:</td>
                <td>
                    <strong>{{ $receipt->payment->customer->company_name }}</strong><br>
                    Attn: {{ $receipt->payment->customer->contact_person }}
                </td>
            </tr>
            <tr class="detail-row">
                <td class="label-cell">Payment Method:</td>
                <td>
                    <strong>{{ $receipt->payment->payment_method }}</strong>
                </td>
            </tr>
            @if($receipt->payment->transaction_no)
            <tr class="detail-row">
                <td class="label-cell">Transaction Ref No:</td>
                <td>
                    <code>{{ $receipt->payment->transaction_no }}</code>
                </td>
            </tr>
            @endif
            <tr class="detail-row">
                <td class="label-cell">In Settlement Of:</td>
                <td>
                    Invoice Reference: <strong>{{ $receipt->payment->invoice->invoice_no }}</strong>
                </td>
            </tr>
            @if($receipt->payment->remarks)
            <tr class="detail-row">
                <td class="label-cell">Remarks / Note:</td>
                <td>
                    {{ $receipt->payment->remarks }}
                </td>
            </tr>
            @endif
        </table>

        <!-- Signatures / Footer -->
        <table style="margin-top: 50px;">
            <tr>
                <td style="width: 50%;">
                    <small style="color: #94a3b8; display: block; margin-top: 40px; font-style: italic;">
                        This is a computer-generated receipt.<br>No signature is required.
                    </small>
                </td>
                <td style="width: 50%; text-align: right;">
                    <div style="border-top: 1px solid #cbd5e1; width: 180px; display: inline-block; margin-top: 40px; text-align: center;">
                        <span style="font-size: 11px; font-weight: bold; color: #64748b;">Authorized Signatory</span>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
