<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use App\Services\LateService;
use App\Services\WhatsAppService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';

    protected static ?string $navigationLabel = 'Jadwal Status';

    protected static ?string $modelLabel = 'Status';

    protected static ?string $pluralModelLabel = 'Jadwal Status';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Media & Konten')
                    ->schema([
                        Forms\Components\FileUpload::make('media_path')
                            ->label('Upload Media')
                            ->disk('public')
                            ->directory('status')
                            ->acceptedFileTypes([
                                'image/jpeg',
                                'image/png',
                                'image/webp',
                                'video/mp4',
                                'video/quicktime',
                            ])
                            ->maxSize(100 * 1024) // 100MB
                            ->image()
                            ->imagePreviewHeight('250')
                            ->openable()
                            ->required()
                            ->helperText('Format yang didukung: JPG, PNG, WEBP, MP4, MOV (maks. 100MB)')
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('caption')
                            ->label('Caption / Teks Status')
                            ->rows(4)
                            ->maxLength(2000)
                            ->columnSpanFull(),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Platform & Jadwal')
                    ->schema([
                        Forms\Components\Select::make('content_type')
                            ->label('Tipe Konten')
                            ->options([
                                'story'   => '📖 Story / Status',
                                'post'    => '🖼️ Postingan (Feed)',
                            ])
                            ->default('story')
                            ->required()
                            ->live()
                            ->helperText('Pilih apakah konten akan diposting sebagai Story atau Postingan di Feed'),

                        Forms\Components\CheckboxList::make('platforms')
                            ->label('Platform Tujuan')
                            ->options(function (\Filament\Forms\Get $get) {
                                $type = $get('content_type') ?? 'story';
                                if ($type === 'post') {
                                    return [
                                        'instagram_post'  => '📸 Instagram Post (Feed)',
                                        'facebook_post'   => '👤 Facebook Post (Feed)',
                                        'tiktok_story'    => '🎵 TikTok Video',
                                    ];
                                }
                                return [
                                    'whatsapp_status'  => '📱 WhatsApp Status',
                                    'instagram_story'  => '📸 Instagram Story',
                                    'facebook_story'   => '👤 Facebook Story',
                                    'tiktok_story'     => '🎵 TikTok Story',
                                ];
                            })
                            ->required()
                            ->columns(2)
                            ->gridDirection('row'),

                        Forms\Components\DateTimePicker::make('schedule_time')
                            ->label('Waktu Jadwal')
                            ->required()
                            ->native(false)
                            ->seconds(false)
                            ->default(now()->addMinutes(5))
                            ->helperText('Waktu posting otomatis akan berjalan setiap menit'),

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Pending',
                                'posted'  => 'Posted',
                                'failed'  => 'Failed',
                            ])
                            ->default('pending')
                            ->required()
                            ->visibleOn('edit'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('media_path')
                    ->label('Preview')
                    ->disk('public')
                    ->width(80)
                    ->height(60)
                    ->defaultImageUrl(asset('images/no-media.png'))
                    ->extraImgAttributes(['class' => 'rounded-lg object-cover']),

                Tables\Columns\TextColumn::make('caption')
                    ->label('Caption')
                    ->limit(60)
                    ->tooltip(fn($record) => $record->caption)
                    ->searchable(),

                Tables\Columns\TextColumn::make('content_type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn($state) => $state === 'post' ? 'info' : 'primary')
                    ->formatStateUsing(fn($state) => $state === 'post' ? '🖼️ Postingan' : '📖 Story'),

                Tables\Columns\TextColumn::make('platforms')
                    ->label('Platform')
                    ->formatStateUsing(function ($state) {
                        if (!is_array($state)) return '-';
                        $icons = [
                            'whatsapp_status'  => '📱 WhatsApp',
                            'instagram_story'  => '📸 IG Story',
                            'facebook_story'   => '👤 FB Story',
                            'tiktok_story'     => '🎵 TikTok',
                            'instagram_post'   => '📸 IG Post',
                            'facebook_post'    => '👤 FB Post',
                        ];
                        return collect($state)
                            ->map(fn($p) => $icons[$p] ?? $p)
                            ->implode(', ');
                    }),

                Tables\Columns\TextColumn::make('schedule_time')
                    ->label('Jadwal Posting')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'posted' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'pending' => 'heroicon-m-clock',
                        'posted' => 'heroicon-m-check-circle',
                        'failed' => 'heroicon-m-x-circle',
                        default => 'heroicon-m-question-mark-circle',
                    })
                    ->formatStateUsing(fn($state) => ucfirst($state)),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('schedule_time', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'posted'  => 'Posted',
                        'failed'  => 'Failed',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('publish_now')
                    ->label('Publish Sekarang')
                    ->icon('heroicon-o-rocket-launch')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Publish Status Sekarang?')
                    ->modalDescription('Konten akan langsung dipublikasikan ke platform yang dipilih.')
                    ->action(function (Post $record) {
                        $platforms   = $record->platforms ?? [];
                        $mediaPath   = $record->media_path;   // Raw path: status/filename.ext
                        $caption     = $record->caption ?? '';
                        $contentType = $record->content_type ?? 'story';

                        if (!$mediaPath) {
                            Notification::make()
                                ->title('Gagal: Tidak ada media!')
                                ->danger()
                                ->send();
                            return;
                        }

                        $errors       = [];
                        $successCount = 0;
                        $totalPlatforms = 0;

                        // WhatsApp via Node.js Bridge (port 3000)
                        if (in_array('whatsapp_status', $platforms)) {
                            $totalPlatforms++;
                            $result = app(WhatsAppService::class)->sendStatus($mediaPath, $caption);
                            if (!$result['success']) {
                                $isConnectionError = str_contains($result['message'], 'Bridge Exception') ||
                                                     str_contains($result['message'], 'port 3000') ||
                                                     str_contains($result['message'], 'connect');
                                if ($isConnectionError) {
                                    // Bridge offline — treat as warning, not hard failure
                                    $errors[] = '⚠ WhatsApp Bridge tidak aktif: ' . $result['message'];
                                } else {
                                    $errors[] = 'WhatsApp: ' . $result['message'];
                                }
                            } else {
                                $successCount++;
                            }
                        }

                        // Late API (Instagram Story, Facebook Story, TikTok Story)
                        $lateStoryPlatforms = array_values(array_filter($platforms, fn($p) => in_array($p, [
                            'instagram_story', 'facebook_story', 'tiktok_story'
                        ])));

                        if (!empty($lateStoryPlatforms)) {
                            $totalPlatforms++;
                            $result = app(LateService::class)->sendContent($lateStoryPlatforms, $mediaPath, $caption, 'story');
                            if (!$result['success']) {
                                $errors[] = 'Late API (Story): ' . $result['message'];
                            } else {
                                $successCount++;
                            }
                        }

                        // Late API (Instagram Post, Facebook Post)
                        $latePostPlatforms = array_values(array_filter($platforms, fn($p) => in_array($p, [
                            'instagram_post', 'facebook_post',
                        ])));

                        if (!empty($latePostPlatforms)) {
                            $totalPlatforms++;
                            $result = app(LateService::class)->sendContent($latePostPlatforms, $mediaPath, $caption, 'post');
                            if (!$result['success']) {
                                $errors[] = 'Late API (Post): ' . $result['message'];
                            } else {
                                $successCount++;
                            }
                        }

                        if ($successCount > 0) {
                            $errorNote = !empty($errors) ? implode("\n", $errors) : null;
                            $record->update(['status' => 'posted', 'error_message' => $errorNote]);
                            Notification::make()
                                ->title('Berhasil dipublikasikan!')
                                ->body($errorNote ? 'Catatan: ' . implode(' | ', $errors) : null)
                                ->success()
                                ->send();
                        } elseif ($totalPlatforms === 0) {
                            Notification::make()
                                ->title('Tidak ada platform dipilih!')
                                ->danger()
                                ->send();
                        } else {
                            $record->update(['status' => 'failed', 'error_message' => implode(' | ', $errors)]);
                            Notification::make()
                                ->title('Gagal mempublikasikan!')
                                ->body(implode("\n", $errors))
                                ->danger()
                                ->send();
                        }
                    })
                    ->hidden(fn(Post $record) => $record->status === 'posted'),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit'   => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
