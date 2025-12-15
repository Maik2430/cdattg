# 📋 Módulo de Gestión de Aspirantes - Programas Complementarios

## 📖 Descripción General

El módulo de **Gestión de Aspirantes** es parte del sistema de **Programas Complementarios** del SENA. Permite a los administradores gestionar los aspirantes inscritos en programas de formación complementaria, incluyendo su registro, validación de documentos, exportación de datos y validación en el sistema SOFIA Plus.

## 🎯 Propósito del Módulo

Este módulo permite:
- **Gestionar aspirantes** inscritos en programas complementarios
- **Validar documentos** de identidad almacenados en Google Drive
- **Exportar datos** a Excel para importación en SOFIA Plus
- **Descargar cédulas** en formato PDF combinado
- **Validar aspirantes** en el sistema SOFIA Plus
- **Rechazar aspirantes** que no cumplan con los requisitos
- **Obtener estadísticas** de exclusión y validación

## 🗄️ Estructura de Base de Datos

### Tabla Principal: `aspirantes_complementarios`

La tabla principal almacena la relación entre personas y programas complementarios:

```sql
CREATE TABLE aspirantes_complementarios (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    persona_id BIGINT NOT NULL,
    complementario_id BIGINT NOT NULL,
    observaciones TEXT NULL,
    estado TINYINT DEFAULT 1,
    documento_identidad_path VARCHAR(255) NULL,
    documento_identidad_nombre VARCHAR(255) NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    UNIQUE KEY aspirantes_complementarios_persona_programa_unique (persona_id, complementario_id),
    FOREIGN KEY (persona_id) REFERENCES personas(id) ON DELETE CASCADE,
    FOREIGN KEY (complementario_id) REFERENCES complementarios_ofertados(id) ON DELETE CASCADE
);
```

**Campos importantes:**
- `persona_id`: Relación con la tabla `personas`
- `complementario_id`: Relación con la tabla `complementarios_ofertados`
- `estado`: Estado del aspirante (1=En proceso, 3=Admitido, 4=Rechazado)
- `observaciones`: Notas adicionales sobre el aspirante
- `documento_identidad_path`: Ruta del documento en Google Drive
- `documento_identidad_nombre`: Nombre del archivo del documento

**Restricción única:** Una persona no puede estar inscrita dos veces en el mismo programa complementario.

### Estados del Aspirante

| Estado | Valor | Descripción |
|--------|-------|-------------|
| En proceso | 1 | Aspirante inscrito, en proceso de validación |
| Admitido | 3 | Aspirante aceptado en el programa |
| Rechazado | 4 | Aspirante rechazado o eliminado del programa |

## 🔗 Relaciones con Otras Tablas

### 1. Relación con `personas`

**Tipo:** `belongsTo` (Muchos a Uno)

```php
// En AspiranteComplementario
public function persona()
{
    return $this->belongsTo(Persona::class);
}
```

**Campos relacionados en `personas`:**
- `id`: Identificador único de la persona
- `numero_documento`: Número de documento de identidad
- `tipo_documento`: Tipo de documento (CC, TI, CE, etc.)
- `primer_nombre`, `segundo_nombre`, `primer_apellido`, `segundo_apellido`: Nombres completos
- `condocumento`: Indicador si tiene documento en Google Drive (0=No, 1=Sí)
- `estado_sofia`: Estado en SOFIA Plus (0=No registrado, 1=Registrado, 2=Pendiente validación)
- `parametro_id`: ID de caracterización de población

**Relación inversa:** Una persona puede tener múltiples inscripciones en diferentes programas complementarios.

### 2. Relación con `complementarios_ofertados`

**Tipo:** `belongsTo` (Muchos a Uno)

```php
// En AspiranteComplementario
public function complementario()
{
    return $this->belongsTo(ComplementarioOfertado::class, 'complementario_id');
}
```

**Campos relacionados en `complementarios_ofertados`:**
- `id`: Identificador único del programa
- `nombre`: Nombre del programa complementario
- `codigo`: Código del programa
- `cupos`: Número de cupos disponibles
- `modalidad_id`: Modalidad de formación
- `jornada_id`: Jornada de formación
- `ambiente_id`: Ambiente asignado

**Relación inversa:** Un programa complementario puede tener múltiples aspirantes.

```php
// En ComplementarioOfertado
public function aspirantes()
{
    return $this->hasMany(AspiranteComplementario::class, 'complementario_id');
}
```

### 3. Relación con `persona_caracterizacion` (Tabla Pivot)

**Tipo:** `belongsToMany` (Muchos a Muchos)

Las personas pueden tener múltiples caracterizaciones complementarias:

```php
// En Persona
public function caracterizacionesComplementarias(): BelongsToMany
{
    return $this->belongsToMany(
        Parametro::class,
        'persona_caracterizacion',
        'persona_id',
        'parametro_id'
    )->withTimestamps();
}
```

**Uso:** Se utiliza para identificar el tipo de población del aspirante (víctima del conflicto, desplazado, etc.) para la exportación a SOFIA Plus.

### 4. Relación con `sofia_validation_progress`

**Tipo:** Relación indirecta a través de `complementario_id`

Esta tabla almacena el progreso de validación de aspirantes en SOFIA Plus:

```sql
CREATE TABLE sofia_validation_progress (
    id BIGINT PRIMARY KEY,
    complementario_id BIGINT NOT NULL,
    user_id BIGINT NULL,
    status VARCHAR(255) DEFAULT 'pending',
    total_aspirantes INT DEFAULT 0,
    processed_aspirantes INT DEFAULT 0,
    successful_validations INT DEFAULT 0,
    failed_validations INT DEFAULT 0,
    errors JSON NULL,
    started_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### 5. Relación con `senasofiaplus_validation_logs`

**Tipo:** Relación indirecta a través de `aspirante_id`

Almacena los logs de validación individual de cada aspirante:

```sql
CREATE TABLE senasofiaplus_validation_logs (
    id BIGINT PRIMARY KEY,
    aspirante_id BIGINT NOT NULL,
    complementario_id BIGINT NOT NULL,
    validation_status VARCHAR(255),
    response_data JSON,
    error_message TEXT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (aspirante_id) REFERENCES aspirantes_complementarios(id) ON DELETE CASCADE
);
```

## 🏗️ Arquitectura del Código

### Estructura de Directorios

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Complementarios/
│   │       └── AspiranteComplementarioController.php
│   └── Requests/
│       └── Complementarios/
│           ├── AspiranteRequest.php
│           └── BuscarPersonaRequest.php
├── Models/
│   └── Complementarios/
│       └── AspiranteComplementario.php
├── Services/
│   └── Complementarios/
│       ├── AspiranteManagementService.php
│       ├── AspiranteExportService.php
│       ├── AspiranteDocumentoService.php
│       └── AspiranteComplementarioService.php
└── Repositories/
    └── Complementarios/
        └── AspiranteComplementarioRepository.php

routes/
└── complementarios/
    └── gestion_aspirante.php

resources/
└── views/
    └── complementarios/
        └── aspirantes/
            ├── index.blade.php
            ├── programa.blade.php
            └── create.blade.php
```

## 📝 Componentes Principales

### 1. Modelo: `AspiranteComplementario`

**Ubicación:** `app/Models/Complementarios/AspiranteComplementario.php`

**Responsabilidades:**
- Definir la estructura de datos del aspirante
- Establecer relaciones con `Persona` y `ComplementarioOfertado`
- Proporcionar accesores para estados legibles

**Relaciones:**
```php
// Relación con Persona
public function persona(): BelongsTo
{
    return $this->belongsTo(Persona::class);
}

// Relación con ComplementarioOfertado
public function complementario(): BelongsTo
{
    return $this->belongsTo(ComplementarioOfertado::class, 'complementario_id');
}
```

**Accesores:**
```php
// Obtener etiqueta del estado
public function getEstadoLabelAttribute(): string
{
    return match($this->estado) {
        1 => 'En proceso',
        3 => 'Admitido',
        4 => 'Rechazado',
        default => 'Desconocido'
    };
}
```

### 2. Controlador: `AspiranteComplementarioController`

**Ubicación:** `app/Http/Controllers/Complementarios/AspiranteComplementarioController.php`

**Responsabilidades:**
- Manejar las peticiones HTTP relacionadas con aspirantes
- Coordinar entre servicios y vistas
- Validar datos de entrada mediante Form Requests
- Retornar respuestas JSON o vistas Blade

**Métodos principales:**

| Método | Ruta | Descripción |
|--------|------|-------------|
| `index()` | GET `/gestion-aspirantes` | Lista todos los programas complementarios |
| `gestionAspirantes()` | GET `/gestion-aspirantes` | Alias de `index()` |
| `verAspirantes($curso)` | GET `/programas-complementarios/{curso}` | Muestra aspirantes de un programa por nombre |
| `programa($programa)` | GET `/programas-complementarios/{programa}` | Muestra aspirantes de un programa por ID |
| `agregarAspirante()` | POST `/programas-complementarios/{complementarioId}/agregar-aspirante` | Agrega un aspirante existente a un programa |
| `eliminarAspirante()` | DELETE `/programas-complementarios/{complementarioId}/aspirante/{aspiranteId}` | Rechaza un aspirante (cambia estado a 4) |
| `exportarAspirantesExcel()` | GET `/programas-complementarios/{complementarioId}/exportar-excel` | Exporta aspirantes a Excel |
| `descargarCedulas()` | GET `/programas-complementarios/{complementarioId}/descargar-cedulas` | Descarga cédulas en PDF combinado |
| `validarDocumentos()` | POST `/programas-complementarios/{complementarioId}/validar-documentos` | Valida documentos en Google Drive |
| `buscarPersona()` | POST `/buscar-persona` | Busca una persona por número de documento |
| `create($programa)` | GET `/programas-complementarios/{programa}/create` | Muestra formulario para crear nuevo aspirante |
| `store()` | POST `/programas-complementarios/{programa}/store` | Almacena nuevo aspirante |
| `getEstadisticasExclusion()` | GET `/programas-complementarios/{complementarioId}/estadisticas-exclusion` | Obtiene estadísticas de exclusión |

**Inyección de Dependencias:**
```php
public function __construct(
    private readonly AspiranteManagementService $aspiranteManagementService,
    private readonly AspiranteExportService $exportService,
    private readonly AspiranteDocumentoService $documentoService,
    private readonly PersonaService $personaService,
    private readonly AspiranteComplementarioRepository $aspiranteRepository,
    private readonly ComplementarioService $complementarioService,
    private readonly TemaRepository $temaRepository
) {}
```

### 3. Servicio: `AspiranteManagementService`

**Ubicación:** `app/Services/Complementarios/AspiranteManagementService.php`

**Responsabilidades:**
- Lógica de negocio para gestión de aspirantes
- Validaciones de precondiciones
- Coordinación entre repositorios
- Manejo de errores y logging

**Métodos principales:**

#### `obtenerProgramasParaGestion(): Collection`
Obtiene todos los programas complementarios con conteo de aspirantes para mostrar en la vista principal.

#### `obtenerAspirantesPorPrograma(string $cursoNombre): array`
Obtiene aspirantes de un programa por su nombre. Si no existe, retorna 404.

#### `obtenerAspirantesPorProgramaId(int $programaId): array`
Obtiene aspirantes de un programa por su ID. Incluye:
- Programa con relaciones (modalidad, jornada, días de formación)
- Lista de aspirantes con relaciones (persona, complementario)
- Progreso de validación existente (si hay)

#### `agregarAspirante(int $complementarioId, string $numeroDocumento): array`
Agrega un aspirante existente a un programa complementario.

**Flujo:**
1. Valida que el programa exista
2. Valida que la persona exista
3. Valida que no esté ya inscrita
4. Crea el registro en `aspirantes_complementarios` con estado 1 (En proceso)
5. Retorna respuesta de éxito o error

**Validaciones:**
- Programa debe existir
- Persona debe existir en la base de datos
- Persona no debe estar ya inscrita en el programa

#### `rechazarAspirante(int $complementarioId, int $aspiranteId): array`
Rechaza un aspirante cambiando su estado a 4 (Rechazado).

**Flujo:**
1. Valida permisos del usuario (`ELIMINAR ASPIRANTE COMPLEMENTARIO`)
2. Valida que el programa exista
3. Valida que el aspirante exista
4. Actualiza el estado a 4
5. Registra en log
6. Retorna respuesta de éxito o error

#### `validarDocumentos(int $complementarioId, AspiranteDocumentoService $documentoService): array`
Valida documentos de identidad de aspirantes en Google Drive.

**Flujo:**
1. Valida que el programa exista
2. Valida que haya aspirantes
3. Obtiene archivos de Google Drive
4. Para cada aspirante:
   - Construye patrón de búsqueda
   - Busca documento en Google Drive
   - Actualiza estado `condocumento` en la persona
5. Retorna estadísticas (total, con documento, sin documento, errores)

#### `obtenerEstadisticasPrograma(int $programaId): array`
Obtiene estadísticas básicas de un programa:
- Total de aspirantes
- Aspirantes activos (estado 1)
- Aspirantes aceptados (estado 3)
- Cupos disponibles

### 4. Servicio: `AspiranteExportService`

**Ubicación:** `app/Services/Complementarios/AspiranteExportService.php`

**Responsabilidades:**
- Exportar aspirantes a Excel en formato SOFIA Plus
- Descargar cédulas en PDF combinado desde Google Drive

**Métodos principales:**

#### `exportarAspirantesExcel(int $complementarioId): StreamedResponse`
Exporta aspirantes a Excel en formato compatible con SOFIA Plus v1.0.

**Formato Excel:**
- **Título:** "FORMATO PARA LA INSCRIPCIÓN DE ASPIRANTES EN SOFIA PLUS v1.0"
- **Columnas:**
  - A: Resultado del Registro (vacío)
  - B: Tipo de Identificación (CC, TI, CE, etc.)
  - C: Número de Identificación
  - D: Código de la ficha (vacío)
  - E: Tipo Población Aspirante (caracterización)
  - F: (vacío)
  - G: Código Empresa (vacío)

**Características:**
- Estilos personalizados (bordes, colores, fuentes)
- Conversión de tipos de documento a iniciales
- Incluye caracterización de población
- Solo exporta aspirantes válidos (no rechazados, con documento, registrados en SOFIA)

#### `descargarCedulas(int $complementarioId): StreamedResponse`
Descarga cédulas de identidad de aspirantes en un PDF combinado.

**Flujo:**
1. Valida que el programa exista
2. Obtiene aspirantes con documentos
3. Crea directorio temporal
4. Para cada aspirante:
   - Busca documento en Google Drive
   - Descarga archivo temporal
   - Agrega páginas al PDF
5. Genera archivo PDF final
6. Limpia archivos temporales
7. Retorna respuesta de descarga

**Manejo de errores:**
- Si no hay aspirantes con documentos, lanza `AspirantesSinDocumentosException`
- Si no se pueden descargar documentos, lanza `DescargaDocumentosException`

### 5. Servicio: `AspiranteDocumentoService`

**Ubicación:** `app/Services/Complementarios/AspiranteDocumentoService.php`

**Responsabilidades:**
- Construir patrones de búsqueda para documentos
- Buscar documentos en Google Drive
- Manejar variantes de nombres de archivos
- Procesar archivos PDF

**Métodos principales:**

#### `construirPatronBusqueda(Persona $persona): string`
Construye un patrón de búsqueda para encontrar documentos en Google Drive.

**Formato del patrón:**
```
{TIPO_DOCUMENTO}_{NUMERO_DOCUMENTO}_{PRIMER_NOMBRE}_{PRIMER_APELLIDO}_
```

**Ejemplo:**
```
CÉDULA_DE_CIUDADANÍA_1234567890_JUAN_PÉREZ_
```

#### `buscarDocumentoEnGoogleDrive(array $files, string $patron): bool`
Busca un documento en Google Drive usando variantes del patrón.

**Variantes generadas:**
1. Patrón original con guiones bajos
2. Patrón con espacios en lugar de guiones bajos
3. Patrón con guiones bajos en lugar de espacios
4. Patrón sin nombres (solo tipo_documento + numero_documento)
5. Patrón sin nombres con espacios

**Lógica de búsqueda:**
- Itera sobre todos los archivos en Google Drive
- Compara cada archivo con todas las variantes del patrón
- Verifica existencia del archivo en el disco
- Retorna `true` si encuentra coincidencia, `false` en caso contrario

#### `encontrarArchivoEnGoogleDrive(string $patron): ?string`
Encuentra y retorna la ruta completa del archivo en Google Drive.

**Retorna:**
- Ruta completa del archivo si lo encuentra
- `null` si no encuentra el archivo

#### `agregarPaginasAPDF($pdf, string $tempFilePath): void`
Agrega páginas de un PDF temporal al PDF principal usando FPDI.

#### `getGoogleDriveFiles(): array`
Obtiene lista de archivos del directorio `documentos_aspirantes` en Google Drive.

**Manejo de errores:**
- Si hay error al acceder a Google Drive, lanza `GoogleDriveException`

### 6. Repositorio: `AspiranteComplementarioRepository`

**Ubicación:** `app/Repositories/Complementarios/AspiranteComplementarioRepository.php`

**Responsabilidades:**
- Abstracción de acceso a datos
- Consultas complejas a la base de datos
- Agregación de datos y estadísticas

**Métodos principales:**

#### `findByPrograma(int $programaId, array $relations = []): Collection`
Obtiene todos los aspirantes de un programa con relaciones opcionales.

#### `findByProgramaConDocumentos(int $programaId): Collection`
Obtiene aspirantes que tienen documentos de identidad.

#### `findByProgramaConDocumentosExcluyendoRechazados(int $programaId): Collection`
Obtiene aspirantes con documentos excluyendo los rechazados (estado != 4).

#### `findByProgramaParaExportacion(int $programaId): Collection`
Obtiene aspirantes válidos para exportación:
- No rechazados (estado != 4)
- Con documento (condocumento = 1)
- Registrados en SOFIA (estado_sofia != 0)

#### `existeInscripcion(int $personaId, int $programaId): bool`
Verifica si una persona ya está inscrita en un programa.

#### `countByEstado(int $programaId, int $estado): int`
Cuenta aspirantes por estado en un programa.

#### `countByPrograma(int $programaId): int`
Cuenta total de aspirantes en un programa.

#### `getEstadisticasExclusion(int $programaId): array`
Obtiene estadísticas de exclusión:
- Total de aspirantes
- Rechazados
- Sin documento
- No registrados en SOFIA
- Válidos para exportación

#### `getTendenciaInscripciones(int $meses = 6): Collection`
Obtiene tendencia de inscripciones por mes (últimos N meses).

#### `getDistribucionPorProgramas(): Collection`
Obtiene distribución de aspirantes por programas.

### 7. Form Request: `AspiranteRequest`

**Ubicación:** `app/Http/Requests/Complementarios/AspiranteRequest.php`

**Responsabilidades:**
- Validar datos de entrada para agregar aspirantes
- Mensajes de error personalizados

**Reglas de validación:**
```php
[
    'numero_documento' => 'required|string|max:191',
    'observaciones' => 'nullable|string|max:500',
]
```

## 🔄 Flujos de Trabajo

### Flujo 1: Agregar Aspirante a un Programa

```
1. Usuario accede a /programas-complementarios/{curso}
   ↓
2. Sistema muestra lista de aspirantes del programa
   ↓
3. Usuario hace clic en "Agregar Aspirante"
   ↓
4. Sistema muestra formulario de búsqueda
   ↓
5. Usuario ingresa número de documento
   ↓
6. Sistema busca persona (AJAX)
   ↓
7. Si encuentra persona:
   - Muestra datos de la persona
   - Usuario confirma agregar
   ↓
8. Sistema valida:
   - Programa existe
   - Persona existe
   - Persona no está ya inscrita
   ↓
9. Si validaciones OK:
   - Crea registro en aspirantes_complementarios
   - Estado = 1 (En proceso)
   - Observaciones = "Agregado manualmente desde gestión de aspirantes"
   ↓
10. Sistema retorna respuesta JSON de éxito
```

### Flujo 2: Validar Documentos en Google Drive

```
1. Usuario accede a /programas-complementarios/{curso}
   ↓
2. Usuario hace clic en "Validar Documentos"
   ↓
3. Sistema valida:
   - Programa existe
   - Hay aspirantes en el programa
   ↓
4. Sistema obtiene lista de archivos de Google Drive
   ↓
5. Para cada aspirante:
   a. Construye patrón de búsqueda
   b. Genera variantes del patrón
   c. Busca archivo en Google Drive
   d. Si encuentra:
      - Actualiza condocumento = 1 en persona
      - Incrementa contador "con documento"
   e. Si no encuentra:
      - Actualiza condocumento = 0 en persona
      - Incrementa contador "sin documento"
   ↓
6. Sistema retorna estadísticas:
   - Total procesados
   - Con documento
   - Sin documento
   - Errores
```

### Flujo 3: Exportar a Excel

```
1. Usuario accede a /programas-complementarios/{curso}
   ↓
2. Usuario hace clic en "Exportar a Excel"
   ↓
3. Sistema valida que el programa exista
   ↓
4. Sistema obtiene aspirantes válidos para exportación:
   - Estado != 4 (no rechazados)
   - condocumento = 1 (con documento)
   - estado_sofia != 0 (registrados en SOFIA)
   ↓
5. Sistema crea hoja de cálculo Excel:
   - Título: "FORMATO PARA LA INSCRIPCIÓN DE ASPIRANTES EN SOFIA PLUS v1.0"
   - Encabezados en fila 2
   - Datos desde fila 3
   ↓
6. Para cada aspirante:
   - Convierte tipo documento a iniciales (CC, TI, CE, etc.)
   - Obtiene número de documento
   - Obtiene caracterización de población
   - Llena fila en Excel
   ↓
7. Sistema aplica estilos:
   - Bordes negros
   - Fuente Calibri
   - Tamaño de letra 8 para datos
   - Ajuste de texto
   ↓
8. Sistema genera archivo y retorna descarga
```

### Flujo 4: Descargar Cédulas en PDF

```
1. Usuario accede a /programas-complementarios/{curso}
   ↓
2. Usuario hace clic en "Descargar Cédulas"
   ↓
3. Sistema valida:
   - Programa existe
   - Hay aspirantes con documentos
   ↓
4. Sistema obtiene aspirantes con documentos
   ↓
5. Sistema crea directorio temporal
   ↓
6. Sistema crea objeto PDF (FPDI)
   ↓
7. Para cada aspirante:
   a. Construye patrón de búsqueda
   b. Busca archivo en Google Drive
   c. Si encuentra:
      - Descarga archivo a directorio temporal
      - Agrega páginas al PDF
      - Guarda ruta del archivo temporal
   d. Si no encuentra:
      - Registra en log
      - Continúa con siguiente
   ↓
8. Si se agregaron archivos:
   - Genera nombre de archivo PDF final
   - Guarda PDF en directorio temporal
   - Retorna respuesta de descarga
   - Limpia archivos temporales después de descarga
   ↓
9. Si no se agregaron archivos:
   - Limpia archivos temporales
   - Lanza excepción AspirantesSinDocumentosException
```

### Flujo 5: Rechazar Aspirante

```
1. Usuario accede a /programas-complementarios/{curso}
   ↓
2. Usuario hace clic en "Rechazar" en un aspirante
   ↓
3. Sistema muestra confirmación (SweetAlert2)
   ↓
4. Usuario confirma rechazo
   ↓
5. Sistema valida:
   - Usuario tiene permiso "ELIMINAR ASPIRANTE COMPLEMENTARIO"
   - Programa existe
   - Aspirante existe
   ↓
6. Si validaciones OK:
   - Actualiza estado = 4 (Rechazado)
   - Registra en log
   ↓
7. Sistema retorna respuesta JSON de éxito
```

## 🔐 Validaciones y Reglas de Negocio

### Validaciones al Agregar Aspirante

1. **Programa debe existir**
   - El `complementario_id` debe existir en `complementarios_ofertados`
   - Si no existe, retorna error: "Programa no encontrado."

2. **Persona debe existir**
   - El número de documento debe existir en `personas`
   - Si no existe, retorna error: "No se encontró ninguna persona registrada con el número de documento '{numero}'."

3. **Persona no debe estar ya inscrita**
   - No puede haber duplicados en la misma combinación `persona_id` + `complementario_id`
   - Si ya existe, retorna error: "La persona con documento '{numero}' ya se encuentra inscrita en este programa complementario."

### Validaciones al Rechazar Aspirante

1. **Permisos del usuario**
   - Usuario debe tener permiso `ELIMINAR ASPIRANTE COMPLEMENTARIO`
   - Si no tiene permiso, retorna error 403: "No tiene permisos para rechazar aspirantes."

2. **Programa debe existir**
   - El `complementario_id` debe existir
   - Si no existe, retorna error: "Programa no encontrado."

3. **Aspirante debe existir**
   - El `aspirante_id` debe existir en el programa
   - Si no existe, retorna error: "Aspirante no encontrado."

### Validaciones al Validar Documentos

1. **Programa debe existir**
   - El `complementario_id` debe existir
   - Si no existe, retorna error: "Programa no encontrado."

2. **Debe haber aspirantes**
   - Debe existir al menos un aspirante en el programa
   - Si no hay aspirantes, retorna error: "No hay aspirantes en este programa para validar documentos."

### Validaciones al Exportar a Excel

1. **Programa debe existir**
   - El `complementario_id` debe existir
   - Si no existe, lanza `ProgramaNoEncontradoException`

2. **Solo exporta aspirantes válidos:**
   - Estado != 4 (no rechazados)
   - `condocumento = 1` (con documento)
   - `estado_sofia != 0` (registrados en SOFIA)

### Validaciones al Descargar Cédulas

1. **Programa debe existir**
   - El `complementario_id` debe existir
   - Si no existe, lanza `ProgramaNoEncontradoException`

2. **Debe haber aspirantes con documentos**
   - Debe existir al menos un aspirante con `condocumento = 1`
   - Si no hay, lanza `AspirantesSinDocumentosException`

## 🔌 Integraciones

### 1. Integración con Google Drive

**Propósito:** Almacenar y recuperar documentos de identidad de aspirantes.

**Configuración:**
- Disco configurado en `config/filesystems.php` como `google`
- Directorio base: `documentos_aspirantes`

**Operaciones:**
- **Listar archivos:** `Storage::disk('google')->files('documentos_aspirantes')`
- **Verificar existencia:** `Storage::disk('google')->exists($file)`
- **Obtener contenido:** `Storage::disk('google')->get($file)`

**Patrones de búsqueda:**
El sistema genera múltiples variantes de patrones para encontrar archivos:
- Con guiones bajos: `CÉDULA_DE_CIUDADANÍA_1234567890_JUAN_PÉREZ_`
- Con espacios: `CÉDULA DE CIUDADANÍA 1234567890 JUAN PÉREZ`
- Sin nombres: `CÉDULA_DE_CIUDADANÍA_1234567890_`

### 2. Integración con SOFIA Plus

**Propósito:** Validar y registrar aspirantes en el sistema SOFIA Plus del SENA.

**Componentes relacionados:**
- `ValidacionSofiaController`: Controlador para iniciar validación
- `ValidarSofiaJob`: Job en cola para procesar validación
- `SofiaValidationService`: Servicio de validación
- `SofiaValidationProgress`: Modelo de progreso de validación
- `SenasofiaplusValidationLog`: Modelo de logs de validación

**Flujo de validación:**
1. Usuario inicia validación desde la interfaz
2. Sistema crea registro en `sofia_validation_progress`
3. Sistema despacha job `ValidarSofiaJob` en cola
4. Job procesa aspirantes en lotes
5. Para cada aspirante:
   - Valida datos en SOFIA Plus
   - Actualiza `estado_sofia` en `personas`
   - Crea log en `senasofiaplus_validation_logs`
6. Actualiza progreso en `sofia_validation_progress`

**Estados en SOFIA:**
- `0`: No registrado
- `1`: Registrado exitosamente
- `2`: Pendiente de validación

## 📊 Estadísticas y Reportes

### Estadísticas de Exclusión

El sistema proporciona estadísticas sobre aspirantes excluidos de la exportación:

```php
[
    'total' => 100,              // Total de aspirantes
    'rechazados' => 5,           // Aspirantes rechazados (estado 4)
    'sin_documento' => 10,       // Sin documento en Google Drive
    'no_registrados_sofia' => 15, // No registrados en SOFIA
    'validos' => 70              // Válidos para exportación
]
```

### Estadísticas de Programa

Estadísticas básicas de un programa complementario:

```php
[
    'total_aspirantes' => 100,
    'aspirantes_activos' => 80,    // Estado 1
    'aspirantes_aceptados' => 15,  // Estado 3
    'cupos_disponibles' => 20      // cupos - total_aspirantes
]
```

## 🛣️ Rutas del Módulo

Todas las rutas están definidas en `routes/complementarios/gestion_aspirante.php`:

| Método | Ruta | Nombre | Controlador | Middleware |
|--------|------|--------|-------------|------------|
| GET | `/gestion-aspirantes` | `gestion-aspirantes` | `AspiranteComplementarioController@gestionAspirantes` | `auth` |
| GET | `/programas-complementarios/{curso}` | `programas-complementarios.ver-aspirantes` | `AspiranteComplementarioController@verAspirantes` | `auth` |
| POST | `/programas-complementarios/{complementarioId}/agregar-aspirante` | `programas-complementarios.agregar-aspirante` | `AspiranteComplementarioController@agregarAspirante` | `auth` |
| DELETE | `/programas-complementarios/{complementarioId}/aspirante/{aspiranteId}` | `programas-complementarios.eliminar-aspirante` | `AspiranteComplementarioController@eliminarAspirante` | `auth` |
| GET | `/programas-complementarios/{complementarioId}/exportar-excel` | `programas-complementarios.exportar-excel` | `AspiranteComplementarioController@exportarAspirantesExcel` | `auth` |
| GET | `/programas-complementarios/{complementarioId}/descargar-cedulas` | `programas-complementarios.descargar-cedulas` | `AspiranteComplementarioController@descargarCedulas` | `auth` |
| POST | `/programas-complementarios/{complementarioId}/validar-documentos` | `programas-complementarios.validar-documentos` | `AspiranteComplementarioController@validarDocumentos` | `auth` |

## 🎨 Vistas (Blade Templates)

### Vista Principal: `index.blade.php`

**Ubicación:** `resources/views/complementarios/aspirantes/index.blade.php`

**Contenido:**
- Lista de programas complementarios
- Conteo de aspirantes por programa
- Enlaces para acceder a gestión de cada programa

### Vista de Programa: `programa.blade.php`

**Ubicación:** `resources/views/complementarios/aspirantes/programa.blade.php`

**Contenido:**
- Información del programa (nombre, código, cupos, modalidad, jornada)
- Tabla de aspirantes con:
  - Número de documento
  - Nombres completos
  - Estado
  - Acciones (rechazar, ver detalles)
- Botones de acción:
  - Agregar Aspirante
  - Validar Documentos
  - Exportar a Excel
  - Descargar Cédulas
  - Validar en SOFIA Plus

### Vista de Crear: `create.blade.php`

**Ubicación:** `resources/views/complementarios/aspirantes/create.blade.php`

**Contenido:**
- Formulario para buscar persona por número de documento
- Formulario para crear nueva persona (si no existe)
- Campos de persona (nombres, apellidos, documento, etc.)
- Selectores de parámetros (tipo documento, género, caracterización, etc.)

## 🔧 Configuración y Dependencias

### Dependencias de Composer

```json
{
    "require": {
        "phpoffice/phpspreadsheet": "^1.29",  // Para exportación Excel
        "setasign/fpdi": "^2.6"                // Para manipulación PDF
    }
}
```

### Configuración de Google Drive

Configurado en `config/filesystems.php`:

```php
'google' => [
    'driver' => 'google',
    'root' => 'documentos_aspirantes',
    // ... configuración de credenciales
]
```

## 📝 Logging y Auditoría

El módulo registra las siguientes operaciones:

1. **Agregar aspirante:**
   - Log de éxito con datos del aspirante
   - Log de error con detalles de excepción

2. **Rechazar aspirante:**
   - Log de éxito con ID de aspirante, programa y usuario
   - Log de error con detalles de excepción

3. **Validar documentos:**
   - Log de inicio con total de aspirantes
   - Log de documento encontrado/no encontrado
   - Log de resumen con estadísticas

4. **Exportar/Descargar:**
   - Log de inicio y finalización
   - Log de errores con detalles

## 🧪 Testing

El módulo incluye tests unitarios y de integración:

**Ubicación:** `tests/Modulos/Complementarios/`

**Tests principales:**
- `AspiranteManagementServiceTest`: Tests del servicio de gestión
- `SofiaValidationServiceTest`: Tests de validación SOFIA
- `AspiranteExportServiceTest`: Tests de exportación

## 🔄 Migraciones

**Ubicación:** `database/migrations/batch_17_complementarios/`

**Migraciones relacionadas:**
1. `2025_10_27_000172_create_aspirantes_complementarios_table.php`: Crea tabla principal
2. `2025_11_30_215231_add_documento_identidad_to_aspirantes_complementarios_table.php`: Agrega campos de documento
3. `2025_10_27_000178_create_sofia_validation_progress_table.php`: Crea tabla de progreso
4. `2025_11_12_000001_create_senasofiaplus_validation_logs_table.php`: Crea tabla de logs

## 📌 Consideraciones Importantes

1. **Restricción única:** Una persona no puede estar inscrita dos veces en el mismo programa.

2. **Eliminación suave:** Al "eliminar" un aspirante, solo se cambia su estado a 4 (Rechazado), no se elimina físicamente el registro.

3. **Validación de documentos:** El sistema busca documentos usando múltiples variantes de patrones para manejar diferentes formatos de nombres de archivos.

4. **Exportación a Excel:** Solo exporta aspirantes válidos (no rechazados, con documento, registrados en SOFIA).

5. **Descarga de cédulas:** Solo descarga documentos de aspirantes que tienen `condocumento = 1`.

6. **Integración SOFIA:** La validación en SOFIA Plus se ejecuta en segundo plano mediante jobs en cola.

7. **Permisos:** Algunas operaciones requieren permisos específicos (ej: `ELIMINAR ASPIRANTE COMPLEMENTARIO`).

## 🚀 Mejoras Futuras

Posibles mejoras para el módulo:

1. **Filtros avanzados:** Filtrar aspirantes por estado, fecha de inscripción, etc.
2. **Búsqueda de aspirantes:** Buscar aspirantes por nombre, documento, etc.
3. **Historial de cambios:** Registrar cambios de estado con timestamps y usuarios
4. **Notificaciones:** Notificar a aspirantes sobre cambios de estado
5. **Reportes avanzados:** Generar reportes PDF con estadísticas detalladas
6. **Importación masiva:** Importar aspirantes desde Excel
7. **Validación automática:** Validar documentos automáticamente al agregar aspirante

