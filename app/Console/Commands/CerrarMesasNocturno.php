<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CerrarMesasNocturno extends Command
{
    protected $signature   = 'mesas:cerrar-nocturno';
    protected $description = 'Cierra automáticamente las mesas abiertas de días anteriores';

    public function handle(): int
    {
        $hoy = Carbon::today()->toDateString();

        $ventasAbiertas = DB::table('Venta')
            ->where('status', 0)
            ->where('tipo_servicio', 1)
            ->whereNotNull('mesa')
            ->whereRaw('DATE(fecha_hora) < ?', [$hoy])
            ->get();

        if ($ventasAbiertas->isEmpty()) {
            $this->info('No hay mesas abiertas de días anteriores.');
            return Command::SUCCESS;
        }

        foreach ($ventasAbiertas as $venta) {
            $comentario = trim(($venta->comentarios ?? '') . ' | CIERRE AUTOMÁTICO ' . now()->format('d/m/Y H:i'), ' | ');
            DB::table('Venta')->where('id_venta', $venta->id_venta)->update([
                'status'      => 1,
                'comentarios' => $comentario,
            ]);
        }

        $this->info("Mesas cerradas automáticamente: {$ventasAbiertas->count()}");
        return Command::SUCCESS;
    }
}
