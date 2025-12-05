# Mapeo de Requerimientos No Funcionales (RNF)

## RNF del Proyecto

| ID | Categoría | Nombre | Descripción |
|----|-----------|--------|--------------|
| RNF-01 | Seguridad | Acceso Restringido por Roles | El sistema debe restringir el acceso a funcionalidades según los roles del usuario (Administrador, Operador, Aspirante) |
| RNF-02 | Seguridad | Integridad y Protección de Datos | El sistema debe garantizar la integridad y protección de datos mediante validaciones, encriptación y auditoría |
| RNF-03 | Rendimiento | Procesamiento Asíncrono | Las operaciones que requieren tiempo deben procesarse de forma asíncrona para no bloquear la interfaz |
| RNF-04 | Rendimiento | Eficiencia Operacional | El sistema debe procesar operaciones de forma eficiente, optimizando consultas y recursos |
| RNF-05 | Compatibilidad | Soporte de Navegadores | El sistema debe ser compatible con los navegadores modernos (Chrome, Firefox, Edge, Safari) |
| RNF-06 | Compatibilidad | Diseño Responsivo | El sistema debe adaptarse a diferentes tamaños de pantalla (desktop, tablet, móvil) |
| RNF-07 | Escalabilidad | Diseño Modular | El sistema debe estar diseñado de forma modular para facilitar mantenimiento y expansión |
| RNF-08 | Escalabilidad | Capacidad de Expansión de Documentos | El sistema debe soportar el crecimiento en volumen de documentos almacenados |
| RNF-09 | Escalabilidad | Crecimiento de Usuarios y Documentos | El sistema debe soportar el crecimiento en número de usuarios y documentos sin degradación |
| RNF-10 | Mantenibilidad | Estructura y Documentación del Código | El código debe estar bien estructurado y documentado siguiendo estándares |
| RNF-11 | Mantenibilidad | Uso de Estándares | El sistema debe seguir estándares de desarrollo (PSR-12, Laravel conventions) |
| RNF-12 | Mantenibilidad | Pruebas Automatizadas | El sistema debe tener pruebas automatizadas para garantizar calidad |

## Mapeo con RNF Usados en SRS

| RNF Anterior (SRS) | RNF Proyecto | Justificación |
|---------------------|--------------|----------------|
| RNF-ASP-001: Autenticación Requerida | **RNF-01** | Acceso restringido por roles incluye autenticación |
| RNF-ASP-002: Control de Acceso Basado en Permisos | **RNF-01** | Control de acceso es parte de acceso restringido por roles |
| RNF-ASP-003: Validación de Datos de Entrada | **RNF-02** | Validación es parte de integridad y protección de datos |
| RNF-ASP-005: Auditoría y Logging | **RNF-02** | Auditoría es parte de protección de datos |
| RNF-ASP-006: Procesamiento Eficiente | **RNF-04** | Eficiencia operacional |
| RNF-ASP-007: Generación Optimizada de Archivos | **RNF-04** | Optimización de recursos |
| RNF-ASP-009: Mensajes de Error Claros | **RNF-06** | Parte de usabilidad en diseño responsivo |
| RNF-ASP-010: Respuestas JSON Consistentes | **RNF-11** | Uso de estándares de API |
| RNF-ASP-011: Búsqueda en Tiempo Real | **RNF-03** | Procesamiento asíncrono (AJAX) |

## Aplicación por Caso de Uso

### RF-ASP-001: Listar Programas
- **RNF-01:** Acceso restringido por roles (autenticación requerida)
- **RNF-04:** Eficiencia operacional (consultas optimizadas)

### RF-ASP-002: Ver Aspirantes
- **RNF-01:** Acceso restringido por roles
- **RNF-04:** Eficiencia operacional (eager loading)

### RF-ASP-003: Agregar Aspirante
- **RNF-01:** Acceso restringido por roles
- **RNF-02:** Integridad y protección de datos (validaciones, auditoría)
- **RNF-04:** Eficiencia operacional
- **RNF-11:** Uso de estándares (Form Requests, JSON)

### RF-ASP-004: Rechazar Aspirante
- **RNF-01:** Acceso restringido por roles (permisos específicos)
- **RNF-02:** Integridad y protección de datos (auditoría)
- **RNF-11:** Uso de estándares (JSON)

### RF-ASP-005: Buscar Persona
- **RNF-01:** Acceso restringido por roles
- **RNF-03:** Procesamiento asíncrono (AJAX)
- **RNF-04:** Eficiencia operacional
- **RNF-11:** Uso de estándares (JSON)

### RF-ASP-006: Crear Aspirante
- **RNF-01:** Acceso restringido por roles
- **RNF-02:** Integridad y protección de datos (validaciones)

### RF-ASP-007: Estadísticas de Exclusión
- **RNF-01:** Acceso restringido por roles
- **RNF-04:** Eficiencia operacional
- **RNF-11:** Uso de estándares (JSON)

### RF-ASP-008: Exportar a Excel
- **RNF-01:** Acceso restringido por roles
- **RNF-04:** Eficiencia operacional (generación optimizada)
- **RNF-11:** Uso de estándares (formato SOFIA Plus)

