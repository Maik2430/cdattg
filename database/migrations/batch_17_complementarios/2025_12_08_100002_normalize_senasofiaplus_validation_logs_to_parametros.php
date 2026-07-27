<?php

use App\Models\Parametro;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLE = 'senasofiaplus_validation_logs';

    private const SQL_ALTER_TABLE = 'ALTER TABLE ';

    private const SQL_UPDATE = 'UPDATE ';

    /**
     * Elimina índices compuestos que impiden modificar columnas accion/resultado en SQLite.
     */
    private function dropAccionResultadoIndexes(): void
    {
        if (! Schema::hasTable(self::TABLE)) {
            return;
        }

        foreach ([['accion', 'resultado'], 'senasofiaplus_validation_logs_accion_resultado_index'] as $index) {
            try {
                Schema::table(self::TABLE, function (Blueprint $table) use ($index) {
                    $table->dropIndex($index);
                });
            } catch (Throwable) {
                // El índice puede no existir o tener otro nombre según el driver.
            }
        }
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $parametros = $this->getValidationParametros();

        if ($parametros === null) {
            $this->upStructureOnly();

            return;
        }

        $this->upWithDataMigration($parametros);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $parametros = $this->getValidationParametros();

        if ($parametros === null) {
            return;
        }

        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropIndex(['accion', 'resultado']);
        });

        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropForeign(['accion']);
            $table->dropForeign(['resultado']);
        });

        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->enum('accion_tmp', ['validar'])->after('accion');
            $table->enum('resultado_tmp', ['exitoso', 'error', 'advertencia'])->after('resultado');
        });

        $this->revertAccionResultadoData($parametros);
        $this->revertColumnsToEnum();

        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->index(['accion', 'resultado']);
        });
    }

    private function getValidationParametros(): ?array
    {
        $validar = Parametro::find(280);
        $exitoso = Parametro::find(281);
        $error = Parametro::find(282);
        $advertencia = Parametro::find(283);

        if (! $validar || ! $exitoso || ! $error || ! $advertencia) {
            return null;
        }

        return compact('validar', 'exitoso', 'error', 'advertencia');
    }

    private function upStructureOnly(): void
    {
        if (! Schema::hasTable(self::TABLE)) {
            return;
        }

        if (DB::getDriverName() === 'sqlite') {
            $this->dropAccionResultadoIndexes();
            $this->convertStringColumnToBigintNullableForSqlite('accion', 'aspirante_id');
            $this->convertStringColumnToBigintNullableForSqlite('resultado', 'accion');
        } else {
            $this->convertColumnsToBigintNullableForMysql();
        }

        $this->addAccionResultadoForeignKeysIfPossible();
    }

    private function convertStringColumnToBigintNullableForSqlite(string $column, string $afterColumn): void
    {
        if (! Schema::hasColumn(self::TABLE, $column)) {
            return;
        }

        $newColumn = "{$column}_new";

        Schema::table(self::TABLE, function (Blueprint $table) use ($newColumn, $afterColumn) {
            $table->unsignedBigInteger($newColumn)->nullable()->after($afterColumn);
        });
        DB::statement(self::SQL_UPDATE.self::TABLE." SET {$newColumn} = NULL");
        Schema::table(self::TABLE, function (Blueprint $table) use ($column) {
            $table->dropColumn($column);
        });

        try {
            DB::statement(self::SQL_ALTER_TABLE.self::TABLE." RENAME COLUMN {$newColumn} TO {$column}");
        } catch (Exception) {
            Schema::table(self::TABLE, function (Blueprint $table) use ($column, $afterColumn) {
                $table->unsignedBigInteger($column)->nullable()->after($afterColumn);
            });
            Schema::table(self::TABLE, function (Blueprint $table) use ($newColumn) {
                $table->dropColumn($newColumn);
            });
        }
    }

    private function convertColumnsToBigintNullableForMysql(): void
    {
        foreach (['accion', 'resultado'] as $column) {
            if (Schema::hasColumn(self::TABLE, $column)) {
                DB::statement(self::SQL_ALTER_TABLE.self::TABLE." MODIFY COLUMN {$column} BIGINT UNSIGNED NULL");
            }
        }
    }

    private function addAccionResultadoForeignKeysIfPossible(): void
    {
        try {
            $this->addAccionResultadoForeignKeys();
        } catch (Exception) {
            // Si falla, se agregará después cuando existan los parámetros
        }
    }

    private function upWithDataMigration(array $parametros): void
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->unsignedBigInteger('accion_parametro_id')->nullable()->after('accion');
            $table->unsignedBigInteger('resultado_parametro_id')->nullable()->after('resultado');
        });

        $this->migrateAccionResultadoToParametroIds($parametros);
        $this->dropAccionResultadoCompositeIndex();
        $this->replaceColumnsWithParametroIds();
        $this->addAccionResultadoForeignKeysRequired();
        $this->recreateAccionResultadoIndex();
    }

    private function migrateAccionResultadoToParametroIds(array $parametros): void
    {
        DB::table(self::TABLE)
            ->where('accion', 'validar')
            ->update(['accion_parametro_id' => $parametros['validar']->id]);

        DB::table(self::TABLE)
            ->where('resultado', 'exitoso')
            ->update(['resultado_parametro_id' => $parametros['exitoso']->id]);

        DB::table(self::TABLE)
            ->where('resultado', 'error')
            ->update(['resultado_parametro_id' => $parametros['error']->id]);

        DB::table(self::TABLE)
            ->where('resultado', 'advertencia')
            ->update(['resultado_parametro_id' => $parametros['advertencia']->id]);
    }

    private function dropAccionResultadoCompositeIndex(): void
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropIndex(['accion', 'resultado']);
        });
    }

    private function replaceColumnsWithParametroIds(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            $this->replaceColumnsWithParametroIdsForSqlite();

            return;
        }

        $this->replaceColumnsWithParametroIdsForMysql();
    }

    private function replaceColumnsWithParametroIdsForSqlite(): void
    {
        $this->dropAccionResultadoIndexes();

        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->unsignedBigInteger('accion_new')->after('aspirante_id');
            $table->unsignedBigInteger('resultado_new')->after('accion_new');
        });

        DB::statement(self::SQL_UPDATE.self::TABLE.' SET accion_new = accion_parametro_id, resultado_new = resultado_parametro_id');

        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn(['accion', 'resultado', 'accion_parametro_id', 'resultado_parametro_id']);
        });

        try {
            DB::statement(self::SQL_ALTER_TABLE.self::TABLE.' RENAME COLUMN accion_new TO accion');
            DB::statement(self::SQL_ALTER_TABLE.self::TABLE.' RENAME COLUMN resultado_new TO resultado');
        } catch (Exception) {
            Schema::table(self::TABLE, function (Blueprint $table) {
                $table->unsignedBigInteger('accion')->after('aspirante_id');
                $table->unsignedBigInteger('resultado')->after('accion');
            });
            DB::statement(self::SQL_UPDATE.self::TABLE.' SET accion = accion_new, resultado = resultado_new');
            Schema::table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn(['accion_new', 'resultado_new']);
            });
        }
    }

    private function replaceColumnsWithParametroIdsForMysql(): void
    {
        DB::statement(self::SQL_ALTER_TABLE.self::TABLE.' DROP COLUMN accion, DROP COLUMN resultado');
        DB::statement(self::SQL_ALTER_TABLE.self::TABLE.' CHANGE accion_parametro_id accion BIGINT UNSIGNED NOT NULL');
        DB::statement(self::SQL_ALTER_TABLE.self::TABLE.' CHANGE resultado_parametro_id resultado BIGINT UNSIGNED NOT NULL');
    }

    private function addAccionResultadoForeignKeys(): void
    {
        if (Schema::hasColumn(self::TABLE, 'accion')) {
            Schema::table(self::TABLE, function (Blueprint $table) {
                $table->foreign('accion')
                    ->references('id')
                    ->on('parametros')
                    ->onDelete('restrict');
            });
        }

        if (Schema::hasColumn(self::TABLE, 'resultado')) {
            Schema::table(self::TABLE, function (Blueprint $table) {
                $table->foreign('resultado')
                    ->references('id')
                    ->on('parametros')
                    ->onDelete('restrict');
            });
        }
    }

    private function addAccionResultadoForeignKeysRequired(): void
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->foreign('accion')
                ->references('id')
                ->on('parametros')
                ->onDelete('restrict');

            $table->foreign('resultado')
                ->references('id')
                ->on('parametros')
                ->onDelete('restrict');
        });
    }

    private function recreateAccionResultadoIndex(): void
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->index(['accion', 'resultado']);
        });
    }

    private function revertAccionResultadoData(array $parametros): void
    {
        DB::table(self::TABLE)
            ->where('accion', $parametros['validar']->id)
            ->update(['accion_tmp' => 'validar']);

        DB::table(self::TABLE)
            ->where('resultado', $parametros['exitoso']->id)
            ->update(['resultado_tmp' => 'exitoso']);

        DB::table(self::TABLE)
            ->where('resultado', $parametros['error']->id)
            ->update(['resultado_tmp' => 'error']);

        DB::table(self::TABLE)
            ->where('resultado', $parametros['advertencia']->id)
            ->update(['resultado_tmp' => 'advertencia']);
    }

    private function revertColumnsToEnum(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            Schema::table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn(['accion', 'resultado']);
            });

            Schema::table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn(['accion_tmp', 'resultado_tmp']);
            });

            Schema::table(self::TABLE, function (Blueprint $table) {
                $table->string('accion')->default('validar')->after('aspirante_id');
                $table->string('resultado')->after('accion');
            });

            return;
        }

        DB::statement(self::SQL_ALTER_TABLE.self::TABLE.' DROP COLUMN accion, DROP COLUMN resultado');
        DB::statement(self::SQL_ALTER_TABLE.self::TABLE." CHANGE accion_tmp accion ENUM('validar') NOT NULL");
        DB::statement(self::SQL_ALTER_TABLE.self::TABLE." CHANGE resultado_tmp resultado ENUM('exitoso', 'error', 'advertencia') NOT NULL");
    }
};
