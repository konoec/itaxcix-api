// Controlador para manejar el perfil del usuario
class ProfileController {
    constructor() {
        this.profileService = window.ProfileService;
        this.profileImageElement = null;
        this.userDisplayElement = null;
        this.currentUserId = null;
        this.imageCache = new Map(); // Cache para las imágenes
        
        this.init();
    }

    /**
     * Inicializa el controlador del perfil
     */
    init() {
        console.log('🎯 Inicializando ProfileController...');
        
        // Buscar elementos del perfil en el DOM
        this.findProfileElements();
        
        // Cargar información del usuario
        this.loadUserProfile();
        
        console.log('✅ ProfileController inicializado');
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
    }

    /**
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

            // Cargar nombre del usuario
            this.updateUserName();

            // Cargar foto de perfil
            await this.loadProfileImage();

        } catch (error) {
            console.error('❌ Error al cargar perfil:', error);
            this.setDefaultProfile();
        }
    }

    /**
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
            }

            // Mostrar loading state
            this.setImageLoading(true);

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
    }

    /**
     * Establece la imagen por defecto
     */
    setDefaultProfileImage() {
        const defaultUrl = this.profileService.getDefaultAvatarUrl();
        this.setProfileImage(defaultUrl);
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
    }
}

// Exportar la clase para que esté disponible en otros archivos
if (typeof module !== "undefined" && module.exports) {
    module.exports = ProfileController;
} else {
    // Para navegadores sin soporte de módulos
    window.ProfileController = ProfileController;
}
