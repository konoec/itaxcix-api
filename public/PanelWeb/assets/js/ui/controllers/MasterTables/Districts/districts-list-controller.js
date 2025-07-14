// Controlador para la lista de distritos
class DistrictsListController {
    constructor() {
        console.log('🔧 Inicializando DistrictsListController...');
        
        this.tableBody = document.getElementById('districtsTableBody');
        console.log('📋 TableBody encontrado:', !!this.tableBody);
        
        if (!this.tableBody) {
            console.error('❌ Error crítico: No se encontró el elemento districtsTableBody');
            return;
        }
        
        // Inicializar directamente sin test
        this.continueInit();
    }
    
    continueInit() {
        console.log('🔧 Continuando inicialización...');
        
        this.stats = {
            total: document.getElementById('totalDistricts'),
            filtered: document.getElementById('filteredResults'),
            page: document.getElementById('currentPage'),
            range: document.getElementById('itemsRange'),
            // Footer elements
            footerTotal: document.getElementById('footerTotalDistricts'),
            footerRange: document.getElementById('footerItemsRange')
        };
        
        // Debug: verificar que los elementos existen
        console.log('🔍 Debug stats elements:', {
            total: !!this.stats.total,
            filtered: !!this.stats.filtered,
            page: !!this.stats.page,
            range: !!this.stats.range,
            footerTotal: !!this.stats.footerTotal,
            footerRange: !!this.stats.footerRange
        });
        
        // Verificar si algún elemento crítico no existe
        if (!this.stats.total) console.warn('⚠️ Elemento totalDistricts no encontrado');
        if (!this.stats.range) console.warn('⚠️ Elemento itemsRange no encontrado');
        if (!this.stats.footerTotal) console.warn('⚠️ Elemento footerTotalDistricts no encontrado');
        if (!this.stats.footerRange) console.warn('⚠️ Elemento footerItemsRange no encontrado');
        
        this.filters = {
            search: document.getElementById('searchInput'),
            name: document.getElementById('nameFilter'),
            province: document.getElementById('provinceFilter'),
            ubigeo: document.getElementById('ubigeoFilter'),
            perPage: document.getElementById('perPageSelect'),
            sortBy: document.getElementById('sortBySelect'),
            sortDirection: document.getElementById('sortOrderSelect')
        };
        
        // Debug: verificar que los filtros existen
        console.log('🔍 Debug filters elements:', {
            search: !!this.filters.search,
            name: !!this.filters.name,
            province: !!this.filters.province,
            ubigeo: !!this.filters.ubigeo,
            perPage: !!this.filters.perPage,
            sortBy: !!this.filters.sortBy,
            sortDirection: !!this.filters.sortDirection
        });
        this.pagination = document.getElementById('paginationContainer');
        this.refreshBtn = document.getElementById('refreshBtn');
        this.clearBtn = document.getElementById('clearFiltersBtn');
        this.initEvents();
        this.loadProvinces();
        this.load();
    }
    initEvents() {
        Object.values(this.filters).forEach(input => {
            if (input) input.addEventListener('change', () => this.load(1));
        });
        if (this.refreshBtn) this.refreshBtn.addEventListener('click', () => this.load());
        if (this.clearBtn) this.clearBtn.addEventListener('click', () => this.clearFilters());
    }
    async load(page = 1) {
        console.log('🔄 Método load() iniciado con página:', page);
        
        const params = {
            page,
            perPage: this.filters.perPage.value,
            search: this.filters.search.value,
            name: this.filters.name.value,
            provinceId: this.filters.province.value,
            ubigeo: this.filters.ubigeo.value,
            sortBy: this.filters.sortBy.value,
            sortDirection: this.filters.sortDirection.value
        };
        
        console.log('📤 Enviando parámetros a la API:', params);
        
        // Verificar que el servicio esté disponible
        if (!window.DistrictsService) {
            console.error('❌ DistrictsService no está disponible');
            this.tableBody.innerHTML = `<tr><td colspan="6" class="text-center text-danger">DistrictsService no está disponible</td></tr>`;
            return;
        }
        
        this.setLoading(true);
        try {
            const res = await DistrictsService.getDistricts(params);
            console.log('📥 Respuesta completa de la API:', res);
            console.log('📊 Data que se pasa a render:', res.data);
            
            // Verificar que tenemos la estructura esperada
            if (res.success && res.data) {
                this.render(res.data);
            } else {
                console.warn('⚠️ Respuesta de API sin datos válidos:', res);
                this.tableBody.innerHTML = `<tr><td colspan="6" class="text-center text-warning">No hay datos disponibles</td></tr>`;
            }
        } catch (err) {
            console.error('❌ Error al cargar distritos:', err);
            this.tableBody.innerHTML = `<tr><td colspan="6" class="text-center text-danger">Error: ${err.message}</td></tr>`;
            
            // Limpiar stats en caso de error
            if (this.stats.total) this.stats.total.textContent = '0';
            if (this.stats.range) this.stats.range.textContent = '0 - 0';
            if (this.stats.footerTotal) this.stats.footerTotal.textContent = '0';
            if (this.stats.footerRange) this.stats.footerRange.textContent = '0 - 0';
        }
        this.setLoading(false);
    }
    render(data) {
        console.log('🎨 Iniciando render con data:', data);
        
        const items = data.items || [];
        const meta = data.meta || {};
        
        console.log('📋 Items encontrados:', items.length);
        console.log('📊 Meta información:', meta);
        
        this.tableBody.innerHTML = items.length ? items.map((d, i) => `
            <tr>
                <td class="text-center">${d.id}</td>
                <td>${d.name}</td>
                <td>${d.province?.name || '--'}</td>
                <td>${d.province?.departmentName || '--'}</td>
                <td class="text-center">${d.ubigeo}</td>
                <td class="text-center">
                    <div class="btn-list flex-nowrap">
                        <button class="btn btn-sm btn-outline-orange" 
                                onclick="window.districtsController.handleEditDistrict(${d.id}, '${this.escapeHtml(d.name)}', ${d.province?.id || 'null'}, '${this.escapeHtml(d.ubigeo)}')" 
                                title="Editar">
                            <i class="fas fa-edit text-orange"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-red" 
                                onclick="window.districtDeleteController.handleDeleteDistrict({
                                    id: ${d.id}, 
                                    name: '${this.escapeHtml(d.name)}', 
                                    ubigeo: '${this.escapeHtml(d.ubigeo)}',
                                    province: {
                                        id: ${d.province?.id || 'null'},
                                        name: '${this.escapeHtml(d.province?.name || 'N/A')}'
                                    }
                                })" 
                                title="Eliminar">
                            <i class="fas fa-trash text-red"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('') : `<tr><td colspan="6" class="text-center text-muted">No hay resultados</td></tr>`;
        
        console.log('🎨 HTML generado para tabla:', this.tableBody.innerHTML.substring(0, 500) + '...');
        
        // Stats - usar datos de meta de la API
        const total = meta.total || 0;
        const currentItems = items.length;
        const currentPage = meta.currentPage || 1;
        const perPage = meta.perPage || 15;
        
        // Debug: mostrar datos recibidos
        console.log('📊 Datos para stats calculados:', {
            total,
            currentItems,
            currentPage,
            perPage,
            meta
        });
        
        // Actualizar elementos de estadísticas
        if (this.stats.total) {
            this.stats.total.textContent = total;
            console.log('✅ Total actualizado:', total);
        }
        if (this.stats.filtered) {
            this.stats.filtered.textContent = currentItems;
            console.log('✅ Filtered actualizado:', currentItems);
        }
        if (this.stats.page) {
            this.stats.page.textContent = currentPage;
            console.log('✅ Page actualizado:', currentPage);
        }
        
        // Calcular rango correctamente
        if (this.stats.range) {
            if (currentItems > 0 && total > 0) {
                const start = ((currentPage - 1) * perPage) + 1;
                const end = Math.min(start + currentItems - 1, total);
                this.stats.range.textContent = `${start} - ${end}`;
                console.log('✅ Range actualizado:', `${start} - ${end}`);
            } else {
                this.stats.range.textContent = '0 - 0';
                console.log('⚠️ Range sin datos: 0 - 0');
            }
        }
        
        // Actualizar elementos del footer también
        if (this.stats.footerTotal) {
            this.stats.footerTotal.textContent = total;
            console.log('✅ Footer total actualizado:', total);
        }
        if (this.stats.footerRange) {
            if (currentItems > 0 && total > 0) {
                const start = ((currentPage - 1) * perPage) + 1;
                const end = Math.min(start + currentItems - 1, total);
                this.stats.footerRange.textContent = `${start} - ${end}`;
                console.log('✅ Footer range actualizado:', `${start} - ${end}`);
            } else {
                this.stats.footerRange.textContent = '0 - 0';
                console.log('⚠️ Footer range sin datos: 0 - 0');
            }
        }
        
        // Paginación
        this.renderPagination(meta);
    }
    renderPagination(meta) {
        if (!meta || !this.pagination) {
            console.log('⚠️ No hay meta datos o contenedor de paginación');
            return;
        }
        
        const { currentPage, lastPage } = meta;
        console.log('📄 Renderizando paginación:', { currentPage, lastPage });
        
        if (!lastPage || lastPage <= 1) {
            this.pagination.innerHTML = '';
            return;
        }
        
        let html = '';
        for (let i = 1; i <= lastPage; i++) {
            html += `<li class="page-item${i === currentPage ? ' active' : ''}">
                        <button class="page-link" onclick="if(window.districtsController && window.districtsController.load) { window.districtsController.load(${i}); } else { console.error('districtsController no disponible'); }">${i}</button>
                     </li>`;
        }
        this.pagination.innerHTML = html;
        console.log('✅ Paginación renderizada con', lastPage, 'páginas');
    }
    setLoading(loading) {
        if (loading) {
            this.tableBody.innerHTML = `<tr><td colspan="6" class="text-center py-4"><div class="spinner-border text-primary"></div></td></tr>`;
        }
    }
    clearFilters() {
        Object.values(this.filters).forEach(input => { if (input) input.value = ''; });
        this.load(1);
    }
    async loadProvinces() {
        // Asume que existe ProvincesService global
        if (window.ProvincesService) {
            try {
                // Simplificar la llamada con parámetros básicos
                const res = await ProvincesService.getProvinces(1, 100, '', 'name', 'ASC');
                const select = this.filters.province;
                select.innerHTML = '<option value="">Todas las provincias</option>';
                
                // Manejar diferentes estructuras de respuesta
                const provinces = res.data?.items || res.data?.data || res.data || [];
                provinces.forEach(p => {
                    const opt = document.createElement('option');
                    opt.value = p.id;
                    opt.textContent = p.name;
                    select.appendChild(opt);
                });
            } catch (error) {
                console.error('❌ Error al cargar provincias:', error);
                // En caso de error, al menos mostrar la opción por defecto
                const select = this.filters.province;
                if (select) {
                    select.innerHTML = '<option value="">Todas las provincias</option>';
                }
            }
        }
    }

    /**
     * Escape HTML para prevenir XSS
     * @param {string} text - Texto a escapar
     * @returns {string} Texto escapado
     */
    escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Maneja la edición de un distrito
     * @param {number} districtId - ID del distrito a editar
     * @param {string} districtName - Nombre del distrito
     * @param {number|null} provinceId - ID de la provincia
     * @param {string} ubigeo - Código UBIGEO
     */
    handleEditDistrict(districtId, districtName, provinceId, ubigeo) {
        console.log(`✏️ Editar distrito:`, { districtId, districtName, provinceId, ubigeo });
        
        // Verificar que el controlador de actualización esté disponible
        if (!window.districtUpdateController) {
            this.showError('El sistema de edición no está disponible');
            console.error('❌ DistrictUpdateController no encontrado');
            return;
        }

        // Debug: mostrar métodos disponibles
        console.log('🔍 Métodos disponibles en districtUpdateController:', Object.getOwnPropertyNames(Object.getPrototypeOf(window.districtUpdateController)));

        // Crear objeto de datos del distrito
        const districtData = {
            id: districtId,
            name: districtName,
            provinceId: provinceId,
            ubigeo: ubigeo
        };

        // Verificar si existe el método específico, si no usar el método original
        if (typeof window.districtUpdateController.openEditModalWithData === 'function') {
            // Usar el método nuevo que recibe datos directamente
            window.districtUpdateController.openEditModalWithData(districtData);
        } else if (typeof window.districtUpdateController.openEditModal === 'function') {
            // Usar el método original pasando los datos como segundo parámetro
            window.districtUpdateController.openEditModal(districtId, districtData);
        } else {
            this.showError('Método de edición no disponible');
            console.error('❌ No se encontró método de edición válido');
        }
    }

    /**
     * Muestra toast de error
     * @param {string} message - Mensaje de error
     */
    showError(message) {
        if (window.GlobalToast) {
            GlobalToast.show(message, 'error');
        } else {
            console.error('❌', message);
            alert(message);
        }
    }
}

// Hacer disponible la clase globalmente
window.DistrictsListController = DistrictsListController;

// Crear instancia global cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔄 DOM cargado, creando DistrictsListController...');
    if (!window.districtsController) {
        window.districtsController = new DistrictsListController();
        console.log('✅ districtsController cargado correctamente');
    }
});
