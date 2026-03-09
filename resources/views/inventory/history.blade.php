@extends('layouts.app')
@section('title', 'History: ' . $inventory->name)
@section('page-title', __('inventory.title'))

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('inventory.show', $inventory) }}" class="btn-icon">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white uppercase tracking-tight">Stock Movement History</h2>
                <p class="text-xs text-gray-500 mt-1">{{ $inventory->name }} ({{ $inventory->code }})</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <div class="text-right">
                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Current Total</p>
                <p class="text-lg font-extrabold text-blue-600 leading-none">{{ $inventory->stock_total }} {{ $inventory->unit }}</p>
            </div>
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="table-container border-none shadow-none rounded-none">
            <table class="table">
                <thead class="bg-gray-50 dark:bg-gray-800/50">
                    <tr>
                        <th class="w-1/6">Date & Time</th>
                        <th class="w-1/6 text-center">Type</th>
                        <th class="w-1/6 text-center">Reference</th>
                        <th class="w-1/6 text-right">Quantity</th>
                        <th class="w-2/6">Notes / Remarks</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($histories as $history)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors">
                        <td class="text-xs font-medium text-gray-600 dark:text-gray-400">
                            {{ $history->created_at->translatedFormat('d F Y') }}
                            <div class="text-[10px] opacity-70">{{ $history->created_at->format('H:i') }} ({{ $history->created_at->diffForHumans() }})</div>
                        </td>
                        <td class="text-center">
                            @if($history->type === 'in')
                            <span class="badge badge-success px-2.5 py-1">Stock IN (+)</span>
                            @elseif($history->type === 'out')
                            <span class="badge badge-warning px-2.5 py-1">Stock OUT (-)</span>
                            @else
                            <span class="badge badge-primary px-2.5 py-1">Adjustment</span>
                            @endif
                        </td>
                        <td class="text-center text-xs font-bold uppercase tracking-tight text-gray-500 dark:text-gray-500">
                            {{ str_replace('_', ' ', $history->reference_type) }}
                        </td>
                        <td class="text-right font-mono font-bold {{ $history->type === 'in' ? 'text-emerald-600' : ($history->type === 'out' ? 'text-amber-600' : 'text-blue-600') }}">
                            {{ $history->type === 'in' ? '+' : ($history->type === 'out' ? '-' : '±') }}
                            {{ $history->quantity }} {{ $inventory->unit }}
                        </td>
                        <td class="max-w-xs">
                            <p class="text-xs truncate dark:text-gray-300" title="{{ $history->notes }}">{{ $history->notes }}</p>
                            @if($history->reference_id)
                            <div class="mt-1 text-[10px] text-gray-400">Ref: #{{ $history->reference_id }}</div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-16 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-200 dark:text-gray-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <p class="text-sm text-gray-400">No stock movements found.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($histories->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between">
            <span class="text-xs text-gray-400">Showing page {{ $histories->currentPage() }} of {{ $histories->lastPage() }}</span>
            <div class="pagination">
                {!! $histories->links() !!}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
