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
                        Forms\Components\CheckboxList::make('platforms')
                            ->label('Platform Tujuan')
                            ->options([
                                'whatsapp_status'  => '📱 WhatsApp Status',
                                'instagram_story'  => '📸 Instagram Story',
                                'facebook_story'   => '👤 Facebook Story',
                                'tiktok_story'     => '🎵 TikTok Story',
                            ])
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

                Tables\Columns\TextColumn::make('platforms')
                    ->label('Platform')
                    ->formatStateUsing(function ($state) {
                        if (!is_array($state)) return '-';
                        $icons = [
                            'whatsapp_status'  => '📱 WhatsApp',
                            'instagram_story'  => '📸 Instagram',
                            'facebook_story'   => '👤 Facebook',
                            'tiktok_story'     => '🎵 TikTok',
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
                        $platforms = $record->platforms ?? [];
                        $mediaUrl  = $record->media_url;    // Full URL: http://localhost:8000/storage/status/...
                        $mediaPath = $record->media_path;   // Raw path: status/filename.ext
                        $caption   = $record->caption ?? '';

                        if (!$mediaPath) {
                            Notification::make()
                                ->title('Gagal: Tidak ada media!')
                                ->danger()
                                ->send();
                            return;
                        }

                        $errors  = [];
                        $success = true;

                        // WhatsApp via Fonnte (uses file binary upload, works on localhost)
                        if (in_array('whatsapp_status', $platforms)) {
                            $result = app(WhatsAppService::class)->sendStatus($mediaPath, $caption);
                            if (!$result['success']) {
                                $errors[] = 'WhatsApp: ' . $result['message'];
                                $success  = false;
                            }
                        }

                        // Late API (needs raw media_path for local file upload)
                        $latePlatforms = array_values(array_filter($platforms, fn($p) => in_array($p, [
                            'instagram_story', 'facebook_story', 'tiktok_story'
                        ])));

                        if (!empty($latePlatforms)) {
                            $result = app(LateService::class)->sendStory($latePlatforms, $mediaPath, $caption);
                            if (!$result['success']) {
                                $errors[] = 'Late API: ' . $result['message'];
                                $success  = false;
                            }
                        }

                        if ($success) {
                            $record->update(['status' => 'posted', 'error_message' => null]);
                            Notification::make()
                                ->title('Berhasil dipublikasikan!')
                                ->success()
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
