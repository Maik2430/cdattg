<?php

namespace Database\Seeders;

use App\Models\Persona;
use Database\Seeders\Concerns\ResolvesParametroTema;
use Database\Seeders\Concerns\TruncatesTables;
use Database\Seeders\Data\DemoPersonaDefinitions;
use Illuminate\Database\Seeder;

class PersonaSeeder extends Seeder
{
    use ResolvesParametroTema;
    use TruncatesTables;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->truncateModel(Persona::class);

        $tipoDocumentoCedula = $this->getParametroTemaId(2, 3);
        $generoMasculino = $this->getParametroTemaId(3, 9);
        $generoFemenino = $this->getParametroTemaId(3, 10);

        foreach (DemoPersonaDefinitions::all($tipoDocumentoCedula, $generoMasculino, $generoFemenino) as $persona) {
            Persona::updateOrCreate(
                ['id' => $persona['id']],
                $persona['attributes']
            );
        }
    }
}
