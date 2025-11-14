$(document).ready(function(){
    console.log('Script de gestión de clientes cargado');

    const exprif = /^\d{8}$/;
    const exprazon = /^[A-Za-z0-9Ä-ÿ\u00f1\u00d1\-\s\.\,]{1,200}$/;
    const exptelefono = /^[0424\,0412\,0416\,0251\,0426\,0414]{4}-\d{7}$/; 
    const expdireccion = /^[A-Za-z0-9Ä-ÿ\u00f1\u00d1\-\s\w\.\,\#\-\/]{1,500}$/;
    const expcorreo = /^[A-Za-z0-9_\.\-]+@[a-z0-9\-]+\.[A-Za-z0-9\-]{1,}$/;

    let rifsExistentes = [];

    const estilos = {
        valido: '3px solid #28a745',
        invalido: '3px solid #dc3545',
        normal: '2px solid #dee2e6',
        focus: '2px solid #4e54c8'
    };

    function restaurarScroll() {
        setTimeout(function() {
            const modalesAbiertos = $('.modal.show');
            if (modalesAbiertos.length === 0) {
                $('body').removeClass('modal-open');
                $('body').css({
                    'overflow': 'auto',
                    'padding-right': '0'
                });
                $('.modal-backdrop').remove();
            }
        }, 150);
    }

    function mostrarErrorCampo(campoId, mensaje, inputElement = null) {
        const errorElement = $(`#${campoId}`);
        if (mensaje) {
            errorElement.text(mensaje).show();
            const input = inputElement || $(`#${campoId.replace('error', '')}`);
            if (input.length) {
                aplicarEstilo(input, false);
            }
        } else {
            errorElement.hide().text('');
        }
    }

    function limpiarFormularioCrear() {
        $('#formCrearCliente')[0].reset();
        $("[id$='error']").hide().text('');
        $('#crearClienteModal .form-control').each(function() {
            aplicarEstilo($(this), null);
        });
    }

    function limpiarFormularioEditar() {
        $("[id$='error_editar']").hide().text('');
        $('#editarClienteModal .form-control').each(function() {
            aplicarEstilo($(this), null);
        });
    }

    function cargarClientes() {
        $('#loading-clientes').show();
        $('#tabla-clientes-container').hide();
        $('#sin-clientes').hide();

        $.ajax({
            url: 'index.php?c=ClienteControlador&m=listarAjax',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                $('#loading-clientes').hide();
                
                console.log('Respuesta del servidor:', response);
                
                if (response.success && response.count > 0) {
                    $('#tbody-clientes').html(response.html);
                    $('#tabla-clientes-container').show();
                    $('#sin-clientes').hide();
                    inicializarEventosBotones();
                } else {
                    $('#tabla-clientes-container').hide();
                    $('#sin-clientes').show();
                }
            },
            error: function(xhr, status, error) {
                $('#loading-clientes').hide();
                $('#tabla-clientes-container').hide();
                $('#sin-clientes').show();
                console.error('Error al cargar clientes:', error);
                console.error('Status:', status);
                console.error('XHR:', xhr);
                mostrarMensaje('error', 'Error', 'No se pudieron cargar los clientes: ' + error);
            }
        });
    }

    function cargarRifsExistentes() {
        return $.ajax({
            url: 'index.php?c=ClienteControlador&m=obtenerTodosRifs',
            type: 'GET',
            dataType: 'json'
        }).then(function(response) {
            if (response.success) {
                rifsExistentes = response.rifs;
                console.log('RIFs cargados:', rifsExistentes);
            }
            return response;
        });
    }

    function inicializarEventosBotones() {
        $('.btn-editar-cliente').off('click').on('click', function() {
            const rif = $(this).data('rif');
            cargarDatosEditarModal(rif);
        });

        $('.btn-eliminar-cliente').off('click').on('click', function() {
            const rif = $(this).data('id');
            $('#btnEliminarConfirmado').data('rif', rif);
        });
    }

    function cargarDatosEditarModal(rif) {
        $.ajax({
            url: `index.php?c=ClienteControlador&m=obtenerClienteAjax&rif=${rif}`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.error) {
                    mostrarMensaje('error', 'Error', response.error);
                    return;
                }
                
                $('#rif_editar').val(response.rif);
                $('#razon_social_editar').val(response.razon_social);
                $('#telefono_editar').val(response.telefono);
                $('#email_editar').val(response.correo);
                $('#direccion_editar').val(response.direccion);
                
                limpiarFormularioEditar();
                
                aplicarEstilo($('#razon_social_editar'), true);
                aplicarEstilo($('#telefono_editar'), true);
                aplicarEstilo($('#email_editar'), true);
                aplicarEstilo($('#direccion_editar'), true);
                
                $('#editarClienteModal').modal('show');
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                mostrarMensaje('error', 'Error', 'No se pudieron cargar los datos del cliente');
            }
        });
    }

    function registrarCliente(formData) {
        $.ajax({
            url: 'index.php?c=ClienteControlador&m=guardarAjax',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#crearClienteModal').modal('hide');
                    restaurarScroll();
                    
                    mostrarMensaje('success', 'Éxito', response.message);
                    limpiarFormularioCrear();
                    
                    setTimeout(() => {
                        cargarClientes();
                        cargarRifsExistentes();
                    }, 500);
                } else {
                    let errorMessage = response.message;
                    if (response.errors) {
                        errorMessage += '\n' + response.errors.join('\n');
                    }
                    mostrarMensaje('error', 'Error', errorMessage);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                mostrarMensaje('error', 'Error', 'Error al registrar el cliente');
            }
        });
    }

    function actualizarCliente(formData) {
        $.ajax({
            url: 'index.php?c=ClienteControlador&m=actualizarAjax',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#editarClienteModal').modal('hide');
                    restaurarScroll();
                    
                    mostrarMensaje('success', 'Éxito', response.message);
                    
                    setTimeout(() => {
                        cargarClientes();
                    }, 500);
                } else {
                    let errorMessage = response.message;
                    if (response.errors) {
                        errorMessage += '\n' + response.errors.join('\n');
                    }
                    mostrarMensaje('error', 'Error', errorMessage);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                mostrarMensaje('error', 'Error', 'Error al actualizar el cliente');
            }
        });
    }

    function eliminarCliente(rif) {
        console.log('Iniciando eliminación para RIF:', rif);
        
        $('#confirmarEliminarModal').modal('hide');
        restaurarScroll();

        setTimeout(() => {
            $.ajax({
                url: `index.php?c=ClienteControlador&m=eliminarAjax&id=${rif}`,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log('Respuesta eliminación:', response);
                    
                    if (response.success) {
                        mostrarMensaje('success', 'Éxito', response.message);
                              
                        setTimeout(() => {
                            cargarClientes();
                            cargarRifsExistentes();
                        }, 500);
                    } else {
                        mostrarMensaje('error', 'Error', response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error en eliminación:', error);
                    mostrarMensaje('error', 'Error', 'Error al eliminar el cliente: ' + error);
                }
            });
        }, 300);
    }

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

    function validarRifUnico(rif) {
        return !rifsExistentes.includes(rif);
    }

    function inicializarEventosModal() {
        $('#crearClienteModal').on('hidden.bs.modal', function () {
            restaurarScroll();
        });
        
        $('#editarClienteModal').on('hidden.bs.modal', function () {
            restaurarScroll();
        });
        
        $('#confirmarEliminarModal').on('hidden.bs.modal', function () {
            restaurarScroll();
        });
        
        $('#mensajeModal').on('hidden.bs.modal', function () {
            restaurarScroll();
        });
    }

    function inicializarValidacion(formType) {
        const isCrear = formType === 'crear';      

        const rifInput = isCrear ? $("#rif") : $("#rif_editar");
        const razonInput = isCrear ? $("#razon_social") : $("#razon_social_editar");
        const telefonoInput = isCrear ? $("#telefono") : $("#telefono_editar");
        const direccionInput = isCrear ? $("#direccion") : $("#direccion_editar");
        const correoInput = isCrear ? $("#correo") : $("#email_editar");
        const form = isCrear ? $('#formCrearCliente') : $('#formEditarCliente');

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

        form.find('.form-control').on('focus', function() {
            $(this).css('border', estilos.focus);
        });

        form.find('.form-control').on('blur', function() {
            const value = $(this).val().trim();
            if (value === '') {
                aplicarEstilo($(this), null);
            }
        });

        $("[id$='error']").hide();
        $("[id$='error_editar']").hide();

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
                        if (rifError) rifError.hide().html('');
                    } else {
                        aplicarEstilo(rifInput, false);
                        if (rifError) rifError.show().html('Este RIF ya está registrado');
                    }
                } else {
                    aplicarEstilo(rifInput, false);
                    if (rifError) rifError.hide().html('');
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

        form.off('submit').on('submit', function(e) {
            e.preventDefault();
            
            let isValid = true;
            if (isCrear) {
                $("[id$='error']").hide().text('');
            } else {
                $("[id$='error_editar']").hide().text('');
            }

            if (isCrear) {
                const rifValue = rifInput.val().trim();
                if (!rifValue) {
                    isValid = false;
                    mostrarErrorCampo('riferror', 'El RIF es obligatorio', rifInput);
                } else if (!exprif.test(rifValue)) {
                    isValid = false;
                    mostrarErrorCampo('riferror', 'RIF debe tener 8 dígitos', rifInput);
                } else if (!validarRifUnico(rifValue)) {
                    isValid = false;
                    mostrarErrorCampo('riferror', 'Este RIF ya está registrado', rifInput);
                } else {
                    mostrarErrorCampo('riferror', '');
                }
            }

            const razonValue = razonInput.val().trim();
            if (!razonValue) {
                isValid = false;
                mostrarErrorCampo(isCrear ? 'razonerror' : 'razonerror_editar', 'La razón social es obligatoria', razonInput);
            } else if (!exprazon.test(razonValue)) {
                isValid = false;
                mostrarErrorCampo(isCrear ? 'razonerror' : 'razonerror_editar', 'Razón social contiene caracteres no válidos', razonInput);
            } else {
                mostrarErrorCampo(isCrear ? 'razonerror' : 'razonerror_editar', '');
            }

            const telefonoValue = telefonoInput.val().trim();
            if (!telefonoValue) {
                isValid = false;
                mostrarErrorCampo(isCrear ? 'telefonoerror' : 'telefonoerror_editar', 'El teléfono es obligatorio', telefonoInput);
            } else if (!exptelefono.test(telefonoValue)) {
                isValid = false;
                mostrarErrorCampo(isCrear ? 'telefonoerror' : 'telefonoerror_editar', 'Teléfono debe tener formato: xxxx-xxxxxxx', telefonoInput);
            } else {
                mostrarErrorCampo(isCrear ? 'telefonoerror' : 'telefonoerror_editar', '');
            }

            const direccionValue = direccionInput.val().trim();
            if (!direccionValue) {
                isValid = false;
                mostrarErrorCampo(isCrear ? 'direccionerror' : 'direccionerror_editar', 'La dirección es obligatoria', direccionInput);
            } else if (!expdireccion.test(direccionValue)) {
                isValid = false;
                mostrarErrorCampo(isCrear ? 'direccionerror' : 'direccionerror_editar', 'Dirección contiene caracteres no válidos', direccionInput);
            } else {
                mostrarErrorCampo(isCrear ? 'direccionerror' : 'direccionerror_editar', '');
            }

            const correoValue = correoInput.val().trim();
            if (!correoValue) {
                isValid = false;
                mostrarErrorCampo(isCrear ? 'correoerror' : 'emailerror_editar', 'El correo electrónico es obligatorio', correoInput);
            } else if (!expcorreo.test(correoValue)) {
                isValid = false;
                mostrarErrorCampo(isCrear ? 'correoerror' : 'emailerror_editar', 'Formato de correo electrónico no válido', correoInput);
            } else {
                mostrarErrorCampo(isCrear ? 'correoerror' : 'emailerror_editar', '');
            }

            if (isValid) {
                const formData = $(this).serialize();
                
                if (isCrear) {
                    registrarCliente(formData);
                } else {
                    actualizarCliente(formData);
                }
            }
        });
    }

    cargarClientes();
    cargarRifsExistentes();
    inicializarEventosModal(); 

    if ($('#crearClienteModal').length) {
        inicializarValidacion('crear');
        
        $('#crearClienteModal').on('show.bs.modal', function() {
            cargarRifsExistentes();
            $("[id$='error']").hide().text('');
            $('#crearClienteModal .form-control').each(function() {
                aplicarEstilo($(this), null);
            });
        });

        $('#crearClienteModal .btn-secondary').off('click').on('click', function() {
            limpiarFormularioCrear();
        });
    }

    if ($('#editarClienteModal').length) {
        inicializarValidacion('editar');
        
        $('#editarClienteModal').on('show.bs.modal', function() {
            limpiarFormularioEditar();
        });

        $('#editarClienteModal .btn-secondary').off('click').on('click', function() {
            limpiarFormularioEditar();
        });
    }

    $('#btnEliminarConfirmado').off('click').on('click', function(e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        
        const rif = $(this).data('rif');
        console.log('Botón eliminar clickeado, RIF:', rif);
        
        if (rif) {
            eliminarCliente(rif);
        }
    });
});