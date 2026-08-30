<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Filament\SuperAdmin\Resources\PlanResource\Pages;
use App\Models\Plan;
use BackedEnum;
use Filament\Actions;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class PlanResource extends Resource
{
    protected static ?string $model = Plan::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationLabel = 'Planes';

    protected static string|UnitEnum|null $navigationGroup = 'Facturación';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Plan';

    protected static ?string $pluralModelLabel = 'Planes';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Datos del Plan')
                    ->columns(2)
                    ->schema([
                        TextInput::make('nombre')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('orden')
                            ->numeric()
                            ->default(0),
                        Toggle::make('activo')
                            ->default(true),
                    ]),

                Section::make('Precios')
                    ->columns(2)
                    ->schema([
                        TextInput::make('precio_mensual')
                            ->label('Precio mensual ($)')
                            ->required()
                            ->numeric()
                            ->prefix('$'),
                        TextInput::make('precio_anual')
                            ->label('Precio anual ($)')
                            ->numeric()
                            ->prefix('$')
                            ->helperText('Si se deja vacío, no se ofrece facturación anual'),
                    ]),

                Section::make('Límites')
                    ->columns(3)
                    ->schema([
                        TextInput::make('max_usuarios')
                            ->label('Max. usuarios')
                            ->required()
                            ->numeric()
                            ->default(2)
                            ->helperText('0 = sin límite (Enterprise)'),
                        TextInput::make('max_ventas_mensuales')
                            ->label('Max. ventas/mes')
                            ->required()
                            ->numeric()
                            ->default(100),
                        TextInput::make('max_articulos')
                            ->label('Max. artículos')
                            ->required()
                            ->numeric()
                            ->default(50),
                        TextInput::make('max_filamentos')
                            ->label('Max. filamentos')
                            ->required()
                            ->numeric()
                            ->default(10),
                        TextInput::make('max_impresoras')
                            ->label('Max. impresoras')
                            ->required()
                            ->numeric()
                            ->default(3),
                        TextInput::make('trial_dias')
                            ->label('Días de prueba')
                            ->required()
                            ->numeric()
                            ->default(14),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),
                Tables\Columns\TextColumn::make('formatted_precio_mensual')
                    ->label('Precio/mes'),
                Tables\Columns\TextColumn::make('max_usuarios')
                    ->label('Usuarios')
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_ventas_mensuales')
                    ->label('Ventas/mes')
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_articulos')
                    ->label('Artículos')
                    ->sortable(),
                Tables\Columns\TextColumn::make('trial_dias')
                    ->label('Trial')
                    ->formatStateUsing(fn ($state): string => "{$state} días"),
                Tables\Columns\IconColumn::make('activo')
                    ->boolean(),
                Tables\Columns\TextColumn::make('subscriptions_count')
                    ->counts('subscriptions')
                    ->label('Suscripciones')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('orden')
            ->filters([])
            ->actions([
                Actions\EditAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListPlans::route('/'),
            'create' => Pages\CreatePlan::route('/create'),
            'edit' => Pages\EditPlan::route('/{record}/edit'),
        ];
    }
}
