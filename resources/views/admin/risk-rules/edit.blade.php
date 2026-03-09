@extends('layouts.app')
@section('title', 'Edit Risk Rule')
@section('page-title', 'Admin Panel')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="card">
        <div class="card-header"><h3 class="font-bold">Modify Rule Definition: {{ $rule->name }}</h3><a href="{{ route('admin.risk-rules.index') }}" class="btn-secondary btn-sm">{{ __('common.back') }}</a></div>
        <form action="{{ route('admin.risk-rules.update', $rule) }}" method="POST" class="card-body">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div class="form-group">
                    <label class="form-label">Rule Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $rule->name) }}" class="form-input" required>
                    @error('name') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">System Key (e.g. LATE_RETURN) <span class="text-red-500">*</span></label>
                    <input type="text" name="rule_key" value="{{ old('rule_key', $rule->rule_key) }}" class="form-input font-mono" required>
                    @error('rule_key') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Impact on Risk Score <span class="text-red-500">*</span></label>
                    <input type="number" name="point_impact" value="{{ old('point_impact', $rule->point_impact) }}" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Condition Logic Description</label>
                    <textarea name="description" rows="3" class="form-textarea">{{ old('description', $rule->description) }}</textarea>
                </div>
                <div class="form-group pt-2">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $rule->is_active) ? 'checked' : '' }} class="w-4 h-4 text-primary-600 rounded border-gray-300 focus:ring-primary-500">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-primary-600 transition-colors uppercase tracking-tight">Active & Applying to Live Environment</span>
                    </label>
                </div>
            </div>
            <div class="mt-8 flex items-center justify-end gap-3 border-t border-gray-100 dark:border-gray-800 pt-6">
                <button type="reset" class="btn-secondary">{{ __('common.cancel') }}</button>
                <button type="submit" class="btn-primary px-8 font-bold text-xs uppercase tracking-widest">UPDATE RULE LOGIC</button>
            </div>
        </form>
    </div>
</div>
@endsection
