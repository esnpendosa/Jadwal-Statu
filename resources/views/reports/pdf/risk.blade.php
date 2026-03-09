<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Risk Analysis Report</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #e74a3b; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #e74a3b; font-size: 20px; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background-color: #fcf8f8; color: #e74a3b; text-align: left; padding: 8px; border-bottom: 1px solid #f5c6cb; text-transform: uppercase; font-size: 9px; }
        td { padding: 8px; border-bottom: 1px solid #eee; }
        .points { font-weight: bold; color: #e74a3b; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 9px; color: #aaa; border-top: 1px solid #eee; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Risk Analysis & Security Audit</h1>
        <p>Comprehensive risk logging as of {{ $date }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>PIC Name</th>
                <th>Project Context</th>
                <th>Violation / Rule</th>
                <th style="text-align: right;">Points</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @foreach($scores as $score)
            <tr>
                <td>{{ $score->created_at->format('d/m/Y H:i') }}</td>
                <td><strong>{{ $score->user->name }}</strong></td>
                <td>{{ $score->project->name ?? 'N/A' }}</td>
                <td>{{ $score->riskRule->name }}</td>
                <td style="text-align: right;" class="points">+{{ $score->points_added }}</td>
                <td><small>{{ $score->notes }}</small></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">Smart Inventory Compliance Engine - Confidential Audit Report</div>
</body>
</html>
