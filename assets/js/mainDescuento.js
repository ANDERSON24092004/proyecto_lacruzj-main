$(document).ready(function(){
    console.log('Script de gestión de descuentos cargado');
    
    const expPorcentaje = /^(100|[1-9]?\d)$/; 

    const estilos = {
        valido: '2px solid #28a745',
        invalido: '2px solid #dc3545',
        normal: '1px solid #ced4da'
    };

    function inicializarAplicacion() {
        cargarTablaDescuentos();
        inicializarEventos();
        inicializarEventosEliminar();
        inicializarValidaciones();
        inicializarPrevencionEntrada();
    }

    function cargarTablaDescuentos() {
        console.log('Cargando tabla de descuentos...');
        $.ajax({
            url: 'index.php?c=DescuentoControlador&m=listarAjax',
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                console.log('Respuesta recibida:', response);
                if (response.success) {
                    renderizarTablaDescuentos(response.data);
                } else {
                    mostrarErrorTabla(response.details);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en AJAX:', error);
                mostrarErrorTabla('Error al cargar los descuentos: ' + error);
            }
        });
    }
    
    function renderizarTablaDescuentos(descuentos) {
        console.log('Renderizando tabla con:', descuentos);
        
        const tbody = $('#descuento').find('tbody');
        tbody.empty();
        
        if (descuentos.length === 0) {
            tbody.append(`
                <tr>
                    <td colspan="3" class="text-center text-muted">
                        <i class="fas fa-percentage fa-2x mb-2"></i>
                        <p>No hay descuentos registrados</p>
                    </td>
                </tr>
            `);
            return;
        }
        
        descuentos.forEach(descuento => {
            const fila = `
                <tr>
                    <td>${escapeHtml(descuento.id)}</td>
                    <td>${escapeHtml(descuento.porcentaje)}%</td>
                    <td>
                        <div class="btn-group" role="group">
                            <button data-id="${descuento.id}" 
                                    data-porcentaje="${descuento.porcentaje}" 
                                    type="button" 
                                    class="btn btn-primary btn-sm btn-editar-descuento">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" 
                                class="btn btn-danger btn-sm btn-eliminar-descuento" 
                                data-id="${descuento.id}" 
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
        $('#descuento').find('tbody').html(`
            <tr>
                <td colspan="3" class="text-center text-danger">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                    <p>${mensaje}</p>
                    <button class="btn btn-sm btn-primary mt-2" onclick="cargarTablaDescuentos()">
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
        $('#modalDescuento .btn-secondary').on('click', function() {
            restaurarScroll();
            limpiarFormularioCrear();
        });
        
        $('#modalEditarDescuento .btn-secondary').on('click', function() {
            limpiarFormularioEditar();
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
        $('.btn-editar-descuento').off('click').on('click', function() {
            const id = $(this).data('id');
            const porcentaje = $(this).data('porcentaje');
            cargarDatosEditar(id, porcentaje);
        });

        $('.btn-eliminar-descuento').off('click').on('click', function() {
            const id = $(this).data('id');
            mostrarModalEliminar(id);
        });
    }
    
    function inicializarEventosEliminar() {
        $('#btnEliminarConfirmadoDescuento').off('click').on('click', function(e) {
            e.preventDefault();
            ejecutarEliminacion();
        });
    }
    
    function inicializarPrevencionEntrada() {
        $('#crear_descuento, #editar_descuento').on('input', function() {
            let value = $(this).val().replace(/[^0-9]/g, '');
            if (value > 100) value = 100;
            $(this).val(value);
        });
    }
    
    function inicializarValidaciones() {
        $('#crear_descuento').on('input', validarPorcentajeCrear);
        $('#editar_descuento').on('input', validarPorcentajeEditar);
    }

    function validarPorcentajeCrear() {
        const porcentaje = $('#crear_descuento').val().trim();
        const errorElement = $('#porcentaje_error');
        
        if (!porcentaje) {
            mostrarErrorCampo('crear_descuento', 'El porcentaje es obligatorio', errorElement);
            return false;
        } else if (!expPorcentaje.test(porcentaje)) {
            mostrarErrorCampo('crear_descuento', 'El porcentaje debe ser un número entre 0 y 100', errorElement);
            return false;
        } else {
            mostrarExitoCampo('crear_descuento', errorElement);
            return true;
        }
    }

    function validarPorcentajeEditar() {
        const porcentaje = $('#editar_descuento').val().trim();
        const errorElement = $('#editar_porcentaje_error');
        
        if (!porcentaje) {
            mostrarErrorCampo('editar_descuento', 'El porcentaje es obligatorio', errorElement);
            return false;
        } else if (!expPorcentaje.test(porcentaje)) {
            mostrarErrorCampo('editar_descuento', 'El porcentaje debe ser un número entre 0 y 100', errorElement);
            return false;
        } else {
            mostrarExitoCampo('editar_descuento', errorElement);
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
    
    function cargarDatosEditar(id, porcentaje) {
        $('#id_descuento').val(id);
        $('#editar_descuento').val(porcentaje);
        
        mostrarExitoCampo('editar_descuento', $('#editar_porcentaje_error'));
        
        $('#modalEditarDescuento').modal('show');
    }
    
    function mostrarModalEliminar(id) {
        $('#btnEliminarConfirmadoDescuento').data('id', id);
        $('#confirmarEliminarModalDescuento').modal('show');
    }
    
   function ejecutarEliminacion() {
    const id = $('#btnEliminarConfirmadoDescuento').data('id');
    
    console.log('Intentando eliminar descuento ID:', id);
    
    if (!id) {
        mostrarMensaje('error', 'Error', 'No se especificó el ID del descuento a eliminar');
        return;
    }
    
    $.ajax({
        url: 'index.php?c=DescuentoControlador&m=eliminarAjax',
        type: 'POST',
        data: { id: id },
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        beforeSend: function() {
            console.log('Enviando solicitud de eliminación...');
            $('#btnEliminarConfirmadoDescuento').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Eliminando...');
        },
        success: function(response) {
            console.log('Respuesta recibida:', response);
            $('#btnEliminarConfirmadoDescuento').prop('disabled', false).html('<i class="fas fa-check me-2"></i> Confirmar');
            
            if (response.success) {
                $('#confirmarEliminarModalDescuento').modal('hide');
                mostrarMensaje('success', response.message, response.details);
                cargarTablaDescuentos();
            } else {
                mostrarMensaje('error', response.message, response.details);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error en AJAX:', error);
            $('#btnEliminarConfirmadoDescuento').prop('disabled', false).html('<i class="fas fa-check me-2"></i> Confirmar');
            mostrarMensaje('error', 'Error', 'No se pudo eliminar el descuento: ' + error);
        }
    });
}
    
    function validarFormularioCreacion() {
        let isValid = true;
        
        if (!validarPorcentajeCrear()) isValid = false;
        
        return isValid;
    }
    
    function validarFormularioEdicion() {
        let isValid = true;
        
        if (!validarPorcentajeEditar()) isValid = false;
        
        return isValid;
    }
    
    $('#formDescuento').on('submit', function(e){
        e.preventDefault();
        
        if (!validarFormularioCreacion()) {
            return;
        }

        const formData = $(this).serialize();
        
        $.ajax({
            url: 'index.php?c=DescuentoControlador&m=guardarAjax',
            type: 'POST',
            data: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            beforeSend: function() {
                $('#formDescuento button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Guardando...');
            },
            success: function(response) {
                $('#formDescuento button[type="submit"]').prop('disabled', false).html('<i class="fas fa-save me-2"></i> Guardar Descuento');
                
                if (response.success) {
                    $('#modalDescuento').modal('hide');
                    $('#formDescuento')[0].reset();
                    mostrarMensaje('success', response.message, response.details);
                    cargarTablaDescuentos();
                } else {
                    const detalles = response.details || response.message;
                    mostrarMensaje('error', response.message, response.details);
                }
            },
            error: function(xhr, status, error) {
                $('#formDescuento button[type="submit"]').prop('disabled', false).html('<i class="fas fa-save me-2"></i> Guardar Descuento');
                mostrarMensaje('error', 'Error', 'No se pudo registrar el descuento: ' + error);
            }
        });
    });
    
    $('#formEditarDescuento').on('submit', function(e){
        e.preventDefault();
        
        if (!validarFormularioEdicion()) {
            return;
        }

        const formData = $(this).serialize();
        
        $.ajax({
            url: 'index.php?c=DescuentoControlador&m=actualizarAjax',
            type: 'POST',
            data: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            beforeSend: function() {
                $('#formEditarDescuento button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Guardando...');
            },
            success: function(response) {
                $('#formEditarDescuento button[type="submit"]').prop('disabled', false).html('<i class="fas fa-save me-2"></i> Guardar Cambios');
                
                if (response.success) {
                    $('#modalEditarDescuento').modal('hide');
                    mostrarMensaje('success', response.message, response.details);
                    cargarTablaDescuentos();
                } else {
                    const detalles = response.details || response.message;
                    mostrarMensaje('error', response.message, response.details);
                }
            },
            error: function(xhr, status, error) {
                $('#formEditarDescuento button[type="submit"]').prop('disabled', false).html('<i class="fas fa-save me-2"></i> Guardar Cambios');
                mostrarMensaje('error', 'Error', 'No se pudo actualizar el descuento: ' + error);
            }
        });
    });
    
    function limpiarFormularioCrear() {
        $('#formDescuento')[0].reset();
        $('.error-message').addClass('d-none').text('');
        $('#formDescuento .form-control').each(function() {
            resetearEstiloCampo($(this).attr('id'), $(`#${$(this).attr('id')}_error`));
        });
    }
    
    function limpiarFormularioEditar() {
        $('.error-message').addClass('d-none').text('');
        $('#formEditarDescuento .form-control').each(function() {
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
                cargarTablaDescuentos();
            }, 300);
        }
    }
    
    inicializarAplicacion();
    window.cargarTablaDescuentos = cargarTablaDescuentos;
});