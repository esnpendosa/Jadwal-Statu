@component('mail::message')
# Pengingat Pengembalian Barang

Halo {{ $user->name }},

Kami ingin mengingatkan bahwa ada barang yang Anda pinjam untuk proyek **{{ $project->name }}** yang harus segera dikembalikan.

**Detail Peminjaman:**
- Kode Transaksi: {{ $transaction->code }}
- Tanggal Pinjam: {{ $transaction->created_at->format('d M Y') }}
- **Batas Waktu: {{ $transaction->expected_return_date->format('d M Y') }}**

**Daftar Barang:**
@foreach($transaction->items as $item)
- {{ $item->inventory->name }} ({{ $item->quantity }} {{ $item->inventory->unit }})
@endforeach

Mohon segera lakukan pengembalian ke gudang sesuai jadwal yang ditentukan untuk menghindari poin risiko pada akun Anda.

@component('mail::button', ['url' => route('borrows.show', $transaction->id)])
Lihat Detail Peminjaman
@endcomponent

Terima kasih,<br>
{{ config('app.name') }}
@endcomponent
