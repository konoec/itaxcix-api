# 🔧 GUÍA DE DEBUGGING PARA FORMATO BASE64

## 📋 PROBLEMA ACTUAL
El endpoint `POST /users/{id}/profile-photo` está devolviendo **400 Bad Request**, lo que indica que el servidor está rechazando el formato de datos que estamos enviando.

## 🛠️ HERRAMIENTAS DE DEBUG CREADAS

### 1. **Script de Consola** (`debug-base64-script.js`)
**Uso rápido desde la consola del navegador:**

```javascript
// 1. Cargar el script (copiar y pegar en consola)
// 2. Ejecutar test completo
await debugImageUpload()

// 3. Test específico
await testSpecificFormat("clean")  // Prueba formato limpio
await testSpecificFormat("full")   // Prueba con prefijo data:image
```

### 2. **Página de Debug Completa** (`test-base64-formats.html`)
**Interfaz gráfica para pruebas exhaustivas:**

1. Abrir: `http://localhost/test-base64-formats.html`
2. Cargar imagen o generar imagen de prueba
3. Ejecutar tests individuales o todos a la vez
4. Revisar resultados detallados

### 3. **Métodos en ProfileService**
**Para integración en código:**

```javascript
// Test de todos los formatos
const results = await ProfileService.testAllFormats(userId, base64Image);

// Test de formato específico
const result = await ProfileService.debugUploadFormats(userId, base64Image, 'clean');
```

## 🧪 FORMATOS QUE SE PRUEBAN

| Formato | Descripción | Estructura |
|---------|-------------|------------|
| `clean` | **Base64 puro** (actual) | `{base64Image: "iVBORw0KGgo..."}` |
| `full` | Con prefijo data:image | `{base64Image: "data:image/png;base64,iVBORw0KGgo..."}` |
| `image` | Campo 'image' | `{image: "iVBORw0KGgo..."}` |
| `photo` | Campo 'photo' | `{photo: "iVBORw0KGgo..."}` |
| `nested` | Objeto anidado | `{profilePhoto: {base64: "...", mimeType: "image/png"}}` |
| `file` | Formato archivo | `{file: {data: "...", type: "image/png", name: "profile.jpg"}}` |

## 🚀 PASOS PARA RESOLVER EL PROBLEMA

### Paso 1: Ejecutar Debug
```javascript
// En la consola del navegador (en página con ProfileService cargado)
await debugImageUpload()
```

### Paso 2: Analizar Resultados
- **Si hay formatos exitosos**: Usar el primer formato que funcione
- **Si todos fallan**: Revisar configuración de servidor/endpoint

### Paso 3: Implementar Solución
Una vez identificado el formato correcto, actualizar el método `uploadProfilePhoto()`:

```javascript
// Ejemplo si funciona el formato 'image'
const requestBody = {
    image: cleanBase64  // En lugar de base64Image
};
```

## ⚠️ ERRORES COMUNES Y SOLUCIONES

### 400 Bad Request
- **Causa**: Formato de datos incorrecto
- **Solución**: Probar diferentes formatos con las herramientas

### 401 Unauthorized
- **Causa**: Token inválido o expirado
- **Solución**: Verificar token en sessionStorage

### 404 Not Found
- **Causa**: Endpoint no existe o URL incorrecta
- **Solución**: Verificar URL del endpoint

### 413 Payload Too Large
- **Causa**: Imagen muy grande
- **Solución**: Comprimir imagen o reducir calidad

## 🔍 VERIFICACIONES PREVIAS

Antes de ejecutar los tests, verificar:

```javascript
// 1. Verificar ProfileService
console.log(window.ProfileService); // Debe existir

// 2. Verificar token
console.log(sessionStorage.getItem('authToken')); // Debe tener valor

// 3. Verificar userId
console.log(sessionStorage.getItem('userId')); // Debe tener valor

// 4. Verificar conectividad
fetch('https://149.130.161.148/api/v1/').then(r => console.log('Server:', r.status));
```

## 📊 INTERPRETACIÓN DE RESULTADOS

### ✅ Resultado Exitoso
```javascript
{
  formatType: "clean",
  success: true,
  status: 200,
  response: { success: true, message: "Profile photo updated" }
}
```

### ❌ Resultado Fallido
```javascript
{
  formatType: "clean", 
  success: false,
  status: 400,
  error: { message: "Invalid base64 format" }
}
```

## 💡 TIPS ADICIONALES

1. **Usar imagen pequeña**: Los tests usan imagen de 10x10 px para rapidez
2. **Probar en orden**: Empezar con formato 'clean' (actual)
3. **Revisar logs**: Todos los tests logean información detallada
4. **Comparar con API docs**: Si hay documentación del endpoint

## 🎯 OBJETIVO FINAL

Identificar el formato exacto que acepta el servidor y actualizar el código de producción para usar ese formato, eliminando así el error 400 Bad Request.
