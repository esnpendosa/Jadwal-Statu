@extends('layouts.app')
@section('title', __('inventory.create'))
@section('page-title', __('inventory.create'))

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                <h5 class="mb-0 fw-bold text-dark">{{ __('inventory.create') }}</h5>
                <a href="{{ route('inventory.index') }}" class="btn btn-sm btn-light border">
                    <i class="bi bi-arrow-left me-1"></i> {{ __('common.back') }}
                </a>
            </div>
            
            <form action="{{ route('inventory.store') }}" method="POST" enctype="multipart/form-data" class="card-body p-4">
                @csrf
                
                <div class="row g-4">
                    {{-- Basic Information --}}
                    <div class="col-md-6 border-end">
                        <h6 class="text-uppercase text-muted small fw-bold mb-4">Basic Information</h6>
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold">{{ __('inventory.name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Hammer Drill Hilti" class="form-control @error('name') is-invalid @enderror" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">{{ __('inventory.category') }} <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                <option value="">{{ __('inventory.all_categories') }}</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">{{ __('inventory.location') }}</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-geo-alt text-muted"></i></span>
                                <input type="text" name="location" value="{{ old('location') }}" placeholder="e.g. Gudang A, Rak 4" class="form-control border-start-0 ps-0">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">{{ __('inventory.condition') }} <span class="text-danger">*</span></label>
                            <div class="d-flex gap-2 flex-wrap">
                                @foreach(['good' => 'success', 'fair' => 'info', 'poor' => 'warning', 'damaged' => 'danger'] as $val => $color)
                                <input type="radio" class="btn-check" name="condition" id="cond-{{ $val }}" value="{{ $val }}" {{ old('condition', 'good') == $val ? 'checked' : '' }} required>
                                <label class="btn btn-outline-{{ $color }} btn-sm px-3" for="cond-{{ $val }}">{{ __('inventory.conditions.' . $val) }}</label>
                                @endforeach
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">{{ __('inventory.purchase_date') }}</label>
                            <input type="date" name="purchase_date" value="{{ old('purchase_date', date('Y-m-d')) }}" class="form-control">
                        </div>
                    </div>

                    {{-- Stock & Settings --}}
                    <div class="col-md-6">
                        <h6 class="text-uppercase text-muted small fw-bold mb-4">Inventory Control</h6>
                        
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-bold">{{ __('inventory.stock') }} <span class="text-danger">*</span></label>
                                <input type="number" name="stock_total" value="{{ old('stock_total', 0) }}" min="0" class="form-control" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold">{{ __('inventory.unit') }} <span class="text-danger">*</span></label>
                                <input type="text" name="unit" value="{{ old('unit', 'Pcs') }}" placeholder="Pcs, Set, etc" class="form-control" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold">{{ __('inventory.stock_minimum') }}</label>
                            <div class="input-group mb-1">
                                <input type="number" name="stock_minimum" value="{{ old('stock_minimum', 1) }}" min="0" class="form-control">
                                <span class="input-group-text bg-light small text-muted">Threshold</span>
                            </div>
                            <p class="text-muted" style="font-size: 0.75rem"><i class="bi bi-info-circle me-1"></i> System will notify when available stock falls below this level.</p>
                        </div>

                        {{-- Image Upload --}}
                        <div class="mb-3">
                            <label class="form-label small fw-bold d-block">Item Visualization</label>
                            <div id="drop-zone" class="border border-2 border-dashed rounded-3 p-4 text-center cursor-pointer bg-light bg-opacity-50 transition-all" 
                                onclick="document.getElementById('image-input').click()"
                                ondragover="this.classList.add('border-primary', 'bg-primary', 'bg-opacity-10'); event.preventDefault();"
                                ondragleave="this.classList.remove('border-primary', 'bg-primary', 'bg-opacity-10')"
                                ondrop="handleDrop(event)">
                                
                                <div id="upload-placeholder">
                                    <i class="bi bi-cloud-arrow-up fs-2 text-muted"></i>
                                    <p class="mb-0 small fw-bold text-muted">Click or drag image Here</p>
                                    <p class="text-muted extra-small" style="font-size: 0.65rem">JPG, PNG (Max 2MB)</p>
                                </div>

                                <div id="preview-container" class="d-none">
                                    <img id="preview-img" src="" class="img-thumbnail" style="max-height: 120px">
                                    <p id="file-name-display" class="mt-2 small text-primary fw-bold mb-0"></p>
                                </div>

                                <input type="file" id="image-input" name="image" class="d-none" accept="image/*" onchange="previewImage(this)">
                            </div>
                            <button type="button" id="reset-image-btn" class="btn btn-link btn-sm text-danger text-decoration-none d-none mt-2" onclick="resetImage()">
                                <i class="bi bi-x-circle me-1"></i> Remove Image
                            </button>
                            @error('image') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-4 border-top pt-4">
                    <label class="form-label small fw-bold">{{ __('common.notes') }} / Description</label>
                    <textarea name="description" rows="3" class="form-control" placeholder="Additional details, serial numbers, etc.">{{ old('description') }}</textarea>
                </div>

                <div class="mt-5 d-flex gap-3 justify-content-end">
                    <a href="{{ route('inventory.index') }}" class="btn btn-light px-4">{{ __('common.cancel') }}</a>
                    <button type="submit" class="btn btn-primary px-5 shadow-sm">
                        <i class="bi bi-check-lg me-2"></i> {{ __('common.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .cursor-pointer { cursor: pointer; }
    .extra-small { font-size: 0.7rem; }
</style>
@endsection

@push('scripts')
<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        if (file.size > 2 * 1024 * 1024) {
            alert('Image too large! Maximum 2MB allowed.');
            input.value = '';
            return;
        }
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-img').src = e.target.result;
            document.getElementById('preview-container').classList.remove('d-none');
            document.getElementById('upload-placeholder').classList.add('d-none');
            document.getElementById('file-name-display').textContent = file.name;
            document.getElementById('reset-image-btn').classList.remove('d-none');
        };
        reader.readAsDataURL(file);
    }
}

function resetImage() {
    document.getElementById('image-input').value = '';
    document.getElementById('preview-img').src = '';
    document.getElementById('preview-container').classList.add('d-none');
    document.getElementById('upload-placeholder').classList.remove('d-none');
    document.getElementById('reset-image-btn').classList.add('d-none');
}

function handleDrop(e) {
    e.preventDefault();
    const dz = document.getElementById('drop-zone');
    dz.classList.remove('border-primary', 'bg-primary', 'bg-opacity-10');
    
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        const input = document.getElementById('image-input');
        input.files = files;
        previewImage(input);
    }
}
</script>
@endpush
