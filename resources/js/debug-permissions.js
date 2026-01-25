// Script para verificar permisos del usuario actual (solo para debugging)
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔍 Verificando estado de autenticación...');
    
    // Solo para debugging - NO redirigir, Laravel maneja la autenticación
    fetch('/api/user/permissions')
        .then(response => {
            console.log('📊 Status de respuesta:', response.status, response.statusText);
            
            if (!response.ok) {
                if (response.status === 401) {
                    console.log('ℹ️ Usuario no autenticado - Laravel debería manejar esto');
                    // NO redirigir aquí - Laravel ya maneja la autenticación
                    return;
                } else if (response.status === 419) {
                    console.log('⚠️ Token CSRF expirado - Laravel debería manejar esto');
                    return;
                } else {
                    console.log('❌ Error inesperado:', response.status);
                    return;
                }
            }
            return response.json();
        })
        .then(data => {
            if (!data) return;
            
            console.log('✅ Usuario autenticado correctamente');
            console.log('✅ Permisos del usuario:', data.permissions);
            
            // Verificar permiso específico (solo para información)
            const gestionCompetencias = data.permissions && data.permissions.includes('GESTIONAR COMPETENCIAS RESULTADO APRENDIZAJE');
            console.log('🎯 ¿Tiene permiso GESTIONAR COMPETENCIAS RESULTADO APRENDIZAJE?', gestionCompetencias);
            
            if (gestionCompetencias) {
                console.log('✅ El usuario puede gestionar competencias');
            } else {
                console.log('ℹ️ El usuario no tiene el permiso específico');
            }
        })
        .catch(error => {
            console.log('❌ Error en la petición:', error.message);
        });
});
