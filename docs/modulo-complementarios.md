z`# 📚 Módulo de Complementarios

## 📖 Descripción General

El módulo de **Complementarios** es un sistema integral para la gestión de programas de formación complementaria del SENA. Permite administrar cursos complementarios, gestionar aspirantes, procesar inscripciones, validar documentos y generar estadísticas sobre la demanda y participación en estos programas.

### Finalidad

Este módulo tiene como objetivo:

- **Gestionar programas complementarios**: Crear, editar y administrar cursos complementarios con toda su información académica (competencias, resultados de aprendizaje, guías de aprendizaje).
- **Gestionar aspirantes**: Administrar el proceso completo de inscripción, desde la solicitud hasta la admisión o rechazo.
- **Procesar inscripciones**: Permitir a los usuarios inscribirse en programas complementarios de forma pública o autenticada.
- **Validar documentos**: Integración con Google Drive para almacenar y validar documentos de identidad de los aspirantes.
- **Validación SOFIA**: Integración con el sistema SenaSofiaPlus para validar la información de los aspirantes.
- **Generar estadísticas**: Reportes y análisis sobre programas, aspirantes y tendencias de inscripción.

---

## 🏗️ Arquitectura del Módulo

### Estructura de Directorios

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Complementarios/
│   │       ├── ProgramaComplementarioController.php
│   │       ├── AspiranteComplementarioController.php
│   │       ├── InscripcionComplementarioController.php
│   │       ├── EstadisticaComplementarioController.php
│   │       ├── DocumentoComplementarioController.php
│   │       ├── PerfilComplementarioController.php
│   │       └── ValidacionSofiaController.php
│   └── Requests/
│       └── Complementarios/
│           ├── StoreProgramaComplementarioRequest.php
│           ├── UpdateProgramaComplementarioRequest.php
│           ├── InscripcionComplementarioRequest.php
│           └── AspiranteRequest.php
├── Services/
│   ├── ComplementarioService.php
│   ├── InscripcionComplementarioService.php
│   ├── EstadisticaComplementarioService.php
│   ├── AspiranteManagementService.php
│   ├── AspiranteComplementarioService.php
│   ├── AspiranteExportService.php
│   └── AspiranteDocumentoService.php
├── Repositories/
│   ├── ComplementarioOfertadoRepository.php
│   └── AspiranteComplementarioRepository.php
└── Models/
    ├── ComplementarioOfertado.php
    ├── AspiranteComplementario.php
    └── CategoriaCaracterizacionComplementario.php

routes/
└── complementarios/
    ├── gestion_programas_complementarios.php
    ├── gestion_aspirante.php
    ├── estadisticas.php
    ├── inscripciones.php
    └── programa_complementario.php

database/
└── migrations/
    └── batch_17_complementarios/
        ├── create_complementarios_ofertados_table.php
        ├── create_complementarios_ofertados_dias_formacion_table.php
        ├── create_aspirantes_complementarios_table.php
        └── ... (13 migraciones en total)
```

---

## 🎯 Funcionalidades Principales

### 1. Gestión de Programas Complementarios

#### Funcionalidades:
- ✅ Crear nuevos programas complementarios
- ✅ Editar programas existentes
- ✅ Listar todos los programas (vista admin)
- ✅ Ver programas públicos (vista pública)
- ✅ Ver detalles de un programa específico
- ✅ Eliminar programas
- ✅ Asignar competencias, resultados de aprendizaje (RAPs) y guías de aprendizaje
- ✅ Configurar días y horarios de formación
- ✅ Asignar ambiente físico
- ✅ Gestionar estados: Sin Oferta, Con Oferta, Cupos Llenos

#### Estados del Programa:
- `0`: Sin Oferta
- `1`: Con Oferta
- `2`: Cupos Llenos

### 2. Gestión de Aspirantes

#### Funcionalidades:
- ✅ Ver aspirantes por programa
- ✅ Agregar aspirantes existentes a un programa
- ✅ Rechazar aspirantes (cambiar estado)
- ✅ Exportar listado de aspirantes a Excel
- ✅ Descargar cédulas de aspirantes en PDF combinado
- ✅ Validar documentos en Google Drive
- ✅ Filtrar aspirantes por estado

#### Estados del Aspirante:
- `1`: En proceso
- `2`: Completo (documento subido)
- `3`: Admitido
- `4`: Rechazado

### 3. Procesos de Inscripción

#### Tipos de Inscripción:

**a) Inscripción General:**
- Permite registrar datos personales y caracterización sin programa específico
- Útil para captar información de posibles aspirantes

**b) Inscripción a Programa Específico:**
- Inscripción directa a un programa complementario
- Crea automáticamente el usuario si no existe
- Genera el registro de aspirante
- Permite subir documento de identidad

#### Flujo de Inscripción:
1. Usuario accede al formulario de inscripción
2. Completa datos personales y caracterización
3. Sube documento de identidad (PDF)
4. Sistema crea usuario con contraseña = número de documento
5. Sistema almacena documento en Google Drive
6. Aspirante queda en estado "En proceso"

### 4. Validación SOFIA

#### Funcionalidades:
- ✅ Validación masiva de aspirantes contra SenaSofiaPlus
- ✅ Seguimiento de progreso de validación
- ✅ Registro de validaciones exitosas y fallidas
- ✅ Procesamiento en cola (queue) para mejor rendimiento

#### Estados de Validación SOFIA:
- `0`: No registrado
- `1`: Validado
- `2`: Error en validación

### 5. Estadísticas y Reportes

#### Métricas Disponibles:
- Total de aspirantes
- Aspirantes aceptados
- Aspirantes pendientes
- Programas activos
- Tendencia de inscripciones (últimos 6 meses)
- Distribución por programas
- Programas con mayor demanda
- Tasa de aceptación por programa

#### Filtros Disponibles:
- Por fecha (inicio y fin)
- Por departamento
- Por municipio
- Por programa

### 6. Gestión de Documentos

#### Funcionalidades:
- ✅ Subida de documentos de identidad
- ✅ Almacenamiento en Google Drive
- ✅ Validación de formato (PDF, máximo 5MB)
- ✅ Nomenclatura automática: `TipoDoc_NumDoc_Nombre_Apellido_Timestamp.pdf`
- ✅ Verificación de existencia de documentos

---

## 🗄️ Modelos de Datos

### ComplementarioOfertado

**Tabla:** `complementarios_ofertados`

**Campos principales:**
- `id`: Identificador único
- `codigo`: Código único del programa
- `nombre`: Nombre del programa
- `justificacion`: Justificación del programa
- `requisitos_ingreso`: Requisitos para ingresar
- `duracion`: Duración en horas
- `cupos`: Número de cupos disponibles
- `estado`: Estado del programa (0, 1, 2)
- `modalidad_id`: ID de la modalidad (relación con `parametros_temas`)
- `jornada_id`: ID de la jornada (relación con `jornadas_formacion`)
- `ambiente_id`: ID del ambiente físico

**Relaciones:**
- `modalidad()`: BelongsTo `ParametroTema`
- `jornada()`: BelongsTo `JornadaFormacion`
- `ambiente()`: BelongsTo `Ambiente`
- `diasFormacion()`: BelongsToMany `Parametro` (con pivot: `hora_inicio`, `hora_fin`)
- `aspirantes()`: HasMany `AspiranteComplementario`
- `competencias()`: BelongsToMany `Competencia`
- `raps()`: BelongsToMany `ResultadosAprendizaje`
- `guiasAprendizaje()`: BelongsToMany `GuiasAprendizaje`

### AspiranteComplementario

**Tabla:** `aspirantes_complementarios`

**Campos principales:**
- `id`: Identificador único
- `persona_id`: ID de la persona (relación con `personas`)
- `complementario_id`: ID del programa complementario
- `observaciones`: Observaciones adicionales
- `estado`: Estado del aspirante (1, 2, 3, 4)
- `documento_identidad_path`: Ruta del documento en Google Drive
- `documento_identidad_nombre`: Nombre del archivo

**Relaciones:**
- `persona()`: BelongsTo `Persona`
- `complementario()`: BelongsTo `ComplementarioOfertado`

### Tablas Relacionadas

- `complementarios_ofertados_dias_formacion`: Tabla pivot para días de formación
- `competencia_complementario`: Tabla pivot para competencias
- `resultado_aprendizaje_complementario`: Tabla pivot para RAPs
- `guia_aprendizaje_complementario`: Tabla pivot para guías de aprendizaje
- `persona_caracterizacion`: Tabla pivot para caracterización de personas
- `categorias_caracterizacion_complementarios`: Categorías de caracterización

---

## 🎮 Controladores

### ProgramaComplementarioController

**Ubicación:** `app/Http/Controllers/Complementarios/ProgramaComplementarioController.php`

**Métodos principales:**

| Método | Ruta | Descripción | Autenticación |
|--------|------|-------------|---------------|
| `index()` | `GET /complementarios-ofertados` | Listar programas (admin) | ✅ |
| `create()` | `GET /complementarios-ofertados/create` | Formulario de creación | ✅ |
| `store()` | `POST /complementarios-ofertados` | Crear programa | ✅ |
| `show()` | `GET /complementarios-ofertados/{programa}` | Ver detalles | ✅ |
| `edit()` | `GET /complementarios-ofertados/{programa}/edit` | Formulario de edición | ✅ |
| `update()` | `PUT /complementarios-ofertados/{programa}` | Actualizar programa | ✅ |
| `destroy()` | `DELETE /complementarios-ofertados/{programa}` | Eliminar programa | ✅ |
| `programasPublicos()` | `GET /programas-complementarios` | Listar programas públicos | ❌ |
| `verPrograma()` | `GET /programas-complementarios/{programa}` | Ver programa público | ❌ |
| `editApi()` | `GET /complementarios-ofertados/{programa}/edit-api` | API para edición (AJAX) | ✅ |

### AspiranteComplementarioController

**Ubicación:** `app/Http/Controllers/Complementarios/AspiranteComplementarioController.php`

**Métodos principales:**

| Método | Ruta | Descripción | Autenticación |
|--------|------|-------------|---------------|
| `gestionAspirantes()` | `GET /gestion-aspirantes` | Gestión de aspirantes | ✅ |
| `verAspirantes()` | `GET /programas-complementarios/{curso}` | Ver aspirantes por programa | ✅ |
| `programa()` | `GET /programas-complementarios/{programa}` | Ver aspirantes (por ID) | ✅ |
| `agregarAspirante()` | `POST /programas-complementarios/{complementarioId}/agregar-aspirante` | Agregar aspirante | ✅ |
| `eliminarAspirante()` | `DELETE /programas-complementarios/{complementarioId}/aspirante/{aspiranteId}` | Rechazar aspirante | ✅ |
| `exportarAspirantesExcel()` | `GET /programas-complementarios/{complementarioId}/exportar-excel` | Exportar a Excel | ✅ |
| `descargarCedulas()` | `GET /programas-complementarios/{complementarioId}/descargar-cedulas` | Descargar PDF de cédulas | ✅ |
| `validarDocumentos()` | `POST /programas-complementarios/{complementarioId}/validar-documentos` | Validar documentos | ✅ |

### InscripcionComplementarioController

**Ubicación:** `app/Http/Controllers/Complementarios/InscripcionComplementarioController.php`

**Métodos principales:**

| Método | Ruta | Descripción | Autenticación |
|--------|------|-------------|---------------|
| `inscripcionGeneral()` | `GET /inscripciones/general` | Formulario inscripción general | ❌ |
| `procesarInscripcionGeneral()` | `POST /inscripciones/general` | Procesar inscripción general | ❌ |
| `formularioInscripcion()` | `GET /inscripciones/{programa}` | Formulario inscripción programa | ❌ |
| `procesarInscripcion()` | `POST /inscripciones/{programa}` | Procesar inscripción programa | ❌ |

### EstadisticaComplementarioController

**Ubicación:** `app/Http/Controllers/Complementarios/EstadisticaComplementarioController.php`

**Métodos principales:**

| Método | Ruta | Descripción | Autenticación |
|--------|------|-------------|---------------|
| `estadisticas()` | `GET /estadisticas` | Dashboard de estadísticas | ✅ |
| `apiEstadisticas()` | `GET /estadisticas/api` | API de estadísticas con filtros | ✅ |

### DocumentoComplementarioController

**Ubicación:** `app/Http/Controllers/Complementarios/DocumentoComplementarioController.php`

**Métodos principales:**

| Método | Ruta | Descripción | Autenticación |
|--------|------|-------------|---------------|
| `formularioDocumentos()` | `GET /documentos/{id}` | Formulario subida documentos | ❌ |
| `subirDocumento()` | `POST /documentos/{id}` | Subir documento | ❌ |
| `procesarDocumentos()` | `GET /procesar-documentos` | Procesar documentos (legacy) | ❌ |
| `procesarDocumentoSubmit()` | `POST /procesar-documentos` | Procesar envío documento | ❌ |

### PerfilComplementarioController

**Ubicación:** `app/Http/Controllers/Complementarios/PerfilComplementarioController.php`

**Métodos principales:**

| Método | Ruta | Descripción | Autenticación |
|--------|------|-------------|---------------|
| `Perfil()` | `GET /perfil` | Ver perfil propio | ✅ |

### ValidacionSofiaController

**Ubicación:** `app/Http/Controllers/Complementarios/ValidacionSofiaController.php`

**Métodos principales:**

| Método | Ruta | Descripción | Autenticación |
|--------|------|-------------|---------------|
| `validarSofia()` | `POST /validar-sofia/{complementarioId}` | Iniciar validación SOFIA | ✅ |
| `getValidationProgress()` | `GET /validacion-progreso/{progressId}` | Obtener progreso validación | ✅ |

---

## 🔧 Servicios

### ComplementarioService

**Ubicación:** `app/Services/ComplementarioService.php`

**Responsabilidades:**
- Gestión de iconos y badges para programas
- Enriquecimiento de datos de programas
- Sincronización de días de formación
- Obtención de datos para formularios (modalidades, jornadas, ambientes)
- Obtención de tipos de documento y géneros
- Verificación de inscripciones existentes
- Creación y actualización de aspirantes
- Obtención de estadísticas básicas de programas

**Métodos principales:**
- `getIconoForPrograma($nombre)`: Obtener icono por nombre
- `getBadgeClassForEstado($estado)`: Obtener clase CSS por estado
- `getEstadoLabel($estado)`: Obtener etiqueta de estado
- `enriquecerPrograma($programa)`: Enriquecer programa con datos auxiliares
- `enriquecerProgramas($programas)`: Enriquecer colección de programas
- `obtenerProgramas($relations, $estado)`: Obtener programas con relaciones
- `sincronizarDiasFormacion($programa, $dias)`: Sincronizar días de formación
- `obtenerDatosFormulario()`: Obtener datos para formularios
- `getTiposDocumento()`: Obtener tipos de documento
- `getGeneros()`: Obtener géneros
- `verificarInscripcionExistente($personaId, $programaId)`: Verificar inscripción
- `crearAspirante($personaId, $programaId, $observaciones)`: Crear aspirante
- `actualizarEstadoAspirante($aspiranteId, $estado)`: Actualizar estado
- `obtenerEstadisticasPrograma($programaId)`: Obtener estadísticas

### InscripcionComplementarioService

**Ubicación:** `app/Services/InscripcionComplementarioService.php`

**Responsabilidades:**
- Preparar datos para formularios de inscripción
- Procesar inscripciones generales
- Procesar inscripciones a programas específicos
- Crear o actualizar personas
- Crear o actualizar usuarios
- Procesar documentos de identidad
- Gestionar caracterizaciones

**Métodos principales:**
- `prepararFormularioGeneral()`: Preparar datos para inscripción general
- `procesarInscripcionGeneral($data)`: Procesar inscripción general
- `prepararFormularioInscripcion($programaId)`: Preparar datos para inscripción
- `procesarInscripcion($data, $programaId)`: Procesar inscripción a programa
- `obtenerCaracterizacionesAgrupadas()`: Obtener caracterizaciones agrupadas

### EstadisticaComplementarioService

**Ubicación:** `app/Services/EstadisticaComplementarioService.php`

**Responsabilidades:**
- Obtener estadísticas reales de la base de datos
- Obtener estadísticas filtradas
- Generar reportes de tendencias
- Obtener estadísticas por género y edad

**Métodos principales:**
- `obtenerEstadisticasReales()`: Obtener estadísticas generales
- `obtenerEstadisticasFiltradas($filtros)`: Obtener estadísticas con filtros
- `generarReporteTendencias($meses)`: Generar reporte de tendencias
- `obtenerEstadisticasPorGenero()`: Estadísticas por género
- `obtenerEstadisticasPorEdad()`: Estadísticas por edad

### AspiranteManagementService

**Ubicación:** `app/Services/AspiranteManagementService.php`

**Responsabilidades:**
- Obtener programas para gestión
- Obtener aspirantes por programa
- Agregar aspirantes a programas
- Rechazar aspirantes
- Validar documentos

**Métodos principales:**
- `obtenerProgramasParaGestion()`: Obtener programas con conteo
- `obtenerAspirantesPorPrograma($cursoNombre)`: Obtener aspirantes por nombre
- `obtenerAspirantesPorProgramaId($programaId)`: Obtener aspirantes por ID
- `agregarAspirante($complementarioId, $numeroDocumento)`: Agregar aspirante
- `rechazarAspirante($complementarioId, $aspiranteId)`: Rechazar aspirante
- `validarDocumentos($complementarioId, $documentoService)`: Validar documentos

### AspiranteExportService

**Ubicación:** `app/Services/AspiranteExportService.php`

**Responsabilidades:**
- Exportar aspirantes a Excel
- Generar PDF combinado de cédulas

**Métodos principales:**
- `exportarAspirantesExcel($complementarioId)`: Exportar a Excel
- `descargarCedulas($complementarioId)`: Descargar PDF de cédulas

### AspiranteDocumentoService

**Ubicación:** `app/Services/AspiranteDocumentoService.php`

**Responsabilidades:**
- Validar documentos en Google Drive
- Verificar existencia de documentos

---

## 📦 Repositorios

### ComplementarioOfertadoRepository

**Ubicación:** `app/Repositories/ComplementarioOfertadoRepository.php`

**Métodos principales:**
- `getAll($relations)`: Obtener todos los programas
- `getByEstado($estado, $relations)`: Obtener por estado
- `getActivos($relations)`: Obtener programas activos
- `findWithRelations($id, $relations)`: Buscar por ID con relaciones
- `findByNombre($nombre)`: Buscar por nombre
- `getAllWithAspirantesCount($relations)`: Obtener con conteo de aspirantes
- `create($data)`: Crear programa
- `update($programa, $data)`: Actualizar programa
- `delete($programa)`: Eliminar programa
- `countActivos()`: Contar programas activos
- `getEstadisticas()`: Obtener estadísticas
- `getProgramasConMayorDemanda($limit)`: Obtener programas con mayor demanda

### AspiranteComplementarioRepository

**Ubicación:** `app/Repositories/AspiranteComplementarioRepository.php`

**Métodos principales:**
- `findByPrograma($programaId, $relations)`: Obtener por programa
- `findByProgramaConDocumentos($programaId)`: Obtener con documentos
- `countByEstado($programaId, $estado)`: Contar por estado
- `countByPrograma($programaId)`: Contar total por programa
- `existeInscripcion($personaId, $programaId)`: Verificar inscripción
- `findByPersonaYPrograma($personaId, $programaId)`: Buscar específico
- `findById($id)`: Buscar por ID
- `create($data)`: Crear aspirante
- `update($aspirante, $data)`: Actualizar aspirante
- `delete($aspirante)`: Eliminar aspirante
- `findForExport($programaId)`: Obtener para exportación
- `getEstadisticas()`: Obtener estadísticas
- `getTendenciaInscripciones($meses)`: Obtener tendencia
- `getDistribucionPorProgramas()`: Obtener distribución

---

## 🛣️ Rutas

### Rutas de Gestión de Programas

**Archivo:** `routes/complementarios/gestion_programas_complementarios.php`

```php
// Rutas autenticadas para administración
Route::middleware('auth')
    ->prefix('complementarios-ofertados')
    ->name('complementarios-ofertados.')
    ->group(function () {
        // CRUD completo de programas
    });
```

### Rutas de Gestión de Aspirantes

**Archivo:** `routes/complementarios/gestion_aspirante.php`

```php
// Rutas autenticadas para gestión de aspirantes
Route::get('/gestion-aspirantes', ...)
Route::get('/programas-complementarios/{curso}', ...)
Route::post('/programas-complementarios/{complementarioId}/agregar-aspirante', ...)
Route::delete('/programas-complementarios/{complementarioId}/aspirante/{aspiranteId}', ...)
Route::get('/programas-complementarios/{complementarioId}/exportar-excel', ...)
Route::get('/programas-complementarios/{complementarioId}/descargar-cedulas', ...)
Route::post('/programas-complementarios/{complementarioId}/validar-documentos', ...)
```

### Rutas de Inscripciones

**Archivo:** `routes/complementarios/inscripciones.php`

```php
// Rutas públicas para inscripciones
Route::prefix('inscripciones')
    ->name('inscripciones.')
    ->group(function () {
        // Inscripción general
        Route::get('general', ...)
        Route::post('general', ...)
        
        // Inscripción a programa específico
        Route::get('{programa}', ...)
        Route::post('{programa}', ...)
    });
```

### Rutas de Estadísticas

**Archivo:** `routes/complementarios/estadisticas.php`

```php
// Rutas autenticadas para estadísticas
Route::get('/estadisticas', ...)
Route::get('/estadisticas/api', ...)
```

### Rutas Públicas

**Archivo:** `routes/web.php`

```php
// Rutas públicas (sin autenticación)
Route::prefix('programas-complementarios')
    ->name('programas-complementarios.')
    ->group(function () {
        Route::get('/', [ProgramaComplementarioController::class, 'programasPublicos'])
        Route::get('{programa}', [ProgramaComplementarioController::class, 'verPrograma'])
        Route::get('{programa}/inscripcion', [InscripcionComplementarioController::class, 'formularioInscripcion'])
        Route::post('{programa}/inscripcion', [InscripcionComplementarioController::class, 'procesarInscripcion'])
    });
```

---

## 🗃️ Migraciones

El módulo utiliza el batch `batch_17_complementarios` con las siguientes migraciones:

1. **create_complementarios_ofertados_table**: Tabla principal de programas
2. **create_complementarios_ofertados_dias_formacion_table**: Tabla pivot para días de formación
3. **create_aspirantes_complementarios_table**: Tabla de aspirantes
4. **create_categorias_caracterizacion_complementarios_table**: Categorías de caracterización
5. **add_parent_id_to_categorias_caracterizacion_complementarios_table**: Relación padre-hijo en categorías
6. **create_sofia_validation_progress_table**: Progreso de validación SOFIA
7. **add_caracterizacion_id_to_personas_table**: Caracterización en personas
8. **create_persona_caracterizacion_table**: Tabla pivot persona-caracterización
9. **add_ambiente_id_to_complementarios_ofertados_table**: Ambiente físico en programas
10. **add_condocumento_to_personas_table**: Flag de documento en personas
11. **add_parametro_id_to_personas_table**: Parámetro en personas
12. **create_senasofiaplus_validation_logs_table**: Logs de validación SOFIA
13. **add_competencias_raps_to_complementarios.php**: Tablas pivot para competencias y RAPs
14. **add_justificacion_requisitos_to_complementarios.php**: Campos justificación y requisitos

### Ejecutar Migraciones

```bash
# Migrar solo el módulo de complementarios
php artisan migrate:module batch_17_complementarios

# Migrar todo el sistema (incluye complementarios)
php artisan migrate:module --all
```

---

## 🔐 Permisos y Roles

El módulo utiliza el sistema de permisos de Spatie. Los permisos relacionados incluyen:

- `VER COMPLEMENTARIOS`: Ver programas complementarios
- `CREAR COMPLEMENTARIOS`: Crear programas
- `EDITAR COMPLEMENTARIOS`: Editar programas
- `ELIMINAR COMPLEMENTARIOS`: Eliminar programas
- `GESTIONAR ASPIRANTES`: Gestionar aspirantes
- `VER ESTADISTICAS`: Ver estadísticas

**Roles relacionados:**
- `ADMINISTRADOR`: Acceso completo
- `COORDINADOR`: Gestión de programas y aspirantes
- `ASPIRANTE`: Solo puede ver su perfil e inscribirse

---

## 🔗 Integraciones

### Google Drive

El módulo integra con Google Drive para almacenar documentos de identidad:

- **Configuración**: Variables de entorno `GOOGLE_DRIVE_*`
- **Disco**: `google` (configurado en `config/filesystems.php`)
- **Carpeta**: `documentos_aspirantes`
- **Formato de nombre**: `TipoDoc_NumDoc_Nombre_Apellido_Timestamp.pdf`

### SenaSofiaPlus (SOFIA)

Integración para validar información de aspirantes:

- **Job**: `ValidarSofiaJob`
- **Cola**: `sofia-validation`
- **Modelo**: `SofiaValidationProgress`
- **Estados**: `pending`, `processing`, `completed`, `failed`

### PHPSpreadsheet

Para exportación de datos a Excel:

- **Uso**: Exportación de listados de aspirantes
- **Formato**: `.xlsx`

### FPDI

Para generación de PDFs combinados:

- **Uso**: Generación de cédulas combinadas
- **Librería**: `setasign\Fpdi\Fpdi`

---

## 📊 Estados y Flujos

### Flujo de Inscripción

```
1. Usuario accede a formulario
   ↓
2. Completa datos personales
   ↓
3. Sube documento de identidad
   ↓
4. Sistema crea/actualiza Persona
   ↓
5. Sistema crea/actualiza Usuario
   ↓
6. Sistema crea AspiranteComplementario (estado: 1 - En proceso)
   ↓
7. Sistema sube documento a Google Drive
   ↓
8. Sistema actualiza AspiranteComplementario (estado: 2 - Completo)
   ↓
9. Usuario puede iniciar sesión
```

### Flujo de Validación SOFIA

```
1. Administrador inicia validación
   ↓
2. Sistema crea SofiaValidationProgress (estado: pending)
   ↓
3. Sistema despacha ValidarSofiaJob a cola
   ↓
4. Job procesa aspirantes uno por uno
   ↓
5. Sistema actualiza progreso en tiempo real
   ↓
6. Al finalizar, estado cambia a completed/failed
```

### Flujo de Gestión de Aspirantes

```
1. Administrador ve listado de aspirantes
   ↓
2. Puede agregar aspirante existente
   ↓
3. Puede rechazar aspirante (estado: 4)
   ↓
4. Puede validar documentos en Google Drive
   ↓
5. Puede exportar a Excel o PDF
   ↓
6. Puede iniciar validación SOFIA masiva
```

---

## 🧪 Testing

El módulo incluye tests unitarios y de integración:

**Tests Unitarios:**
- `ComplementarioServiceTest`
- `InscripcionComplementarioServiceTest`
- `EstadisticaComplementarioServiceTest`
- `AspiranteComplementarioRepositoryTest`
- `ComplementarioOfertadoRepositoryTest`

**Tests de Integración:**
- `ProgramaComplementarioControllerTest`
- `AspiranteComplementarioControllerTest`
- `InscripcionComplementarioControllerTest`

### Ejecutar Tests

```bash
# Todos los tests del módulo
php artisan test --filter Complementario

# Tests específicos
php artisan test tests/Unit/Services/ComplementarioServiceTest.php
php artisan test tests/Feature/Complementarios/
```

---

## 📝 Request Validation

### StoreProgramaComplementarioRequest

**Ubicación:** `app/Http/Requests/Complementarios/StoreProgramaComplementarioRequest.php`

**Reglas de validación:**
- `codigo`: Requerido, único, string
- `nombre`: Requerido, string
- `justificacion`: Requerido, string
- `requisitos_ingreso`: Requerido, string
- `duracion`: Requerido, integer, min:1
- `cupos`: Requerido, integer, min:1
- `estado`: Requerido, integer, in:0,1,2
- `modalidad_id`: Requerido, existe en `parametros_temas`
- `jornada_id`: Requerido, existe en `jornadas_formacion`
- `ambiente_id`: Opcional, existe en `ambientes`
- `dias`: Array de días con `dia_id`, `hora_inicio`, `hora_fin`
- `competencias`: Array opcional de IDs
- `raps`: Array opcional de IDs
- `guias`: Array opcional de IDs

### InscripcionComplementarioRequest

**Ubicación:** `app/Http/Requests/Complementarios/InscripcionComplementarioRequest.php`

**Reglas de validación:**
- Datos personales completos
- Documento de identidad: Requerido, PDF, máximo 5MB
- Caracterización opcional
- Aceptación de privacidad

---

## 🚀 Mejores Prácticas

1. **Siempre usar transacciones** para operaciones que involucren múltiples tablas
2. **Validar existencia** antes de crear relaciones
3. **Usar repositorios** para acceso a datos, no modelos directamente en controladores
4. **Manejar errores** con try-catch y logging apropiado
5. **Validar permisos** antes de operaciones sensibles
6. **Usar colas** para procesos largos (validación SOFIA)
7. **Enriquecer datos** en servicios, no en controladores
8. **Mantener consistencia** en nomenclatura de archivos

---

## 🔄 Dependencias del Módulo

El módulo depende de los siguientes batches:

- `batch_05_personas`: Personas y usuarios
- `batch_03_parametros`: Parámetros y configuración
- `batch_11_jornadas_horarios`: Jornadas y horarios
- `batch_06_infraestructura`: Infraestructura física
- `batch_13_competencias`: Competencias, RAPs y guías

---

## 📚 Recursos Adicionales

- **Documentación API**: Ver `docs/api/API.md`
- **Guía de Migraciones**: Ver `docs/development/migrations-modules.md`
- **Sistema de Permisos**: Ver documentación de Spatie Permissions

---

## 👥 Contribuidores

Este módulo fue desarrollado como parte del sistema CDATTG Web.

---

## 📅 Versión

**Versión actual:** 1.0.0  
**Última actualización:** 2025

---

## 📞 Soporte

Para consultas o problemas relacionados con este módulo, contactar al equipo de desarrollo.

