@extends('layouts.app')
@section('title', 'Edit Role')
@section('page-title', 'Admin Panel')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="card">
        <div class="card-header border-none pb-0 pt-6">
            <h3 class="text-xl font-bold uppercase tracking-tight text-gray-900 dark:text-white">Edit Role: {{ strtoupper($role->name) }}</h3>
            <a href="{{ route('admin.roles.index') }}" class="btn-secondary btn-sm">{{ __('common.back') }}</a>
        </div>
        <form action="{{ route('admin.roles.update', $role) }}" method="POST" class="card-body">
            @csrf
            @method('PUT')
            
            <div class="form-group mb-8 pb-8 border-b border-gray-100 dark:border-gray-800">
                <label class="form-label uppercase font-black tracking-widest text-[10px] text-gray-400">Role Name (Slug Format) <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $role->name) }}" class="form-input text-lg font-bold" {{ $role->name === 'super-admin' ? 'disabled' : 'required' }}>
                @if($role->name === 'super-admin')
                    <input type="hidden" name="name" value="{{ $role->name }}">
                    <p class="text-[9px] text-red-500 font-bold mt-1 uppercase">THE SUPER-ADMIN ROLE NAME CANNOT BE MODIFIED</p>
                @endif
                @error('name') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <h4 class="text-xs font-bold uppercase tracking-widest text-gray-400 leading-none">Permission Mapping Adjustment</h4>
                    <div class="flex items-center gap-4">
                        <button type="button" class="text-[9px] font-bold text-primary-600 hover:underline uppercase tracking-widest" onclick="toggleAllPerms(true)">SELECT ALL</button>
                        <button type="button" class="text-[9px] font-bold text-red-500 hover:underline uppercase tracking-widest" onclick="toggleAllPerms(false)">DESELECT ALL</button>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @php
                        $activePerms = $role->permissions->pluck('name')->toArray();
                    @endphp
                    @foreach($permissions as $group => $groupPerms)
                    <div class="card p-4 border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/10 hover:bg-white dark:hover:bg-gray-800 transition-colors">
                        <h5 class="text-[10px] font-black uppercase text-gray-900 dark:text-gray-300 tracking-widest mb-3 border-b border-gray-200 dark:border-gray-700 pb-2">{{ ucfirst($group) }} Management</h5>
                        <div class="space-y-3">
                            @foreach($groupPerms as $perm)
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="checkbox" name="permissions[]" value="{{ $perm->name }}" class="perm-check w-4 h-4 text-primary-600 rounded border-gray-300 focus:ring-primary-500" {{ in_array($perm->name, $activePerms) ? 'checked' : '' }}>
                                <span class="text-xs font-bold text-gray-700 dark:text-gray-300 group-hover:text-primary-600 transition-colors uppercase tracking-tight">{{ $perm->name }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-12 flex items-center justify-end gap-3 border-t border-gray-100 dark:border-gray-800 pt-6">
                <button type="reset" class="btn-secondary">{{ __('common.cancel') }}</button>
                <button type="submit" class="btn-primary px-8 font-bold">SAVE SECURITY UPDATE</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleAllPerms(checked) {
    document.querySelectorAll('.perm-check').forEach(el => el.checked = checked);
}
</script>
@endpush
