# ESPECIFICACIÓN DE REQUERIMIENTOS DE SOFTWARE (SRS)
## RF-ASP-008: Exportar Aspirantes a Excel (SOFIA Plus)

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

Este documento especifica los requerimientos funcionales y no funcionales para el caso de uso **"Exportar Aspirantes a Excel (SOFIA Plus)"** (RF-ASP-008), que forma parte del módulo de Gestión de Aspirantes del sistema CDATTG Web.

### 1.2 Alcance

Este SRS cubre la funcionalidad que permite al administrador exportar aspirantes válidos a un archivo Excel en formato compatible con SOFIA Plus v1.0 para su importación en el sistema oficial del SENA.

### 1.3 Definiciones, Acrónimos y Abreviaciones

- **SRS**: Software Requirements Specification
- **RF**: Requerimiento Funcional
- **RNF**: Requerimiento No Funcional
- **CDATTG**: Centro de Desarrollo Agroempresarial y Turístico del Guaviare
- **SENA**: Servicio Nacional de Aprendizaje
- **SOFIA Plus**: Sistema de Gestión de Formación del SENA v1.0
- **PHPSpreadsheet**: Librería PHP para manipulación de archivos Excel

### 1.4 Referencias

- IEEE Std 830-1998, IEEE Recommended Practice for Software Requirements Specifications
- Documentación del módulo: `docs/gestion-aspirantes.md`
- Caso de Uso: CU-16
- Formato SOFIA Plus v1.0

---

## 2. DESCRIPCIÓN GENERAL

### 2.1 Perspectiva del Requerimiento

Este requerimiento extiende "Ver Aspirantes" y permite exportar aspirantes válidos en formato Excel compatible con SOFIA Plus para su importación masiva.

### 2.2 Funciones del Requerimiento

- Filtrar aspirantes válidos para exportación
- Generar archivo Excel con formato SOFIA Plus v1.0
- Convertir tipos de documento a iniciales
- Incluir caracterizaciones de población
- Aplicar estilos y formato al archivo
- Descargar archivo al navegador

### 2.3 Características del Usuario

**Actor Principal:** Administrador o Operador del sistema

### 2.4 Restricciones

- Requiere autenticación de usuario
- El programa complementario debe existir
- Solo se exportan aspirantes válidos (no rechazados, con documento, registrados en SOFIA)
- El formato debe ser exactamente compatible con SOFIA Plus v1.0

---

## 3. REQUERIMIENTOS FUNCIONALES

### 3.1 RF-ASP-008: Exportar Aspirantes a Excel (SOFIA Plus)

**Identificador:** RF-ASP-008  
**Título:** Exportar Aspirantes a Excel (SOFIA Plus)  
**Versión:** 1.0  
**Prioridad:** Alta  
**Urgencia:** Alta

#### 3.1.1 Descripción

El sistema debe generar un archivo Excel con los datos de aspirantes válidos (no rechazados, con documento, registrados en SOFIA) en el formato específico requerido por SOFIA Plus v1.0. El archivo debe incluir: tipo de identificación (en iniciales), número de identificación, código de ficha, y tipo de población aspirante (caracterización).

#### 3.1.2 Objetivos Asociados

- Permitir al administrador exportar aspirantes válidos a un archivo Excel en formato compatible con SOFIA Plus v1.0 para su importación en el sistema oficial del SENA
- Facilitar la importación masiva de aspirantes en SOFIA Plus
- Asegurar que solo se exporten aspirantes que cumplan todos los requisitos

#### 3.1.3 Precondiciones

- El usuario debe haber iniciado sesión con rol de administrador o operador
- El programa complementario debe existir en el sistema
- Debe haber aspirantes válidos para exportar (no rechazados, con documento, registrados en SOFIA)

#### 3.1.4 Secuencia Normal

1. El usuario accede a la gestión de aspirantes de un programa (RF-ASP-002)
2. El usuario hace clic en "Exportar a Excel" o "Exportar Aspirantes"
3. El sistema valida que el programa exista consultando `complementarios_ofertados`
4. El sistema obtiene aspirantes válidos para exportación desde `aspirantes_complementarios` con filtros:
   - `estado` != 4 (no rechazados)
   - `condocumento` = 1 (con documento) o persona tiene documento
   - `estado_sofia` != 0 (registrados en SOFIA) o existe registro en SOFIA
5. El sistema carga relaciones necesarias: `persona`, `persona.tipoDocumento`, `persona.caracterizacionesComplementarias`
6. El sistema crea una hoja de cálculo Excel usando PHPSpreadsheet con formato SOFIA Plus:
   - **Fila 1:** Título: "FORMATO PARA LA INSCRIPCIÓN DE ASPIRANTES EN SOFIA PLUS v1.0"
   - **Fila 2:** Encabezados: "Tipo Identificación", "Número Identificación", "Código Ficha", "Tipo Población", etc.
   - **Fila 3 en adelante:** Datos de cada aspirante
7. Para cada aspirante, el sistema:
   - Convierte tipo de documento a iniciales: CC, TI, CE, PA, RC
   - Incluye número de documento
   - Incluye código de ficha del programa
   - Incluye caracterización de población (si tiene)
8. El sistema aplica estilos al archivo:
   - Bordes negros en todas las celdas
   - Fuente: Calibri, tamaño 8
   - Ajuste de texto automático
   - Alineación apropiada
9. El sistema genera nombre de archivo: `aspirantes_[nombre_programa]_[fecha_hora].xlsx`
   - Formato fecha: Y-m-d_H-i-s
   - Nombre del programa sanitizado (sin caracteres especiales)
10. El sistema retorna el archivo como descarga directa usando `StreamedResponse`
11. El navegador descarga automáticamente el archivo Excel

#### 3.1.5 Excepciones

**E-001:** Si el programa no existe
- **Condición:** La consulta no encuentra el programa
- **Acción:** El sistema lanzará `ProgramaNoEncontradoException` o retornará error 404
- **Código de Error:** 404 (Not Found)
- **Log:** Se registrará el intento de exportar de programa inexistente

**E-002:** Si no hay aspirantes válidos
- **Condición:** La consulta no retorna aspirantes que cumplan los criterios
- **Acción:** El sistema puede generar un archivo vacío (solo encabezados) o mostrar mensaje: "No hay aspirantes válidos para exportar."
- **Código de Error:** 200 (pero con archivo vacío o mensaje)
- **Comportamiento:** Depende de la implementación, puede ser archivo vacío o mensaje informativo

**E-003:** Si hay error al generar el archivo
- **Condición:** Se produce excepción al crear el archivo Excel (memoria, permisos, etc.)
- **Acción:** El sistema retornará: "Error al generar el archivo Excel. Por favor intente nuevamente."
- **Código de Error:** 500 (Internal Server Error)
- **Log:** Se registrará el error con stack trace completo

#### 3.1.6 Postcondiciones

- Se genera un archivo Excel (.xlsx) con formato compatible con SOFIA Plus v1.0
- El archivo contiene solo aspirantes válidos (no rechazados, con documento, registrados en SOFIA)
- El archivo se descarga automáticamente en el navegador del usuario
- El nombre del archivo incluye el nombre del programa y la fecha/hora de generación
- El sistema queda listo para que el usuario importe el archivo en SOFIA Plus

#### 3.1.7 Requisitos Asociados

- **RF-ASP-002:** Ver Aspirantes de un Programa (requerimiento que extiende)
- **RF-ASP-010:** Validar Documentos (puede ejecutarse antes de exportar)
- **RF-ASP-011:** Validar SOFIA Plus (puede ejecutarse antes de exportar)
- **RNF-ASP-001:** Autenticación Requerida
- **RNF-ASP-007:** Generación Optimizada de Archivos

---

## 4. REQUERIMIENTOS NO FUNCIONALES

### 4.1 RNF-01: Acceso Restringido por Roles

**Prioridad:** Crítica  
**Categoría:** Seguridad

**Descripción:** La funcionalidad de exportar debe requerir autenticación de usuario y acceso según roles (Administrador u Operador).

**Criterios de Aceptación:**
- La ruta está protegida con middleware `auth`
- Solo usuarios con rol Administrador u Operador pueden acceder
- Intentos de acceso sin autenticación redirigen a página de login

### 4.2 RNF-04: Eficiencia Operacional

**Prioridad:** Media  
**Categoría:** Rendimiento

**Descripción:** El sistema debe generar archivos Excel de forma eficiente incluso con grandes volúmenes de aspirantes (1000+), optimizando recursos y procesamiento.

**Criterios de Aceptación:**
- El tiempo de generación no excede 30 segundos para 1000 aspirantes
- Se usa streaming para evitar problemas de memoria
- El archivo se genera de forma incremental
- Las consultas se optimizan para cargar solo datos necesarios

### 4.3 RNF-11: Uso de Estándares

**Prioridad:** Media  
**Categoría:** Mantenibilidad

**Descripción:** El sistema debe seguir estándares de desarrollo y generar archivos en formato compatible con SOFIA Plus v1.0.

**Criterios de Aceptación:**
- El formato del archivo Excel cumple exactamente con la especificación SOFIA Plus v1.0
- El código sigue convenciones de Laravel y PSR-12
- Se utiliza PHPSpreadsheet siguiendo buenas prácticas

---

## 5. CRITERIOS DE ACEPTACIÓN

### 5.1 Criterios Funcionales

**CA-001:** El sistema debe filtrar solo aspirantes válidos
- **Verificación:** Se verifica que no se incluyan rechazados, sin documento, o no registrados en SOFIA
- **Resultado Esperado:** Solo aspirantes válidos en el archivo

**CA-002:** El sistema debe generar formato SOFIA Plus v1.0 correcto
- **Verificación:** Se revisa la estructura del archivo (título, encabezados, datos)
- **Resultado Esperado:** Formato exacto según especificación SOFIA Plus

**CA-003:** El sistema debe convertir tipos de documento a iniciales
- **Verificación:** Se verifica la conversión: CC, TI, CE, PA, RC
- **Resultado Esperado:** Tipos convertidos correctamente

**CA-004:** El sistema debe incluir caracterizaciones de población
- **Verificación:** Se verifica que se incluyan caracterizaciones de cada aspirante
- **Resultado Esperado:** Caracterizaciones presentes en el archivo

**CA-005:** El sistema debe aplicar estilos correctos
- **Verificación:** Se revisa el archivo generado
- **Resultado Esperado:** Bordes, fuente, tamaño, alineación correctos

**CA-006:** El sistema debe generar nombre de archivo apropiado
- **Verificación:** Se revisa el nombre del archivo descargado
- **Resultado Esperado:** Formato: `aspirantes_[programa]_[fecha_hora].xlsx`

**CA-007:** El archivo debe descargarse automáticamente
- **Verificación:** Se prueba la descarga
- **Resultado Esperado:** Descarga automática en el navegador

### 5.2 Criterios No Funcionales

**CA-008:** El tiempo de generación no debe exceder 30 segundos
- **Verificación:** Se mide el tiempo con 1000 aspirantes
- **Resultado Esperado:** Tiempo < 30 segundos

**CA-009:** La funcionalidad debe estar disponible solo para usuarios autenticados
- **Verificación:** Se intenta exportar sin autenticación
- **Resultado Esperado:** Redirección a página de login

**CA-010:** El archivo debe ser compatible con SOFIA Plus
- **Verificación:** Se importa el archivo en SOFIA Plus (prueba manual)
- **Resultado Esperado:** Importación exitosa sin errores

### 5.3 Criterios de Validación

**CA-011:** Si no hay aspirantes válidos, se debe manejar apropiadamente
- **Verificación:** Se prueba con programa sin aspirantes válidos
- **Resultado Esperado:** Archivo vacío o mensaje informativo

**CA-012:** Los errores deben manejarse apropiadamente
- **Verificación:** Se simula error en la generación
- **Resultado Esperado:** Mensaje de error claro y registro en log

---

## 6. TRAZABILIDAD

### 6.1 Requerimientos Relacionados

| Requerimiento | Relación | Descripción |
|---------------|----------|-------------|
| RF-ASP-002 | Extiende | Ver Aspirantes (se ejecuta desde esta vista) |
| RF-ASP-010 | Anterior | Validar Documentos (puede ejecutarse antes) |
| RF-ASP-011 | Anterior | Validar SOFIA Plus (puede ejecutarse antes) |
| RNF-01 | Depende | Acceso Restringido por Roles |
| RNF-04 | Depende | Eficiencia Operacional |
| RNF-11 | Depende | Uso de Estándares |

### 6.2 Casos de Uso Relacionados

- **CU-16:** Exportar Aspirantes a Excel (SOFIA Plus)

### 6.3 Componentes del Sistema

- **Controlador:** `AspiranteComplementarioController@exportarAspirantesExcel()`
- **Servicio:** `AspiranteExportService@exportarAspirantesExcel()`
- **Repositorio:** `AspiranteComplementarioRepository@findForExport()`
- **Librería:** PHPSpreadsheet (PhpOffice\PhpSpreadsheet)
- **Ruta:** `GET /programas-complementarios/{id}/exportar-excel`

### 6.4 Pruebas Relacionadas

- Test unitario del servicio `exportarAspirantesExcel()`
- Test de filtrado de aspirantes válidos
- Test de formato SOFIA Plus correcto
- Test de conversión de tipos de documento
- Test de generación de archivo Excel
- Test de descarga automática
- Test de rendimiento con grandes volúmenes
- Test de integración de la ruta GET

---

## 7. GLOSARIO

| Término | Definición |
|--------|------------|
| **SOFIA Plus v1.0** | Sistema oficial del SENA para gestión de formación, versión 1.0 |
| **Formato SOFIA Plus** | Estructura específica de archivo Excel requerida para importación en SOFIA Plus |
| **Aspirante Válido** | Aspirante que cumple: no rechazado, con documento, registrado en SOFIA |
| **Iniciales de Tipo de Documento** | Códigos cortos: CC (Cédula), TI (Tarjeta Identidad), CE (Cédula Extranjería), PA (Pasaporte), RC (Registro Civil) |
| **StreamedResponse** | Tipo de respuesta HTTP que permite enviar archivos grandes sin cargar todo en memoria |
| **PHPSpreadsheet** | Librería PHP para crear y manipular archivos Excel |

---

**FIN DEL DOCUMENTO**

