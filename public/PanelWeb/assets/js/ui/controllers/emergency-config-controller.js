/**
 * Controlador UI para la página de Configuración de Emergencia
 * Maneja toda la funcionalidad específica de esta página
 */
class EmergencyConfigController {
    constructor() {
        this.isInitialized = false;
        this.configData = {};
        
        // Referencias a elementos del DOM
        this.emergencyContactInputs = {};
        this.alertConfigInputs = {};
        this.locationConfigInputs = {};
        
        // Inicializar
        this.init();
    }

    /**
     * Inicializa el controlador
     */
    async init() {
        console.log('🚨 Inicializando EmergencyConfigController...');
        try {
            this.findDOMElements();
            this.setupEventListeners();
            await this.loadConfiguration();
            this.setupEmergencyNumberValidation();
            this.setupEmergencyNumberFormSubmit();
            // Mostrar el número real guardado en la API
            const input = document.getElementById('emergency-number');
            const msg = document.getElementById('emergency-number-message');
            if (input && window.EmergencyConfigService) {
                const res = await window.EmergencyConfigService.getEmergencyNumber();
                if (res.success && res.data && res.data.number) {
                    input.value = res.data.number;
                    if (msg) msg.textContent = '';
                } else {
                    input.value = '';
                    if (msg) {
                        msg.textContent = res.error?.message || res.message || 'No hay número configurado.';
                        msg.style.color = 'orange';
                    }
                }
            }
            this.isInitialized = true;
            console.log('✅ EmergencyConfigController inicializado correctamente');
        } catch (error) {
            console.error('❌ Error al inicializar EmergencyConfigController:', error);
        }
    }

    /**
     * Busca y almacena referencias a los elementos del DOM
     */
    findDOMElements() {
        // Contactos de emergencia
        this.emergencyContactInputs = {
            police: {
                phone: document.getElementById('police-phone'),
                desc: document.getElementById('police-desc')
            },
            fire: {
                phone: document.getElementById('fire-phone'),
                desc: document.getElementById('fire-desc')
            },
            medical: {
                phone: document.getElementById('medical-phone'),
                desc: document.getElementById('medical-desc')
            },
            control: {
                phone: document.getElementById('control-phone'),
                desc: document.getElementById('control-desc')
            }
        };

        // Configuración de alertas
        this.alertConfigInputs = {
            responseTimeout: document.getElementById('response-timeout'),
            alertInterval: document.getElementById('alert-interval'),
            maxRetries: document.getElementById('max-retries')
        };

        // Configuración de ubicación
        this.locationConfigInputs = {
            gpsAccuracy: document.getElementById('gps-accuracy'),
            locationInterval: document.getElementById('location-interval'),
            safeZoneRadius: document.getElementById('safe-zone-radius')
        };

        // Elementos del toast
        this.toast = document.getElementById('toast');
        this.toastMessage = document.getElementById('toast-message');
    }

    /**
     * Configura los event listeners
     */
    setupEventListeners() {
        // Botones principales
        const saveBtn = document.querySelector('button[onclick="saveConfiguration()"]');
        const resetBtn = document.querySelector('button[onclick="resetToDefaults()"]');
        const testBtn = document.querySelector('button[onclick="testEmergencySystem()"]');

        if (saveBtn) {
            saveBtn.removeAttribute('onclick');
            saveBtn.addEventListener('click', () => this.saveConfiguration());
        }

        if (resetBtn) {
            resetBtn.removeAttribute('onclick');
            resetBtn.addEventListener('click', () => this.resetToDefaults());
        }

        if (testBtn) {
            testBtn.removeAttribute('onclick');
            testBtn.addEventListener('click', () => this.testEmergencySystem());
        }

        // Validación en tiempo real para campos numéricos
        Object.values(this.alertConfigInputs).forEach(input => {
            if (input) {
                input.addEventListener('input', (e) => this.validateNumericInput(e.target));
            }
        });

        Object.values(this.locationConfigInputs).forEach(input => {
            if (input) {
                input.addEventListener('input', (e) => this.validateNumericInput(e.target));
            }
        });

        // Validación para números de teléfono
        Object.values(this.emergencyContactInputs).forEach(contact => {
            if (contact.phone) {
                contact.phone.addEventListener('input', (e) => this.validatePhoneInput(e.target));
            }
        });
    }

    /**
     * Carga la configuración existente (simulado)
     */
    async loadConfiguration() {
        try {
            // En una implementación real, esto vendría de una API
            console.log('📡 Cargando configuración de emergencia...');
            
            // Simular carga de datos
            this.configData = {
                emergencyContacts: {
                    police: { phone: '105', description: 'Emergencias policiales y seguridad ciudadana' },
                    fire: { phone: '116', description: 'Emergencias de incendios y rescates' },
                    medical: { phone: '106', description: 'Emergencias médicas y ambulancias' },
                    control: { phone: '+51 123 456 789', description: 'Centro de control y monitoreo de la plataforma' }
                },
                alerts: {
                    responseTimeout: 5,
                    alertInterval: 2,
                    maxRetries: 3
                },
                location: {
                    gpsAccuracy: 10,
                    locationInterval: 30,
                    safeZoneRadius: 100
                }
            };

            // Aplicar los datos cargados a los inputs
            this.populateInputs();
            
            console.log('✅ Configuración cargada correctamente');
        } catch (error) {
            console.error('❌ Error al cargar configuración:', error);
            this.showToast('Error al cargar la configuración', 'error');
        }
    }

    /**
     * Puebla los inputs con los datos cargados
     */
    populateInputs() {
        // Contactos de emergencia
        Object.keys(this.configData.emergencyContacts).forEach(key => {
            const contact = this.emergencyContactInputs[key];
            const data = this.configData.emergencyContacts[key];
            
            if (contact && data) {
                if (contact.phone) contact.phone.value = data.phone;
                if (contact.desc) contact.desc.value = data.description;
            }
        });

        // Configuración de alertas
        Object.keys(this.configData.alerts).forEach(key => {
            const input = this.alertConfigInputs[key];
            if (input) {
                input.value = this.configData.alerts[key];
            }
        });

        // Configuración de ubicación
        Object.keys(this.configData.location).forEach(key => {
            const input = this.locationConfigInputs[key];
            if (input) {
                input.value = this.configData.location[key];
            }
        });
    }

    /**
     * Guarda la configuración
     */
    async saveConfiguration() {
        try {
            console.log('💾 Guardando configuración...');
            this.showToast('Guardando configuración...', 'info');

            // Recopilar datos de los inputs
            const configToSave = this.gatherConfigurationData();

            // Validar datos
            if (!this.validateConfiguration(configToSave)) {
                return;
            }

            // En una implementación real, esto iría a una API
            console.log('📤 Datos a guardar:', configToSave);

            // Simular guardado
            await new Promise(resolve => setTimeout(resolve, 1500));

            // Actualizar datos locales
            this.configData = configToSave;

            this.showToast('Configuración guardada exitosamente', 'success');
            console.log('✅ Configuración guardada');
        } catch (error) {
            console.error('❌ Error al guardar configuración:', error);
            this.showToast('Error al guardar la configuración', 'error');
        }
    }

    /**
     * Recopila los datos de configuración de los inputs
     */
    gatherConfigurationData() {
        return {
            emergencyContacts: {
                police: {
                    phone: this.emergencyContactInputs.police.phone?.value || '',
                    description: this.emergencyContactInputs.police.desc?.value || ''
                },
                fire: {
                    phone: this.emergencyContactInputs.fire.phone?.value || '',
                    description: this.emergencyContactInputs.fire.desc?.value || ''
                },
                medical: {
                    phone: this.emergencyContactInputs.medical.phone?.value || '',
                    description: this.emergencyContactInputs.medical.desc?.value || ''
                },
                control: {
                    phone: this.emergencyContactInputs.control.phone?.value || '',
                    description: this.emergencyContactInputs.control.desc?.value || ''
                }
            },
            alerts: {
                responseTimeout: parseInt(this.alertConfigInputs.responseTimeout?.value) || 5,
                alertInterval: parseInt(this.alertConfigInputs.alertInterval?.value) || 2,
                maxRetries: parseInt(this.alertConfigInputs.maxRetries?.value) || 3
            },
            location: {
                gpsAccuracy: parseInt(this.locationConfigInputs.gpsAccuracy?.value) || 10,
                locationInterval: parseInt(this.locationConfigInputs.locationInterval?.value) || 30,
                safeZoneRadius: parseInt(this.locationConfigInputs.safeZoneRadius?.value) || 100
            }
        };
    }

    /**
     * Valida la configuración antes de guardar
     */
    validateConfiguration(config) {
        // Validar contactos de emergencia
        for (const [key, contact] of Object.entries(config.emergencyContacts)) {
            if (!contact.phone.trim()) {
                this.showToast(`El teléfono de ${this.getContactDisplayName(key)} es requerido`, 'error');
                return false;
            }
        }

        // Validar rangos numéricos
        if (config.alerts.responseTimeout < 1 || config.alerts.responseTimeout > 30) {
            this.showToast('El tiempo de respuesta debe estar entre 1 y 30 minutos', 'error');
            return false;
        }

        return true;
    }

    /**
     * Restaura los valores por defecto
     */
    resetToDefaults() {
        if (confirm('¿Está seguro de que desea restaurar los valores por defecto? Se perderán todos los cambios no guardados.')) {
            // Restaurar valores por defecto
            this.configData = {
                emergencyContacts: {
                    police: { phone: '105', description: 'Emergencias policiales y seguridad ciudadana' },
                    fire: { phone: '116', description: 'Emergencias de incendios y rescates' },
                    medical: { phone: '106', description: 'Emergencias médicas y ambulancias' },
                    control: { phone: '+51 123 456 789', description: 'Centro de control y monitoreo de la plataforma' }
                },
                alerts: { responseTimeout: 5, alertInterval: 2, maxRetries: 3 },
                location: { gpsAccuracy: 10, locationInterval: 30, safeZoneRadius: 100 }
            };

            this.populateInputs();
            this.showToast('Valores restaurados por defecto', 'success');
        }
    }

    /**
     * Prueba el sistema de emergencia
     */
    async testEmergencySystem() {
        try {
            this.showToast('Iniciando prueba del sistema de emergencia...', 'info');
            console.log('🧪 Iniciando prueba del sistema...');

            // Simular pruebas del sistema
            await new Promise(resolve => setTimeout(resolve, 3000));

            this.showToast('Sistema de emergencia funcionando correctamente', 'success');
            console.log('✅ Prueba del sistema completada');
        } catch (error) {
            console.error('❌ Error en prueba del sistema:', error);
            this.showToast('Error durante la prueba del sistema', 'error');
        }
    }

    /**
     * Valida inputs numéricos
     */
    validateNumericInput(input) {
        const value = parseInt(input.value);
        const min = parseInt(input.min) || 0;
        const max = parseInt(input.max) || Infinity;

        if (isNaN(value) || value < min || value > max) {
            input.classList.add('invalid');
        } else {
            input.classList.remove('invalid');
        }
    }

    /**
     * Valida inputs de teléfono
     */
    validatePhoneInput(input) {
        const value = input.value.trim();
        // Validación básica para números de teléfono
        if (value && !/^[\+\d\s\-\(\)]+$/.test(value)) {
            input.classList.add('invalid');
        } else {
            input.classList.remove('invalid');
        }
    }

    /**
     * Configura la validación del input de número de emergencia
     */
    setupEmergencyNumberValidation() {
        const input = document.getElementById('emergency-number');
        if (!input) return;
        input.addEventListener('input', (e) => {
            // Permitir solo números, espacios y el símbolo +, máximo 15 caracteres
            let value = e.target.value.replace(/[^\d\+\s]/g, '');
            if (value.length > 15) value = value.slice(0, 15);
            e.target.value = value;
        });
    }

    /**
     * Asocia el submit del formulario de número de emergencia para mostrar confirmación y actualizar el input
     */
    setupEmergencyNumberFormSubmit() {
        const input = document.getElementById('emergency-number');
        const form = document.getElementById('emergency-number-form');
        const msg = document.getElementById('emergency-number-message');
        if (!input || !form || !msg) return;
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            msg.textContent = '';
            const number = input.value.trim();
            if (!number) {
                msg.textContent = 'Ingrese un número válido.';
                msg.style.color = 'red';
                return;
            }
            const res = await window.EmergencyConfigService.updateEmergencyNumber(number);
            if (res.success) {
                msg.textContent = 'Número actualizado correctamente.';
                msg.style.color = 'green';
                // Obtener el número actualizado desde la API y mostrarlo
                const getRes = await window.EmergencyConfigService.getEmergencyNumber();
                if (getRes.success && getRes.data && getRes.data.number) {
                    input.value = getRes.data.number;
                }
            } else {
                msg.textContent = res.error?.message || res.message || 'Error al actualizar.';
                msg.style.color = 'red';
            }
        });
    }

    /**
     * Obtiene el nombre de visualización de un contacto
     */
    getContactDisplayName(key) {
        const names = {
            police: 'Policía',
            fire: 'Bomberos',
            medical: 'SAMU',
            control: 'Centro de Control'
        };
        return names[key] || key;
    }

    /**
     * Muestra un mensaje toast
     */
    showToast(message, type = 'info') {
        if (!this.toast || !this.toastMessage) return;

        this.toastMessage.textContent = message;
        this.toast.className = `toast ${type}`;
        this.toast.classList.add('show');

        setTimeout(() => {
            this.toast.classList.remove('show');
        }, 3000);
    }

    /**
     * Verifica si el controlador está listo
     */
    isReady() {
        return this.isInitialized;
    }

    /**
     * Destruye el controlador
     */
    destroy() {
        this.isInitialized = false;
        console.log('🗑️ EmergencyConfigController destruido');
    }
}

// Crear instancia global del controlador
if (typeof window !== 'undefined') {
    window.EmergencyConfigController = EmergencyConfigController;
}
