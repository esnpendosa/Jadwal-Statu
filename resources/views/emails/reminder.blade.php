<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Smart Inventory - Reminder</title>
<style>
body { font-family: 'Inter', Arial, sans-serif; background: #f3f4f6; margin: 0; padding: 40px 20px; color: #1f2937; }
.container { max-width: 600px; margin: 0 auto; background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,.08); }
.header { background: linear-gradient(135deg, #4f46e5, #7c3aed); padding: 32px; text-align: center; }
.header h1 { color: white; font-size: 24px; margin: 0 0 4px; font-weight: 700; }
.header p { color: rgba(255,255,255,.8); margin: 0; font-size: 14px; }
.body { padding: 32px; }
.info-card { background: #f8fafc; border-radius: 12px; padding: 20px; margin: 20px 0; border-left: 4px solid #4f46e5; }
.info-row { display: flex; justify-content: space-between; margin: 8px 0; font-size: 14px; }
.info-row .label { color: #6b7280; }
.info-row .value { font-weight: 600; color: #1f2937; }
.btn { display: inline-block; background: #4f46e5; color: white; padding: 14px 28px; border-radius: 10px; text-decoration: none; font-weight: 600; font-size: 14px; margin: 16px 0; }
.footer { background: #f9fafb; padding: 20px; text-align: center; font-size: 12px; color: #9ca3af; border-top: 1px solid #e5e7eb; }
</style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Smart Inventory</h1>
        <p>Management System</p>
    </div>
    <div class="body">
        {!! $body !!}
        <div class="info-card">
            <div class="info-row"><span class="label">Kode/Code:</span><span class="value">{{ $borrow->code }}</span></div>
            <div class="info-row"><span class="label">Item:</span><span class="value">{{ $borrow->inventory->name }}</span></div>
            <div class="info-row"><span class="label">Jumlah/Qty:</span><span class="value">{{ $borrow->quantity }} {{ $borrow->inventory->unit }}</span></div>
            <div class="info-row"><span class="label">Project:</span><span class="value">{{ $borrow->project->name }}</span></div>
            <div class="info-row"><span class="label">Deadline:</span><span class="value">{{ $borrow->expected_return_date->format('d M Y') }}</span></div>
        </div>
        <center><a href="{{ config('app.url') }}/return/create/{{ $borrow->id }}" class="btn">Proses Pengembalian / Process Return</a></center>
    </div>
    <div class="footer">
        <p>© {{ date('Y') }} Smart Inventory Management — Automated System</p>
        <p>Jangan balas email ini / Do not reply to this email.</p>
    </div>
</div>
</body>
</html>
