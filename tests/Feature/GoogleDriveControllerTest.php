<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GoogleDriveControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            \Database\Seeders\RolePermissionSeeder::class,
        ]);

        User::factory()->create();

        config([
            'filesystems.disks.google.clientId' => 'test-client-id',
            'filesystems.disks.google.clientSecret' => 'test-client-secret',
            'filesystems.disks.google.refreshToken' => 'test-refresh-token',
            'filesystems.disks.google.folderId' => 'test-folder-id',
        ]);
    }

    #[Test]
    public function connect_redirige_a_google_oauth(): void
    {
        $response = $this->get(route('google.drive.connect'));

        $response->assertRedirect();
        $this->assertStringContainsString('accounts.google.com', $response->headers->get('Location'));
    }

    #[Test]
    public function callback_sin_code_retorna_400(): void
    {
        $response = $this->getJson(route('google.drive.callback'));

        $response->assertStatus(400);
        $response->assertJson([
            'success' => false,
            'message' => 'Falta el parámetro "code" en la URL',
        ]);
    }

    #[Test]
    public function test_conectividad_exitosa_con_storage_fake(): void
    {
        Storage::fake('google');

        $response = $this->getJson(route('google.drive.test'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $response->assertJsonStructure(['success', 'path']);
        Storage::disk('google')->assertExists($response->json('path'));
    }

    #[Test]
    public function test_conectividad_falla_cuando_storage_lanza_error(): void
    {
        Storage::shouldReceive('disk')
            ->once()
            ->with('google')
            ->andThrow(new \RuntimeException('Drive unavailable'));

        $response = $this->getJson(route('google.drive.test'));

        $response->assertStatus(500);
        $response->assertJson([
            'success' => false,
            'error' => 'Error al conectar con Google Drive.',
        ]);
    }
}
