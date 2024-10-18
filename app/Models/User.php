<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Rol;
use Illuminate\Support\Facades\Password;

use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'nombre',
        'apellido',
        'email',
        'rol_id',
        'habilitado',
        'eliminado',
        'fecha_eliminado',
        'bloqueado',
        'cambiar_password',
        'token',
        'ultimo_login',
        'password',
    ];
    
	
	protected $primaryKey = 'user_id';

	/**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function roles()
    {
		return $this->belongsToMany(Rol::class, 'roles_x_usuario', 'user_id', 'rol_id');
    }

    public function hasRole($rol): bool
    {
		#dd($this->roles);
        return $this->roles()->where('nombre', $rol)->exists();
		#return $this->roles->contains('nombre', $rol);

    }

    public function hasPermission($permission)
    {
        foreach ($this->roles as $rol) {
            if ($rol->permissions()->where('nombre', $permission)->exists()) {
                return true;
            }
        }
        return false;
    }

    public function assignRole($rol)
    {
        $rol = Rol::where('nombre', $rol)->firstOrFail();
        $this->roles()->attach($rol);
    }

    public static function reset(array $credentials, \Closure $callback)
    {
        #dd($credentials);
        // Buscar el usuario por su email y token
        $user = self::where('token', $credentials['token'])
                    ->first();

        if (!$user) {
            return Password::INVALID_USER;
        }

        // Llamar al callback que actualiza la contraseña
        $callback($user, $credentials['password']);

        // Generar un nuevo token de seguridad y guardar los cambios
        $user->forceFill([
            'remember_token' => Str::random(60),
        ])->save();

        return Password::PASSWORD_RESET;
    }


}
