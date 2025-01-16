<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
Use App\Filament\Resources\InvoiceResource\Widgets;
use Illuminate\Support\Facades\Auth;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationLabel = 'Faktury';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function getWidgets(): array
    {
        return [
            Widgets\BuyerWidget::class,
        ];
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\TextInput::make('seller_name')->required(),
                Forms\Components\TextInput::make('seller_address')->required(),
                Forms\Components\TextInput::make('seller_nip')->required(),
                Forms\Components\TextInput::make('buyer_name')->required(),
                Forms\Components\TextInput::make('buyer_address')->required(),
                Forms\Components\TextInput::make('buyer_nip')->required(),
                Forms\Components\TextInput::make('service_name')->required(),
                Forms\Components\TextInput::make('invoice_number')->required(),
                Forms\Components\TextInput::make('net_value')->required()->numeric(),
                Forms\Components\TextInput::make('tax_rate')->required()->numeric(),
                Forms\Components\TextInput::make('gross_value')->required()->numeric(),
                Forms\Components\TextInput::make('bank_account_number')->required(),
                Forms\Components\DatePicker::make('issue_date')->required(),
                Forms\Components\DatePicker::make('sale_date')->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->label('Użytkownik'),
                TextColumn::make('user.first_name')->label('Imię'),
                TextColumn::make('user.last_name')->label('Imię'),
                TextColumn::make('invoice_number')->label('Numer faktury'),
                TextColumn::make('gross_value')
                    ->label('Kwota brutto')
                    ->formatStateUsing(fn($state) => number_format($state, 2, ',', ' ') . ' zł')
                    ->sortable(),
                TextColumn::make('issue_date')
                    ->label('Data wystawienia')
                    ->date('Y-m-d'),
                TextColumn::make('sale_date')
                    ->label('Termin płatności')
                    ->date('Y-m-d'),
            ])
            ->actions([
                Action::make('download_pdf')
                    ->label('Pobierz PDF')
                    ->icon('heroicon-s-chevron-down')
                    ->color('success')
                    ->action(function (Invoice $record) {
                        $safeInvoiceNumber = str_replace(['/', '\\'], '-', $record->invoice_number);
                        $pdf = PDF::loadView('pdf.pdf_filament', compact('record'));

                        return response()->streamDownload(
                            fn() => print($pdf->output()),
                            "Faktura_{$safeInvoiceNumber}.pdf"
                        );
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        $user = Auth::user();
        return $user && $user->hasRole('manager');
    }
}
