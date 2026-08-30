<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Enums\EstadoSubscription;
use App\Filament\SuperAdmin\Resources\ComprobantePagoResource\Pages;
use App\Models\ComprobantePago;
use App\Models\Empresa;
use App\Models\Subscription;
use BackedEnum;
use Filament\Actions;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use UnitEnum;

class ComprobantePagoResource extends Resource
{
    protected static ?string $model = ComprobantePago::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-check';

    protected static UnitEnum|string|null $navigationGroup = 'Herramientas';

    protected static ?string $navigationLabel = 'Comprobantes de Pago';

    protected static ?string $modelLabel = 'Comprobante de Pago';

    protected static ?string $pluralModelLabel = 'Comprobantes de Pago';

    protected static ?string $slug = 'comprobantes-pago';

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable(),
                TextColumn::make('empresa.nombre')
                    ->label('Empresa')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('monto')
                    ->label('Monto')
                    ->formatStateUsing(fn ($state) => '$'.number_format($state, 0, ',', '.'))
                    ->sortable(),
                TextColumn::make('estado')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'pendiente' => 'warning',
                        'aprobado' => 'success',
                        'rechazado' => 'danger',
                    }),
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'aprobado' => 'Aprobado',
                        'rechazado' => 'Rechazado',
                    ]),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\Action::make('aprobar')
                    ->label('Aprobar')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Aprobar comprobante')
                    ->modalDescription('Se activará la empresa y se cambiará el estado de la suscripción a activa.')
                    ->action(function (ComprobantePago $record) {
                        $record->update(['estado' => 'aprobado']);

                        $empresa = Empresa::find($record->empresa_id);

                        if ($empresa) {
                            $empresa->update(['activa' => true]);

                            if ($empresa->subscription) {
                                $empresa->subscription->update([
                                    'estado' => EstadoSubscription::Activa,
                                    'fecha_fin' => now()->addMonth(),
                                ]);
                            } else {
                                $sub = Subscription::create([
                                    'empresa_id' => $empresa->id,
                                    'plan_id' => 1,
                                    'estado' => EstadoSubscription::Activa,
                                    'fecha_inicio' => now(),
                                    'fecha_fin' => now()->addMonth(),
                                ]);
                                $empresa->update(['subscription_id' => $sub->id]);
                            }
                        }

                        Notification::make()
                            ->title('Comprobante aprobado')
                            ->body("La empresa {$empresa->nombre} fue activada correctamente.")
                            ->success()
                            ->send();
                    })
                    ->visible(fn (ComprobantePago $record) => $record->estado === 'pendiente'),
                Actions\Action::make('rechazar')
                    ->label('Rechazar')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Rechazar comprobante')
                    ->modalDescription('El comprobante será marcado como rechazado.')
                    ->action(function (ComprobantePago $record) {
                        $record->update(['estado' => 'rechazado']);

                        Notification::make()
                            ->title('Comprobante rechazado')
                            ->body("El comprobante de {$record->empresa->nombre} fue rechazado.")
                            ->danger()
                            ->send();
                    })
                    ->visible(fn (ComprobantePago $record) => $record->estado === 'pendiente'),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Datos del comprobante')
                    ->schema([
                        TextEntry::make('empresa.nombre')
                            ->label('Empresa'),
                        TextEntry::make('monto')
                            ->label('Monto')
                            ->formatStateUsing(fn ($state) => '$'.number_format($state, 0, ',', '.')),
                        TextEntry::make('estado')
                            ->badge()
                            ->color(fn ($state) => match ($state) {
                                'pendiente' => 'warning',
                                'aprobado' => 'success',
                                'rechazado' => 'danger',
                            }),
                        TextEntry::make('notas')
                            ->label('Notas'),
                        TextEntry::make('created_at')
                            ->label('Fecha de envío')
                            ->dateTime('d/m/Y H:i'),
                    ])
                    ->columns(2),
                Section::make('Comprobante')
                    ->schema([
                        ImageEntry::make('archivo')
                            ->label('Imagen del comprobante')
                            ->disk('public')
                            ->visibility('public')
                            ->columnSpanFull(),
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
            'index' => Pages\ListComprobantes::route('/'),
            'view' => Pages\ViewComprobante::route('/{record}'),
        ];
    }
}
