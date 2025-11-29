# 📚 Documentación Completa - CDATTG Asistence Web

Bienvenido a la documentación del sistema de gestión de asistencias y programas complementarios del SENA.

> **Para nuevos desarrolladores**: Empieza con la [Guía de Inicio Rápido](#-guía-de-inicio-rápido-para-nuevos-desarrolladores) y luego explora las secciones según tus necesidades.

---

## 🚀 Guía de Inicio Rápido para Nuevos Desarrolladores

### 1️⃣ Configuración Inicial

```bash
# Clonar y configurar
git clone [url-del-repositorio]
cd academica_web
composer install
npm install
cp .env.example .env
php artisan key:generate

# Base de datos
php artisan migrate:module --all --fresh
php artisan db:seed

# Servicios en desarrollo
php artisan serve              # Terminal 1: Servidor web
php artisan reverb:start       # Terminal 2: WebSocket (opcional)
php artisan queue:work         # Terminal 3: Colas (opcional)
```

### 2️⃣ Estructura del Proyecto

- **`app/Http/Controllers/`** - Controladores (70+)
- **`app/Services/`** - Lógica de negocio (50 servicios)
- **`app/Repositories/`** - Acceso a datos (45 repositorios)
- **`app/Models/`** - Modelos Eloquent (63 modelos)
- **`routes/`** - Rutas modulares por funcionalidad
- **`database/migrations/`** - 120+ migraciones en 15 módulos

### 3️⃣ Convenciones del Proyecto

- **Patrón Repository**: Acceso a datos en repositorios, no en controladores
- **Services**: Lógica de negocio en servicios
- **Form Requests**: Validación en Form Requests
- **Policies**: Autorización con políticas
- **PSR-12**: Formato de código estándar

### 4️⃣ Comandos Esenciales

```bash
# Calidad de código
php artisan refactor:sonarqube --dry-run  # Analizar
php artisan pint                          # Formatear

# Base de datos
php artisan migrate:module --list        # Ver módulos
php artisan migrate:module --all         # Migrar todo

# Testing
php artisan test                         # Ejecutar tests
```

---

## 📖 Índice de Documentación

### 🚀 Despliegue y Producción

Documentación completa para desplegar y mantener el sistema en producción.

| Documento | Descripción |
|-----------|-------------|
| [🐳 Docker](deployment/docker.md) | Configuración y despliegue con Docker |
| [🔔 WebSocket](deployment/websocket.md) | Configuración de notificaciones en tiempo real |
| [⚙️ Sistema de Colas](deployment/queues.md) | Guía completa de workers y colas |
| [📊 Laravel Horizon](deployment/horizon.md) | Dashboard de monitoreo de colas |

> **Nota**: Para comandos de producción, consulta también la sección [Despliegue a Producción](../README.md#-despliegue-a-producción) en el README principal.

### 💻 Desarrollo

Guías para desarrolladores sobre herramientas, patrones y buenas prácticas.

| Documento | Descripción |
|-----------|-------------|
| [🤖 Refactorización Automática](development/refactoring.md) | Comando SonarQube para correcciones automáticas |
| [🧩 Blade Components](development/blade-components.md) | Componentes reutilizables de Blade |
| [📋 Table Refactoring](development/table-refactoring.md) | Guía de refactorización de tablas |
| [🗂️ Migraciones Modulares](development/migrations-modules.md) | Sistema modular de migraciones (15 batches) |
| [📝 Reorganización de Migraciones](development/migrations-reorganization.md) | Historial de reorganización |

### 📚 Guías de Usuario y Funcionalidades

Documentación sobre funcionalidades específicas del sistema.

| Documento | Descripción |
|-----------|-------------|
| [📚 Módulo de Complementarios](modulo-complementarios.md) | Documentación completa del módulo de programas complementarios |
| [📦 Sistema de Inventario](guides/sistema-inventario.md) | Sistema híbrido de inventario |
| [📅 Días de Formación](guides/dias-formacion.md) | Gestión de días de formación |
| [👨‍🏫 Instructor - Días](guides/instructor-dias.md) | Asignación de días a instructores |
| [📱 Diseño Offline-First](guides/diseno-offline-first.md) | Arquitectura offline-first (diseño) |

### 🌐 API

| Documento | Descripción |
|-----------|-------------|
| [📡 Documentación API](api/api.md) | Endpoints REST y especificaciones |

### 🔧 Historial de Correcciones

Documentación histórica de correcciones y mejoras implementadas.

| Documento | Descripción |
|-----------|-------------|
| [✏️ Correcciones Vista Editar](fixes/correcciones-vista-editar.md) | Correcciones en vistas de edición |
| [📋 Resumen de Correcciones](fixes/resumen-correcciones.md) | Historial general de correcciones |
| [📅 Implementación Días Instructor](fixes/implementacion-dias-instructor.md) | Implementación completa |
| [🔗 Integración Días Instructor](fixes/integracion-dias-instructor.md) | Integración de días |
| [✅ Resumen Final](fixes/resumen-final.md) | Resumen de implementaciones |
| [📊 Resumen Días Instructor](fixes/resumen-dias-instructor.md) | Resumen específico |

---

## 🏗️ Arquitectura del Proyecto

### Stack Tecnológico

**Backend:**
- Laravel 12.0
- PHP 8.3+
- MySQL 8.0+
- Redis (cache, colas, WebSocket)
- Laravel Horizon 5.40 (colas)
- Laravel Reverb 1.6 (WebSocket)
- Laravel Sanctum 4.0 (API)

**Frontend:**
- Blade Templates
- AdminLTE 3.15
- Livewire 3.6
- Alpine.js
- Vite 6.2.2
- SweetAlert2

**Librerías:**
- PHPSpreadsheet (Excel)
- DomPDF (PDF)
- Endroid QR Code
- Google Drive API

### Módulos Principales

1. **Asistencias** - Control de asistencias con QR
2. **Programas Complementarios** - Gestión de programas
3. **Gestión de Personas** - Personas, instructores, aprendices
4. **Infraestructura** - Sedes, bloques, pisos, ambientes
5. **Inventario** - Sistema híbrido de inventario
6. **Caracterización** - Fichas, competencias, guías
7. **Entrada/Salida** - Control de ingreso por sede
8. **Talento Humano** - Instructores y asignaciones
9. **Reportes** - Reportes y estadísticas

### Sistema de Migraciones Modulares

El proyecto utiliza **15 batches** de migraciones organizados por funcionalidad:

```bash
# Ver módulos disponibles
php artisan migrate:module --list

# Migrar todo
php artisan migrate:module --all --fresh
```

Ver [Migraciones Modulares](development/migrations-modules.md) para más detalles.

---

## 🔧 Comandos Útiles por Categoría

### Desarrollo Local

```bash
# Servidores
php artisan serve              # Servidor web
php artisan reverb:start       # WebSocket
php artisan queue:work         # Colas
php artisan horizon            # Dashboard de colas

# Assets
npm run dev                    # Desarrollo (watch)
npm run build                  # Producción
```

### Base de Datos

```bash
# Migraciones modulares
php artisan migrate:module --list
php artisan migrate:module --all
php artisan migrate:module batch_01_sistema_base

# Seeders
php artisan db:seed
```

### Calidad de Código

```bash
# Análisis y corrección
php artisan refactor:sonarqube --dry-run
php artisan refactor:sonarqube
php artisan pint

# Testing
php artisan test
php artisan test --coverage
```

### Producción

```bash
# Optimización
composer install --optimize-autoloader --no-dev
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Ver [README principal](../README.md#-comandos-útiles) para lista completa.

---

## 🆘 Solución de Problemas Comunes

### Problema: Colas no se procesan

```bash
# Verificar workers
ps aux | grep "queue:work"

# Verificar colas
php artisan queue:monitor

# Ver logs
tail -f storage/logs/laravel.log
```

**Solución**: Ver [Sistema de Colas](deployment/queues.md)

### Problema: WebSocket no funciona

```bash
# Verificar Reverb
php artisan reverb:start --debug

# Verificar configuración
php artisan config:show reverb
```

**Solución**: Ver [WebSocket](deployment/websocket.md)

### Problema: Migraciones fallan

```bash
# Verificar módulos
php artisan migrate:module --list

# Migrar módulo específico
php artisan migrate:module batch_01_sistema_base
```

**Solución**: Ver [Migraciones Modulares](development/migrations-modules.md)

### Problema: Código con errores de linting

```bash
# Analizar
php artisan refactor:sonarqube --dry-run

# Corregir automáticamente
php artisan refactor:sonarqube

# Formatear
php artisan pint
```

**Solución**: Ver [Refactorización](development/refactoring.md)

---

## 📝 Agregar Nueva Documentación

### Estructura de Carpetas

```
docs/
├── deployment/     # Despliegue, Docker, producción
├── development/    # Herramientas de desarrollo
├── guides/         # Guías de usuario y funcionalidades
├── api/            # Documentación de API
└── fixes/          # Historial de correcciones
```

### Proceso

1. Crea el archivo `.md` en la carpeta correspondiente
2. Usa formato Markdown con emojis para mejor legibilidad
3. Actualiza este README con un enlace al nuevo documento
4. Sigue el formato de los documentos existentes

### Plantilla Básica

```markdown
# 📝 Título del Documento

## 📋 Descripción

Breve descripción del contenido.

## 🚀 Inicio Rápido

Pasos básicos para empezar.

## 📖 Contenido Detallado

Información completa.

## 🔧 Ejemplos

Ejemplos de código o configuración.

## 🆘 Solución de Problemas

Problemas comunes y soluciones.
```

---

## 🔗 Enlaces Útiles

- [📖 README Principal](../README.md) - Información general del proyecto
- [💻 Código Fuente](../app/) - Estructura de la aplicación
- [📦 Composer](../composer.json) - Dependencias PHP
- [📦 NPM](../package.json) - Dependencias JavaScript
- [🐳 Docker](../docker-compose.yml) - Configuración Docker

---

## 📞 Soporte

Si necesitas ayuda:

1. **Revisa esta documentación** - La mayoría de problemas están documentados
2. **Consulta los logs** - `storage/logs/laravel.log`
3. **Ejecuta diagnósticos**:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan route:clear
   ```
4. **Verifica servicios**:
   ```bash
   php artisan queue:monitor
   php artisan horizon:status
   ```

---

**Última actualización**: 2025-11-17  
**Versión del proyecto**: Laravel 12.0  
**Mantenedores**: ADSO - 2923560
