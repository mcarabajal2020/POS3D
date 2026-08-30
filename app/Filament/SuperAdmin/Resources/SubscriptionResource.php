<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Enums\CicloFacturacion;
use App\Enums\EstadoSubscription;
use App\Filament\SuperAdmin\Resources\SubscriptionResource\Pages;
use App\Models\Empresa;
use App\Models\Plan;
use App\Models\Subscription;
use App\Services\FacturacionService;
use BackedEnum;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path';

    protected static ?string $navigationLabel = 'Suscripciones';

    protected static string|UnitEnum|null $navigationGroup = 'Facturación';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Suscripción';

    protected static ?string $pluralModelLabel = 'Suscripciones';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('empresa_id')
                    ->label('Empresa')
                    ->options(Empresa::pluck('nombre', 'id'))
                    ->searchable()
                    ->preload()
                    ->required()
                    ->disabled(fn (?Subscription $record) => $record !== null),
                Select::make('plan_id')
                    ->label('Plan')
                    ->options(Plan::where('activo', true)->pluck('nombre', 'id'))
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('estado')
                    ->options(EstadoSubscription::class)
                    ->required(),
                Select::make('facturacion_ciclo')
                    ->label('Ciclo de facturación')
                    ->options(CicloFacturacion::class)
                    ->required(),
                DatePicker::make('fecha_inicio')
                    ->label('Fecha de inicio')
                    ->default(now())
                    ->required(),
                DatePicker::make('fecha_fin')
                    ->label('Fecha de fin')
                    ->helperText('Dejar vacío si es trial o sin vencimiento'),
                DatePicker::make('trial_fin')
                    ->label('Fin de prueba')
                    ->helperText('Si aplica período de prueba'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('empresa.nombre')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),
                Tables\Columns\TextColumn::make('plan.nombre')
                    ->label('Plan')
                    ->sortable(),
                Tables\Columns\TextColumn::make('estado')
                    ->badge()
                    ->formatStateUsing(fn (EstadoSubscription $state): string => $state->label())
                    ->color(fn (EstadoSubscription $state): string => $state->color()),
                Tables\Columns\TextColumn::make('facturacion_ciclo')
                    ->label('Ciclo')
                    ->formatStateUsing(fn (CicloFacturacion $state): string => $state->label()),
                Tables\Columns\TextColumn::make('fecha_inicio')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_fin')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('trial_fin')
                    ->label('Trial hasta')
                    ->date('d/m/Y')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('invoices_count')
                    ->counts('invoices')
                    ->label('Facturas')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->options(EstadoSubscription::class),
            ])
            ->actions([
                Actions\Action::make('cambiarPlan')
                    ->label('Cambiar plan')
                    ->icon('heroicon-o-arrow-path')
                    ->form(fn () => [
                        Select::make('plan_id')
                            ->label('Nuevo plan')
                            ->options(Plan::where('activo', true)->pluck('nombre', 'id'))
                            ->required(),
                    ])
                    ->action(function (Subscription $record, array $data): void {
                        $service = app(FacturacionService::class);
                        $service->cambiarPlan($record, Plan::find($data['plan_id']));

                        Notification::make()
                            ->title('Plan cambiado')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation(),
                Actions\Action::make('generarInvoice')
                    ->label('Generar factura')
                    ->icon('heroicon-o-document-text')
                    ->action(function (Subscription $record): void {
                        $service = app(FacturacionService::class);
                        $service->generarInvoice($record);

                        Notification::make()
                            ->title('Factura generada')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Subscription $record) => $record->estaActiva()),
                Actions\Action::make('suspender')
                    ->label('Suspender')
                    ->icon('heroicon-o-pause-circle')
                    ->color('warning')
                    ->action(function (Subscription $record): void {
                        $service = app(FacturacionService::class);
                        $service->suspender($record);

                        Notification::make()
                            ->title('Suscripción suspendida')
                            ->warning()
                            ->send();
                    })
                    ->visible(fn (Subscription $record) => $record->estaActiva())
                    ->requiresConfirmation(),
                Actions\Action::make('reactivar')
                    ->label('Reactivar')
                    ->icon('heroicon-o-play-circle')
                    ->color('success')
                    ->action(function (Subscription $record): void {
                        $service = app(FacturacionService::class);
                        $service->reactivar($record);

                        Notification::make()
                            ->title('Suscripción reactivada')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Subscription $record) => in_array($record->estado, [EstadoSubscription::Suspendida, EstadoSubscription::Vencida]))
                    ->requiresConfirmation(),
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
            'index' => Pages\ListSubscriptions::route('/'),
            'create' => Pages\CreateSubscription::route('/create'),
            'edit' => Pages\EditSubscription::route('/{record}/edit'),
        ];
    }
}
