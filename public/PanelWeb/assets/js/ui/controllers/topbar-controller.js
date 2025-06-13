/**
 * Controlador para el Top Bar
 * Maneja la funcionalidad de la barra superior incluyendo:
 * - Título de la página
 * - Botón del sidebar
 * - Información del perfil de usuario
 * - Interacciones con el perfil
 */
class TopBarController {
    constructor() {
        this.topBarElement = null;
        this.titleElement = null;
        this.sidebarToggleButton = null;
        this.profileContainer = null;
        this.profileImage = null;
        this.userDisplay = null;
        this.userRole = null;
        
        // Referencias a otros controladores
        this.sidebarController = null;
        this.profileController = null;
        
        // Estado del top bar
        this.currentTitle = '';
        this.isInitialized = false;
        
        // Inicializar
        this.init();
    }

    /**
     * Inicializa el controlador del top bar
     */
    init() {
        console.log('🎯 Inicializando TopBarController...');
        
        try {
            // Buscar elementos del DOM
            this.findDOMElements();
            
            // Configurar eventos
            this.setupEventListeners();
            
            // Configurar referencias a otros controladores
            this.setupControllerReferences();
            
            this.isInitialized = true;
            console.log('✅ TopBarController inicializado correctamente');
        } catch (error) {
            console.error('❌ Error al inicializar TopBarController:', error);
        }
    }

    /**
     * Busca y almacena referencias a los elementos del DOM
     */
    findDOMElements() {
        this.topBarElement = document.querySelector('.top-bar');
        this.titleElement = document.querySelector('.top-bar h1, .top-bar h3');
        this.sidebarToggleButton = document.getElementById('open-sidebar');
        this.profileContainer = document.getElementById('profile-container');
        this.profileImage = document.getElementById('profile-image');
        this.userDisplay = document.getElementById('user-display');
        this.userRole = document.querySelector('.user-role');

        // Verificar elementos críticos
        if (!this.topBarElement) {
            console.warn('⚠️ No se encontró el elemento .top-bar');
        }
        
        if (!this.sidebarToggleButton) {
            console.warn('⚠️ No se encontró el botón del sidebar #open-sidebar');
        }
        
        if (!this.profileContainer) {
            console.warn('⚠️ No se encontró el contenedor del perfil #profile-container');
        }
    }

    /**
     * Configura los event listeners
     */
    setupEventListeners() {
        // Event listener para el botón del sidebar
        if (this.sidebarToggleButton) {
            this.sidebarToggleButton.addEventListener('click', (e) => {
                e.preventDefault();
                this.handleSidebarToggle();
            });
        }

        // Event listener para el contenedor del perfil
        if (this.profileContainer) {
            this.profileContainer.addEventListener('click', (e) => {
                e.preventDefault();
                this.handleProfileClick();
            });
        }

        // Event listener para cambios de ventana (responsive)
        window.addEventListener('resize', () => {
            this.handleWindowResize();
        });
    }    /**
     * Configura referencias a otros controladores
     */
    setupControllerReferences() {
        console.log('🔗 Configurando referencias a otros controladores...');
        
        // Función para establecer referencias con retry
        const establishReferences = () => {
            // Intentar obtener referencia al sidebar controller
            if (window.sidebarControllerInstance && !this.sidebarController) {
                this.sidebarController = window.sidebarControllerInstance;
                console.log('✅ Referencia al SidebarController establecida');
            }

            // Intentar obtener referencia al profile controller
            if (window.profileControllerInstance && !this.profileController) {
                this.profileController = window.profileControllerInstance;
                console.log('✅ Referencia al ProfileController establecida');
            }
        };

        // Intentar inmediatamente
        establishReferences();

        // Si no se establecieron las referencias, intentar después de un delay
        if (!this.sidebarController || !this.profileController) {
            console.log('🔄 Reintentando establecer referencias después de delay...');
            setTimeout(() => {
                establishReferences();
                
                if (!this.sidebarController) {
                    console.warn('⚠️ SidebarController aún no encontrado después del retry');
                }
                if (!this.profileController) {
                    console.warn('⚠️ ProfileController aún no encontrado después del retry');
                }
            }, 100);
        }
    }

    /**
     * Maneja el clic en el botón del sidebar
     */    handleSidebarToggle() {
        console.log('🔄 Toggle sidebar desde TopBar');
        console.log('🔍 SidebarController instance:', this.sidebarController);
        console.log('🔍 Window sidebarControllerInstance:', window.sidebarControllerInstance);
        
        if (this.sidebarController && typeof this.sidebarController.toggle === 'function') {
            console.log('✅ Llamando a sidebarController.toggle()');
            this.sidebarController.toggle();
        } else if (window.sidebarControllerInstance && typeof window.sidebarControllerInstance.toggle === 'function') {
            console.log('✅ Usando window.sidebarControllerInstance.toggle()');
            window.sidebarControllerInstance.toggle();
        } else {
            console.warn('⚠️ No se puede encontrar sidebarController, usando fallback');
            // Fallback: disparar evento personalizado
            document.dispatchEvent(new CustomEvent('toggleSidebar'));
        }
    }

    /**
     * Maneja el clic en el perfil
     */
    handleProfileClick() {
        console.log('👤 Clic en perfil desde TopBar');
        
        if (this.profileController && typeof this.profileController.showModal === 'function') {
            this.profileController.showModal();
        } else {
            // Fallback: disparar evento personalizado
            document.dispatchEvent(new CustomEvent('showProfile'));
        }
    }

    /**
     * Maneja el redimensionamiento de ventana
     */
    handleWindowResize() {
        // Ajustar el top bar según el tamaño de pantalla
        this.adjustForScreenSize();
    }

    /**
     * Ajusta el top bar según el tamaño de pantalla
     */
    adjustForScreenSize() {
        if (!this.topBarElement) return;

        const screenWidth = window.innerWidth;
        
        if (screenWidth <= 768) {
            // Móvil: ajustes específicos
            this.topBarElement.classList.add('mobile-view');
        } else {
            // Desktop/Tablet: remover clase móvil
            this.topBarElement.classList.remove('mobile-view');
        }
    }

    /**
     * Establece el título de la página
     * @param {string} title - El título a mostrar
     */
    setTitle(title) {
        if (this.titleElement && title) {
            this.titleElement.textContent = title;
            this.currentTitle = title;
            console.log(`📝 Título actualizado: ${title}`);
        }
    }

    /**
     * Obtiene el título actual
     * @returns {string} El título actual
     */
    getTitle() {
        return this.currentTitle;
    }

    /**
     * Actualiza la información del usuario en el perfil
     * @param {Object} userInfo - Información del usuario
     */
    updateUserInfo(userInfo) {
        if (!userInfo) return;

        try {
            // Actualizar nombre del usuario
            if (this.userDisplay && userInfo.name) {
                this.userDisplay.textContent = userInfo.name;
            }

            // Actualizar rol del usuario
            if (this.userRole && userInfo.role) {
                this.userRole.textContent = userInfo.role;
            }

            // Actualizar imagen del perfil
            if (this.profileImage && userInfo.avatar) {
                this.profileImage.style.backgroundImage = `url(${userInfo.avatar})`;
            }

            console.log('👤 Información del usuario actualizada en TopBar');
        } catch (error) {
            console.error('❌ Error al actualizar información del usuario:', error);
        }
    }

    /**
     * Muestra un indicador de carga en el perfil
     */
    showProfileLoading() {
        if (this.profileContainer) {
            this.profileContainer.classList.add('loading');
        }
    }

    /**
     * Oculta el indicador de carga del perfil
     */
    hideProfileLoading() {
        if (this.profileContainer) {
            this.profileContainer.classList.remove('loading');
        }
    }

    /**
     * Establece el estado del botón del sidebar
     * @param {boolean} isActive - Si el sidebar está activo/abierto
     */
    setSidebarButtonState(isActive) {
        if (this.sidebarToggleButton) {
            if (isActive) {
                this.sidebarToggleButton.classList.add('active');
            } else {
                this.sidebarToggleButton.classList.remove('active');
            }
        }
    }

    /**
     * Añade una clase CSS al top bar
     * @param {string} className - Nombre de la clase a añadir
     */
    addClass(className) {
        if (this.topBarElement && className) {
            this.topBarElement.classList.add(className);
        }
    }

    /**
     * Remueve una clase CSS del top bar
     * @param {string} className - Nombre de la clase a remover
     */
    removeClass(className) {
        if (this.topBarElement && className) {
            this.topBarElement.classList.remove(className);
        }
    }

    /**
     * Verifica si el top bar tiene una clase específica
     * @param {string} className - Nombre de la clase a verificar
     * @returns {boolean} True si tiene la clase
     */
    hasClass(className) {
        return this.topBarElement ? this.topBarElement.classList.contains(className) : false;
    }

    /**
     * Destruye el controlador y limpia los event listeners
     */
    destroy() {
        if (this.sidebarToggleButton) {
            this.sidebarToggleButton.removeEventListener('click', this.handleSidebarToggle);
        }

        if (this.profileContainer) {
            this.profileContainer.removeEventListener('click', this.handleProfileClick);
        }

        window.removeEventListener('resize', this.handleWindowResize);

        // Limpiar referencias
        this.topBarElement = null;
        this.titleElement = null;
        this.sidebarToggleButton = null;
        this.profileContainer = null;
        this.profileImage = null;
        this.userDisplay = null;
        this.userRole = null;
        this.sidebarController = null;
        this.profileController = null;

        this.isInitialized = false;
        console.log('🗑️ TopBarController destruido');
    }

    /**
     * Verifica si el controlador está inicializado
     * @returns {boolean} True si está inicializado
     */
    isReady() {
        return this.isInitialized;
    }
}

// Crear instancia global del controlador si no existe
if (typeof window !== 'undefined') {
    window.TopBarController = TopBarController;
      // NOTA: Auto-inicialización comentada para evitar doble inicialización
    // El TopBarController debe ser inicializado por el inicializador específico de cada página
    /*
    // Auto-inicializar si el DOM está listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            if (!window.topBarControllerInstance) {
                window.topBarControllerInstance = new TopBarController();
            }
        });
    } else {
        // El DOM ya está cargado
        if (!window.topBarControllerInstance) {
            window.topBarControllerInstance = new TopBarController();
        }
    }
    */
}
