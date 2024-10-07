<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Rol;


class User extends Authenticatable
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
        return $this->belongsToMany(Rol::class);
    }

    public function hasRole($rol): bool
    {
        #return $this->roles()->where('nombre', $rol)->exists();
		return $this->roles->contains('nombre', $rol);

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
}
