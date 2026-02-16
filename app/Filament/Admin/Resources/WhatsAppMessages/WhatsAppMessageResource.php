<?php

namespace App\Filament\Admin\Resources\WhatsAppMessages;

use App\Filament\Admin\Resources\WhatsAppMessages\Pages\ListWhatsAppMessages;
use App\Filament\Admin\Resources\WhatsAppMessages\Pages\ViewWhatsAppMessage;
use App\Models\WhatsAppMessage;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use App\Services\WhatsAppService;
use Filament\Notifications\Notification;

class WhatsAppMessageResource extends Resource
{
    protected static ?string $model = WhatsAppMessage::class;

    protected static ?string $modelLabel = 'Pesan WhatsApp';
    
    protected static ?string $pluralModelLabel = 'Pesan WhatsApp (Outbox)';

    protected static ?string $navigationLabel = 'WhatsApp Outbox';

    protected static string|\UnitEnum|null $navigationGroup = 'Pengaturan';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('phone')
                    ->label('Nomor HP')
                    ->readOnly(),
                
                Textarea::make('message')
                    ->label('Pesan')
                    ->readOnly()
                    ->rows(4)
                    ->columnSpanFull(),
                
                TextInput::make('status')
                    ->label('Status')
                    ->readOnly(),
                
                TextInput::make('attachment_url')
                    ->label('Lampiran')
                    ->readOnly()
                    ->url()
                    ->suffixIcon('heroicon-m-arrow-top-right-on-square')
                    ->columnSpanFull(),
                
                TextInput::make('wablas_message_id')
                    ->label('Wablas ID')
                    ->readOnly(),
                
                TextInput::make('created_at')
                    ->label('Dibuat Pada')
                    ->readOnly(),
                
                TextInput::make('updated_at')
                    ->label('Diperbarui Pada')
                    ->readOnly(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime()
                    ->sortable(),
                
                TextColumn::make('phone')
                    ->label('Penerima')
                    ->searchable(),
                
                TextColumn::make('message')
                    ->label('Pesan')
                    ->limit(50)
                    ->tooltip(fn (WhatsAppMessage $record): string => $record->message ?? ''),
                
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'sent' => 'info',
                        'read' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    }),
                
                IconColumn::make('attachment_url')
                    ->label('Lampiran')
                    ->boolean()
                    ->trueIcon('heroicon-o-paper-clip')
                    ->falseIcon('')
                    ->getStateUsing(fn (WhatsAppMessage $record): bool => !empty($record->attachment_url)),            
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                ViewAction::make(),
                \Filament\Actions\Action::make('resend')
                    ->label('Resend')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (WhatsAppMessage $record, WhatsAppService $wa) {
                        if (!empty($record->attachment_url)) {
                            $wa->sendDocument($record->phone, $record->attachment_url, $record->message);
                        } else {
                            $wa->sendMessage($record->phone, $record->message);
                        }
                        
                        Notification::make()
                            ->title('Pesan dikirim ulang')
                            ->success()
                            ->send();
                    }),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWhatsAppMessages::route('/'),
            'view' => ViewWhatsAppMessage::route('/{record}'),
        ];
    }
}
