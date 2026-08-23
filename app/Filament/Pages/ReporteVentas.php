<?php

namespace App\Filament\Pages;

use App\Services\ReporteVentasService;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Carbon;
use UnitEnum;

class ReporteVentas extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static string|UnitEnum|null $navigationGroup = 'Finanzas';

    protected static ?string $navigationLabel = 'Reporte de Ventas';

    protected static ?string $title = 'Reporte de Ventas y Cobros';

    protected static ?string $slug = 'reporte-ventas';

    protected string $view = 'filament.pages.reporte-ventas';

    public ?array $data = [];

    public ?array $totales = null;

    public ?array $timeline = null;

    public ?string $fechaDesde = null;

    public ?string $fechaHasta = null;

    public function mount(): void
    {
        $this->fechaDesde = now()->format('Y-m-d');
        $this->fechaHasta = now()->format('Y-m-d');

        $this->form->fill([
            'fecha_desde' => $this->fechaDesde,
            'fecha_hasta' => $this->fechaHasta,
        ]);

        $this->buscar();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DatePicker::make('fecha_desde')
                    ->label('Desde')
                    ->required()
                    ->native(false),
                DatePicker::make('fecha_hasta')
                    ->label('Hasta')
                    ->required()
                    ->native(false),
            ])
            ->statePath('data');
    }

    public function buscar(): void
    {
        $data = $this->form->getState();

        $desde = Carbon::parse($data['fecha_desde']);
        $hasta = Carbon::parse($data['fecha_hasta']);

        $this->fechaDesde = $desde->format('Y-m-d');
        $this->fechaHasta = $hasta->format('Y-m-d');

        $service = app(ReporteVentasService::class);
        $result = $service->obtenerDatos($desde, $hasta);

        $this->totales = $result['totales'];
        $this->timeline = $result['timeline']->toArray();
    }
}
