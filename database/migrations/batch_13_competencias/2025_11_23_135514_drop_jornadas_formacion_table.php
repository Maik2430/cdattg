<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const ON_DELETE_SET_NULL = 'set null';

    private const JORNADA_ID_COLUMN = 'jornada_id';

    private const TABLES_WITH_JORNADA_ID = [
        'fichas_caracterizacion',
        'caracterizacion_programas',
        'complementarios_ofertados',
        'ambiente_instructor_ficha',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            $this->upForSqlite();

            return;
        }

        $this->upForMysql();
    }

    /**
     * SQLite no soporta ALTER TABLE ... ADD FOREIGN KEY ni MODIFY.
     * Usamos el schema builder de Laravel para retargetear FKs y renombrar tablas.
     */
    private function upForSqlite(): void
    {
        foreach (self::TABLES_WITH_JORNADA_ID as $tableName) {
            $this->retargetJornadaForeignKeyToParametrosTema($tableName);
        }

        $this->migrateInstructorJornadaFormacionForSqlite();
        Schema::dropIfExists('jornadas_formacion');
    }

    private function migrateInstructorJornadaFormacionForSqlite(): void
    {
        if (! Schema::hasTable('instructor_jornada_formacion') || Schema::hasTable('instructor_parametro_tema')) {
            return;
        }

        Schema::disableForeignKeyConstraints();

        try {
            Schema::table('instructor_jornada_formacion', function (Blueprint $table) {
                $table->dropForeign(['jornada_formacion_id']);
            });
        } catch (Throwable) {
            // La FK puede no existir o tener otro nombre en entornos de testing.
        }

        Schema::enableForeignKeyConstraints();

        Schema::table('instructor_jornada_formacion', function (Blueprint $table) {
            $table->renameColumn('jornada_formacion_id', 'parametro_tema_id');
        });

        Schema::rename('instructor_jornada_formacion', 'instructor_parametro_tema');

        Schema::table('instructor_parametro_tema', function (Blueprint $table) {
            $table->foreign('parametro_tema_id')
                ->references('id')
                ->on('parametros_temas')
                ->cascadeOnDelete();
        });
    }

    private function retargetJornadaForeignKeyToParametrosTema(string $tableName, string $column = self::JORNADA_ID_COLUMN): void
    {
        if (! Schema::hasTable($tableName) || ! Schema::hasColumn($tableName, $column)) {
            return;
        }

        Schema::disableForeignKeyConstraints();

        try {
            Schema::table($tableName, function (Blueprint $table) use ($column) {
                $table->dropForeign([$column]);
            });
        } catch (Throwable) {
            // La FK puede no existir o tener otro nombre en entornos de testing.
        }

        Schema::table($tableName, function (Blueprint $table) use ($column) {
            $table->foreign($column)
                ->references('id')
                ->on('parametros_temas')
                ->nullOnDelete();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Cambiar foreign keys de jornadas_formacion a parametros_temas (MySQL/MariaDB).
     */
    private function upForMysql(): void
    {
        foreach (self::TABLES_WITH_JORNADA_ID as $tableName) {
            $this->retargetJornadaForeignKeyForMysql($tableName);
        }

        $this->migrateInstructorJornadaFormacionForMysql();
        Schema::dropIfExists('jornadas_formacion');
    }

    private function retargetJornadaForeignKeyForMysql(string $tableName, string $column = self::JORNADA_ID_COLUMN): void
    {
        if (! Schema::hasTable($tableName) || ! Schema::hasColumn($tableName, $column)) {
            return;
        }

        $this->makeColumnNullableForMysql($tableName, $column, $tableName === 'fichas_caracterizacion');
        $this->dropForeignKeyOnColumn($tableName, $column, "{$tableName}_{$column}_foreign");
        $this->addForeignKeyToParametrosTema($tableName, $column);
    }

    private function migrateInstructorJornadaFormacionForMysql(): void
    {
        if (! Schema::hasTable('instructor_jornada_formacion')) {
            return;
        }

        $this->dropForeignKeyOnColumn(
            'instructor_jornada_formacion',
            'jornada_formacion_id',
            'instructor_jornada_formacion_jornada_formacion_id_foreign'
        );

        DB::statement('ALTER TABLE instructor_jornada_formacion CHANGE jornada_formacion_id parametro_tema_id BIGINT UNSIGNED');
        $this->addForeignKeyToParametrosTema('instructor_jornada_formacion', 'parametro_tema_id', true);
        Schema::rename('instructor_jornada_formacion', 'instructor_parametro_tema');
    }

    private function makeColumnNullableForMysql(string $tableName, string $column, bool $suppressErrors = false): void
    {
        if ($suppressErrors) {
            try {
                DB::statement("ALTER TABLE {$tableName} MODIFY {$column} BIGINT UNSIGNED NULL");
            } catch (Exception) {
                // Continuar si ya es nullable o si hay otro error
            }

            return;
        }

        DB::statement("ALTER TABLE {$tableName} MODIFY {$column} BIGINT UNSIGNED NULL");
    }

    private function dropForeignKeyOnColumn(string $tableName, string $column, string $defaultConstraintName): void
    {
        try {
            DB::statement("ALTER TABLE {$tableName} DROP FOREIGN KEY {$defaultConstraintName}");
        } catch (Exception) {
            $this->dropForeignKeysFromInformationSchema($tableName, $column);
        }
    }

    private function dropForeignKeysFromInformationSchema(string $tableName, string $column): void
    {
        try {
            $constraints = DB::select("
                SELECT CONSTRAINT_NAME
                FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = ?
                AND COLUMN_NAME = ?
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ", [$tableName, $column]);

            foreach ($constraints as $constraint) {
                DB::statement("ALTER TABLE {$tableName} DROP FOREIGN KEY {$constraint->CONSTRAINT_NAME}");
            }
        } catch (Exception) {
            // Continuar si no se puede eliminar
        }
    }

    private function addForeignKeyToParametrosTema(string $tableName, string $column, bool $cascadeOnDelete = false): void
    {
        $constraintName = "{$tableName}_{$column}_foreign";
        $onDeleteClause = $cascadeOnDelete ? 'ON DELETE CASCADE' : 'ON DELETE SET NULL';

        try {
            DB::statement("ALTER TABLE {$tableName}
                ADD CONSTRAINT {$constraintName}
                FOREIGN KEY ({$column}) REFERENCES parametros_temas(id) {$onDeleteClause}");
        } catch (Exception) {
            DB::statement("ALTER TABLE {$tableName}
                ADD FOREIGN KEY ({$column}) REFERENCES parametros_temas(id) {$onDeleteClause}");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        $this->downForMysql();
    }

    private function downForMysql(): void
    {
        Schema::create('jornadas_formacion', function (Blueprint $table) {
            $table->id();
            $table->string('jornada');
            $table->time('hora_inicio')->nullable();
            $table->time('hora_fin')->nullable();
            $table->timestamps();
        });

        $this->revertInstructorParametroTemaPivot();

        foreach (self::TABLES_WITH_JORNADA_ID as $tableName) {
            $this->restoreJornadaForeignKeyOnTable($tableName);
        }
    }

    private function revertInstructorParametroTemaPivot(): void
    {
        if (! Schema::hasTable('instructor_parametro_tema')) {
            return;
        }

        Schema::rename('instructor_parametro_tema', 'instructor_jornada_formacion');

        $this->dropForeignKeyOnColumn(
            'instructor_jornada_formacion',
            'parametro_tema_id',
            'instructor_jornada_formacion_parametro_tema_id_foreign'
        );

        DB::statement('ALTER TABLE instructor_jornada_formacion CHANGE parametro_tema_id jornada_formacion_id BIGINT UNSIGNED');

        Schema::table('instructor_jornada_formacion', function (Blueprint $table) {
            $table->foreign('jornada_formacion_id')
                ->references('id')
                ->on('jornadas_formacion')
                ->onDelete('cascade');
        });
    }

    private function restoreJornadaForeignKeyOnTable(string $tableName): void
    {
        if (! Schema::hasTable($tableName) || ! Schema::hasColumn($tableName, self::JORNADA_ID_COLUMN)) {
            return;
        }

        try {
            DB::statement("ALTER TABLE {$tableName} DROP FOREIGN KEY {$tableName}_jornada_id_foreign");
        } catch (Exception) {
            // Continuar
        }

        Schema::table($tableName, function (Blueprint $table) {
            $table->foreign(self::JORNADA_ID_COLUMN)
                ->references('id')
                ->on('jornadas_formacion')
                ->onDelete(self::ON_DELETE_SET_NULL);
        });
    }
};
