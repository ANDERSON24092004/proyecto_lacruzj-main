$(document).ready(function(){
    console.log('Script de gestión de unidades de medida cargado');
    
    const expNombre = /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{2,30}$/;

    const estilos = {
        valido: '2px solid #28a745',
        invalido: '2px solid #dc3545',
        normal: '1px solid #ced4da'
    };

    function inicializarAplicacion() {
        cargarTablaUnidades();
        inicializarEventos();
        inicializarEventosEliminar();
        inicializarValidaciones();
        inicializarPrevencionEntrada();
    }

    function cargarTablaUnidades() {
        $.ajax({
            url: 'index.php?c=UnidadMedidaControlador&m=listarAjax',
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    renderizarTablaUnidades(response.data);
                } else {
                    mostrarErrorTabla(response.details);
                }
            },
            error: function(xhr, status, error) {
                mostrarErrorTabla('Error al cargar las unidades de medida: ' + error);
            }
        });
    }
    
    function renderizarTablaUnidades(unidades) {
        const tbody = $('#tbody-unidades');
        tbody.empty();
        
        if (unidades.length === 0) {
            tbody.append(`
                <tr>
                    <td colspan="3" class="text-center text-muted">
                        <i class="fas fa-ruler-combined fa-2x mb-2"></i>
                        <p>No hay unidades de medida registradas</p>
                    </td>
                </tr>
            `);
            return;
        }
        
        unidades.forEach(unidad => {
            const fila = `
                <tr>
                    <td>${escapeHtml(unidad.id_unidad_medida)}</td>
                    <td>${escapeHtml(unidad.nombre)}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <button data-id="${unidad.id_unidad_medida}" 
                                    data-nombre="${escapeHtml(unidad.nombre)}" 
                                    type="button" 
                                    class="btn btn-primary btn-sm btn-editar-unidad">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" 
                                class="btn btn-danger btn-sm btn-eliminar-unidad" 
                                data-id="${unidad.id_unidad_medida}" 
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
        $('#tbody-unidades').html(`
            <tr>
                <td colspan="3" class="text-center text-danger">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                    <p>${mensaje}</p>
                    <button class="btn btn-sm btn-primary mt-2" onclick="cargarTablaUnidades()">
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
        $('#modalUnidad .btn-secondary').on('click', function() {
            limpiarFormularioCrear();
        });
        
        $('#modalEditarUnidad .btn-secondary').on('click', function() {
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
        $('.btn-editar-unidad').off('click').on('click', function() {
            const id = $(this).data('id');
            const nombre = $(this).data('nombre');
            cargarDatosEditar(id, nombre);
        });

        $('.btn-eliminar-unidad').off('click').on('click', function() {
            const id = $(this).data('id');
            mostrarModalEliminar(id);
        });
    }
    
    function inicializarEventosEliminar() {
        $('#btnEliminarConfirmado').off('click').on('click', function(e) {
            e.preventDefault();
            ejecutarEliminacion();
        });
    }
    
    function inicializarPrevencionEntrada() {
        $('#crear_nombreM, #editar_nombreM').on('input', function() {
            let value = $(this).val().replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/g, '');
            if (value.length > 30) value = value.substring(0, 30);
            $(this).val(value);
        });
    }
    
    function inicializarValidaciones() {
        $('#crear_nombreM').on('input', validarNombreCrear);
        $('#editar_nombreM').on('input', validarNombreEditar);
    }

    function validarNombreCrear() {
        const nombre = $('#crear_nombreM').val().trim();
        const errorElement = $('#nombre_error');
        
        if (!nombre) {
            mostrarErrorCampo('crear_nombreM', 'El nombre de la unidad es obligatorio', errorElement);
            return false;
        } else if (!expNombre.test(nombre)) {
            mostrarErrorCampo('crear_nombreM', 'El nombre solo puede contener letras y espacios (2-30 caracteres)', errorElement);
            return false;
        } else {
            mostrarExitoCampo('crear_nombreM', errorElement);
            return true;
        }
    }

    function validarNombreEditar() {
        const nombre = $('#editar_nombreM').val().trim();
        const errorElement = $('#editar_nombre_error');
        
        if (!nombre) {
            mostrarErrorCampo('editar_nombreM', 'El nombre de la unidad es obligatorio', errorElement);
            return false;
        } else if (!expNombre.test(nombre)) {
            mostrarErrorCampo('editar_nombreM', 'El nombre solo puede contener letras y espacios (2-30 caracteres)', errorElement);
            return false;
        } else {
            mostrarExitoCampo('editar_nombreM', errorElement);
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
    
    function cargarDatosEditar(id, nombre) {
        $('#id_unidad_medida').val(id);
        $('#editar_nombreM').val(nombre || '');
        
        mostrarExitoCampo('editar_nombreM', $('#editar_nombre_error'));
        
        $('#modalEditarUnidad').modal('show');
    }
    
    function mostrarModalEliminar(id) {
        $('#btnEliminarConfirmado').data('id', id);
        $('#confirmarEliminarModal').modal('show');
    }
    
    function ejecutarEliminacion() {
        const id = $('#btnEliminarConfirmado').data('id');
        
        if (!id) {
            mostrarMensaje('error', 'Error', 'No se especificó el ID de la unidad a eliminar');
            return;
        }
        
        $.ajax({
            url: 'index.php?c=UnidadMedidaControlador&m=eliminarAjax',
            type: 'POST',
            data: { id_unidad_medida: id },
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            beforeSend: function() {
                $('#btnEliminarConfirmado').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Eliminando...');
            },
            success: function(response) {
                $('#btnEliminarConfirmado').prop('disabled', false).html('<i class="fas fa-check me-2"></i> Confirmar');
                
                if (response.success) {
                    $('#confirmarEliminarModal').modal('hide');
                    mostrarMensaje('success', response.message, response.details);
                    cargarTablaUnidades();
                } else {
                    mostrarMensaje('error', response.message, response.details);
                }
            },
            error: function(xhr, status, error) {
                $('#btnEliminarConfirmado').prop('disabled', false).html('<i class="fas fa-check me-2"></i> Confirmar');
                mostrarMensaje('error', 'Error', 'No se pudo eliminar la unidad: ' + error);
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
    
    $('#formUnidadM').on('submit', function(e){
        e.preventDefault();
        
        if (!validarFormularioCreacion()) {
            return;
        }

        const formData = $(this).serialize();
        
        $.ajax({
            url: 'index.php?c=UnidadMedidaControlador&m=guardarAjax',
            type: 'POST',
            data: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            beforeSend: function() {
                $('#formUnidadM button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Guardando...');
            },
            success: function(response) {
                $('#formUnidadM button[type="submit"]').prop('disabled', false).html('<i class="fas fa-save me-2"></i> Guardar Unidad');
                
                if (response.success) {
                    $('#modalUnidad').modal('hide');
                    $('#formUnidadM')[0].reset();
                    mostrarMensaje('success', response.message, response.details);
                    cargarTablaUnidades();
                } else {
                    const detalles = response.details || response.message;
                    mostrarMensaje('error', response.message, response.details);
                }
            },
            error: function(xhr, status, error) {
                $('#formUnidadM button[type="submit"]').prop('disabled', false).html('<i class="fas fa-save me-2"></i> Guardar Unidad');
                mostrarMensaje('error', 'Error', 'No se pudo registrar la unidad: ' + error);
            }
        });
    });
    
    $('#formEditarUnidadM').on('submit', function(e){
        e.preventDefault();
        
        if (!validarFormularioEdicion()) {
            return;
        }

        const formData = $(this).serialize();
        
        $.ajax({
            url: 'index.php?c=UnidadMedidaControlador&m=actualizarAjax',
            type: 'POST',
            data: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            beforeSend: function() {
                $('#formEditarUnidadM button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Guardando...');
            },
            success: function(response) {
                $('#formEditarUnidadM button[type="submit"]').prop('disabled', false).html('<i class="fas fa-save me-2"></i> Guardar Cambios');
                
                if (response.success) {
                    $('#modalEditarUnidad').modal('hide');
                    mostrarMensaje('success', response.message, response.details);
                    cargarTablaUnidades();
                } else {
                    const detalles = response.details || response.message;
                    mostrarMensaje('error', response.message, response.details);
                }
            },
            error: function(xhr, status, error) {
                $('#formEditarUnidadM button[type="submit"]').prop('disabled', false).html('<i class="fas fa-save me-2"></i> Guardar Cambios');
                mostrarMensaje('error', 'Error', 'No se pudo actualizar la unidad: ' + error);
            }
        });
    });
    
    function limpiarFormularioCrear() {
        $('#formUnidadM')[0].reset();
        $('.error-message').addClass('d-none').text('');
        $('#formUnidadM .form-control').each(function() {
            resetearEstiloCampo($(this).attr('id'), $(`#${$(this).attr('id')}_error`));
        });
    }
    
    function limpiarFormularioEditar() {
        $('.error-message').addClass('d-none').text('');
        $('#formEditarUnidadM .form-control').each(function() {
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
                cargarTablaUnidades();
            }, 300);
        }
    }
    
    inicializarAplicacion();
    window.cargarTablaUnidades = cargarTablaUnidades;
});