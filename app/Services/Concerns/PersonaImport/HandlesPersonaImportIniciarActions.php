<?php

namespace App\Services\Concerns\PersonaImport;

use App\Jobs\ProcessPersonaImportJob;
use App\Models\PersonaImport;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

trait HandlesPersonaImportIniciarActions
{
    public function iniciarImportacion(UploadedFile $file, int $userId): PersonaImport
    {
        $timestamp = now()->format('Ymd_His');
        $extension = strtolower($file->getClientOriginalExtension() ?: 'xlsx');
        $baseName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $baseName = $baseName ? $baseName : 'archivo';
        $fileName = "{$timestamp}_usuario{$userId}_{$baseName}.{$extension}";

        $storedPath = $file->storeAs('carga_masiva', $fileName, 'local');

        $import = PersonaImport::create([
            'user_id' => $userId,
            'original_name' => $file->getClientOriginalName(),
            'disk' => 'local',
            'path' => $storedPath,
        ]);

        ProcessPersonaImportJob::dispatch($import->id);

        return $import;
    }
}
