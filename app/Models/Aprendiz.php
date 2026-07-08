<?php

namespace App\Models;

use App\Models\Concerns\Aprendiz\HasAprendizRelations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

class Aprendiz extends Model
{
    use HasAprendizRelations;
    use HasFactory;
    use HasRoles;
    use SoftDeletes;

    protected $table = 'aprendices';

    /**
     * Los atributos asignables.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'persona_id',
        'ficha_caracterizacion_id',
        'estado',
        'user_create_id',
        'user_edit_id',
    ];

    /**
     * Los atributos que deben ser casteados.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'estado' => 'boolean',
    ];

    /**
     * Verifica si el aprendiz tiene un rol específico.
     */
    public function hasRole(string $role): bool
    {
        if (parent::hasRole($role)) {
            return true;
        }

        if ($this->persona && $this->persona->user) {
            return $this->persona->user->hasRole($role);
        }

        return false;
    }

    /**
     * Verifica si el aprendiz tiene un permiso específico.
     */
    public function hasPermissionTo(string $permission): bool
    {
        if (parent::hasPermissionTo($permission)) {
            return true;
        }

        if ($this->persona && $this->persona->user) {
            return $this->persona->user->hasPermissionTo($permission);
        }

        return false;
    }

    /**
     * Obtiene todos los roles del aprendiz (directos y a través del usuario).
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllRoles()
    {
        $roles = collect();

        $roles = $roles->merge($this->roles);

        if ($this->persona && $this->persona->user) {
            $roles = $roles->merge($this->persona->user->roles);
        }

        return $roles->unique('id');
    }

    /**
     * Obtiene todos los permisos del aprendiz (directos y a través del usuario).
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllPermissions()
    {
        $permissions = collect();

        $permissions = $permissions->merge($this->permissions);

        if ($this->persona && $this->persona->user) {
            $permissions = $permissions->merge($this->persona->user->permissions);
        }

        return $permissions->unique('id');
    }
}
