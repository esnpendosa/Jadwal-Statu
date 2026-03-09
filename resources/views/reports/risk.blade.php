@extends('layouts.app')
@section('title', 'Risk Analytics Report')
@section('page-title', 'Reporting Engine')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h2 class="h4 mb-1 fw-black text-dark tracking-tighter uppercase mb-0">
            <i class="bi bi-shield-shaded me-2 text-danger"></i>ANALISA RISIKO
        </h2>
        <p class="text-muted small mb-0">Log audit dan skor kepatuhan untuk manajemen aset</p>
    </div>
    <div class="col-md-6 text-md-end mt-3 mt-md-0">
        <div class="btn-group shadow-sm">
            <a href="{{ route('reports.export', ['type' => 'risk'] + request()->all()) }}" class="btn btn-danger fw-bold">
                <i class="bi bi-file-earmark-pdf me-2"></i>Ekspor Analisa PDF
            </a>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-xl-4">
        {{-- High Risk Users List --}}
        <div class="card border-0 shadow-sm mb-4 h-100">
            <div class="card-header bg-danger bg-opacity-10 border-0 py-3">
                <h6 class="mb-0 fw-black text-danger text-uppercase extra-small tracking-widest"><i class="bi bi-people-fill me-2"></i>Entitas Berisiko Tertinggi</h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($users as $u)
                    <div class="list-group-item p-3 d-flex align-items-center justify-content-between border-0 border-bottom">
                        <div class="d-flex align-items-center gap-3">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($u->name) }}&background=e74a3b&color=fff&size=32" class="rounded-circle shadow-sm">
                            <div>
                                <div class="small fw-bold text-dark">{{ $u->name }}</div>
                                <div class="d-flex align-items-center gap-1">
                                    <span class="extra-small text-muted uppercase">{{ $u->roles->first()?->name ?? 'User' }}</span>
                                    <span class="extra-small px-1 rounded-pill 
                                        {{ $u->risk_level === 'low' ? 'bg-success bg-opacity-10 text-success' : 
                                           ($u->risk_level === 'medium' ? 'bg-warning bg-opacity-10 text-warning' : 'bg-danger bg-opacity-10 text-danger') }}">
                                        {{ strtoupper($u->risk_level) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="bg-dark bg-opacity-10 text-dark rounded-pill px-2 py-1 extra-small fw-black">
                            {{ $u->risk_score ?? 0 }} Pts
                        </div>
                    </div>
                    @empty
                    <div class="p-4 text-center text-muted small italic">Tidak ada entitas berisiko tinggi yang teridentifikasi.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-8">
        {{-- Detailed Risk Logs --}}
        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 fw-black text-dark text-uppercase extra-small tracking-widest">Audit Pelanggaran Komprehensif</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr class="extra-small text-muted text-uppercase fw-black tracking-widest">
                            <th class="ps-4">Konteks Entitas</th>
                            <th>Tipe Pelanggaran</th>
                            <th class="text-center">Tingkat Bahaya</th>
                            <th>Catatan / Bukti</th>
                            <th class="text-end pe-4">Waktu Kejadian</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($scores as $score)
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="small fw-bold text-dark">{{ $score->user->name }}</div>
                                <div class="extra-small text-muted">Project: {{ $score->project->name ?? 'N/A' }}</div>
                            </td>
                            <td>
                                <span class="badge bg-danger-subtle text-danger border border-danger extra-small px-2 py-1 rounded-pill fw-black uppercase">
                                    {{ $score->riskRule->name }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="fw-black text-danger">+{{ $score->points_added }}</span>
                            </td>
                            <td>
                                <p class="extra-small text-muted mb-0 font-italic line-clamp-1" title="{{ $score->notes }}">"{{ $score->notes }}"</p>
                            </td>
                            <td class="text-end pe-4">
                                <span class="extra-small text-muted font-monospace">{{ $score->created_at->format('d/m/y H:i') }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="py-5 text-center text-muted italic small">Catatan audit bersih. Tidak ditemukan pelanggaran.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-0 py-3">
                {{ $scores->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<style>
    .fw-black { font-weight: 900; }
    .extra-small { font-size: 0.65rem; }
    .line-clamp-1 { display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; }
</style>
@endsection
