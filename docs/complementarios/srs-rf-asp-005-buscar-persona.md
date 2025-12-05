# ESPECIFICACIÓN DE REQUERIMIENTOS DE SOFTWARE (SRS)
## RF-ASP-005: Buscar Persona por Documento

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

Este documento especifica los requerimientos funcionales y no funcionales para el caso de uso **"Buscar Persona por Documento"** (RF-ASP-005), que forma parte del módulo de Gestión de Aspirantes del sistema CDATTG Web.

### 1.2 Alcance

Este SRS cubre la funcionalidad que permite al administrador buscar una persona existente en la base de datos mediante su número de documento, retornando todos sus datos incluyendo relaciones, para poder agregarla como aspirante.

### 1.3 Definiciones, Acrónimos y Abreviaciones

- **SRS**: Software Requirements Specification
- **RF**: Requerimiento Funcional
- **RNF**: Requerimiento No Funcional
- **CDATTG**: Centro de Desarrollo Agroempresarial y Turístico del Guaviare
- **SENA**: Servicio Nacional de Aprendizaje
- **AJAX**: Asynchronous JavaScript and XML

### 1.4 Referencias

- IEEE Std 830-1998, IEEE Recommended Practice for Software Requirements Specifications
- Documentación del módulo: `docs/gestion-aspirantes.md`
- Caso de Uso: CU-13

---

## 2. DESCRIPCIÓN GENERAL

### 2.1 Perspectiva del Requerimiento

Este requerimiento es parte del módulo de Gestión de Aspirantes y permite al administrador buscar una persona antes de agregarla como aspirante. La búsqueda se realiza en tiempo real mediante peticiones AJAX.

### 2.2 Funciones del Requerimiento

- Validar formato del número de documento
- Buscar persona en la base de datos
- Cargar relaciones de la persona (tipo documento, género, ubicación, caracterizaciones)
- Retornar datos en formato JSON
- Proporcionar opción de crear nueva persona si no existe

### 2.3 Características del Usuario

**Actor Principal:** Administrador o Operador del sistema

### 2.4 Restricciones

- Requiere autenticación de usuario
- El número de documento debe ser válido (string, máximo 191 caracteres)
- La búsqueda debe ser eficiente (tiempo de respuesta < 2 segundos)

### 2.5 Suposiciones y Dependencias

- La persona puede o no existir en el sistema
- La base de datos contiene información actualizada de personas
- Las relaciones de persona están correctamente establecidas

---

## 3. REQUERIMIENTOS FUNCIONALES

### 3.1 RF-ASP-005: Buscar Persona por Documento

**Identificador:** RF-ASP-005  
**Título:** Buscar Persona por Documento  
**Versión:** 1.0  
**Prioridad:** Alta  
**Urgencia:** Media

#### 3.1.1 Descripción

El sistema debe permitir al administrador buscar una persona existente en la base de datos mediante su número de documento. La búsqueda debe realizarse en tiempo real (AJAX) y retornar todos los datos de la persona incluyendo sus relaciones (tipo documento, género, ubicación, caracterizaciones).

#### 3.1.2 Objetivos Asociados

- Permitir al administrador buscar una persona en el sistema por su número de documento para poder agregarla como aspirante
- Proporcionar información completa de la persona para confirmar su identidad
- Facilitar el proceso de agregar aspirantes mediante búsqueda rápida

#### 3.1.3 Precondiciones

- El usuario debe haber iniciado sesión con rol de administrador o operador
- El número de documento debe ser válido (no vacío, formato correcto)

#### 3.1.4 Secuencia Normal

1. El usuario ingresa el número de documento en el campo de búsqueda del formulario
2. El sistema limpia el número de documento (elimina espacios al inicio y final usando `trim()`)
3. El sistema valida el número de documento:
   - Es requerido (no puede estar vacío)
   - Es string
   - Máximo 191 caracteres
4. El sistema realiza búsqueda en la base de datos en la tabla `personas` por el campo `numero_documento`
5. Si encuentra la persona, el sistema carga sus relaciones usando `loadMissing()`:
   - `tipoDocumento`: Relación con tabla de tipos de documento
   - `tipoGenero`: Relación con tabla de géneros
   - `pais`: Relación con tabla de países
   - `departamento`: Relación con tabla de departamentos
   - `municipio`: Relación con tabla de municipios
   - `caracterizacionesComplementarias`: Relación many-to-many con caracterizaciones
6. El sistema retorna respuesta JSON con:
   - `success`: true
   - `found`: true
   - `message`: null (o mensaje de éxito)
   - `persona`: objeto con todos los datos:
     - `id`: ID de la persona
     - `tipo_documento_id`: ID del tipo de documento
     - `tipo_documento`: Nombre del tipo de documento
     - `numero_documento`: Número de documento
     - `primer_nombre`, `segundo_nombre`, `primer_apellido`, `segundo_apellido`: Nombres completos
     - `fecha_nacimiento`: Fecha de nacimiento
     - `genero_id`: ID del género
     - `genero`: Nombre del género
     - `telefono`, `celular`, `email`: Información de contacto
     - `pais_id`, `pais`: Información de país
     - `departamento_id`, `departamento`: Información de departamento
     - `municipio_id`, `municipio`: Información de municipio
     - `direccion`: Dirección de residencia
     - `caracterizaciones`: Array de IDs de caracterizaciones
7. El sistema muestra los datos de la persona en el formulario para que el usuario confirme agregar como aspirante

#### 3.1.5 Excepciones

**E-001:** Si la persona no existe
- **Condición:** La búsqueda no encuentra ningún registro con el número de documento
- **Acción:** El sistema retornará respuesta JSON: `{success: false, found: false, message: "Persona no encontrada."}`
- **Código de Error:** 200 (pero con success: false)
- **Comportamiento Adicional:** El sistema puede ofrecer la opción de "Crear Nuevo Aspirante" (RF-ASP-006)

**E-002:** Si el número de documento está vacío
- **Condición:** El campo número de documento está vacío o solo contiene espacios
- **Acción:** El sistema mostrará mensaje de validación: "El número de documento es obligatorio."
- **Código de Error:** 422 (Unprocessable Entity)
- **Validación:** Se realiza mediante Form Request `BuscarPersonaRequest`

**E-003:** Si hay error en la búsqueda
- **Condición:** Se produce una excepción en la consulta a la base de datos
- **Acción:** El sistema retornará respuesta JSON: `{success: false, found: false, message: "Error al buscar la persona. Por favor intente nuevamente."}`
- **Código de Error:** 500 (Internal Server Error)
- **Log:** Se registrará el error con stack trace completo

#### 3.1.6 Postcondiciones

- Si la persona existe, se retornan todos sus datos en formato JSON incluyendo relaciones (tipo documento, género, ubicación, caracterizaciones)
- Los datos de la persona se muestran en el formulario para confirmar agregar como aspirante
- Si la persona no existe, se ofrece la opción de crear nuevo aspirante (RF-ASP-006)
- El sistema queda listo para continuar con el proceso de agregar aspirante (RF-ASP-003)
- La información retornada está actualizada al momento de la consulta

#### 3.1.7 Requisitos Asociados

- **RF-ASP-003:** Agregar Aspirante a Programa (requerimiento que incluye este caso de uso)
- **RF-ASP-006:** Crear Nuevo Aspirante (puede ejecutarse si la persona no existe)
- **RNF-ASP-001:** Autenticación Requerida
- **RNF-ASP-010:** Respuestas JSON Consistentes
- **RNF-ASP-011:** Búsqueda en Tiempo Real

---

## 4. REQUERIMIENTOS NO FUNCIONALES

### 4.1 RNF-01: Acceso Restringido por Roles

**Prioridad:** Crítica  
**Categoría:** Seguridad

**Descripción:** La funcionalidad de buscar persona debe requerir autenticación de usuario y acceso según roles (Administrador u Operador).

**Criterios de Aceptación:**
- La ruta está protegida con middleware `auth`
- Solo usuarios con rol Administrador u Operador pueden acceder
- Intentos de acceso sin autenticación redirigen a página de login

### 4.2 RNF-03: Procesamiento Asíncrono

**Prioridad:** Media  
**Categoría:** Rendimiento

**Descripción:** El sistema debe permitir búsqueda de personas en tiempo real mediante peticiones AJAX sin recargar la página, procesando de forma asíncrona.

**Criterios de Aceptación:**
- La búsqueda se realiza mediante petición AJAX
- La respuesta es inmediata (< 2 segundos)
- Se muestra feedback visual durante la búsqueda (loading indicator)
- No se bloquea la interfaz durante la búsqueda

### 4.3 RNF-04: Eficiencia Operacional

**Prioridad:** Media  
**Categoría:** Rendimiento

**Descripción:** El sistema debe procesar la búsqueda de forma eficiente, optimizando consultas y recursos.

**Criterios de Aceptación:**
- Las consultas utilizan índices apropiados en la base de datos
- Se evitan consultas redundantes
- El tiempo de respuesta no excede 2 segundos

### 4.4 RNF-11: Uso de Estándares

**Prioridad:** Media  
**Categoría:** Mantenibilidad

**Descripción:** El sistema debe seguir estándares de desarrollo y retornar respuestas JSON consistentes.

**Criterios de Aceptación:**
- Todas las respuestas siguen el formato: `{success, found, message, persona?}`
- Los códigos de estado HTTP son apropiados
- El código sigue convenciones de Laravel y PSR-12

---

## 5. CRITERIOS DE ACEPTACIÓN

### 5.1 Criterios Funcionales

**CA-001:** El sistema debe validar el formato del número de documento
- **Verificación:** Se envía número de documento vacío o inválido
- **Resultado Esperado:** Mensaje de validación apropiado

**CA-002:** El sistema debe buscar correctamente por número de documento
- **Verificación:** Se busca con número de documento existente
- **Resultado Esperado:** Se encuentra la persona y se retornan sus datos

**CA-003:** El sistema debe cargar todas las relaciones de la persona
- **Verificación:** Se verifica que se carguen: tipoDocumento, tipoGenero, pais, departamento, municipio, caracterizacionesComplementarias
- **Resultado Esperado:** Todas las relaciones están presentes en la respuesta JSON

**CA-004:** El sistema debe retornar datos completos de la persona
- **Verificación:** Se revisa la estructura de la respuesta JSON
- **Resultado Esperado:** Todos los campos requeridos están presentes: id, nombres, apellidos, documento, contacto, ubicación, caracterizaciones

**CA-005:** El sistema debe manejar correctamente cuando la persona no existe
- **Verificación:** Se busca con número de documento inexistente
- **Resultado Esperado:** Respuesta JSON con `found: false` y mensaje "Persona no encontrada."

**CA-006:** El sistema debe ofrecer opción de crear nueva persona si no existe
- **Verificación:** Se busca persona inexistente
- **Resultado Esperado:** Se muestra opción o botón para "Crear Nuevo Aspirante"

### 5.2 Criterios No Funcionales

**CA-007:** El tiempo de respuesta no debe exceder 2 segundos
- **Verificación:** Se mide el tiempo de respuesta de la búsqueda
- **Resultado Esperado:** Tiempo de respuesta < 2 segundos

**CA-008:** La búsqueda debe realizarse mediante AJAX
- **Verificación:** Se revisa que la petición sea AJAX y no recargue la página
- **Resultado Esperado:** Petición AJAX sin recarga de página

**CA-009:** Se debe mostrar feedback visual durante la búsqueda
- **Verificación:** Se verifica la presencia de indicador de carga
- **Resultado Esperado:** Indicador de carga visible durante la búsqueda

**CA-010:** La funcionalidad debe estar disponible solo para usuarios autenticados
- **Verificación:** Se intenta buscar sin autenticación
- **Resultado Esperado:** Redirección a página de login

### 5.3 Criterios de Validación

**CA-011:** El número de documento debe ser limpiado (trim)
- **Verificación:** Se envía número de documento con espacios al inicio/final
- **Resultado Esperado:** Los espacios se eliminan antes de buscar

**CA-012:** Los errores deben manejarse apropiadamente
- **Verificación:** Se simula error en la búsqueda
- **Resultado Esperado:** Mensaje de error claro y registro en log

---

## 6. TRAZABILIDAD

### 6.1 Requerimientos Relacionados

| Requerimiento | Relación | Descripción |
|---------------|----------|-------------|
| RF-ASP-003 | Incluye | Agregar Aspirante (siempre se ejecuta antes de agregar) |
| RF-ASP-006 | Siguiente | Crear Nuevo Aspirante (se ejecuta si persona no existe) |
| RNF-01 | Depende | Acceso Restringido por Roles |
| RNF-03 | Depende | Procesamiento Asíncrono |
| RNF-04 | Depende | Eficiencia Operacional |
| RNF-11 | Depende | Uso de Estándares |

### 6.2 Casos de Uso Relacionados

- **CU-13:** Buscar Persona por Documento

### 6.3 Componentes del Sistema

- **Controlador:** `AspiranteComplementarioController@buscarPersona()`
- **Servicio:** `PersonaService@buscarPorDocumento()`
- **Repositorio:** `PersonaRepository@findByNumeroDocumento()`
- **Form Request:** `BuscarPersonaRequest` (validación de entrada)
- **Ruta:** `POST /buscar-persona` (o ruta específica definida)

### 6.4 Pruebas Relacionadas

- Test unitario del servicio `buscarPorDocumento()`
- Test de búsqueda exitosa de persona existente
- Test de búsqueda de persona inexistente
- Test de validación de número de documento vacío
- Test de carga de relaciones
- Test de tiempo de respuesta (< 2 segundos)
- Test de integración de la ruta POST
- Test de respuesta JSON correcta

---

## 7. GLOSARIO

| Término | Definición |
|--------|------------|
| **Búsqueda AJAX** | Búsqueda asíncrona que no recarga la página completa |
| **Eager Loading** | Técnica de optimización que carga relaciones de forma anticipada |
| **loadMissing()** | Método de Laravel que carga relaciones solo si no están ya cargadas |
| **Caracterizaciones Complementarias** | Tipo de población del aspirante (víctima del conflicto, desplazado, etc.) |

---

**FIN DEL DOCUMENTO**

