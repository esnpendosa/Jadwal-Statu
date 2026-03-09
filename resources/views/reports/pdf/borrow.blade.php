<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Equipment Circulation Report</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #4e73df; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #4e73df; font-size: 20px; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background-color: #f8f9fc; color: #4e73df; text-align: left; padding: 8px; border-bottom: 1px solid #e3e6f0; text-transform: uppercase; font-size: 9px; }
        td { padding: 8px; border-bottom: 1px solid #e3e6f0; }
        .badge { padding: 2px 6px; border-radius: 4px; font-size: 8px; font-weight: bold; text-transform: uppercase; }
        .bg-pending { background-color: #fff3cd; color: #856404; }
        .bg-borrowed { background-color: #cfe2ff; color: #084298; }
        .bg-returned { background-color: #d1e7dd; color: #0f5132; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 9px; color: #aaa; border-top: 1px solid #eee; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Equipment Circulation Report</h1>
        <p>Generated on: {{ $date }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Trx Code</th>
                <th>Date</th>
                <th>Project</th>
                <th>Borrower</th>
                <th>Items</th>
                <th>Expected Return</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($borrows as $borrow)
            <tr>
                <td><strong>#{{ $borrow->code }}</strong></td>
                <td>{{ $borrow->created_at->format('d/m/Y') }}</td>
                <td>{{ $borrow->project->name }}</td>
                <td>{{ $borrow->requester->name }}</td>
                <td>
                    @foreach($borrow->items as $item)
                        {{ $item->inventory->name }} ({{ $item->quantity }})<br>
                    @endforeach
                </td>
                <td>{{ $borrow->expected_return_date->format('d/m/Y') }}</td>
                <td>
                    <span class="badge bg-{{ $borrow->status }}">{{ $borrow->status }}</span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">Smart Inventory - Historical Circulation Record</div>
</body>
</html>
