# ESPECIFICACIÓN DE REQUERIMIENTOS DE SOFTWARE (SRS)
## RF-ASP-006: Crear Nuevo Aspirante

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

Este documento especifica los requerimientos funcionales y no funcionales para el caso de uso **"Crear Nuevo Aspirante"** (RF-ASP-006), que forma parte del módulo de Gestión de Aspirantes del sistema CDATTG Web.

### 1.2 Alcance

Este SRS cubre la funcionalidad que permite al administrador crear una nueva persona en el sistema y simultáneamente agregarla como aspirante a un programa complementario en una sola operación.

### 1.3 Definiciones, Acrónimos y Abreviaciones

- **SRS**: Software Requirements Specification
- **RF**: Requerimiento Funcional
- **RNF**: Requerimiento No Funcional
- **CDATTG**: Centro de Desarrollo Agroempresarial y Turístico del Guaviare
- **SENA**: Servicio Nacional de Aprendizaje

### 1.4 Referencias

- IEEE Std 830-1998, IEEE Recommended Practice for Software Requirements Specifications
- Documentación del módulo: `docs/gestion-aspirantes.md`
- Caso de Uso: CU-14

---

## 2. DESCRIPCIÓN GENERAL

### 2.1 Perspectiva del Requerimiento

Este requerimiento extiende "Agregar Aspirante" cuando la persona no existe en el sistema. Permite crear la persona y agregarla como aspirante en una sola operación.

### 2.2 Funciones del Requerimiento

- Cargar datos necesarios para formulario (tipos documento, géneros, ubicaciones, caracterizaciones)
- Validar datos de entrada
- Crear registro en tabla `personas`
- Crear registro en tabla `aspirantes_complementarios`
- Crear relaciones de caracterizaciones si aplica

### 2.3 Características del Usuario

**Actor Principal:** Administrador o Operador del sistema

### 2.4 Restricciones

- Requiere autenticación de usuario
- El programa complementario debe existir
- El número de documento no debe existir ya en el sistema
- Todos los campos obligatorios deben ser completados

---

## 3. REQUERIMIENTOS FUNCIONALES

### 3.1 RF-ASP-006: Crear Nuevo Aspirante

**Identificador:** RF-ASP-006  
**Título:** Crear Nuevo Aspirante  
**Versión:** 1.0  
**Prioridad:** Media  
**Urgencia:** Baja

#### 3.1.1 Descripción

El sistema debe permitir al administrador crear una nueva persona en la base de datos y simultáneamente agregarla como aspirante a un programa complementario. Este caso de uso se ejecuta cuando el administrador intenta agregar un aspirante pero la persona no existe en el sistema.

#### 3.1.2 Objetivos Asociados

- Permitir al administrador crear una nueva persona en el sistema y agregarla como aspirante a un programa en una sola operación
- Mantener integridad de datos mediante validaciones exhaustivas

#### 3.1.3 Precondiciones

- El usuario debe haber iniciado sesión con rol de administrador o operador
- El programa complementario debe existir
- La persona no debe existir en el sistema (se ejecuta como extensión de "Agregar Aspirante")

#### 3.1.4 Secuencia Normal

1. El usuario intenta agregar un aspirante pero la búsqueda no encuentra la persona (RF-ASP-005)
2. El sistema ofrece la opción de "Crear Nuevo Aspirante"
3. El usuario accede al formulario de creación
4. El sistema carga datos necesarios para el formulario:
   - Tipos de documento desde `tipos_documento`
   - Géneros desde `tipos_genero`
   - Caracterizaciones complementarias desde `caracterizaciones_complementarias`
   - Países desde `paises`
   - Departamentos desde `departamentos` (filtrados por país si aplica)
   - Municipios desde `municipios` (filtrados por departamento si aplica)
5. El usuario completa el formulario con datos de la persona:
   - Tipo y número de documento (obligatorio, único)
   - Nombres y apellidos (obligatorios)
   - Fecha de nacimiento (opcional)
   - Género (opcional)
   - Contacto: teléfono, celular, email (opcionales)
   - Ubicación: país, departamento, municipio, dirección (opcionales)
   - Caracterizaciones (opcional, múltiples)
6. El usuario envía el formulario
7. El sistema valida todos los datos de entrada mediante Form Request
8. El sistema valida que el número de documento no exista ya
9. El sistema crea el registro en la tabla `personas` con todos los datos proporcionados
10. El sistema crea el registro en la tabla `aspirantes_complementarios` asociando la nueva persona al programa con estado "En proceso" (1)
11. Si se especificaron caracterizaciones, el sistema crea registros en la tabla pivot `persona_caracterizacion`
12. El sistema retorna mensaje de éxito

#### 3.1.5 Excepciones

**E-001:** Si faltan campos obligatorios
- **Condición:** Campos requeridos están vacíos
- **Acción:** El sistema mostrará mensajes de error específicos por campo
- **Código de Error:** 422 (Unprocessable Entity)

**E-002:** Si el número de documento ya existe
- **Condición:** Ya existe una persona con el mismo número de documento
- **Acción:** El sistema mostrará: "Ya existe una persona con este número de documento."
- **Código de Error:** 422 (Unprocessable Entity)

**E-003:** Si el email ya existe
- **Condición:** Ya existe una persona con el mismo email
- **Acción:** El sistema mostrará: "Ya existe una persona con este correo electrónico."
- **Código de Error:** 422 (Unprocessable Entity)

**E-004:** Si hay error al crear
- **Condición:** Se produce excepción al insertar en BD
- **Acción:** El sistema retornará: "Error al crear el aspirante. Por favor intente nuevamente."
- **Código de Error:** 500 (Internal Server Error)
- **Log:** Se registrará el error con stack trace

#### 3.1.6 Postcondiciones

- Se crea un nuevo registro en la tabla `personas` con todos los datos proporcionados
- Se crea un registro en la tabla `aspirantes_complementarios` asociando la nueva persona al programa con estado "En proceso" (1)
- Si se especificaron caracterizaciones, se crean registros en la tabla pivot `persona_caracterizacion`
- El sistema actualiza el conteo de aspirantes del programa
- El usuario recibe confirmación de la creación exitosa

#### 3.1.7 Requisitos Asociados

- **RF-ASP-003:** Agregar Aspirante (requerimiento que extiende)
- **RF-ASP-005:** Buscar Persona (requerimiento anterior que determina si se ejecuta este)
- **RNF-ASP-001:** Autenticación Requerida
- **RNF-ASP-003:** Validación de Datos de Entrada

---

## 4. REQUERIMIENTOS NO FUNCIONALES

### 4.1 RNF-01: Acceso Restringido por Roles

**Prioridad:** Crítica  
**Categoría:** Seguridad

**Descripción:** La funcionalidad de crear aspirante debe requerir autenticación de usuario y acceso según roles (Administrador u Operador).

**Criterios de Aceptación:**
- La ruta está protegida con middleware `auth`
- Solo usuarios con rol Administrador u Operador pueden acceder
- Intentos de acceso sin autenticación redirigen a página de login

### 4.2 RNF-02: Integridad y Protección de Datos

**Prioridad:** Alta  
**Categoría:** Seguridad

**Descripción:** El sistema debe garantizar la integridad y protección de datos mediante validaciones exhaustivas antes de crear registros.

**Criterios de Aceptación:**
- Se utiliza Form Request para validación
- Campos obligatorios son validados
- Formatos de datos son validados (email, fechas, etc.)
- Unicidad de documento y email es validada
- Los datos se sanitizan antes de almacenar

---

## 5. CRITERIOS DE ACEPTACIÓN

### 5.1 Criterios Funcionales

**CA-001:** El sistema debe validar campos obligatorios
- **Verificación:** Se envía formulario con campos obligatorios vacíos
- **Resultado Esperado:** Mensajes de error específicos por campo

**CA-002:** El sistema debe validar unicidad de documento
- **Verificación:** Se intenta crear persona con documento existente
- **Resultado Esperado:** Error indicando que el documento ya existe

**CA-003:** El sistema debe crear registro en tabla personas
- **Verificación:** Se verifica en BD después de crear
- **Resultado Esperado:** Registro creado con todos los datos correctos

**CA-004:** El sistema debe crear registro en tabla aspirantes_complementarios
- **Verificación:** Se verifica en BD después de crear
- **Resultado Esperado:** Registro creado con estado=1 y relación correcta

**CA-005:** El sistema debe crear relaciones de caracterizaciones si aplica
- **Verificación:** Se crea con caracterizaciones seleccionadas
- **Resultado Esperado:** Registros en tabla pivot creados correctamente

### 5.2 Criterios No Funcionales

**CA-006:** La funcionalidad debe estar disponible solo para usuarios autenticados
- **Verificación:** Se intenta crear sin autenticación
- **Resultado Esperado:** Redirección a página de login

**CA-007:** Los datos deben ser validados exhaustivamente
- **Verificación:** Se revisa el Form Request
- **Resultado Esperado:** Validaciones completas implementadas

---

## 6. TRAZABILIDAD

### 6.1 Requerimientos Relacionados

| Requerimiento | Relación | Descripción |
|---------------|----------|-------------|
| RF-ASP-003 | Extiende | Agregar Aspirante (se ejecuta si persona no existe) |
| RF-ASP-005 | Anterior | Buscar Persona (determina si se ejecuta este) |
| RNF-01 | Depende | Acceso Restringido por Roles |
| RNF-02 | Depende | Integridad y Protección de Datos |

### 6.2 Casos de Uso Relacionados

- **CU-14:** Crear Nuevo Aspirante

### 6.3 Componentes del Sistema

- **Controlador:** `AspiranteComplementarioController@create()` y `@store()`
- **Servicio:** `PersonaService@crearPersona()`
- **Repositorio:** `PersonaRepository@create()`
- **Repositorio:** `AspiranteComplementarioRepository@create()`
- **Form Request:** `CrearPersonaRequest` (validación)
- **Rutas:** `GET /aspirantes/create` y `POST /aspirantes`

---

## 7. GLOSARIO

| Término | Definición |
|--------|------------|
| **Caracterización Complementaria** | Tipo de población del aspirante (víctima del conflicto, desplazado, etc.) |
| **Tabla Pivot** | Tabla intermedia que relaciona dos tablas en relación many-to-many |

---

**FIN DEL DOCUMENTO**

