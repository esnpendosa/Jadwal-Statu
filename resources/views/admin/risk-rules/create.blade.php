@extends('layouts.app')
@section('title', 'Add Risk Rule')
@section('page-title', 'Admin Panel')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="card">
        <div class="card-header"><h3 class="font-bold">Define Risk Calculation Rule</h3><a href="{{ route('admin.risk-rules.index') }}" class="btn-secondary btn-sm">{{ __('common.back') }}</a></div>
        <form action="{{ route('admin.risk-rules.store') }}" method="POST" class="card-body">
            @csrf
            <div class="space-y-4">
                <div class="form-group">
                    <label class="form-label">Rule Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Late Return Penalty" class="form-input" required>
                    @error('name') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">System Key (e.g. LATE_RETURN) <span class="text-red-500">*</span></label>
                    <input type="text" name="rule_key" value="{{ old('rule_key') }}" placeholder="LATE_RETURN" class="form-input font-mono" required>
                    @error('rule_key') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Impact on Risk Score <span class="text-red-500">*</span></label>
                    <input type="number" name="point_impact" value="{{ old('point_impact', 0) }}" placeholder="e.g. 10 or -5" class="form-input" required>
                    <p class="text-[9px] text-gray-400 mt-1 uppercase font-bold">Positive values increase risk, Negative values decrease it.</p>
                </div>
                <div class="form-group">
                    <label class="form-label">Condition Logic Description</label>
                    <textarea name="description" rows="3" class="form-textarea" placeholder="Explain the business logic for this rule...">{{ old('description') }}</textarea>
                </div>
                <div class="form-group pt-2">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }} class="w-4 h-4 text-primary-600 rounded border-gray-300 focus:ring-primary-500">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-primary-600 transition-colors uppercase tracking-tight">Activate Rule Immediately</span>
                    </label>
                </div>
            </div>
            <div class="mt-8 flex items-center justify-end gap-3 border-t border-gray-100 dark:border-gray-800 pt-6">
                <button type="reset" class="btn-secondary">{{ __('common.cancel') }}</button>
                <button type="submit" class="btn-primary px-8 font-bold">SAVE RULE DEFINITION</button>
            </div>
        </form>
    </div>
</div>
@endsection
