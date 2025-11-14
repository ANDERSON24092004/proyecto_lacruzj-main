$(document).ready(function(){
    console.log('Script de gestión de condiciones de pago cargado');
    
    const expForma = /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{2,30}$/;

    const estilos = {
        valido: '2px solid #28a745',
        invalido: '2px solid #dc3545',
        normal: '1px solid #ced4da'
    };

    function inicializarAplicacion() {
        cargarTablaCondiciones();
        inicializarEventos();
        inicializarEventosEliminar();
        inicializarValidaciones();
        inicializarPrevencionEntrada();
    }

    function cargarTablaCondiciones() {
        $.ajax({
            url: 'index.php?c=CondicionPagoControlador&m=listarAjax',
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    renderizarTablaCondiciones(response.data);
                } else {
                    mostrarErrorTabla(response.details);
                }
            },
            error: function(xhr, status, error) {
                mostrarErrorTabla('Error al cargar las condiciones de pago: ' + error);
            }
        });
    }
    
    function renderizarTablaCondiciones(condiciones) {
        const tbody = $('#tbody-condiciones');
        tbody.empty();
        
        if (condiciones.length === 0) {
            tbody.append(`
                <tr>
                    <td colspan="3" class="text-center text-muted">
                        <i class="fas fa-credit-card fa-2x mb-2"></i>
                        <p>No hay condiciones de pago registradas</p>
                    </td>
                </tr>
            `);
            return;
        }
        
        condiciones.forEach(condicion => {
            const fila = `
                <tr>
                    <td>${escapeHtml(condicion.id_condicion_pago)}</td>
                    <td>${escapeHtml(condicion.forma)}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <button data-id="${condicion.id_condicion_pago}" type="button" class="btn btn-primary btn-sm btn-editar-condicion">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" 
                                class="btn btn-danger btn-sm btn-eliminar-condicion" 
                                data-id="${condicion.id_condicion_pago}" 
                                title="Eliminar">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
            tbody.append(fila);
        });
        
        inicializarEventosBotones();
    }
    
    function mostrarErrorTabla(mensaje) {
        $('#tbody-condiciones').html(`
            <tr>
                <td colspan="3" class="text-center text-danger">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                    <p>${mensaje}</p>
                    <button class="btn btn-sm btn-primary mt-2" onclick="cargarTablaCondiciones()">
                        <i class="fas fa-redo"></i> Reintentar
                    </button>
                </td>
            </tr>
        `);
    }
    
    function escapeHtml(unsafe) {
        if (unsafe === null || unsafe === undefined) return '';
        return unsafe
            .toString()
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
    
    function inicializarEventos() {
        $('#modalCondicionPago .btn-secondary').on('click', function() {
            limpiarFormularioCrear();
        });
        
        $('#modalEditarCondicion .btn-secondary').on('click', function() {
            limpiarFormularioEditar();
        });

        $('#mensajeModal').on('hidden.bs.modal', function() {
            restaurarScroll();
        }); 
    }

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
    
    function inicializarEventosBotones() {
        $('.btn-editar-condicion').off('click').on('click', function() {
            const id = $(this).data('id');
            cargarDatosEditar(id);
        });

        $('.btn-eliminar-condicion').off('click').on('click', function() {
            const id = $(this).data('id');
            mostrarModalEliminar(id);
        });
    }
    
    function inicializarEventosEliminar() {
        $('#btnEliminarConfirmadoCondicion').off('click').on('click', function(e) {
            e.preventDefault();
            ejecutarEliminacion();
        });
    }
    
    function inicializarPrevencionEntrada() {
        $('#crear_forma, #editar_forma').on('input', function() {
            let value = $(this).val().replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/g, '');
            if (value.length > 30) value = value.substring(0, 30);
            $(this).val(value);
        });
    }
    
    function inicializarValidaciones() {
        $('#crear_forma').on('input', validarFormaCrear);
        $('#editar_forma').on('input', validarFormaEditar);
    }

    function validarFormaCrear() {
        const forma = $('#crear_forma').val().trim();
        const errorElement = $('#forma_error');
        
        if (!forma) {
            mostrarErrorCampo('crear_forma', 'La forma de pago es obligatoria', errorElement);
            return false;
        } else if (!expForma.test(forma)) {
            mostrarErrorCampo('crear_forma', 'La forma de pago solo puede contener letras y espacios (2-30 caracteres)', errorElement);
            return false;
        } else {
            mostrarExitoCampo('crear_forma', errorElement);
            return true;
        }
    }

    function validarFormaEditar() {
        const forma = $('#editar_forma').val().trim();
        const errorElement = $('#editar_forma_error');
        
        if (!forma) {
            mostrarErrorCampo('editar_forma', 'La forma de pago es obligatoria', errorElement);
            return false;
        } else if (!expForma.test(forma)) {
            mostrarErrorCampo('editar_forma', 'La forma de pago solo puede contener letras y espacios (2-30 caracteres)', errorElement);
            return false;
        } else {
            mostrarExitoCampo('editar_forma', errorElement);
            return true;
        }
    }

    function mostrarErrorCampo(campoId, mensaje, errorElement) {
        $(`#${campoId}`).css('border', estilos.invalido);
        errorElement.text(mensaje).removeClass('d-none');
    }

    function mostrarExitoCampo(campoId, errorElement) {
        $(`#${campoId}`).css('border', estilos.valido);
        errorElement.addClass('d-none').text('');
    }

    function resetearEstiloCampo(campoId, errorElement) {
        $(`#${campoId}`).css('border', estilos.normal);
        errorElement.addClass('d-none').text('');
    }
    
    function cargarDatosEditar(id) {
        $.ajax({
            url: 'index.php?c=CondicionPagoControlador&m=obtenerCondicionAjax&id_condicion_pago=' + id,
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response) {
                    $('#id_condicion_pago').val(response.id_condicion_pago);
                    $('#editar_forma').val(response.forma || '');
                    
                    mostrarExitoCampo('editar_forma', $('#editar_forma_error'));
                    
                    $('#modalEditarCondicion').modal('show');
                } else {
                    mostrarMensaje('error', 'Error', 'No se pudieron cargar los datos de la condición de pago');
                }
            },
            error: function(xhr, status, error) {
                mostrarMensaje('error', 'Error', 'No se pudieron cargar los datos de la condición de pago: ' + error);
            }
        });
    }
    
    function mostrarModalEliminar(id) {
        $('#btnEliminarConfirmadoCondicion').data('id', id);
        $('#confirmarEliminarModalCondicion').modal('show');
    }
    
    function ejecutarEliminacion() {
        const id = $('#btnEliminarConfirmadoCondicion').data('id');
        
        if (!id) {
            mostrarMensaje('error', 'Error', 'No se especificó el ID de la condición de pago a eliminar');
            return;
        }
        
        $.ajax({
            url: 'index.php?c=CondicionPagoControlador&m=eliminarAjax',
            type: 'POST',
            data: { id_condicion_pago: id },
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            beforeSend: function() {
                $('#btnEliminarConfirmadoCondicion').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Eliminando...');
            },
            success: function(response) {
                $('#btnEliminarConfirmadoCondicion').prop('disabled', false).html('<i class="fas fa-check me-2"></i> Confirmar');
                
                if (response.success) {
                    $('#confirmarEliminarModalCondicion').modal('hide');
                    mostrarMensaje('success', response.message, response.details);
                    cargarTablaCondiciones();
                } else {
                    mostrarMensaje('error', response.message, response.details);
                }
            },
            error: function(xhr, status, error) {
                $('#btnEliminarConfirmadoCondicion').prop('disabled', false).html('<i class="fas fa-check me-2"></i> Confirmar');
                mostrarMensaje('error', 'Error', 'No se pudo eliminar la condición de pago: ' + error);
            }
        });
    }
    
    function validarFormularioCreacion() {
        let isValid = true;
        
        if (!validarFormaCrear()) isValid = false;
        
        return isValid;
    }
    
    function validarFormularioEdicion() {
        let isValid = true;
        
        if (!validarFormaEditar()) isValid = false;
        
        return isValid;
    }
    
    $('#formCondicion').on('submit', function(e){
        e.preventDefault();
        
        if (!validarFormularioCreacion()) {
            return;
        }

        const formData = $(this).serialize();
        
        $.ajax({
            url: 'index.php?c=CondicionPagoControlador&m=guardarAjax',
            type: 'POST',
            data: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            beforeSend: function() {
                $('#formCondicion button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Guardando...');
            },
            success: function(response) {
                $('#formCondicion button[type="submit"]').prop('disabled', false).html('<i class="fas fa-save me-2"></i> Guardar Condicion de Pago');
                
                if (response.success) {
                    $('#modalCondicionPago').modal('hide');
                    $('#formCondicion')[0].reset();
                    mostrarMensaje('success', response.message, response.details);
                    cargarTablaCondiciones();
                } else {
                    const detalles = response.details || response.message;
                    mostrarMensaje('error', response.message, response.details);
                }
            },
            error: function(xhr, status, error) {
                $('#formCondicion button[type="submit"]').prop('disabled', false).html('<i class="fas fa-save me-2"></i> Guardar Condicion de Pago');
                mostrarMensaje('error', 'Error', 'No se pudo registrar la condición de pago: ' + error);
            }
        });
    });
    
    $('#formEditarCondicion').on('submit', function(e){
        e.preventDefault();
        
        if (!validarFormularioEdicion()) {
            return;
        }

        const formData = $(this).serialize();
        
        $.ajax({
            url: 'index.php?c=CondicionPagoControlador&m=actualizarAjax',
            type: 'POST',
            data: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            beforeSend: function() {
                $('#formEditarCondicion button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Guardando...');
            },
            success: function(response) {
                $('#formEditarCondicion button[type="submit"]').prop('disabled', false).html('<i class="fas fa-save me-2"></i> Guardar Cambios');
                
                if (response.success) {
                    $('#modalEditarCondicion').modal('hide');
                    mostrarMensaje('success', response.message, response.details);
                    cargarTablaCondiciones();
                } else {
                    const detalles = response.details || response.message;
                    mostrarMensaje('error', response.message, response.details);
                }
            },
            error: function(xhr, status, error) {
                $('#formEditarCondicion button[type="submit"]').prop('disabled', false).html('<i class="fas fa-save me-2"></i> Guardar Cambios');
                mostrarMensaje('error', 'Error', 'No se pudo actualizar la condición de pago: ' + error);
            }
        });
    });
    
    function limpiarFormularioCrear() {
        $('#formCondicion')[0].reset();
        $('.error-message').addClass('d-none').text('');
        $('#formCondicion .form-control').each(function() {
            resetearEstiloCampo($(this).attr('id'), $(`#${$(this).attr('id')}_error`));
        });
    }
    
    function limpiarFormularioEditar() {
        $('.error-message').addClass('d-none').text('');
        $('#formEditarCondicion .form-control').each(function() {
            const fieldName = $(this).attr('id');
            const errorElement = $(`#${fieldName}_error`);
            resetearEstiloCampo(fieldName, errorElement);
        });
    }
    
    function mostrarMensaje(tipo, titulo, mensaje) {
        console.log("mensaje:", tipo, titulo, mensaje);
        
        if (typeof window.mostrarMensaje === 'function') {
            window.mostrarMensaje(tipo, titulo, mensaje);
        } else {
            console.error('Función mostrarMensaje no disponible');
            alert(`${titulo}: ${mensaje}`);
            setTimeout(function() {
                cargarTablaCondiciones();
            }, 300);
        }
    }
    
    inicializarAplicacion();
    window.cargarTablaCondiciones = cargarTablaCondiciones;
});