# ESPECIFICACIÓN DE REQUERIMIENTOS DE SOFTWARE (SRS)
## RF-ASP-002: Ver Aspirantes de un Programa

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
| 1.0 | 2025-01-XX | Equipo de Desarrollo | Versión inicial del documento SRS |

---

## 1. INTRODUCCIÓN

### 1.1 Propósito

Este documento especifica los requerimientos funcionales y no funcionales para el caso de uso **"Ver Aspirantes de un Programa"** (RF-ASP-002), que forma parte del módulo de Gestión de Aspirantes del sistema CDATTG Web.

### 1.2 Alcance

Este SRS cubre la funcionalidad que permite al administrador consultar la lista completa de aspirantes inscritos en un programa complementario específico, incluyendo información detallada del programa y de cada aspirante.

### 1.3 Definiciones, Acrónimos y Abreviaciones

- **SRS**: Software Requirements Specification
- **RF**: Requerimiento Funcional
- **RNF**: Requerimiento No Funcional
- **CDATTG**: Centro de Desarrollo Agroempresarial y Turístico del Guaviare
- **SENA**: Servicio Nacional de Aprendizaje
- **SOFIA Plus**: Sistema de Gestión de Formación del SENA

### 1.4 Referencias

- IEEE Std 830-1998, IEEE Recommended Practice for Software Requirements Specifications
- Documentación del módulo: `docs/gestion-aspirantes.md`
- Caso de Uso: CU-10

---

## 2. DESCRIPCIÓN GENERAL

### 2.1 Perspectiva del Requerimiento

Este requerimiento es parte del módulo de Gestión de Aspirantes y permite al administrador visualizar todos los aspirantes inscritos en un programa específico, así como información detallada del programa y el estado de validación SOFIA.

### 2.2 Funciones del Requerimiento

- Consultar información completa de un programa complementario
- Listar todos los aspirantes inscritos en el programa
- Mostrar datos personales de cada aspirante
- Mostrar estado de validación SOFIA si existe uno en progreso
- Proporcionar acceso a acciones sobre los aspirantes

### 2.3 Características del Usuario

**Actor Principal:** Administrador o Operador del sistema

### 2.4 Restricciones

- Requiere autenticación de usuario
- El programa debe existir en el sistema
- La consulta debe ser eficiente incluso con grandes volúmenes de aspirantes

### 2.5 Suposiciones y Dependencias

- El programa complementario existe en el sistema
- Existen relaciones establecidas entre programas, aspirantes y personas
- La base de datos contiene información actualizada

---

## 3. REQUERIMIENTOS FUNCIONALES

### 3.1 RF-ASP-002: Ver Aspirantes de un Programa

**Identificador:** RF-ASP-002  
**Título:** Ver Aspirantes de un Programa  
**Versión:** 1.0  
**Prioridad:** Alta  
**Urgencia:** Alta

#### 3.1.1 Descripción

El sistema debe permitir al administrador visualizar todos los aspirantes inscritos en un programa complementario específico, mostrando información detallada de cada aspirante (nombres, documento, estado) y del programa (nombre, código, cupos, modalidad, jornada), así como el estado de validación SOFIA si existe uno en progreso.

#### 3.1.2 Objetivos Asociados

- Permitir al administrador consultar la lista completa de aspirantes inscritos en un programa complementario específico
- Proporcionar información detallada del programa y sus aspirantes
- Facilitar el acceso a acciones de gestión sobre los aspirantes

#### 3.1.3 Precondiciones

- El usuario debe haber iniciado sesión con rol de administrador o operador
- El programa complementario debe existir en el sistema
- El usuario debe tener acceso a la gestión de aspirantes

#### 3.1.4 Secuencia Normal

1. El usuario accede a la gestión de aspirantes y selecciona un programa específico mediante la ruta `/programas-complementarios/{curso}` o `/programas-complementarios/{programa}`
2. El sistema autentica al usuario mediante middleware `auth`
3. El sistema valida que el programa exista consultando la tabla `complementarios_ofertados`
4. El sistema consulta todos los aspirantes del programa desde la tabla `aspirantes_complementarios` con relación a `personas`
5. El sistema consulta información del programa incluyendo relaciones:
   - Modalidad (con parámetro)
   - Jornada
   - Días de formación
6. El sistema verifica si existe una validación SOFIA en progreso consultando la tabla `sofia_validation_progress`
7. El sistema muestra una vista con:
   - **Información del programa:**
     - Nombre del programa
     - Código del programa
     - Cupos disponibles
     - Modalidad de formación
     - Jornada de formación
     - Días y horarios de formación
   - **Tabla de aspirantes con:**
     - Número de documento
     - Nombres completos (primer nombre, segundo nombre, primer apellido, segundo apellido)
     - Estado del aspirante (En proceso, Admitido, Rechazado)
   - **Indicador de progreso de validación SOFIA** (si existe uno en curso)
   - **Botones de acción:**
     - Agregar Aspirante
     - Validar Documentos
     - Exportar a Excel
     - Descargar Cédulas
     - Validar en SOFIA Plus

#### 3.1.5 Excepciones

**E-001:** Si el programa no existe
- **Condición:** La consulta no encuentra el programa con el ID o nombre proporcionado
- **Acción:** El sistema retornará error 404 con mensaje: "Programa no encontrado"
- **Código de Error:** 404 (Not Found)
- **Log:** Se registrará el intento de acceso a programa inexistente

**E-002:** Si hay error al consultar los aspirantes
- **Condición:** Se produce una excepción en la consulta de aspirantes
- **Acción:** El sistema mostrará mensaje: "Error al cargar los aspirantes. Por favor intente nuevamente."
- **Código de Error:** 500 (Internal Server Error)
- **Log:** Se registrará el error con stack trace completo

**E-003:** Si el programa no tiene aspirantes
- **Condición:** La consulta retorna una colección vacía de aspirantes
- **Acción:** El sistema mostrará mensaje: "Este programa no tiene aspirantes inscritos"
- **Código de Error:** N/A (mensaje informativo)

#### 3.1.6 Postcondiciones

- El sistema muestra la vista completa del programa con información detallada (nombre, código, cupos, modalidad, jornada, días)
- Se muestra la tabla de aspirantes con sus datos personales y estados
- Se muestra el estado de validación SOFIA si existe uno en progreso
- El usuario puede realizar acciones sobre los aspirantes (agregar, rechazar, exportar, validar)
- La información mostrada está actualizada al momento de la consulta

#### 3.1.7 Requisitos Asociados

- **RF-ASP-001:** Listar Programas para Gestión (requerimiento anterior en el flujo)
- **RF-ASP-003:** Agregar Aspirante a Programa (acción disponible desde esta vista)
- **RF-ASP-004:** Rechazar Aspirante (acción disponible desde esta vista)
- **RF-ASP-007:** Obtener Estadísticas de Exclusión (puede ejecutarse desde esta vista)
- **RF-ASP-008:** Exportar Aspirantes a Excel (acción disponible desde esta vista)
- **RF-ASP-010:** Validar Documentos (acción disponible desde esta vista)
- **RNF-ASP-001:** Autenticación Requerida

---

## 4. REQUERIMIENTOS NO FUNCIONALES

### 4.1 RNF-01: Acceso Restringido por Roles

**Prioridad:** Crítica  
**Categoría:** Seguridad

**Descripción:** La funcionalidad de ver aspirantes debe requerir autenticación de usuario y acceso según roles (Administrador u Operador). El sistema no debe permitir acceso sin sesión activa ni sin los permisos adecuados.

**Criterios de Aceptación:**
- La ruta está protegida con middleware `auth`
- Solo usuarios con rol Administrador u Operador pueden acceder
- Intentos de acceso sin autenticación redirigen a página de login
- No se puede acceder a la información de aspirantes sin sesión válida

### 4.2 RNF-04: Eficiencia Operacional

**Prioridad:** Media  
**Categoría:** Rendimiento

**Descripción:** La consulta de aspirantes debe ser eficiente incluso con grandes volúmenes (100+ aspirantes), optimizando consultas y recursos.

**Criterios de Aceptación:**
- Se utiliza eager loading para evitar consultas N+1
- El tiempo de carga no excede 5 segundos para 100 aspirantes
- Las relaciones se cargan de forma optimizada
- Las consultas utilizan índices apropiados en la base de datos

---

## 5. CRITERIOS DE ACEPTACIÓN

### 5.1 Criterios Funcionales

**CA-001:** El sistema debe validar que el programa exista antes de mostrar aspirantes
- **Verificación:** Se prueba con ID de programa inexistente
- **Resultado Esperado:** Error 404 con mensaje "Programa no encontrado"

**CA-002:** El sistema debe mostrar información completa del programa
- **Verificación:** Se verifica que se muestren: nombre, código, cupos, modalidad, jornada, días
- **Resultado Esperado:** Todos los campos requeridos están presentes y correctos

**CA-003:** El sistema debe listar todos los aspirantes del programa
- **Verificación:** Se consulta la tabla `aspirantes_complementarios` filtrando por `complementario_id`
- **Resultado Esperado:** Se muestran todos los aspirantes sin omitir ninguno

**CA-004:** El sistema debe mostrar datos personales de cada aspirante
- **Verificación:** Se verifica que se muestren: número de documento, nombres completos, estado
- **Resultado Esperado:** Todos los datos personales están presentes y correctos

**CA-005:** El sistema debe mostrar estado de validación SOFIA si existe
- **Verificación:** Se consulta la tabla `sofia_validation_progress` para el programa
- **Resultado Esperado:** Si existe validación en progreso, se muestra el indicador

**CA-006:** El sistema debe proporcionar acceso a acciones sobre aspirantes
- **Verificación:** Se verifica la presencia de botones: Agregar, Validar Documentos, Exportar, Descargar Cédulas, Validar SOFIA
- **Resultado Esperado:** Todos los botones de acción están presentes y funcionales

### 5.2 Criterios No Funcionales

**CA-007:** El tiempo de carga no debe exceder 5 segundos
- **Verificación:** Se mide el tiempo de respuesta con 100 aspirantes
- **Resultado Esperado:** Tiempo de respuesta < 5 segundos

**CA-008:** La funcionalidad debe estar disponible solo para usuarios autenticados con roles apropiados
- **Verificación:** Se intenta acceder sin autenticación o con rol incorrecto
- **Resultado Esperado:** Redirección a página de login o error 403

**CA-009:** Las consultas deben usar eager loading para optimizar rendimiento (RNF-04)
- **Verificación:** Se revisa el código del servicio y repositorio
- **Resultado Esperado:** Se utilizan `with()` para cargar relaciones

### 5.3 Criterios de Validación

**CA-010:** Si el programa no existe, se debe mostrar error 404
- **Verificación:** Se prueba con ID de programa inexistente
- **Resultado Esperado:** Error 404 con mensaje apropiado

**CA-011:** Si no hay aspirantes, se debe mostrar mensaje apropiado
- **Verificación:** Se prueba con programa sin aspirantes
- **Resultado Esperado:** Mensaje "Este programa no tiene aspirantes inscritos" visible

**CA-012:** Los errores deben manejarse apropiadamente
- **Verificación:** Se simula error en la consulta
- **Resultado Esperado:** Mensaje de error claro y registro en log

---

## 6. TRAZABILIDAD

### 6.1 Requerimientos Relacionados

| Requerimiento | Relación | Descripción |
|---------------|----------|-------------|
| RF-ASP-001 | Anterior | Listar Programas para Gestión (se ejecuta antes de seleccionar un programa) |
| RF-ASP-003 | Siguiente | Agregar Aspirante (se ejecuta desde esta vista) |
| RF-ASP-004 | Extiende | Rechazar Aspirante (se ejecuta desde esta vista) |
| RF-ASP-007 | Extiende | Obtener Estadísticas (se ejecuta desde esta vista) |
| RF-ASP-008 | Extiende | Exportar a Excel (se ejecuta desde esta vista) |
| RF-ASP-010 | Siguiente | Validar Documentos (se ejecuta desde esta vista) |
| RNF-01 | Depende | Acceso Restringido por Roles |
| RNF-04 | Depende | Eficiencia Operacional |

### 6.2 Casos de Uso Relacionados

- **CU-10:** Ver Aspirantes de un Programa

### 6.3 Componentes del Sistema

- **Controlador:** `AspiranteComplementarioController@verAspirantes()` y `@programa()`
- **Servicio:** `AspiranteManagementService@obtenerAspirantesPorPrograma()` y `@obtenerAspirantesPorProgramaId()`
- **Repositorio:** `AspiranteComplementarioRepository@findByPrograma()`
- **Repositorio:** `ComplementarioOfertadoRepository@findWithRelations()`
- **Vista:** `resources/views/complementarios/aspirantes/programa.blade.php`
- **Rutas:** 
  - `GET /programas-complementarios/{curso}`
  - `GET /programas-complementarios/{programa}`

### 6.4 Pruebas Relacionadas

- Test unitario del servicio `obtenerAspirantesPorProgramaId()`
- Test de integración de las rutas de ver aspirantes
- Test de autenticación requerida
- Test de manejo de errores (programa no existe, sin aspirantes)
- Test de rendimiento con grandes volúmenes de aspirantes

---

## 7. GLOSARIO

| Término | Definición |
|--------|------------|
| **Aspirante** | Persona que se ha inscrito en un programa complementario y está en proceso de evaluación |
| **Programa Complementario** | Curso de formación complementaria ofertado por el SENA |
| **Estado del Aspirante** | Valor numérico que indica el estado: 1=En proceso, 3=Admitido, 4=Rechazado |
| **Validación SOFIA** | Proceso de validación de aspirantes en el sistema SOFIA Plus del SENA |
| **Eager Loading** | Técnica de optimización que carga relaciones de forma anticipada para evitar consultas N+1 |

---

**FIN DEL DOCUMENTO**

