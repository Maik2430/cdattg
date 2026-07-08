<?php

namespace Database\Seeders;

use App\Models\Parametro;
use Database\Seeders\Concerns\TruncatesTables;
use Database\Seeders\Data\ParametroDefinitions;
use Exception;
use Illuminate\Database\Seeder;

class ParametroSeeder extends Seeder
{
    use TruncatesTables;

    public function run(): void
    {
        $this->resetTable();

        foreach (ParametroDefinitions::all() as $parametro) {
            Parametro::updateOrCreate(
                ['id' => $parametro['id']],
                [
                    'name' => $parametro['name'],
                    'status' => $parametro['status'],
                    'user_create_id' => $parametro['user_create_id'],
                    'user_edit_id' => $parametro['user_edit_id'],
                ]
            );
        }
    }

    private function resetTable(): void
    {
        if (app()->environment('production')) {
            return;
        }

        if (app()->environment('testing')) {
            try {
                $this->truncateModel(Parametro::class);
            } catch (Exception) {
                // RefreshDatabase limpia al final del test
            }

            return;
        }

        $this->truncateModel(Parametro::class);
    }
}
