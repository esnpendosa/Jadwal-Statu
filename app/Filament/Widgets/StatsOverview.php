<?php

namespace App\Filament\Widgets;

use App\Models\Post;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $total     = Post::count();
        $scheduled = Post::where('status', 'pending')->count();
        $posted    = Post::where('status', 'posted')->count();
        $failed    = Post::where('status', 'failed')->count();

        return [
            Stat::make('Total Status', $total)
                ->description('Semua konten terjadwal')
                ->descriptionIcon('heroicon-m-rectangle-stack')
                ->color('primary'),

            Stat::make('Terjadwal', $scheduled)
                ->description('Menunggu dipublikasikan')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Berhasil Diposting', $posted)
                ->description('Berhasil dipublikasikan')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Gagal', $failed)
                ->description('Gagal dipublikasikan')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
        ];
    }
}
