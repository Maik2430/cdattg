# 🤖 Agente de Refactorización Automática - SonarQube

Este comando Artisan analiza todo tu proyecto y aplica correcciones automáticas para problemas comunes de mantenibilidad.

## 🚀 Uso

### Modo DRY-RUN (solo analizar, sin cambios)
```bash
php artisan refactor:sonarqube --dry-run
```

### Modo CORRECCIÓN (aplicar cambios)
```bash
php artisan refactor:sonarqube
```

### Analizar una ruta específica
```bash
php artisan refactor:sonarqube --path=app/Http/Controllers
php artisan refactor:sonarqube --path=app/Services
```

### Combinar opciones
```bash
php artisan refactor:sonarqube --dry-run --path=app/Http/Controllers/InstructorController.php
```

## ✅ Correcciones que aplica automáticamente

1. **Trailing whitespace**: Elimina espacios en blanco al final de líneas
2. **count() vs empty()**: Reemplaza `count($arr) > 0` con `!empty($arr)`
3. **Variables no usadas**: Detecta y reporta (requiere revisión manual)
4. **Literales duplicados**: Detecta strings repetidos que deberían ser constantes
5. **Nombres de métodos**: Detecta métodos con ñ/tildes y sugiere corrección

## 📊 Ejemplo de salida

```
🤖 Agente de Refactorización SonarQube iniciado
📁 Ruta base: /var/www/proyecto
🎯 Analizando: app/Http/Controllers
✏️  Modo CORRECCIÓN (aplicará cambios)
============================================================

📄 Analizando: app/Http/Controllers/InstructorController.php
  ✅ Corregidos 8 problemas

📄 Analizando: app/Http/Controllers/PersonaController.php
  ✅ Corregidos 3 problemas

============================================================
📊 REPORTE FINAL
============================================================

📁 Archivos analizados: 54
🔍 Errores encontrados: 127
✅ Errores corregidos: 127
📝 Archivos modificados: 12

Archivos modificados:
  - app/Http/Controllers/InstructorController.php
  - app/Http/Controllers/PersonaController.php
  ...

✨ Proceso completado
```

## 🔧 Integración con Git

### Antes de ejecutar
```bash
# Crear una rama para las correcciones
git checkout -b refactor/sonarqube-fixes

# Ejecutar modo dry-run primero
php scripts/refactor_sonarqube.php --dry-run

# Si todo se ve bien, aplicar cambios
php scripts/refactor_sonarqube.php

# Revisar cambios
git diff

# Commitear
git add .
git commit -m "refactor: aplicar correcciones automáticas de SonarQube"
```

## 🔒 Seguridad

Este comando **solo se ejecuta en entornos de desarrollo** (local, development, testing).
Está bloqueado automáticamente en producción.

## ⚙️ Integración con CI/CD

### GitHub Actions
```yaml
name: SonarQube Auto-Fix
on: [pull_request]

jobs:
  refactor:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
      - name: Install dependencies
        run: composer install --no-interaction
      - name: Run refactor check
        run: php artisan refactor:sonarqube --dry-run
```

## 🎯 Roadmap

- [ ] Detectar métodos con múltiples returns
- [ ] Extraer métodos largos automáticamente
- [ ] Detectar clases con muchos métodos
- [ ] Integración directa con SonarQube API
- [ ] Generación de constantes automática
- [ ] Soporte para otros lenguajes (JS, CSS)

## 🤝 Contribuir

Si encuentras bugs o quieres agregar más reglas de refactorización, edita el script según tus necesidades.

---

**Última actualización:** 2025-11-17  
**Versión:** Laravel 13.0, PHP 8.4+

