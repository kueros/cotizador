<?php
namespace App\Traits;

use App\Models\Permiso_x_Rol;

trait PermissionTrait
{
    public function hasPermission($permisoNombre)
    {
        $roles = $this->roles; // Asumiendo que el modelo User tiene una relación 'roles'

        foreach ($roles as $rol) {
            $permiso = Permiso_x_Rol::where('rol_id', $rol->id)
                                     ->whereHas('permiso', function ($query) use ($permisoNombre) {
                                         $query->where('nombre', $permisoNombre);
                                     })
                                     ->first();

            if ($permiso && $permiso->habilitado) {
                return true;
            }
        }

        return false;
    }
}