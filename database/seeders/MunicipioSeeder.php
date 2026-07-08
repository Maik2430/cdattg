<?php

namespace Database\Seeders;

use App\Models\Municipio;
use Database\Seeders\Concerns\TruncatesTables;
use Database\Seeders\Data\MunicipioDefinitions;
use Illuminate\Database\Seeder;

class MunicipioSeeder extends Seeder
{
    use TruncatesTables;

    public function run(): void
    {
        $this->truncateModel(Municipio::class);

        foreach (MunicipioDefinitions::all() as $municipio) {
            Municipio::updateOrCreate(
                [
                    'municipio' => $municipio['municipio'],
                    'departamento_id' => $municipio['departamento_id'],
                ],
                ['status' => $municipio['status']]
            );
        }
    }
}
