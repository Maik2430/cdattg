<?php

namespace Database\Seeders\Aitg\Support;

use App\Models\Parametro;
use App\Models\ParametroTema;
use App\Models\Tema;

/** Resuelve IDs de nivel en parametros y parametros_temas (tema 6). */
class AitgNivelResolver
{
    private const TEMA_NIVELES = 6; // Tema NIVELES DE FORMACION

    public function __construct(private int $userId)
    {
    }

    /** ID de parametros (FK en aitg_perfiles_plan). */
    public function parametroId(string $nombre): int
    {
        $nombre = strtoupper(trim($nombre)); // Normaliza texto de entrada
        $parametro = Parametro::where('name', $nombre)->first(); // Busca nivel existente
        if ($parametro) {
            return $parametro->id; // Reutiliza parametro catalogado
        }

        $parametro = Parametro::create([
            'name' => $nombre, // Nombre del nivel académico
            'status' => 1, // Marca parametro activo
            'user_create_id' => $this->userId, // Auditoría creación
            'user_edit_id' => $this->userId, // Auditoría edición
        ]);

        $this->vincularTemaNiveles($parametro->id); // Enlaza parametro al tema 6

        return $parametro->id; // Devuelve FK para perfiles AITG
    }

    /** ID de parametros_temas (FK en programas_formacion). */
    public function parametroTemaId(string $nombre): int
    {
        $parametroId = $this->parametroId($nombre); // Garantiza parametro base
        $pivot = ParametroTema::where('tema_id', self::TEMA_NIVELES)
            ->where('parametro_id', $parametroId)
            ->first(); // Busca relación tema-parametro

        if ($pivot) {
            return $pivot->id; // Devuelve FK válida para programas
        }

        return $this->vincularTemaNiveles($parametroId); // Crea pivot si faltaba
    }

    /** Sincroniza parametro con tema de niveles y retorna pivot id. */
    private function vincularTemaNiveles(int $parametroId): int
    {
        $tema = Tema::find(self::TEMA_NIVELES); // Obtiene tema niveles
        if (! $tema) {
            throw new \RuntimeException('Tema NIVELES DE FORMACION (id 6) no existe. Ejecute TemaSeeder.');
        }

        $tema->parametros()->syncWithoutDetaching([$parametroId => ['status' => 1]]); // Crea/activa pivot

        return (int) ParametroTema::where('tema_id', self::TEMA_NIVELES)
            ->where('parametro_id', $parametroId)
            ->value('id'); // ID pivot recién creado
    }
}
