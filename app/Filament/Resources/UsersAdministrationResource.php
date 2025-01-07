<?php

namespace App\Filament\Resources;

use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Database\Eloquent\Builder;

class UsersAdministrationResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Administracja Użytkownikami';

    public static function canViewAny(): bool
    {
        // Dostęp mają tylko użytkownicy z rolą admin
        return auth()->user()?->hasRole('admin') ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Imię i nazwisko')
                    ->disabled(), // Pole tylko do odczytu

                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->disabled(), // Pole tylko do odczytu

                Forms\Components\Select::make('roles')
                    ->label('Rola')
                    ->relationship('roles', 'name') // Powiązanie relacji
                    ->options(\Spatie\Permission\Models\Role::query()->pluck('name', 'id')) // Pobiera ID i nazwę
                    ->required()
                    ->helperText('Zmień rolę użytkownika.')
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
                    ->label('Email')
                    ->sortable()
                    ->searchable(),

                BadgeColumn::make('roles.name')
                    ->label('Rola')
                    ->colors([
                        'danger' => 'admin',
                        'success' => 'manager',
                        'primary' => 'employee',
                    ]),

                TextColumn::make('created_at')
                    ->label('Data stworzenia')
                    ->date('Y-m-d'),
            ])
            ->filters([
                //  filtry,
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\UsersAdministrationResource\Pages\ListUsersAdministration::route('/'),
            'edit' => \App\Filament\Resources\UsersAdministrationResource\Pages\EditUsersAdministration::route('/{record}/edit'),
        ];
    }
}
