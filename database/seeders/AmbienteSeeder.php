<?php

namespace Database\Seeders;

use App\Models\Ambiente;
use Database\Seeders\Concerns\TruncatesTables;
use Database\Seeders\Data\AmbienteDefinitions;
use Illuminate\Database\Seeder;

class AmbienteSeeder extends Seeder
{
    use TruncatesTables;

    public function run(): void
    {
        $this->truncateModel(Ambiente::class);

        foreach (AmbienteDefinitions::all() as $ambiente) {
            Ambiente::create([
                'id' => $ambiente['id'],
                'title' => $ambiente['title'],
                'piso_id' => $ambiente['piso_id'],
                'user_create_id' => 1,
                'user_edit_id' => 1,
                'status' => 1,
            ]);
        }
    }
}
