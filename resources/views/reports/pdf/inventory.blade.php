<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Inventory Report</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            font-size: 12px;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #4e73df;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            color: #4e73df;
            text-transform: uppercase;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-grid {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-grid td {
            padding: 5px 0;
        }
        .label {
            font-weight: bold;
            color: #555;
            width: 120px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table.data-table th {
            background-color: #f8f9fc;
            color: #4e73df;
            text-align: left;
            padding: 10px;
            border-bottom: 1px solid #e3e6f0;
            text-transform: uppercase;
            font-size: 10px;
        }
        table.data-table td {
            padding: 10px;
            border-bottom: 1px solid #e3e6f0;
        }
        .badge {
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-success { background-color: #d1e7dd; color: #0f5132; }
        .badge-warning { background-color: #fff3cd; color: #664d03; }
        .badge-danger { background-color: #f8d7da; color: #842029; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #aaa;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
        .summary-box {
            background-color: #f8f9fc;
            padding: 15px;
            border-radius: 5px;
            margin-top: 30px;
        }
        .summary-title {
            font-weight: bold;
            color: #4e73df;
            margin-bottom: 10px;
            display: block;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Inventory Report</h1>
        <p>Smart Inventory Management System</p>
    </div>

    <div class="info-section">
        <table class="info-grid">
            <tr>
                <td class="label">Date Generated:</td>
                <td>{{ $date }}</td>
                <td class="label">Category:</td>
                <td>{{ $filters['category'] }}</td>
            </tr>
            <tr>
                <td class="label">Report Type:</td>
                <td>Active Inventory</td>
                <td class="label">Condition:</td>
                <td>{{ strtoupper($filters['condition']) }}</td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>Item Details</th>
                <th>Category</th>
                <th class="text-center">Stock</th>
                <th class="text-center">Available</th>
                <th class="text-center">In Use</th>
                <th>Condition</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inventories as $item)
            @php
                $inUse = $item->stock_total - $item->stock_available;
                $badgeClass = match($item->condition) {
                    'good' => 'badge-success',
                    'maintenance', 'poor' => 'badge-warning',
                    default => 'badge-danger'
                };
            @endphp
            <tr>
                <td>
                    <div style="font-weight: bold;">{{ $item->name }}</div>
                    <div style="font-size: 9px; color: #888;">#{{ $item->code }}</div>
                </td>
                <td>{{ $item->category->name }}</td>
                <td class="text-center">{{ $item->stock_total }}</td>
                <td class="text-center">{{ $item->stock_available }}</td>
                <td class="text-center">{{ $inUse }}</td>
                <td>
                    <span class="badge {{ $badgeClass }}">{{ $item->condition }}</span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary-box">
        <span class="summary-title">Executive Summary</span>
        <table style="width: 100%;">
            <tr>
                <td>Total Asset Types: <strong>{{ $inventories->count() }}</strong></td>
                <td>Total Items: <strong>{{ $inventories->sum('stock_total') }}</strong></td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Generated by Smart Inventory Management System - {{ date('Y') }}
    </div>
</body>
</html>
