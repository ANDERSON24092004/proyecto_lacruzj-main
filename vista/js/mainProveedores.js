$(document).ready(function(){
    console.log('Script de validación de proveedores cargado');
    
    const expRif = /^\d{8}$/;
    const expNombre = /^[A-Za-zÄ-ÿ\u00f1\u00d1\-\s]{1,100}$/;
    const expTelefono = /^\d{10,11}$/;
    const expDireccion = /^[A-Za-z0-9Ä-ÿ\u00f1\u00d1\-\s\.,#]{1,200}$/;
    const expEmail = /^[A-Za-z0-9_\.\-]+@[a-z0-9\-]+\.[A-Za-z0-9\-]{1,}$/;

    const crearRif = $("#crear_rif");
    const crearNombre = $("#crear_nombre");
    const crearTelefono = $("#crear_telefono");
    const crearEmail = $("#crear_email");
    const crearDireccionDetalle = $("#crear_direccion");
    const crearGuardar = $("#guardar_proveedor");
    
    const editarRif = $("#rif");
    const editarNombre = $("#nombre");
    const editarTelefono = $("#telefono");
    const editarEmail = $("#email");
    const editarDireccionDetalle = $("#direccion");
    const editarGuardar = $("#editar_proveedor");

    $(".error-message").hide();
    
    function verificarRifUnico(rif, callback, excluirId = null) {
        $.ajax({
            url: 'index.php?c=ProveedorControlador&m=verificarRifUnico',
            type: 'POST',
            data: {
                rif: rif,
                excluir_id: excluirId
            },
            success: function(response) {
                callback(response.existe);
            },
            error: function() {
                console.log('Error al verificar RIF');
                callback(false);
            }
        });
    } 

    crearRif.on("input", function(){
        const rifValue = crearRif.val();
        
        if (expRif.test(rifValue)) {
            verificarRifUnico(rifValue, function(existe) {
                if (existe) {
                    crearRif.css('border-bottom','7px solid red');
                    $("#rif_error").show();
                    $("#rif_error").html('El RIF ya está registrado');
                } else {
                    crearRif.css('border-bottom','7px solid green');
                    $("#rif_error").hide();
                    $("#rif_error").show();
                    $("#rif_error").html('RIF válido y disponible');
                }
            });
        } else {
            crearRif.css('border-bottom','7px solid red');
            $("#rif_error").show();
            $("#rif_error").html('El RIF debe contener exactamente 8 dígitos');
        }
    });

    crearNombre.on("input", function(){
        if (expNombre.test(crearNombre.val())) {
            console.log('El nombre es válido');
            crearNombre.css('border-bottom','7px solid green');
            $("#nombre_error").hide();
            $("#nombre_error").show();
            $("#nombre_error").html('Nombre válido');
        } else {
            console.log('El nombre no es válido');
            crearNombre.css('border-bottom','7px solid red');
            $("#nombre_error").show();
            $("#nombre_error").html('El nombre debe contener solo letras y espacios (máx. 100 caracteres)');
        }
    });

    crearTelefono.on("input", function(){
        if (expTelefono.test(crearTelefono.val())) {
            console.log('El teléfono es válido');
            crearTelefono.css('border-bottom','7px solid green');
            $("#telefono_error").hide();
            $("#telefono_error").show();
            $("#telefono_error").html('Teléfono válido');
        } else {
            console.log('El teléfono no es válido');
            crearTelefono.css('border-bottom','7px solid red');
            $("#telefono_error").show();
            $("#telefono_error").html('El teléfono debe contener entre 10 y 11 dígitos');
        }
    });
    
    crearEmail.on("input", function(){
        if (expEmail.test(crearEmail.val())) {
            console.log('El email es válido');
            crearEmail.css('border-bottom','7px solid green');
            $("#email_error").hide();
            $("#email_error").show();
            $("#email_error").html('Email válido');
        } else {
            console.log('El email no es válido');
            crearEmail.css('border-bottom','7px solid red');
            $("#email_error").show();
            $("#email_error").html('El formato del email no es válido');
        }
    });
    
    crearDireccionDetalle.on("input", function(){
        if (expDireccion.test(crearDireccionDetalle.val())) {
            console.log('La dirección es válida');
            crearDireccionDetalle.css('border-bottom','7px solid green');
            $("#direccion_error").hide();
            $("#direccion_error").show();
            $("#direccion_error").html('Dirección válida');
        } else {
            console.log('La dirección no es válida');
            crearDireccionDetalle.css('border-bottom','7px solid red');
            $("#direccion_error").show();
            $("#direccion_error").html('La dirección contiene caracteres no válidos');
        }
    });

    editarNombre.on("input", function(){
        if (expNombre.test(editarNombre.val())) {
            console.log('El nombre es válido');
            editarNombre.css('border-bottom','7px solid green');
            $("#enombre_editar_error").hide();
            $("#enombre_editar_error").show();
            $("#enombre_editar_error").html('Nombre válido');
        } else {
            console.log('El nombre no es válido');
            editarNombre.css('border-bottom','7px solid red');
            $("#enombre_editar_error").show();
            $("#enombre_editar_error").html('El nombre debe contener solo letras y espacios (máx. 100 caracteres)');
        }
    });
    
    editarTelefono.on("input", function(){
        if (expTelefono.test(editarTelefono.val())) {
            console.log('El teléfono es válido');
            editarTelefono.css('border-bottom','7px solid green');
            $("#telefono_editar_error").hide();
            $("#telefono_editar_error").show();
            $("#telefono_editar_error").html('Teléfono válido');
        } else {
            console.log('El teléfono no es válido');
            editarTelefono.css('border-bottom','7px solid red');
            $("#telefono_editar_error").show();
            $("#telefono_editar_error").html('El teléfono debe contener entre 10 y 11 dígitos');
        }
    });
    
    editarEmail.on("input", function(){
        if (expEmail.test(editarEmail.val())) {
            console.log('El email es válido');
            editarEmail.css('border-bottom','7px solid green');
            $("#email_editar_error").hide();
            $("#email_editar_error").show();
            $("#email_editar_error").html('Email válido');
        } else {
            console.log('El email no es válido');
            editarEmail.css('border-bottom','7px solid red');
            $("#email_editar_error").show();
            $("#email_editar_error").html('El formato del email no es válido');
        }
    });

    editarDireccionDetalle.on("input", function(){
        if (expDireccion.test(editarDireccionDetalle.val())) {
            console.log('La dirección es válida');
            editarDireccionDetalle.css('border-bottom','7px solid green');
            $("#direccion_editar_error").hide();
            $("#direccion_editar_error").show();
            $("#direccion_editar_error").html('Dirección válida');
        } else {
            console.log('La dirección no es válida');
            editarDireccionDetalle.css('border-bottom','7px solid red');
            $("#direccion_editar_error").show();
            $("#direccion_editar_error").html('La dirección contiene caracteres no válidos');
        }
    });
  
    crearGuardar.click(function(e){
        console.log('click en boton guardar proveedor');
        
        let crearValido = true;

        if (!expRif.test(crearRif.val())) {
            crearRif.css('border-bottom','7px solid red');
            $("#rif_error").show();
            $("#rif_error").html('El RIF debe contener exactamente 8 dígitos');
            crearValido = false;
        }
        
        if (!expNombre.test(crearNombre.val())) {
            crearNombre.css('border-bottom','7px solid red');
            $("#nombre_error").show();
            $("#nombre_error").html('El nombre debe contener solo letras y espacios (máx. 100 caracteres)');
            crearValido = false;
        }

        if (!expTelefono.test(crearTelefono.val())) {
            crearTelefono.css('border-bottom','7px solid red');
            $("#telefono_error").show();
            $("#telefono_error").html('El teléfono debe contener entre 10 y 11 dígitos');
            crearValido = false;
        }
        
        if (!expEmail.test(crearEmail.val())) {
            crearEmail.css('border-bottom','7px solid red');
            $("#email_error").show();
            $("#email_error").html('El formato del email no es válido');
            crearValido = false;
        }
        
        if (!expDireccion.test(crearDireccionDetalle.val())) {
            crearDireccionDetalle.css('border-bottom','7px solid red');
            $("#direccion_error").show();
            $("#direccion_error").html('La dirección contiene caracteres no válidos');
            crearValido = false;
        }
        
        if (!crearValido) {
            console.log('registro de proveedor incorrecto');
            e.preventDefault();
            $("#crear_btnerror").show();
            $("#crear_btnerror").html('Por favor, complete todos los campos correctamente');
        } else {
            console.log('registro de proveedor correcto');
            $("#crear_btnerror").hide();
        }
    });
    
    editarGuardar.click(function(e){
        console.log('click en boton guardar edición de proveedor');
        
        let editarValido = true;

        if (!expNombre.test(editarNombre.val())) {
            editarNombre.css('border-bottom','7px solid red');
            $("#enombre_editar_error").show();
            $("#enombre_editar_error").html('El nombre debe contener solo letras y espacios (máx. 100 caracteres)');
            editarValido = false;
        }

        if (!expTelefono.test(editarTelefono.val())) {
            editarTelefono.css('border-bottom','7px solid red');
            $("#telefono_editar_error").show();
            $("#telefono_editar_error").html('El teléfono debe contener entre 10 y 11 dígitos');
            editarValido = false;
        }
        
        if (!expEmail.test(editarEmail.val())) {
            editarEmail.css('border-bottom','7px solid red');
            $("#email_editar_error").show();
            $("#email_editar_error").html('El formato del email no es válido');
            editarValido = false;
        }

        if (!expDireccion.test(editarDireccionDetalle.val())) {
            editarDireccionDetalle.css('border-bottom','7px solid red');
            $("#direccion_editar_error").show();
            $("#direccion_editar_error").html('La dirección contiene caracteres no válidos');
            editarValido = false;
        }
        
        if (!editarValido) {
            console.log('edición de proveedor incorrecta');
            e.preventDefault();
            $("#editar_btnerror").show();
            $("#editar_btnerror").html('Por favor, complete todos los campos correctamente');
        } else {
            console.log('edición de proveedor correcta');
            $("#editar_btnerror").hide();
        }
    });

    
    $('#registrarProveedorModal').on('hidden.bs.modal', function () {
        $(".error-message").hide();
        $("#crear_rif, #crear_nombre, #crear_telefono, #crear_email, #crear_direccion").css('border-bottom', '');
        $("#crear_rif").val('');
        $("#crear_nombre").val('');
        $("#crear_telefono").val('');
        $("#crear_email").val('');
        $("#crear_direccion").val('');
    });
    
    $('#editarProveedorModal').on('hidden.bs.modal', function () {
        $(".error-message").hide();
        $("#rif, #nombre, #telefono, #email, #direccion").css('border-bottom', '');
    });

    crearRif.on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
    
    crearTelefono.on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
    
    editarTelefono.on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    crearNombre.on('input', function() {
        this.value = this.value.replace(/[^A-Za-zÄ-ÿ\u00f1\u00d1\-\s]/g, '');
    });
    
    editarNombre.on('input', function() {
        this.value = this.value.replace(/[^A-Za-zÄ-ÿ\u00f1\u00d1\-\s]/g, '');
    });

    
    function limpiarValidacionesCrear() {
        crearRif.css('border-bottom', '');
        crearNombre.css('border-bottom', '');
        crearTelefono.css('border-bottom', '');
        crearEmail.css('border-bottom', '');
        crearDireccionDetalle.css('border-bottom', '');
        $(".error-message").hide();
    }
    
    function limpiarValidacionesEditar() {
        editarNombre.css('border-bottom', '');
        editarTelefono.css('border-bottom', '');
        editarEmail.css('border-bottom', '');
        editarDireccionDetalle.css('border-bottom', '');
        $(".error-message").hide();
    }

    
    console.log('Validaciones de proveedores inicializadas correctamente');
});