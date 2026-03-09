@extends('layouts.app')
@section('title', __('borrow.create'))
@section('page-title', __('borrow.title'))

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('borrow.index') }}" class="btn btn-light border shadow-sm btn-sm">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h2 class="h4 mb-0 fw-bold text-dark text-uppercase tracking-tight">Create Borrow Request</h2>
                    <p class="text-muted extra-small mb-0">REQUEST MULTIPLE ITEMS FOR A SINGLE PROJECT</p>
                </div>
            </div>
        </div>

        <form action="{{ route('borrow.store') }}" method="POST">
            @csrf
            
            <div class="row g-4">
                {{-- Global Settings --}}
                <div class="col-lg-12">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <div class="row g-4 align-items-end">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Target Project <span class="text-danger">*</span></label>
                                    <select name="project_id" id="project-select" class="form-select border-2 @error('project_id') is-invalid @enderror" required onchange="syncProjectDates(this)">
                                        <option value="">— Select Target Project —</option>
                                        @foreach($projects as $p)
                                        <option value="{{ $p->id }}" 
                                            data-start="{{ $p->start_date->format('Y-m-d') }}" 
                                            data-end="{{ $p->end_date->format('Y-m-d') }}"
                                            {{ (request('project_id') == $p->id || old('project_id') == $p->id) ? 'selected' : '' }}>
                                            {{ $p->name }} ({{ $p->code }})
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('project_id') <div class="invalid-feedback extra-small">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Deadline / Return Date <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-2 border-end-0"><i class="bi bi-calendar-event"></i></span>
                                        <input type="date" name="expected_return_date" id="expected-return" value="{{ old('expected_return_date', date('Y-m-d', strtotime('+7 days'))) }}" class="form-control border-2 border-start-0 ps-0" required>
                                    </div>
                                    <p class="extra-small text-muted mt-2 mb-0 fw-bold" id="project-timeline-hint">
                                        <i class="bi bi-info-circle me-1"></i> Auto-syncs with selected project end date
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Item Repeater --}}
                <div class="col-lg-12">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold text-dark text-uppercase small tracking-wider">Requested Items List</h6>
                            <button type="button" onclick="addItemRow()" class="btn btn-primary btn-sm px-3 fw-bold shadow-sm">
                                <i class="bi bi-plus-lg me-1"></i> ADD ANOTHER ITEM
                            </button>
                        </div>
                        <div class="card-body p-4">
                            <div id="items-container" class="row g-3">
                                @php $oldItems = old('items', [['inventory_id' => '', 'quantity' => 1]]); @endphp
                                @foreach($oldItems as $index => $oldItem)
                                <div class="col-12 item-row animate-fade-in">
                                    <div class="row g-2 align-items-end p-3 bg-light rounded-3 border">
                                        <div class="col-md-7">
                                            <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">Equipment / Item</label>
                                            <select name="items[{{ $index }}][inventory_id]" class="form-select border-0 shadow-sm inventory-select" required onchange="updateRowStock(this)">
                                                <option value="">Select Equipment</option>
                                                @foreach($inventories as $inv)
                                                <option value="{{ $inv->id }}" {{ $oldItem['inventory_id'] == $inv->id ? 'selected' : '' }} data-stock="{{ $inv->stock_available }}" data-unit="{{ $inv->unit }}">
                                                    {{ $inv->name }} ({{ $inv->stock_available }} {{ $inv->unit }} available)
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">Quantity</label>
                                            <div class="input-group shadow-sm rounded-3 overflow-hidden">
                                                <input type="number" name="items[{{ $index }}][quantity]" min="1" value="{{ $oldItem['quantity'] }}" class="form-control border-0" required>
                                                <span class="input-group-text border-0 bg-white unit-label extra-small fw-bold text-primary min-w-50 text-center">{{ $oldItem['inventory_id'] ? $inventories->find($oldItem['inventory_id'])->unit : '—' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-1 text-end">
                                            <button type="button" onclick="removeItemRow(this)" class="btn btn-link text-danger p-0 pb-1 mb-1">
                                                <i class="bi bi-trash3 fs-5"></i>
                                            </button>
                                        </div>
                                        @error("items.{$index}.quantity")
                                        <div class="col-12">
                                            <p class="text-danger extra-small fw-bold mb-0 mt-2">{{ $message }}</p>
                                        </div>
                                        @enderror
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Purpose / Delivery Instructions</label>
                            <textarea name="notes" rows="3" class="form-control" placeholder="Describe why this equipment is needed or provide any specific notes for the warehouse team...">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center border-top pt-4">
                        <a href="{{ route('borrow.index') }}" class="btn btn-light border px-4 fw-bold text-muted">CANCEL</a>
                        <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow-sm">
                            <i class="bi bi-send me-2"></i> SUBMIT BORROW REQUEST
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<template id="row-template">
    <div class="col-12 item-row animate-fade-in mt-3">
        <div class="row g-2 align-items-end p-3 bg-light rounded-3 border">
            <div class="col-md-7">
                <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">Equipment / Item</label>
                <select name="items[REPLACE_INDEX][inventory_id]" class="form-select border-0 shadow-sm inventory-select" required onchange="updateRowStock(this)">
                    <option value="">Select Equipment</option>
                    @foreach($inventories as $inv)
                    <option value="{{ $inv->id }}" data-stock="{{ $inv->stock_available }}" data-unit="{{ $inv->unit }}">
                        {{ $inv->name }} ({{ $inv->stock_available }} {{ $inv->unit }} available)
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">Quantity</label>
                <div class="input-group shadow-sm rounded-3 overflow-hidden">
                    <input type="number" name="items[REPLACE_INDEX][quantity]" min="1" value="1" class="form-control border-0" required>
                    <span class="input-group-text border-0 bg-white unit-label extra-small fw-bold text-primary min-w-50 text-center">—</span>
                </div>
            </div>
            <div class="col-md-1 text-end">
                <button type="button" onclick="removeItemRow(this)" class="btn btn-link text-danger p-0 pb-1 mb-1">
                    <i class="bi bi-trash3 fs-5"></i>
                </button>
            </div>
        </div>
    </div>
</template>

<style>
    .extra-small { font-size: 0.65rem; }
    .min-w-50 { min-width: 50px; }
    @keyframes fade-in { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fade-in 0.3s ease-out forwards; }
</style>
@endsection

@push('scripts')
<script>
let rowIndex = {{ count($oldItems) }};

function addItemRow() {
    const container = document.getElementById('items-container');
    const template = document.getElementById('row-template').innerHTML;
    const newRow = template.replace(/REPLACE_INDEX/g, rowIndex++);
    
    const div = document.createElement('div');
    div.innerHTML = newRow;
    container.appendChild(div.firstElementChild);
}

function removeItemRow(btn) {
    const rows = document.querySelectorAll('.item-row');
    if (rows.length > 1) {
        btn.closest('.item-row').remove();
    } else {
        alert('At least one item is required.');
    }
}

function updateRowStock(select) {
    const selected = select.options[select.selectedIndex];
    const row = select.closest('.item-row');
    const unitLabel = row.querySelector('.unit-label');
    
    if (selected.value) {
        unitLabel.innerText = selected.getAttribute('data-unit');
    } else {
        unitLabel.innerText = '—';
    }
}

function syncProjectDates(select) {
    const selected = select.options[select.selectedIndex];
    const returnInput = document.getElementById('expected-return');
    const hint = document.getElementById('project-timeline-hint');
    
    if (selected.value) {
        const endDate = selected.getAttribute('data-end');
        returnInput.value = endDate;
        hint.innerHTML = `<i class="bi bi-check2-circle text-success me-1"></i> Syncing with Project End: <strong>${endDate}</strong>`;
    } else {
        hint.innerText = "Auto-sync with project end date";
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const projectSelect = document.getElementById('project-select');
    if (projectSelect.value) syncProjectDates(projectSelect);
});
</script>
@endpush
