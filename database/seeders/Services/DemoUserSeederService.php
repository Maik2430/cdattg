<?php

namespace Database\Seeders\Services;

use App\Models\User;
use Database\Seeders\Data\DemoUserDefinitions;
use Illuminate\Support\Facades\Hash;

/**
 * Persiste usuarios demo y asigna roles Spatie sin duplicar lógica en el seeder.
 */
final class DemoUserSeederService
{
    public function seedAll(): void
    {
        foreach (DemoUserDefinitions::all() as $userData) {
            $this->createOrUpdateUser($userData);
        }
    }

    /**
     * @param  array{email: string, password: string, persona_id: int, role: string}  $userData
     */
    private function createOrUpdateUser(array $userData): void
    {
        $user = User::updateOrCreate(
            ['email' => $userData['email']],
            [
                'password' => Hash::make($userData['password']),
                'status' => 1,
                'persona_id' => $userData['persona_id'],
                'email_verified_at' => now(),
            ]
        );

        if (!$user->hasRole($userData['role'])) {
            $user->assignRole($userData['role']);
        }
    }
}
