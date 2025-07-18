// Controlador para el modal de creación de provincia
class CreateProvinceModalController {
    constructor(modalId) {
        this.modalId = modalId;
        this.modal = document.getElementById(modalId);
        this.form = this.modal.querySelector('form');
        this.nameInput = this.form.querySelector('#provinceName');
        this.departmentSelect = this.form.querySelector('#provinceDepartment');
        this.ubigeoInput = this.form.querySelector('#provinceUbigeo');
        this.saveBtn = this.form.querySelector('#saveProvinceBtn');
        this.closeBtns = this.modal.querySelectorAll('[data-bs-dismiss="modal"]');
        this.toast = document.getElementById('recovery-toast');
        this.toastMsg = document.getElementById('recovery-toast-message');
        this.loading = this.form.querySelector('.modal-loading');
        this.initEvents();
    }

    initEvents() {
        this.form.addEventListener('submit', e => {
            e.preventDefault();
            this.createProvince();
        });
        this.closeBtns.forEach(btn => btn.addEventListener('click', () => this.resetForm()));
    }

    async createProvince() {
        this.setLoading(true);
        const name = this.nameInput.value.trim();
        const departmentId = parseInt(this.departmentSelect.value);
        const ubigeo = this.ubigeoInput.value.trim();
        if (!name || !departmentId || !ubigeo) {
            this.showToast('Completa todos los campos obligatorios', true);
            this.setLoading(false);
            return;
        }
        try {
            const result = await CreateProvinceService.createProvince({ name, departmentId, ubigeo });
            this.showToast('Provincia creada exitosamente');
            this.resetForm();
            // Cerrar modal
            const modal = bootstrap.Modal.getOrCreateInstance(this.modal);
            modal.hide();
            // Recargar listado
            if (window.ProvincesListController) window.ProvincesListController.reload();
        } catch (err) {
            this.showToast(err.message || 'Error al crear provincia', true);
        }
        this.setLoading(false);
    }

    setLoading(isLoading) {
        this.saveBtn.disabled = isLoading;
        this.loading.style.display = isLoading ? 'inline-block' : 'none';
    }

    showToast(msg, error = false) {
        this.toastMsg.textContent = msg;
        this.toast.classList.remove('error-toast');
        if (error) this.toast.classList.add('error-toast');
        this.toast.classList.add('show');
        setTimeout(() => this.toast.classList.remove('show'), 3000);
    }

    resetForm() {
        this.form.reset();
        this.setLoading(false);
    }

    // Llenar departamentos dinámicamente
    async fillDepartments(departments) {
        this.departmentSelect.innerHTML = '<option value="">Selecciona un departamento</option>';
        departments.forEach(dep => {
            const opt = document.createElement('option');
            opt.value = dep.id;
            opt.textContent = dep.name;
            this.departmentSelect.appendChild(opt);
        });
    }
}

    // Cargar el modal HTML y preparar el controlador
    (async function() {
      console.log('🏗️ Inicializando modal de crear provincia...');
      
      // Configurar el botón para abrir el modal
      const btn = document.getElementById('createProvinceBtn');
      if (btn) {
        btn.setAttribute('data-bs-toggle', 'modal');
        btn.setAttribute('data-bs-target', '#createProvinceModal');
        console.log('✅ Botón de crear provincia configurado');
      }

      // Cargar departamentos cuando se abra el modal
      const modal = document.getElementById('createProvinceModal');
      if (modal) {
        modal.addEventListener('shown.bs.modal', async function() {
          console.log('📂 Modal abierto, cargando departamentos...');
          await loadDepartments();
        });
      }
    })();

    // Función para cargar departamentos
    async function loadDepartments() {
      const departmentSelect = document.getElementById('provinceDepartment');
      if (!departmentSelect) {
        console.error('❌ Select de departamentos no encontrado');
        return;
      }

      try {
        // Mostrar loading
        departmentSelect.innerHTML = '<option value="">Cargando departamentos...</option>';
        departmentSelect.disabled = true;

        console.log('🔄 Obteniendo departamentos de la API...');
        
        // Llamar al servicio de departamentos
        const response = await DepartmentsService.getDepartments(1, 100, '', 'name', 'ASC');
        console.log('📥 Respuesta de departamentos:', response);

        if (response.success && response.data) {
          const departments = response.data.data || response.data.items || response.data || [];
          console.log('📋 Departamentos obtenidos:', departments.length);

          // Limpiar y llenar el select
          departmentSelect.innerHTML = '<option value="">Seleccione departamento...</option>';
          
          departments.forEach(dept => {
            const option = document.createElement('option');
            option.value = dept.id;
            option.textContent = dept.name;
            departmentSelect.appendChild(option);
          });

          departmentSelect.disabled = false;
          console.log('✅ Departamentos cargados exitosamente');
        } else {
          throw new Error('Respuesta de API inválida');
        }
      } catch (error) {
        console.error('❌ Error al cargar departamentos:', error);
        departmentSelect.innerHTML = '<option value="">Error al cargar departamentos</option>';
        departmentSelect.disabled = false;
        
        // Mostrar mensaje de error al usuario
        if (window.GlobalToast) {
          GlobalToast.show('Error al cargar departamentos', 'error');
        }
      }
    }

    // Funcionalidad de Vista Previa para el Modal
    document.addEventListener('DOMContentLoaded', function() {
        const provinceNameInput = document.getElementById('provinceName');
        const provinceDepartmentSelect = document.getElementById('provinceDepartment');
        const provinceUbigeoInput = document.getElementById('provinceUbigeo');
        
        const previewName = document.getElementById('previewProvinceName');
        const previewDepartment = document.getElementById('previewDepartment');
        const previewUbigeo = document.getElementById('previewProvinceUbigeo');

        // Manejar envío del formulario
        const form = document.getElementById('createProvinceForm');
        const saveBtn = document.getElementById('saveProvinceBtn');
        
        if (form) {
            form.addEventListener('submit', async function(e) {
                e.preventDefault(); // Prevenir recarga de página
                console.log('📝 Enviando formulario de crear provincia...');
                
                try {
                    // Deshabilitar botón y mostrar loading
                    saveBtn.disabled = true;
                    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Creando...';
                    
                    // Obtener datos del formulario
                    const formData = {
                        name: provinceNameInput.value.trim(),
                        departmentId: parseInt(provinceDepartmentSelect.value),
                        ubigeo: provinceUbigeoInput.value.trim()
                    };
                    
                    console.log('📤 Datos a enviar:', formData);
                    
                    // Validar datos
                    if (!formData.name) {
                        throw new Error('El nombre de la provincia es requerido');
                    }
                    if (!formData.departmentId) {
                        throw new Error('Debe seleccionar un departamento');
                    }
                    if (!formData.ubigeo || !/^[0-9]{4}$/.test(formData.ubigeo)) {
                        throw new Error('El código UBIGEO debe tener 4 dígitos');
                    }
                    
                    // Llamar al servicio para crear la provincia
                    const response = await fetch('https://149.130.161.148/api/v1/admin/provinces', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Authorization': `Bearer ${sessionStorage.getItem('token') || sessionStorage.getItem('authToken')}`,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(formData)
                    });
                    
                    const result = await response.json();
                    console.log('📥 Respuesta de la API:', result);
                    
                    if (response.ok && result.success) {
                        // Éxito
                        console.log('✅ Provincia creada exitosamente');
                        
                        // Mostrar mensaje de éxito
                        if (window.GlobalToast) {
                            GlobalToast.show('Provincia creada exitosamente', 'success');
                        }
                        
                        // Cerrar modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('createProvinceModal'));
                        if (modal) {
                            modal.hide();
                        }
                        
                        // Recargar lista de provincias si existe el controlador
                        if (window.ProvincesListController && window.ProvincesListController.load) {
                            window.ProvincesListController.load();
                        } else {
                            // Si no existe el controlador, recargar la página
                            setTimeout(() => location.reload(), 1000);
                        }
                        
                    } else {
                        // Error de la API
                        throw new Error(result.message || 'Error al crear la provincia');
                    }
                    
                } catch (error) {
                    console.error('❌ Error al crear provincia:', error);
                    
                    // Mostrar mensaje de error
                    if (window.GlobalToast) {
                        GlobalToast.show(error.message || 'Error al crear la provincia', 'error');
                    } else {
                        alert(error.message || 'Error al crear la provincia');
                    }
                    
                } finally {
                    // Restaurar botón
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = '<i class="fas fa-save me-1"></i> Crear Provincia';
                }
            });
        }
        /**
         * Actualiza la vista previa del modal con los valores actuales del formulario
         * - Actualiza el nombre de la provincia
         * - Actualiza el nombre del departamento seleccionado
         * - Actualiza el UBIGEO
         */
        function updatePreview() {
            // Actualizar nombre
            if (provinceNameInput && provinceNameInput.value.trim()) {
                previewName.innerHTML = `<span class="text-primary">${provinceNameInput.value}</span>`;
            } else {
                previewName.innerHTML = '<span class="text-muted">Vista previa</span>';
            }

            // Actualizar departamento
            if (provinceDepartmentSelect && provinceDepartmentSelect.value) {
                const selectedOption = provinceDepartmentSelect.options[provinceDepartmentSelect.selectedIndex];
                previewDepartment.textContent = selectedOption.text;
                previewDepartment.className = 'badge bg-success-lt me-1';
            } else {
                previewDepartment.textContent = '--';
                previewDepartment.className = 'badge bg-success-lt me-1';
            }

            // Actualizar UBIGEO
            if (provinceUbigeoInput && provinceUbigeoInput.value.trim()) {
                previewUbigeo.textContent = provinceUbigeoInput.value;
                previewUbigeo.className = 'badge bg-info-lt';
            } else {
                previewUbigeo.textContent = '--';
                previewUbigeo.className = 'badge bg-info-lt';
            }
        }

        // Agregar event listeners
        if (provinceNameInput) {
            provinceNameInput.addEventListener('input', updatePreview);
        }
        if (provinceDepartmentSelect) {
            provinceDepartmentSelect.addEventListener('change', updatePreview);
        }
        if (provinceUbigeoInput) {
            provinceUbigeoInput.addEventListener('input', updatePreview);
        }

        // Limpiar vista previa cuando se cierra el modal
        const modal = document.getElementById('createProvinceModal');
        if (modal) {
            modal.addEventListener('hidden.bs.modal', function() {
                previewName.innerHTML = '<span class="text-muted">Vista previa</span>';
                previewDepartment.textContent = '--';
                previewUbigeo.textContent = '--';
                
                // Limpiar formulario
                if (form) {
                    form.reset();
                }
            });
        }
    });
window.CreateProvinceModalController = CreateProvinceModalController;
