<?php

namespace App\Http\Controllers\Concerns\Auth;

use App\Models\Persona;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

trait HandlesRegisterPersonaCreationHelpers
{
    private function personaExists(string $numeroDocumento, string $email): bool
    {
        return Persona::where('numero_documento', $numeroDocumento)
            ->orWhere('email', $email)
            ->exists()
            || User::where('email', $email)->exists();
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{0: Persona, 1: User}
     */
    private function createPersonaYUsuario(array $data): array
    {
        $caracterizacionIds = $this->extractCaracterizacionIds($data);

        $persona = Persona::create([
            'tipo_documento' => $data['tipo_documento'],
            'numero_documento' => $data['numero_documento'],
            'primer_nombre' => strtoupper($data['primer_nombre']),
            'segundo_nombre' => $this->normalizeOptionalUpper($data['segundo_nombre'] ?? null),
            'primer_apellido' => strtoupper($data['primer_apellido']),
            'segundo_apellido' => $this->normalizeOptionalUpper($data['segundo_apellido'] ?? null),
            'fecha_nacimiento' => $data['fecha_nacimiento'],
            'genero' => $data['genero'],
            'telefono' => $data['telefono'] ?? null,
            'celular' => $data['celular'],
            'email' => strtolower($data['email']),
            'pais_id' => $data['pais_id'],
            'departamento_id' => $data['departamento_id'],
            'municipio_id' => $data['municipio_id'],
            'direccion' => $data['direccion'],
            'user_create_id' => $this->resolveAuditableUserId(),
            'user_edit_id' => $this->resolveAuditableUserId(),
        ]);

        $user = User::create([
            'email' => strtolower($data['email']),
            'password' => Hash::make($data['numero_documento']),
            'status' => 1,
            'persona_id' => $persona->id,
        ]);

        $user->assignRole('VISITANTE');

        $this->syncCaracterizaciones($persona, $caracterizacionIds);

        return [$persona, $user];
    }

    private function normalizeOptionalUpper(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed === '' ? null : strtoupper($trimmed);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<int, int>
     */
    private function extractCaracterizacionIds(array $data): array
    {
        return collect($data['caracterizacion_ids'] ?? [])
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @param  array<int, int>  $caracterizacionIds
     */
    private function syncCaracterizaciones(Persona $persona, array $caracterizacionIds): void
    {
        $persona->caracterizacionesComplementarias()->sync($caracterizacionIds);

        if (! empty($caracterizacionIds)) {
            $persona->updateQuietly([
                'parametro_id' => $caracterizacionIds[0],
            ]);
        }
    }

    private function resolveAuditableUserId(): ?int
    {
        if (Auth::check()) {
            return Auth::id();
        }

        $candidates = collect([
            config('app.audit_default_user_id'),
            config('registro.audit_default_user_id'),
        ])->filter(fn ($id) => $id !== null)
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $resolvedId = $candidates
            ->first(fn ($candidateId) => User::whereKey($candidateId)->exists());

        if (! $resolvedId) {
            $resolvedId = User::role('ADMIN')->value('id');
        }

        if (! $resolvedId) {
            $resolvedId = User::orderBy('id')->value('id');
        }

        return $resolvedId ? (int) $resolvedId : null;
    }
}
