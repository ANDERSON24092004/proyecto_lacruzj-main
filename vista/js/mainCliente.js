$(document).ready(function(){
    console.log('Script de validación de clientes cargado');

    const exprif = /^\d{8}$/;
    const exprazon = /^[A-Za-z0-9Ä-ÿ\u00f1\u00d1\-\s\.\,]{1,200}$/;
    const exptelefono = /^\d{4}\-\d{7}$/;
    const expdireccion = /^[A-Za-z0-9Ä-ÿ\u00f1\u00d1\-\s\w\.\,\#\-\/]{1,500}$/;
    const expcorreo = /^[A-Za-z0-9_\.\-]+@[a-z0-9\-]+\.[A-Za-z0-9\-]{1,}$/;

    let rifsExistentes = [];

    const estilos = {
        valido: '3px solid #28a745',
        invalido: '3px solid #dc3545',
        normal: '2px solid #dee2e6',
        focus: '2px solid #4e54c8'
    };

    function aplicarEstilo(input, esValido) {
        if (esValido === true) {
            input.css('border', estilos.valido);
            input.css('box-shadow', '0 0 0 0.2rem rgba(40, 167, 69, 0.25)');
        } else if (esValido === false) {
            input.css('border', estilos.invalido);
            input.css('box-shadow', '0 0 0 0.2rem rgba(220, 53, 69, 0.25)');
        } else {
            input.css('border', estilos.normal);
            input.css('box-shadow', 'none');
        }
    }

    function cargarRifsExistentes() {
        $.ajax({
            url: 'index.php?c=ClienteControlador&m=obtenerTodosRifs',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    rifsExistentes = response.rifs;
                    console.log('RIFs cargados:', rifsExistentes);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar RIFs existentes:', error);
            }
        });
    }

    cargarRifsExistentes();

    function validarRifUnico(rif) {
        return !rifsExistentes.includes(rif);
    }

    function prevenirEntradaInvalida(input, regex, maxLength = null) {
        input.on('input', function() {
            let value = $(this).val();
            
            if (maxLength && value.length > maxLength) {
                value = value.substring(0, maxLength);
                $(this).val(value);
            }
            
            const cleanedValue = value.replace(new RegExp(`[^${regex.source.replace(/^\/|\/$/g, '').replace(/\\/g, '')}]`, 'g'), '');
            
            if (cleanedValue !== value) {
                $(this).val(cleanedValue);
            }
        });
    }

    function formatearTelefono(input) {
        input.on('input', function() {
            let value = $(this).val().replace(/\D/g, '');
            
            if (value.length === '') {
                $(this).val('');
            }

            if (value.length > 11) {
                value = value.substring(0, 11);
            }
            
            if (value.length >= 4) {
                value = value.substring(0, 4) + '-' + value.substring(4);
            }
            
            $(this).val(value);
        });
    }

    function formatearRif(input) {
        input.on('input', function() {
            let value = $(this).val().replace(/\D/g, '');
            
            if (value.length > 8) {
                value = value.substring(0, 8);
            }
            
            $(this).val(value);
        });
    }

    function inicializarValidacion(formType) {
        const isCrear = formType === 'crear';      

        const rifInput = isCrear ? $("#rif") : $("#rif_editar");
        const razonInput = isCrear ? $("#razon_social") : $("#razon_social_editar");
        const telefonoInput = isCrear ? $("#telefono") : $("#telefono_editar");
        const direccionInput = isCrear ? $("#direccion") : $("#direccion_editar");
        const correoInput = isCrear ? $("#correo") : $("#email_editar");
        const guardarBtn = isCrear ? $("#guardar") : $("#formEditarCliente button[type='submit']");

        const rifError = isCrear ? $("#riferror") : null;
        const razonError = isCrear ? $("#razonerror") : $("#razonerror_editar");
        const telefonoError = isCrear ? $("#telefonoerror") : $("#telefonoerror_editar");
        const direccionError = isCrear ? $("#direccionerror") : $("#direccionerror_editar");
        const correoError = isCrear ? $("#correoerror") : $("#emailerror_editar");

        aplicarEstilo(rifInput, null);
        aplicarEstilo(razonInput, null);
        aplicarEstilo(telefonoInput, null);
        aplicarEstilo(direccionInput, null);
        aplicarEstilo(correoInput, null);

        $('.form-control').on('focus', function() {
            $(this).css('border', estilos.focus);
        });

        $('.form-control').on('blur', function() {
            const value = $(this).val().trim();
            if (value === '') {
                aplicarEstilo($(this), null);
            }
        });

        $("[id$='error']").hide();

        if (isCrear) {
            formatearRif(rifInput);
        }
        
        prevenirEntradaInvalida(razonInput, exprazon, 200);
        formatearTelefono(telefonoInput);
        prevenirEntradaInvalida(direccionInput, expdireccion, 500);

        if (isCrear) {
            rifInput.on('input', function() {
                const rifValue = rifInput.val().trim();
                
                if (exprif.test(rifValue)) {
                    if (validarRifUnico(rifValue)) {
                        aplicarEstilo(rifInput, true);
                        rifError.hide().html('');
                    } else {
                        aplicarEstilo(rifInput, false);
                        rifError.show().html('Este RIF ya está registrado');
                    }
                } else {
                    aplicarEstilo(rifInput, false);
                    rifError.hide().html('');
                }
            });
        }

        razonInput.on('input', function() {
            const razonValue = razonInput.val().trim();
            
            if (razonValue === '') {
                aplicarEstilo(razonInput, null);
                razonError.hide().html('');
            } else if (exprazon.test(razonValue)) {
                aplicarEstilo(razonInput, true);
                razonError.hide().html('');
            } else {
                aplicarEstilo(razonInput, false);
                razonError.show().html('La razón social contiene caracteres no válidos');
            }
        });

        telefonoInput.on('input', function() {
            const telefonoValue = telefonoInput.val().trim();
            
            if (telefonoValue === '') {
                aplicarEstilo(telefonoInput, null);
                telefonoError.hide().html('');
            } else if (exptelefono.test(telefonoValue)) {
                aplicarEstilo(telefonoInput, true);
                telefonoError.hide().html('');
            } else {
                aplicarEstilo(telefonoInput, false);
                telefonoError.show().html('El formato debe ser: xxxx-xxxxxxx');
            }
        });

        direccionInput.on('input', function() {
            const direccionValue = direccionInput.val().trim();
            
            if (direccionValue === '') {
                aplicarEstilo(direccionInput, null);
                direccionError.hide().html('');
            } else if (expdireccion.test(direccionValue)) {
                aplicarEstilo(direccionInput, true);
                direccionError.hide().html('');
            } else {
                aplicarEstilo(direccionInput, false);
                direccionError.show().html('La dirección contiene caracteres no válidos');
            }
        });

        correoInput.on('input', function() {
            const correoValue = correoInput.val().trim();
            
            if (correoValue === '') {
                aplicarEstilo(correoInput, null);
                correoError.hide().html('');
            } else if (expcorreo.test(correoValue)) {
                aplicarEstilo(correoInput, true);
                correoError.hide().html('');
            } else {
                aplicarEstilo(correoInput, false);
                correoError.show().html('El formato del correo electrónico no es válido');
            }
        });

        guardarBtn.on('click', function(e) {
            e.preventDefault();
            
            let isValid = true;
            const errorMessages = [];

            if (isCrear) {
                const rifValue = rifInput.val().trim();
                if (!exprif.test(rifValue)) {
                    isValid = false;
                    errorMessages.push('RIF debe tener 8 dígitos');
                    aplicarEstilo(rifInput, false);
                } else if (!validarRifUnico(rifValue)) {
                    isValid = false;
                    errorMessages.push('El RIF ya está registrado');
                    aplicarEstilo(rifInput, false);
                }
            }

            const razonValue = razonInput.val().trim();
            if (!razonValue) {
                isValid = false;
                errorMessages.push('La razón social es obligatoria');
                aplicarEstilo(razonInput, false);
            } else if (!exprazon.test(razonValue)) {
                isValid = false;
                errorMessages.push('Razón social contiene caracteres no válidos');
                aplicarEstilo(razonInput, false);
            }

            const telefonoValue = telefonoInput.val().trim();
            if (!telefonoValue) {
                isValid = false;
                errorMessages.push('El teléfono es obligatorio');
                aplicarEstilo(telefonoInput, false);
            } else if (!exptelefono.test(telefonoValue)) {
                isValid = false;
                errorMessages.push('Teléfono debe tener formato: xxxx-xxxxxxx');
                aplicarEstilo(telefonoInput, false);
            }

            const direccionValue = direccionInput.val().trim();
            if (!direccionValue) {
                isValid = false;
                errorMessages.push('La dirección es obligatoria');
                aplicarEstilo(direccionInput, false);
            } else if (!expdireccion.test(direccionValue)) {
                isValid = false;
                errorMessages.push('Dirección contiene caracteres no válidos');
                aplicarEstilo(direccionInput, false);
            }

            const correoValue = correoInput.val().trim();
            if (!correoValue) {
                isValid = false;
                errorMessages.push('El correo electrónico es obligatorio');
                aplicarEstilo(correoInput, false);
            } else if (!expcorreo.test(correoValue)) {
                isValid = false;
                errorMessages.push('Formato de correo electrónico no válido');
                aplicarEstilo(correoInput, false);
            }

            if (isValid) {
                console.log('Formulario válido, enviando...');
                mostrarMensajeCarga('Procesando solicitud...');
                
                if (isCrear) {
                    $('#formCrearCliente').submit();
                } else {
                    $('#formEditarCliente').submit();
                }
            } else {
                console.log('Errores de validación:', errorMessages);
                mostrarMensaje('error', 'Error de validación', 'Por favor, corrija los siguientes errores:\n' + errorMessages.join('\n'));
            }
        });
    }

    if ($('#crearClienteModal').length) {
        inicializarValidacion('crear');
        
        $('#crearClienteModal').on('show.bs.modal', function() {
            cargarRifsExistentes();
            $('#formCrearCliente')[0].reset();
            $("[id$='error']").hide();
            $('.form-control').each(function() {
                aplicarEstilo($(this), null);
            });
        });
    }

    if ($('#editarClienteModal').length) {
        inicializarValidacion('editar');
        
        $('#editarClienteModal').on('show.bs.modal', function() {
            $("[id$='error']").hide();
            $('.form-control').each(function() {
                aplicarEstilo($(this), null);
            });
        });
    }

    window.cargarDatosEditar = function(cliente) {
        $('#rif_editar').val(cliente.rif);
        $('#razon_social_editar').val(cliente.razon_social);
        $('#telefono_editar').val(cliente.telefono);
        $('#direccion_editar').val(cliente.direccion);
        $('#email_editar').val(cliente.correo);
        
        aplicarEstilo($('#razon_social_editar'), true);
        aplicarEstilo($('#telefono_editar'), true);
        aplicarEstilo($('#direccion_editar'), true);
        aplicarEstilo($('#email_editar'), true);
    };
});