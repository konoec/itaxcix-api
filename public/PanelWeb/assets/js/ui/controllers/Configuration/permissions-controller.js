/**
 * Controlador para la gestión de permisos - Versión simplificada
 * Solo maneja la creación de permisos por ahora
 */
class PermissionsController {
    constructor() {
        this.isInitialized = false;
        
        // Referencias a elementos del DOM
        this.createPermissionBtn = document.getElementById('create-permission');
        this.listPermissionsBtn = document.querySelector('#card-permisos .btn-list'); // Botón "Listar Permisos"
        this.createPermissionModal = document.getElementById('create-permission-modal');
        this.createPermissionForm = document.getElementById('create-permission-form');
        this.closeModalBtn = document.getElementById('close-create-permission');
        this.cancelBtn = document.getElementById('cancel-create-permission');
        
        // Referencias para campos del formulario
        this.permissionNameInput = document.getElementById('permission-name');
        this.permissionActiveInput = document.getElementById('permission-active');
        this.permissionWebInput = document.getElementById('permission-web');
        
        // Paginación
        this.currentPage = 1;
        this.perPage = 10;
        
        // Inicializar
        this.init();
    }

    /**
     * Inicializa el controlador
     */
    async init() {
        console.log('🔐 Inicializando PermissionsController (versión simplificada)...');
        try {
            // Esperar a que los servicios estén disponibles
            await this.waitForServices();
            this.setupEventListeners();
            this.isInitialized = true;
            console.log('✅ PermissionsController inicializado correctamente');
        } catch (error) {
            console.error('❌ Error al inicializar PermissionsController:', error);
        }
    }

    /**
     * Espera a que los servicios necesarios estén disponibles
     */
    async waitForServices() {
        return new Promise((resolve) => {
            const checkServices = () => {
                if (window.ConfigurationService) {
                    console.log('✅ ConfigurationService detectado en PermissionsController');
                    resolve();
                } else {
                    console.log('⏳ Esperando ConfigurationService...');
                    setTimeout(checkServices, 100);
                }
            };
            checkServices();
        });
    }

    /**
     * Configura los event listeners
     */
    setupEventListeners() {
        // Botón para abrir modal de crear permiso
        if (this.createPermissionBtn) {
            this.createPermissionBtn.addEventListener('click', () => {
                this.openCreateModal();
            });
        }

        // Botón para cerrar modal (X)
        if (this.closeModalBtn) {
            this.closeModalBtn.addEventListener('click', () => {
                this.closeCreateModal();
            });
        }

        // Botón cancelar
        if (this.cancelBtn) {
            this.cancelBtn.addEventListener('click', () => {
                this.closeCreateModal();
            });
        }

        // Formulario de crear permiso
        if (this.createPermissionForm) {
            this.createPermissionForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.createPermission();
            });
        }

        // Cerrar modal al hacer clic fuera
        if (this.createPermissionModal) {
            this.createPermissionModal.addEventListener('click', (e) => {
                if (e.target === this.createPermissionModal) {
                    this.closeCreateModal();
                }
            });
        }

        // Botón para listar permisos
        if (this.listPermissionsBtn) {
            this.listPermissionsBtn.addEventListener('click', () => {
                this.openListModal();
            });
        }
    }

    /**
     * Abre el modal para crear permiso
     */
    openCreateModal() {
        if (this.createPermissionModal) {
            this.clearForm();
            this.createPermissionModal.classList.add('show');
            this.createPermissionModal.style.display = 'flex';
            console.log('📂 Modal de crear permiso abierto');
        }
    }

    /**
     * Cierra el modal de crear permiso
     */
    closeCreateModal() {
        if (this.createPermissionModal) {
            this.createPermissionModal.classList.remove('show');
            this.createPermissionModal.style.display = 'none';
            this.clearForm();
            console.log('📂 Modal de crear permiso cerrado');
        }
    }

    /**
     * Abre el modal de listar permisos
     */
    openListModal() {
        console.log('📋 Abriendo modal de lista de permisos...');
        const modal = document.getElementById('list-permissions-modal');
        if (modal) {
            modal.classList.add('show');
            modal.style.display = 'flex';
            
            // Verificar que el servicio esté disponible antes de cargar permisos
            if (window.ConfigurationService) {
                // Cargar permisos automáticamente
                this.listPermissions(1);
            } else {
                console.error('❌ ConfigurationService no está disponible');
                this.showToast('Servicio no disponible. Por favor, recarga la página.', 'error');
            }
            
            // Configurar event listeners del modal si no están configurados
            this.setupListModalEventListeners();
        } else {
            console.error('❌ Modal de lista de permisos no encontrado');
        }
    }

    /**
     * Configura los event listeners del modal de listar permisos
     */
    setupListModalEventListeners() {
        // Botón de cerrar modal (solo configurar una vez)
        const closeBtn = document.getElementById('close-list-permissions');
        if (closeBtn && !closeBtn.hasListenerConfigured) {
            closeBtn.addEventListener('click', () => {
                this.closeListModal();
            });
            closeBtn.hasListenerConfigured = true;
        }
        
        // Cerrar modal al hacer clic fuera (solo configurar una vez)
        const modal = document.getElementById('list-permissions-modal');
        if (modal && !modal.hasListenerConfigured) {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    this.closeListModal();
                }
            });
            modal.hasListenerConfigured = true;
        }
    }

    /**
     * Cierra el modal de listar permisos
     */
    closeListModal() {
        const modal = document.getElementById('list-permissions-modal');
        if (modal) {
            modal.classList.remove('show');
            modal.style.display = 'none';
            console.log('📋 Modal de lista de permisos cerrado');
        }
    }

    /**
     * Limpia el formulario
     */
    clearForm() {
        if (this.permissionNameInput) this.permissionNameInput.value = '';
        if (this.permissionActiveInput) this.permissionActiveInput.checked = true;
        if (this.permissionWebInput) this.permissionWebInput.checked = false;
    }

    /**
     * Crea un nuevo permiso
     */
    async createPermission() {
        try {
            // Obtener datos del formulario
            const permissionData = {
                name: this.permissionNameInput?.value?.trim(),
                active: this.permissionActiveInput?.checked || false,
                web: this.permissionWebInput?.checked || false
            };

            // Validar datos básicos
            if (!permissionData.name) {
                this.showToast('El nombre del permiso es obligatorio', 'error');
                return;
            }

            console.log('📝 Creando permiso:', permissionData);

            // Llamar al servicio de configuración
            const response = await window.ConfigurationService.createPermission(permissionData);

            if (response.success) {
                this.showToast('Permiso creado exitosamente', 'success');
                this.closeCreateModal();
                console.log('✅ Permiso creado:', response);
            } else {
                // Manejar errores de la API
                const errorMessage = response.message || 'Error al crear el permiso';
                this.showToast(errorMessage, 'error');
                console.error('❌ Error al crear permiso:', response);
            }
        } catch (error) {
            console.error('❌ Error de conexión al crear permiso:', error);
            this.showToast('Error de conexión al crear el permiso', 'error');
        }
    }

    /**
     * Lista todos los permisos
     */
    /**
     * Muestra la lista de permisos en la consola (temporal)
     * En el futuro se puede crear un modal o tabla para mostrar los datos
     */
    displayPermissionsList(data) {
        console.group('📋 Lista de Permisos');
        console.log('Metadatos:', data.meta);
        console.table(data.items);
        console.groupEnd();
        
        // Mostrar información resumida en el toast
        const { total, currentPage, lastPage } = data.meta;
        const message = `Página ${currentPage} de ${lastPage} - Total: ${total} permisos`;
        this.showToast(message, 'info');
    }

    /**
     * Renderiza la lista de permisos en la tabla
     */
    renderPermissions(permissions) {
        const permissionsTableBody = document.getElementById('permissions-table-body');
        
        if (permissionsTableBody) {
            // Limpiar tabla
            permissionsTableBody.innerHTML = '';
            
            // Agregar filas por cada permiso
            permissions.forEach(permiso => {
                const row = document.createElement('tr');
                
                row.innerHTML = `
                    <td>${permiso.name}</td>
                    <td>${permiso.active ? 'Sí' : 'No'}</td>
                    <td>${permiso.web ? 'Sí' : 'No'}</td>
                `;
                
                permissionsTableBody.appendChild(row);
            });
        }
    }

    /**
     * Configura la paginación
     */
    setupPagination(totalItems, currentPage) {
        const paginationContainer = document.getElementById('pagination');
        
        if (paginationContainer) {
            // Limpiar paginación
            paginationContainer.innerHTML = '';
            
            // Calcular total de páginas
            const totalPages = Math.ceil(totalItems / this.perPage);
            
            // Crear botones de paginación
            for (let i = 1; i <= totalPages; i++) {
                const button = document.createElement('button');
                button.textContent = i;
                button.className = 'pagination-button';
                
                if (i === currentPage) {
                    button.classList.add('active');
                }
                
                button.addEventListener('click', () => {
                    this.currentPage = i;
                    this.listPermissions(i);
                });
                
                paginationContainer.appendChild(button);
            }
        }
    }

    /**
     * Lista permisos con paginación
     */
    async listPermissions(page = 1) {
        try {
            console.log(`📋 🆕 INICIANDO CARGA DE PERMISOS - página ${page}...`);
            console.log(`📋 Página actual antes: ${this.currentPage}`);
            console.log(`📋 Página solicitada: ${page}`);
            
            // Verificar que el servicio esté disponible
            if (!window.ConfigurationService) {
                throw new Error('ConfigurationService no está disponible. Verifica que el servicio se haya cargado correctamente.');
            }
            
            // Mostrar loading
            const loadingElement = document.getElementById('permissions-list-loading');
            const tableContainer = document.querySelector('.permissions-table-container');
            const emptyState = document.getElementById('permissions-empty-state');
            
            if (loadingElement) loadingElement.style.display = 'block';
            if (tableContainer) tableContainer.style.display = 'none';
            if (emptyState) emptyState.style.display = 'none';
            
            // Obtener permisos del servicio
            console.log('🔄 Llamando a ConfigurationService.getPermissions...');
            const response = await window.ConfigurationService.getPermissions(page, this.perPage);
            
            // Log detallado de la respuesta
            console.log('📝 Respuesta completa de la API:', response);
            
            // Verificar la estructura de la respuesta
            if (!response) {
                throw new Error('No se recibió respuesta del servidor');
            }
            
            if (response.success) {
                console.log('✅ Respuesta exitosa, verificando datos...');
                console.log('📊 response.data:', response.data);
                
                // Verificar que response.data exista
                if (!response.data) {
                    throw new Error('La respuesta no contiene datos (response.data es undefined)');
                }
                
                // Manejar diferentes estructuras de respuesta
                let items, meta;
                
                // Caso 1: Estructura estándar con items y meta
                if (response.data.items && response.data.meta) {
                    items = response.data.items;
                    meta = response.data.meta;
                }
                // Caso 2: response.data es directamente un array
                else if (Array.isArray(response.data)) {
                    items = response.data;
                    meta = {
                        total: response.data.length,
                        currentPage: page,
                        lastPage: 1,
                        perPage: this.perPage
                    };
                }
                // Caso 3: Los datos están directamente en response (sin data wrapper)
                else if (response.items && response.meta) {
                    items = response.items;
                    meta = response.meta;
                }
                // Caso 4: La API retorna "permissions" como objeto con items y meta
                else if (response.data.permissions && response.data.permissions.items) {
                    items = response.data.permissions.items;
                    meta = response.data.permissions.meta || {
                        total: response.data.permissions.items.length,
                        currentPage: page,
                        lastPage: Math.ceil(response.data.permissions.items.length / this.perPage),
                        perPage: this.perPage
                    };
                }
                // Caso 5: La API retorna "permissions" como array directo
                else if (response.data.permissions && Array.isArray(response.data.permissions)) {
                    items = response.data.permissions;
                    meta = response.data.meta || {
                        total: response.data.permissions.length,
                        currentPage: page,
                        lastPage: Math.ceil(response.data.permissions.length / this.perPage),
                        perPage: this.perPage
                    };
                }
                // Caso 6: Intentar extraer propiedades conocidas
                else {
                    console.log('🔍 Estructura de respuesta no reconocida, intentando extraer datos...');
                    console.log('🔍 Propiedades disponibles en response.data:', Object.keys(response.data));
                    
                    // Buscar propiedades que puedan contener los datos
                    const possibleItemsKeys = ['items', 'data', 'permissions', 'results', 'list'];
                    const possibleMetaKeys = ['meta', 'pagination', 'info'];
                    
                    let foundItems = null;
                    let foundMeta = null;
                    
                    for (const key of possibleItemsKeys) {
                        if (response.data[key] && Array.isArray(response.data[key])) {
                            foundItems = response.data[key];
                            break;
                        }
                    }
                    
                    for (const key of possibleMetaKeys) {
                        if (response.data[key]) {
                            foundMeta = response.data[key];
                            break;
                        }
                    }
                    
                    if (foundItems) {
                        items = foundItems;
                        meta = foundMeta || {
                            total: foundItems.length,
                            currentPage: page,
                            lastPage: 1,
                            perPage: this.perPage
                        };
                    } else {
                        throw new Error(`Estructura de respuesta no reconocida. Propiedades disponibles: ${Object.keys(response.data).join(', ')}`);
                    }
                }
                
                console.log(`📋 Items procesados: ${items ? items.length : 0}`);
                console.log('📊 Meta procesada:', meta);
                
                // Ocultar loading
                if (loadingElement) loadingElement.style.display = 'none';
                
                // Actualizar página actual antes de configurar paginación
                this.currentPage = page;
                
                if (items && items.length > 0) {
                    // Mostrar tabla y llenar datos
                    if (tableContainer) tableContainer.style.display = 'block';
                    this.populatePermissionsTable(items);
                    this.updatePaginationInfo(meta);
                    this.setupPaginationControls(meta);
                } else {
                    // Mostrar estado vacío
                    if (emptyState) {
                        emptyState.style.display = 'block';
                        emptyState.innerHTML = `
                            <i class="fas fa-shield-alt"></i>
                            <h3>No hay permisos disponibles</h3>
                            <p>No se encontraron permisos en el sistema.</p>
                        `;
                    }
                }
                
                console.log(`✅ Permisos cargados correctamente: ${items ? items.length : 0} elementos`);
            } else {
                // La API retornó success: false
                const errorMessage = response.message || 'Error desconocido del servidor';
                console.log('❌ La API retornó success: false:', errorMessage);
                throw new Error(errorMessage);
            }
        } catch (error) {
            console.error('❌ Error al cargar permisos:', error);
            
            // Ocultar loading
            const loadingElement = document.getElementById('permissions-list-loading');
            if (loadingElement) loadingElement.style.display = 'none';
            
            // Mostrar estado de error
            const emptyState = document.getElementById('permissions-empty-state');
            if (emptyState) {
                emptyState.style.display = 'block';
                emptyState.innerHTML = `
                    <i class="fas fa-exclamation-triangle"></i>
                    <h3>Error al cargar permisos</h3>
                    <p>${error.message}</p>
                    <small style="color: #666; font-style: italic;">
                        Verifica la conexión y que tengas permisos para acceder a esta funcionalidad.
                    </small>
                `;
            }
            
            this.showToast(`Error: ${error.message}`, 'error');
        }
    }

    /**
     * Llena la tabla de permisos con los datos
     */
    populatePermissionsTable(permissions) {
        const tableBody = document.getElementById('permissions-table-body');
        
        if (tableBody) {
            tableBody.innerHTML = '';
            
            permissions.forEach(permission => {
                const row = document.createElement('tr');
                
                row.innerHTML = `
                    <td>${permission.id}</td>
                    <td>${permission.name}</td>
                    <td>
                        <span class="status-badge ${permission.active ? 'active' : 'inactive'}">
                            ${permission.active ? 'Activo' : 'Inactivo'}
                        </span>
                    </td>
                    <td>
                        <span class="type-badge ${permission.web ? 'web' : 'mobile'}">
                            ${permission.web ? 'Web' : 'Móvil'}
                        </span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn-action edit" title="Editar" onclick="editPermission(${permission.id})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn-action delete" title="Eliminar" onclick="deletePermission(${permission.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                `;
                
                tableBody.appendChild(row);
            });
        }
    }

    /**
     * Actualiza la información de paginación
     */
    updatePaginationInfo(meta) {
        const totalInfo = document.getElementById('permissions-total-info');
        const pageInfo = document.getElementById('permissions-page-info');
        
        console.log(`📊 Actualizando info de paginación - Página: ${this.currentPage}, Total: ${meta.total}, Última: ${meta.lastPage}`);
        
        if (totalInfo) {
            totalInfo.textContent = `Total: ${meta.total} permisos`;
        }
        
        if (pageInfo) {
            pageInfo.textContent = `Página ${this.currentPage} de ${meta.lastPage}`;
        }
    }

    /**
     * Configura los controles de paginación
     */
    setupPaginationControls(meta) {
        const prevBtn = document.getElementById('permissions-prev-page');
        const nextBtn = document.getElementById('permissions-next-page');
        
        console.log(`🔧 Configurando paginación - Página actual: ${this.currentPage}, Última página: ${meta.lastPage}`);
        console.log(`🔧 Botones encontrados - Anterior: ${!!prevBtn}, Siguiente: ${!!nextBtn}`);
        
        if (prevBtn) {
            // Configurar estado del botón anterior
            prevBtn.disabled = this.currentPage <= 1;
            
            // Remover listeners anteriores y agregar nuevo
            const newPrevBtn = prevBtn.cloneNode(true);
            prevBtn.parentNode.replaceChild(newPrevBtn, prevBtn);
            
            newPrevBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                console.log(`📖 Click en botón anterior - Página actual: ${this.currentPage}`);
                if (this.currentPage > 1) {
                    console.log(`📖 Navegando a página anterior: ${this.currentPage - 1}`);
                    this.listPermissions(this.currentPage - 1);
                } else {
                    console.log('⚠️ Ya estás en la primera página');
                }
            });
            
            console.log(`🔧 Botón anterior configurado - Deshabilitado: ${newPrevBtn.disabled}`);
        }
        
        if (nextBtn) {
            // Configurar estado del botón siguiente
            nextBtn.disabled = this.currentPage >= meta.lastPage;
            
            // Remover listeners anteriores y agregar nuevo
            const newNextBtn = nextBtn.cloneNode(true);
            nextBtn.parentNode.replaceChild(newNextBtn, nextBtn);
            
            newNextBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                console.log(`📖 Click en botón siguiente - Página actual: ${this.currentPage}, Última: ${meta.lastPage}`);
                if (this.currentPage < meta.lastPage) {
                    console.log(`📖 Navegando a página siguiente: ${this.currentPage + 1}`);
                    this.listPermissions(this.currentPage + 1);
                } else {
                    console.log('⚠️ Ya estás en la última página');
                }
            });
            
            console.log(`🔧 Botón siguiente configurado - Deshabilitado: ${newNextBtn.disabled}`);
        }
        
        console.log(`✅ Paginación configurada correctamente`);
    }

    /**
     * Muestra una notificación toast
     */
    showToast(message, type = 'info') {
        const toast = document.getElementById('toast');
        const toastMessage = document.getElementById('toast-message');
        
        if (toast && toastMessage) {
            // Configurar el mensaje
            toastMessage.textContent = message;
            
            // Remover clases de tipo anteriores
            toast.classList.remove('success', 'error', 'info', 'warning');
            
            // Agregar la clase del tipo correspondiente
            toast.classList.add(type);
            
            // Mostrar el toast
            toast.classList.add('show');
            
            // Ocultar después de 3 segundos
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }
    }
}

// Exportar para uso global
window.PermissionsController = PermissionsController;

// Auto-inicialización cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    // Verificar si estamos en la página de configuración
    const createPermissionBtn = document.getElementById('create-permission');
    
    if (createPermissionBtn && !window.permissionsController) {
        window.permissionsController = new PermissionsController();
        console.log('🔐 PermissionsController auto-inicializado');
    }
});
