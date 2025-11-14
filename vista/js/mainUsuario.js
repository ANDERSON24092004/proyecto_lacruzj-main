$(document).ready(function(){
    console.log('Script de validación de usuarios cargado');
    
    const expCedula = /^\d{6,10}$/;
    const expNombre = /^[A-Za-z0-9Ä-ÿ\u00f1\u00d1\-\s]{3,50}$/; 
    const expTelefono = /^[0-9+\-\s()]{10,15}$/;
    const expEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const expPassword = /^.{6,}$/;
    
    const crearCedula = $("#cedula");
    const crearNombre = $("#nombre");
    const crearTelefono = $("#telefono");
    const crearCorreo = $("#correo");
    const crearRol = $("#id_rol");
    const crearPassword = $("#clave");
    const crearConfirmarPassword = $("#confirmar_clave");

    const editarCedula = $("#cedula_display");
    const editarNombre = $("#nombre_editar");
    const editarTelefono = $("#telefono_editar");
    const editarCorreo = $("#correo_editar");
    const editarRol = $("#rol_editar");
    const editarPassword = $("#clave_editar");
    
    $(".error-message").hide();
 
    function aplicarEstiloValidacion(elemento, esValido, mensajeError = '') {
        const errorDiv = elemento.next('.error-message');
        
        if (esValido) {
            elemento.css({
                'border': '2px solid #28a745',
                'box-shadow': '0 0 5px rgba(40, 167, 69, 0.3)'
            });
            errorDiv.hide();
        } else {
            elemento.css({
                'border': '2px solid #dc3545',
                'box-shadow': '0 0 5px rgba(220, 53, 69, 0.3)'
            });
            if (mensajeError) {
                errorDiv.text(mensajeError).show();
            } else {
                errorDiv.hide();
            }
        }
    }
    
    function validarCampoRequerido(elemento, mensajeError) {
        const valor = elemento.val().trim();
        if (!valor) {
            aplicarEstiloValidacion(elemento, false, mensajeError);
            return false;
        }
        aplicarEstiloValidacion(elemento, true);
        return true;
    }
    
    function verificarNombreUnico(nombre, cedula = null) {
        return new Promise((resolve) => {
            if (!expNombre.test(nombre)) {
                resolve(false);
                return;
            }
            
            const data = new URLSearchParams();
            data.append('nombre', nombre);
            if (cedula) {
                data.append('cedula', cedula);
            }
            
            fetch('index.php?c=usuario&m=verificarNombre', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: data
            })
            .then(response => response.json())
            .then(data => {
                resolve(data.disponible);
            })
            .catch(error => {
                console.error('Error al verificar nombre:', error);
                resolve(false);
            });
        });
    }
    
    function formatearTelefono(telefono) {
        return telefono.replace(/[^\d+\-()\s]/g, '');
    }
  
    function prevenirCaracteresInvalidos(elemento, regex, maxLength = null) {
        elemento.on('input', function() {
            let valor = $(this).val();
            
            const valorLimpio = valor.replace(new RegExp(`[^${regex}]`, 'g'), '');
            
            if (maxLength && valorLimpio.length > maxLength) {
                $(this).val(valorLimpio.substring(0, maxLength));
            } else if (valor !== valorLimpio) {
                $(this).val(valorLimpio);
            }
        });
        
        elemento.on('paste', function(e) {
            const clipboardData = e.originalEvent.clipboardData || window.clipboardData;
            const pastedData = clipboardData.getData('text');
            const cleanedData = pastedData.replace(new RegExp(`[^${regex}]`, 'g'), '');
            
            if (maxLength) {
                e.preventDefault();
                const currentValue = $(this).val();
                const newValue = currentValue + cleanedData;
                $(this).val(newValue.substring(0, maxLength));
            }
        });
    }
  
    prevenirCaracteresInvalidos(crearCedula, '0-9', 10);
    prevenirCaracteresInvalidos(crearNombre, 'A-Za-z0-9Ä-ÿñÑ\\-\\s', 50);
    prevenirCaracteresInvalidos(crearTelefono, '0-9+\\-\\s\\(\\)', 15);
    prevenirCaracteresInvalidos(editarNombre, 'A-Za-z0-9Ä-ÿñÑ\\-\\s', 50);
    prevenirCaracteresInvalidos(editarTelefono, '0-9+\\-\\s\\(\\)', 15);
    
    crearCedula.on('input', function(){
        const valor = $(this).val().trim();
        
        if (!valor) {
            aplicarEstiloValidacion($(this), false, 'La cédula es requerida');
            return;
        }
        
        if (expCedula.test(valor)) {
            aplicarEstiloValidacion($(this), true);
        } else {
            aplicarEstiloValidacion($(this), false, 'La cédula debe tener entre 6 y 10 dígitos');
        }
    });
    
    crearNombre.on('input', debounce(function(){
        const nombre = crearNombre.val().trim();
        
        if (!nombre) {
            aplicarEstiloValidacion(crearNombre, false, 'El nombre de usuario es requerido');
            return;
        }
        
        if (nombre.length < 3) {
            aplicarEstiloValidacion(crearNombre, false, 'El nombre debe tener al menos 3 caracteres');
            return;
        }
        
        if (!expNombre.test(nombre)) {
            aplicarEstiloValidacion(crearNombre, false, 'Solo se permiten letras, números, espacios y guiones');
            return;
        }
        
        verificarNombreUnico(nombre).then(disponible => {
            if (disponible) {
                aplicarEstiloValidacion(crearNombre, true);
            } else {
                aplicarEstiloValidacion(crearNombre, false, 'Este nombre de usuario ya está en uso');
            }
        });
    }, 500));
    
    crearTelefono.on('input', function(){
        const telefono = formatearTelefono($(this).val().trim());
        $(this).val(telefono);
        
        if (!telefono) {
            aplicarEstiloValidacion($(this), false, 'El teléfono es requerido');
            return;
        }
        
        if (expTelefono.test(telefono)) {
            aplicarEstiloValidacion($(this), true);
        } else {
            aplicarEstiloValidacion($(this), false, 'El teléfono debe tener entre 10 y 15 dígitos');
        }
    });
    
    crearCorreo.on('input', function(){
        const correo = crearCorreo.val().trim();
        
        if (!correo) {
            aplicarEstiloValidacion(crearCorreo, false, 'El correo electrónico es requerido');
            return;
        }
        
        if (expEmail.test(correo)) {
            aplicarEstiloValidacion(crearCorreo, true);
        } else {
            aplicarEstiloValidacion(crearCorreo, false, 'Ingrese un correo electrónico válido');
        }
    });
    
    crearRol.change(function(){
        const valor = crearRol.val();
        
        if (!valor) {
            aplicarEstiloValidacion(crearRol, false, 'Debe seleccionar un rol');
        } else {
            aplicarEstiloValidacion(crearRol, true);
        }
    });
    
    crearPassword.on('input', function(){
        const password = crearPassword.val();
        
        if (!password) {
            aplicarEstiloValidacion(crearPassword, false, 'La contraseña es requerida');
            return;
        }
        
        if (expPassword.test(password)) {
            aplicarEstiloValidacion(crearPassword, true);
        } else {
            aplicarEstiloValidacion(crearPassword, false, 'La contraseña debe tener al menos 6 caracteres');
        }
       
        const confirmarClave = crearConfirmarPassword.val();
        if (confirmarClave) {
            if (crearPassword.val() === confirmarClave) {
                aplicarEstiloValidacion(crearConfirmarPassword, true);
            } else {
                aplicarEstiloValidacion(crearConfirmarPassword, false, 'Las contraseñas no coinciden');
            }
        }
    });
    
    crearConfirmarPassword.on('input', function(){
        const clave = crearPassword.val();
        const confirmarClave = $(this).val();
        
        if (!confirmarClave) {
            aplicarEstiloValidacion($(this), false, 'Confirme la contraseña');
        } else if (clave === confirmarClave) {
            aplicarEstiloValidacion($(this), true);
        } else {
            aplicarEstiloValidacion($(this), false, 'Las contraseñas no coinciden');
        }
    });

    editarNombre.on('input', debounce(function(){
        const nombre = editarNombre.val().trim();
        const cedula = $("#cedula_editar").val();
        
        if (!nombre) {
            aplicarEstiloValidacion(editarNombre, false, 'El nombre de usuario es requerido');
            return;
        }
        
        if (nombre.length < 3) {
            aplicarEstiloValidacion(editarNombre, false, 'El nombre debe tener al menos 3 caracteres');
            return;
        }
        
        if (!expNombre.test(nombre)) {
            aplicarEstiloValidacion(editarNombre, false, 'Solo se permiten letras, números, espacios y guiones');
            return;
        }
        
        verificarNombreUnico(nombre, cedula).then(disponible => {
            if (disponible) {
                aplicarEstiloValidacion(editarNombre, true);
            } else {
                aplicarEstiloValidacion(editarNombre, false, 'Este nombre de usuario ya está en uso');
            }
        });
    }, 500));
    
    editarTelefono.on('input', function(){
        const telefono = formatearTelefono($(this).val().trim());
        $(this).val(telefono);
        
        if (!telefono) {
            aplicarEstiloValidacion($(this), false, 'El teléfono es requerido');
            return;
        }
        
        if (expTelefono.test(telefono)) {
            aplicarEstiloValidacion($(this), true);
        } else {
            aplicarEstiloValidacion($(this), false, 'El teléfono debe tener entre 10 y 15 dígitos');
        }
    });

    editarCorreo.on('input', function(){
        const correo = editarCorreo.val().trim();
        
        if (!correo) {
            aplicarEstiloValidacion(editarCorreo, false, 'El correo electrónico es requerido');
            return;
        }
        
        if (expEmail.test(correo)) {
            aplicarEstiloValidacion(editarCorreo, true);
        } else {
            aplicarEstiloValidacion(editarCorreo, false, 'Ingrese un correo electrónico válido');
        }
    });
    
    editarRol.change(function(){
        const valor = editarRol.val();
        
        if (!valor) {
            aplicarEstiloValidacion(editarRol, false, 'Debe seleccionar un rol');
        } else {
            aplicarEstiloValidacion(editarRol, true);
        }
    });
    
    editarPassword.on('input', function(){
        const password = editarPassword.val();
        
        if (password && !expPassword.test(password)) {
            aplicarEstiloValidacion(editarPassword, false, 'La contraseña debe tener al menos 6 caracteres');
        } else {
            aplicarEstiloValidacion(editarPassword, true);
        }
    });
    
    $('#formusuario').on('submit', function(e){
        console.log('Validando formulario de creación de usuario');
        
        let crearValido = true;
   
        const camposRequeridos = [
            { elemento: crearCedula, mensaje: 'La cédula es requerida' },
            { elemento: crearNombre, mensaje: 'El nombre de usuario es requerido' },
            { elemento: crearTelefono, mensaje: 'El teléfono es requerido' },
            { elemento: crearCorreo, mensaje: 'El correo electrónico es requerido' },
            { elemento: crearPassword, mensaje: 'La contraseña es requerida' },
            { elemento: crearConfirmarPassword, mensaje: 'Confirmar contraseña es requerido' }
        ];

        camposRequeridos.forEach(campo => {
            const valor = campo.elemento.val().trim();
            if (!valor) {
                aplicarEstiloValidacion(campo.elemento, false, campo.mensaje);
                crearValido = false;
            }
        });
        
        if (!crearRol.val()) {
            aplicarEstiloValidacion(crearRol, false, 'Debe seleccionar un rol');
            crearValido = false;
        }
        
        if (!crearValido) {
            console.log('Formulario de creación inválido - Campos vacíos detectados');
            e.preventDefault();
            $("#crear_btnerror").show().html('<i class="fas fa-exclamation-triangle me-2"></i>Por favor, complete todos los campos requeridos');
            $('html, body').animate({
                scrollTop: $("#crear_btnerror").offset().top - 100
            }, 500);
            return;
        }
        
        const camposValidacion = [
            { elemento: crearCedula, validador: () => expCedula.test(crearCedula.val().trim()), mensaje: 'La cédula debe tener entre 6 y 10 dígitos' },
            { elemento: crearNombre, validador: () => expNombre.test(crearNombre.val().trim()), mensaje: 'El nombre debe ser válido' },
            { elemento: crearTelefono, validador: () => expTelefono.test(crearTelefono.val().trim()), mensaje: 'El teléfono debe tener entre 10 y 15 dígitos' },
            { elemento: crearCorreo, validador: () => expEmail.test(crearCorreo.val().trim()), mensaje: 'Ingrese un correo electrónico válido' },
            { elemento: crearPassword, validador: () => expPassword.test(crearPassword.val()), mensaje: 'La contraseña debe tener al menos 6 caracteres' },
            { elemento: crearConfirmarPassword, validador: () => crearPassword.val() === crearConfirmarPassword.val(), mensaje: 'Las contraseñas no coinciden' }
        ];
        
        let formatoValido = true;
        camposValidacion.forEach(campo => {
            if (!campo.validador()) {
                aplicarEstiloValidacion(campo.elemento, false, campo.mensaje);
                formatoValido = false;
            }
        });
        
        if (!formatoValido) {
            console.log('Formulario de creación inválido - Errores de formato');
            e.preventDefault();
            $("#crear_btnerror").show().html('<i class="fas fa-exclamation-triangle me-2"></i>Por favor, complete todos los campos requeridos correctamente');
            $('html, body').animate({
                scrollTop: $("#crear_btnerror").offset().top - 100
            }, 500);
        } else {
            console.log('Formulario de creación válido');
            $("#crear_btnerror").hide();
        }
    });

    $('#formEditarUsuario').on('submit', function(e){
        console.log('Validando formulario de edición de usuario');
        
        let editarValido = true;
        
        const camposRequeridos = [
            { elemento: editarNombre, mensaje: 'El nombre de usuario es requerido' },
            { elemento: editarTelefono, mensaje: 'El teléfono es requerido' },
            { elemento: editarCorreo, mensaje: 'El correo electrónico es requerido' }
        ];
        
        camposRequeridos.forEach(campo => {
            const valor = campo.elemento.val().trim();
            if (!valor) {
                aplicarEstiloValidacion(campo.elemento, false, campo.mensaje);
                editarValido = false;
            }
        });
        
        if (!editarRol.val()) {
            aplicarEstiloValidacion(editarRol, false, 'Debe seleccionar un rol');
            editarValido = false;
        }
        
        if (!editarValido) {
            console.log('Formulario de edición inválido - Campos vacíos detectados');
            e.preventDefault();
            $("#editar_btnerror").show().html('<i class="fas fa-exclamation-triangle me-2"></i>Por favor, complete todos los campos requeridos');
            $('html, body').animate({
                scrollTop: $("#editar_btnerror").offset().top - 100
            }, 500);
            return;
        }
        
        const camposValidacion = [
            { elemento: editarNombre, validador: () => expNombre.test(editarNombre.val().trim()), mensaje: 'El nombre debe ser válido' },
            { elemento: editarTelefono, validador: () => expTelefono.test(editarTelefono.val().trim()), mensaje: 'El teléfono debe tener entre 10 y 15 dígitos' },
            { elemento: editarCorreo, validador: () => expEmail.test(editarCorreo.val().trim()), mensaje: 'Ingrese un correo electrónico válido' }
        ];
        
        let formatoValido = true;
        camposValidacion.forEach(campo => {
            if (!campo.validador()) {
                aplicarEstiloValidacion(campo.elemento, false, campo.mensaje);
                formatoValido = false;
            }
        });
        
        const password = editarPassword.val();
        if (password && !expPassword.test(password)) {
            aplicarEstiloValidacion(editarPassword, false, 'La contraseña debe tener al menos 6 caracteres');
            formatoValido = false;
        }
        
        if (!formatoValido) {
            console.log('Formulario de edición inválido - Errores de formato');
            e.preventDefault();
            $("#editar_btnerror").show().html('<i class="fas fa-exclamation-triangle me-2"></i>Por favor, complete todos los campos requeridos correctamente');
            $('html, body').animate({
                scrollTop: $("#editar_btnerror").offset().top - 100
            }, 500);
        } else {
            console.log('Formulario de edición válido');
            $("#editar_btnerror").hide();
        }
    });
    
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    function limpiarEstilosValidacion() {
        $('input, select').css({
            'border': '',
            'box-shadow': ''
        });
        $(".error-message").hide();
        $("#crear_btnerror").hide();
        $("#editar_btnerror").hide();
        $(".error-message").text('');
    }
    
    $('#registrarUsuarioModal').on('show.bs.modal', function () {
        console.log('Abriendo modal de registro');
        limpiarEstilosValidacion();
    });
    
    $('#registrarUsuarioModal').on('hidden.bs.modal', function () {
        console.log('Cerrando modal de registro');
        limpiarEstilosValidacion();
        $('#formusuario')[0].reset();
        $('input, select').removeAttr('style');
    });
    
    $('#editarUsuarioModal').on('show.bs.modal', function () {
        console.log('Abriendo modal de edición');
        limpiarEstilosValidacion();
    });
    
    $('#editarUsuarioModal').on('hidden.bs.modal', function () {
        console.log('Cerrando modal de edición');
        limpiarEstilosValidacion();
        $('input, select').removeAttr('style');
    });
    
    $('input').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            return false;
        }
    });
});