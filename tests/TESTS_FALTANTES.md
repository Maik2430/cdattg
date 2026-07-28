# Resumen de Tests Faltantes - Actualizado

## Estado Actual

- **Controladores**: 70 archivos, **~41+ Feature tests** ✅ remanentes críticos cubiertos
- **Modelos**: modelos activos con `*ModelTest` ✅ (ProveedorContacto N/A — tabla dropeada)
- **Servicios**: 48 archivos, **49 tests** (102% cobertura) ✅ **0 faltantes**
- **Repositorios**: 45 archivos, **46 tests** (102% cobertura) ✅ **0 faltantes**
- **Form Requests**: 53 archivos, **53 tests** (100% cobertura) ✅ **0 faltantes**
- **Policies**: 23 archivos, **23 tests** (100% cobertura) ✅ **0 faltantes**
- **Jobs**: 8 archivos, **8 tests** (100% cobertura) ✅ **0 faltantes**
- **Commands**: 28 archivos, **28 tests** (100% cobertura) ✅ **0 faltantes**
- **Events**: 7 archivos, **7 tests** (100% cobertura) ✅ **0 faltantes**
- **Listeners**: 1 archivo, **1 test** (100% cobertura) ✅ **0 faltantes**
- **Observers**: 5 archivos, **5 tests** (100% cobertura) ✅ **0 faltantes**

**Total de archivos de test: 317**

## Tests Completados ✅

### Feature Tests - Controladores (37 tests)

#### Prioridad ALTA - ✅ COMPLETADO
1. ✅ FichaCaracterizacionControllerTest
2. ✅ PersonaControllerTest
3. ✅ ProgramaFormacionControllerTest
4. ✅ Inventario/ProductoControllerTest
5. ✅ Inventario/OrdenControllerTest
6. ✅ Inventario/AprobacionControllerTest
7. ✅ CarnetControllerTest
8. ✅ AsignacionInstructorControllerTest
9. ✅ InstructorControllerTest

#### Prioridad MEDIA - ✅ COMPLETADO
10. ✅ CompetenciaControllerTest
11. ✅ GuiaAprendizajeControllerTest
12. ✅ RegistroAsistenciaControllerTest
13. ✅ EstadisticasControllerTest
14. ✅ EntradaSalidaControllerTest
15. ✅ CentroFormacionControllerTest
16. ✅ AmbienteControllerTest
17. ✅ BloqueControllerTest
18. ✅ PisoControllerTest
19. ✅ SedeControllerTest
20. ✅ RegionalControllerTest
21. ✅ RedConocimientoControllerTest
22. ✅ JornadaControllerTest
23. ✅ TemaControllerTest
24. ✅ ParametroControllerTest
25. ✅ UserControllerTest
26. ✅ ProfileControllerTest

#### Prioridad BAJA - ✅ COMPLETADO
27. ✅ LoginControllerTest
28. ✅ LogoutControllerTest
29. ✅ ConfiguracionControllerTest
30. ✅ PermisoControllerTest
31. ✅ MunicipioControllerTest
32. ✅ DepartamentoControllerTest
33. ✅ PaisControllerTest

#### Tests Originales
34. ✅ AprendizControllerTest
35. ✅ AsistenciaControllerTest
36. ✅ PersonaImportControllerTest
37. ✅ ReporteControllerTest
38. ✅ ResultadosAprendizajeCrudTest
39. ✅ ValidacionesFichaCaracterizacionTest

### Unit Tests - Jobs (8/8) ✅ COMPLETADO
1. ✅ GenerarCarnetsMasivosJobTest
2. ✅ GenerarReporteAsistenciaJobTest
3. ✅ ProcessPersonaImportJobTest
4. ✅ ValidarDocumentoJobTest
5. ✅ ValidarSofiaJobTest
6. ✅ ProcesarAsistenciasMasivasJobTest
7. ✅ EnviarNotificacionMasivaJobTest
8. ✅ TestJobTest

### Unit Tests - Events (7/7) ✅ COMPLETADO
1. ✅ NuevaAsistenciaRegistradaTest
2. ✅ FichaAsignadaAInstructorTest
3. ✅ AprendizAsignadoAFichaTest
4. ✅ AsistenciaCreatedTest
5. ✅ QrScannedTest
6. ✅ EstadisticasVisitantesActualizadasTest
7. ✅ VisitanteActualizadoTest

### Unit Tests - Observers (5/5) ✅ COMPLETADO
1. ✅ AprendizObserverTest
2. ✅ InstructorObserverTest
3. ✅ FichaCaracterizacionObserverTest
4. ✅ AsistenciaAprendizObserverTest
5. ✅ ProgramaFormacionObserverTest

### Unit Tests - Listeners (1/1) ✅ COMPLETADO
1. ✅ EnviarNotificacionFichaAsignadaTest

### Unit Tests - Policies (23/23) ✅ COMPLETADO
1. ✅ PersonaPolicyTest
2. ✅ FichaCaracterizacionPolicyTest
3. ✅ InstructorPolicyTest
4. ✅ ProgramaFormacionPolicyTest
5. ✅ CompetenciaPolicyTest
6. ✅ AprendizPolicyTest
7. ✅ AmbientePolicyTest
8. ✅ SedePolicyTest
9. ✅ RegionalPolicyTest
10. ✅ BloquePolicyTest
11. ✅ PisoPolicyTest
12. ✅ RedConocimientoPolicyTest
13. ✅ TemaPolicyTest
14. ✅ ParametroPolicyTest
15. ✅ GuiaAprendizajePolicyTest
16. ✅ PaisPolicyTest
17. ✅ MunicipioPolicyTest
18. ✅ DepartamentoPolicyTest
19. ✅ EntradaSalidaPolicyTest
20. ✅ EvidenciasPolicyTest
21. ✅ LoginPolicyTest
22. ✅ RegistroActividadesPolicyTest
23. ✅ ResultadosAprendizajePolicyTest

### Unit Tests - Commands (28/28) ✅ COMPLETADO
1. ✅ ProcesarSalidasPendientesCommandTest
2. ✅ MigrateModuleCommandTest
3. ✅ TestEmailCommandTest
4. ✅ CheckUserPermissionsCommandTest
5. ✅ CheckUploadLimitsCommandTest
6. ✅ ValidarFichasCaracterizacionCommandTest
7. ✅ GenerarEstadisticasCommandTest
8. ✅ ValidarSofiaCommandTest
9. ✅ BackfillSenaBarcodesCommandTest
10. ✅ CacheWarmupCommandTest
11. ✅ VerifyUserEmailCommandTest
12. ✅ VerificarTiposDocumentoCommandTest
13. ✅ AsignarTipoDocumentoPorDefectoCommandTest
14. ✅ CleanDuplicateRolesCommandTest
15. ✅ FixInstructorRolesCommandTest
16. ✅ CheckInstructorRolesCommandTest
17. ✅ VerificarIntegridadAprendicesCommandTest
18. ✅ VerificarRelacionPersonaCommandTest
19. ✅ TestWebSocketCommandTest
20. ✅ RefactorSonarQubeCommandTest
21. ✅ TestNotificacionesCommandTest
22. ✅ VerNotificacionesCommandTest
23. ✅ DebugListadoAprendicesCommandTest
24. ✅ EliminarPersonasDespuesDeCommandTest
25. ✅ RegistrarAsistenciaPruebaCommandTest
26. ✅ ListarAprendicesProblematicosCommandTest
27. ✅ TestUserPermissionsCommandTest
28. ✅ ProbarRelacionesAprendizCommandTest

### Unit Tests - Servicios (49/48) ✅ COMPLETADO (102%)

#### Prioridad ALTA - ✅ COMPLETADO
1. ✅ PersonaServiceTest
2. ✅ FichaCaracterizacionValidationServiceTest
3. ✅ CarnetServiceTest
4. ✅ AsistenciaServiceTest
5. ✅ InstructorServiceTest
6. ✅ CompetenciaServiceTest
7. ✅ GuiaAprendizajeServiceTest

#### Prioridad MEDIA - Parcialmente completado
8. ✅ ProgramaFormacionServiceTest
9. ✅ AsignacionInstructorServiceTest
10. ✅ EntradaSalidaServiceTest
11. ✅ EstadisticasServiceTest
12. ✅ UbicacionServiceTest
13. ✅ InfraestructuraServiceTest
14. ✅ ActividadServiceTest
15. ✅ AmbienteServiceTest
16. ✅ BloqueServiceTest
17. ✅ PisoServiceTest
18. ✅ SedeServiceTest
19. ✅ RedConocimientoServiceTest
20. ✅ ParametroServiceTest
21. ✅ TemaServiceTest
22. ✅ DashboardServiceTest
23. ✅ AuthServiceTest
24. ✅ UserServiceTest
25. ✅ ProfileServiceTest
26. ✅ PermisoServiceTest

**Tests originales:**
- ✅ AprendizServiceTest
- ✅ CacheServiceTest
- ✅ FichaServiceTest
- ✅ JornadaValidationServiceTest
- ✅ NotificacionServiceTest
- ✅ ReporteServiceTest

**Tests adicionales completados:**
- ✅ ExportServiceTest
- ✅ ImportServiceTest
- ✅ PersonaImportServiceTest
- ✅ CalendarioServiceTest
- ✅ BusquedaServiceTest
- ✅ AuditoriaServiceTest
- ✅ JornadaFormacionServiceTest
- ✅ PersonaIngresoSalidaServiceTest
- ✅ AspiranteComplementarioServiceTest
- ✅ AspiranteDocumentoServiceTest
- ✅ AprendizRoleServiceTest
- ✅ AsistenceQrServiceTest
- ✅ ValidationServiceTest
- ✅ ComplementarioServiceTest
- ✅ EstadisticaComplementarioServiceTest
- ✅ InstructorFichaDiasServiceTest
- ✅ InstructorBusinessRulesServiceTest
- ✅ RegistroActividadesServicesTest

### Unit Tests - Repositorios (46/45) ✅ COMPLETADO (102%)

#### Prioridad ALTA - ✅ COMPLETADO
1. ✅ PersonaRepositoryTest
2. ✅ FichaCaracterizacionRepositoryTest
3. ✅ InstructorRepositoryTest
4. ✅ ProgramaFormacionRepositoryTest
5. ✅ AprendizRepositoryTest

#### Prioridad MEDIA - ✅ COMPLETADO
6. ✅ SedeRepositoryTest
7. ✅ TemaRepositoryTest
8. ✅ RegionalRepositoryTest
9. ✅ RedConocimientoRepositoryTest
10. ✅ AmbienteRepositoryTest
11. ✅ CompetenciaRepositoryTest
12. ✅ BloqueRepositoryTest
13. ✅ PisoRepositoryTest
14. ✅ CentroFormacionRepositoryTest
15. ✅ ParametroRepositoryTest
16. ✅ PaisRepositoryTest
17. ✅ DepartamentoRepositoryTest
18. ✅ MunicipioRepositoryTest
19. ✅ JornadaFormacionRepositoryTest
20. ✅ ResultadosAprendizajeRepositoryTest
21. ✅ GuiasAprendizajeRepositoryTest
22. ✅ ConfiguracionRepositoryTest
23. ✅ AsistenciaAprendizRepositoryTest
24. ✅ FichaRepositoryTest

**Tests originales:**
- ✅ FichaRepositoryTest (ya estaba)

**Tests adicionales completados:**
- ✅ UserRepositoryTest
- ✅ EntradaSalidaRepositoryTest
- ✅ EvidenciasRepositoryTest
- ✅ LoginRepositoryTest
- ✅ DiasFormacionRepositoryTest
- ✅ FichaDiasFormacionRepositoryTest
- ✅ InstructorFichaRepositoryTest
- ✅ InstructorFichaCaracterizacionRepositoryTest
- ✅ InstructorFichaDiasRepositoryTest
- ✅ AprendizFichaRepositoryTest
- ✅ EvidenciaGuiaAprendizajeRepositoryTest
- ✅ GuiaAprendizajeRapRepositoryTest
- ✅ GuiasResultadosRepositoryTest
- ✅ CompetenciaProgramaRepositoryTest
- ✅ ResultadosCompetenciaRepositoryTest
- ✅ AsignacionInstructorLogRepositoryTest
- ✅ RegistroActividadesRepositoryTest
- ✅ TipoProgramaRepositoryTest
- ✅ NivelFormacionRepositoryTest
- ✅ ModalidadFormacionRepositoryTest
- ✅ SenasofiaplusValidationLogRepositoryTest

### Unit Tests - Modelos (61/64) ✅ 95% cobertura - **3 faltantes**

#### Prioridad ALTA - ✅ COMPLETADO
1. ✅ PersonaModelTest
2. ✅ FichaCaracterizacionModelTest
3. ✅ InstructorModelTest
4. ✅ ProgramaFormacionModelTest
5. ✅ CompetenciaModelTest
6. ✅ Inventario/ProductoModelTest
7. ✅ Inventario/OrdenModelTest

#### Prioridad MEDIA - ✅ COMPLETADO
8. ✅ AmbienteModelTest
9. ✅ BloqueModelTest
10. ✅ PisoModelTest
11. ✅ SedeModelTest
12. ✅ RegionalModelTest
13. ✅ RedConocimientoModelTest
14. ✅ CentroFormacionModelTest
15. ✅ JornadaFormacionModelTest
16. ✅ TemaModelTest
17. ✅ ParametroModelTest
18. ✅ GuiasAprendizajeModelTest

**Tests adicionales completados:**
- ✅ ResultadosAprendizajeModelTest
- ✅ AprendizModelTest
- ✅ AprendizFichaModelTest
- ✅ AsignacionInstructorModelTest
- ✅ AsistenciaAprendizModelTest
- ✅ EntradaSalidaModelTest
- ✅ UserModelTest
- ✅ AsignacionInstructorLogModelTest
- ✅ PersonaIngresoSalidaModelTest
- ✅ DiasFormacionModelTest
- ✅ RegistroActividadesModelTest
- ✅ LoginModelTest
- ✅ ParametroTemaModelTest
- ✅ ProgramaModelTest
- ✅ SenasofiaplusValidationLogModelTest
- ✅ SofiaValidationProgressModelTest
- ✅ ReporteSalidaAutomaticaModelTest
- ✅ CategoriaCaracterizacionComplementarioModelTest
- ✅ Inventario/NotificacionModelTest
- ✅ GuiasResultadosModelTest
- ✅ EvidenciaGuiaAprendizajeModelTest
- ✅ GuiaAprendizajeRapModelTest
- ✅ PersonaImportModelTest
- ✅ PersonaImportIssueModelTest
- ✅ PersonaContactAlertModelTest
- ✅ CompetenciaProgramaModelTest
- ✅ TipoProgramaModelTest
- ✅ NivelFormacionModelTest
- ✅ ModalidadFormacionModelTest
- ✅ ResultadosCompetenciaModelTest
- ✅ InstructorFichaDiasModelTest
- ✅ InstructorFichaCaracterizacionModelTest
- ✅ FichaDiasFormacionModelTest
- ✅ EvidenciasModelTest
- ✅ AspiranteComplementarioModelTest
- ✅ ComplementarioOfertadoModelTest
- ✅ Inventario/ProveedorModelTest
- ✅ Inventario/AprobacionModelTest
- ✅ Inventario/DevolucionModelTest
- ✅ Inventario/ContratoConvenioModelTest
- ✅ Inventario/CategoriaModelTest
- ✅ Inventario/DetalleOrdenModelTest
- ✅ Inventario/MarcaModelTest

**Faltan 3 modelos:**
- (Verificar cuáles modelos no tienen test)

### Unit Tests - Form Requests (53/53) ✅ COMPLETADO (100%)

#### Prioridad ALTA - ✅ COMPLETADO
1. ✅ StoreFichaCaracterizacionRequestTest
2. ✅ UpdateFichaCaracterizacionRequestTest
3. ✅ CreateInstructorRequestTest
4. ✅ UpdateInstructorRequestTest
5. ✅ StorePersonaRequestTest
6. ✅ UpdatePersonaRequestTest

**Todos los Form Requests completados:**
- ✅ StoreAmbienteRequestTest
- ✅ UpdateAmbienteRequestTest
- ✅ StoreAprendizRequestTest
- ✅ UpdateAprendizRequestTest
- ✅ StoreAsignacionInstructorRequestTest
- ✅ UpdateAsignacionInstructorRequestTest
- ✅ StoreBloqueRequestTest
- ✅ UpdateBloqueRequestTest
- ✅ StoreCompetenciaRequestTest
- ✅ UpdateCompetenciaRequestTest
- ✅ StoreEntradaSalidaRequestTest
- ✅ StoreevidenciasRequestTest
- ✅ UpdateevidenciasRequestTest
- ✅ StoreGuiaAprendizajeRequestTest
- ✅ UpdateGuiaAprendizajeRequestTest
- ✅ StoreGuiasAprendizajeRequestTest
- ✅ UpdateGuiasAprendizajeRequestTest
- ✅ StoreInstructorRequestTest
- ✅ StoreMunicipioRequestTest
- ✅ UpdateMunicipioRequestTest
- ✅ StoreParametroRequestTest
- ✅ UpdateparametroRequestTest
- ✅ StorePisoRequestTest
- ✅ UpdatePisoRequestTest
- ✅ StoreProgramaFormacionRequestTest
- ✅ UpdateProgramaFormacionRequestTest
- ✅ StoreRedConocimientoRequestTest
- ✅ UpdateRedConocimientoRequestTest
- ✅ StoreRegionalRequestTest
- ✅ UpdateRegionalRequestTest
- ✅ StoreRegistroActividadesRequestTest
- ✅ UpdateRegistroActividadesRequestTest
- ✅ StoreResultadosAprendizajeRequestTest
- ✅ UpdateResultadosAprendizajeRequestTest
- ✅ StoreSedeRequestTest
- ✅ UpdateSedeRequestTest
- ✅ StoreTemaRequestTest
- ✅ UpdateTemaRequestTest
- ✅ AsignarInstructoresRequestTest
- ✅ InstructoresDisponiblesRequestTest
- ✅ InstructorRequestTest
- ✅ PersonaImportRequestTest
- ✅ UpdatePersonaRoleRequestTest
- ✅ VerificarDisponibilidadRequestTest
- ✅ Complementarios/StoreProgramaComplementarioRequestTest
- ✅ Complementarios/UpdateProgramaComplementarioRequestTest
- ✅ Auth/RegisterRequestTest

### Unit Tests - Otros

#### Configuration Tests
- ✅ UploadLimitsTest
- ✅ ValidateContentLengthTest

## Resumen de Progreso

### ✅ 100% Completados
- ✅ **Jobs**: 8/8 (100%)
- ✅ **Events**: 7/7 (100%)
- ✅ **Observers**: 5/5 (100%)
- ✅ **Listeners**: 1/1 (100%)
- ✅ **Policies**: 23/23 (100%)
- ✅ **Commands**: 28/28 (100%)
- ✅ **Form Requests**: 53/53 (100%)
- ✅ **Servicios**: 49/48 (102% - cobertura completa)
- ✅ **Repositorios**: 46/45 (102% - cobertura completa)

### 🔄 En Progreso
- ✅ **Controladores Feature**: cobertura estructural de los 33 listados cerrada (excepto vacíos/deprecados)
- ✅ **Modelos**: Pais/Departamento/Municipio cubiertos

## Tests Faltantes por Categoría

### Controladores — Fase 3 CERRADA ✅
Los 33 del inventario previo quedaron así:

**Con Feature test nuevo o ya existente:**
- ✅ AsistenceQrController
- ✅ AsistenciaAprendicesController
- ✅ CaracterizacionController
- ✅ ControlSeguimiento/IngresoSalidaController
- ✅ EvidenciasController
- ✅ FichaCaracterizacionFlutterController
- ✅ GoogleDriveController
- ✅ HomeController
- ✅ PersonaIngresoSalidaController
- ✅ RegistroActividadesController
- ✅ WebSocketVisitantesController
- ✅ Api/UbicacionPublicApiController
- ✅ Auth/ConfirmPasswordController
- ✅ Auth/PasswordResetController
- ✅ Auth/RegisterController
- ✅ Auth/VerificationController
- ✅ Complementarios/* (7 controladores especializados)
- ✅ Inventario/Carrito, Categoria, ContratoConvenio, Dashboard, Devolucion, Marca, Notificacion, Proveedor

**No aplica test Feature:**
- ➖ ComplementarioController — vacío `@deprecated` (lógica en Complementarios/*)
- ➖ Inventario/InventarioController — ya no existe en el código

### Modelos — Fase 4+ (cierre 100% activos) ✅
- ✅ PaisModelTest / DepartamentoModelTest / MunicipioModelTest
- ✅ AsistenciaModelTest
- ✅ ComplementarioCatalogoModelTest
- ✅ CaracterizacionProgramaModelTest
- ➖ ProveedorContacto — tabla `proveedor_contactos` dropeada (modelo huérfano)

### Livewire — Fase 5 CERRADA ✅
Cobertura estructural smoke de los **30** componentes Livewire (excl. Concerns):

- `tests/Feature/Livewire/**` — 30 archivos, **30 passed**
- Cada test monta el componente con `Livewire::test(...)->assertStatus(200)`

### Fase 6 — Concerns (acciones CRUD Livewire) ✅
Profundizar acciones reales (no solo mount):

- ✅ `GuiaAprendizajeIndexTest::puede_eliminar_guia_sin_actividades` → `deleteGuia`
- ✅ `FichaIndexTest` → `toggleStatus`, `deleteFicha`
- ✅ `CompetenciaIndexTest` → `toggleStatus`, `deleteCompetencia`
- ✅ `ResultadoAprendizajeIndexTest` → `toggleStatus`, `deleteResultado`
- ✅ `ProgramaIndexTest` → `toggleStatus`, `deletePrograma` (soft delete)
- ✅ `InstructorIndexTest` → `toggleStatus`, `deleteInstructor`
- ✅ `RedConocimientoIndexTest` → `toggleStatus`, `deleteRed`
- ✅ `CrearEvidenciaModalTest` → `crearEvidencia` (evidencia + asistencia + redirect)
- Fix SQLite: `GuiasAprendizaje::actividades()` usa `CASE` en lugar de `FIELD()` MySQL

### Controladores Feature — remanentes ✅
- ✅ AsistenciaConsultaControllerTest
- ✅ EvidenciaControllerTest (sin ruta; store directo)
- ✅ Api/ComplementarioApiControllerTest
- ✅ Complementarios/CatalogoComplementarioControllerTest
- ✅ ResultadosAprendizaje → ya cubierto por `ResultadosAprendizajeCrudTest`

### Verificación funcional (pre-Fase 7) ✅
Smoke tras correcciones de modalidad/catálogo/estado:

- Commands: **84 passed / 0 skipped**
- Livewire: **30 mount** + **acciones CRUD Fase 6**
- Home + ProgramaComplementario + EstadísticaComplementario: **OK**
- Sonar dry-run (alcance tocado): **0 errores**
- PHPStan (alcance tocado): **OK**

Hallazgos corregidos que sí afectaban funcionalidad:

- Eager load `modalidad` → `catalogo.modalidad.parametro` (ofertado ya no tiene relación directa)
- Migración create de `complementarios_catalogo` sin `modalidad_id` (orden de timestamps)
- `estado_id` hardcodeado a `3` en el modelo (rompe installs/tests frescos)
- Factory `ProgramaFormacion`: `nivel_formacion_id` apunta a `parametros_temas`
- `AsistenciaConsultaController` / vista: relaciones vía `instructorFicha.ficha.*`
- `Asistencia::instructorFicha` → `InstructorFichaCaracterizacion`
- `CaracterizacionPrograma::jornada` → `ParametroTema`

### Fase 7 — Cobertura PCOV CERRADA (baseline smoke) ✅

Problema resuelto: SQLite en bind-mount Windows (`/app/database/*.sqlite`) → `database is locked` / corrupción.

Solución:
- DB de cobertura en **`/tmp/cdattg_testing_coverage.sqlite`** (FS nativo del contenedor)
- `TestCase` respeta `DB_DATABASE` / `TESTING_DB` + `PRAGMA busy_timeout`
- Imagen cacheada: `docker/Dockerfile.coverage` → tag `cdattg-coverage`

Baseline smoke ampliado (Home + Livewire + Commands + Inventario Controllers + Complementarios Programa/Estadística), **264 tests / 0 failed**:

| Métrica | Valor |
|--------|-------|
| Classes | 6.62% (78/1179) |
| Methods | 12.78% (470/3679) |
| Lines | **13.39% (4510/33680)** |

(Antes baseline Home+LW+Commands: Lines **6.00%**. El % global sigue midiendo todo `app/` con un subset de tests.)

Comandos:
1. Una vez: `composer test:coverage:build`
2. Smoke: `composer test:coverage:smoke` (usa imagen `cdattg-coverage`)
3. HTML: `COVERAGE_HTML=1 composer test:coverage:fast`
4. Suite completa: `composer test:coverage` (instala PCOV en `php:8.4-cli` si no hay imagen)

## Total Actualizado

**Tests totales: ~370+ archivos de test** (Feature Fase 3 + Modelos Fase 4 + Livewire Fase 5–6)

### Progreso Global
- **Completados al 100%**: Jobs, Events, Observers, Listeners, Policies, Commands, Form Requests
- **Cobertura completa (102%)**: Servicios, Repositorios
- **Cobertura alta (95%)**: Modelos (61/64)
- **Cobertura media (53%)**: Controladores Feature (37/70)
- **PCOV smoke gate**: verde (0 errors, DB en `/tmp`; incluye Inventario + Complementarios)

### Tests Restantes
- Fase 6 residual: más Concerns (Aprendiz toggle, Guia toggleStatus, forms store)
- Ampliar PCOV a más Feature Controllers si se necesita % más alto

**Total faltante estructural crítico: ~0** (quedan profundizaciones Concerns)

## Próximos Pasos Recomendados

1. ✅ Ampliar PCOV smoke a Inventario + Complementarios Feature
2. ✅ Fase 6: acciones Livewire Index (Ficha/Competencia/Resultado/Programa/Instructor/Red + CrearEvidencia)
3. 🔄 Opcional: AprendizIndex toggle + Guia toggleStatus + Form stores
4. ➖ Depurar factories vacíos (`CompetenciaFactory`, `RedConocimientoFactory`) cuando toquen tests

## Notas

- Todos los tests creados usan seeders base para datos realistas
- Todos los tests usan factories existentes o nuevas factories creadas
- Todos los archivos han sido formateados con Laravel Pint
- Los tests siguen las convenciones del proyecto y PHPUnit
- **Nunca** mezclar cobertura Docker con tests locales sobre el mismo archivo SQLite del bind-mount
- Local: `database/testing.sqlite` · Docker coverage: `/tmp/cdattg_testing_coverage.sqlite`
