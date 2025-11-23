<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Modifica la columna habilidades_pedagogicas para que almacene un array de IDs
     * de modalidades (parametros_temas con tema_id = 5) en lugar de valores hardcodeados.
     */
    public function up(): void
    {
        Schema::table('instructors', function (Blueprint $table) {
            // La columna ya existe como JSON, solo actualizamos el comentario
            // y migramos los datos existentes si los hay
        });
        
        // Migrar datos existentes: convertir valores hardcodeados a IDs de modalidades
        // Buscar modalidades que coincidan con los valores antiguos
        $modalidadesMapping = [
            'virtual' => 'A DISTANCIA',
            'presencial' => 'PRESENCIAL',
            'dual' => 'DUAL'
        ];
        
        // Obtener las modalidades desde la base de datos
        $modalidades = DB::table('parametros_temas')
            ->join('temas', 'parametros_temas.tema_id', '=', 'temas.id')
            ->join('parametros', 'parametros_temas.parametro_id', '=', 'parametros.id')
            ->where('temas.id', 5) // MODALIDADES DE FORMACION
            ->where('parametros.status', true)
            ->where('parametros_temas.status', true)
            ->select('parametros_temas.id', 'parametros.name')
            ->get()
            ->keyBy('name');
        
        // Actualizar instructores que tengan habilidades_pedagogicas con valores antiguos
        $instructores = DB::table('instructors')
            ->whereNotNull('habilidades_pedagogicas')
            ->get();
        
        foreach ($instructores as $instructor) {
            $habilidades = json_decode($instructor->habilidades_pedagogicas, true);
            
            if (is_array($habilidades) && !empty($habilidades)) {
                $modalidadesIds = [];
                
                foreach ($habilidades as $habilidad) {
                    // Si es un string (valor antiguo), buscar la modalidad correspondiente
                    if (is_string($habilidad)) {
                        $nombreModalidad = $modalidadesMapping[strtolower($habilidad)] ?? null;
                        if ($nombreModalidad && isset($modalidades[$nombreModalidad])) {
                            $modalidadesIds[] = $modalidades[$nombreModalidad]->id;
                        }
                    } 
                    // Si ya es un ID numérico, mantenerlo
                    elseif (is_numeric($habilidad)) {
                        $modalidadesIds[] = (int)$habilidad;
                    }
                }
                
                // Actualizar solo si hay IDs válidos
                if (!empty($modalidadesIds)) {
                    DB::table('instructors')
                        ->where('id', $instructor->id)
                        ->update([
                            'habilidades_pedagogicas' => json_encode(array_unique($modalidadesIds))
                        ]);
                } else {
                    // Si no se encontraron modalidades, limpiar el campo
                    DB::table('instructors')
                        ->where('id', $instructor->id)
                        ->update([
                            'habilidades_pedagogicas' => null
                        ]);
                }
            }
        }
        
        // Agregar comentario a la columna
        DB::statement("ALTER TABLE `instructors` MODIFY COLUMN `habilidades_pedagogicas` JSON NULL COMMENT 'Array de IDs de modalidades (parametros_temas con tema_id = 5) - MODALIDADES DE FORMACION'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir a valores hardcodeados si es necesario
        // Nota: Esta reversión es compleja porque necesitaríamos mapear IDs a nombres
        // Por ahora, solo dejamos la columna como está
        Schema::table('instructors', function (Blueprint $table) {
            // No hacemos cambios en el down para evitar pérdida de datos
        });
    }
};
