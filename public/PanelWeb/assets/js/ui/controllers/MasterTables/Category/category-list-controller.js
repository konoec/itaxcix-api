// Controlador para la lista de categorías
class CategoryListController {
  constructor() {
    console.log('🏗️ Inicializando CategoryListController');
    this.tableBody = document.getElementById('categoryTableBody');
    this.stats = {
      total: document.getElementById('totalCategories'),
      filtered: document.getElementById('filteredCategories'),
      page: document.getElementById('currentCategoryPage'),
      range: document.getElementById('categoriesRange'),
      totalFooter: document.getElementById('totalCategoriesFooter'),
      rangeFooter: document.getElementById('categoriesRangeFooter'),
    };
    this.filters = {
      search: document.getElementById('categorySearchInput'),
      name: document.getElementById('categoryNameFilter'),
      active: document.getElementById('categoryActiveFilter'),
      perPage: document.getElementById('categoryPerPageSelect'),
      sortBy: document.getElementById('categorySortBySelect'),
      sortDirection: document.getElementById('categorySortDirectionSelect'),
    };
    this.pagination = document.getElementById('categoryPagination');
    this.refreshBtn = document.getElementById('refreshCategoryBtn');
    this.clearBtn = document.getElementById('clearCategoryFiltersBtn');

    this.initEvents();
    setTimeout(() => this.load(), 100);
  }

  debounce(fn, wait = 300) {
    let t;
    return (...args) => {
      clearTimeout(t);
      t = setTimeout(() => fn.apply(this, args), wait);
    };
  }

  initEvents() {
    const dLoad = this.debounce(() => this.load());
    this.filters.search?.addEventListener('input', dLoad);
    this.filters.name?.addEventListener('input', dLoad);
    ['active','perPage','sortBy','sortDirection'].forEach(id=>{
      this.filters[id]?.addEventListener('change', ()=>this.load());
    });
    this.refreshBtn?.addEventListener('click', ()=>this.load());
    this.clearBtn?.addEventListener('click', ()=>this.clearFilters());
    document.getElementById('clearCategorySearchBtn')
      ?.addEventListener('click', ()=>{ this.filters.search.value=''; this.load(); });
  }

  clearFilters() {
    ['search','name'].forEach(f=>this.filters[f].value='');
    this.filters.active.value='';
    this.filters.perPage.value='15';
    this.filters.sortBy.value='name';
    this.filters.sortDirection.value='ASC';
    this.load();
  }

  async load() {
    try {
      this.showLoading();
      const params = {
        page: this.currentPage||1,
        perPage: this.filters.perPage.value,
        search: this.filters.search.value,
        name: this.filters.name.value,
        sortBy: this.filters.sortBy.value,
        sortDirection: this.filters.sortDirection.value
      };
      if (this.filters.active.value!=='') params.active = (this.filters.active.value==='true');

      const res = await CategoryService.getCategories(params);
      const items = res.data.data||[];
      const pg = res.data.pagination||{};
      this.render(items);
      this.updateStats({ data: items, pagination: pg });
      this.renderPagination(pg);
    } catch(err) {
      console.error(err);
      this.showError(err.message);
      GlobalToast.show('Error al cargar categorías: '+err.message,'error');
    }
  }

  showLoading() {
    this.tableBody.innerHTML = `
      <tr><td colspan="4" class="text-center py-4">
        <div class="spinner-border spinner-border-sm me-2"></div>Cargando categorías...
      </td></tr>`;
  }

  showError(msg) {
    this.tableBody.innerHTML = `
      <tr><td colspan="4" class="text-center py-4 text-danger">
        <i class="fas fa-exclamation-triangle me-1"></i>${msg}
      </td></tr>`;
  }

  render(data) {
    if (!Array.isArray(data)||data.length===0) {
      this.tableBody.innerHTML = `
        <tr><td colspan="4" class="text-center py-4 text-muted">
          <i class="fas fa-search me-1"></i>No se encontraron categorías
        </td></tr>`;
      return;
    }
    this.tableBody.innerHTML = data.map(cat=>`
      <tr>
        <td class="text-center">${cat.id}</td>
        <td>${cat.name}</td>
        <td class="text-center">
          <span class="badge ${cat.active?'bg-success-lt':'bg-danger-lt'}">
            <i class="fas ${cat.active?'fa-check-circle':'fa-times-circle'} me-1"></i>
            ${cat.active?'Activo':'Inactivo'}
          </span>
        </td>
        <td class="text-center">
          <div class="btn-group">
            <button class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></button>
            <button class="btn btn-sm btn-outline-warning"><i class="fas fa-edit"></i></button>
            <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
          </div>
        </td>
      </tr>`).join('');
  }

  updateStats({ data, pagination }) {
    this.stats.total.textContent = pagination.total_items||0;
    this.stats.filtered.textContent = data.length;
    this.stats.page.textContent = pagination.current_page||1;
    const start = ((pagination.current_page||1)-1)*(pagination.per_page||15)+1;
    const end = start + data.length -1;
    const range = `${start}-${end}`;
    this.stats.range.textContent = range;
    this.stats.totalFooter.textContent = pagination.total_items||0;
    this.stats.rangeFooter.textContent = range;
  }

  renderPagination(p) {
    if ((p.total_pages||1)<=1) { this.pagination.innerHTML=''; return; }
    let html = `<li class="page-item ${p.current_page===1?'disabled':''}">
      <a class="page-link" href="#" data-page="${p.current_page-1}">
        <i class="fas fa-chevron-left"></i>Anterior
      </a></li>`;
    const startPage = Math.max(1,p.current_page-2);
    const endPage = Math.min(p.total_pages,p.current_page+2);
    if (startPage>1) {
      html+=`<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>`;
      if (startPage>2) html+=`<li class="page-item disabled"><span class="page-link">…</span></li>`;
    }
    for(let i=startPage;i<=endPage;i++){
      html+=`<li class="page-item ${i===p.current_page?'active':''}">
        <a class="page-link" href="#" data-page="${i}">${i}</a>
      </li>`;
    }
    if (endPage<p.total_pages) {
      if (endPage<p.total_pages-1) html+=`<li class="page-item disabled"><span class="page-link">…</span></li>`;
      html+=`<li class="page-item"><a class="page-link" href="#" data-page="${p.total_pages}">${p.total_pages}</a></li>`;
    }
    html+=`<li class="page-item ${p.current_page===p.total_pages?'disabled':''}">
      <a class="page-link" href="#" data-page="${p.current_page+1}">
        Siguiente<i class="fas fa-chevron-right ms-1"></i>
      </a></li>`;
    this.pagination.innerHTML=html;
    this.pagination.querySelectorAll('a[data-page]').forEach(a=>{
      a.addEventListener('click',e=>{
        e.preventDefault();
        const pg=parseInt(a.dataset.page);
        if(!isNaN(pg)){ this.currentPage=pg; this.load(); }
      });
    });
  }
}

window.CategoryListController = CategoryListController;
