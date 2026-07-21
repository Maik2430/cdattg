# 🎓 CDATTG Asistence Web

Sistema de gestión de asistencias y programas complementarios para el SENA (Servicio Nacional de Aprendizaje).

## 📋 Descripción

Aplicación web desarrollada en Laravel para la gestión integral de:

### 🎯 Módulos Principales

- ✅ **Asistencias**: Control de asistencias de aprendices e instructores con registro QR
- 📚 **Programas Complementarios**: Gestión completa de programas de formación complementaria
- 👥 **Gestión de Personas**: Administración de personas, instructores, aprendices y visitantes
- 🏢 **Infraestructura**: Gestión de sedes, bloques, pisos y ambientes
- 📦 **Inventario**: Sistema híbrido de inventario con productos, categorías, proveedores y órdenes
- 📋 **Caracterización**: Fichas de caracterización, competencias y guías de aprendizaje
- 🚪 **Entrada/Salida**: Control de ingreso y salida de personas por sede
- 👨‍🏫 **Talento Humano**: Gestión de instructores, asignaciones y días de formación
- 📊 **Reportes y Estadísticas**: Reportes detallados y dashboards
- 🔔 **Notificaciones en Tiempo Real**: WebSocket con Laravel Reverb
- 📱 **API REST**: Endpoints para aplicación móvil Flutter
- 📄 **Importación Masiva**: Importación de personas desde archivos Excel
- 🔐 **Permisos y Roles**: Sistema de control de acceso basado en Spatie Permission

## 🚀 Inicio Rápido

### Requisitos Previos
- **PHP**: 8.4+
- **Composer**: 2.0+
- **MySQL**: 8.0+
- **Node.js**: 24+ (requerido por Vite 8)
- **Redis**: Recomendado (para cache, colas y WebSocket)
- **Extensiones PHP**: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML

### Instalación

```bash
# Clonar repositorio
git clone [url-del-repositorio]
cd academica_web

# Instalar dependencias
composer install
npm install

# Configurar entorno
cp .env.example .env
php artisan key:generate

# Configurar base de datos en .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=academica_web
# DB_USERNAME=root
# DB_PASSWORD=

# Migrar base de datos (sistema modular)
php artisan migrate:module --all --fresh
php artisan db:seed

# O migración tradicional
# php artisan migrate --seed

# Compilar assets
npm run build

# Iniciar servidor
php artisan serve

# En otra terminal: Iniciar WebSocket (opcional)
php artisan reverb:start

# En otra terminal: Iniciar colas (opcional)
php artisan queue:work
```

## 📚 Documentación

Toda la documentación del proyecto está organizada en la carpeta [`docs/`](docs/README.md):

### 🚀 Despliegue
- [Docker](docs/deployment/docker.md) - Configuración con contenedores
- [WebSocket](docs/deployment/websocket.md) - Notificaciones en tiempo real

### 💻 Desarrollo
- [Refactorización](docs/development/refactoring.md) - Comando de refactorización automática
- [Blade Components](docs/development/blade-components.md) - Componentes reutilizables
- [Table Refactoring](docs/development/table-refactoring.md) - Refactorización de tablas
- [Migraciones Modulares](docs/development/migrations-modules.md) - Sistema modular de base de datos

### 📚 Guías
- [Sistema de Inventario](docs/guides/sistema-inventario.md) - Sistema híbrido
- [Días de Formación](docs/guides/dias-formacion.md) - Gestión de horarios

### 🌐 API
- [Documentación API](docs/api/api.md) - Endpoints REST

**📖 [Ver índice completo de documentación →](docs/README.md)**

## 🛠️ Stack Tecnológico

### Backend
- **Framework**: Laravel 13.0
- **PHP**: 8.4+
- **Base de Datos**: MySQL 8.0+
- **Cache/Colas**: Redis + Predis 3.2
- **Colas**: Laravel Horizon 5.40
- **WebSocket**: Laravel Reverb 1.6
- **Autenticación API**: Laravel Sanctum 4.0
- **Permisos**: Spatie Laravel Permission 6.4

### Frontend
- **Templates**: Blade Templates
- **UI Framework**: AdminLTE 3.15
- **JavaScript**: Alpine.js, Livewire 3.6
- **Build Tool**: Vite 6.2.2
- **Notificaciones**: SweetAlert2 11.6
- **WebSocket Client**: Laravel Echo + Pusher JS

### Librerías Principales
- **PDF**: DomPDF 3.0
- **Excel**: PHPSpreadsheet 5.9
- **QR Codes**: Endroid QR Code 5.1
- **Google Drive**: Masbug Flysystem Google Drive 2.4
- **Google APIs**: Google API Client 2.0
- **HTTP Client**: Guzzle 7.2

### DevOps
- **Contenedores**: Docker + Docker Compose
- **Testing**: PHPUnit 12.0, Playwright 1.61
- **Code Quality**: Laravel Pint 1.0
- **Debug**: Laravel Debugbar 3.16 (dev)

## 🔧 Comandos Útiles

### Desarrollo
```bash
# Servidor de desarrollo
php artisan serve                          # Iniciar servidor web
php artisan reverb:start                   # Iniciar servidor WebSocket
php artisan queue:work                     # Procesar colas
php artisan horizon                        # Dashboard de colas (Horizon)

# Assets
npm run dev                                # Compilar assets en desarrollo (watch)
npm run build                              # Compilar assets para producción

# Base de datos
php artisan migrate:module --list          # Listar módulos de migración
php artisan migrate:module --all          # Migrar todos los módulos
php artisan migrate:module --all --fresh  # Resetear y migrar todo
php artisan migrate:module batch_01_sistema_base  # Migrar módulo específico
php artisan db:seed                        # Ejecutar seeders
```

### Calidad de Código
```bash
php artisan refactor:sonarqube --dry-run  # Analizar código (sin cambios)
php artisan refactor:sonarqube            # Corregir problemas automáticamente
php artisan pint                           # Formatear código (PSR-12)
```

### Testing
```bash
php artisan test                           # Ejecutar todos los tests
php artisan test --filter=InstructorTest  # Ejecutar test específico
php artisan test --coverage               # Con cobertura de código
```

### Producción
```bash
# Optimizar dependencias
composer install --optimize-autoloader --no-dev
npm run build

# Cachear configuración
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Limpiar caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

## 🚀 Despliegue a Producción

### Checklist Pre-Despliegue

```bash
# 1. Verificar configuración
php artisan config:clear
php artisan config:cache

# 2. Verificar rutas
php artisan route:list

# 3. Ejecutar migraciones
php artisan migrate:module --all --force

# 4. Optimizar autoloader
composer install --optimize-autoloader --no-dev

# 5. Compilar assets
npm run build

# 6. Cachear todo
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### Servicios en Producción

#### 1. Worker de Colas (Queue Worker)

**Opción A: Laravel Horizon (Recomendado)**
```bash
# Iniciar Horizon
php artisan horizon

# Reiniciar Horizon
php artisan horizon:terminate

# Pausar Horizon
php artisan horizon:pause

# Reanudar Horizon
php artisan horizon:continue
```

**Opción B: Queue Worker Manual**
```bash
# Worker básico
php artisan queue:work --tries=3 --timeout=90

# Worker con configuración específica
php artisan queue:work \
    --queue=default,emails,imports \
    --tries=3 \
    --timeout=90 \
    --max-jobs=1000 \
    --max-time=3600 \
    --sleep=3 \
    --memory=512
```

**Configuración con Supervisor (Linux)**
```ini
# /etc/supervisor/conf.d/academica-worker.conf
[program:academica-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /ruta/al/proyecto/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/ruta/al/proyecto/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
# Recargar configuración de Supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start academica-worker:*

# Verificar estado
sudo supervisorctl status
```

**Configuración con systemd (Linux)**
```ini
# /etc/systemd/system/academica-worker.service
[Unit]
Description=Academica Queue Worker
After=network.target

[Service]
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /ruta/al/proyecto/artisan queue:work --sleep=3 --tries=3

[Install]
WantedBy=multi-user.target
```

```bash
# Habilitar y iniciar servicio
sudo systemctl enable academica-worker
sudo systemctl start academica-worker
sudo systemctl status academica-worker
```

#### 2. WebSocket (Laravel Reverb)

```bash
# Iniciar servidor WebSocket
php artisan reverb:start \
    --host=0.0.0.0 \
    --port=8080 \
    --debug

# Con configuración personalizada
php artisan reverb:start \
    --host=0.0.0.0 \
    --port=8080 \
    --hostname=ws.tudominio.com
```

**Configuración con Supervisor**
```ini
# /etc/supervisor/conf.d/academica-reverb.conf
[program:academica-reverb]
command=php /ruta/al/proyecto/artisan reverb:start --host=0.0.0.0 --port=8080
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/ruta/al/proyecto/storage/logs/reverb.log
```

#### 3. Laravel Horizon (Dashboard de Colas)

```bash
# Iniciar Horizon
php artisan horizon

# Verificar estado
php artisan horizon:status

# Terminar y reiniciar
php artisan horizon:terminate
```

**Configuración con Supervisor**
```ini
# /etc/supervisor/conf.d/academica-horizon.conf
[program:academica-horizon]
command=php /ruta/al/proyecto/artisan horizon
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/ruta/al/proyecto/storage/logs/horizon.log
stopwaitsecs=3600
```

### Script de Inicio Completo (Producción)

```bash
#!/bin/bash
# start-production.sh

# Variables
PROJECT_PATH="/ruta/al/proyecto"
PHP_BIN="/usr/bin/php"

# Ir al directorio del proyecto
cd $PROJECT_PATH

# Optimizar
composer install --optimize-autoloader --no-dev --quiet
npm run build --silent

# Cachear
$PHP_BIN artisan config:cache
$PHP_BIN artisan route:cache
$PHP_BIN artisan view:cache
$PHP_BIN artisan event:cache

# Iniciar servicios (con Supervisor/systemd)
# Los servicios deben estar configurados previamente

echo "✅ Servicios de producción iniciados"
```

### Verificación Post-Despliegue

```bash
# Verificar configuración
php artisan config:show

# Verificar rutas
php artisan route:list

# Verificar colas
php artisan queue:monitor

# Verificar Horizon
php artisan horizon:status

# Verificar logs
tail -f storage/logs/laravel.log
tail -f storage/logs/horizon.log
```

### Monitoreo y Mantenimiento

```bash
# Limpiar colas fallidas
php artisan queue:flush

# Reintentar trabajos fallidos
php artisan queue:retry all

# Ver trabajos fallidos
php artisan queue:failed

# Limpiar cache de aplicación
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimizar base de datos
php artisan db:optimize

# Ver estadísticas de Horizon
# Acceder a: https://tudominio.com/horizon
```

### Variables de Entorno de Producción

Asegúrate de configurar en `.env`:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tudominio.com

QUEUE_CONNECTION=redis
REDIS_CLIENT=predis

BROADCAST_DRIVER=reverb
REVERB_APP_ID=tu-app-id
REVERB_APP_KEY=tu-app-key
REVERB_APP_SECRET=tu-app-secret
REVERB_HOST=ws.tudominio.com
REVERB_PORT=8080

HORIZON_PREFIX=academica
```

## ✨ Características Especiales

### 🗂️ Sistema de Migraciones Modulares
El proyecto utiliza un sistema de migraciones modulares con **15 batches** organizados por funcionalidad:
- Sistema base, permisos, ubicaciones, personas
- Infraestructura, programas, instructores/aprendices
- Fichas, relaciones, jornadas/horarios
- Asistencias, competencias, evidencias
- Logs, parámetros, inventario

**Comandos:**
```bash
php artisan migrate:module --list          # Ver módulos disponibles
php artisan migrate:module --all          # Migrar todos los módulos
php artisan migrate:module --all --fresh  # Resetear y migrar
```

### 🔐 Sistema de Permisos y Roles
- Control de acceso basado en roles (RBAC)
- Permisos granulares por módulo
- Integración con Spatie Laravel Permission
- Políticas de autorización por modelo

### 📊 Laravel Horizon
- Dashboard para monitoreo de colas
- Métricas en tiempo real
- Reintentos automáticos
- Configuración de workers

### 🔔 WebSocket con Laravel Reverb
- Notificaciones en tiempo real
- Actualizaciones de asistencias
- Estadísticas de visitantes
- Eventos del sistema

### 📄 Importación Masiva
- Importación de personas desde Excel
- Validación de datos
- Procesamiento en colas
- Reporte de errores detallado

## 🐳 Docker

Para despliegue con Docker, consulta la [documentación de Docker](docs/deployment/docker.md).

```bash
# Inicio rápido con Docker
docker-compose up -d
```

## 🧪 Testing

El proyecto incluye tests automatizados con PHPUnit y Playwright.

```bash
# Ejecutar todos los tests
php artisan test

# Ejecutar tests específicos
php artisan test --filter=InstructorTest

# Con cobertura de código
php artisan test --coverage

# Tests de navegador (Playwright)
npm run test:e2e
```

Para más información sobre WebSocket, consulta la [documentación de WebSocket](docs/deployment/websocket.md).

## 📦 Estructura del Proyecto

```
academica_web/
├── app/
│   ├── Console/Commands/          # Comandos Artisan personalizados
│   ├── Core/                      # Servicios y traits core
│   ├── Events/                    # Eventos del sistema
│   ├── Exceptions/                # Excepciones personalizadas
│   ├── Http/
│   │   ├── Controllers/          # 70+ controladores
│   │   ├── Livewire/              # Componentes Livewire
│   │   ├── Middleware/            # Middleware personalizado
│   │   ├── Requests/              # Form requests (validación)
│   │   └── Resources/             # API Resources
│   ├── Jobs/                      # Jobs para colas
│   ├── Listeners/                 # Event listeners
│   ├── Livewire/                  # Componentes Livewire
│   ├── Mail/                      # Clases de correo
│   ├── Models/                    # 63 modelos Eloquent
│   ├── Notifications/             # Notificaciones
│   ├── Observers/                 # Model observers
│   ├── Policies/                  # Authorization policies
│   ├── Providers/                 # Service providers
│   ├── Repositories/              # 45 repositorios (patrón Repository)
│   └── Services/                  # 50 servicios de negocio
├── config/                        # Archivos de configuración
├── database/
│   ├── migrations/                # 120+ migraciones (15 módulos)
│   ├── seeders/                   # Seeders de datos
│   └── factories/                 # Model factories
├── docs/                          # Documentación completa
│   ├── api/                       # Documentación API
│   ├── deployment/                # Guías de despliegue
│   ├── development/               # Guías de desarrollo
│   ├── fixes/                     # Historial de correcciones
│   └── guides/                    # Guías de usuario
├── docker/                        # Configuración Docker
├── public/                        # Archivos públicos
├── resources/
│   ├── css/                       # Estilos CSS
│   ├── js/                        # JavaScript (62 archivos)
│   ├── lang/                      # Traducciones (55 archivos)
│   └── views/                     # Vistas Blade (296 archivos)
├── routes/                        # Rutas modulares
│   ├── api.php                    # Rutas API
│   ├── web.php                    # Rutas web principales
│   └── [módulos]/                 # Rutas por módulo
├── storage/                       # Archivos de almacenamiento
├── tests/                         # Tests automatizados
│   ├── Feature/                   # Tests de características
│   └── Unit/                      # Tests unitarios
└── vendor/                        # Dependencias Composer
```

## 🤝 Contribución

### Proceso de Contribución

1. Fork el proyecto
2. Crea una rama feature (`git checkout -b feature/amazing-feature`)
3. Realiza tus cambios siguiendo los estándares del proyecto
4. Ejecuta los tests: `php artisan test`
5. Verifica calidad de código: `php artisan refactor:sonarqube --dry-run`
6. Commit tus cambios: `git commit -m 'feat: add amazing feature'`
7. Push a la rama: `git push origin feature/amazing-feature`
8. Abre un Pull Request

### Estándares de Código

- **PSR-12**: Formato de código estándar PHP
- **Convención de Commits**: Usar prefijos (feat, fix, docs, refactor, test)
- **Análisis de Código**: Ejecutar `php artisan refactor:sonarqube --dry-run` antes de commit
- **Formateo**: Usar `php artisan pint` para formatear código
- **Tests**: Escribir tests para nuevas funcionalidades (Feature y Unit)
- **Documentación**: Actualizar documentación en `docs/` para cambios importantes
- **Patrón Repository**: Usar repositorios para acceso a datos
- **Services**: Lógica de negocio en servicios, no en controladores
- **Policies**: Usar políticas para autorización
- **Form Requests**: Validación en Form Requests, no en controladores

### Estructura de Commits

```
feat: agregar nueva funcionalidad
fix: corregir bug
docs: actualizar documentación
refactor: refactorizar código
test: agregar o modificar tests
style: cambios de formato
chore: tareas de mantenimiento
```

## 📄 Licencia

Este proyecto es propiedad del SENA - CDATTG.

## 👥 Equipo

Desarrollado por ADSO - 2923560.

## 📞 Contacto

Para soporte o consultas, contacta a ADSO - 2923560.

---

**Nota**: Este sistema está en desarrollo activo. Para más información, consulta la [documentación completa](docs/).
