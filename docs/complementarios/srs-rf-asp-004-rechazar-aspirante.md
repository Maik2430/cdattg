# ESPECIFICACIÓN DE REQUERIMIENTOS DE SOFTWARE (SRS)
## RF-ASP-004: Rechazar Aspirante

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

Este documento especifica los requerimientos funcionales y no funcionales para el caso de uso **"Rechazar Aspirante"** (RF-ASP-004), que forma parte del módulo de Gestión de Aspirantes del sistema CDATTG Web.

### 1.2 Alcance

Este SRS cubre la funcionalidad que permite al administrador rechazar un aspirante inscrito en un programa complementario, cambiando su estado a "Rechazado" (4), incluyendo validaciones de permisos y registro en auditoría.

### 1.3 Definiciones, Acrónimos y Abreviaciones

- **SRS**: Software Requirements Specification
- **RF**: Requerimiento Funcional
- **RNF**: Requerimiento No Funcional
- **CDATTG**: Centro de Desarrollo Agroempresarial y Turístico del Guaviare
- **SENA**: Servicio Nacional de Aprendizaje

### 1.4 Referencias

- IEEE Std 830-1998, IEEE Recommended Practice for Software Requirements Specifications
- Documentación del módulo: `docs/gestion-aspirantes.md`
- Caso de Uso: CU-12

---

## 2. DESCRIPCIÓN GENERAL

### 2.1 Perspectiva del Requerimiento

Este requerimiento es parte del módulo de Gestión de Aspirantes y permite al administrador rechazar un aspirante que no cumple con los requisitos del programa, excluyéndolo de futuras exportaciones.

### 2.2 Funciones del Requerimiento

- Validar permisos del usuario
- Validar existencia del programa
- Validar existencia del aspirante
- Cambiar estado del aspirante a "Rechazado" (4)
- Registrar acción en log de auditoría
- Actualizar estadísticas de exclusión

### 2.3 Características del Usuario

**Actor Principal:** Administrador o Operador del sistema con permiso "ELIMINAR ASPIRANTE COMPLEMENTARIO"

### 2.4 Restricciones

- Requiere autenticación de usuario
- Requiere permiso específico "ELIMINAR ASPIRANTE COMPLEMENTARIO"
- El programa debe existir
- El aspirante debe existir en el programa
- La operación es irreversible (cambio de estado, no eliminación física)

### 2.5 Suposiciones y Dependencias

- El programa complementario existe en el sistema
- El aspirante existe en el programa
- El usuario tiene el permiso necesario
- El sistema tiene capacidad para registrar logs de auditoría

---

## 3. REQUERIMIENTOS FUNCIONALES

### 3.1 RF-ASP-004: Rechazar Aspirante

**Identificador:** RF-ASP-004  
**Título:** Rechazar Aspirante  
**Versión:** 1.0  
**Prioridad:** Media  
**Urgencia:** Media

#### 3.1.1 Descripción

El sistema debe permitir al administrador rechazar un aspirante inscrito en un programa complementario, cambiando su estado a "Rechazado" (4). Esta operación requiere permisos específicos y debe registrar la acción en log de auditoría. El aspirante rechazado queda excluido de futuras exportaciones a Excel.

#### 3.1.2 Objetivos Asociados

- Permitir al administrador rechazar un aspirante que no cumple con los requisitos del programa
- Mantener integridad de datos mediante validaciones de permisos
- Registrar la acción para auditoría y trazabilidad
- Excluir aspirantes rechazados de procesos de exportación

#### 3.1.3 Precondiciones

- El usuario debe haber iniciado sesión con rol de administrador o operador
- El usuario debe tener el permiso "ELIMINAR ASPIRANTE COMPLEMENTARIO"
- El programa complementario debe existir en el sistema
- El aspirante debe existir en el programa
- El aspirante debe estar visible en la lista de aspirantes del programa

#### 3.1.4 Secuencia Normal

1. El usuario accede a la gestión de aspirantes de un programa y visualiza la lista de aspirantes (RF-ASP-002)
2. El usuario selecciona un aspirante de la lista
3. El usuario hace clic en el botón "Rechazar" asociado al aspirante
4. El sistema muestra un diálogo de confirmación (SweetAlert2) preguntando: "¿Está seguro de que desea rechazar a [Nombre] [Apellido]?"
5. El usuario confirma la acción en el diálogo
6. El sistema valida que el usuario tenga el permiso "ELIMINAR ASPIRANTE COMPLEMENTARIO" usando `Auth::user()->can('ELIMINAR ASPIRANTE COMPLEMENTARIO')`
7. El sistema valida que el programa exista consultando `complementarios_ofertados`
8. El sistema valida que el aspirante exista en el programa consultando `aspirantes_complementarios` con filtros: `complementario_id` y `id = aspirante_id`
9. El sistema carga la relación con la persona para obtener datos (nombres, documento)
10. El sistema actualiza el estado del aspirante a 4 (Rechazado) en la tabla `aspirantes_complementarios`:
    - `estado`: 4
    - `updated_at`: Timestamp actual
11. El sistema registra la acción en log de auditoría con:
    - `aspirante_id`: ID del aspirante rechazado
    - `complementario_id`: ID del programa
    - `persona_id`: ID de la persona
    - `user_id`: ID del usuario que realizó la acción
    - `timestamp`: Fecha y hora de la acción
    - `accion`: "Aspirante rechazado"
12. El sistema retorna respuesta JSON con:
    - `success`: true
    - `message`: "Aspirante rechazado exitosamente. [Nombre] [Apellido] ([Documento]) ha sido marcado como rechazado en el programa."
    - `status_code`: 200

#### 3.1.5 Excepciones

**E-001:** Si el usuario no tiene permisos
- **Condición:** El usuario no tiene el permiso "ELIMINAR ASPIRANTE COMPLEMENTARIO"
- **Acción:** El sistema retornará respuesta JSON: `{success: false, message: "No tiene permisos para rechazar aspirantes.", status_code: 403}`
- **Código de Error:** 403 (Forbidden)
- **Log:** Se registrará el intento de rechazar sin permisos

**E-002:** Si el programa no existe
- **Condición:** La consulta no encuentra el programa con el `complementario_id` proporcionado
- **Acción:** El sistema retornará respuesta JSON: `{success: false, message: "Programa no encontrado.", status_code: 200}`
- **Código de Error:** 200 (pero con success: false)
- **Log:** Se registrará el intento de rechazar aspirante de programa inexistente

**E-003:** Si el aspirante no existe
- **Condición:** La consulta no encuentra el aspirante con el `aspirante_id` en el programa especificado
- **Acción:** El sistema retornará respuesta JSON: `{success: false, message: "Aspirante no encontrado.", status_code: 200}`
- **Código de Error:** 200 (pero con success: false)
- **Log:** Se registrará el intento de rechazar aspirante inexistente

**E-004:** Si hay error al actualizar
- **Condición:** Se produce una excepción al actualizar el registro en la base de datos
- **Acción:** El sistema retornará respuesta JSON: `{success: false, message: "Error interno del servidor. Por favor intente nuevamente.", status_code: 500}`
- **Código de Error:** 500 (Internal Server Error)
- **Log:** Se registrará el error con stack trace completo incluyendo: complementario_id, aspirante_id, user_id, exception

#### 3.1.6 Postcondiciones

- El estado del aspirante cambia a "Rechazado" (4) en la tabla `aspirantes_complementarios`
- El aspirante queda excluido de futuras exportaciones a Excel (filtro: estado != 4)
- Se registra la acción en el log de auditoría con: usuario, aspirante, programa y timestamp
- El sistema actualiza las estadísticas de exclusión del programa
- El usuario recibe confirmación de la operación exitosa mediante respuesta JSON
- La vista se actualiza para reflejar el nuevo estado del aspirante (opcional, si hay actualización en tiempo real)

#### 3.1.7 Requisitos Asociados

- **RF-ASP-002:** Ver Aspirantes de un Programa (requerimiento anterior, desde donde se accede y extiende)
- **RF-ASP-007:** Obtener Estadísticas de Exclusión (se actualiza después de rechazar)
- **RNF-ASP-001:** Autenticación Requerida
- **RNF-ASP-002:** Control de Acceso Basado en Permisos
- **RNF-ASP-005:** Auditoría y Logging
- **RNF-ASP-009:** Mensajes de Error Claros
- **RNF-ASP-010:** Respuestas JSON Consistentes

---

## 4. REQUERIMIENTOS NO FUNCIONALES

### 4.1 RNF-01: Acceso Restringido por Roles

**Prioridad:** Crítica  
**Categoría:** Seguridad

**Descripción:** La funcionalidad de rechazar aspirante debe requerir autenticación de usuario y acceso según roles. Además, requiere el permiso específico "ELIMINAR ASPIRANTE COMPLEMENTARIO".

**Criterios de Aceptación:**
- La ruta está protegida con middleware `auth`
- Solo usuarios con rol Administrador u Operador pueden acceder
- El sistema valida el permiso "ELIMINAR ASPIRANTE COMPLEMENTARIO" antes de ejecutar
- Usuarios sin permiso reciben error 403 (Forbidden)
- Los permisos se gestionan mediante Spatie Permission
- Intentos de acceso sin autenticación redirigen a página de login

### 4.2 RNF-02: Integridad y Protección de Datos

**Prioridad:** Alta  
**Categoría:** Seguridad

**Descripción:** El sistema debe garantizar la integridad y protección de datos mediante auditoría de todas las operaciones de rechazar aspirante.

**Criterios de Aceptación:**
- Se registra en log de auditoría: aspirante_id, complementario_id, persona_id, user_id, timestamp
- Los errores se registran con stack trace completo
- Los mensajes son descriptivos y en español, incluyendo información específica (nombre y documento del aspirante)

### 4.3 RNF-11: Uso de Estándares

**Prioridad:** Media  
**Categoría:** Mantenibilidad

**Descripción:** El sistema debe seguir estándares de desarrollo y retornar respuestas JSON consistentes.

**Criterios de Aceptación:**
- Todas las respuestas siguen el formato: `{success, message, status_code?}`
- Los códigos de estado HTTP son apropiados (200, 403, 500)
- El código sigue convenciones de Laravel y PSR-12

---

## 5. CRITERIOS DE ACEPTACIÓN

### 5.1 Criterios Funcionales

**CA-001:** El sistema debe validar permisos antes de rechazar
- **Verificación:** Se intenta rechazar sin el permiso "ELIMINAR ASPIRANTE COMPLEMENTARIO"
- **Resultado Esperado:** Respuesta JSON con `success: false`, mensaje de error y status_code: 403

**CA-002:** El sistema debe validar que el programa exista
- **Verificación:** Se prueba con ID de programa inexistente
- **Resultado Esperado:** Respuesta JSON con `success: false` y mensaje "Programa no encontrado."

**CA-003:** El sistema debe validar que el aspirante exista
- **Verificación:** Se prueba con ID de aspirante inexistente en el programa
- **Resultado Esperado:** Respuesta JSON con `success: false` y mensaje "Aspirante no encontrado."

**CA-004:** El sistema debe actualizar el estado correctamente
- **Verificación:** Se verifica en la base de datos que el estado cambió a 4
- **Resultado Esperado:** Registro actualizado con estado = 4 y updated_at actualizado

**CA-005:** El sistema debe registrar la acción en log
- **Verificación:** Se revisa el log del sistema después de rechazar un aspirante
- **Resultado Esperado:** Entrada en log con: aspirante_id, complementario_id, persona_id, user_id, timestamp

**CA-006:** El sistema debe retornar mensaje de éxito apropiado
- **Verificación:** Se verifica la respuesta JSON después de rechazar exitosamente
- **Resultado Esperado:** Respuesta con `success: true` y mensaje incluyendo nombre y documento del aspirante

**CA-007:** El aspirante rechazado debe quedar excluido de exportaciones
- **Verificación:** Se intenta exportar a Excel después de rechazar un aspirante
- **Resultado Esperado:** El aspirante rechazado no aparece en el archivo Excel generado

### 5.2 Criterios No Funcionales

**CA-008:** La funcionalidad debe estar disponible solo para usuarios autenticados
- **Verificación:** Se intenta rechazar aspirante sin autenticación
- **Resultado Esperado:** Redirección a página de login

**CA-009:** Los permisos deben validarse correctamente
- **Verificación:** Se prueba con usuario sin permiso y con usuario con permiso
- **Resultado Esperado:** Sin permiso: error 403. Con permiso: operación exitosa

**CA-010:** Los mensajes de error deben ser claros
- **Verificación:** Se revisan los mensajes de error en cada excepción
- **Resultado Esperado:** Mensajes descriptivos y específicos

### 5.3 Criterios de Validación

**CA-011:** El estado debe cambiar a "Rechazado" (4)
- **Verificación:** Se verifica el estado del registro después de rechazar
- **Resultado Esperado:** Estado = 4 (Rechazado)

**CA-012:** La operación debe ser registrada en auditoría
- **Verificación:** Se revisa el log después de la operación
- **Resultado Esperado:** Log con información completa de la acción

**CA-013:** El aspirante rechazado no debe aparecer en exportaciones
- **Verificación:** Se exporta a Excel después de rechazar
- **Resultado Esperado:** El archivo Excel no incluye aspirantes con estado = 4

---

## 6. TRAZABILIDAD

### 6.1 Requerimientos Relacionados

| Requerimiento | Relación | Descripción |
|---------------|----------|-------------|
| RF-ASP-002 | Extiende | Ver Aspirantes (se ejecuta desde esta vista) |
| RF-ASP-007 | Actualiza | Obtener Estadísticas (se actualiza después de rechazar) |
| RNF-01 | Depende | Acceso Restringido por Roles |
| RNF-02 | Depende | Integridad y Protección de Datos |
| RNF-11 | Depende | Uso de Estándares |

### 6.2 Casos de Uso Relacionados

- **CU-12:** Rechazar Aspirante

### 6.3 Componentes del Sistema

- **Controlador:** `AspiranteComplementarioController@eliminarAspirante()`
- **Servicio:** `AspiranteManagementService@rechazarAspirante()`
- **Repositorio:** `AspiranteComplementarioRepository@update()`
- **Repositorio:** `AspiranteComplementarioRepository@findByPrograma()`
- **Ruta:** `DELETE /programas-complementarios/{complementarioId}/aspirante/{aspiranteId}`
- **Permiso:** "ELIMINAR ASPIRANTE COMPLEMENTARIO"

### 6.4 Pruebas Relacionadas

- Test unitario del servicio `rechazarAspirante()`
- Test de validación de permisos (con y sin permiso)
- Test de validación de programa inexistente
- Test de validación de aspirante inexistente
- Test de actualización exitosa de estado
- Test de logging de auditoría
- Test de exclusión de exportaciones
- Test de integración de la ruta DELETE

---

## 7. GLOSARIO

| Término | Definición |
|--------|------------|
| **Rechazar Aspirante** | Cambiar el estado de un aspirante a "Rechazado" (4), excluyéndolo del proceso de formación |
| **Estado "Rechazado"** | Estado del aspirante (valor 4) que indica que ha sido rechazado del programa |
| **Permiso "ELIMINAR ASPIRANTE COMPLEMENTARIO"** | Permiso específico requerido para poder rechazar aspirantes |
| **Eliminación Suave** | Técnica que cambia el estado en lugar de eliminar físicamente el registro |

---

**FIN DEL DOCUMENTO**

