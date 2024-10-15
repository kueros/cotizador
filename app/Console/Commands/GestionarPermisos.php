<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GestionarPermisos extends Command
{
    protected $signature = 'permisos:gestionar {accion}';
    protected $description = 'Gestionar permisos del sistema';

    public function handle()
    {
        $accion = $this->argument('accion');

        switch ($accion) {
            case 'listar':
                $this->listarPermisos();
                break;

            case 'agregar':
                $this->agregarPermiso();
                break;

            case 'reordenar':
                $this->reordenarPermisos();
                break;

            default:
                $this->error('Acción no válida.');
                break;
        }
    }

    protected function listarPermisos() {
        $permisos = DB::table('permisos')
            ->orderBy('seccion_id')
            ->orderBy('orden')
            ->get();
dd($permisos);
        foreach ($permisos as $permiso) {
            $this->info("Permiso: {$permiso->nombre}, Orden: {$permiso->orden}, sección: {$permiso->seccion_id}");
        }
    }

    protected function agregarPermiso() {
        $nombre = $this->ask('Ingrese el nombre del permiso');
        $orden = $this->ask('Ingrese el orden del permiso');
        $seccion_id = $this->ask('Ingrese el ID de la sección');

        DB::table('permisos')->insert([
            'nombre' => $nombre,
            'orden' => $orden,
            'seccion_id' => $seccion_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->info('Permiso agregado exitosamente.');
    }

    protected function reordenarPermisos() {
        $seccion_id = $this->ask('Ingrese el ID de la sección a reordenar');
        $nuevo_orden = $this->ask('Ingrese el nuevo orden de permisos, separados por comas (ej: 3,1,2)');

        $nuevo_orden = explode(',', $nuevo_orden);
        foreach ($nuevo_orden as $index => $permiso_id) {
            DB::table('permisos')
                ->where('id', $permiso_id)
                ->where('seccion_id', $seccion_id)
                ->update(['orden' => $index + 1]);
        }

        $this->info('Permisos reordenados correctamente.');
    }

	protected function actualizarPermiso($id, $nombre, $orden, $seccion_id) {
		return DB::table('permisos')
			->where('id', $id)
			->update([
				'nombre' => $nombre,
				'orden' => $orden,
				'seccion_id' => $seccion_id,
				'updated_at' => now(),
			]);
	}

	protected function eliminarPermiso($id) {
		return DB::table('permisos')
			->where('id', $id)
			->delete();
	}




}