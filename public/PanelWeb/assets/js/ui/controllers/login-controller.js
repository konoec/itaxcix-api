class LoginController {
    constructor() {
        this.form = document.getElementById('login-form');
        this.errorMsg = document.getElementById('error-msg');
        this.submitBtn = this.form.querySelector('.btn-ingresar');
        this.btnText = this.submitBtn.querySelector('.btn-text');
        this.btnLoading = this.submitBtn.querySelector('.btn-loading');        this.loginService = window.LoginService;
        
          this.baseUrl = window.location.hostname.includes('github.io') 
            ? '/PanelWeb'
            : '';
        this.init();
    }    init() {
        // Prevenir comportamiento por defecto del formulario
        this.form.addEventListener('submit', async (e) => {
            e.preventDefault();
            await this.handleLogin();
        });        // Configurar toggle de contraseña
        this.setupPasswordToggle();
        
        // Configurar validación del documento
        this.setupDocumentValidation();
    }    /**
     * Configura la funcionalidad para mostrar/ocultar contraseña
     */
    setupPasswordToggle() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eye-icon');
        
        if (passwordInput && eyeIcon) {
            eyeIcon.addEventListener('click', (e) => {
                e.preventDefault(); // Prevenir cualquier comportamiento por defecto
                
                const isPassword = passwordInput.getAttribute('type') === 'password';
                
                // Toggle tipo de input
                passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                
                // Toggle icono
                eyeIcon.classList.toggle('fa-eye', !isPassword);
                eyeIcon.classList.toggle('fa-eye-slash', isPassword);
                
                // Cambiar título del ícono para accesibilidad
                eyeIcon.setAttribute('title', isPassword ? 'Ocultar contraseña' : 'Mostrar contraseña');
            });
            
            // Establecer título inicial
            eyeIcon.setAttribute('title', 'Mostrar contraseña');
        }    }

    setLoading(isLoading) {
        this.submitBtn.disabled = isLoading;
        this.btnText.style.display = isLoading ? 'none' : 'inline';
        this.btnLoading.style.display = isLoading ? 'inline' : 'none';
    }    async handleLogin() {
        const documentInput = document.getElementById('documentValue');
        const passwordInput = document.getElementById('password');
        if (!documentInput || !passwordInput) {
            this.showError("Error interno: formulario no disponible.");
            return;
        }
        const documentValue = documentInput.value.trim();
        const password = passwordInput.value;

        // Validar que el documento tenga exactamente 8 dígitos numéricos
        if (!documentValue) {
            this.showError("Por favor, ingresa tu número de documento.");
            return;
        }
        
        if (!/^[0-9]{8}$/.test(documentValue)) {
            this.showError("El documento debe contener exactamente 8 dígitos numéricos.");
            return;
        }        try {
            this.setLoading(true);
            const response = await this.loginService.verifyCredentials(documentValue, password);            // La respuesta exitosa debe contener el token y otros datos
            if (response && response.token) {
                console.log('✅ Login exitoso, datos recibidos:', {
                    userId: response.userId,
                    documentValue: response.documentValue,
                    firstName: response.firstName,
                    lastName: response.lastName,
                    roles: response.roles,
                    permissions: response.permissions,
                    rating: response.rating,
                    hasToken: !!response.token
                });
                
                // Limpiar sessionStorage antes de guardar nuevos datos
                sessionStorage.clear();
                  // Guardar datos de autenticación
                sessionStorage.setItem("isLoggedIn", "true");
                sessionStorage.setItem("authToken", response.token);
                sessionStorage.setItem("userId", response.userId?.toString() || "");
                sessionStorage.setItem("documentValue", response.documentValue || documentValue);
                sessionStorage.setItem("userRoles", JSON.stringify(response.roles || []));
                sessionStorage.setItem("userPermissions", JSON.stringify(response.permissions || []));
                sessionStorage.setItem("userAvailability", response.availability?.toString() ?? "true");
                
                // Guardar información del nombre del usuario
                sessionStorage.setItem("firstName", response.firstName || "");
                sessionStorage.setItem("lastName", response.lastName || "");
                sessionStorage.setItem("userFullName", `${response.firstName || ""} ${response.lastName || ""}`.trim());
                sessionStorage.setItem("userRating", response.rating?.toString() || "0");
                
                sessionStorage.setItem("loginTime", Date.now().toString());                // Prevenir navegación hacia atrás y redirigir
                window.history.pushState(null, '', window.location.href);
                window.location.replace(`${this.baseUrl}/pages/Admision/ControlAdmisionConductores.html`);
            } else {
                this.showError("Error en la respuesta del servidor. Inténtalo de nuevo.");
            }        } catch (error) {
            console.error('Error durante el login:', error);
              // Mapear errores a mensajes amigables para el usuario
            let errorMessage = "Error inesperado. Inténtalo de nuevo.";
            
            if (error.message) {
                const msg = error.message.toLowerCase();
                
                // Manejar errores específicos de la API mejorados
                if (msg.includes('documento o contraseña') && msg.includes('incorrectos')) {
                    errorMessage = "El documento o contraseña ingresados son incorrectos. Verifica tus datos.";
                } else if (msg.includes('credenciales') && (msg.includes('inválidas') || msg.includes('incorrectas'))) {
                    errorMessage = "Las credenciales proporcionadas no son válidas. Intenta nuevamente.";
                } else if (msg.includes('datos enviados no son válidos') || msg.includes('formato del documento')) {
                    errorMessage = "Los datos enviados no son válidos. Verifica el formato del documento (8 dígitos).";
                } else if (msg.includes('error interno del servidor') || msg.includes('ocurrió un error inesperado')) {
                    errorMessage = "Error interno del servidor. Intenta más tarde o contacta al administrador.";
                } else if (msg.includes('usuario y contraseña son requeridos')) {
                    errorMessage = "Por favor, completa todos los campos.";
                } else if (msg.includes('acceso denegado') || msg.includes('no tienes permisos')) {
                    errorMessage = "Acceso denegado. No tienes permisos para acceder.";
                } else if (msg.includes('servicio no encontrado') || msg.includes('verifica la configuración')) {
                    errorMessage = "Servicio no disponible. Contacta al administrador.";
                } else if (msg.includes('servidor no disponible temporalmente')) {
                    errorMessage = "El servidor no está disponible temporalmente. Intenta más tarde.";
                } else if (msg.includes('certificado ssl') || 
                          msg.includes('certificate') ||
                          msg.includes('ssl') ||
                          msg.includes('contacta al administrador')) {
                    errorMessage = "Error de certificado SSL del servidor. ";
                    this.showSSLError();
                    return; // No mostrar error normal, mostrar panel SSL
                } else if (msg.includes('error en la comunicación') || 
                          msg.includes('network') || 
                          msg.includes('fetch') ||
                          msg.includes('conexión') ||
                          msg.includes('failed to fetch')) {
                    errorMessage = "Error de conexión. Verifica tu internet o que el servidor esté disponible.";
                } else if (msg.includes('respuesta del servidor incompleta')) {
                    errorMessage = "Error en la respuesta del servidor.";
                } else {
                    // Usar el mensaje original si es descriptivo
                    errorMessage = error.message;
                }
            }
            
            this.showError(errorMessage);
        } finally {
            this.setLoading(false);
        }
    }

    showError(message) {
        this.errorMsg.textContent = message;
        this.errorMsg.style.display = 'block';
        setTimeout(() => {
            this.errorMsg.style.display = 'none';
        }, 5000);
    }

    showSSLError() {
        // Crear un panel especial para errores SSL
        const sslPanel = document.createElement('div');
        sslPanel.id = 'ssl-error-panel';
        sslPanel.style.cssText = `
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            border: 2px solid #dc3545;
            border-radius: 10px;
            padding: 30px;
            max-width: 600px;
            z-index: 10000;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        `;
        
        sslPanel.innerHTML = `
            <h3 style="color: #dc3545; margin-top: 0;">🔒 Error de Certificado SSL</h3>
            <p><strong>El servidor tiene un certificado SSL inválido.</strong></p>
            <p>Por eso funciona en Postman pero no en el navegador.</p>
            
            <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 15px 0;">
                <h4>💡 Soluciones:</h4>
                <ol>
                    <li><strong>Visita:</strong> <a href="https://149.130.161.148" target="_blank">https://149.130.161.148</a> y acepta el certificado</li>
                    <li><strong>O usa Chrome en modo desarrollo</strong> (ver herramienta de diagnóstico)</li>
                    <li><strong>O contacta al administrador</strong> del servidor</li>
                </ol>
            </div>
            
            <div style="text-align: center; margin-top: 20px;">
                <button onclick="window.open('${this.baseUrl}/ssl-diagnostic.html', '_blank')" 
                        style="background: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 5px; margin: 5px; cursor: pointer;">
                    🛠️ Herramienta de Diagnóstico
                </button>
                <button onclick="window.open('https://149.130.161.148', '_blank')" 
                        style="background: #28a745; color: white; border: none; padding: 10px 20px; border-radius: 5px; margin: 5px; cursor: pointer;">
                    🔗 Abrir Servidor
                </button>
                <button onclick="document.getElementById('ssl-error-panel').remove()" 
                        style="background: #6c757d; color: white; border: none; padding: 10px 20px; border-radius: 5px; margin: 5px; cursor: pointer;">
                    ❌ Cerrar
                </button>
            </div>
        `;
        
        // Agregar overlay
        const overlay = document.createElement('div');
        overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 9999;
        `;
        overlay.onclick = () => {
            document.body.removeChild(overlay);
            document.body.removeChild(sslPanel);
        };
        
        document.body.appendChild(overlay);
        document.body.appendChild(sslPanel);
    }

    /**
     * Configura la validación del campo de documento
     * Solo permite 8 dígitos numéricos
     */
    setupDocumentValidation() {
        const documentInput = document.getElementById('documentValue');
        
        if (documentInput) {
            // Evento para filtrar solo números y limitar a 8 dígitos
            documentInput.addEventListener('input', (e) => {
                // Remover cualquier carácter que no sea número
                let value = e.target.value.replace(/[^0-9]/g, '');
                
                // Limitar a máximo 8 dígitos
                if (value.length > 8) {
                    value = value.slice(0, 8);
                }
                
                // Actualizar el valor del input
                e.target.value = value;
            });
            
            // Prevenir pegar texto que no sean números
            documentInput.addEventListener('paste', (e) => {
                e.preventDefault();
                
                // Obtener texto del clipboard
                const paste = (e.clipboardData || window.clipboardData).getData('text');
                
                // Filtrar solo números y limitar a 8 dígitos
                const filteredPaste = paste.replace(/[^0-9]/g, '').slice(0, 8);
                
                // Establecer el valor filtrado
                documentInput.value = filteredPaste;
            });
            
            // Prevenir entrada de teclas no numéricas (excepto teclas de control)
            documentInput.addEventListener('keypress', (e) => {
                // Permitir teclas de control (backspace, delete, tab, etc.)
                if (e.key === 'Backspace' || e.key === 'Delete' || e.key === 'Tab' || 
                    e.key === 'ArrowLeft' || e.key === 'ArrowRight' || e.key === 'Home' || e.key === 'End') {
                    return;
                }
                
                // Permitir solo números (0-9)
                if (!/^[0-9]$/.test(e.key)) {
                    e.preventDefault();
                }
                
                // Prevenir entrada si ya tiene 8 dígitos
                if (documentInput.value.length >= 8) {
                    e.preventDefault();
                }
            });
        }
    }
}

// Inicializar el controlador cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    new LoginController();
});