# ESPECIFICACIÓN DE REQUERIMIENTOS DE SOFTWARE (SRS)
## RF-ASP-003: Agregar Aspirante a Programa

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

Este documento especifica los requerimientos funcionales y no funcionales para el caso de uso **"Agregar Aspirante a Programa"** (RF-ASP-003), que forma parte del módulo de Gestión de Aspirantes del sistema CDATTG Web.

### 1.2 Alcance

Este SRS cubre la funcionalidad que permite al administrador inscribir una persona existente en el sistema como aspirante a un programa complementario específico, incluyendo todas las validaciones necesarias y el registro en la base de datos.

### 1.3 Definiciones, Acrónimos y Abreviaciones

- **SRS**: Software Requirements Specification
- **RF**: Requerimiento Funcional
- **RNF**: Requerimiento No Funcional
- **CDATTG**: Centro de Desarrollo Agroempresarial y Turístico del Guaviare
- **SENA**: Servicio Nacional de Aprendizaje

### 1.4 Referencias

- IEEE Std 830-1998, IEEE Recommended Practice for Software Requirements Specifications
- Documentación del módulo: `docs/gestion-aspirantes.md`
- Caso de Uso: CU-11

---

## 2. DESCRIPCIÓN GENERAL

### 2.1 Perspectiva del Requerimiento

Este requerimiento es parte del módulo de Gestión de Aspirantes y permite al administrador agregar una persona existente como aspirante a un programa complementario, creando la relación necesaria en la base de datos.

### 2.2 Funciones del Requerimiento

- Buscar persona por número de documento
- Validar existencia del programa
- Validar existencia de la persona
- Validar que la persona no esté ya inscrita
- Crear registro de aspirante con estado "En proceso"
- Registrar acción en log de auditoría

### 2.3 Características del Usuario

**Actor Principal:** Administrador o Operador del sistema

### 2.4 Restricciones

- Requiere autenticación de usuario
- El programa debe existir
- La persona debe existir en el sistema
- Una persona no puede estar inscrita dos veces en el mismo programa (restricción única en BD)

### 2.5 Suposiciones y Dependencias

- El programa complementario existe en el sistema
- La persona existe en la tabla `personas`
- No existe ya una inscripción de la persona en el mismo programa
- El sistema tiene capacidad para registrar logs de auditoría

---

## 3. REQUERIMIENTOS FUNCIONALES

### 3.1 RF-ASP-003: Agregar Aspirante a Programa

**Identificador:** RF-ASP-003  
**Título:** Agregar Aspirante a Programa  
**Versión:** 1.0  
**Prioridad:** Alta  
**Urgencia:** Alta

#### 3.1.1 Descripción

El sistema debe permitir al administrador agregar una persona que ya existe en la base de datos como aspirante a un programa complementario específico. El sistema debe validar que la persona exista, que no esté ya inscrita en el mismo programa, y crear el registro con estado "En proceso".

#### 3.1.2 Objetivos Asociados

- Permitir al administrador inscribir una persona existente en el sistema como aspirante a un programa complementario
- Mantener integridad de datos mediante validaciones
- Registrar la acción para auditoría

#### 3.1.3 Precondiciones

- El usuario debe haber iniciado sesión con rol de administrador o operador
- El programa complementario debe existir en el sistema
- La persona debe existir en la tabla `personas`
- La persona no debe estar ya inscrita en el mismo programa

#### 3.1.4 Secuencia Normal

1. El usuario accede a la gestión de aspirantes de un programa
2. El usuario hace clic en "Agregar Aspirante"
3. El usuario ingresa el número de documento de la persona en el formulario
4. El sistema valida el formato del número de documento (requerido, string, máximo 191 caracteres)
5. El sistema busca la persona en la base de datos por número de documento (RF-ASP-005)
6. Si encuentra la persona, el sistema muestra sus datos:
   - Nombres y apellidos
   - Tipo de documento
   - Número de documento
   - Información de contacto
   - Caracterizaciones (si tiene)
7. El usuario confirma agregar la persona como aspirante
8. El sistema valida que el programa exista consultando `complementarios_ofertados`
9. El sistema valida que la persona exista (ya validado en paso 5)
10. El sistema valida que la persona no esté ya inscrita consultando `aspirantes_complementarios` con restricción única `(persona_id, complementario_id)`
11. El sistema crea el registro en la tabla `aspirantes_complementarios` con:
    - `persona_id`: ID de la persona encontrada
    - `complementario_id`: ID del programa
    - `estado`: 1 (En proceso)
    - `observaciones`: "Agregado manualmente desde gestión de aspirantes"
    - `created_at`: Timestamp actual
    - `updated_at`: Timestamp actual
12. El sistema registra la acción en log de auditoría con:
    - `user_id`: ID del usuario que realizó la acción
    - `complementario_id`: ID del programa
    - `persona_id`: ID de la persona agregada
    - `timestamp`: Fecha y hora de la acción
13. El sistema retorna respuesta JSON con:
    - `success`: true
    - `message`: "Aspirante agregado exitosamente. [Nombre] [Apellido] ha sido inscrito en el programa."

#### 3.1.5 Excepciones

**E-001:** Si el programa no existe
- **Condición:** La consulta no encuentra el programa con el `complementario_id` proporcionado
- **Acción:** El sistema retornará respuesta JSON: `{success: false, message: "Programa no encontrado."}`
- **Código de Error:** 200 (pero con success: false)
- **Log:** Se registrará el intento de agregar aspirante a programa inexistente

**E-002:** Si la persona no existe
- **Condición:** La búsqueda por número de documento no encuentra resultados
- **Acción:** El sistema retornará respuesta JSON: `{success: false, message: "No se encontró ninguna persona registrada con el número de documento '[numero]'."}`
- **Código de Error:** 200 (pero con success: false)
- **Log:** Se registrará el intento de agregar persona inexistente

**E-003:** Si la persona ya está inscrita
- **Condición:** Ya existe un registro en `aspirantes_complementarios` con la misma combinación `(persona_id, complementario_id)`
- **Acción:** El sistema retornará respuesta JSON: `{success: false, message: "La persona con documento '[numero]' ya se encuentra inscrita en este programa complementario."}`
- **Código de Error:** 200 (pero con success: false)
- **Log:** Se registrará el intento de duplicar inscripción

**E-004:** Si hay error al crear el registro
- **Condición:** Se produce una excepción al insertar en la base de datos
- **Acción:** El sistema retornará respuesta JSON: `{success: false, message: "Error interno del servidor. Por favor intente nuevamente."}`
- **Código de Error:** 500 (Internal Server Error)
- **Log:** Se registrará el error con stack trace completo incluyendo: complementario_id, numero_documento, exception

#### 3.1.6 Postcondiciones

- Se crea un nuevo registro en la tabla `aspirantes_complementarios` con estado "En proceso" (1)
- La persona queda asociada al programa complementario
- Se registra la acción en el log de auditoría con: usuario, programa, persona y timestamp
- El sistema actualiza el conteo de aspirantes del programa
- El usuario recibe confirmación de la operación exitosa mediante respuesta JSON
- La vista se actualiza para mostrar el nuevo aspirante en la lista

#### 3.1.7 Requisitos Asociados

- **RF-ASP-002:** Ver Aspirantes de un Programa (requerimiento anterior, desde donde se accede)
- **RF-ASP-005:** Buscar Persona por Documento (incluido en este caso de uso)
- **RF-ASP-006:** Crear Nuevo Aspirante (puede extenderse si la persona no existe)
- **RF-ASP-010:** Validar Documentos (puede ejecutarse después de agregar)
- **RNF-ASP-001:** Autenticación Requerida
- **RNF-ASP-003:** Validación de Datos de Entrada
- **RNF-ASP-005:** Auditoría y Logging
- **RNF-ASP-009:** Mensajes de Error Claros
- **RNF-ASP-010:** Respuestas JSON Consistentes

---

## 4. REQUERIMIENTOS NO FUNCIONALES

### 4.1 RNF-01: Acceso Restringido por Roles

**Prioridad:** Crítica  
**Categoría:** Seguridad

**Descripción:** La funcionalidad de agregar aspirante debe requerir autenticación de usuario y acceso según roles (Administrador u Operador).

**Criterios de Aceptación:**
- La ruta está protegida con middleware `auth`
- Solo usuarios con rol Administrador u Operador pueden acceder
- Intentos de acceso sin autenticación redirigen a página de login

### 4.2 RNF-02: Integridad y Protección de Datos

**Prioridad:** Alta  
**Categoría:** Seguridad

**Descripción:** El sistema debe garantizar la integridad y protección de datos mediante validaciones exhaustivas, sanitización y auditoría de todas las operaciones.

**Criterios de Aceptación:**
- Se utiliza Form Request `AspiranteRequest` para validación
- El número de documento es requerido, string, máximo 191 caracteres
- Los datos se sanitizan antes de almacenar
- Se registra en log de auditoría: user_id, complementario_id, persona_id, timestamp
- Los errores se registran con stack trace completo

### 4.3 RNF-04: Eficiencia Operacional

**Prioridad:** Media  
**Categoría:** Rendimiento

**Descripción:** El sistema debe procesar la operación de agregar aspirante de forma eficiente, optimizando consultas y recursos.

**Criterios de Aceptación:**
- Las consultas utilizan índices apropiados
- Se evitan consultas redundantes
- El tiempo de procesamiento no excede 3 segundos

### 4.4 RNF-11: Uso de Estándares

**Prioridad:** Media  
**Categoría:** Mantenibilidad

**Descripción:** El sistema debe seguir estándares de desarrollo (PSR-12, Laravel conventions) y retornar respuestas JSON consistentes.

**Criterios de Aceptación:**
- Todas las respuestas siguen el formato: `{success, message, data?}`
- Los códigos de estado HTTP son apropiados
- El código sigue convenciones de Laravel y PSR-12

---

## 5. CRITERIOS DE ACEPTACIÓN

### 5.1 Criterios Funcionales

**CA-001:** El sistema debe validar que el programa exista antes de agregar aspirante
- **Verificación:** Se prueba con ID de programa inexistente
- **Resultado Esperado:** Respuesta JSON con `success: false` y mensaje "Programa no encontrado."

**CA-002:** El sistema debe validar que la persona exista
- **Verificación:** Se prueba con número de documento inexistente
- **Resultado Esperado:** Respuesta JSON con `success: false` y mensaje indicando que la persona no existe

**CA-003:** El sistema debe validar que la persona no esté ya inscrita
- **Verificación:** Se intenta agregar una persona que ya está inscrita en el mismo programa
- **Resultado Esperado:** Respuesta JSON con `success: false` y mensaje indicando que ya está inscrita

**CA-004:** El sistema debe crear el registro correctamente
- **Verificación:** Se verifica en la base de datos que se creó el registro con los datos correctos
- **Resultado Esperado:** Registro creado con: persona_id, complementario_id, estado=1, observaciones correctas

**CA-005:** El sistema debe registrar la acción en log
- **Verificación:** Se revisa el log del sistema después de agregar un aspirante
- **Resultado Esperado:** Entrada en log con: user_id, complementario_id, persona_id, timestamp

**CA-006:** El sistema debe retornar mensaje de éxito apropiado
- **Verificación:** Se verifica la respuesta JSON después de agregar exitosamente
- **Resultado Esperado:** Respuesta con `success: true` y mensaje incluyendo nombre del aspirante

### 5.2 Criterios No Funcionales

**CA-007:** La funcionalidad debe estar disponible solo para usuarios autenticados
- **Verificación:** Se intenta agregar aspirante sin autenticación
- **Resultado Esperado:** Redirección a página de login

**CA-008:** Los datos de entrada deben ser validados
- **Verificación:** Se envía número de documento vacío o inválido
- **Resultado Esperado:** Mensaje de error de validación apropiado

**CA-009:** Los mensajes de error deben ser claros
- **Verificación:** Se revisan los mensajes de error en cada excepción
- **Resultado Esperado:** Mensajes descriptivos y específicos

### 5.3 Criterios de Validación

**CA-010:** No se debe permitir duplicados
- **Verificación:** Se intenta agregar la misma persona dos veces al mismo programa
- **Resultado Esperado:** Error indicando que ya está inscrita (restricción única de BD)

**CA-011:** El estado inicial debe ser "En proceso"
- **Verificación:** Se verifica el estado del registro creado
- **Resultado Esperado:** Estado = 1 (En proceso)

**CA-012:** Las observaciones deben ser correctas
- **Verificación:** Se verifica el campo observaciones del registro creado
- **Resultado Esperado:** Observaciones = "Agregado manualmente desde gestión de aspirantes"

---

## 6. TRAZABILIDAD

### 6.1 Requerimientos Relacionados

| Requerimiento | Relación | Descripción |
|---------------|----------|-------------|
| RF-ASP-002 | Anterior | Ver Aspirantes (desde donde se accede a agregar) |
| RF-ASP-005 | Incluye | Buscar Persona por Documento (siempre se ejecuta) |
| RF-ASP-006 | Extiende | Crear Nuevo Aspirante (si persona no existe) |
| RF-ASP-010 | Siguiente | Validar Documentos (puede ejecutarse después) |
| RNF-01 | Depende | Acceso Restringido por Roles |
| RNF-02 | Depende | Integridad y Protección de Datos |
| RNF-04 | Depende | Eficiencia Operacional |
| RNF-11 | Depende | Uso de Estándares |

### 6.2 Casos de Uso Relacionados

- **CU-11:** Agregar Aspirante a Programa

### 6.3 Componentes del Sistema

- **Controlador:** `AspiranteComplementarioController@agregarAspirante()`
- **Servicio:** `AspiranteManagementService@agregarAspirante()`
- **Repositorio:** `AspiranteComplementarioRepository@create()`
- **Repositorio:** `AspiranteComplementarioRepository@existeInscripcion()`
- **Repositorio:** `PersonaRepository@findByNumeroDocumento()`
- **Form Request:** `AspiranteRequest` (validación de entrada)
- **Ruta:** `POST /programas-complementarios/{complementarioId}/agregar-aspirante`

### 6.4 Pruebas Relacionadas

- Test unitario del servicio `agregarAspirante()`
- Test de validación de programa inexistente
- Test de validación de persona inexistente
- Test de validación de duplicados
- Test de creación exitosa de registro
- Test de logging de auditoría
- Test de integración de la ruta POST

---

## 7. GLOSARIO

| Término | Definición |
|--------|------------|
| **Aspirante** | Persona que se ha inscrito en un programa complementario y está en proceso de evaluación |
| **Estado "En proceso"** | Estado inicial del aspirante (valor 1) que indica que está en proceso de validación |
| **Restricción Única** | Constraint de base de datos que previene duplicados en la combinación (persona_id, complementario_id) |
| **Form Request** | Clase de Laravel que encapsula validación de datos de entrada HTTP |

---

**FIN DEL DOCUMENTO**

