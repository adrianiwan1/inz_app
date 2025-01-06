<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UsersManagementResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class UsersManagementResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Zarządzanie użytkownikami';

    protected static ?string $pluralModelLabel = 'Użytkownicy';
    protected static ?string $singularModelLabel = 'Użytkownik';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Imię i nazwisko')
                    ->disabled(),
                Forms\Components\TextInput::make('email')
                    ->label('E-mail')
                    ->disabled(),
                Forms\Components\Select::make('employment_type')
                    ->label('Typ umowy')
                    ->options([
                        'employment' => 'Umowa o pracę',
                        'b2b' => 'B2B',
                        'contract' => 'Umowa zlecenie',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('hourly_rate')
                    ->label('Stawka godzinowa (zł)')
                    ->numeric()
                    ->step(0.01)
                    ->hint('Podaj stawkę w złotówkach (np. 25.50)')
                    ->visible(fn ($record) => in_array($record?->employment_type, ['b2b', 'contract']))
                    ->afterStateHydrated(fn ($component, $record) => $component->state($record?->hourly_rate / 100)) // Przekształca wartość z groszy na złotówki
                    ->mutateDehydratedStateUsing(fn ($state) => (int) round($state * 100)), // Przekształca wartość ze złotówek na grosze
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Imię i nazwisko')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('email')
                    ->label('E-mail')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('employment_type')
                    ->label('Typ umowy')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'employment' => 'Umowa o pracę',
                        'b2b' => 'B2B',
                        'contract' => 'Umowa zlecenie',
                        default => 'Nieznany',
                    }),
                TextColumn::make('hourly_rate')
                    ->label('Wynagrodzenie godzinowe (zł)')
                    ->formatStateUsing(fn ($state, $record) => $record->employment_type === 'employment'
                        ? 'Nie dotyczy'
                        : number_format($state / 100, 2, ',', ' ') . ' zł'),
                TextColumn::make('created_at')
                    ->label('Data utworzenia')
                    ->dateTime('Y-m-d'),
            ])
            ->filters([
                // Możesz dodać filtry
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsersManagement::route('/'),
            'create' => Pages\CreateUsersManagement::route('/create'),
            'edit' => Pages\EditUsersManagement::route('/{record}/edit'),
        ];
    }
}
