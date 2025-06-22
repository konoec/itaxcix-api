// Controlador para manejar el perfil del usuario
class ProfileController {    constructor() {
        // Inicializar ProfileService de forma segura
        this.profileService = window.ProfileService || new ProfileService();
        this.profileImageElement = null;
        this.userDisplayElement = null;
        this.currentUserId = null;
        this.imageCache = new Map(); // Cache para las imágenes
        
        // Elementos del modal de perfil (se inicializarán después)
        this.profileModal = null;
        this.profileContainer = null;
        
        // Inicializar de forma asíncrona sin bloquear el constructor
        this.init().catch(error => {
            console.warn('⚠️ Error en inicialización automática de ProfileController:', error);
        });
    }/**
     * Inicializa el controlador del perfil
     */
    async init() {
        console.log('🎯 Inicializando ProfileController...');
        
        try {
            // Buscar elementos del perfil en el DOM
            this.findProfileElements();
            
            // Cargar información del usuario
            await this.loadUserProfile();
            
            // Configurar modal cuando el DOM esté completamente cargado
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => {
                    this.setupModalElements();
                });
            } else {
                // El DOM ya está cargado
                setTimeout(() => {
                    this.setupModalElements();
                }, 100);
            }
            
            console.log('✅ ProfileController inicializado');
        } catch (error) {
            console.warn('⚠️ Error durante inicialización del ProfileController:', error);
            // No lanzar el error para no bloquear la aplicación
        }
    }

    /**
     * Busca los elementos del perfil en el DOM
     */
    findProfileElements() {
        // Buscar imagen de perfil (puede tener diferentes IDs o clases)
        this.profileImageElement = document.getElementById('profile-image') ||
                                 document.querySelector('.profile-image') ||
                                 document.querySelector('[data-profile="image"]');
        
        // Buscar elemento de display del usuario
        this.userDisplayElement = document.getElementById('user-display') ||
                                document.querySelector('.user-display') ||
                                document.querySelector('[data-profile="name"]');

        console.log('🔍 Elementos encontrados:', {
            profileImage: !!this.profileImageElement,
            userDisplay: !!this.userDisplayElement
        });
    }    /**
     * Carga el perfil completo del usuario
     */
    async loadUserProfile() {
        try {
            // Obtener ID del usuario de sessionStorage
            this.currentUserId = sessionStorage.getItem("userId");
            
            if (!this.currentUserId) {
                console.warn('⚠️ No se encontró ID de usuario en sessionStorage');
                this.setDefaultProfile();
                return;
            }

            console.log(`👤 Cargando perfil para usuario ${this.currentUserId}`);

            // Cargar nombre del usuario INMEDIATAMENTE (no depende de API)
            this.updateUserName();

            // Cargar foto de perfil EN PARALELO (no bloquea)
            // NO usar await aquí para que sea completamente independiente
            this.loadProfileImage().catch(error => {
                console.warn('⚠️ Error cargando imagen de perfil (no crítico):', error);
                this.setDefaultProfileImage();
            });

            console.log('✅ Perfil básico cargado (imagen en segundo plano)');

        } catch (error) {
            console.error('❌ Error al cargar perfil:', error);
            this.setDefaultProfile();
        }
    }    /**
     * Actualiza el nombre del usuario en la interfaz
     */
    updateUserName() {
        if (!this.userDisplayElement) {
            return;
        }

        const fullName = sessionStorage.getItem("userFullName");
        const firstName = sessionStorage.getItem("firstName");
        const lastName = sessionStorage.getItem("lastName");
        const documentValue = sessionStorage.getItem("documentValue");

        let displayName = "Admin";

        // Prioridad 1: Nombre completo
        if (fullName && fullName.trim() !== "") {
            displayName = fullName.trim();
        }
        // Prioridad 2: Nombre y apellido por separado
        else if (firstName || lastName) {
            displayName = `${firstName || ""} ${lastName || ""}`.trim() || "Admin";
        }
        // Prioridad 3: Documento
        else if (documentValue) {
            displayName = `Doc. ${documentValue}`;
        }

        this.userDisplayElement.textContent = displayName;
        console.log(`📝 Nombre actualizado: ${displayName}`);
    }

    /**
     * Carga la imagen de perfil del usuario
     */
    async loadProfileImage() {
        if (!this.profileImageElement || !this.currentUserId) {
            return;
        }

        try {
            // Verificar si ya está en cache
            if (this.imageCache.has(this.currentUserId)) {
                console.log('📸 Usando imagen desde cache');
                this.setProfileImage(this.imageCache.get(this.currentUserId));
                return;
            }            // Mostrar loading state
            this.setImageLoading(true);

            // Verificar que ProfileService esté disponible
            if (!this.profileService) {
                console.warn('⚠️ ProfileService no disponible, usando imagen por defecto');
                this.setDefaultProfileImage();
                this.setImageLoading(false);
                return;
            }

            // Obtener imagen del servicio
            const base64Image = await this.profileService.getProfilePhoto(this.currentUserId);

            if (base64Image) {
                const imageUrl = this.profileService.base64ToImageUrl(base64Image);
                
                // Guardar en cache
                this.imageCache.set(this.currentUserId, imageUrl);
                
                // Establecer imagen
                this.setProfileImage(imageUrl);
                console.log('✅ Foto de perfil cargada exitosamente');
            } else {
                // No hay foto, usar avatar por defecto
                this.setDefaultProfileImage();
                console.log('📸 Usando avatar por defecto');
            }

        } catch (error) {
            console.error('❌ Error al cargar imagen de perfil:', error);
            this.setDefaultProfileImage();
        } finally {
            this.setImageLoading(false);
        }
    }

    /**
     * Establece la imagen de perfil
     * @param {string} imageUrl - URL de la imagen
     */
    setProfileImage(imageUrl) {
        if (!this.profileImageElement || !imageUrl) {
            return;
        }

        // Si es un elemento img
        if (this.profileImageElement.tagName === 'IMG') {
            this.profileImageElement.src = imageUrl;
            this.profileImageElement.alt = 'Foto de perfil';
        }
        // Si es un div u otro elemento, usar background-image
        else {
            this.profileImageElement.style.backgroundImage = `url(${imageUrl})`;
            this.profileImageElement.style.backgroundSize = 'cover';
            this.profileImageElement.style.backgroundPosition = 'center';
        }

        // Agregar clases de estilo si no las tiene
        this.profileImageElement.classList.add('profile-loaded');
    }    /**
     * Establece la imagen por defecto
     */
    setDefaultProfileImage() {
        try {
            // Verificar que ProfileService esté disponible
            if (!this.profileService) {
                console.warn('⚠️ ProfileService no disponible, usando URL por defecto hardcoded');
                const fallbackUrl = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1zbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMjAiIGN5PSIyMCIgcj0iMjAiIGZpbGw9IiM2MzczZDAiLz4KPGNpcmNsZSBjeD0iMjAiIGN5PSIxNiIgcj0iNiIgZmlsbD0id2hpdGUiLz4KPHBhdGggZD0iTTEwIDMyYzAtNS41MjMgNC40NzctMTAgMTAtMTBzMTAgNC40NzcgMTAgMTAiIGZpbGw9IndoaXRlIi8+Cjwvc3ZnPgo=';
                this.setProfileImage(fallbackUrl);
                return;
            }

            // Verificar que el método exista
            if (typeof this.profileService.getDefaultAvatarUrl !== 'function') {
                console.warn('⚠️ Método getDefaultAvatarUrl no disponible en ProfileService');
                const fallbackUrl = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1zbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMjAiIGN5PSIyMCIgcj0iMjAiIGZpbGw9IiM2MzczZDAiLz4KPGNpcmNsZSBjeD0iMjAiIGN5PSIxNiIgcj0iNiIgZmlsbD0id2hpdGUiLz4KPHBhdGggZD0iTTEwIDMyYzAtNS41MjMgNC40NzctMTAgMTAtMTBzMTAgNC40NzcgMTAgMTAiIGZpbGw9IndoaXRlIi8+Cjwvc3ZnPgo=';
                this.setProfileImage(fallbackUrl);
                return;
            }

            const defaultUrl = this.profileService.getDefaultAvatarUrl();
            this.setProfileImage(defaultUrl);
        } catch (error) {
            console.error('❌ Error al establecer imagen por defecto:', error);
            // Fallback de emergencia
            const emergencyUrl = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1zbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMjAiIGN5PSIyMCIgcj0iMjAiIGZpbGw9IiM2MzczZDAiLz4KPGNpcmNsZSBjeD0iMjAiIGN5PSIxNiIgcj0iNiIgZmlsbD0id2hpdGUiLz4KPHBhdGggZD0iTTEwIDMyYzAtNS41MjMgNC40NzctMTAgMTAtMTBzMTAgNC40NzcgMTAgMTAiIGZpbGw9IndoaXRlIi8+Cjwvc3ZnPgo=';
            this.setProfileImage(emergencyUrl);
        }
    }

    /**
     * Establece el perfil por defecto completo
     */
    setDefaultProfile() {
        this.setDefaultProfileImage();
        
        if (this.userDisplayElement) {
            this.userDisplayElement.textContent = 'Admin';
        }
        
        console.log('🎭 Perfil por defecto establecido');
    }

    /**
     * Muestra/oculta el estado de carga de la imagen
     * @param {boolean} isLoading - Si está cargando
     */
    setImageLoading(isLoading) {
        if (!this.profileImageElement) {
            return;
        }

        if (isLoading) {
            this.profileImageElement.classList.add('profile-loading');
            // Agregar un spinner si es necesario
        } else {
            this.profileImageElement.classList.remove('profile-loading');
        }
    }

    /**
     * Refresca el perfil completo
     */
    async refreshProfile() {
        console.log('🔄 Refrescando perfil...');
        
        // Limpiar cache
        this.imageCache.clear();
        
        // Recargar perfil
        await this.loadUserProfile();
    }

    /**
     * Limpia los datos del perfil (para logout)
     */
    clearProfile() {
        console.log('🧹 Limpiando perfil...');
        
        // Limpiar cache
        this.imageCache.clear();
        
        // Establecer perfil por defecto
        this.setDefaultProfile();
        
        // Limpiar ID actual
        this.currentUserId = null;
    }

    /**
     * Actualiza solo el nombre del usuario
     */
    updateUserDisplayOnly() {
        this.updateUserName();
    }

    /**
     * Verifica si hay una imagen de perfil cargada
     * @returns {boolean}
     */
    hasProfileImage() {
        return this.imageCache.has(this.currentUserId);
    }

    /**
     * Obtiene la URL de la imagen actual
     * @returns {string|null}
     */
    getCurrentImageUrl() {
        return this.imageCache.get(this.currentUserId) || null;
    }    /**
     * Configura los elementos del modal de perfil
     */
    setupModalElements() {
        console.log('🔧 Configurando elementos del modal de perfil...');
        
        // Elementos del modal
        this.profileModal = document.getElementById('profile-modal');
        this.profileContainer = document.getElementById('profile-container');
        
        // Elementos del modal de perfil
        this.modalName = document.getElementById('profile-modal-name');
        this.modalAvatar = document.getElementById('profile-modal-avatar');
        this.modalFirstName = document.getElementById('profile-modal-firstname');
        this.modalLastName = document.getElementById('profile-modal-lastname');
        this.modalDocType = document.getElementById('profile-modal-doctype');
        this.modalDocument = document.getElementById('profile-modal-document');
        this.modalArea = document.getElementById('profile-modal-area');
        this.modalPosition = document.getElementById('profile-modal-position');
        this.modalEmail = document.getElementById('profile-modal-email');
        this.modalPhone = document.getElementById('profile-modal-phone');
        
        // Elementos para cambio de foto
        this.changePhotoBtn = document.getElementById('change-photo-btn');
        this.photoInput = document.getElementById('photo-input');
        this.avatarLarge = document.querySelector('.avatar-large');
        
        console.log('📋 Elementos encontrados:', {
            profileModal: !!this.profileModal,
            profileContainer: !!this.profileContainer,
            changePhotoBtn: !!this.changePhotoBtn,
            photoInput: !!this.photoInput
        });
        
        if (!this.profileModal) {
            console.warn('⚠️ Modal de perfil no encontrado en el DOM');
            return;
        }
        
        if (!this.profileContainer) {
            console.warn('⚠️ Contenedor de perfil no encontrado en el DOM');
            return;
        }

        // Event listeners para el modal
        this.setupModalEventListeners();
        
        // Event listeners para cambio de foto
        this.setupPhotoChangeListeners();
    }

    /**
     * Configura los event listeners del modal
     */
    setupModalEventListeners() {
        // Abrir modal al hacer clic en el contenedor del perfil
        if (this.profileContainer) {
            this.profileContainer.addEventListener('click', () => {
                this.showProfileModal();
            });
        }

        // Cerrar modal
        const closeButtons = [
            document.getElementById('close-profile-modal'),
            document.getElementById('profile-modal-close')
        ];

        closeButtons.forEach(btn => {
            if (btn) {
                btn.addEventListener('click', () => {
                    this.hideProfileModal();
                });
            }
        });

        // Cerrar modal al hacer clic fuera de él
        if (this.profileModal) {
            this.profileModal.addEventListener('click', (e) => {
                if (e.target === this.profileModal) {
                    this.hideProfileModal();
                }
            });
        }

        // Cerrar modal con Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.profileModal && this.profileModal.style.display === 'block') {
                this.hideProfileModal();
            }
        });
    }

    /**
     * Configura los event listeners para cambio de foto
     */
    setupPhotoChangeListeners() {
        if (this.changePhotoBtn && this.photoInput) {
            // Botón para abrir selector de archivo
            this.changePhotoBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.photoInput.click();
            });

            // Manejar selección de archivo
            this.photoInput.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file) {
                    this.handlePhotoSelection(file);
                }
            });

            console.log('📸 Event listeners para cambio de foto configurados');
        }
    }

    /**
     * Maneja la selección de una nueva foto
     * @param {File} file - Archivo de imagen seleccionado
     */
    async handlePhotoSelection(file) {
        try {
            // Validar el archivo
            if (!this.validateImageFile(file)) {
                return;
            }

            console.log('📸 Procesando nueva foto...', {
                name: file.name,
                size: file.size,
                type: file.type
            });

            // Mostrar estado de carga
            this.setAvatarLoading(true);            // Convertir a base64
            const base64Image = await this.fileToBase64(file);
            console.log('📸 Base64 generado:', {
                length: base64Image.length,
                prefix: base64Image.substring(0, 50),
                hasDataPrefix: base64Image.startsWith('data:'),
                estimatedSizeKB: Math.round(base64Image.length * 0.75 / 1024)            });

            // Redimensionar y convertir a JPEG de calidad reducida (500px max)
            const processedImage = await this.processImageForUpload(base64Image, 500);
            
            // Validar el base64 procesado antes de enviar
            const validation = this.profileService.validateBase64Image(processedImage);
            if (!validation.isValid) {
                throw new Error(`Imagen inválida: ${validation.error}`);
            }
            
            console.log('✅ Validación de imagen procesada exitosa:', validation);            // Subir la imagen procesada
            console.log('📤 Iniciando subida al servidor...');
            const result = await this.profileService.uploadProfilePhoto(this.currentUserId, processedImage);

            if (result.success) {                // Actualizar cache e interfaz
                const imageUrl = this.profileService.base64ToImageUrl(processedImage);
                this.imageCache.set(this.currentUserId, imageUrl);
                
                // Actualizar imagen del modal
                if (this.modalAvatar) {
                    this.modalAvatar.src = imageUrl;
                }
                
                // Actualizar imagen en la barra superior
                this.setProfileImage(imageUrl);
                
                this.showToast('Foto de perfil actualizada correctamente', 'success');
                console.log('✅ Foto actualizada exitosamente');
            } else {
                throw new Error(result.message);
            }

        } catch (error) {
            console.error('❌ Error al cambiar foto:', error);
            this.showToast(`❌ Error: ${error.message}`, 'error');
        } finally {
            this.setAvatarLoading(false);
            // Limpiar input
            if (this.photoInput) {
                this.photoInput.value = '';
            }
        }
    }    /**
     * Valida que el archivo sea una imagen válida
     * @param {File} file - Archivo a validar
     * @returns {boolean}
     */
    validateImageFile(file) {
        console.log('🔍 Validando archivo:', {
            name: file.name,
            size: file.size,
            type: file.type,
            lastModified: new Date(file.lastModified)
        });

        // Validar tipo - aceptar varios formatos que se convertirán a JPEG
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'image/bmp'];
        if (!validTypes.includes(file.type)) {
            this.showToast('❌ Formato no válido. Use JPG, PNG, GIF, WebP o BMP', 'error');
            return false;
        }

        // Validar tamaño (máximo 10MB para permitir conversión)
        const maxSize = 10 * 1024 * 1024; // 10MB
        if (file.size > maxSize) {
            this.showToast('❌ Imagen muy grande. Máximo 10MB', 'error');
            return false;
        }

        // Validar tamaño mínimo (1KB)
        const minSize = 1024; // 1KB
        if (file.size < minSize) {
            this.showToast('❌ Archivo muy pequeño. Mínimo 1KB', 'error');
            return false;
        }

        console.log('✅ Archivo válido - se convertirá a JPEG si es necesario');
        return true;
    }/**
     * Convierte un archivo a base64 en formato JPEG
     * @param {File} file - Archivo a convertir
     * @returns {Promise<string>}
     */
    fileToBase64(file) {
        return new Promise((resolve, reject) => {
            console.log('🔄 Convirtiendo archivo a JPEG base64...');
            
            // Si el archivo ya es JPEG, usar FileReader directo
            if (file.type === 'image/jpeg' || file.type === 'image/jpg') {
                const reader = new FileReader();
                reader.onload = () => {
                    const result = reader.result;
                    console.log('✅ Conversión directa JPEG exitosa:', {
                        originalSize: file.size,
                        base64Size: result.length,
                        format: 'JPEG (directo)'
                    });
                    resolve(result);
                };
                reader.onerror = (error) => {
                    console.error('❌ Error en conversión directa:', error);
                    reject(new Error('Error al procesar la imagen JPEG'));
                };
                reader.readAsDataURL(file);
                return;
            }

            // Para otros formatos, convertir a JPEG usando canvas
            console.log(`🎨 Convirtiendo de ${file.type} a JPEG...`);
            
            const reader = new FileReader();
            reader.onload = () => {
                const img = new Image();
                img.onload = () => {
                    try {
                        // Crear canvas para conversión
                        const canvas = document.createElement('canvas');
                        const ctx = canvas.getContext('2d');
                        
                        // Mantener dimensiones originales (optimización opcional)
                        canvas.width = img.width;
                        canvas.height = img.height;
                        
                        // Fondo blanco para JPEG (elimina transparencia)
                        ctx.fillStyle = 'white';
                        ctx.fillRect(0, 0, canvas.width, canvas.height);
                        
                        // Dibujar imagen
                        ctx.drawImage(img, 0, 0);
                        
                        // Convertir a JPEG con calidad alta
                        const jpegBase64 = canvas.toDataURL('image/jpeg', 0.9);
                        
                        console.log('✅ Conversión a JPEG exitosa:', {
                            originalSize: file.size,
                            originalFormat: file.type,
                            jpegBase64Size: jpegBase64.length,
                            dimensions: `${canvas.width}x${canvas.height}`,
                            quality: '90%'
                        });
                        
                        resolve(jpegBase64);
                    } catch (error) {
                        console.error('❌ Error en conversión con canvas:', error);
                        reject(new Error('Error al convertir imagen a JPEG'));
                    }
                };
                img.onerror = () => {
                    console.error('❌ Error al cargar imagen en elemento img');
                    reject(new Error('Error al procesar la imagen'));
                };
                img.src = reader.result;
            };
            reader.onerror = (error) => {
                console.error('❌ Error en FileReader:', error);
                reject(new Error('Error al leer el archivo'));
            };
            reader.readAsDataURL(file);
        });
    }

    /**
     * Establece el estado de carga en el avatar
     * @param {boolean} loading - Si está cargando o no
     */
    setAvatarLoading(loading) {
        if (this.avatarLarge) {
            if (loading) {
                this.avatarLarge.classList.add('avatar-loading');
            } else {
                this.avatarLarge.classList.remove('avatar-loading');
            }
        }
    }    /**
     * Muestra un mensaje toast usando el mismo diseño que la recuperación de contraseña
     * @param {string} message - Mensaje a mostrar
     * @param {string} type - Tipo: success, error, warning
     */
    showToast(message, type = 'info') {
        // Crear el toast con los mismos estilos que la recuperación de contraseña
        let toast = document.getElementById('profile-notification-toast');
        
        if (!toast) {
            // Crear estructura HTML igual a la de recovery-toast
            toast = document.createElement('div');
            toast.id = 'profile-notification-toast';
            toast.style.cssText = `
                position: fixed;
                bottom: 20px;
                right: 20px;
                z-index: 10000;
                opacity: 0;
                transform: translateX(100%);
                transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                pointer-events: none;
            `;
            
            const toastContent = document.createElement('div');
            toastContent.style.cssText = `
                background: linear-gradient(135deg, #1fb583, #059669);
                color: white;
                padding: 16px 20px;
                border-radius: 8px;
                box-shadow: 0 8px 32px rgba(16, 185, 129, 0.3);
                display: flex;
                align-items: center;
                gap: 12px;
                min-width: 320px;
                max-width: 400px;
                font-family: 'Inter', sans-serif;
                border: 1px solid rgba(255, 255, 255, 0.2);
            `;
            
            const icon = document.createElement('i');
            icon.style.cssText = `
                font-size: 20px;
                color: rgba(255, 255, 255, 0.9);
                flex-shrink: 0;
            `;
            
            const messageSpan = document.createElement('span');
            messageSpan.style.cssText = `
                font-size: 14px;
                font-weight: 500;
                line-height: 1.4;
                letter-spacing: 0.025em;
            `;
            
            toastContent.appendChild(icon);
            toastContent.appendChild(messageSpan);
            toast.appendChild(toastContent);
            document.body.appendChild(toast);
        }
        
        const toastContent = toast.querySelector('div');
        const icon = toast.querySelector('i');
        const messageSpan = toast.querySelector('span');
        
        // Configurar el mensaje
        messageSpan.textContent = message;
        
        // Configurar colores según el tipo
        if (type === 'success') {
            icon.className = 'fas fa-check-circle';
            toastContent.style.background = 'linear-gradient(135deg, #1fb583, #059669)';
            toastContent.style.boxShadow = '0 8px 32px rgba(16, 185, 129, 0.3)';
        } else if (type === 'error') {
            icon.className = 'fas fa-exclamation-circle';
            toastContent.style.background = 'linear-gradient(135deg, #ef4444, #dc2626)';
            toastContent.style.boxShadow = '0 8px 32px rgba(239, 68, 68, 0.3)';
        } else if (type === 'warning') {
            icon.className = 'fas fa-exclamation-triangle';
            toastContent.style.background = 'linear-gradient(135deg, #f59e0b, #d97706)';
            toastContent.style.boxShadow = '0 8px 32px rgba(245, 158, 11, 0.3)';
        }
        
        // Mostrar el toast
        toast.style.opacity = '1';
        toast.style.transform = 'translateX(0)';
        toast.style.pointerEvents = 'auto';
        
        // Ocultar después de 4 segundos
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(100%)';
            toast.style.pointerEvents = 'none';
        }, 4000);
        
        console.log(`📢 Toast mostrado: ${type} - ${message}`);
    }    /**
     * Muestra el modal de perfil con la información del usuario
     */
    async showProfileModal() {
        if (!this.profileModal) {
            console.warn('⚠️ Modal de perfil no encontrado');
            return;
        }

        try {
            console.log('📋 Abriendo modal de perfil...');
            
            // Usar datos de sessionStorage como fuente base
            let userData = this.getUserDataFromSession();
            
            // Intentar obtener datos actualizados de la API
            if (this.currentUserId && this.profileService) {
                try {
                    console.log('🌐 Obteniendo datos del perfil desde API...');
                    const apiData = await this.profileService.getUserProfile(this.currentUserId);
                    
                    if (apiData) {
                        console.log('✅ Datos del perfil obtenidos de la API:', apiData);
                        
                        // Combinar datos de API con sessionStorage (API tiene prioridad)
                        userData = {
                            ...userData,
                            firstName: apiData.firstName || userData.firstName,
                            lastName: apiData.lastName || userData.lastName,
                            documentType: apiData.documentType || userData.documentType,
                            document: apiData.document || userData.documentValue,
                            area: apiData.area || userData.area,
                            position: apiData.position || userData.position,
                            email: apiData.email || userData.email,
                            phone: apiData.phone || userData.phone
                        };
                        
                        // Actualizar sessionStorage con los datos más recientes
                        this.updateSessionStorageWithApiData(apiData);
                        
                    } else {
                        console.log('ℹ️ API no devolvió datos, usando sessionStorage');
                    }
                } catch (error) {
                    console.log('ℹ️ Error al obtener datos de API, usando sessionStorage:', error.message);
                }
            }
              
            // Poblar el modal con los datos
            await this.populateProfileModal(userData);
            
            // Mostrar el modal
            this.profileModal.style.display = 'block';
            
            console.log('✅ Modal de perfil abierto');
            
        } catch (error) {
            console.error('❌ Error al abrir modal de perfil:', error);
            
            // Fallback: usar solo datos de sessionStorage
            const userData = this.getUserDataFromSession();
            this.populateProfileModal(userData);
            this.profileModal.style.display = 'block';
        }
    }

    /**
     * Oculta el modal de perfil
     */
    hideProfileModal() {
        if (this.profileModal) {
            this.profileModal.style.display = 'none';
            console.log('✅ Modal de perfil cerrado');
        }
    }

    /**
     * Obtiene los datos del usuario desde sessionStorage
     */
    getUserDataFromSession() {
        return {
            userId: sessionStorage.getItem("userId"),
            firstName: sessionStorage.getItem("firstName"),
            lastName: sessionStorage.getItem("lastName"),
            fullName: sessionStorage.getItem("userFullName"),
            documentType: sessionStorage.getItem("documentType") || sessionStorage.getItem("typeDocument"),
            documentValue: sessionStorage.getItem("documentValue"),
            email: sessionStorage.getItem("email") || sessionStorage.getItem("userEmail"),
            phone: sessionStorage.getItem("phone") || sessionStorage.getItem("userPhone"),
            area: sessionStorage.getItem("area") || sessionStorage.getItem("userArea"),
            position: sessionStorage.getItem("position") || sessionStorage.getItem("userPosition") || sessionStorage.getItem("userRole"),
            roles: sessionStorage.getItem("userRoles")
        };
    }    /**
     * Puebla el modal con los datos del usuario
     */
    async populateProfileModal(userData) {
        // Nombre completo en el header
        if (this.modalName) {
            const fullName = userData.fullName || 
                           `${userData.firstName || ''} ${userData.lastName || ''}`.trim() ||
                           'Usuario';
            this.modalName.textContent = fullName;
        }

        // Avatar/foto de perfil - cargar desde API igual que en pantalla principal
        if (this.modalAvatar) {
            await this.loadModalProfileImage();
        }

        // Datos personales
        if (this.modalFirstName) {
            this.modalFirstName.textContent = userData.firstName || 'No disponible';
        }
        
        if (this.modalLastName) {
            this.modalLastName.textContent = userData.lastName || 'No disponible';
        }
        
        if (this.modalDocType) {
            const docType = this.getDocumentTypeName(userData.documentType);
            this.modalDocType.textContent = docType;
        }
        
        if (this.modalDocument) {
            // Usar 'document' primero (de la API), luego 'documentValue' (de sessionStorage)
            const documentNumber = userData.document || userData.documentValue;
            this.modalDocument.textContent = documentNumber || 'No disponible';
        }

        // Datos laborales
        if (this.modalArea) {
            this.modalArea.textContent = userData.area || 'No disponible';
        }
        
        if (this.modalPosition) {
            this.modalPosition.textContent = userData.position || 'No disponible';
        }

        // Datos de contacto
        if (this.modalEmail) {
            this.modalEmail.textContent = userData.email || 'No disponible';
        }
        
        if (this.modalPhone) {
            this.modalPhone.textContent = userData.phone || 'No disponible';
        }

        console.log('📋 Modal poblado con datos:', userData);
    }

    /**
     * Convierte el código de tipo de documento a nombre legible
     */
    getDocumentTypeName(typeCode) {
        const documentTypes = {
            'DNI': 'Documento Nacional de Identidad',
            'CE': 'Carné de Extranjería',
            'PASSPORT': 'Pasaporte',
            'RUC': 'Registro Único de Contribuyentes',
            '1': 'DNI',
            '2': 'Carné de Extranjería',
            '3': 'Pasaporte',
            '4': 'RUC'
        };
        
        return documentTypes[typeCode] || typeCode || 'No disponible';
    }

    /**
     * Carga la imagen de perfil específicamente para el modal
     */
    async loadModalProfileImage() {
        if (!this.currentUserId || !this.modalAvatar) {
            console.log('⚠️ No hay userId o elemento de avatar del modal');
            this.setModalDefaultImage();
            return;
        }

        try {
            console.log(`📸 Cargando imagen para el modal...`);

            // Verificar si ya está en cache
            if (this.imageCache.has(this.currentUserId)) {
                console.log('📸 Usando imagen desde cache para el modal');
                this.modalAvatar.src = this.imageCache.get(this.currentUserId);
                return;
            }            // Mostrar loading state en el modal
            this.setModalImageLoading(true);

            // Verificar que ProfileService esté disponible
            if (!this.profileService) {
                console.warn('⚠️ ProfileService no disponible para modal, usando imagen por defecto');
                this.modalAvatar.src = this.getDefaultAvatarUrl();
                this.setModalImageLoading(false);
                return;
            }

            // Obtener imagen del servicio
            const base64Image = await this.profileService.getProfilePhoto(this.currentUserId);

            if (base64Image) {
                const imageUrl = this.profileService.base64ToImageUrl(base64Image);
                
                // Guardar en cache
                this.imageCache.set(this.currentUserId, imageUrl);
                
                // Establecer imagen en el modal
                this.modalAvatar.src = imageUrl;
                console.log('✅ Foto de perfil cargada en el modal exitosamente');
            } else {
                // No hay foto, usar avatar por defecto
                this.setModalDefaultImage();
                console.log('📸 Usando avatar por defecto en el modal');
            }

        } catch (error) {
            console.error('❌ Error al cargar imagen de perfil en el modal:', error);
            this.setModalDefaultImage();
        } finally {
            this.setModalImageLoading(false);
        }
    }

    /**
     * Establece imagen por defecto en el modal
     */
    setModalDefaultImage() {
        if (this.modalAvatar) {
            this.modalAvatar.src = this.profileService.getDefaultAvatarUrl();
            
            // Manejar error de carga de imagen
            this.modalAvatar.onerror = () => {
                this.modalAvatar.onerror = null;
                this.modalAvatar.src = this.profileService.getDefaultAvatarUrl();
            };
        }
    }

    /**
     * Establece el estado de loading para la imagen del modal
     */
    setModalImageLoading(loading) {
        if (this.modalAvatar) {
            if (loading) {
                this.modalAvatar.style.opacity = '0.5';
                console.log('🔄 Cargando imagen del modal...');
            } else {
                this.modalAvatar.style.opacity = '1';
            }
        }
    }

    /**
     * Método de debug para probar formatos de base64
     * Solo para desarrollo - no usar en producción
     */
    async debugImageFormats() {
        if (!this.currentUserId) {
            console.error('❌ No hay usuario logueado para debug');
            return;
        }

        if (!testImage) {
            console.error('❌ No hay imagen de prueba. Usa generateSmallTestImage() primero');
            return;
        }

        console.log('🧪 Iniciando debug de formatos de imagen...');
        
        try {
            const results = await this.profileService.testAllFormats(this.currentUserId, testImage);
            
            console.log('\n📋 RESULTADOS COMPLETOS:', results);
            
            // Buscar el primer formato exitoso
            const workingFormat = results.find(r => r.success);
            if (workingFormat) {
                console.log(`\n🎯 FORMATO RECOMENDADO: ${workingFormat.formatType}`);
                console.log(`   Descripción: ${workingFormat.description}`);
                console.log(`   Status: ${workingFormat.status}`);
                
                // Actualizar el método uploadProfilePhoto para usar el formato correcto
                console.log('\n💡 Considera actualizar uploadProfilePhoto() para usar este formato');
            } else {
                console.log('\n💥 NINGÚN FORMATO FUNCIONÓ - Revisar configuración del servidor');
            }
            
            return results;
            
        } catch (error) {
            console.error('❌ Error durante debug:', error);
            return null;
        }
    }

    /**
     * Genera una imagen pequeña de prueba para debug
     */
    generateTestImage() {
        const canvas = document.createElement('canvas');
        canvas.width = 10;
        canvas.height = 10;
        const ctx = canvas.getContext('2d');
        
        // Crear un pequeño patrón colorido
        ctx.fillStyle = '#FF0000';
        ctx.fillRect(0, 0, 5, 5);
        ctx.fillStyle = '#00FF00';
        ctx.fillRect(5, 0, 5, 5);
        ctx.fillStyle = '#0000FF';
        ctx.fillRect(0, 5, 5, 5);
        ctx.fillStyle = '#FFFF00';
        ctx.fillRect(5, 5, 5, 5);
        
        const testImage = canvas.toDataURL('image/png');
        
        // Hacer la imagen disponible globalmente para debug
        window.testImage = testImage;
        
        console.log('🎯 Imagen de prueba generada:', {
            size: `${canvas.width}x${canvas.height}`,
            format: 'PNG',
            base64Length: testImage.length,
            sizeKB: Math.round(testImage.length * 0.75 / 1024)
        });
        
        return testImage;
    }

    /**
     * Obtiene la URL del avatar por defecto
     * Método helper para casos donde ProfileService no esté disponible
     * @returns {string} - URL del avatar por defecto
     */
    getDefaultAvatarUrl() {
        if (this.profileService && typeof this.profileService.getDefaultAvatarUrl === 'function') {
            return this.profileService.getDefaultAvatarUrl();
        }
        
        // Fallback hardcoded
        return 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1zbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMjAiIGN5PSIyMCIgcj0iMjAiIGZpbGw9IiM2MzczZDAiLz4KPGNpcmNsZSBjeD0iMjAiIGN5PSIxNiIgcj0iNiIgZmlsbD0id2hpdGUiLz4KPHBhdGggZD0iTTEwIDMyYzAtNS41MjMgNC40NzctMTAgMTAtMTBzMTAgNC40NzcgMTAgMTAiIGZpbGw9IndoaXRlIi8+Cjwvc3ZnPgo=';
    }

    /**
     * Procesa una imagen base64 para reducir su tamaño a máximo 500px y convertir a JPEG
     * @param {string} base64Image - Imagen en formato base64
     * @param {number} maxSize - Tamaño máximo en píxeles (default: 500)
     * @returns {Promise<string>} - Imagen procesada en base64
     */
    processImageForUpload(base64Image, maxSize = 500) {
        return new Promise((resolve, reject) => {
            console.log('🔄 Procesando imagen para subida...', {
                originalLength: base64Image.length,
                maxSize: maxSize
            });
            
            const img = new Image();
            img.onload = () => {
                try {
                    // Calcular nuevas dimensiones manteniendo proporción
                    let { width, height } = img;
                    
                    if (width > maxSize || height > maxSize) {
                        const aspectRatio = width / height;
                        
                        if (width > height) {
                            width = maxSize;
                            height = maxSize / aspectRatio;
                        } else {
                            height = maxSize;
                            width = maxSize * aspectRatio;
                        }
                        
                        // Redondear a enteros
                        width = Math.round(width);
                        height = Math.round(height);
                    }
                    
                    // Crear canvas con nuevas dimensiones
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');
                    
                    canvas.width = width;
                    canvas.height = height;
                    
                    // Fondo blanco para JPEG
                    ctx.fillStyle = 'white';
                    ctx.fillRect(0, 0, width, height);
                    
                    // Redimensionar y dibujar imagen
                    ctx.drawImage(img, 0, 0, width, height);
                    
                    // Convertir a JPEG con calidad optimizada para servidor
                    const processedBase64 = canvas.toDataURL('image/jpeg', 0.8);
                    
                    console.log('✅ Imagen procesada exitosamente:', {
                        originalSize: `${img.width}x${img.height}`,
                        newSize: `${width}x${height}`,
                        originalLength: base64Image.length,
                        processedLength: processedBase64.length,
                        compressionRatio: `${((1 - processedBase64.length / base64Image.length) * 100).toFixed(1)}%`,
                        quality: '80%'
                    });
                    
                    resolve(processedBase64);
                } catch (error) {
                    console.error('❌ Error procesando imagen:', error);
                    reject(new Error('Error al procesar imagen para subida'));
                }
            };
            
            img.onerror = () => {
                console.error('❌ Error al cargar imagen base64');
                reject(new Error('Error al cargar imagen para procesar'));
            };
            
            img.src = base64Image;
        });
    }

    /**
     * Actualiza el sessionStorage con los datos obtenidos de la API
     * @param {Object} apiData - Datos del perfil desde la API
     */
    updateSessionStorageWithApiData(apiData) {
        if (!apiData) return;
        
        try {
            // Actualizar solo los campos que vienen de la API y no están vacíos
            if (apiData.firstName) {
                sessionStorage.setItem("firstName", apiData.firstName);
            }
            if (apiData.lastName) {
                sessionStorage.setItem("lastName", apiData.lastName);
            }
            if (apiData.documentType) {
                sessionStorage.setItem("documentType", apiData.documentType);
            }
            if (apiData.document) {
                sessionStorage.setItem("documentValue", apiData.document);
            }
            if (apiData.area) {
                sessionStorage.setItem("area", apiData.area);
            }
            if (apiData.position) {
                sessionStorage.setItem("position", apiData.position);
            }
            if (apiData.email) {
                sessionStorage.setItem("email", apiData.email);
                sessionStorage.setItem("userEmail", apiData.email);
            }
            if (apiData.phone) {
                sessionStorage.setItem("phone", apiData.phone);
                sessionStorage.setItem("userPhone", apiData.phone);
            }
            
            // Actualizar nombre completo si tenemos firstName y lastName
            if (apiData.firstName && apiData.lastName) {
                const fullName = `${apiData.firstName} ${apiData.lastName}`;
                sessionStorage.setItem("userFullName", fullName);
            }
            
            console.log('✅ SessionStorage actualizado con datos de la API');
            
        } catch (error) {
            console.warn('⚠️ Error al actualizar sessionStorage:', error);
        }
    }
    
    /**
     * Refresca los datos del perfil desde la API y actualiza la interfaz
     */
    async refreshProfileFromApi() {
        if (!this.currentUserId || !this.profileService) {
            console.warn('⚠️ No se puede refrescar: sin userId o ProfileService');
            return false;
        }

        try {
            console.log('🔄 Refrescando datos del perfil desde API...');
            
            const apiData = await this.profileService.getUserProfile(this.currentUserId);
            
            if (apiData) {
                // Actualizar sessionStorage con los nuevos datos
                this.updateSessionStorageWithApiData(apiData);
                
                // Actualizar el nombre en la interfaz
                this.updateUserName();
                
                console.log('✅ Perfil refrescado desde API exitosamente');
                return true;
            } else {
                console.log('ℹ️ No se obtuvieron datos de la API');
                return false;
            }
            
        } catch (error) {
            console.error('❌ Error al refrescar perfil desde API:', error);
            return false;
        }
    }
}

// Exportar la clase para que esté disponible en otros archivos
if (typeof module !== "undefined" && module.exports) {
    module.exports = ProfileController;
} else {
    // Para navegadores sin soporte de módulos
    window.ProfileController = ProfileController;
}
