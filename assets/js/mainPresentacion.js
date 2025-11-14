$(document).ready(function(){
    console.log('Script de gestión de presentaciones cargado');
    
    const expNombre = /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{2,100}$/;

    const estilos = {
        valido: '2px solid #28a745',
        invalido: '2px solid #dc3545',
        normal: '1px solid #ced4da'
    };

    function inicializarAplicacion() {
        cargarTablaPresentaciones();
        inicializarEventos();
        inicializarEventosEliminar();
        inicializarValidaciones();
        inicializarPrevencionEntrada();
    }

    function cargarTablaPresentaciones() {
        console.log('Cargando tabla de presentaciones...');
        $.ajax({
            url: 'index.php?c=PresentacionControlador&m=listarAjax',
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                console.log('Respuesta recibida:', response);
                if (response.success) {
                    renderizarTablaPresentaciones(response.data);
                } else {
                    mostrarErrorTabla(response.details);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar:', error);
                mostrarErrorTabla('Error al cargar las presentaciones: ' + error);
            }
        });
    }
    
    function renderizarTablaPresentaciones(presentaciones) {
        console.log('Renderizando tabla con:', presentaciones);
      
        const tbody = $('#presentacion').find('tbody');
        tbody.empty();
        
        if (presentaciones.length === 0) {
            tbody.append(`
                <tr>
                    <td colspan="3" class="text-center text-muted">
                        <i class="fas fa-box fa-2x mb-2"></i>
                        <p>No hay presentaciones registradas</p>
                    </td>
                </tr>
            `);
            return;
        }
        
        presentaciones.forEach(presentacion => {
            const fila = `
                <tr>
                    <td>${escapeHtml(presentacion.id_pres)}</td>
                    <td>${escapeHtml(presentacion.nombre)}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <button data-id="${presentacion.id_pres}" type="button" class="btn btn-primary btn-sm btn-editar-presentacion">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" 
                                class="btn btn-danger btn-sm btn-eliminar-presentacion" 
                                data-id="${presentacion.id_pres}" 
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
        $('#presentacion').find('tbody').html(`
            <tr>
                <td colspan="3" class="text-center text-danger">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                    <p>${mensaje}</p>
                    <button class="btn btn-sm btn-primary mt-2" onclick="cargarTablaPresentaciones()">
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
        $('#modalPresentacion .btn-secondary').on('click', function() {
            limpiarFormularioCrear();
        });
        
        $('#modalEditarPresentacion .btn-secondary').on('click', function() {
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
        $('.btn-editar-presentacion').off('click').on('click', function() {
            const id = $(this).data('id');
            cargarDatosEditar(id);
        });

        $('.btn-eliminar-presentacion').off('click').on('click', function() {
            const id = $(this).data('id');
            mostrarModalEliminar(id);
        });
    }
    
    function inicializarEventosEliminar() {
        $('#btnEliminarConfirmadoPresentacion').off('click').on('click', function(e) {
            e.preventDefault();
            ejecutarEliminacion();
        });
    }
    
    function inicializarPrevencionEntrada() {
        $('#crear_presentacion, #editar_presentacion').on('input', function() {
            let value = $(this).val().replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/g, '');
            if (value.length > 100) value = value.substring(0, 100);
            $(this).val(value);
        });
    }
    
    function inicializarValidaciones() {
        $('#crear_presentacion').on('input', validarNombreCrear);
        $('#editar_presentacion').on('input', validarNombreEditar);
    }

    function validarNombreCrear() {
        const nombre = $('#crear_presentacion').val().trim();
        const errorElement = $('#presentacion_error');
        
        if (!nombre) {
            mostrarErrorCampo('crear_presentacion', 'El nombre de la presentación es obligatorio', errorElement);
            return false;
        } else if (!expNombre.test(nombre)) {
            mostrarErrorCampo('crear_presentacion', 'El nombre solo puede contener letras y espacios (2-100 caracteres)', errorElement);
            return false;
        } else {
            mostrarExitoCampo('crear_presentacion', errorElement);
            return true;
        }
    }

    function validarNombreEditar() {
        const nombre = $('#editar_presentacion').val().trim();
        const errorElement = $('#editar_presentacion_error');
        
        if (!nombre) {
            mostrarErrorCampo('editar_presentacion', 'El nombre de la presentación es obligatorio', errorElement);
            return false;
        } else if (!expNombre.test(nombre)) {
            mostrarErrorCampo('editar_presentacion', 'El nombre solo puede contener letras y espacios (2-100 caracteres)', errorElement);
            return false;
        } else {
            mostrarExitoCampo('editar_presentacion', errorElement);
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
            url: 'index.php?c=PresentacionControlador&m=obtenerPresentacionAjax&id_pres=' + id,
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response) {
                    $('#id_pres').val(response.id_pres);
                    $('#editar_presentacion').val(response.nombre || '');
                    
                    mostrarExitoCampo('editar_presentacion', $('#editar_presentacion_error'));
                    
                    $('#modalEditarPresentacion').modal('show');
                } else {
                    mostrarMensaje('error', 'Error', 'No se pudieron cargar los datos de la presentación');
                }
            },
            error: function(xhr, status, error) {
                mostrarMensaje('error', 'Error', 'No se pudieron cargar los datos de la presentación: ' + error);
            }
        });
    }
    
    function mostrarModalEliminar(id) {
        $('#btnEliminarConfirmadoPresentacion').data('id', id);
        $('#confirmarEliminarModalPresentacion').modal('show');
    }
    
    function ejecutarEliminacion() {
        const id = $('#btnEliminarConfirmadoPresentacion').data('id');
        
        if (!id) {
            mostrarMensaje('error', 'Error', 'No se especificó el ID de la presentación a eliminar');
            return;
        }
        
        $.ajax({
            url: 'index.php?c=PresentacionControlador&m=eliminarAjax',
            type: 'POST',
            data: { id_pres: id },
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            beforeSend: function() {
                $('#btnEliminarConfirmadoPresentacion').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Eliminando...');
            },
            success: function(response) {
                $('#btnEliminarConfirmadoPresentacion').prop('disabled', false).html('<i class="fas fa-check me-2"></i> Confirmar');
                
                if (response.success) {
                    $('#confirmarEliminarModalPresentacion').modal('hide');
                    mostrarMensaje('success', response.message, response.details);
                    cargarTablaPresentaciones();
                } else {
                    mostrarMensaje('error', response.message, response.details);
                }
            },
            error: function(xhr, status, error) {
                $('#btnEliminarConfirmadoPresentacion').prop('disabled', false).html('<i class="fas fa-check me-2"></i> Confirmar');
                mostrarMensaje('error', 'Error', 'No se pudo eliminar la presentación: ' + error);
            }
        });
    }
    
    function validarFormularioCreacion() {
        let isValid = true;
        
        if (!validarNombreCrear()) isValid = false;
        
        return isValid;
    }
    
    function validarFormularioEdicion() {
        let isValid = true;
        
        if (!validarNombreEditar()) isValid = false;
        
        return isValid;
    }
    
    $('#formPresentacion').on('submit', function(e){
        e.preventDefault();
        
        if (!validarFormularioCreacion()) {
            return;
        }

        const formData = $(this).serialize();
        
        $.ajax({
            url: 'index.php?c=PresentacionControlador&m=guardarAjax',
            type: 'POST',
            data: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            beforeSend: function() {
                $('#formPresentacion button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Guardando...');
            },
            success: function(response) {
                $('#formPresentacion button[type="submit"]').prop('disabled', false).html('<i class="fas fa-save me-2"></i> Guardar Presentación');
                
                if (response.success) {
                    $('#modalPresentacion').modal('hide');
                    $('#formPresentacion')[0].reset();
                    mostrarMensaje('success', response.message, response.details);
                    cargarTablaPresentaciones();
                } else {
                    const detalles = response.details || response.message;
                    mostrarMensaje('error', response.message, response.details);
                }
            },
            error: function(xhr, status, error) {
                $('#formPresentacion button[type="submit"]').prop('disabled', false).html('<i class="fas fa-save me-2"></i> Guardar Presentación');
                mostrarMensaje('error', 'Error', 'No se pudo registrar la presentación: ' + error);
            }
        });
    });
    
    $('#formEditarPresentacion').on('submit', function(e){
        e.preventDefault();
        
        if (!validarFormularioEdicion()) {
            return;
        }

        const formData = $(this).serialize();
        
        $.ajax({
            url: 'index.php?c=PresentacionControlador&m=actualizarAjax',
            type: 'POST',
            data: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            beforeSend: function() {
                $('#formEditarPresentacion button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Guardando...');
            },
            success: function(response) {
                $('#formEditarPresentacion button[type="submit"]').prop('disabled', false).html('<i class="fas fa-save me-2"></i> Guardar Cambios');
                
                if (response.success) {
                    $('#modalEditarPresentacion').modal('hide');
                    mostrarMensaje('success', response.message, response.details);
                    cargarTablaPresentaciones();
                } else {
                    const detalles = response.details || response.message;
                    mostrarMensaje('error', response.message, response.details);
                }
            },
            error: function(xhr, status, error) {
                $('#formEditarPresentacion button[type="submit"]').prop('disabled', false).html('<i class="fas fa-save me-2"></i> Guardar Cambios');
                mostrarMensaje('error', 'Error', 'No se pudo actualizar la presentación: ' + error);
            }
        });
    });
    
    function limpiarFormularioCrear() {
        $('#formPresentacion')[0].reset();
        $('.error-message').addClass('d-none').text('');
        $('#formPresentacion .form-control').each(function() {
            resetearEstiloCampo($(this).attr('id'), $(`#${$(this).attr('id')}_error`));
        });
    }
    
    function limpiarFormularioEditar() {
        $('.error-message').addClass('d-none').text('');
        $('#formEditarPresentacion .form-control').each(function() {
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
                cargarTablaPresentaciones();
            }, 300);
        }
    }
    
    inicializarAplicacion();
    window.cargarTablaPresentaciones = cargarTablaPresentaciones;
});