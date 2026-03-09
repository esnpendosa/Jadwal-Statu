@extends('layouts.app')
@section('title', __('general.notifications'))
@section('page-title', __('general.notifications'))

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white uppercase tracking-tight">{{ __('general.notifications') }}</h2>
            <p class="text-xs text-gray-500 mt-1">Stay updated with system activities</p>
        </div>
        @if(auth()->user()->unreadNotifications->count() > 0)
        <form action="{{ route('notifications.mark-all-read') }}" method="POST">
            @csrf
            <button type="submit" class="text-xs font-bold text-primary-600 hover:underline uppercase tracking-widest">Mark All as Read</button>
        </form>
        @endif
    </div>

    <div class="card overflow-hidden">
        <div class="divide-y divide-gray-100 dark:divide-gray-800">
            @forelse(auth()->user()->notifications()->paginate(20) as $notification)
            <div class="px-6 py-6 hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors flex items-start gap-4 {{ $notification->unread() ? 'bg-primary-50/20 dark:bg-primary-900/5' : '' }}">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 {{ $notification->unread() ? 'bg-primary-100 text-primary-600' : 'bg-gray-100 text-gray-400' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-4 mb-1">
                        <h4 class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ $notification->data['title'] ?? 'System Notification' }}</h4>
                        <span class="text-[10px] text-gray-400 uppercase tracking-tighter whitespace-nowrap">{{ $notification->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed mb-4">{{ $notification->data['message'] ?? '---' }}</p>
                    
                    <div class="flex items-center gap-4">
                        @if($notification->unread())
                        <form action="{{ route('notifications.mark-read', $notification->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="text-[10px] font-bold text-primary-600 hover:underline uppercase tracking-widest">Mark Read</button>
                        </form>
                        @endif
                        @if(isset($notification->data['url']))
                        <a href="{{ $notification->data['url'] }}" class="text-[10px] font-bold text-indigo-600 hover:underline uppercase tracking-widest">View Details</a>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="py-24 text-center">
                <div class="w-16 h-16 bg-gray-50 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0l-8 8-8-8"/></svg>
                </div>
                <p class="text-sm font-medium text-gray-400">No notifications yet.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
