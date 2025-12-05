# ESPECIFICACIÓN DE REQUERIMIENTOS DE SOFTWARE (SRS)
## RF-ASP-007: Obtener Estadísticas de Exclusión

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

Este documento especifica los requerimientos funcionales y no funcionales para el caso de uso **"Obtener Estadísticas de Exclusión"** (RF-ASP-007), que forma parte del módulo de Gestión de Aspirantes del sistema CDATTG Web.

### 1.2 Alcance

Este SRS cubre la funcionalidad que permite al administrador consultar estadísticas sobre aspirantes de un programa que están excluidos de la exportación, categorizándolos por motivo de exclusión.

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
- Caso de Uso: CU-15

---

## 2. DESCRIPCIÓN GENERAL

### 2.1 Perspectiva del Requerimiento

Este requerimiento extiende "Ver Aspirantes" y proporciona información estadística sobre por qué algunos aspirantes no pueden ser exportados a SOFIA Plus.

### 2.2 Funciones del Requerimiento

- Calcular total de aspirantes del programa
- Contar aspirantes rechazados (estado = 4)
- Contar aspirantes sin documento (condocumento = 0)
- Contar aspirantes no registrados en SOFIA (estado_sofia = 0)
- Calcular aspirantes válidos para exportación
- Retornar estadísticas en formato JSON

### 2.3 Características del Usuario

**Actor Principal:** Administrador o Operador del sistema

### 2.4 Restricciones

- Requiere autenticación de usuario
- El programa complementario debe existir
- Los cálculos deben ser eficientes

---

## 3. REQUERIMIENTOS FUNCIONALES

### 3.1 RF-ASP-007: Obtener Estadísticas de Exclusión

**Identificador:** RF-ASP-007  
**Título:** Obtener Estadísticas de Exclusión  
**Versión:** 1.0  
**Prioridad:** Baja  
**Urgencia:** Baja

#### 3.1.1 Descripción

El sistema debe calcular y mostrar estadísticas sobre aspirantes de un programa que están excluidos de la exportación, categorizándolos por motivo de exclusión: rechazados, sin documento, no registrados en SOFIA, y aspirantes válidos para exportación.

#### 3.1.2 Objetivos Asociados

- Proporcionar al administrador información estadística sobre aspirantes excluidos de la exportación para entender por qué algunos aspirantes no se pueden exportar a SOFIA Plus
- Facilitar la toma de decisiones sobre gestión de aspirantes

#### 3.1.3 Precondiciones

- El usuario debe haber iniciado sesión con rol de administrador o operador
- El programa complementario debe existir en el sistema

#### 3.1.4 Secuencia Normal

1. El usuario accede a la gestión de aspirantes de un programa (RF-ASP-002)
2. El usuario solicita ver estadísticas de exclusión (puede ser desde un botón o modal)
3. El sistema valida que el programa exista
4. El sistema calcula las siguientes estadísticas:
   - **Total de aspirantes:** COUNT(*) de `aspirantes_complementarios` WHERE `complementario_id` = X
   - **Aspirantes rechazados:** COUNT(*) WHERE `estado` = 4
   - **Aspirantes sin documento:** COUNT(*) WHERE `condocumento` = 0 (o persona no tiene documento)
   - **Aspirantes no registrados en SOFIA:** COUNT(*) WHERE `estado_sofia` = 0 (o no existe registro en SOFIA)
   - **Aspirantes válidos para exportación:** COUNT(*) WHERE `estado` != 4 AND `condocumento` = 1 AND `estado_sofia` != 0
5. El sistema retorna las estadísticas en formato JSON:
   ```json
   {
     "success": true,
     "estadisticas": {
       "total": 100,
       "rechazados": 5,
       "sin_documento": 3,
       "no_registrados_sofia": 10,
       "validos_exportacion": 82
     }
   }
   ```
6. El sistema muestra las estadísticas en un modal o sección de la vista

#### 3.1.5 Excepciones

**E-001:** Si el programa no existe
- **Condición:** La consulta no encuentra el programa
- **Acción:** El sistema retornará: `{success: false, message: "Programa no encontrado."}`
- **Código de Error:** 200 (pero con success: false)

**E-002:** Si hay error al calcular estadísticas
- **Condición:** Se produce excepción en las consultas
- **Acción:** El sistema retornará: `{success: false, message: "Error al obtener estadísticas de exclusión: [mensaje de error]"}`
- **Código de Error:** 500 (Internal Server Error)
- **Log:** Se registrará el error con stack trace

#### 3.1.6 Postcondiciones

- Se calculan y retornan las estadísticas de exclusión en formato JSON con los siguientes valores: total de aspirantes, rechazados, sin documento, no registrados en SOFIA, y válidos para exportación
- Las estadísticas se muestran al usuario en un modal o sección de la vista
- El usuario puede usar esta información para tomar decisiones sobre la gestión de aspirantes

#### 3.1.7 Requisitos Asociados

- **RF-ASP-002:** Ver Aspirantes de un Programa (requerimiento que extiende)
- **RNF-ASP-010:** Respuestas JSON Consistentes

---

## 4. REQUERIMIENTOS NO FUNCIONALES

### 4.1 RNF-01: Acceso Restringido por Roles

**Prioridad:** Crítica  
**Categoría:** Seguridad

**Descripción:** La funcionalidad de obtener estadísticas debe requerir autenticación de usuario y acceso según roles (Administrador u Operador).

**Criterios de Aceptación:**
- La ruta está protegida con middleware `auth`
- Solo usuarios con rol Administrador u Operador pueden acceder
- Intentos de acceso sin autenticación redirigen a página de login

### 4.2 RNF-04: Eficiencia Operacional

**Prioridad:** Media  
**Categoría:** Rendimiento

**Descripción:** El sistema debe calcular las estadísticas de forma eficiente, optimizando consultas y recursos.

**Criterios de Aceptación:**
- Las consultas utilizan agregaciones eficientes (COUNT, GROUP BY)
- El tiempo de cálculo no excede 3 segundos para 1000 aspirantes
- Se evitan consultas redundantes

### 4.3 RNF-11: Uso de Estándares

**Prioridad:** Media  
**Categoría:** Mantenibilidad

**Descripción:** El sistema debe seguir estándares de desarrollo y retornar respuestas JSON consistentes.

**Criterios de Aceptación:**
- Todas las respuestas siguen el formato: `{success, estadisticas?, message?}`
- Los códigos de estado HTTP son apropiados
- El código sigue convenciones de Laravel y PSR-12

---

## 5. CRITERIOS DE ACEPTACIÓN

### 5.1 Criterios Funcionales

**CA-001:** El sistema debe calcular total de aspirantes correctamente
- **Verificación:** Se compara con conteo manual en BD
- **Resultado Esperado:** El total coincide con el número real

**CA-002:** El sistema debe categorizar aspirantes por motivo de exclusión
- **Verificación:** Se verifica cada categoría (rechazados, sin documento, no registrados SOFIA)
- **Resultado Esperado:** Cada categoría tiene el conteo correcto

**CA-003:** El sistema debe calcular aspirantes válidos para exportación
- **Verificación:** Se verifica la fórmula: total - rechazados - sin_documento - no_registrados_sofia
- **Resultado Esperado:** El número de válidos es correcto

**CA-004:** El sistema debe retornar estadísticas en formato JSON
- **Verificación:** Se revisa la estructura de la respuesta
- **Resultado Esperado:** JSON válido con estructura esperada

### 5.2 Criterios No Funcionales

**CA-005:** El tiempo de cálculo no debe exceder 3 segundos
- **Verificación:** Se mide el tiempo de respuesta
- **Resultado Esperado:** Tiempo < 3 segundos para 1000 aspirantes

**CA-006:** La funcionalidad debe estar disponible solo para usuarios autenticados
- **Verificación:** Se intenta acceder sin autenticación
- **Resultado Esperado:** Redirección a página de login

---

## 6. TRAZABILIDAD

### 6.1 Requerimientos Relacionados

| Requerimiento | Relación | Descripción |
|---------------|----------|-------------|
| RF-ASP-002 | Extiende | Ver Aspirantes (se ejecuta desde esta vista) |
| RNF-01 | Depende | Acceso Restringido por Roles |
| RNF-04 | Depende | Eficiencia Operacional |
| RNF-11 | Depende | Uso de Estándares |

### 6.2 Casos de Uso Relacionados

- **CU-15:** Obtener Estadísticas de Exclusión

### 6.3 Componentes del Sistema

- **Controlador:** `AspiranteComplementarioController@getEstadisticasExclusion()`
- **Servicio:** `AspiranteManagementService@obtenerEstadisticasPrograma()`
- **Repositorio:** `AspiranteComplementarioRepository@getEstadisticasExclusion()`
- **Ruta:** `GET /programas-complementarios/{id}/estadisticas-exclusion`

---

## 7. GLOSARIO

| Término | Definición |
|--------|------------|
| **Aspirante Rechazado** | Aspirante con estado = 4, excluido del proceso |
| **Aspirante sin Documento** | Aspirante que no tiene documento de identidad registrado |
| **Aspirante no Registrado en SOFIA** | Aspirante que no ha sido validado en el sistema SOFIA Plus |
| **Aspirante Válido para Exportación** | Aspirante que cumple todas las condiciones para ser exportado a SOFIA Plus |

---

**FIN DEL DOCUMENTO**

