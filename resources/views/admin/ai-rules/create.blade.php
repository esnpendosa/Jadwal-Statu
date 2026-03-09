@extends('layouts.app')
@section('title', 'Add AI Rule')
@section('page-title', 'Admin Panel')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="card">
        <div class="card-header"><h3 class="font-bold uppercase tracking-tight">Define AI Intelligence Logic</h3><a href="{{ route('admin.ai-rules.index') }}" class="btn-secondary btn-sm">{{ __('common.back') }}</a></div>
        <form action="{{ route('admin.ai-rules.store') }}" method="POST" class="card-body">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8 pb-8 border-b border-gray-100 dark:border-gray-800">
                <div class="space-y-4">
                    <div class="form-group">
                        <label class="form-label uppercase font-black tracking-widest text-[9px] text-gray-400">Rule Logic Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Critical Stock Shortage" class="form-input font-bold" required>
                        @error('name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label uppercase font-black tracking-widest text-[9px] text-gray-400">Condition Linker Key <span class="text-red-500">*</span></label>
                        <input type="text" name="rule_key" value="{{ old('rule_key') }}" placeholder="STOCK_CRITICAL" class="form-input font-mono" required>
                        @error('rule_key') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="form-group">
                        <label class="form-label uppercase font-black tracking-widest text-[9px] text-gray-400">Severity Level <span class="text-red-500">*</span></label>
                        <select name="severity" class="form-select font-bold" required>
                            <option value="info">Info (Blue)</option>
                            <option value="warning">Warning (Amber)</option>
                            <option value="critical">Critical (Red)</option>
                        </select>
                    </div>
                    <div class="form-group pt-4">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }} class="w-4 h-4 text-primary-600 rounded border-gray-300 focus:ring-primary-500">
                            <span class="text-xs font-bold text-gray-700 dark:text-gray-300 group-hover:text-primary-600 transition-colors uppercase tracking-tight">Enable Intelligent Trigger</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <h4 class="text-[10px] font-black uppercase text-gray-400 tracking-widest">Multilingual AI Suggestions</h4>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="form-group">
                        <label class="form-label flex items-center gap-2 text-[10px]"><span class="bg-gray-200 px-1 rounded font-black">ID</span> Bahasa Indonesia</label>
                        <textarea name="suggestion_id" rows="4" class="form-textarea text-xs italic" placeholder="Stok item {inventory} sedang menipis..." required>{{ old('suggestion_id') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label flex items-center gap-2 text-[10px]"><span class="bg-gray-200 px-1 rounded font-black">EN</span> English (Global)</label>
                        <textarea name="suggestion_en" rows="4" class="form-textarea text-xs italic" placeholder="Stock for {inventory} is running low..." required>{{ old('suggestion_en') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label flex items-center gap-2 text-[10px]"><span class="bg-gray-200 px-1 rounded font-black">ZH</span> Mandarin (CN)</label>
                        <textarea name="suggestion_zh" rows="4" class="form-textarea text-xs italic" placeholder="库存不足 {inventory}...">{{ old('suggestion_zh') }}</textarea>
                    </div>
                </div>
                <div class="alert alert-info py-2">
                    <p class="text-[9px] uppercase tracking-tighter opacity-80">Keyword injection: <code>{inventory}</code>, <code>{project}</code>, <code>{user}</code> are available placeholders for dynamic logic.</p>
                </div>
            </div>

            <div class="mt-8 flex items-center justify-end gap-3 border-t border-gray-100 dark:border-gray-800 pt-6">
                <button type="reset" class="btn-secondary">{{ __('common.cancel') }}</button>
                <button type="submit" class="btn-primary px-8 font-bold uppercase tracking-widest text-xs">ARCHITECT AI LOGIC</button>
            </div>
        </form>
    </div>
</div>
@endsection
