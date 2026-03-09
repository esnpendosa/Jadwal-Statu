@extends('layouts.app')

@section('title', __('return.title'))

@section('content')
<div class="container-fluid px-4 py-4">
    {{-- Page Header --}}
    <div class="row align-items-center mb-5 fade-in">
        <div class="col-md-6">
            <h1 class="h2 fw-black text-dark text-uppercase tracking-tighter mb-1">
                Daftar Pengembalian
                <span class="d-block h6 fw-bold text-muted text-uppercase tracking-[0.2em] mt-2">Pelacakan Siklus Hidup Logistik</span>
            </h1>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="{{ route('borrow.index', ['status' => 'borrowed']) }}" class="btn btn-primary btn-lg rounded-4 fw-bold shadow-sm px-4">
                <i class="bi bi-plus-circle-fill me-2"></i>PROSES PENGEMBALIAN BARU
            </a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-4 mb-5">
        @php
            $stats = [
                ['label' => 'Total Kembali', 'value' => $returns->total(), 'icon' => 'bi-arrow-return-left', 'color' => 'primary'],
                ['label' => 'Kondisi Baik', 'value' => $returns->where('condition_status', 'good')->count(), 'icon' => 'bi-check-circle', 'color' => 'success'],
                ['label' => 'Masalah Perangkat', 'value' => $returns->whereIn('condition_status', ['damaged', 'lost'])->count(), 'icon' => 'bi-exclamation-triangle', 'color' => 'danger'],
                ['label' => 'Terlambat Kembali', 'value' => $returns->where('is_late', true)->count(), 'icon' => 'bi-clock-history', 'color' => 'warning'],
            ];
        @endphp
        @foreach($stats as $stat)
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="small fw-bold text-muted text-uppercase tracking-widest mb-1">{{ $stat['label'] }}</p>
                            <h2 class="fw-black mb-0">{{ $stat['value'] }}</h2>
                        </div>
                        <div class="bg-{{ $stat['color'] }} bg-opacity-10 text-{{ $stat['color'] }} rounded-4 p-3 font-size-24">
                            <i class="bi {{ $stat['icon'] }}"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Filters --}}
    <div class="card border-0 shadow-sm rounded-4 mb-5">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('return.index') }}" class="row g-3 align-items-end">
                <div class="col-lg-4 col-md-6">
                    <label class="form-label font-label text-muted text-uppercase small fw-bold tracking-wider">Cari Transaksi</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0 ps-0 shadow-none" placeholder="Cari berdasarkan kode atau nama aset...">
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="form-label font-label text-muted text-uppercase small fw-bold tracking-wider">Kondisi Aset</label>
                    <select name="condition" class="form-select shadow-none">
                        <option value="">Semua Kondisi</option>
                        @foreach(['good' => 'Baik', 'poor' => 'Rusak Ringan', 'damaged' => 'Rusak Berat', 'lost' => 'Hilang'] as $key => $val)
                        <option value="{{ $key }}" {{ request('condition') === $key ? 'selected' : '' }}>{{ $val }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="form-label font-label text-muted text-uppercase small fw-bold tracking-wider">Rentang Waktu</label>
                    <select name="timeframe" class="form-select shadow-none">
                        <option value="">Semua Histori</option>
                        <option value="today" {{ request('timeframe') === 'today' ? 'selected' : '' }}>Diproses Hari Ini</option>
                        <option value="week" {{ request('timeframe') === 'week' ? 'selected' : '' }}>7 Hari Terakhir</option>
                        <option value="month" {{ request('timeframe') === 'month' ? 'selected' : '' }}>Bulan Ini</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6 d-grid">
                    <button type="submit" class="btn btn-dark fw-bold rounded-3">TERAPKAN</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Results Table --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light border-bottom">
                    <tr>
                        <th class="ps-4 py-3 text-uppercase extra-small fw-black text-muted tracking-widest">Detail Aset</th>
                        <th class="py-3 text-uppercase extra-small fw-black text-muted tracking-widest">Proyek / Konteks</th>
                        <th class="py-3 text-center text-uppercase extra-small fw-black text-muted tracking-widest">Jumlah</th>
                        <th class="py-3 text-uppercase extra-small fw-black text-muted tracking-widest">Hasil Kondisi</th>
                        <th class="py-3 text-uppercase extra-small fw-black text-muted tracking-widest">Log Waktu</th>
                        <th class="pe-4 py-3 text-end text-uppercase extra-small fw-black text-muted tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($returns as $return)
                    <tr>
                        <td class="ps-4 py-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2 font-size-20">
                                    <i class="bi bi-box-seam"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-dark mb-0">{{ $return->borrowItem?->inventory?->name ?? 'UNKNOWN ASSET' }}</h6>
                                    <span class="font-monospace extra-small text-muted">{{ $return->code }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="fw-bold text-dark small">{{ $return->borrowTransaction?->project?->name ?? 'Internal Context' }}</span>
                                <span class="extra-small text-muted">PIC: {{ $return->returnedBy?->name ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="badge bg-white border text-dark fw-black px-3 py-2 rounded-pill shadow-sm">
                                {{ $return->quantity_returned }} <span class="fw-normal text-muted ms-1">{{ strtoupper($return->borrowItem?->inventory?->unit ?? 'PCS') }}</span>
                            </div>
                        </td>
                        <td>
                            @php
                                $statusMap = [
                                    'good' => ['bg' => 'success', 'icon' => 'check-circle-fill'],
                                    'fair' => ['bg' => 'info', 'icon' => 'dash-circle-fill'],
                                    'damaged' => ['bg' => 'warning', 'icon' => 'exclamation-triangle-fill'],
                                    'lost' => ['bg' => 'danger', 'icon' => 'x-circle-fill'],
                                ];
                                $c = $statusMap[$return->condition_status] ?? ['bg' => 'secondary', 'icon' => 'question-circle-fill'];
                            @endphp
                            <div class="d-inline-flex align-items-center gap-2 px-3 py-1 bg-{{ $c['bg'] }} bg-opacity-10 text-{{ $c['bg'] }} border border-{{ $c['bg'] }} border-opacity-25 rounded-pill extra-small fw-black">
                                <i class="bi bi-{{ $c['icon'] }}"></i>
                                {{ strtoupper($return->condition_status) }}
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="extra-small fw-black {{ $return->is_late ? 'text-danger' : 'text-success' }}">
                                    @if($return->is_late)
                                        <i class="bi bi-calendar-x-fill me-1"></i> TERLAMBAT
                                    @else
                                        <i class="bi bi-calendar-check-fill me-1"></i> TEPAT WAKTU
                                    @endif
                                </span>
                                <span class="extra-small text-muted mt-1">{{ $return->created_at->format('d M Y • H:i') }}</span>
                            </div>
                        </td>
                        <td class="pe-4 text-end">
                            <a href="{{ route('return.show', $return) }}" class="btn btn-sm btn-light border rounded-circle shadow-none p-2" hover-bg-primary>
                                <i class="bi bi-chevron-right text-primary"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-5 text-center">
                            <i class="bi bi-inbox display-4 text-muted opacity-25 d-block mb-3"></i>
                            <h6 class="text-muted fw-bold text-uppercase tracking-widest">No matching return records found.</h6>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($returns->hasPages())
        <div class="card-footer bg-white border-top-0 px-4 py-4">
            {{ $returns->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>

<style>
    .fw-black { font-weight: 900; }
    .extra-small { font-size: 0.65rem; }
    .font-size-24 { font-size: 1.5rem; }
    .font-size-20 { font-size: 1.25rem; }
    .tracking-tighter { letter-spacing: -0.05em; }
    
    .fade-in {
        animation: fadeIn 0.8s ease-out forwards;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .table-hover tbody tr:hover {
        background-color: #f8fafc;
    }

    [hover-bg-primary]:hover {
        background-color: var(--bs-primary) !important;
        border-color: var(--bs-primary) !important;
    }
    [hover-bg-primary]:hover i {
        color: white !important;
    }
</style>
@endsection
