<?php

namespace Database\Seeders;

use App\Models\Tema;
use Database\Seeders\Concerns\TruncatesTables;
use Database\Seeders\Data\TemaDefinitions;
use Illuminate\Database\Seeder;

class TemaSeeder extends Seeder
{
    use TruncatesTables;

    public function run(): void
    {
        $this->resetTables();

        foreach (TemaDefinitions::all() as $config) {
            $this->createTemaWithParametros(
                $config['id'],
                $config['name'],
                $config['paramIds']
            );
        }
    }

    private function resetTables(): void
    {
        if (app()->environment('production')) {
            return;
        }

        $this->truncateModel(Tema::class);
        $this->truncateTable('parametros_temas');
    }

    private function createTemaWithParametros(int $id, string $name, array $paramIds): void
    {
        $tema = Tema::query()->updateOrCreate(
            ['id' => $id],
            [
                'name' => $name,
                'status' => 1,
                'user_create_id' => null,
                'user_edit_id' => null,
            ]
        );

        $tema->parametros()->sync($this->buildSyncData($paramIds));
    }

    /** @param list<int|list<int>> $paramIds
     * @return array<int, array{status: int}>
     */
    private function buildSyncData(array $paramIds): array
    {
        $syncData = [];

        foreach ($paramIds as $paramId) {
            if (is_array($paramId)) {
                foreach ($paramId as $nestedId) {
                    $syncData[$nestedId] = ['status' => 1];
                }

                continue;
            }

            $syncData[$paramId] = ['status' => 1];
        }

        return $syncData;
    }
}
