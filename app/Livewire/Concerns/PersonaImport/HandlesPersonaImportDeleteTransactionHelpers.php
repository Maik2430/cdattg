<?php

namespace App\Livewire\Concerns\PersonaImport;

use App\Models\PersonaImport;
use Illuminate\Support\Facades\DB;

trait HandlesPersonaImportDeleteTransactionHelpers
{
    private function eliminarImportacionEnTransaccion(PersonaImport $importacion): void
    {
        DB::transaction(function () use ($importacion) {
            $importacion->issues()->delete();
            $importacion->contactAlerts()->delete();

            DB::table('jobs')
                ->where('queue', 'long-running')
                ->where('payload', 'like', self::PATRON_PAYLOAD_IMPORT_ID.$importacion->id.'%')
                ->delete();

            DB::table('failed_jobs')
                ->where('queue', 'long-running')
                ->where('payload', 'like', self::PATRON_PAYLOAD_IMPORT_ID.$importacion->id.'%')
                ->delete();

            $importacion->delete();
        });
    }
}
