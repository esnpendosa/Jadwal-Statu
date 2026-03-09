<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Notifikasi Peminjaman</title>
<style>
  body { font-family: 'Inter', Arial, sans-serif; background: #f3f4f6; margin: 0; padding: 40px 20px; color: #1f2937; }
  .container { max-width: 620px; margin: 0 auto; background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,.08); }
  .header-approved { background: linear-gradient(135deg, #1c64f2, #4e73df); padding: 32px; text-align: center; }
  .header-created  { background: linear-gradient(135deg, #0e9f6e, #14a085); padding: 32px; text-align: center; }
  .header-reminder { background: linear-gradient(135deg, #d97706, #f59e0b); padding: 32px; text-align: center; }
  .header-overdue  { background: linear-gradient(135deg, #c81e1e, #e02424); padding: 32px; text-align: center; }
  .header h1 { color: white; font-size: 22px; margin: 0 0 4px; font-weight: 700; letter-spacing: -0.5px; }
  .header .badge { display: inline-block; background: rgba(255,255,255,0.25); color: white; padding: 4px 14px; border-radius: 999px; font-size: 12px; font-weight: 600; margin-top: 8px; }
  .body { padding: 32px; }
  .greeting { font-size: 16px; font-weight: 600; color: #1f2937; margin-bottom: 8px; }
  .message { font-size: 14px; color: #4b5563; line-height: 1.7; margin-bottom: 24px; }
  .info-card { background: #f8fafc; border-radius: 12px; padding: 20px; margin: 20px 0; border-left: 4px solid #4e73df; }
  .info-card.overdue { border-left-color: #e02424; background: #fef2f2; }
  .info-card.reminder { border-left-color: #f59e0b; background: #fffbeb; }
  .info-row { display: flex; justify-content: space-between; margin: 10px 0; font-size: 14px; border-bottom: 1px solid #f1f5f9; padding-bottom: 8px; }
  .info-row:last-child { border-bottom: none; padding-bottom: 0; }
  .info-row .label { color: #6b7280; }
  .info-row .value { font-weight: 700; color: #1f2937; text-align: right; max-width: 60%; }
  .item-list { margin: 0; padding: 0; list-style: none; }
  .item-list li { padding: 8px 0; font-size: 13px; color: #374151; display: flex; justify-content: space-between; border-bottom: 1px solid #f1f5f9; }
  .item-list li:last-child { border-bottom: none; }
  .btn { display: inline-block; background: #4e73df; color: white !important; padding: 14px 30px; border-radius: 10px; text-decoration: none; font-weight: 700; font-size: 14px; margin-top: 16px; }
  .btn-red { background: #e02424; }
  .footer { background: #f9fafb; padding: 20px; text-align: center; font-size: 12px; color: #9ca3af; border-top: 1px solid #e5e7eb; }
</style>
</head>
<body>
<div class="container">
  <div class="header header-{{ $type }}">
    <h1>
      @if($type === 'created') ✅ Permintaan Diterima
      @elseif($type === 'approved') 🎉 Peminjaman Disetujui!
      @elseif($type === 'reminder') ⏰ Pengingat Pengembalian
      @else 🚨 Status Overdue
      @endif
    </h1>
    <div class="badge">{{ strtoupper($borrow->code) }}</div>
  </div>

  <div class="body">
    <p class="greeting">Halo, {{ $borrow->requester?->name ?? 'Peminjam' }}!</p>
    <p class="message">
      @if($type === 'created')
        Permintaan peminjaman Anda telah berhasil dikirim dan sedang menunggu persetujuan dari Admin Gudang.
      @elseif($type === 'approved')
        Kabar baik! Permintaan peminjaman Anda telah <strong>disetujui</strong>. Harap kembalikan barang tepat waktu sesuai batas yang ditentukan.
      @elseif($type === 'reminder')
        Ini adalah pengingat bahwa batas waktu pengembalian barang Anda adalah <strong>besok</strong>. Harap segera proses pengembalian.
      @else
        Barang yang Anda pinjam telah <strong>melewati batas waktu pengembalian</strong>. Harap segera kembalikan dan hubungi Admin Gudang.
      @endif
    </p>

    <div class="info-card {{ $type === 'overdue' ? 'overdue' : ($type === 'reminder' ? 'reminder' : '') }}">
      <div class="info-row">
        <span class="label">Kode Transaksi</span>
        <span class="value">{{ $borrow->code }}</span>
      </div>
      <div class="info-row">
        <span class="label">Proyek</span>
        <span class="value">{{ $borrow->project?->name ?? '-' }}</span>
      </div>
      <div class="info-row">
        <span class="label">Tanggal Pinjam</span>
        <span class="value">{{ $borrow->borrow_date ? \Carbon\Carbon::parse($borrow->borrow_date)->translatedFormat('d F Y') : '-' }}</span>
      </div>
      <div class="info-row">
        <span class="label">Batas Pengembalian</span>
        <span class="value" style="color: {{ $type === 'overdue' ? '#e02424' : ($type === 'reminder' ? '#d97706' : 'inherit') }};">
          {{ $borrow->expected_return_date->translatedFormat('d F Y') }}
        </span>
      </div>
    </div>

    <p style="font-size: 13px; font-weight: 700; color: #374151; margin: 20px 0 8px;">Item yang Dipinjam:</p>
    <ul class="item-list">
      @foreach($borrow->items as $item)
      <li>
        <span>{{ $item->inventory?->name ?? '-' }}</span>
        <span style="font-weight: 700; color: #4e73df;">{{ $item->quantity }} {{ $item->inventory?->unit ?? 'pcs' }}</span>
      </li>
      @endforeach
    </ul>

    <center>
      <a href="{{ config('app.url') }}/borrow/{{ $borrow->id }}" class="btn {{ $type === 'overdue' ? 'btn-red' : '' }}">
        Lihat Detail Peminjaman
      </a>
    </center>
  </div>

  <div class="footer">
    <p>© {{ date('Y') }} {{ config('app.name', 'Inventory System') }} — Sistem Manajemen Inventaris</p>
    <p>Email ini dikirim secara otomatis. Jangan balas email ini.</p>
  </div>
</div>
</body>
</html>
