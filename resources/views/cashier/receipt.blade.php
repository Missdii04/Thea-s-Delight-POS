@php 
    use Carbon\Carbon; 
    
    // --- Data Setup ---
    $receiptDetails = $receiptDetails ?? []; 
    $grandTotal = $receiptDetails['totals']['finalTotal'] ?? 0;
    $change = $receiptDetails['change'] ?? 0.00;
    $received = $receiptDetails['received'] ?? $grandTotal;
    $cashierName = Auth::user()->name ?? 'N/A';
    
    // --- Base64 Encode Logo for PDF (MANDATORY for reliable image rendering in Dompdf) ---
    $logoSrc = null;
    try {
        // Assuming your logo is named 'logoPOS.png' and is in the storage/app/public directory
        $logoPath = storage_path('app/public/logoPOS.png'); 
        if (file_exists($logoPath)) {
            $logoData = base64_encode(file_get_contents($logoPath));
            // Check file type and set the appropriate header
            $logoType = pathinfo($logoPath, PATHINFO_EXTENSION);
            $logoSrc = 'data:image/' . $logoType . ';base64,' . $logoData;
        }
    } catch (\Exception $e) {
        // Log error if file reading fails
    }
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Receipt #{{ $receiptDetails['receiptNo'] ?? 'N/A' }}</title>
    <style>
        /* BASE STYLES & SIZING */
        body {
            font-family: sans-serif;
            font-size: 12px; /* Increased font size for readability */
            margin: 0;
            padding: 0;
            width: 302px; 
        }
        .container {
            width: 300px;
            margin: 0 auto;
            padding: 5px;
            /* ⭐️ NEW: Double Line Magenta Border ⭐️ */
            border: 3px double #d946ef; 
            box-sizing: border-box; 
        }
        /* THEME COLORS */
        .color-primary { color: #d946ef; /* Magenta */ }
        .bg-secondary { background-color: #fce7f3; /* Light Pink */ }
        .text-bold { font-weight: bold; }

        /* HEADER & INFO */
        .header-section {
            text-align: center;
            padding-bottom: 5px;
            margin-bottom: 5px;
        }
        .header-section h1 {
            font-size: 18px; 
            margin: 0;
            padding: 0;
            font-weight: bold;
        }
        .info-block {
            padding: 5px 0;
            font-size: 11px;
            text-align: left;
        }
        .info-block p {
            margin: 2px 0;
        }

        /* DASHED LINES */
        .dashed-line {
            border-bottom: 1px dashed #ccc;
            margin: 5px 0;
        }

        /* TABLES */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }
        .item-header th {
            padding: 5px 0;
            font-size: 11px;
            font-weight: bold;
            background-color: #fce7f3; /* Light Pink Background for Header */
            border-bottom: 1px solid #000;
        }
        table td {
            padding: 4px 0;
            border-bottom: 1px dashed #eee; /* Dashed line for item separation */
            font-size: 11px;
        }
        .right { text-align: right; }
        .center { text-align: center; }

        /* TOTALS */
        .totals-box {
            border-top: 1px dashed #ccc;
            padding-top: 5px;
            font-size: 12px;
        }
        .total-row {
            font-size: 15px; 
            font-weight: bold;
            color: #d946ef; 
            padding: 5px 0;
            border-top: 1px dashed #ccc; 
        }
        .change-row {
            color: #ef4444; 
            font-size: 13px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        
        <div class="header-section">
            @if ($logoSrc)
                <img src="{{ $logoSrc }}" style="width: 50px; height: auto; margin-bottom: 5px;" alt="Thea's Delight Logo"> 
            @endif
            <h1 class="color-primary">Thea's Delight</h1>
        </div>
        
        <div class="info-block center" style="padding-top: 0;">
            <p>123 Cake Lane, City, Philippines</p>
            <p>Contact: (02) 8555-CAKE</p>
        </div>

        <div class="dashed-line"></div>


        <div class="info-block">
            <p><strong>Receipt No:</strong> {{ $receiptDetails['receiptNo'] ?? 'N/A' }}</p>
            <p><strong>Date/Time:</strong> {{ Carbon::now()->format('Y-m-d h:i:s A') }}</p>
            <p><strong>Cashier:</strong> {{ $cashierName }}</p>
            <p><strong>Method:</strong> {{ $receiptDetails['payment_method'] ?? 'N/A' }}</p>
        </div>

        <div class="dashed-line"></div>

        <table>
            <thead>
                <tr class="item-header">
                    <th class="center" style="width: 50%;">ITEM DESCRIPTION</th>
                    <th class="center" style="width: 10%;">QTY</th>
                    <th class="right" style="width: 20%;">UNIT PRICE</th> 
                    <th class="right" style="width: 20%;">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($receiptDetails['items'] as $item)
                    @php
                        $itemGrossTotal = $item['price'] * $item['quantity'];
                    @endphp
                    <tr>
                        <td class="center">{{ $item['name'] }}</td>
                        <td class="center">{{ $item['quantity'] }}</td>
                        <td class="right">P{{ number_format($item['price'], 2) }}</td>
                        <td class="right">P{{ number_format($itemGrossTotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="dashed-line"></div>

        <div class="totals-box">
            <div style="display: flex; justify-content: space-between;">
                <span>Subtotal (Net of VAT):</span>
                <span class="right">P{{ number_format($receiptDetails['totals']['subTotal'] ?? 0, 2) }}</span>
            </div>
            
            <div style="display: flex; justify-content: space-between; color: #ef4444;">
                <span>VAT Exempted:</span>
                <span class="right">-P{{ number_format($receiptDetails['totals']['vatExemptAmount'] ?? 0, 2) }}</span>
            </div>
            
            <div style="display: flex; justify-content: space-between; color: #ef4444;">
                <span>20% Discount:</span>
                <span class="right">-P{{ number_format($receiptDetails['totals']['discountAmount'] ?? 0, 2) }}</span>
            </div>
            
            <div style="display: flex; justify-content: space-between;">
                <span>VAT (12%):</span>
                <span class="right">P{{ number_format($receiptDetails['totals']['vatTax'] ?? 0, 2) }}</span>
            </div>
            
            <div class="total-row" style="display: flex; justify-content: space-between;">
                <span>TOTAL DUE:</span>
                <span class="right">P{{ number_format($grandTotal, 2) }}</span>
            </div>

            <div class="line"></div>

            <div style="display: flex; justify-content: space-between; margin-top: 5px;">
                <span class="text-bold">Amount Paid ({{ $receiptDetails['payment_method'] ?? 'N/A' }}):</span>
                <span class="right text-bold">P{{ number_format($received, 2) }}</span>
            </div>
            
            <div class="change-row" style="display: flex; justify-content: space-between; color: #000;">
                <span>Change:</span>
                <span class="right">P{{ number_format($change, 2) }}</span>
            </div>
        </div>

        <div class="center" style="margin-top: 10px; padding-bottom: 5px;">
            <div class="dashed-line"></div>
            <p style="font-size: 11px; margin-bottom: 3px; font-weight: bold;">*** THANK YOU FOR YOUR PURCHASE ***</p>
            @if (($receiptDetails['totals']['vatExemptAmount'] ?? 0) > 0)
                <p style="font-weight: bold; font-size: 11px;">VAT EXEMPT SALE</p>
            @endif
        </div>
    </div>
</body>
</html>