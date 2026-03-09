<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class Settings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Pengaturan API';

    protected static ?string $title = 'Pengaturan API';

    protected static ?int $navigationSort = 10;

    protected static string $view = 'filament.pages.settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'late_api_key'    => Setting::get('late_api_key', config('services.late.api_key')),
            'late_profile_id' => Setting::get('late_profile_id', config('services.late.profile_id')),
            'fonnte_token'    => Setting::get('fonnte_token', config('services.fonnte.token')),
            'whatsapp_target' => Setting::get('whatsapp_target', ''),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Late API (Instagram, Facebook, TikTok Story)')
                    ->description('Konfigurasi API untuk posting story ke Instagram, Facebook, dan TikTok melalui Late.dev')
                    ->icon('heroicon-o-globe-alt')
                    ->schema([
                        Forms\Components\TextInput::make('late_api_key')
                            ->label('Late API Key')
                            ->password()
                            ->revealable()
                            ->required()
                            ->placeholder('sk_xxxxxx...')
                            ->helperText('Dapatkan API Key dari dashboard getlate.dev'),

                        Forms\Components\TextInput::make('late_profile_id')
                            ->label('Late Profile ID')
                            ->required()
                            ->placeholder('69a43e96b53d3512c0bd30bb')
                            ->helperText('Profile ID yang terhubung dengan akun sosial media Anda'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Fonnte API (WhatsApp Status)')
                    ->description('Konfigurasi API untuk mengirim WhatsApp Status melalui Fonnte')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->schema([
                        Forms\Components\TextInput::make('fonnte_token')
                            ->label('Fonnte Token')
                            ->password()
                            ->revealable()
                            ->required()
                            ->placeholder('Token dari dashboard Fonnte')
                            ->helperText('Dapatkan token dari dashboard.fonnte.com'),

                        Forms\Components\TextInput::make('whatsapp_target')
                            ->label('Nomor WhatsApp Target')
                            ->required()
                            ->placeholder('628xxxxxxxxxx')
                            ->tel()
                            ->helperText('Nomor WhatsApp dengan kode negara, tanpa tanda + (contoh: 628123456789)'),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        Setting::set('late_api_key', $data['late_api_key']);
        Setting::set('late_profile_id', $data['late_profile_id']);
        Setting::set('fonnte_token', $data['fonnte_token']);
        Setting::set('whatsapp_target', $data['whatsapp_target']);

        Notification::make()
            ->title('Pengaturan berhasil disimpan!')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            \Filament\Actions\Action::make('save')
                ->label('Simpan Pengaturan')
                ->submit('save'),
        ];
    }
}
