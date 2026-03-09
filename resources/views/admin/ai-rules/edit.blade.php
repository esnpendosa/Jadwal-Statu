@extends('layouts.app')
@section('title', 'Edit AI Rule')
@section('page-title', 'Admin Panel')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="card">
        <div class="card-header"><h3 class="font-bold uppercase tracking-tight">Modify AI Intelligence Logic: {{ $rule->name }}</h3><a href="{{ route('admin.ai-rules.index') }}" class="btn-secondary btn-sm">{{ __('common.back') }}</a></div>
        <form action="{{ route('admin.ai-rules.update', $rule) }}" method="POST" class="card-body">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8 pb-8 border-b border-gray-100 dark:border-gray-800">
                <div class="space-y-4">
                    <div class="form-group">
                        <label class="form-label uppercase font-black tracking-widest text-[9px] text-gray-400">Rule Logic Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $rule->name) }}" class="form-input font-bold" required>
                        @error('name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label uppercase font-black tracking-widest text-[9px] text-gray-400">Condition Linker Key <span class="text-red-500">*</span></label>
                        <input type="text" name="rule_key" value="{{ old('rule_key', $rule->rule_key) }}" class="form-input font-mono" required>
                        @error('rule_key') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="form-group">
                        <label class="form-label uppercase font-black tracking-widest text-[9px] text-gray-400">Severity Level <span class="text-red-500">*</span></label>
                        <select name="severity" class="form-select font-bold" required>
                            <option value="info" {{ $rule->severity === 'info' ? 'selected' : '' }}>Info (Blue)</option>
                            <option value="warning" {{ $rule->severity === 'warning' ? 'selected' : '' }}>Warning (Amber)</option>
                            <option value="critical" {{ $rule->severity === 'critical' ? 'selected' : '' }}>Critical (Red)</option>
                        </select>
                    </div>
                    <div class="form-group pt-4">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $rule->is_active) ? 'checked' : '' }} class="w-4 h-4 text-primary-600 rounded border-gray-300 focus:ring-primary-500">
                            <span class="text-xs font-bold text-gray-700 dark:text-gray-300 group-hover:text-primary-600 transition-colors uppercase tracking-tight">Stay Active in Intelligence Layer</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <h4 class="text-[10px] font-black uppercase text-gray-400 tracking-widest">Update AI Content Tiers</h4>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="form-group">
                        <label class="form-label flex items-center gap-2 text-[10px] text-gray-500 font-bold uppercase"><span class="bg-gray-100 px-1.5 py-0.5 rounded font-black text-gray-700">ID</span> Indonesia</label>
                        <textarea name="suggestion_id" rows="4" class="form-textarea text-xs italic tracking-tighter" required>{{ old('suggestion_id', $rule->suggestion_id) }}</textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label flex items-center gap-2 text-[10px] text-gray-500 font-bold uppercase"><span class="bg-gray-100 px-1.5 py-0.5 rounded font-black text-gray-700">EN</span> Global English</label>
                        <textarea name="suggestion_en" rows="4" class="form-textarea text-xs italic tracking-tighter" required>{{ old('suggestion_en', $rule->suggestion_en) }}</textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label flex items-center gap-2 text-[10px] text-gray-500 font-bold uppercase"><span class="bg-gray-100 px-1.5 py-0.5 rounded font-black text-gray-700">ZH</span> Mandarin (Simplified)</label>
                        <textarea name="suggestion_zh" rows="4" class="form-textarea text-xs italic tracking-tighter">{{ old('suggestion_zh', $rule->suggestion_zh) }}</textarea>
                    </div>
                </div>
                <div class="alert alert-warning py-2">
                    <p class="text-[9px] uppercase tracking-tighter font-bold opacity-80">Keyword injection Check: <code>{inventory}</code>, <code>{project}</code>, <code>{user}</code> are active hooks.</p>
                </div>
            </div>

            <div class="mt-8 flex items-center justify-end gap-3 border-t border-gray-100 dark:border-gray-800 pt-6">
                <button type="reset" class="btn-secondary">{{ __('common.cancel') }}</button>
                <button type="submit" class="btn-primary px-8 font-bold uppercase tracking-widest text-xs">UPDATE SYSTEM INTELLIGENCE</button>
            </div>
        </form>
    </div>
</div>
@endsection
