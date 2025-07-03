/**
 * Script de prueba para verificar el sistema de logout automático global
 * 
 * Para probar:
 * 1. Abrir la página en el navegador
 * 2. Abrir la consola del navegador (F12)
 * 3. Ejecutar estos comandos para verificar el estado
 */

// Función para verificar el estado actual del sistema
function verificarEstadoSistema() {
    console.log('=== VERIFICACIÓN DEL SISTEMA GLOBAL ===');
    
    // 1. Verificar que el GlobalInitializer esté disponible
    console.log('1. GlobalInitializer disponible:', typeof window.GlobalInitializer !== 'undefined');
    
    // 2. Verificar que el sistema esté inicializado
    if (window.GlobalInitializer) {
        console.log('2. Sistema inicializado:', window.GlobalInitializer.initialized);
        console.log('3. Monitor de usuario activo:', window.GlobalInitializer.userMonitor.isMonitoring);
    }
    
    // 3. Verificar UserService
    console.log('4. UserService disponible:', typeof window.UserService !== 'undefined');
    
    // 4. Verificar sesión
    console.log('5. Token en sesión:', sessionStorage.getItem('authToken') ? 'SÍ' : 'NO');
    console.log('6. Usuario logueado:', sessionStorage.getItem('isLoggedIn'));
    
    // 5. Verificar función de forzar verificación
    console.log('7. Función forceUserStatusCheck disponible:', typeof window.forceUserStatusCheck !== 'undefined');
    
    console.log('=== FIN VERIFICACIÓN ===');
}

// Función para forzar una verificación manual
function forzarVerificacion() {
    console.log('🔍 Forzando verificación del estado del usuario...');
    if (typeof window.forceUserStatusCheck === 'function') {
        window.forceUserStatusCheck();
    } else {
        console.error('❌ Función forceUserStatusCheck no disponible');
    }
}

// Función para simular un usuario desactivado (solo para pruebas)
function simularUsuarioDesactivado() {
    console.log('🧪 SIMULACIÓN: Usuario desactivado');
    if (window.GlobalInitializer && window.GlobalInitializer.userMonitor) {
        window.GlobalInitializer.userMonitor.handleUserLogout('Usuario desactivado por prueba', true);
    }
}

// Función para simular sesión expirada (solo para pruebas)
function simularSesionExpirada() {
    console.log('🧪 SIMULACIÓN: Sesión expirada');
    if (window.GlobalInitializer && window.GlobalInitializer.userMonitor) {
        window.GlobalInitializer.userMonitor.handleUserLogout('Token inválido por prueba', false);
    }
}

// Exportar funciones para uso en consola
window.verificarEstadoSistema = verificarEstadoSistema;
window.forzarVerificacion = forzarVerificacion;
window.simularUsuarioDesactivado = simularUsuarioDesactivado;
window.simularSesionExpirada = simularSesionExpirada;

console.log('🧪 HERRAMIENTAS DE PRUEBA CARGADAS');
console.log('📋 Comandos disponibles:');
console.log('   - verificarEstadoSistema() - Verificar estado del sistema');
console.log('   - forzarVerificacion() - Forzar verificación del usuario');
console.log('   - simularUsuarioDesactivado() - Simular logout por desactivación');
console.log('   - simularSesionExpirada() - Simular logout por sesión expirada');
