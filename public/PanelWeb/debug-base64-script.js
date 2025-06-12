// 🧪 SCRIPT DE DEBUG PARA FORMATO BASE64
// Ejecutar en la consola del navegador para probar todos los formatos de imagen

console.log('🚀 Iniciando debug de formatos de imagen...');
console.log('📋 Este script probará diferentes formatos de envío de base64 al servidor');

// Función para generar imagen de prueba
function generateDebugImage() {
    const canvas = document.createElement('canvas');
    canvas.width = 10;
    canvas.height = 10;
    const ctx = canvas.getContext('2d');
    
    // Patrón colorido pequeño
    ctx.fillStyle = '#FF0000'; ctx.fillRect(0, 0, 5, 5);
    ctx.fillStyle = '#00FF00'; ctx.fillRect(5, 0, 5, 5);
    ctx.fillStyle = '#0000FF'; ctx.fillRect(0, 5, 5, 5);
    ctx.fillStyle = '#FFFF00'; ctx.fillRect(5, 5, 5, 5);
    
    // ¡IMPORTANTE! Generar como JPEG según requerimiento del servidor
    return canvas.toDataURL('image/jpeg', 0.9);
}

// Función principal de debug
async function debugImageUpload() {
    try {
        // Verificar que tenemos los servicios disponibles
        if (!window.ProfileService) {
            console.error('❌ ProfileService no encontrado. Asegúrate de estar en una página que lo incluya.');
            return;
        }

        // Obtener datos de sesión
        const userId = sessionStorage.getItem('userId') || '1';
        const token = sessionStorage.getItem('authToken');
        
        if (!token) {
            console.error('❌ No hay token de autenticación en sessionStorage');
            console.log('💡 Intenta: sessionStorage.setItem("authToken", "tu_token_aqui")');
            return;
        }

        console.log(`👤 Usuario: ${userId}`);
        console.log(`🔑 Token: ${token.substring(0, 20)}...`);

        // Generar imagen de prueba
        const testImage = generateDebugImage();
        console.log(`📸 Imagen generada: ${Math.round(testImage.length * 0.75 / 1024)}KB`);

        // Ejecutar todos los tests
        console.log('\n🧪 Ejecutando tests de formato...');
        const results = await window.ProfileService.testAllFormats(userId, testImage);

        // Analizar resultados
        const successful = results.filter(r => r.success);
        const failed = results.filter(r => !r.success);

        console.log('\n📊 ANÁLISIS DE RESULTADOS:');
        console.log('========================');
        
        if (successful.length > 0) {
            console.log(`✅ ${successful.length} formato(s) exitoso(s):`);
            successful.forEach(r => {
                console.log(`   🎯 ${r.formatType}: ${r.description}`);
                console.log(`      Status: ${r.status}, Response: ${JSON.stringify(r.response).substring(0, 100)}`);
            });
            
            console.log('\n💡 RECOMENDACIÓN:');
            console.log(`   Usar formato: ${successful[0].formatType}`);
            console.log(`   Descripción: ${successful[0].description}`);
            
        } else {
            console.log('❌ Ningún formato funcionó');
        }

        if (failed.length > 0) {
            console.log(`\n❌ ${failed.length} formato(s) fallido(s):`);
            failed.forEach(r => {
                console.log(`   💥 ${r.formatType}: Status ${r.status}`);
                if (r.error) {
                    console.log(`      Error: ${typeof r.error === 'string' ? r.error : JSON.stringify(r.error).substring(0, 100)}`);
                }
            });
        }

        // Mostrar errores más comunes
        console.log('\n🔍 ANÁLISIS DE ERRORES:');
        const status400 = failed.filter(r => r.status === 400);
        const status401 = failed.filter(r => r.status === 401);
        const status404 = failed.filter(r => r.status === 404);
        const status500 = failed.filter(r => r.status === 500);

        if (status400.length > 0) {
            console.log(`   📝 ${status400.length} error(es) 400 (Bad Request) - Formato de datos incorrecto`);
        }
        if (status401.length > 0) {
            console.log(`   🔐 ${status401.length} error(es) 401 (Unauthorized) - Token inválido`);
        }
        if (status404.length > 0) {
            console.log(`   🔍 ${status404.length} error(es) 404 (Not Found) - Endpoint no existe`);
        }
        if (status500.length > 0) {
            console.log(`   💥 ${status500.length} error(es) 500 (Server Error) - Error interno del servidor`);
        }

        return results;

    } catch (error) {
        console.error('💥 Error durante debug:', error);
        return null;
    }
}

// Función de ayuda para casos específicos
async function testSpecificFormat(formatType = 'clean') {
    const userId = sessionStorage.getItem('userId') || '1';
    const testImage = generateDebugImage();
    
    console.log(`🧪 Testing formato específico: ${formatType}`);
    
    try {
        const result = await window.ProfileService.debugUploadFormats(userId, testImage, formatType);
        console.log('Resultado:', result);
        return result;
    } catch (error) {
        console.error('Error:', error);
        return null;
    }
}

// Mostrar instrucciones
console.log('\n📋 INSTRUCCIONES:');
console.log('==================');
console.log('1. Ejecutar: await debugImageUpload()');
console.log('2. Para test específico: await testSpecificFormat("clean")');
console.log('   Formatos disponibles: clean, full, image, photo, nested, file');
console.log('3. Revisar resultados en consola');
console.log('\n💡 TIP: Este script identificará el formato correcto de base64 que acepta tu API');

// Hacer funciones disponibles globalmente
window.debugImageUpload = debugImageUpload;
window.testSpecificFormat = testSpecificFormat;
window.generateDebugImage = generateDebugImage;

console.log('\n✅ Script de debug cargado. Ejecuta: await debugImageUpload()');
