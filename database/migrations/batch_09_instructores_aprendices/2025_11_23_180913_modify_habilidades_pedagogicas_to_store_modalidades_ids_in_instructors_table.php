<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const MODALIDADES_MAPPING = [
        'virtual' => 'A DISTANCIA',
        'presencial' => 'PRESENCIAL',
        'dual' => 'DUAL',
    ];

    private const TEMA_MODALIDADES = 5;

    /**
     * Run the migrations.
     *
     * Modifica la columna habilidades_pedagogicas para que almacene un array de IDs
     * de modalidades (parametros_temas con tema_id = 5) en lugar de valores hardcodeados.
     */
    public function up(): void
    {
        $modalidades = $this->getModalidadesFromDb();

        $this->migrateInstructoresHabilidades($modalidades);
        $this->addHabilidadesPedagogicasColumnComment();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir a valores hardcodeados si es necesario
        // Nota: Esta reversión es compleja porque necesitaríamos mapear IDs a nombres
        // Por ahora, solo dejamos la columna como está
    }

    private function getModalidadesFromDb(): Collection
    {
        return DB::table('parametros_temas')
            ->join('temas', 'parametros_temas.tema_id', '=', 'temas.id')
            ->join('parametros', 'parametros_temas.parametro_id', '=', 'parametros.id')
            ->where('temas.id', self::TEMA_MODALIDADES)
            ->where('parametros.status', true)
            ->where('parametros_temas.status', true)
            ->select('parametros_temas.id', 'parametros.name')
            ->get()
            ->keyBy('name');
    }

    private function migrateInstructoresHabilidades(Collection $modalidades): void
    {
        $instructores = DB::table('instructors')
            ->whereNotNull('habilidades_pedagogicas')
            ->get();

        foreach ($instructores as $instructor) {
            $this->processInstructorHabilidades($instructor, $modalidades);
        }
    }

    private function processInstructorHabilidades(object $instructor, Collection $modalidades): void
    {
        $habilidades = json_decode($instructor->habilidades_pedagogicas, true);

        if (! is_array($habilidades) || empty($habilidades)) {
            return;
        }

        $modalidadesIds = $this->resolveModalidadIds($habilidades, $modalidades);
        $this->updateInstructorHabilidades($instructor->id, $modalidadesIds);
    }

    private function resolveModalidadIds(array $habilidades, Collection $modalidades): array
    {
        $modalidadesIds = [];

        foreach ($habilidades as $habilidad) {
            $modalidadId = $this->resolveHabilidadModalidadId($habilidad, $modalidades);

            if ($modalidadId !== null) {
                $modalidadesIds[] = $modalidadId;
            }
        }

        return $modalidadesIds;
    }

    private function resolveHabilidadModalidadId(mixed $habilidad, Collection $modalidades): ?int
    {
        if (is_numeric($habilidad)) {
            return (int) $habilidad;
        }

        if (! is_string($habilidad)) {
            return null;
        }

        $nombreModalidad = self::MODALIDADES_MAPPING[strtolower($habilidad)] ?? null;
        $modalidad = $nombreModalidad !== null ? $modalidades->get($nombreModalidad) : null;

        return $modalidad?->id;
    }

    private function updateInstructorHabilidades(int $instructorId, array $modalidadesIds): void
    {
        $value = ! empty($modalidadesIds)
            ? json_encode(array_unique($modalidadesIds))
            : null;

        DB::table('instructors')
            ->where('id', $instructorId)
            ->update(['habilidades_pedagogicas' => $value]);
    }

    private function addHabilidadesPedagogicasColumnComment(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE `instructors` MODIFY COLUMN `habilidades_pedagogicas` JSON NULL COMMENT 'Array de IDs de modalidades (parametros_temas con tema_id = 5) - MODALIDADES DE FORMACION'");
    }
};
