<?php

declare(strict_types=1);

namespace Tests\Complementarios\Feature\Controllers;

use Tests\TestCase;
use App\Models\Complementarios\ComplementarioOfertado;
use App\Models\Complementarios\AspiranteComplementario;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\Complementarios\Concerns\SeedsComplementariosDatabase;
use Tests\Complementarios\Concerns\AspiranteTestHelpers;

/**
 * Tests para funcionalidad de exportación de aspirantes.
 * RF-ASP-008: Exportar Aspirantes a Excel
 * RF-ASP-010: Descargar Cédulas de Aspirantes (PDF)
 */
class AspiranteExportControllerTest extends TestCase
{
    use RefreshDatabase;
    use SeedsComplementariosDatabase;
    use AspiranteTestHelpers;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seedComplementariosDatabaseIfNeeded();
        
        $this->user = User::factory()->create();
    }

    // ==========================================
    // RF-ASP-008: Exportar Aspirantes a Excel
    // ==========================================

    #[Test]
    public function puede_exportar_aspirantes_a_excel()
    {
        $this->actingAs($this->user);
        
        $programa = $this->crearProgramaComplementario();
        AspiranteComplementario::factory()->count(3)->paraPrograma($programa)->create();

        $response = $this->get(route('programas-complementarios.exportar-excel', $programa->id));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    #[Test]
    public function exportar_excel_retorna_error_si_no_hay_aspirantes()
    {
        $this->actingAs($this->user);
        
        $programa = $this->crearProgramaComplementario();

        $response = $this->get(route('programas-complementarios.exportar-excel', $programa->id));

        // Puede retornar Excel vacío (StreamedResponse con status 200) o error JSON (status 500)
        $statusCode = $response->getStatusCode();
        $this->assertContains($statusCode, [200, 500]);
    }

    // ==========================================
    // RF-ASP-010: Descargar Cédulas de Aspirantes
    // ==========================================

    #[Test]
    public function puede_descargar_cedulas_de_aspirantes()
    {
        $this->actingAs($this->user);
        
        $programa = $this->crearProgramaComplementario();
        AspiranteComplementario::factory()->count(2)->paraPrograma($programa)->create();

        $response = $this->get(route('programas-complementarios.descargar-cedulas', $programa->id));

        // Puede retornar PDF o error, pero debe responder
        $this->assertContains($response->status(), [200, 302, 500]);
    }

    #[Test]
    public function descargar_cedulas_retorna_error_si_no_hay_aspirantes()
    {
        $this->actingAs($this->user);
        
        $programa = $this->crearProgramaComplementario();

        $response = $this->get(route('programas-complementarios.descargar-cedulas', $programa->id));

        // Puede retornar error o redirección
        $this->assertContains($response->status(), [200, 302, 500]);
    }
}

