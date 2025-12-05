# ESPECIFICACIÓN DE REQUERIMIENTOS DE SOFTWARE (SRS)
## Requerimientos No Funcionales (RNF)

**Versión:** 1.0  
**Fecha:** 2025-01-XX  
**Autor:** Equipo de Desarrollo  
**Cliente:** SENA - Centro de Desarrollo Agroempresarial y Turístico del Guaviare  
**Estándar:** IEEE 830-1998  
**Estado:** Aprobado

---

## CONTROL DE VERSIONES

| Versión | Fecha | Autor | Descripción |
|---------|-------|-------|-------------|
| 1.0 | 2025-01-XX | Equipo de Desarrollo | Versión inicial del documento de Requerimientos No Funcionales |

---

## 1. INTRODUCCIÓN

### 1.1 Propósito

Este documento especifica todos los requerimientos no funcionales (RNF) del sistema CDATTG Web, módulo de Gestión de Aspirantes para Programas Complementarios. Los requerimientos no funcionales definen las características del sistema relacionadas con seguridad, rendimiento, compatibilidad, escalabilidad y mantenibilidad.

### 1.2 Alcance

Este documento cubre los requerimientos no funcionales aplicables a todo el sistema, con énfasis en el módulo de Gestión de Aspirantes. Estos requerimientos son transversales y deben cumplirse en todas las funcionalidades del sistema.

### 1.3 Definiciones, Acrónimos y Abreviaciones

- **SRS**: Software Requirements Specification
- **RNF**: Requerimiento No Funcional
- **RF**: Requerimiento Funcional
- **CDATTG**: Centro de Desarrollo Agroempresarial y Turístico del Guaviare
- **SENA**: Servicio Nacional de Aprendizaje
- **AJAX**: Asynchronous JavaScript and XML
- **PSR-12**: PHP Standards Recommendation 12 (estándar de codificación PHP)

### 1.4 Referencias

- IEEE Std 830-1998, IEEE Recommended Practice for Software Requirements Specifications
- Documentación del módulo: `docs/gestion-aspirantes.md`
- Documentos SRS de casos de uso: `docs/srs-rf-asp-*.md`
- Laravel Framework Documentation
- PSR-12 Coding Standard

---

## 2. DESCRIPCIÓN GENERAL

### 2.1 Perspectiva de los Requerimientos No Funcionales

Los requerimientos no funcionales definen las características de calidad del sistema que no están directamente relacionadas con las funcionalidades específicas, sino con aspectos como seguridad, rendimiento, usabilidad, compatibilidad, escalabilidad y mantenibilidad.

### 2.2 Categorías de Requerimientos No Funcionales

Los requerimientos no funcionales se organizan en las siguientes categorías:

1. **Seguridad:** RNF-01, RNF-02
2. **Rendimiento:** RNF-03, RNF-04
3. **Compatibilidad:** RNF-05, RNF-06
4. **Escalabilidad:** RNF-07, RNF-08, RNF-09
5. **Mantenibilidad:** RNF-10, RNF-11, RNF-12

### 2.3 Aplicabilidad

Todos los requerimientos no funcionales son aplicables a todo el sistema, aunque algunos tienen mayor relevancia en módulos específicos. Cada documento SRS de caso de uso referencia los RNF que le aplican.

---

## 3. REQUERIMIENTOS NO FUNCIONALES

### 3.1 RNF-01: Acceso Restringido por Roles

**Identificador:** RNF-01  
**Categoría:** Seguridad  
**Prioridad:** Crítica  
**Versión:** 1.0

#### 3.1.1 Descripción

El sistema debe restringir el acceso a funcionalidades según los roles del usuario (Administrador, Operador, Aspirante). Cada rol tiene permisos específicos y el sistema debe validar estos permisos antes de permitir el acceso a cualquier funcionalidad.

#### 3.1.2 Objetivos

- Garantizar que solo usuarios autorizados accedan a funcionalidades según su rol
- Prevenir acceso no autorizado a información sensible
- Mantener trazabilidad de quién accede a qué funcionalidades
- Cumplir con políticas de seguridad del SENA

#### 3.1.3 Criterios de Aceptación

**CA-RNF-01-001:** El sistema debe requerir autenticación para todas las funcionalidades
- Todas las rutas están protegidas con middleware `auth` de Laravel
- Intentos de acceso sin autenticación redirigen automáticamente a página de login
- No se puede acceder a ninguna funcionalidad sin sesión válida

**CA-RNF-01-002:** El sistema debe validar roles antes de permitir acceso
- Se utiliza Spatie Permission para gestión de roles y permisos
- Cada funcionalidad valida el rol requerido antes de ejecutarse
- Usuarios sin el rol apropiado reciben error 403 (Forbidden)

**CA-RNF-01-003:** El sistema debe validar permisos específicos cuando aplique
- Funcionalidades críticas requieren permisos específicos (ej: "ELIMINAR ASPIRANTE COMPLEMENTARIO")
- Los permisos se validan mediante `Auth::user()->can('PERMISO')`
- Se registra en log los intentos de acceso sin permisos

**CA-RNF-01-004:** Los roles deben estar claramente definidos
- **Administrador:** Acceso completo a todas las funcionalidades
- **Operador:** Acceso a funcionalidades operativas (gestión de aspirantes, validaciones)
- **Aspirante:** Acceso limitado a funcionalidades de autoservicio (inscripción, subir documentos)

#### 3.1.4 Trazabilidad

**Casos de Uso Aplicables:**
- RF-ASP-001: Listar Programas para Gestión
- RF-ASP-002: Ver Aspirantes de un Programa
- RF-ASP-003: Agregar Aspirante a Programa
- RF-ASP-004: Rechazar Aspirante
- RF-ASP-005: Buscar Persona por Documento
- RF-ASP-006: Crear Nuevo Aspirante
- RF-ASP-007: Obtener Estadísticas de Exclusión
- RF-ASP-008: Exportar Aspirantes a Excel

**Componentes del Sistema:**
- Middleware: `app/Http/Middleware/Authenticate.php`
- Middleware: `app/Http/Middleware/Authorize.php`
- Librería: Spatie Permission (`spatie/laravel-permission`)
- Controladores: Todos los controladores del módulo Complementarios

---

### 3.2 RNF-02: Integridad y Protección de Datos

**Identificador:** RNF-02  
**Categoría:** Seguridad  
**Prioridad:** Crítica  
**Versión:** 1.0

#### 3.2.1 Descripción

El sistema debe garantizar la integridad y protección de datos mediante validaciones exhaustivas, sanitización de entrada, encriptación de datos sensibles, y auditoría de todas las operaciones críticas. Los datos deben protegerse contra pérdida, corrupción y acceso no autorizado.

#### 3.2.2 Objetivos

- Prevenir corrupción de datos mediante validaciones
- Proteger información sensible mediante encriptación
- Mantener trazabilidad de cambios mediante auditoría
- Cumplir con normativas de protección de datos personales

#### 3.2.3 Criterios de Aceptación

**CA-RNF-02-001:** El sistema debe validar todos los datos de entrada
- Se utilizan Form Requests de Laravel para validación
- Todos los campos obligatorios son validados
- Formatos de datos son validados (email, fechas, números, etc.)
- Longitudes máximas y mínimas son aplicadas
- Unicidad de datos críticos es validada (ej: número de documento, email)

**CA-RNF-02-002:** El sistema debe sanitizar datos antes de almacenar
- Se eliminan espacios al inicio y final (`trim()`)
- Se escapan caracteres especiales para prevenir inyección SQL
- Se sanitizan datos HTML para prevenir XSS
- Se validan tipos de datos antes de almacenar

**CA-RNF-02-003:** El sistema debe encriptar datos sensibles
- Las contraseñas se almacenan usando `Hash::make()` (bcrypt)
- Los tokens y credenciales se encriptan
- Los datos sensibles no se exponen en logs ni respuestas

**CA-RNF-02-004:** El sistema debe registrar auditoría de operaciones críticas
- Se registra en log: user_id, acción, timestamp, datos relevantes
- Las operaciones CRUD críticas se registran (crear, actualizar, eliminar)
- Los errores se registran con stack trace completo
- Los logs se almacenan de forma segura y con rotación

**CA-RNF-02-005:** El sistema debe proteger contra inyección SQL
- Se utiliza Eloquent ORM o Query Builder (nunca SQL crudo)
- Los parámetros se pasan como bindings
- No se concatenan valores de usuario en consultas SQL

**CA-RNF-02-006:** El sistema debe proteger contra XSS
- Todos los datos de usuario se escapan en vistas Blade usando `{{ }}`
- No se usa `{!! !!}` a menos que sea absolutamente necesario y esté sanitizado
- Los datos JSON se validan antes de procesar

#### 3.2.4 Trazabilidad

**Casos de Uso Aplicables:**
- RF-ASP-003: Agregar Aspirante a Programa
- RF-ASP-004: Rechazar Aspirante
- RF-ASP-006: Crear Nuevo Aspirante
- Todos los casos de uso que manejan datos de entrada

**Componentes del Sistema:**
- Form Requests: `app/Http/Requests/Complementarios/*`
- Validación: Laravel Validation Rules
- Encriptación: `Illuminate\Support\Facades\Hash`
- Logging: `Illuminate\Support\Facades\Log`
- Base de datos: Eloquent ORM

---

### 3.3 RNF-03: Procesamiento Asíncrono

**Identificador:** RNF-03  
**Categoría:** Rendimiento  
**Prioridad:** Media  
**Versión:** 1.0

#### 3.3.1 Descripción

Las operaciones que requieren tiempo de procesamiento deben ejecutarse de forma asíncrona para no bloquear la interfaz de usuario. Esto incluye búsquedas, validaciones, generación de reportes y operaciones que interactúan con sistemas externos.

#### 3.3.2 Objetivos

- Mejorar la experiencia del usuario evitando bloqueos de interfaz
- Permitir que el usuario continúe trabajando mientras se procesan operaciones
- Optimizar el uso de recursos del servidor
- Proporcionar feedback visual del progreso de operaciones

#### 3.3.3 Criterios de Aceptación

**CA-RNF-03-001:** Las búsquedas deben realizarse mediante AJAX
- Las búsquedas se realizan mediante peticiones AJAX sin recargar la página
- Se muestra indicador de carga durante la búsqueda
- La respuesta se procesa y muestra dinámicamente en la interfaz

**CA-RNF-03-002:** Las operaciones largas deben procesarse en background
- Operaciones que exceden 5 segundos se procesan mediante Jobs de Laravel
- Se notifica al usuario cuando la operación completa
- El usuario puede continuar trabajando mientras se procesa

**CA-RNF-03-003:** Las validaciones externas deben ser asíncronas
- Validaciones con SOFIA Plus se procesan de forma asíncrona
- Se muestra progreso de validación en tiempo real
- El usuario recibe notificación cuando la validación completa

**CA-RNF-03-004:** Se debe proporcionar feedback visual
- Indicadores de carga (spinners, progress bars) durante operaciones
- Mensajes de estado claros ("Buscando...", "Procesando...")
- Notificaciones cuando las operaciones completan

#### 3.3.4 Trazabilidad

**Casos de Uso Aplicables:**
- RF-ASP-005: Buscar Persona por Documento
- RF-ASP-010: Validar Documentos
- RF-ASP-011: Validar SOFIA Plus
- RF-ASP-008: Exportar Aspirantes a Excel (para grandes volúmenes)

**Componentes del Sistema:**
- Jobs: `app/Jobs/Complementarios/*`
- Queue: Laravel Queue System
- AJAX: JavaScript/jQuery para peticiones asíncronas
- WebSockets: Para actualizaciones en tiempo real (si aplica)

---

### 3.4 RNF-04: Eficiencia Operacional

**Identificador:** RNF-04  
**Categoría:** Rendimiento  
**Prioridad:** Alta  
**Versión:** 1.0

#### 3.4.1 Descripción

El sistema debe procesar operaciones de forma eficiente, optimizando consultas a la base de datos, uso de memoria, tiempo de procesamiento y recursos del servidor. Las operaciones deben completarse en tiempos razonables incluso con grandes volúmenes de datos.

#### 3.4.2 Objetivos

- Minimizar tiempos de respuesta de las operaciones
- Optimizar uso de recursos del servidor
- Escalar eficientemente con el crecimiento de datos
- Proporcionar experiencia de usuario fluida

#### 3.4.3 Criterios de Aceptación

**CA-RNF-04-001:** Las consultas a la base de datos deben ser optimizadas
- Se utiliza eager loading (`with()`) para evitar consultas N+1
- Se utilizan índices apropiados en tablas de base de datos
- Se evitan consultas redundantes y se cachean resultados cuando aplica
- Las consultas complejas se optimizan usando `select()` para cargar solo campos necesarios

**CA-RNF-04-002:** Los tiempos de respuesta deben cumplir límites definidos
- Consultas simples: < 1 segundo
- Consultas con relaciones: < 3 segundos
- Generación de reportes: < 30 segundos para 1000 registros
- Exportación de archivos: < 30 segundos para 1000 registros

**CA-RNF-04-003:** El sistema debe usar memoria eficientemente
- Se utiliza paginación para listados grandes (mínimo 20 registros por página)
- Se usa streaming para archivos grandes (StreamedResponse)
- Se liberan recursos después de su uso
- Se evitan cargas innecesarias de datos en memoria

**CA-RNF-04-004:** Las operaciones deben escalar con el volumen de datos
- El rendimiento no se degrada significativamente con el crecimiento de datos
- Se utilizan técnicas de optimización (índices, cache, paginación)
- Las consultas se optimizan para grandes volúmenes

**CA-RNF-04-005:** Se debe implementar cache cuando sea apropiado
- Datos que no cambian frecuentemente se cachean (configuraciones, catálogos)
- El cache se invalida cuando los datos se actualizan
- Se utiliza cache de Laravel (Redis, Memcached, o file cache)

#### 3.4.4 Trazabilidad

**Casos de Uso Aplicables:**
- RF-ASP-001: Listar Programas para Gestión
- RF-ASP-002: Ver Aspirantes de un Programa
- RF-ASP-003: Agregar Aspirante a Programa
- RF-ASP-007: Obtener Estadísticas de Exclusión
- RF-ASP-008: Exportar Aspirantes a Excel

**Componentes del Sistema:**
- Repositorios: `app/Repositories/Complementarios/*`
- Servicios: `app/Services/Complementarios/*`
- Cache: `Illuminate\Support\Facades\Cache`
- Base de datos: Optimización de consultas Eloquent

---

### 3.5 RNF-05: Soporte de Navegadores

**Identificador:** RNF-05  
**Categoría:** Compatibilidad  
**Prioridad:** Media  
**Versión:** 1.0

#### 3.5.1 Descripción

El sistema debe ser compatible con los navegadores web modernos más utilizados, asegurando que todas las funcionalidades operen correctamente en cada uno de ellos.

#### 3.5.2 Objetivos

- Garantizar acceso desde diferentes navegadores
- Proporcionar experiencia consistente independientemente del navegador
- Asegurar compatibilidad con estándares web

#### 3.5.3 Criterios de Aceptación

**CA-RNF-05-001:** El sistema debe soportar navegadores modernos
- **Google Chrome:** Últimas 2 versiones
- **Mozilla Firefox:** Últimas 2 versiones
- **Microsoft Edge:** Últimas 2 versiones
- **Safari:** Últimas 2 versiones (macOS e iOS)

**CA-RNF-05-002:** Las funcionalidades deben operar correctamente en todos los navegadores soportados
- Todas las funcionalidades se prueban en cada navegador soportado
- No hay diferencias significativas en comportamiento entre navegadores
- Los estilos se renderizan correctamente en todos los navegadores

**CA-RNF-05-003:** Se debe proporcionar mensaje apropiado para navegadores no soportados
- Se detecta el navegador del usuario
- Se muestra mensaje informativo si el navegador no es soportado
- Se recomienda actualizar o usar un navegador soportado

#### 3.5.4 Trazabilidad

**Casos de Uso Aplicables:**
- Todos los casos de uso con interfaz web

**Componentes del Sistema:**
- Frontend: AdminLTE, Bootstrap, JavaScript
- Detección: User Agent detection (opcional)
- Testing: Pruebas en múltiples navegadores

---

### 3.6 RNF-06: Diseño Responsivo

**Identificador:** RNF-06  
**Categoría:** Compatibilidad  
**Prioridad:** Media  
**Versión:** 1.0

#### 3.6.1 Descripción

El sistema debe adaptarse a diferentes tamaños de pantalla (desktop, tablet, móvil) proporcionando una experiencia de usuario óptima en cada dispositivo. El diseño debe ser responsivo y funcional en todos los tamaños de pantalla.

#### 3.6.2 Objetivos

- Permitir acceso desde diferentes dispositivos
- Proporcionar experiencia de usuario óptima en cada dispositivo
- Asegurar usabilidad en pantallas pequeñas

#### 3.6.3 Criterios de Aceptación

**CA-RNF-06-001:** El sistema debe adaptarse a diferentes tamaños de pantalla
- **Desktop:** > 1200px - Layout completo con sidebar
- **Tablet:** 768px - 1199px - Layout adaptado, sidebar colapsable
- **Móvil:** < 768px - Layout vertical, menú hamburguesa

**CA-RNF-06-002:** Los elementos de interfaz deben ser usables en todos los tamaños
- Los botones tienen tamaño mínimo de 44x44px en móvil
- Los formularios se adaptan al ancho disponible
- Las tablas son scrollables horizontalmente en móvil o se convierten en cards
- Los textos son legibles sin zoom

**CA-RNF-06-003:** La navegación debe ser funcional en todos los dispositivos
- El menú se adapta al tamaño de pantalla
- Los enlaces y botones son fácilmente accesibles
- No se requiere scroll horizontal innecesario

**CA-RNF-06-004:** Se debe usar framework responsivo
- Se utiliza Bootstrap 4/5 para grid system
- Se utilizan breakpoints estándar
- Los componentes de AdminLTE son responsivos

#### 3.6.4 Trazabilidad

**Casos de Uso Aplicables:**
- Todos los casos de uso con interfaz web

**Componentes del Sistema:**
- CSS Framework: Bootstrap 4/5
- AdminLTE: Responsive components
- Media Queries: CSS responsive design

---

### 3.7 RNF-07: Diseño Modular

**Identificador:** RNF-07  
**Categoría:** Escalabilidad  
**Prioridad:** Alta  
**Versión:** 1.0

#### 3.7.1 Descripción

El sistema debe estar diseñado de forma modular para facilitar mantenimiento, expansión y reutilización de código. Cada módulo debe ser independiente y comunicarse mediante interfaces bien definidas.

#### 3.7.2 Objetivos

- Facilitar mantenimiento del código
- Permitir expansión sin afectar módulos existentes
- Promover reutilización de código
- Facilitar testing y debugging

#### 3.7.3 Criterios de Aceptación

**CA-RNF-07-001:** El código debe estar organizado en módulos
- Cada módulo tiene su propia estructura (Controllers, Services, Repositories, Models)
- Los módulos están en directorios separados: `app/Http/Controllers/[Modulo]/`
- Los módulos son independientes y tienen dependencias mínimas entre sí

**CA-RNF-07-002:** Los módulos deben comunicarse mediante interfaces bien definidas
- Se utilizan Services para lógica de negocio compartida
- Se utilizan Repositories para abstracción de datos
- Las dependencias se inyectan mediante Dependency Injection
- No hay acoplamiento directo entre módulos

**CA-RNF-07-003:** El código debe seguir principios SOLID
- Single Responsibility: Cada clase tiene una responsabilidad
- Open/Closed: Abierto para extensión, cerrado para modificación
- Liskov Substitution: Las clases derivadas son sustituibles
- Interface Segregation: Interfaces específicas, no genéricas
- Dependency Inversion: Depender de abstracciones, no de concreciones

**CA-RNF-07-004:** Se debe promover reutilización
- Componentes Blade reutilizables en `resources/views/components/`
- Helpers y funciones utilitarias en `app/Helpers/`
- Traits para funcionalidad compartida
- Services reutilizables

#### 3.7.4 Trazabilidad

**Casos de Uso Aplicables:**
- Todos los casos de uso del sistema

**Componentes del Sistema:**
- Estructura de directorios: `app/Http/Controllers/[Modulo]/`
- Services: `app/Services/[Modulo]/`
- Repositories: `app/Repositories/[Modulo]/`
- Components: `resources/views/components/`

---

### 3.8 RNF-08: Capacidad de Expansión de Documentos

**Identificador:** RNF-08  
**Categoría:** Escalabilidad  
**Prioridad:** Media  
**Versión:** 1.0

#### 3.8.1 Descripción

El sistema debe soportar el crecimiento en volumen de documentos almacenados sin degradación significativa del rendimiento. Los documentos se almacenan en Google Drive y el sistema debe gestionar eficientemente grandes volúmenes.

#### 3.8.2 Objetivos

- Soportar crecimiento en número de documentos
- Mantener rendimiento con grandes volúmenes
- Optimizar búsqueda y recuperación de documentos

#### 3.8.3 Criterios de Aceptación

**CA-RNF-08-001:** El sistema debe soportar grandes volúmenes de documentos
- Soporta al menos 10,000 documentos por programa
- El rendimiento no se degrada significativamente con el crecimiento
- Se utilizan técnicas de paginación y cache

**CA-RNF-08-002:** La búsqueda de documentos debe ser eficiente
- Se utilizan patrones de búsqueda optimizados en Google Drive API
- Se cachean resultados de búsqueda frecuentes
- El tiempo de búsqueda no excede 5 segundos

**CA-RNF-08-003:** El almacenamiento debe ser escalable
- Se utiliza Google Drive para almacenamiento (escalable automáticamente)
- No hay límites de almacenamiento impuestos por el sistema
- Los documentos se organizan en carpetas estructuradas

#### 3.8.4 Trazabilidad

**Casos de Uso Aplicables:**
- RF-ASP-010: Validar Documentos
- RF-ASP-009: Descargar Cédulas

**Componentes del Sistema:**
- Servicio: `app/Services/Complementarios/AspiranteDocumentoService.php`
- Google Drive API: Integración con Google Drive
- Cache: Para resultados de búsqueda

---

### 3.9 RNF-09: Crecimiento de Usuarios y Documentos

**Identificador:** RNF-09  
**Categoría:** Escalabilidad  
**Prioridad:** Media  
**Versión:** 1.0

#### 3.9.1 Descripción

El sistema debe soportar el crecimiento en número de usuarios concurrentes y documentos sin degradación significativa del rendimiento. El sistema debe escalar horizontal o verticalmente según sea necesario.

#### 3.9.2 Objetivos

- Soportar crecimiento en usuarios concurrentes
- Mantener rendimiento con aumento de carga
- Escalar eficientemente según demanda

#### 3.9.3 Criterios de Aceptación

**CA-RNF-09-001:** El sistema debe soportar múltiples usuarios concurrentes
- Soporta al menos 50 usuarios concurrentes sin degradación
- El tiempo de respuesta se mantiene dentro de límites aceptables
- No hay bloqueos por concurrencia

**CA-RNF-09-002:** El sistema debe manejar crecimiento de datos
- Soporta crecimiento de 10x en número de registros sin cambios arquitectónicos
- Las consultas se optimizan para grandes volúmenes
- Se utilizan técnicas de paginación y limitación de resultados

**CA-RNF-09-003:** El sistema debe ser escalable
- Arquitectura permite escalamiento horizontal (múltiples servidores)
- Base de datos puede escalarse independientemente
- Se pueden agregar recursos sin cambios en código

#### 3.9.4 Trazabilidad

**Casos de Uso Aplicables:**
- Todos los casos de uso del sistema

**Componentes del Sistema:**
- Arquitectura: Laravel (permite escalamiento)
- Base de datos: MySQL (escalable)
- Servidor: Configuración para múltiples workers/processes

---

### 3.10 RNF-10: Estructura y Documentación del Código

**Identificador:** RNF-10  
**Categoría:** Mantenibilidad  
**Prioridad:** Alta  
**Versión:** 1.0

#### 3.10.1 Descripción

El código debe estar bien estructurado y documentado siguiendo estándares profesionales. La documentación debe ser clara, completa y actualizada, facilitando el mantenimiento y la incorporación de nuevos desarrolladores.

#### 3.10.2 Objetivos

- Facilitar comprensión del código
- Reducir tiempo de incorporación de nuevos desarrolladores
- Facilitar mantenimiento y debugging
- Promover buenas prácticas

#### 3.10.3 Criterios de Aceptación

**CA-RNF-10-001:** El código debe estar bien estructurado
- Sigue estructura estándar de Laravel (MVC)
- Separación clara de responsabilidades (Controllers, Services, Repositories)
- Nombres descriptivos y autoexplicativos
- Funciones y métodos pequeños y enfocados

**CA-RNF-10-002:** El código debe tener documentación adecuada
- PHPDoc en todas las clases y métodos públicos
- Comentarios explicativos donde sea necesario (lógica compleja)
- README.md actualizado con instrucciones de instalación y uso
- Documentación de API si aplica

**CA-RNF-10-003:** La documentación debe estar actualizada
- Se actualiza cuando se modifica código
- Los cambios se documentan en commits
- README.md refleja el estado actual del proyecto

**CA-RNF-10-004:** Se debe seguir convenciones de nombres
- Clases: PascalCase
- Métodos y funciones: camelCase
- Variables: camelCase
- Constantes: UPPER_SNAKE_CASE
- Archivos: kebab-case para vistas, PascalCase para clases

#### 3.10.4 Trazabilidad

**Casos de Uso Aplicables:**
- Todos los casos de uso del sistema

**Componentes del Sistema:**
- Código fuente: Todo el código en `app/`
- Documentación: `docs/`, `README.md`
- PHPDoc: En todas las clases

---

### 3.11 RNF-11: Uso de Estándares

**Identificador:** RNF-11  
**Categoría:** Mantenibilidad  
**Prioridad:** Alta  
**Versión:** 1.0

#### 3.11.1 Descripción

El sistema debe seguir estándares de desarrollo establecidos (PSR-12 para PHP, convenciones de Laravel, estándares de API REST) para garantizar consistencia, calidad y mantenibilidad del código.

#### 3.11.2 Objetivos

- Garantizar consistencia en el código
- Facilitar mantenimiento y colaboración
- Cumplir con estándares de la industria
- Mejorar calidad del código

#### 3.11.3 Criterios de Aceptación

**CA-RNF-11-001:** El código PHP debe seguir PSR-12
- Indentación de 4 espacios (no tabs)
- Líneas no exceden 120 caracteres
- Declaración de tipos estricta: `declare(strict_types=1);`
- Nombres de espacios (namespaces) siguen PSR-4
- Se utiliza `phpcs` o `php-cs-fixer` para validación

**CA-RNF-11-002:** El código debe seguir convenciones de Laravel
- Estructura de directorios estándar de Laravel
- Nombres de modelos en singular, tablas en plural
- Uso de Resource Controllers
- Uso de Form Requests para validación
- Uso de Eloquent ORM

**CA-RNF-11-003:** Las APIs deben seguir estándares REST
- Uso de verbos HTTP apropiados (GET, POST, PUT, DELETE)
- Códigos de estado HTTP correctos
- Respuestas JSON consistentes
- Versionado de API si aplica

**CA-RNF-11-004:** Se deben usar herramientas de validación de estándares
- `phpcs` o `php-cs-fixer` para validación de código
- `phpstan` o `larastan` para análisis estático
- SonarQube para análisis de calidad
- CI/CD para validación automática

#### 3.11.4 Trazabilidad

**Casos de Uso Aplicables:**
- Todos los casos de uso del sistema

**Componentes del Sistema:**
- Código fuente: Todo el código en `app/`
- Herramientas: `phpcs`, `phpstan`, SonarQube
- CI/CD: Pipeline de validación

---

### 3.12 RNF-12: Pruebas Automatizadas

**Identificador:** RNF-12  
**Categoría:** Mantenibilidad  
**Prioridad:** Alta  
**Versión:** 1.0

#### 3.12.1 Descripción

El sistema debe tener pruebas automatizadas (unitarias, de integración, de funcionalidad) para garantizar calidad, detectar regresiones y facilitar refactorización. Las pruebas deben tener buena cobertura y ejecutarse automáticamente.

#### 3.12.2 Objetivos

- Garantizar calidad del código
- Detectar regresiones tempranamente
- Facilitar refactorización segura
- Documentar comportamiento esperado

#### 3.12.3 Criterios de Aceptación

**CA-RNF-12-001:** Debe haber pruebas unitarias para lógica de negocio
- Services tienen pruebas unitarias
- Helpers y funciones utilitarias tienen pruebas
- Cobertura mínima del 70% en lógica de negocio
- Las pruebas son rápidas (< 1 segundo por prueba)

**CA-RNF-12-002:** Debe haber pruebas de integración para funcionalidades
- Los controladores tienen pruebas de integración
- Las rutas se prueban con diferentes escenarios
- Se prueban flujos completos de funcionalidades
- Se utilizan factories para datos de prueba

**CA-RNF-12-003:** Las pruebas deben ejecutarse automáticamente
- Se ejecutan en CI/CD pipeline
- Se ejecutan antes de cada commit (pre-commit hook opcional)
- Los resultados se reportan claramente
- Las pruebas fallidas bloquean el despliegue

**CA-RNF-12-004:** Las pruebas deben seguir buenas prácticas
- Una prueba por comportamiento específico
- Nombres descriptivos que explican qué se prueba
- Arrange-Act-Assert pattern
- Uso de mocks para dependencias externas
- Datos de prueba aislados (RefreshDatabase)

**CA-RNF-12-005:** Debe haber pruebas para casos edge
- Se prueban casos límite (valores mínimos, máximos, vacíos)
- Se prueban casos de error
- Se prueban validaciones
- Se prueban permisos y autorizaciones

#### 3.12.4 Trazabilidad

**Casos de Uso Aplicables:**
- Todos los casos de uso del sistema

**Componentes del Sistema:**
- Tests: `tests/Feature/Complementarios/`
- Tests: `tests/Unit/Complementarios/`
- Framework: PHPUnit
- CI/CD: GitHub Actions, GitLab CI, o similar

---

## 4. RESUMEN DE APLICABILIDAD

### 4.1 Matriz de Aplicabilidad por Caso de Uso

| RNF | RF-ASP-001 | RF-ASP-002 | RF-ASP-003 | RF-ASP-004 | RF-ASP-005 | RF-ASP-006 | RF-ASP-007 | RF-ASP-008 |
|-----|------------|------------|------------|------------|------------|------------|------------|------------|
| RNF-01 | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| RNF-02 | - | - | ✓ | ✓ | - | ✓ | - | - |
| RNF-03 | - | - | - | - | ✓ | - | - | - |
| RNF-04 | ✓ | ✓ | ✓ | - | ✓ | - | ✓ | ✓ |
| RNF-05 | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| RNF-06 | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| RNF-07 | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| RNF-08 | - | - | - | - | - | - | - | - |
| RNF-09 | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| RNF-10 | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| RNF-11 | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| RNF-12 | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |

**Leyenda:**
- ✓: Aplicable
- -: No aplicable directamente

### 4.2 Priorización de RNF

**Críticos (Deben cumplirse siempre):**
- RNF-01: Acceso Restringido por Roles
- RNF-02: Integridad y Protección de Datos

**Altos (Muy importantes):**
- RNF-04: Eficiencia Operacional
- RNF-07: Diseño Modular
- RNF-10: Estructura y Documentación del Código
- RNF-11: Uso de Estándares
- RNF-12: Pruebas Automatizadas

**Medios (Importantes pero flexibles):**
- RNF-03: Procesamiento Asíncrono
- RNF-05: Soporte de Navegadores
- RNF-06: Diseño Responsivo
- RNF-08: Capacidad de Expansión de Documentos
- RNF-09: Crecimiento de Usuarios y Documentos

---

## 5. GLOSARIO

| Término | Definición |
|--------|------------|
| **Eager Loading** | Técnica de optimización que carga relaciones de forma anticipada para evitar consultas N+1 |
| **N+1 Query Problem** | Problema de rendimiento donde se ejecutan múltiples consultas innecesarias |
| **Form Request** | Clase de Laravel que encapsula validación de datos de entrada HTTP |
| **Middleware** | Capa de software que intercepta y procesa peticiones HTTP antes de llegar al controlador |
| **Dependency Injection** | Patrón de diseño donde las dependencias se inyectan en lugar de crearse internamente |
| **PSR-12** | Estándar de codificación PHP que define estilo y formato de código |
| **SOLID** | Principios de diseño orientado a objetos: Single Responsibility, Open/Closed, Liskov Substitution, Interface Segregation, Dependency Inversion |
| **CI/CD** | Continuous Integration / Continuous Deployment - Integración y despliegue continuo |
| **XSS** | Cross-Site Scripting - Vulnerabilidad de seguridad donde se inyecta código malicioso |
| **SQL Injection** | Vulnerabilidad de seguridad donde se inyecta código SQL malicioso |

---

**FIN DEL DOCUMENTO**

