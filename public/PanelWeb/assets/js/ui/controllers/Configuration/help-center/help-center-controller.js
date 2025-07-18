/**
 * Controlador para gestionar elementos del centro de ayuda
 * Maneja la interfaz de usuario y las operaciones CRUD
 */
class HelpCenterController {
    constructor() {
        this.currentPage = 1;
        this.perPage = 15; // Valor por defecto que coincide con el selector HTML
        this.totalItems = 0;
        this.searchTerm = '';
        this.isLoading = false;
        this.items = [];
        this.initialized = false;
        
        this.initializeElements();
        this.bindEvents();
        
        // NO llamar initialize() aquí para evitar doble inicialización
        console.log('🏗️ HelpCenterController constructor completado');
    }

    /**
     * Inicializa elementos del DOM
     */
    initializeElements() {
        console.log('🔍 Inicializando elementos del DOM...');
        
        // Verificar y limpiar elementos duplicados
        this.removeDuplicateElements();
        
        // Contenedores principales
        this.tableContainer = document.getElementById('help-center-table-container');
        this.paginationContainer = document.getElementById('help-center-pagination');
        this.searchInput = document.getElementById('help-center-search');
        this.perPageSelect = document.getElementById('help-center-per-page');
        this.createBtn = document.getElementById('create-help-center-item-btn');
        this.refreshBtn = document.getElementById('refresh-help-center-btn');
        this.itemsCounter = document.getElementById('help-center-items-count');
        
        // Elementos de paginación específicos
        this.showingStart = document.getElementById('help-center-showing-start');
        this.showingEnd = document.getElementById('help-center-showing-end');
        this.totalRecords = document.getElementById('help-center-total-records');
        this.currentPageInfo = document.getElementById('help-center-current-page-info');
        this.prevPageBtn = document.getElementById('help-center-prev-page-btn');
        this.nextPageBtn = document.getElementById('help-center-next-page-btn');
        
        // Elementos de loading
        this.loadingSpinner = document.getElementById('help-center-loading');
        this.emptyState = document.getElementById('help-center-empty-state');
        
        // Debug: verificar elementos encontrados
        console.log('📦 Elementos encontrados:', {
            tableContainer: !!this.tableContainer,
            paginationContainer: !!this.paginationContainer,
            searchInput: !!this.searchInput,
            perPageSelect: !!this.perPageSelect,
            createBtn: !!this.createBtn,
            refreshBtn: !!this.refreshBtn,
            itemsCounter: !!this.itemsCounter,
            loadingSpinner: !!this.loadingSpinner,
            emptyState: !!this.emptyState,
            showingStart: !!this.showingStart,
            showingEnd: !!this.showingEnd,
            totalRecords: !!this.totalRecords,
            currentPageInfo: !!this.currentPageInfo,
            prevPageBtn: !!this.prevPageBtn,
            nextPageBtn: !!this.nextPageBtn
        });
    }

    /**
     * Elimina elementos duplicados del DOM
     */
    removeDuplicateElements() {
        const ids = [
            'help-center-table-container',
            'help-center-pagination',
            'help-center-search',
            'help-center-per-page',
            'create-help-center-item-btn',
            'refresh-help-center-btn',
            'help-center-items-count',
            'help-center-loading',
            'help-center-empty-state',
            'help-center-showing-start',
            'help-center-showing-end',
            'help-center-total-records',
            'help-center-current-page-info',
            'help-center-prev-page-btn',
            'help-center-next-page-btn'
        ];

        ids.forEach(id => {
            const elements = document.querySelectorAll(`#${id}`);
            if (elements.length > 1) {
                console.log(`⚠️ Encontrados ${elements.length} elementos con ID: ${id}, eliminando duplicados...`);
                // Mantener solo el primer elemento, eliminar los demás
                for (let i = 1; i < elements.length; i++) {
                    elements[i].remove();
                }
            }
        });
    }

    /**
     * Vincula eventos a elementos del DOM
     */
    bindEvents() {
        // Botón crear nuevo elemento
        if (this.createBtn) {
            this.createBtn.addEventListener('click', () => this.showCreateModal());
        }

        // Botón actualizar tabla
        if (this.refreshBtn) {
            this.refreshBtn.addEventListener('click', () => this.refreshData());
        }

        // Búsqueda
        if (this.searchInput) {
            let searchTimeout;
            this.searchInput.addEventListener('input', (e) => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    this.searchTerm = e.target.value.trim();
                    this.currentPage = 1;
                    this.loadHelpCenterItems();
                }, 300);
            });
        }

        // Selector de elementos por página
        if (this.perPageSelect) {
            this.perPageSelect.addEventListener('change', (e) => {
                this.perPage = parseInt(e.target.value);
                this.currentPage = 1; // Resetear a página 1 al cambiar elementos por página
                this.loadHelpCenterItems();
                console.log(`📄 Elementos por página cambiados a: ${this.perPage}`);
            });
        }

        // Botones de paginación
        if (this.prevPageBtn) {
            this.prevPageBtn.addEventListener('click', () => {
                if (this.currentPage > 1) {
                    this.currentPage--;
                    this.loadHelpCenterItems();
                }
            });
        }

        if (this.nextPageBtn) {
            this.nextPageBtn.addEventListener('click', () => {
                if (this.currentPage < this.totalPages) {
                    this.currentPage++;
                    this.loadHelpCenterItems();
                }
            });
        }

        // Eventos de teclado para búsqueda
        if (this.searchInput) {
            this.searchInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    this.searchTerm = e.target.value.trim();
                    this.currentPage = 1;
                    this.loadHelpCenterItems();
                }
            });
        }
    }

    /**
     * Inicializa el controlador
     */
    async initialize() {
        // Evitar inicialización múltiple
        if (this.initialized) {
            console.log('⚠️ HelpCenterController ya fue inicializado, evitando duplicación');
            return;
        }
        this.initialized = true;
        
        console.log('🚀 HelpCenterController: Iniciando initialize()');
        try {
            console.log('🔄 HelpCenterController: Llamando loadHelpCenterItems()');
            await this.loadHelpCenterItems();
            console.log('✅ HelpCenterController: initialize() completado');
        } catch (error) {
            console.error('❌ Error al inicializar HelpCenterController:', error);
            this.showError('Error al cargar elementos del centro de ayuda');
        }
    }

    /**
     * Carga elementos del centro de ayuda
     */
    async loadHelpCenterItems() {
        if (this.isLoading) return;

        try {
            this.setLoading(true);
            
            let response;
            if (this.searchTerm) {
                response = await window.HelpCenterService.searchHelpCenterItems(
                    this.searchTerm, 
                    this.currentPage, 
                    this.perPage
                );
            } else {
                response = await window.HelpCenterService.getHelpCenterItems(
                    this.currentPage, 
                    this.perPage
                );
            }

            this.items = response.data.items || [];
            
            // Usar la información de meta que viene de la API
            if (response.data.meta) {
                this.totalItems = response.data.meta.total || 0;
                this.currentPage = response.data.meta.currentPage || 1;
                this.totalPages = response.data.meta.lastPage || 1;
                this.perPage = response.data.meta.perPage || 10;
            } else {
                this.totalItems = 0;
                this.currentPage = 1;
                this.totalPages = 1;
            }
            
            console.log('📊 Datos de paginación:', {
                currentPage: this.currentPage,
                totalPages: this.totalPages,
                totalItems: this.totalItems,
                perPage: this.perPage,
                itemsLoaded: this.items.length
            });
            
            this.renderTable();
            this.updatePaginationInfo();
            this.updateItemsCounter();
            
        } catch (error) {
            console.error('Error al cargar elementos:', error);
            this.showError('Error al cargar elementos del centro de ayuda');
        } finally {
            this.setLoading(false);
        }
    }

    /**
     * Renderiza la tabla de elementos
     */
    renderTable() {
        if (!this.tableContainer) return;

        if (this.items.length === 0) {
            this.showEmptyState();
            return;
        }

        const tableHtml = `
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th style="width: 80px;">ID</th>
                            <th>Título</th>
                            <th>Subtítulo</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${this.items.map(item => this.renderTableRow(item)).join('')}
                    </tbody>
                </table>
            </div>
        `;

        this.tableContainer.innerHTML = tableHtml;
        this.bindTableEvents();
    }

    /**
     * Renderiza una fila de la tabla
     */
    renderTableRow(item) {
        const statusBadge = item.active 
            ? `<span class="badge bg-lime text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check me-1" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M5 12l5 5l10 -10"/>
                </svg>
                Activo
              </span>`
            : `<span class="badge bg-red text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x me-1" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M18 6l-12 12"/>
                    <path d="M6 6l12 12"/>
                </svg>
                Inactivo
              </span>`;

        return `
            <tr data-item-id="${item.id}">
                <td><span class="text-secondary">#${item.id}</span></td>
                <td>
                    <div class="fw-bold">${this.escapeHtml(item.title)}</div>
                </td>
                <td>
                    <div class="text-muted">${this.escapeHtml(item.subtitle)}</div>
                </td>
                <td>${statusBadge}</td>
                <td>
                    <div class="btn-list flex-nowrap">
                        <button class="btn btn-sm btn-outline-orange edit-item-btn" 
                                data-item-id="${item.id}" 
                                title="Editar">
                            <i class="fas fa-edit text-orange"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-red delete-item-btn" 
                                data-item-id="${item.id}" 
                                title="Eliminar">
                            <i class="fas fa-trash text-red"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }

    /**
     * Vincula eventos de la tabla
     */
    bindTableEvents() {
        // Botones editar
        document.querySelectorAll('.edit-item-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const itemId = parseInt(e.currentTarget.dataset.itemId);
                const itemData = this.items.find(item => item.id === itemId);
                if (itemData) {
                    this.showEditModal(itemData);
                } else {
                    console.error('❌ No se encontraron datos para el elemento:', itemId);
                }
            });
        });

        // Botones eliminar
        document.querySelectorAll('.delete-item-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const itemId = parseInt(e.currentTarget.dataset.itemId);
                this.showDeleteConfirmation(itemId);
            });
        });
    }

    /**
     * Actualiza la información de paginación (estilo empresas)
     */
    updatePaginationInfo() {
        // Calcular rangos
        const start = this.totalItems > 0 ? ((this.currentPage - 1) * this.perPage) + 1 : 0;
        const end = Math.min(this.currentPage * this.perPage, this.totalItems);
        
        // Actualizar información de registros mostrados
        if (this.showingStart && this.showingEnd && this.totalRecords) {
            this.showingStart.textContent = start;
            this.showingEnd.textContent = end;
            this.totalRecords.textContent = this.totalItems;
        }

        // Actualizar página actual
        if (this.currentPageInfo) {
            this.currentPageInfo.textContent = this.currentPage;
        }

        // Actualizar estado de botones
        if (this.prevPageBtn) {
            this.prevPageBtn.disabled = this.currentPage <= 1;
            if (this.currentPage <= 1) {
                this.prevPageBtn.parentElement.classList.add('disabled');
            } else {
                this.prevPageBtn.parentElement.classList.remove('disabled');
            }
        }

        if (this.nextPageBtn) {
            this.nextPageBtn.disabled = this.currentPage >= this.totalPages;
            if (this.currentPage >= this.totalPages) {
                this.nextPageBtn.parentElement.classList.add('disabled');
            } else {
                this.nextPageBtn.parentElement.classList.remove('disabled');
            }
        }

        console.log(`📊 Paginación actualizada: página ${this.currentPage}/${this.totalPages}, elementos ${start}-${end}/${this.totalItems}`);
    }

    /**
     * Genera números de página para la paginación
     */
    /**
     * Actualiza el contador de elementos
     */
    updateItemsCounter() {
        if (!this.itemsCounter) return;

        const start = this.totalItems > 0 ? (this.currentPage - 1) * this.perPage + 1 : 0;
        const end = Math.min(this.currentPage * this.perPage, this.totalItems);

        this.itemsCounter.textContent = `Mostrando ${start}-${end} de ${this.totalItems} elementos`;
    }

    /**
     * Muestra estado de carga
     */
    setLoading(isLoading) {
        console.log(`🔄 setLoading: ${isLoading}`);
        this.isLoading = isLoading;
        
        // Usar SOLO el spinner dedicado del HTML
        if (this.loadingSpinner) {
            this.loadingSpinner.style.display = isLoading ? 'block' : 'none';
            console.log(`📡 Loading spinner ${isLoading ? 'mostrado' : 'oculto'}`);
        }
        
        // Ocultar/mostrar el contenedor de la tabla
        if (this.tableContainer) {
            this.tableContainer.style.display = isLoading ? 'none' : 'block';
        }
        
        // NO crear spinner adicional en tableContainer para evitar duplicados
    }

    /**
     * Muestra estado vacío
     */
    showEmptyState() {
        if (!this.tableContainer) return;

        const emptyMessage = this.searchTerm 
            ? `No se encontraron elementos que coincidan con "${this.searchTerm}"`
            : 'No hay elementos del centro de ayuda registrados';

        this.tableContainer.innerHTML = `
            <div class="text-center py-5 text-muted">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-help-circle" width="48" height="48" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"/>
                    <path d="M12 17l0 .01"/>
                    <path d="M12 13.5a1.5 1.5 0 0 1 1 -1.5a2.6 2.6 0 1 0 -3 -4"/>
                </svg>
                <p class="mt-3">${emptyMessage}</p>
                ${!this.searchTerm ? `
                    <button class="btn btn-primary mt-2" onclick="helpCenterController.showCreateModal()">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M12 5l0 14"/>
                            <path d="M5 12l14 0"/>
                        </svg>
                        Crear primer elemento
                    </button>
                ` : ''}
            </div>
        `;
    }

    /**
     * Muestra modal de creación
     */
    showCreateModal() {
        console.log('📝 Abriendo modal de creación de elemento...');
        
        // Verificar si el controlador de creación está disponible
        if (window.createHelpCenterController) {
            window.createHelpCenterController.open();
        } else {
            console.error('❌ CreateHelpCenterController no está disponible');
            this.showToast('Error: Controlador de creación no disponible', 'error');
        }
    }

    /**
     * Muestra confirmación de eliminación
     * @param {number} itemId - ID del elemento a eliminar
     */
    showDeleteConfirmation(itemId) {
        console.log('🗑️ Delegando eliminación al DeleteHelpCenterController para ID:', itemId);
        
        // Buscar el elemento en la lista local
        const item = this.items.find(item => item.id === itemId);
        if (!item) {
            console.error('❌ No se encontró el elemento para eliminar');
            this.showToast('Error: Elemento no encontrado', 'error');
            return;
        }

        // Verificar si el controlador de eliminación está disponible
        if (window.deleteHelpCenterController) {
            window.deleteHelpCenterController.showDeleteConfirmation(itemId, item);
        } else {
            console.error('❌ DeleteHelpCenterController no está disponible');
            this.showToast('Error: Controlador de eliminación no disponible', 'error');
        }
    }

    /**
     * Muestra el modal de edición de elemento del centro de ayuda
     * @param {Object} itemData - Datos del elemento a editar
     */
    showEditModal(itemData) {
        if (!itemData || !itemData.id) {
            console.error('❌ Datos de elemento inválidos para edición');
            return;
        }

        console.log('✏️ Mostrando modal de edición para elemento:', itemData);

        // Crear modal de edición usando Tabler
        const editModalHtml = `
            <div class="modal fade" id="edit-help-center-modal" tabindex="-1" aria-labelledby="edit-help-center-modal-label" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <form id="edit-help-center-form">
                            <div class="modal-header">
                                <h5 class="modal-title" id="edit-help-center-modal-label">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-edit me-2" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/>
                                        <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/>
                                        <path d="M16 5l3 3"/>
                                    </svg>
                                    Editar Elemento del Centro de Ayuda
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Campo Título -->
                                <div class="mb-3">
                                    <label for="edit-help-center-title" class="form-label">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-heading me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M7 12h10"/>
                                            <path d="M7 4v16"/>
                                            <path d="M17 4v16"/>
                                            <path d="M15 20h4"/>
                                            <path d="M15 4h4"/>
                                            <path d="M5 20h4"/>
                                            <path d="M5 4h4"/>
                                        </svg>
                                        Título <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="edit-help-center-title" name="title" required 
                                           placeholder="Ej: Viajes" maxlength="255">
                                    <div class="invalid-feedback" id="edit-title-error"></div>
                                </div>

                                <!-- Campo Subtítulo -->
                                <div class="mb-3">
                                    <label for="edit-help-center-subtitle" class="form-label">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-text-caption me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M4 15h16"/>
                                            <path d="M4 4h12"/>
                                            <path d="M4 9h16"/>
                                        </svg>
                                        Subtítulo <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="edit-help-center-subtitle" name="subtitle" required 
                                           placeholder="Ej: ¿Cómo solicitar un viaje?" maxlength="255">
                                    <div class="invalid-feedback" id="edit-subtitle-error"></div>
                                </div>

                                <!-- Campo Respuesta -->
                                <div class="mb-3">
                                    <label for="edit-help-center-answer" class="form-label">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-message-circle me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M3 20l1.3 -3.9c-2.324 -3.437 -1.426 -7.872 2.1 -10.374c3.526 -2.501 8.59 -2.296 11.845 .48c3.255 2.777 3.695 7.266 1.029 10.501c-2.666 3.235 -7.615 4.215 -11.574 2.293l-4.7 1"/>
                                        </svg>
                                        Respuesta <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control" id="edit-help-center-answer" name="answer" rows="4" required 
                                              placeholder="Ej: Para solicitar un viaje, abre la aplicación y selecciona tu destino." maxlength="1000"></textarea>
                                    <div class="form-text">Máximo 1000 caracteres</div>
                                    <div class="invalid-feedback" id="edit-answer-error"></div>
                                </div>

                                <!-- Campo Estado Activo -->
                                <div class="mb-3">
                                    <label class="form-label">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-toggle-left me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M8 16a4 4 0 1 1 0 -8a4 4 0 0 1 0 8z"/>
                                            <path d="M4 12h16"/>
                                        </svg>
                                        Estado
                                    </label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="edit-help-center-active" name="active">
                                        <label class="form-check-label" for="edit-help-center-active">
                                            Elemento activo
                                        </label>
                                    </div>
                                    <div class="form-text">Solo los elementos activos son visibles para los usuarios</div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M18 6l-12 12"/>
                                        <path d="M6 6l12 12"/>
                                    </svg>
                                    Cancelar
                                </button>
                                <button type="submit" class="btn btn-primary" id="edit-help-center-submit-btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-device-floppy me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-16a2 2 0 0 1 2 -2"/>
                                        <path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/>
                                        <path d="M14 4l0 4l-6 0l0 -4"/>
                                    </svg>
                                    Actualizar Elemento
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        `;

        // Insertar modal en el contenedor
        const modalContainer = document.getElementById('modal-container');
        if (modalContainer) {
            modalContainer.insertAdjacentHTML('beforeend', editModalHtml);
        } else {
            document.body.insertAdjacentHTML('beforeend', editModalHtml);
        }

        // Poblar formulario con datos existentes
        this.populateEditForm(itemData);

        // Configurar eventos del formulario
        this.setupEditFormEvents(itemData.id);

        // Mostrar modal
        const modal = new bootstrap.Modal(document.getElementById('edit-help-center-modal'));
        modal.show();

        // Limpiar modal al cerrarse
        document.getElementById('edit-help-center-modal').addEventListener('hidden.bs.modal', function () {
            this.remove();
        });
    }

    /**
     * Pobla el formulario de edición con los datos del elemento
     * @param {Object} itemData - Datos del elemento
     */
    populateEditForm(itemData) {
        const titleInput = document.getElementById('edit-help-center-title');
        const subtitleInput = document.getElementById('edit-help-center-subtitle');
        const answerTextarea = document.getElementById('edit-help-center-answer');
        const activeCheckbox = document.getElementById('edit-help-center-active');

        if (titleInput) titleInput.value = itemData.title || '';
        if (subtitleInput) subtitleInput.value = itemData.subtitle || '';
        if (answerTextarea) answerTextarea.value = itemData.answer || '';
        if (activeCheckbox) activeCheckbox.checked = itemData.active === true;

        console.log('📝 Formulario poblado con datos:', itemData);
    }

    /**
     * Configura los eventos del formulario de edición
     * @param {number} itemId - ID del elemento a editar
     */
    setupEditFormEvents(itemId) {
        const form = document.getElementById('edit-help-center-form');
        const submitBtn = document.getElementById('edit-help-center-submit-btn');

        if (form) {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.handleEditSubmit(itemId);
            });
        }

        // Validación en tiempo real
        const titleInput = document.getElementById('edit-help-center-title');
        const subtitleInput = document.getElementById('edit-help-center-subtitle');
        const answerTextarea = document.getElementById('edit-help-center-answer');

        if (titleInput) {
            titleInput.addEventListener('input', () => this.validateEditField('title', titleInput.value));
        }

        if (subtitleInput) {
            subtitleInput.addEventListener('input', () => this.validateEditField('subtitle', subtitleInput.value));
        }

        if (answerTextarea) {
            answerTextarea.addEventListener('input', () => this.validateEditField('answer', answerTextarea.value));
        }
    }

    /**
     * Maneja el envío del formulario de edición
     * @param {number} itemId - ID del elemento a editar
     */
    async handleEditSubmit(itemId) {
        const submitBtn = document.getElementById('edit-help-center-submit-btn');
        const titleInput = document.getElementById('edit-help-center-title');
        const subtitleInput = document.getElementById('edit-help-center-subtitle');
        const answerTextarea = document.getElementById('edit-help-center-answer');
        const activeCheckbox = document.getElementById('edit-help-center-active');

        try {
            // Validar formulario
            const formData = {
                title: titleInput.value.trim(),
                subtitle: subtitleInput.value.trim(),
                answer: answerTextarea.value.trim(),
                active: activeCheckbox.checked
            };

            if (!this.validateEditForm(formData)) {
                return;
            }

            // Mostrar estado de carga
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                    Actualizando...
                `;
            }

            // Enviar actualización
            await window.HelpCenterService.updateHelpCenterItem(itemId, formData);

            // Mostrar éxito
            this.showToast('Elemento actualizado correctamente', 'success');

            // Cerrar modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('edit-help-center-modal'));
            if (modal) {
                modal.hide();
            }

            // Recargar datos
            await this.loadHelpCenterItems();

        } catch (error) {
            console.error('❌ Error al actualizar elemento:', error);
            this.showToast(error.message || 'Error al actualizar elemento', 'error');
        } finally {
            // Restaurar botón
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-device-floppy me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-16a2 2 0 0 1 2 -2"/>
                        <path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/>
                        <path d="M14 4l0 4l-6 0l0 -4"/>
                    </svg>
                    Actualizar Elemento
                `;
            }
        }
    }

    /**
     * Valida el formulario de edición
     * @param {Object} formData - Datos del formulario
     * @returns {boolean} - True si es válido
     */
    validateEditForm(formData) {
        let isValid = true;

        // Validar título
        if (!formData.title || formData.title.length === 0) {
            this.showEditFieldError('title', 'El título es requerido');
            isValid = false;
        } else if (formData.title.length > 255) {
            this.showEditFieldError('title', 'El título no puede exceder 255 caracteres');
            isValid = false;
        } else {
            this.clearEditFieldError('title');
        }

        // Validar subtítulo
        if (!formData.subtitle || formData.subtitle.length === 0) {
            this.showEditFieldError('subtitle', 'El subtítulo es requerido');
            isValid = false;
        } else if (formData.subtitle.length > 255) {
            this.showEditFieldError('subtitle', 'El subtítulo no puede exceder 255 caracteres');
            isValid = false;
        } else {
            this.clearEditFieldError('subtitle');
        }

        // Validar respuesta
        if (!formData.answer || formData.answer.length === 0) {
            this.showEditFieldError('answer', 'La respuesta es requerida');
            isValid = false;
        } else if (formData.answer.length > 1000) {
            this.showEditFieldError('answer', 'La respuesta no puede exceder 1000 caracteres');
            isValid = false;
        } else {
            this.clearEditFieldError('answer');
        }

        return isValid;
    }

    /**
     * Valida un campo específico del formulario de edición
     * @param {string} fieldName - Nombre del campo
     * @param {string} value - Valor del campo
     */
    validateEditField(fieldName, value) {
        switch (fieldName) {
            case 'title':
                if (!value || value.length === 0) {
                    this.showEditFieldError('title', 'El título es requerido');
                } else if (value.length > 255) {
                    this.showEditFieldError('title', 'El título no puede exceder 255 caracteres');
                } else {
                    this.clearEditFieldError('title');
                }
                break;
            case 'subtitle':
                if (!value || value.length === 0) {
                    this.showEditFieldError('subtitle', 'El subtítulo es requerido');
                } else if (value.length > 255) {
                    this.showEditFieldError('subtitle', 'El subtítulo no puede exceder 255 caracteres');
                } else {
                    this.clearEditFieldError('subtitle');
                }
                break;
            case 'answer':
                if (!value || value.length === 0) {
                    this.showEditFieldError('answer', 'La respuesta es requerida');
                } else if (value.length > 1000) {
                    this.showEditFieldError('answer', 'La respuesta no puede exceder 1000 caracteres');
                } else {
                    this.clearEditFieldError('answer');
                }
                break;
        }
    }

    /**
     * Muestra error en un campo del formulario de edición
     * @param {string} fieldName - Nombre del campo
     * @param {string} message - Mensaje de error
     */
    showEditFieldError(fieldName, message) {
        const input = document.getElementById(`edit-help-center-${fieldName}`);
        const errorDiv = document.getElementById(`edit-${fieldName}-error`);

        if (input) {
            input.classList.add('is-invalid');
        }

        if (errorDiv) {
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
        }
    }

    /**
     * Limpia error de un campo del formulario de edición
     * @param {string} fieldName - Nombre del campo
     */
    clearEditFieldError(fieldName) {
        const input = document.getElementById(`edit-help-center-${fieldName}`);
        const errorDiv = document.getElementById(`edit-${fieldName}-error`);

        if (input) {
            input.classList.remove('is-invalid');
        }

        if (errorDiv) {
            errorDiv.textContent = '';
            errorDiv.style.display = 'none';
        }
    }

    /**
     * Muestra un toast de notificación
     * @param {string} message - Mensaje a mostrar
     * @param {string} type - Tipo de mensaje (success, error, info)
     */
    showToast(message, type = 'info') {
        console.log(`🍞 Toast: ${message} (${type})`);
        
        // Usar el sistema de toast global si está disponible
        if (window.showRecoveryToast) {
            window.showRecoveryToast(message, type);
        } else if (window.GlobalToast) {
            window.GlobalToast.show(message, type);
        } else if (window.showToast) {
            window.showToast(message, type);
        } else {
            // Fallback simple
            alert(message);
        }
    }

    /**
     * Escapa HTML para prevenir XSS
     * @param {string} text - Texto a escapar
     * @returns {string} - Texto escapado
     */
    escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Muestra un mensaje de error
     * @param {string} message - Mensaje de error a mostrar
     */
    showError(message) {
        console.error('❌ Error:', message);
        this.showToast(message, 'error');
    }

    /**
     * Refresca los datos
     */
    async refresh() {
        this.currentPage = 1;
        await this.loadHelpCenterItems();
    }

    /**
     * Refresca los datos de la tabla
     */
    async refreshData() {
        console.log('📄 Refrescando datos del centro de ayuda...');
        this.currentPage = 1;
        this.searchTerm = '';
        
        // Limpiar campo de búsqueda si existe
        if (this.searchInput) {
            this.searchInput.value = '';
        }
        
        await this.loadHelpCenterItems();
        this.showToast('Datos actualizados correctamente', 'success');
    }

    // ...existing code...
}

// El servicio HelpCenterService ya está definido en help-center-service.js
// No necesitamos redefinirlo aquí

// Exportar instancia global
window.HelpCenterController = HelpCenterController;
