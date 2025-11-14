document.addEventListener('DOMContentLoaded', function() {
    const expCedula = /^\d{6,10}$/;
    const expNombre = /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{2,50}$/;
    const expTelefono = /^\d{4}-\d{7}$/;
    const expCorreo = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    const expClave = /^.{6,}$/;

    const estilos = {
        valido: '2px solid #28a745',
        invalido: '2px solid #dc3545',
        normal: '1px solid #ced4da'
    };

    const formRegistro = document.getElementById('registroForm');
    const btnRegistro = document.getElementById('btnRegistro');
    const btnRegistroText = document.getElementById('btnRegistroText');
    const btnRegistroLoading = document.getElementById('btnRegistroLoading');

    if (formRegistro && btnRegistro) {
        inicializarEventosRegistro();
        inicializarPrevencionEntrada();
        inicializarValidacionesTiempoReal();
    }

    function inicializarEventosRegistro() {
        formRegistro.addEventListener('submit', function(e) {
            if (!validarFormularioCompleto()) {
                e.preventDefault();
                mostrarMensajeErrorGeneral('Por favor, corrija los errores en el formulario antes de enviar.');
            } else {
                btnRegistro.disabled = true;
                btnRegistroText.textContent = 'Registrando...';
                btnRegistroLoading.classList.remove('d-none');
            }
        });

        const registroModal = document.getElementById('registroModal');
        if (registroModal) {
            registroModal.addEventListener('hidden.bs.modal', function() {
                limpiarValidaciones();
            });
        }
    }

    function inicializarPrevencionEntrada() {
        const cedulaInput = document.getElementById('cedula');
        const telefonoInput = document.getElementById('telefono');
        const nombreInput = document.getElementById('nombre');

        if (cedulaInput) {
            cedulaInput.addEventListener('input', function() {
                let value = this.value.replace(/\D/g, '');
                if (value.length > 10) value = value.substring(0, 10);
                this.value = value;
            });
        }

        if (telefonoInput) {
            telefonoInput.addEventListener('input', function() {
                let value = this.value.replace(/\D/g, '');
                if (value.length > 11) value = value.substring(0, 11);
                
                if (value.length >= 4) {
                    value = value.substring(0, 4) + '-' + value.substring(4);
                }
                
                this.value = value;
            });
        }

        if (nombreInput) {
            nombreInput.addEventListener('input', function() {
                let value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/g, '');
                if (value.length > 50) value = value.substring(0, 50);
                this.value = value;
            });
        }
    }

    function inicializarValidacionesTiempoReal() {
        const cedulaInput = document.getElementById('cedula');
        const nombreInput = document.getElementById('nombre');
        const telefonoInput = document.getElementById('telefono');
        const correoInput = document.getElementById('correo');
        const claveInput = document.getElementById('clave');

        if (cedulaInput) {
            cedulaInput.addEventListener('input', validarCedula);
            cedulaInput.addEventListener('blur', validarCedula);
        }
        if (nombreInput) {
            nombreInput.addEventListener('input', validarNombre);
            nombreInput.addEventListener('blur', validarNombre);
        }
        if (telefonoInput) {
            telefonoInput.addEventListener('input', validarTelefono);
            telefonoInput.addEventListener('blur', validarTelefono);
        }
        if (correoInput) {
            correoInput.addEventListener('input', validarCorreo);
            correoInput.addEventListener('blur', validarCorreo);
        }
    }

    function validarCedula() {
        const cedulaInput = document.getElementById('cedula');
        const cedula = cedulaInput ? cedulaInput.value.trim() : '';
        const errorElement = document.getElementById('cedula_error');
        
        if (!cedula) {
            mostrarErrorCampo('cedula', 'La cédula es obligatoria', errorElement);
            return false;
        } else if (!expCedula.test(cedula)) {
            mostrarErrorCampo('cedula', 'La cédula debe tener entre 6 y 10 dígitos', errorElement);
            return false;
        } else {
            mostrarExitoCampo('cedula', errorElement);
            return true;
        }
    }

    function validarNombre() {
        const nombreInput = document.getElementById('nombre');
        const nombre = nombreInput ? nombreInput.value.trim() : '';
        const errorElement = document.getElementById('nombre_error');
        
        if (!nombre) {
            mostrarErrorCampo('nombre', 'El nombre es obligatorio', errorElement);
            return false;
        } else if (!expNombre.test(nombre)) {
            mostrarErrorCampo('nombre', 'El nombre solo puede contener letras y espacios (2-50 caracteres)', errorElement);
            return false;
        } else {
            mostrarExitoCampo('nombre', errorElement);
            return true;
        }
    }

    function validarTelefono() {
        const telefonoInput = document.getElementById('telefono');
        const telefono = telefonoInput ? telefonoInput.value.trim() : '';
        const errorElement = document.getElementById('telefono_error');
        
        if (!telefono) {
            mostrarErrorCampo('telefono', 'El teléfono es obligatorio', errorElement);
            return false;
        } else if (!expTelefono.test(telefono)) {
            mostrarErrorCampo('telefono', 'Formato: xxxx-xxxxxxx (11 dígitos)', errorElement);
            return false;
        } else {
            mostrarExitoCampo('telefono', errorElement);
            return true;
        }
    }

    function validarCorreo() {
        const correoInput = document.getElementById('correo');
        const correo = correoInput ? correoInput.value.trim() : '';
        const errorElement = document.getElementById('correo_error');
        
        if (!correo) {
            mostrarErrorCampo('correo', 'El correo es obligatorio', errorElement);
            return false;
        } else if (!expCorreo.test(correo)) {
            mostrarErrorCampo('correo', 'Formato de correo electrónico no válido', errorElement);
            return false;
        } else {
            mostrarExitoCampo('correo', errorElement);
            return true;
        }
    }

    function validarFormularioCompleto() {
        let isValid = true;
        
        if (!validarCedula()) isValid = false;
        if (!validarNombre()) isValid = false;
        if (!validarTelefono()) isValid = false;
        if (!validarCorreo()) isValid = false;
        
        return isValid;
    }

    function mostrarErrorCampo(campoId, mensaje, errorElement) {
        const campo = document.getElementById(campoId);
        if (campo) {
            campo.style.border = estilos.invalido;
            campo.classList.add('is-invalid');
        }
        if (errorElement) {
            errorElement.textContent = mensaje;
            errorElement.style.display = 'block';
        }
    }

    function mostrarExitoCampo(campoId, errorElement) {
        const campo = document.getElementById(campoId);
        if (campo) {
            campo.style.border = estilos.valido;
            campo.classList.remove('is-invalid');
        }
        if (errorElement) {
            errorElement.style.display = 'none';
            errorElement.textContent = '';
        }
    }

    function resetearEstiloCampo(campoId, errorElement) {
        const campo = document.getElementById(campoId);
        if (campo) {
            campo.style.border = estilos.normal;
            campo.classList.remove('is-invalid');
        }
        if (errorElement) {
            errorElement.style.display = 'none';
            errorElement.textContent = '';
        }
    }

    function mostrarMensajeErrorGeneral(mensaje) {
        let alerta = document.getElementById('alerta-temporal');
        if (!alerta) {
            alerta = document.createElement('div');
            alerta.id = 'alerta-temporal';
            alerta.className = 'alert alert-danger alert-dismissible fade show mt-3';
            alerta.innerHTML = `
                <i class="fas fa-exclamation-triangle me-2"></i>
                ${mensaje}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            
            formRegistro.parentNode.insertBefore(alerta, formRegistro.nextSibling);
            
            setTimeout(() => {
                if (alerta && alerta.parentNode) {
                    alerta.parentNode.removeChild(alerta);
                }
            }, 5000);
        }
    }

    function limpiarValidaciones() {
        const campos = ['cedula', 'nombre', 'telefono', 'correo', 'clave'];
        const errores = ['cedula_error', 'nombre_error', 'telefono_error', 'correo_error'];
        
        campos.forEach((campo, index) => {
            resetearEstiloCampo(campo, document.getElementById(errores[index]));
        });
        
        const alertaTemporal = document.getElementById('alerta-temporal');
        if (alertaTemporal) {
            alertaTemporal.remove();
        }
        
        btnRegistro.disabled = false;
        btnRegistroText.textContent = 'Registrarse';
        btnRegistroLoading.classList.add('d-none');
    }
});