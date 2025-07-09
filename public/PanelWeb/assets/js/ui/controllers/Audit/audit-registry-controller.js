/**
 * Controlador para el módulo de Registro de Auditoría
 * Maneja la carga, filtrado, paginación y visualización de registros de auditoría
 */
class AuditRegistryController {
    constructor() {
        console.log('🔍 Inicializando AuditRegistryController...');
        
        // Configuración inicial
        this.currentPage = 1;
        this.perPage = 20;
        this.totalPages = 1;
        this.totalItems = 0;
        this.currentData = [];
        this.sortBy = 'date';
        this.sortDirection = 'DESC';
        this.isLoading = false;
        
        // Filtros actuales
        this.filters = {
            affectedTable: '',
            operation: '',
            systemUser: '',
            dateFrom: '',
            dateTo: ''
        };
        
        // Elementos del DOM
        this.initializeElements();
        
        // Event listeners
        this.initializeEventListeners();
        
        // Cargar datos iniciales
        this.loadAuditData();
    }
    
    /**
     * Inicializa las referencias a elementos del DOM
     */
    initializeElements() {
        // Filtros
        this.filterTable = document.getElementById('filter-table');
        this.filterOperation = document.getElementById('filter-operation');
        this.filterUser = document.getElementById('filter-user');
        this.filterDateFrom = document.getElementById('filter-date-from');
        this.filterDateTo = document.getElementById('filter-date-to');
        
        // Botones de filtro
        this.clearFiltersBtn = document.getElementById('clear-filters-btn');
        this.applyFiltersBtn = document.getElementById('apply-filters-btn');
        this.clearFiltersEmptyBtn = document.getElementById('clear-filters-empty');
        
        // Controles de tabla
        this.resultsCount = document.getElementById('results-count');
        this.sortBySelect = document.getElementById('sort-by');
        this.sortDirectionBtn = document.getElementById('sort-direction');
        this.perPageSelect = document.getElementById('per-page');
        
        // Tabla y estados
        this.tableBody = document.getElementById('audit-table-body');
        this.tableLoading = document.getElementById('table-loading');
        this.tableEmpty = document.getElementById('table-empty');
        
        // Paginación
        this.paginationContainer = document.getElementById('pagination-container');
        this.paginationInfo = document.getElementById('pagination-info-text');
        this.paginationPages = document.getElementById('pagination-pages');
        this.firstPageBtn = document.getElementById('first-page-btn');
        this.prevPageBtn = document.getElementById('prev-page-btn');
        this.nextPageBtn = document.getElementById('next-page-btn');
        this.lastPageBtn = document.getElementById('last-page-btn');
        
        // Botones de acción
        this.refreshBtn = document.getElementById('refresh-audit-btn');
        this.downloadBtn = document.getElementById('download-audit-btn');
        
        // Modal de detalles
        this.detailsModal = document.getElementById('audit-details-modal');
        this.closeDetailsBtn = document.getElementById('close-audit-details');
        this.closeDetailBtn = document.getElementById('close-detail-btn');
        
        console.log('✅ Elementos del DOM inicializados');
    }
    
    /**
     * Inicializa los event listeners
     */
    initializeEventListeners() {
        // Filtros
        if (this.clearFiltersBtn) {
            this.clearFiltersBtn.addEventListener('click', () => this.clearFilters());
        }
        
        if (this.applyFiltersBtn) {
            this.applyFiltersBtn.addEventListener('click', () => this.applyFilters());
        }
        
        if (this.clearFiltersEmptyBtn) {
            this.clearFiltersEmptyBtn.addEventListener('click', () => this.clearFilters());
        }
        
        // Enter en inputs de filtro
        [this.filterUser, this.filterDateFrom, this.filterDateTo].forEach(input => {
            if (input) {
                input.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        this.applyFilters();
                    }
                });
            }
        });
        
        // Controles de ordenamiento
        if (this.sortBySelect) {
            this.sortBySelect.addEventListener('change', () => this.updateSort());
        }
        
        if (this.sortDirectionBtn) {
            this.sortDirectionBtn.addEventListener('click', () => this.toggleSortDirection());
        }
        
        // Control de registros por página
        if (this.perPageSelect) {
            this.perPageSelect.addEventListener('change', () => this.updatePerPage());
        }
        
        // Botones de paginación
        if (this.firstPageBtn) {
            this.firstPageBtn.addEventListener('click', (e) => {
                e.preventDefault();
                if (!this.firstPageBtn.classList.contains('disabled')) {
                    this.goToPage(1);
                }
            });
        }
        
        if (this.prevPageBtn) {
            this.prevPageBtn.addEventListener('click', (e) => {
                e.preventDefault();
                if (!this.prevPageBtn.classList.contains('disabled')) {
                    this.goToPage(this.currentPage - 1);
                }
            });
        }
        
        if (this.nextPageBtn) {
            this.nextPageBtn.addEventListener('click', (e) => {
                e.preventDefault();
                if (!this.nextPageBtn.classList.contains('disabled')) {
                    this.goToPage(this.currentPage + 1);
                }
            });
        }
        
        if (this.lastPageBtn) {
            this.lastPageBtn.addEventListener('click', (e) => {
                e.preventDefault();
                if (!this.lastPageBtn.classList.contains('disabled')) {
                    this.goToPage(this.totalPages);
                }
            });
        }
        
        // Botones de acción
        if (this.refreshBtn) {
            this.refreshBtn.addEventListener('click', () => this.refreshData());
        }
        
        if (this.downloadBtn) {
            this.downloadBtn.addEventListener('click', () => this.downloadData());
        }
        
        // Modal de detalles
        if (this.closeDetailsBtn) {
            this.closeDetailsBtn.addEventListener('click', () => this.closeDetailsModal());
        }
        
        if (this.closeDetailBtn) {
            this.closeDetailBtn.addEventListener('click', () => this.closeDetailsModal());
        }
        
        // Cerrar modal al hacer click fuera
        if (this.detailsModal) {
            this.detailsModal.addEventListener('click', (e) => {
                if (e.target === this.detailsModal) {
                    this.closeDetailsModal();
                }
            });
        }
        
        console.log('✅ Event listeners inicializados');
    }
    
    /**
     * Carga los datos de auditoría desde la API
     */
    async loadAuditData() {
        if (this.isLoading) return;
        
        this.showLoading();
        this.isLoading = true;
        
        try {
            // Preparar filtros para la API según la documentación
            const apiFilters = {
                page: this.currentPage,
                limit: this.perPage,
                sortBy: this.sortBy,
                sortDirection: this.sortDirection
            };
            
            // Mapear filtros del UI a los parámetros exactos de la API
            if (this.filters.affectedTable) apiFilters.affectedTable = this.filters.affectedTable;
            if (this.filters.operation) apiFilters.operation = this.filters.operation;
            if (this.filters.systemUser) apiFilters.systemUser = this.filters.systemUser;
            if (this.filters.dateFrom) apiFilters.dateFrom = this.filters.dateFrom;
            if (this.filters.dateTo) apiFilters.dateTo = this.filters.dateTo;
            
            console.log('🔍 Cargando datos de auditoría desde API con filtros:', apiFilters);
            
            // Inicializar servicio de auditoría si no existe
            if (!window.auditService) {
                window.auditService = new AuditService();
            }
            
            // Llamar a la API real
            const response = await window.auditService.getAuditLogs(apiFilters);
            
            // Verificar estructura de respuesta según documentación
            if (response && response.success && response.data) {
                // Adaptar la respuesta de la API al formato esperado por el controlador
                this.currentData = response.data.data || [];
                this.totalItems = response.data.pagination?.total_items || 0;
                this.totalPages = response.data.pagination?.total_pages || 1;
                this.currentPage = response.data.pagination?.current_page || 1;
                
                this.renderTable();
                this.updatePagination();
                this.updateResultsCount();
                
                console.log('✅ Datos de auditoría cargados desde API:', this.currentData.length, 'registros');
                console.log('📊 Paginación:', {
                    currentPage: this.currentPage,
                    totalPages: this.totalPages,
                    totalItems: this.totalItems
                });
            } else {
                console.error('❌ La API no devolvió la estructura esperada:', response);
                this.showApiError('La API no devolvió datos en el formato esperado. Verifique la configuración del servidor.');
            }
            
        } catch (error) {
            console.error('❌ Error al cargar datos de auditoría desde API:', error);
            this.showApiError(`Error de conexión con la API: ${error.message}`);
        } finally {
            this.hideLoading();
            this.isLoading = false;
        }
    }
    
    /**
     * Muestra un error específico de la API y un estado de error en la interfaz
     */
    showApiError(message) {
        // Limpiar datos actuales
        this.currentData = [];
        this.totalItems = 0;
        this.totalPages = 0;
        this.currentPage = 1;
        
        // Mostrar estado de error en lugar de tabla vacía
        this.showErrorState(message);
        
        // Mostrar notificación de error
        this.showError(message);
        
        // Limpiar controles
        this.updatePagination();
        this.updateResultsCount();
    }
    
    /**
     * Renderiza la tabla con los datos actuales
     */
    renderTable() {
        if (!this.tableBody) return;
        
        if (this.currentData.length === 0) {
            this.showEmptyState();
            return;
        }
        
        this.hideEmptyState();
        
        const rows = this.currentData.map(record => this.createTableRow(record)).join('');
        this.tableBody.innerHTML = rows;
    }
    
    /**
     * Crea una fila de la tabla para un registro
     */
    createTableRow(record) {
        // Usar campos exactos de la API según la documentación
        const operation = record.operation || 'N/A';
        const table = record.affectedTable || 'N/A';
        const user = record.systemUser || 'N/A';
        const date = record.date || '';
        
        const operationClass = operation.toLowerCase();
        const formattedDate = this.formatDate(date);
        
        return `
            <tr>
                <td>${record.id}</td>
                <td>${formattedDate}</td>
                <td><span class="table-badge">${table}</span></td>
                <td><span class="operation-badge ${operationClass}">${operation}</span></td>
                <td>${user}</td>
                <td>
                    <button class="table-action-btn" onclick="auditController.showDetails(${record.id})" title="Ver detalles">
                        <i class="fas fa-eye"></i>
                    </button>
                </td>
            </tr>
        `;
    }
    
    /**
     * Formatea una fecha para mostrar
     */
    formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleString('es-ES', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        });
    }
    
    /**
     * Muestra los detalles de un registro en el modal
     */
    async showDetails(recordId) {
        console.log('🔍 Mostrando detalles del registro:', recordId);
        
        try {
            // Inicializar servicio de auditoría si no existe
            if (!window.auditService) {
                window.auditService = new AuditService();
            }
            
            // Buscar primero en los datos actuales
            let record = this.currentData.find(r => r.id === recordId);
            
            // Si no está en los datos actuales, obtener desde la API
            if (!record) {
                console.log('� Obteniendo detalles desde API para ID:', recordId);
                const response = await window.auditService.getAuditLogById(recordId);
                
                if (response && response.data) {
                    record = response.data;
                } else if (response) {
                    // La API puede devolver directamente el objeto sin envoltorio
                    record = response;
                }
            }
            
            if (!record) {
                this.showError('Registro de auditoría no encontrado');
                return;
            }
            
            this.populateDetailsModal(record);
            this.detailsModal.classList.add('show');
            
        } catch (error) {
            console.error('❌ Error al cargar detalles de auditoría:', error);
            
            // Manejar específicamente el error 404
            if (error.message.includes('404') || error.message.includes('no encontrado')) {
                this.showError('Registro de auditoría no encontrado');
            } else {
                this.showError('Error al cargar los detalles del registro: ' + error.message);
            }
        }
    }
    
    /**
     * Puebla el modal de detalles con los datos del registro
     */
    populateDetailsModal(record) {
        // Llenar información básica usando los campos exactos de la API
        document.getElementById('detail-id').textContent = record.id || '-';
        document.getElementById('detail-date').textContent = this.formatDate(record.date) || '-';
        document.getElementById('detail-table').textContent = record.affectedTable || '-';
        document.getElementById('detail-operation').textContent = record.operation || '-';
        document.getElementById('detail-user').textContent = record.systemUser || '-';
        
        // Aplicar clases CSS apropiadas a los badges
        const tableBadge = document.getElementById('detail-table');
        const operationBadge = document.getElementById('detail-operation');
        
        if (tableBadge) {
            tableBadge.className = 'table-badge';
        }
        
        if (operationBadge && record.operation) {
            operationBadge.className = `operation-badge ${record.operation.toLowerCase()}`;
        }
        
        // Manejar secciones de datos - ocultar si están vacías o no tienen datos útiles
        const previousDataSection = document.getElementById('previous-data-section');
        const newDataSection = document.getElementById('new-data-section');
        const changesSection = document.getElementById('changes-section');
        
        const previousData = record.previousData || {};
        const newData = record.newData || {};
        const hasPreviousData = Object.keys(previousData).length > 0;
        const hasNewData = Object.keys(newData).length > 0;
        
        // Ocultar secciones si no hay datos útiles
        if (previousDataSection) {
            if (hasPreviousData) {
                previousDataSection.style.display = 'block';
                const previousDataElement = document.getElementById('previous-data-content');
                if (previousDataElement) {
                    previousDataElement.textContent = JSON.stringify(previousData, null, 2);
                }
            } else {
                previousDataSection.style.display = 'none';
            }
        }
        
        if (newDataSection) {
            if (hasNewData) {
                newDataSection.style.display = 'block';
                const newDataElement = document.getElementById('new-data-content');
                if (newDataElement) {
                    newDataElement.textContent = JSON.stringify(newData, null, 2);
                }
            } else {
                newDataSection.style.display = 'none';
            }
        }
        
        // Ocultar la sección de cambios detectados ya que la API no proporciona datos útiles
        if (changesSection) {
            changesSection.style.display = 'none';
        }
        
        console.log('✅ Modal de detalles poblado con éxito');
    }
    
    /**
     * Genera la lista de cambios detectados
     */
    generateChanges(previousData, newData) {
        const changesContainer = document.getElementById('changes-container');
        const changes = [];
        
        // Detectar cambios
        const allKeys = new Set([...Object.keys(previousData), ...Object.keys(newData)]);
        
        allKeys.forEach(key => {
            const oldValue = previousData[key];
            const newValue = newData[key];
            
            if (oldValue !== newValue) {
                changes.push({
                    field: key,
                    oldValue: oldValue === undefined ? 'N/A' : oldValue,
                    newValue: newValue === undefined ? 'N/A' : newValue
                });
            }
        });
        
        if (changes.length === 0) {
            changesContainer.innerHTML = '<p>No se detectaron cambios específicos.</p>';
            return;
        }
        
        const changesHtml = changes.map(change => `
            <div class="change-item">
                <div class="change-field">${change.field}</div>
                <div class="change-values">
                    <div class="change-value">
                        <div class="change-value-label">Valor Anterior</div>
                        <div class="change-value-content">${change.oldValue}</div>
                    </div>
                    <div class="change-value">
                        <div class="change-value-label">Valor Nuevo</div>
                        <div class="change-value-content">${change.newValue}</div>
                    </div>
                </div>
            </div>
        `).join('');
        
        changesContainer.innerHTML = changesHtml;
    }
    
    /**
     * Cierra el modal de detalles
     */
    closeDetailsModal() {
        if (this.detailsModal) {
            this.detailsModal.classList.remove('show');
        }
    }
    
    /**
     * Aplica los filtros y recarga los datos
     */
    applyFilters() {
        // Obtener valores de filtros
        this.filters.affectedTable = this.filterTable?.value || '';
        this.filters.operation = this.filterOperation?.value || '';
        this.filters.systemUser = this.filterUser?.value || '';
        this.filters.dateFrom = this.filterDateFrom?.value || '';
        this.filters.dateTo = this.filterDateTo?.value || '';
        
        // Resetear a primera página
        this.currentPage = 1;
        
        // Recargar datos
        this.loadAuditData();
        
        console.log('🔍 Filtros aplicados:', this.filters);
    }
    
    /**
     * Limpia todos los filtros
     */
    clearFilters() {
        // Limpiar inputs
        if (this.filterTable) this.filterTable.value = '';
        if (this.filterOperation) this.filterOperation.value = '';
        if (this.filterUser) this.filterUser.value = '';
        if (this.filterDateFrom) this.filterDateFrom.value = '';
        if (this.filterDateTo) this.filterDateTo.value = '';
        
        // Limpiar filtros internos
        this.filters = {
            affectedTable: '',
            operation: '',
            systemUser: '',
            dateFrom: '',
            dateTo: ''
        };
        
        // Resetear página
        this.currentPage = 1;
        
        // Recargar datos
        this.loadAuditData();
        
        console.log('🧹 Filtros limpiados');
    }
    
    /**
     * Actualiza el ordenamiento
     */
    updateSort() {
        this.sortBy = this.sortBySelect?.value || 'date';
        this.currentPage = 1;
        this.loadAuditData();
    }
    
    /**
     * Cambia la dirección de ordenamiento
     */
    toggleSortDirection() {
        this.sortDirection = this.sortDirection === 'ASC' ? 'DESC' : 'ASC';
        
        // Actualizar icono
        const icon = this.sortDirectionBtn?.querySelector('i');
        if (icon) {
            icon.className = this.sortDirection === 'ASC' ? 'fas fa-sort-up' : 'fas fa-sort-down';
        }
        
        this.currentPage = 1;
        this.loadAuditData();
    }
    
    /**
     * Actualiza los registros por página
     */
    updatePerPage() {
        this.perPage = parseInt(this.perPageSelect?.value) || 20;
        this.currentPage = 1;
        this.loadAuditData();
    }
    
    /**
     * Va a una página específica
     */
    goToPage(page) {
        if (page < 1 || page > this.totalPages || page === this.currentPage) return;
        
        this.currentPage = page;
        this.loadAuditData();
    }
    
    /**
     * Actualiza la paginación
     */
    updatePagination() {
        // Actualizar información
        if (this.paginationInfo) {
            this.paginationInfo.textContent = `Página ${this.currentPage} de ${this.totalPages}`;
        }
        
        // Actualizar botones de navegación
        if (this.firstPageBtn) {
            if (this.currentPage === 1) {
                this.firstPageBtn.classList.add('disabled');
                this.firstPageBtn.setAttribute('aria-disabled', 'true');
            } else {
                this.firstPageBtn.classList.remove('disabled');
                this.firstPageBtn.removeAttribute('aria-disabled');
            }
        }
        
        if (this.prevPageBtn) {
            if (this.currentPage === 1) {
                this.prevPageBtn.classList.add('disabled');
                this.prevPageBtn.setAttribute('aria-disabled', 'true');
            } else {
                this.prevPageBtn.classList.remove('disabled');
                this.prevPageBtn.removeAttribute('aria-disabled');
            }
        }
        
        if (this.nextPageBtn) {
            if (this.currentPage === this.totalPages) {
                this.nextPageBtn.classList.add('disabled');
                this.nextPageBtn.setAttribute('aria-disabled', 'true');
            } else {
                this.nextPageBtn.classList.remove('disabled');
                this.nextPageBtn.removeAttribute('aria-disabled');
            }
        }
        
        if (this.lastPageBtn) {
            if (this.currentPage === this.totalPages) {
                this.lastPageBtn.classList.add('disabled');
                this.lastPageBtn.setAttribute('aria-disabled', 'true');
            } else {
                this.lastPageBtn.classList.remove('disabled');
                this.lastPageBtn.removeAttribute('aria-disabled');
            }
        }
        
        // Generar páginas
        this.generatePaginationPages();
    }
    
    /**
     * Genera los botones de páginas
     */
    generatePaginationPages() {
        if (!this.paginationPages) return;
        
        const pages = [];
        const maxPages = 5;
        let start = Math.max(1, this.currentPage - Math.floor(maxPages / 2));
        let end = Math.min(this.totalPages, start + maxPages - 1);
        
        // Ajustar inicio si hay pocas páginas al final
        if (end - start + 1 < maxPages) {
            start = Math.max(1, end - maxPages + 1);
        }
        
        for (let i = start; i <= end; i++) {
            const isActive = i === this.currentPage;
            pages.push(`
                <li class="page-item ${isActive ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}" ${isActive ? 'aria-current="page"' : ''}>
                        ${i}
                    </a>
                </li>
            `);
        }
        
        this.paginationPages.innerHTML = pages.join('');
        
        // Agregar event listeners a los botones de página
        this.paginationPages.querySelectorAll('.page-link').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const page = parseInt(e.target.dataset.page);
                if (page && page !== this.currentPage) {
                    this.goToPage(page);
                }
            });
        });
    }
    
    /**
     * Actualiza el contador de resultados
     */
    updateResultsCount() {
        if (this.resultsCount) {
            const start = (this.currentPage - 1) * this.perPage + 1;
            const end = Math.min(this.currentPage * this.perPage, this.totalItems);
            this.resultsCount.textContent = `Mostrando ${start}-${end} de ${this.totalItems} registros`;
        }
    }
    
    /**
     * Actualiza los datos
     */
    refreshData() {
        console.log('🔄 Actualizando datos de auditoría...');
        
        // Obtener referencias al botón y al ícono
        const refreshBtn = document.getElementById('refresh-audit-btn');
        const refreshIcon = refreshBtn?.querySelector('i.fas.fa-sync-alt');
        
        console.log('🔍 Debug - refreshBtn:', refreshBtn);
        console.log('🔍 Debug - refreshIcon:', refreshIcon);
        
        // Aplicar animación y estado de deshabilitado
        if (refreshIcon) {
            console.log('✅ Agregando clase spinning al ícono');
            refreshIcon.classList.add('spinning');
        } else {
            console.warn('⚠️ No se encontró el ícono de refrescar');
        }
        
        if (refreshBtn) {
            refreshBtn.disabled = true;
            refreshBtn.style.opacity = '0.7';
        } else {
            console.warn('⚠️ No se encontró el botón de refrescar');
        }
        
        // Cargar datos con un pequeño delay para mostrar la animación
        setTimeout(() => {
            this.loadAuditData().finally(() => {
                // Restaurar botón después de completar la carga
                if (refreshIcon) {
                    console.log('✅ Removiendo clase spinning del ícono');
                    refreshIcon.classList.remove('spinning');
                }
                if (refreshBtn) {
                    refreshBtn.disabled = false;
                    refreshBtn.style.opacity = '1';
                }
                
                console.log('✅ Datos de auditoría actualizados');
            });
        }, 300);
    }
    
    /**
     * Descarga los datos de auditoría
     */
    async downloadData() {
        console.log('📥 Iniciando descarga de datos de auditoría...');
        
        try {
            // Mostrar indicador de descarga
            if (this.downloadBtn) {
                this.downloadBtn.disabled = true;
                const originalIcon = this.downloadBtn.querySelector('i');
                if (originalIcon) {
                    originalIcon.className = 'fas fa-spinner fa-spin';
                }
            }

            // Inicializar servicio de auditoría si no existe
            if (!window.auditService) {
                window.auditService = new AuditService();
            }

            // Usar los filtros actuales para la exportación
            const exportFilters = {
                affectedTable: this.filters.affectedTable,
                operation: this.filters.operation,
                systemUser: this.filters.systemUser,
                dateFrom: this.filters.dateFrom,
                dateTo: this.filters.dateTo
            };

            // Llamar al servicio para exportar
            const csvBlob = await window.auditService.exportAuditLogsToCSV(exportFilters);

            // Crear el nombre del archivo con timestamp
            const now = new Date();
            const timestamp = now.toISOString().slice(0, 19).replace(/[:.]/g, '-');
            const filename = `auditoria_${timestamp}.csv`;

            // Crear enlace de descarga y activarlo
            const downloadUrl = window.URL.createObjectURL(csvBlob);
            const downloadLink = document.createElement('a');
            downloadLink.href = downloadUrl;
            downloadLink.download = filename;
            downloadLink.style.display = 'none';
            
            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
            
            // Liberar memoria
            window.URL.revokeObjectURL(downloadUrl);

            // Mostrar mensaje de éxito
            if (window.GlobalToast) {
                window.GlobalToast.show(`Archivo ${filename} descargado exitosamente`, 'success');
            }

            console.log('✅ Descarga completada:', filename);

        } catch (error) {
            console.error('❌ Error al descargar datos de auditoría:', error);
            
            let errorMessage = 'Error al descargar el archivo';
            if (error.message.includes('no encontrado')) {
                errorMessage = 'El servicio de exportación no está disponible';
            } else if (error.message.includes('permisos')) {
                errorMessage = 'No tiene permisos para exportar datos';
            } else if (error.message.includes('autenticación')) {
                errorMessage = 'Sesión expirada. Por favor, inicie sesión nuevamente';
            }

            if (window.GlobalToast) {
                window.GlobalToast.show(errorMessage, 'error');
            } else {
                alert(errorMessage);
            }
        } finally {
            // Restaurar botón
            if (this.downloadBtn) {
                this.downloadBtn.disabled = false;
                const icon = this.downloadBtn.querySelector('i');
                if (icon) {
                    icon.className = 'fas fa-download';
                }
            }
        }
    }
    
    /**
     * Muestra el estado de carga
     */
    showLoading() {
        if (this.tableLoading) this.tableLoading.style.display = 'flex';
        if (this.tableBody) this.tableBody.style.display = 'none';
        this.hideEmptyState();
    }
    
    /**
     * Oculta el estado de carga
     */
    hideLoading() {
        if (this.tableLoading) this.tableLoading.style.display = 'none';
        if (this.tableBody) this.tableBody.style.display = '';
    }
    
    /**
     * Muestra el estado vacío
     */
    showEmptyState() {
        if (this.tableEmpty) this.tableEmpty.style.display = 'flex';
        if (this.tableBody) this.tableBody.style.display = 'none';
        if (this.paginationContainer) this.paginationContainer.style.display = 'none';
    }
    
    /**
     * Oculta el estado vacío
     */
    hideEmptyState() {
        if (this.tableEmpty) this.tableEmpty.style.display = 'none';
        if (this.paginationContainer) this.paginationContainer.style.display = 'flex';
    }
    
    /**
     * Muestra un estado de error específico cuando falla la API
     */
    showErrorState(message) {
        if (this.tableEmpty) {
            // Modificar el contenido del estado vacío para mostrar el error de API
            const emptyIcon = this.tableEmpty.querySelector('.empty-icon i');
            const emptyTitle = this.tableEmpty.querySelector('h3');
            const emptyDescription = this.tableEmpty.querySelector('p');
            const emptyButton = this.tableEmpty.querySelector('button');
            
            if (emptyIcon) emptyIcon.className = 'fas fa-exclamation-triangle';
            if (emptyTitle) emptyTitle.textContent = 'Error de conexión con la API';
            if (emptyDescription) emptyDescription.textContent = message;
            if (emptyButton) {
                emptyButton.textContent = 'Reintentar';
                emptyButton.onclick = () => this.refreshData();
            }
            
            this.tableEmpty.style.display = 'flex';
        }
        
        if (this.tableBody) this.tableBody.style.display = 'none';
        if (this.paginationContainer) this.paginationContainer.style.display = 'none';
    }
    
    /**
     * Muestra un error
     */
    showError(message) {
        console.error('❌', message);
        if (window.GlobalToast) {
            window.GlobalToast.show(message, 'error');
        } else {
            alert(message);
        }
    }
}

// Hacer disponible globalmente
window.AuditRegistryController = AuditRegistryController;
