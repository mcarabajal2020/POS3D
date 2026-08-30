<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Enums\EstadoInvoice;
use App\Filament\SuperAdmin\Resources\InvoiceResource\Pages;
use App\Models\Empresa;
use App\Models\Invoice;
use App\Notifications\FacturaGeneradaNotification;
use App\Services\FacturacionService;
use BackedEnum;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Facturas';

    protected static string|UnitEnum|null $navigationGroup = 'Facturación';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Factura';

    protected static ?string $pluralModelLabel = 'Facturas';

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
                    ->disabled(),
                Select::make('estado')
                    ->options(EstadoInvoice::class)
                    ->required(),
                Textarea::make('nota')
                    ->rows(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable(),
                Tables\Columns\TextColumn::make('empresa.nombre')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),
                Tables\Columns\TextColumn::make('formatted_monto')
                    ->label('Monto')
                    ->sortable(),
                Tables\Columns\TextColumn::make('estado')
                    ->badge()
                    ->formatStateUsing(fn (EstadoInvoice $state): string => $state->label())
                    ->color(fn (EstadoInvoice $state): string => $state->color()),
                Tables\Columns\TextColumn::make('fecha_emision')
                    ->label('Emisión')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_vencimiento')
                    ->label('Vencimiento')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_pago')
                    ->label('Pago')
                    ->date('d/m/Y')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('metodo_pago')
                    ->label('Método')
                    ->placeholder('—'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->options(EstadoInvoice::class),
            ])
            ->actions([
                Actions\Action::make('marcarPagada')
                    ->label('Marcar pagada')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->form(fn () => [
                        Select::make('metodo_pago')
                            ->label('Método de pago')
                            ->options([
                                'transferencia' => 'Transferencia',
                                'efectivo' => 'Efectivo',
                                'mercado_pago' => 'MercadoPago',
                                'otro' => 'Otro',
                            ])
                            ->required(),
                    ])
                    ->action(function (Invoice $record, array $data): void {
                        $service = app(FacturacionService::class);
                        $service->marcarInvoicePagada($record, $data['metodo_pago']);

                        Notification::make()
                            ->title('Factura marcada como pagada')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Invoice $record) => $record->estado !== EstadoInvoice::Pagada),
                Actions\Action::make('notificar')
                    ->label('Notificar')
                    ->icon('heroicon-o-bell')
                    ->action(function (Invoice $record): void {
                        $empresa = $record->empresa;
                        $adminUsers = $empresa->users->filter(fn ($user) => $user->isAdmin());

                        $monto = '$ '.number_format($record->monto, 0, ',', '.');
                        $vence = $record->fecha_vencimiento->format('d/m/Y');

                        foreach ($adminUsers as $user) {
                            $user->notify(new FacturaGeneradaNotification($record));
                        }

                        Notification::make()
                            ->title('Notificación enviada')
                            ->success()
                            ->send();
                    }),
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
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
