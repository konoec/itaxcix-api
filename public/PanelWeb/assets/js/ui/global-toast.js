/**
 * Sistema de notificaciones toast global
 * Utiliza el estilo recovery-toast para mantener consistencia en toda la aplicación
 */

/**
 * Muestra una notificación toast elegante
 * @param {string} message - Mensaje a mostrar
 * @param {string} type - Tipo de notificación ('success' o 'error')
 * @param {number} duration - Duración en milisegundos (por defecto 4000)
 */
window.showRecoveryToast = function(message, type = 'success', duration = 4000) {
    console.log(`📢 Recovery Toast ${type}: ${message}`);
    
    const toast = document.getElementById('recovery-toast');
    const toastMessage = document.getElementById('recovery-toast-message');
    const toastContent = toast?.querySelector('.recovery-toast-content');
    const toastIcon = toast?.querySelector('i');
    
    if (toast && toastMessage && toastContent) {
        // Configurar el mensaje
        toastMessage.textContent = message;
        
        // Configurar estilos según el tipo
        if (type === 'error') {
            toastContent.classList.remove('success', 'deactivated');
            toastContent.classList.add('error');
            if (toastIcon) {
                toastIcon.className = 'fas fa-times-circle';
            }
        } else if (type === 'deactivated') {
            toastContent.classList.remove('success', 'error');
            toastContent.classList.add('deactivated');
            if (toastIcon) {
                toastIcon.className = 'fas fa-user-slash';
            }
        } else {
            toastContent.classList.remove('error', 'deactivated');
            toastContent.classList.add('success');
            if (toastIcon) {
                toastIcon.className = 'fas fa-check-circle';
            }
        }
        
        // Mostrar la notificación
        toast.classList.add('show');
        
        // Ocultar automáticamente después del tiempo especificado
        setTimeout(() => {
            toast.classList.remove('show');
        }, duration);
    } else {
        console.error('❌ Elementos de recovery-toast no encontrados');
        // Fallback al sistema de toast simple si existe
        const fallbackToast = document.getElementById('toast');
        const fallbackMessage = document.getElementById('toast-message');
        
        if (fallbackToast && fallbackMessage) {
            fallbackMessage.textContent = message;
            fallbackToast.classList.remove('success', 'error', 'info', 'warning');
            fallbackToast.classList.add(type);
            fallbackToast.classList.add('show');
            
            setTimeout(() => {
                fallbackToast.classList.remove('show');
            }, duration);
        }
    }
};

/**
 * Alias para compatibilidad con código existente
 */
window.showToast = window.showRecoveryToast;

/**
 * Función de prueba para verificar que el sistema funciona (solo para desarrollo)
 * Puedes ejecutar esto en la consola del navegador: testToast()
 */
window.testToast = function() {
    console.log('🧪 Probando sistema de toast...');
    
    // Probar toast de éxito
    setTimeout(() => {
        window.showRecoveryToast('✅ Configuración guardada exitosamente', 'success');
    }, 500);
    
    // Probar toast de error después de 3 segundos
    setTimeout(() => {
        window.showRecoveryToast('❌ Error al procesar la solicitud', 'error');
    }, 3500);
    
    // Probar toast de desactivación después de 6 segundos (color celeste)
    setTimeout(() => {
        window.showRecoveryToast('🚫 Tu cuenta ha sido desactivada. Serás redirigido al login.', 'deactivated');
    }, 6500);
    
    console.log('🧪 Test programado: verás un toast de éxito ahora, uno de error en 3s y uno de desactivación en 6s');
};
