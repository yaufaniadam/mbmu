<?php

namespace App\Filament\Sppg\Resources;

use App\Filament\Sppg\Resources\InvoiceResource\Pages;
use App\Models\Invoice;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use BackedEnum;
use UnitEnum;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-currency-dollar';
    protected static ?string $navigationLabel = 'Tagihan & Pembayaran';
    protected static ?string $pluralModelLabel = 'Tagihan';
    protected static string|UnitEnum|null $navigationGroup = 'Keuangan';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Tagihan')
                    ->schema([
                        Forms\Components\TextInput::make('invoice_number')
                            ->label('Nomor Invoice')
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('amount')
                            ->label('Jumlah Tagihan')
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\DatePicker::make('due_date')
                            ->label('Jatuh Tempo')
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Alasan Penolakan')
                            ->helperText('Jika pembayaran ditolak, alasannya akan muncul di sini.')
                            ->visible(fn ($record) => $record?->status === 'REJECTED')
                            ->columnSpanFull()
                            ->disabled(),
                    ])->columns(3),
                
                Section::make('Konfirmasi Pembayaran')
                    ->schema([
                         Forms\Components\Placeholder::make('info_transfer')
                            ->label('Instruksi Pembayaran')
                            ->content('Silakan transfer ke rekening Lembaga Pengusul (Lihat Detail SPPG), lalu upload bukti transfer di sini.'),
                         
                         Forms\Components\FileUpload::make('proof_of_payment')->disk('public')
                            ->label('Bukti Transfer')
                            ->image()
                            ->directory('invoice-proofs')
                            ->visibility('public')
                            ->maxSize(10240)
                            ->required()
                            ->columnSpanFull()
                            ->helperText('Upload foto/screenshot bukti transfer.'),
                            
                         Forms\Components\Hidden::make('status_update')
                             ->dehydrated(false),
                    ])
                    ->visible(fn ($record) => in_array($record?->status, ['UNPAID', 'REJECTED'])),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label('No Invoice')
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Periode')
                    ->formatStateUsing(fn ($record) => $record->start_date->format('d M') . ' - ' . $record->end_date->format('d M')),
                Tables\Columns\TextColumn::make('amount')
                    ->money('IDR')
                    ->label('Jumlah'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'UNPAID' => 'gray',
                        'WAITING_VERIFICATION' => 'warning',
                        'PAID' => 'success',
                        'REJECTED' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('due_date')
                    ->date()
                    ->label('Jatuh Tempo'),
            ])
            ->recordActions([
                EditAction::make()
                    ->label(fn (Invoice $record) => $record->status === 'PAID' ? 'Lihat' : 'Bayar')
                    ->icon(fn (Invoice $record) => $record->status === 'PAID' ? 'heroicon-o-eye' : 'heroicon-o-credit-card'),
            ]);
    }
    
    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();
        $query = parent::getEloquentQuery()->where('type', 'SPPG_SEWA');

        $sppg = null;
        if ($user->hasRole('Kepala SPPG')) {
            $sppg = $user->sppgDikepalai;
        } elseif ($user->hasAnyRole(['PJ Pelaksana', 'Staf Akuntan', 'Staf Administrator SPPG'])) {
             $sppg = $user->unitTugas->first();
        }

        if ($sppg) {
             return $query->where('sppg_id', $sppg->id);
        }
        
        return $query->whereRaw('1=0');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
