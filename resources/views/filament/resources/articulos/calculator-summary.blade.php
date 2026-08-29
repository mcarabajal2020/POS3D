@php
    $filamentoId = null;
@endphp

<x-filament::section>
    <x-slot name="heading">
        Resumen del Presupuesto
    </x-slot>

    <div
        x-data="{
            get filamentoId() { return Number($wire.get('data.filamento_id')) || null },
            get precioKg() { return Number($wire.get('data.costo_filamento_kg')) || 25000 },
            get peso() { return Number($wire.get('data.filamento_gramos')) || 0 },
            get horas() { return Number($wire.get('data.horas_impresion')) || 0 },
            get minutos() { return Number($wire.get('data.tiempo_minutos')) || 0 },
            get watts() { return Number($wire.get('data.consumo_watts')) || 120 },
            get costoKwh() { return Number($wire.get('data.costo_kwh')) || 50 },
            get desgaste() { return Number($wire.get('data.desgaste_maquina')) || 120 },
            get manoObra() { return Number($wire.get('data.costo_mano_obra')) || 0 },
            get horasTrabajo() { return Number($wire.get('data.horas_trabajo')) || 0 },
            get extrasVal() { return Number($wire.get('data.extras')) || 0 },
            get margen() { return Number($wire.get('data.multiplicador_precio')) || 4 },
            get cantPiezas() { return Number($wire.get('data.cantidad_piezas')) || 1 },
            get tiempoHoras() { return this.horas + (this.minutos / 60) },
            get material() { return Math.round((this.peso * this.precioKg) / 1000) },
            get electricidad() { return Math.round((this.watts * this.tiempoHoras * this.costoKwh) / 1000) },
            get desgasteTotal() { return Math.round(this.tiempoHoras * this.desgaste) },
            get manoObraTotal() { return Math.round(this.horasTrabajo * this.manoObra) },
            get costoTotal() { return this.material + this.electricidad + this.desgasteTotal + this.manoObraTotal + this.extrasVal },
            get costoUnidad() { return this.cantPiezas > 0 ? Math.round(this.costoTotal / this.cantPiezas) : 0 },
            get ventaSugerida() { return Math.round(this.costoTotal * this.margen) },
            fmt(n) { return n.toLocaleString('es-AR') }
        }"
        class="space-y-2 text-sm"
    >
        <div class="flex justify-between">
            <span class="text-gray-600 dark:text-gray-400">Costo de material:</span>
            <span class="font-medium" x-text="'$ ' + fmt(material)"></span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-600 dark:text-gray-400">Electricidad:</span>
            <span class="font-medium" x-text="'$ ' + fmt(electricidad)"></span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-600 dark:text-gray-400">Amortización / desgaste:</span>
            <span class="font-medium" x-text="'$ ' + fmt(desgasteTotal)"></span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-600 dark:text-gray-400">Mano de obra:</span>
            <span class="font-medium" x-text="'$ ' + fmt(manoObraTotal)"></span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-600 dark:text-gray-400">Extras:</span>
            <span class="font-medium" x-text="'$ ' + fmt(extrasVal)"></span>
        </div>

        <div class="border-t border-gray-200 dark:border-gray-700 pt-2 mt-2">
            <div class="flex justify-between">
                <span class="font-bold text-gray-900 dark:text-white">Costo total:</span>
                <span class="font-bold text-gray-900 dark:text-white" x-text="'$ ' + fmt(costoTotal)"></span>
            </div>
        </div>

        <div class="bg-primary-50 dark:bg-primary-900/20 rounded-lg p-3 mt-3">
            <div class="flex justify-between items-center">
                <span class="font-bold text-primary-700 dark:text-primary-300">Venta sugerida:</span>
                <span class="text-xl font-bold text-primary-700 dark:text-primary-300" x-text="'$ ' + fmt(ventaSugerida)"></span>
            </div>
        </div>

        <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
            <span>Costo por unidad:</span>
            <span x-text="'$ ' + fmt(costoUnidad)"></span>
        </div>
    </div>
</x-filament::section>
