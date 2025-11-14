$(document).ready(function(){
    console.log('Script de gestión de IVA cargado');
    
    const expPorcentaje = /^(100|[1-9]?[0-9])$/;

    const estilos = {
        valido: '2px solid #28a745',
        invalido: '2px solid #dc3545',
        normal: '1px solid #ced4da'
    };

    function inicializarAplicacion() {
        cargarTablaIvas();
        inicializarEventos();
        inicializarEventosEliminar();
        inicializarValidaciones();
        inicializarPrevencionEntrada();
    }

    function cargarTablaIvas() {
        $.ajax({
            url: 'index.php?c=IvaControlador&m=listarAjax',
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    renderizarTablaIvas(response.data);
                } else {
                    mostrarErrorTabla(response.details);
                }
            },
            error: function(xhr, status, error) {
                mostrarErrorTabla('Error al cargar los IVAs: ' + error);
            }
        });
    }
    
    function renderizarTablaIvas(ivas) {
        const tbody = $('#tbody-iva');
        tbody.empty();
        
        if (ivas.length === 0) {
            tbody.append(`
                <tr>
                    <td colspan="3" class="text-center text-muted">
                        <i class="fas fa-receipt fa-2x mb-2"></i>
                        <p>No hay IVAs registrados</p>
                    </td>
                </tr>
            `);
            return;
        }
        
        ivas.forEach(iva => {
            const fila = `
                <tr>
                    <td>${escapeHtml(iva.id_iva)}</td>
                    <td>${escapeHtml(iva.porcentaje)}%</td>
                    <td>
                        <div class="btn-group" role="group">
                            <button data-id="${iva.id_iva}" 
                                    data-porcentaje="${iva.porcentaje}" 
                                    type="button" 
                                    class="btn btn-primary btn-sm btn-editar-iva">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" 
                                class="btn btn-danger btn-sm btn-eliminar-iva" 
                                data-id="${iva.id_iva}" 
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
        $('#tbody-iva').html(`
            <tr>
                <td colspan="3" class="text-center text-danger">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                    <p>${mensaje}</p>
                    <button class="btn btn-sm btn-primary mt-2" onclick="cargarTablaIvas()">
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
        $('#modalIva .btn-secondary').on('click', function() {
            limpiarFormularioCrear();
        });
        
        $('#modalEditarIva .btn-secondary').on('click', function() {
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
        $('.btn-editar-iva').off('click').on('click', function() {
            const id = $(this).data('id');
            const porcentaje = $(this).data('porcentaje');
            cargarDatosEditar(id, porcentaje);
        });

        $('.btn-eliminar-iva').off('click').on('click', function() {
            const id = $(this).data('id');
            mostrarModalEliminar(id);
        });
    }
    
    function inicializarEventosEliminar() {
        $('#btnEliminarConfirmadoIva').off('click').on('click', function(e) {
            e.preventDefault();
            ejecutarEliminacion();
        });
    }
    
    function inicializarPrevencionEntrada() {
        $('#crear_iva, #editar_iva').on('input', function() {
            let value = $(this).val().replace(/[^0-9]/g, '');
            if (value > 100) value = '100';
            $(this).val(value);
        });
    }
    
    function inicializarValidaciones() {
        $('#crear_iva').on('input', validarPorcentajeCrear);
        $('#editar_iva').on('input', validarPorcentajeEditar);
    }

    function validarPorcentajeCrear() {
        const porcentaje = $('#crear_iva').val().trim();
        const errorElement = $('#porcentaje_error');
        
        if (!porcentaje) {
            mostrarErrorCampo('crear_iva', 'El porcentaje de IVA es obligatorio', errorElement);
            return false;
        } else if (!expPorcentaje.test(porcentaje)) {
            mostrarErrorCampo('crear_iva', 'El porcentaje debe ser un número entre 0 y 100', errorElement);
            return false;
        } else {
            mostrarExitoCampo('crear_iva', errorElement);
            return true;
        }
    }

    function validarPorcentajeEditar() {
        const porcentaje = $('#editar_iva').val().trim();
        const errorElement = $('#editar_porcentaje_error');
        
        if (!porcentaje) {
            mostrarErrorCampo('editar_iva', 'El porcentaje de IVA es obligatorio', errorElement);
            return false;
        } else if (!expPorcentaje.test(porcentaje)) {
            mostrarErrorCampo('editar_iva', 'El porcentaje debe ser un número entre 0 y 100', errorElement);
            return false;
        } else {
            mostrarExitoCampo('editar_iva', errorElement);
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
        $('#id_iva').val(id);
        $('#editar_iva').val(porcentaje);
        
        mostrarExitoCampo('editar_iva', $('#editar_porcentaje_error'));
        
        $('#modalEditarIva').modal('show');
    }
    
    function mostrarModalEliminar(id) {
        $('#btnEliminarConfirmadoIva').data('id', id);
        $('#confirmarEliminarModalIva').modal('show');
    }
    
    function ejecutarEliminacion() {
        const id = $('#btnEliminarConfirmadoIva').data('id');
        
        if (!id) {
            mostrarMensaje('error', 'Error', 'No se especificó el ID del IVA a eliminar');
            return;
        }
        
        $.ajax({
            url: 'index.php?c=IvaControlador&m=eliminarAjax',
            type: 'POST',
            data: { id_iva: id },
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            beforeSend: function() {
                $('#btnEliminarConfirmadoIva').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Eliminando...');
            },
            success: function(response) {
                $('#btnEliminarConfirmadoIva').prop('disabled', false).html('<i class="fas fa-check me-2"></i> Confirmar');
                
                if (response.success) {
                    $('#confirmarEliminarModalIva').modal('hide');
                    mostrarMensaje('success', response.message, response.details);
                    cargarTablaIvas();
                } else {
                    mostrarMensaje('error', response.message, response.details);
                }
            },
            error: function(xhr, status, error) {
                $('#btnEliminarConfirmadoIva').prop('disabled', false).html('<i class="fas fa-check me-2"></i> Confirmar');
                mostrarMensaje('error', 'Error', 'No se pudo eliminar el IVA: ' + error);
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
    
    $('#formIva').on('submit', function(e){
        e.preventDefault();
        
        if (!validarFormularioCreacion()) {
            return;
        }

        const formData = $(this).serialize();
        
        $.ajax({
            url: 'index.php?c=IvaControlador&m=guardarAjax',
            type: 'POST',
            data: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            beforeSend: function() {
                $('#formIva button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Guardando...');
            },
            success: function(response) {
                $('#formIva button[type="submit"]').prop('disabled', false).html('<i class="fas fa-save me-2"></i> Guardar IVA');
                
                if (response.success) {
                    $('#modalIva').modal('hide');
                    $('#formIva')[0].reset();
                    mostrarMensaje('success', response.message, response.details);
                    cargarTablaIvas();
                } else {
                    const detalles = response.details || response.message;
                    mostrarMensaje('error', response.message, response.details);
                }
            },
            error: function(xhr, status, error) {
                $('#formIva button[type="submit"]').prop('disabled', false).html('<i class="fas fa-save me-2"></i> Guardar IVA');
                mostrarMensaje('error', 'Error', 'No se pudo registrar el IVA: ' + error);
            }
        });
    });
    
    $('#formEditarIva').on('submit', function(e){
        e.preventDefault();
        
        if (!validarFormularioEdicion()) {
            return;
        }

        const formData = $(this).serialize();
        
        $.ajax({
            url: 'index.php?c=IvaControlador&m=actualizarAjax',
            type: 'POST',
            data: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            beforeSend: function() {
                $('#formEditarIva button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Guardando...');
            },
            success: function(response) {
                $('#formEditarIva button[type="submit"]').prop('disabled', false).html('<i class="fas fa-save me-2"></i> Guardar Cambios');
                
                if (response.success) {
                    $('#modalEditarIva').modal('hide');
                    mostrarMensaje('success', response.message, response.details);
                    cargarTablaIvas();
                } else {
                    const detalles = response.details || response.message;
                    mostrarMensaje('error', response.message, response.details);
                }
            },
            error: function(xhr, status, error) {
                $('#formEditarIva button[type="submit"]').prop('disabled', false).html('<i class="fas fa-save me-2"></i> Guardar Cambios');
                mostrarMensaje('error', 'Error', 'No se pudo actualizar el IVA: ' + error);
            }
        });
    });
    
    function limpiarFormularioCrear() {
        $('#formIva')[0].reset();
        $('.error-message').addClass('d-none').text('');
        $('#formIva .form-control').each(function() {
            resetearEstiloCampo($(this).attr('id'), $(`#${$(this).attr('id')}_error`));
        });
    }
    
    function limpiarFormularioEditar() {
        $('.error-message').addClass('d-none').text('');
        $('#formEditarIva .form-control').each(function() {
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
                cargarTablaIvas();
            }, 300);
        }
    }
    
    inicializarAplicacion();
    window.cargarTablaIvas = cargarTablaIvas;
});