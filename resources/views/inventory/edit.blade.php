@extends('layouts.app')
@section('title', __('inventory.edit'))
@section('page-title', __('inventory.edit'))

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('inventory.show', $inventory) }}" class="btn btn-light border btn-sm shadow-sm">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h2 class="h4 mb-0 fw-bold text-dark text-uppercase tracking-tight">{{ __('inventory.edit') }}</h2>
                    <p class="text-muted extra-small mb-0 text-uppercase">Asset ID: <code class="fw-bold">{{ $inventory->id }}</code></p>
                </div>
            </div>
        </div>

        <form action="{{ route('inventory.update', $inventory) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row g-4">
                {{-- Left Side: Main Data --}}
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 fw-bold text-dark text-uppercase small tracking-wider">Basic Information</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">{{ __('inventory.name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="name" value="{{ old('name', $inventory->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                                @error('name') <div class="invalid-feedback extra-small">{{ $message }}</div> @enderror
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase">{{ __('inventory.category') }} <span class="text-danger">*</span></label>
                                    <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                        @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_id', $inventory->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase">{{ __('inventory.condition') }} <span class="text-danger">*</span></label>
                                    <select name="condition" class="form-select" required>
                                        @foreach(['good' => __('inventory.conditions.good'), 'fair' => __('inventory.conditions.fair'), 'poor' => __('inventory.conditions.poor'), 'damaged' => __('inventory.conditions.damaged')] as $val => $label)
                                        <option value="{{ $val }}" {{ old('condition', $inventory->condition) == $val ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">{{ __('inventory.location') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-geo-alt"></i></span>
                                    <input type="text" name="location" value="{{ old('location', $inventory->location) }}" class="form-control border-start-0 ps-0" placeholder="e.g. Warehouse A, Shelf 4">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">{{ __('inventory.purchase_date') }}</label>
                                <input type="date" name="purchase_date" value="{{ old('purchase_date', $inventory->purchase_date?->format('Y-m-d')) }}" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 fw-bold text-dark text-uppercase small tracking-wider">Description & Notes</h6>
                        </div>
                        <div class="card-body p-4">
                            <textarea name="description" rows="4" class="form-control" placeholder="Detailed technical specifications or usage notes...">{{ old('description', $inventory->description) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Right Side: Stock & Image --}}
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm mb-4 bg-primary text-white overflow-hidden">
                        <div class="card-body p-4 position-relative">
                            <i class="bi bi-box-seam position-absolute text-white opacity-10" style="font-size: 5rem; bottom: -10px; right: -10px;"></i>
                            <h6 class="extra-small fw-bold text-uppercase tracking-widest opacity-75 mb-3">Live Stock Control</h6>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label extra-small fw-black text-white text-uppercase">Total Qty</label>
                                    <input type="number" name="stock_total" value="{{ old('stock_total', $inventory->stock_total) }}" min="{{ $inventory->stock_borrowed }}" class="form-control bg-white bg-opacity-25 border-0 text-white fw-bold shadow-none" required>
                                    <small class="extra-small opacity-75 mt-1 d-block">Min: {{ $inventory->stock_borrowed }} out</small>
                                </div>
                                <div class="col-6">
                                    <label class="form-label extra-small fw-black text-white text-uppercase">Unit Type</label>
                                    <input type="text" name="unit" value="{{ old('unit', $inventory->unit) }}" class="form-control bg-white bg-opacity-25 border-0 text-white fw-bold shadow-none" placeholder="e.g. PCS, Units" required>
                                </div>
                                <div class="col-12 mt-3">
                                    <label class="form-label extra-small fw-black text-white text-uppercase">Min. Threshold</label>
                                    <input type="number" name="stock_minimum" value="{{ old('stock_minimum', $inventory->stock_minimum) }}" min="0" class="form-control bg-white bg-opacity-25 border-0 text-white fw-bold shadow-none">
                                    <small class="extra-small opacity-75 mt-1 d-block">Low stock alert trigger</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 fw-bold text-dark text-uppercase small tracking-wider">Visual Documentation</h6>
                        </div>
                        <div class="card-body p-4">
                            @if($inventory->image)
                            <div class="position-relative mb-3 group rounded overflow-hidden border">
                                <img id="current-img" src="{{ asset('storage/' . $inventory->image) }}" class="img-fluid w-100 object-fit-cover" style="height: 180px;">
                                <div class="position-absolute bottom-0 w-100 p-2 bg-dark bg-opacity-75 d-flex justify-content-between align-items-center">
                                    <span class="extra-small fw-bold text-white text-uppercase">Current Image</span>
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" name="remove_image" value="1" id="remove-image-checkbox" onchange="toggleRemoveImage(this)">
                                        <label class="form-check-label extra-small fw-bold text-danger text-uppercase" for="remove-image-checkbox">Remove</label>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <div id="drop-zone" class="border-2 border-dashed rounded-4 p-4 text-center cursor-pointer transition-all hover-bg-light" style="min-height: 120px;" onclick="document.getElementById('image-input').click()" ondragover="handleDragOver(event)" ondragleave="handleDragLeave(event)" ondrop="handleDrop(event)">
                                <div id="preview-container" class="d-none mb-2">
                                    <img id="preview-img" src="" class="rounded-3 shadow-sm border w-100 object-fit-cover" style="height: 120px;">
                                </div>
                                <div id="upload-placeholder">
                                    <i class="bi bi-cloud-upload fs-2 text-primary opacity-50 d-block mb-1"></i>
                                    <span class="small fw-bold text-muted d-block">{{ $inventory->image ? __('inventory.replace_image') : __('inventory.drag_drop_image') }}</span>
                                    <span class="extra-small text-muted">{{ __('inventory.image_hint') }}</span>
                                </div>
                                <input type="file" id="image-input" name="image" class="d-none" accept="image/*" onchange="previewImage(this)">
                            </div>
                            <button type="button" id="reset-image-btn" class="btn btn-link btn-sm text-danger extra-small fw-bold p-0 mt-2 d-none" onclick="resetImageSelection()"><i class="bi bi-x-circle me-1"></i> CANCEL NEW SELECTION</button>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary py-3 fw-bold shadow-sm">
                            <i class="bi bi-check-circle me-2"></i> UPDATE INVENTORY
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    .extra-small { font-size: 0.65rem; }
    .fw-black { font-weight: 900; }
    .hover-bg-light:hover { background-color: #f8f9fc; border-color: #4e73df !important; }
</style>
@endsection

@push('scripts')
<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-img').src = e.target.result;
            document.getElementById('preview-container').classList.remove('d-none');
            document.getElementById('upload-placeholder').classList.add('d-none');
            document.getElementById('reset-image-btn').classList.remove('d-none');
            const removeChk = document.getElementById('remove-image-checkbox');
            if (removeChk) removeChk.checked = false;
        };
        reader.readAsDataURL(file);
    }
}

function resetImageSelection() {
    document.getElementById('image-input').value = '';
    document.getElementById('preview-img').src = '';
    document.getElementById('preview-container').classList.add('d-none');
    document.getElementById('upload-placeholder').classList.remove('d-none');
    document.getElementById('reset-image-btn').classList.add('d-none');
}

function toggleRemoveImage(checkbox) {
    const dropZone = document.getElementById('drop-zone');
    if (checkbox.checked) {
        dropZone.style.opacity = '0.4';
        dropZone.style.pointerEvents = 'none';
        resetImageSelection();
    } else {
        dropZone.style.opacity = '1';
        dropZone.style.pointerEvents = 'auto';
    }
}

function handleDragOver(e) { e.preventDefault(); e.currentTarget.classList.add('border-primary'); }
function handleDragLeave(e) { e.preventDefault(); e.currentTarget.classList.remove('border-primary'); }
function handleDrop(e) {
    e.preventDefault();
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        const input = document.getElementById('image-input');
        input.files = files;
        previewImage(input);
    }
    handleDragLeave(e);
}
</script>
@endpush
