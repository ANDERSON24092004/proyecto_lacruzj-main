$(document).ready(function(){
    console.log('Script de gestión de materia prima cargado');
    
    const expNombre = /^[A-Za-zÁáÉéÍíÓóÚúÑñ\s]{2,100}$/;
    const expStock = /^\d+$/;
    const expCosto = /^\d+(\.\d{1,2})?$/;
    const expSoloLetras = /^[A-Za-zÁáÉéÍíÓóÚúÑñ\s]*$/;

    const estilos = {
        valido: '2px solid #28a745',
        invalido: '2px solid #dc3545',
        normal: '1px solid #ced4da'
    };

    function inicializarAplicacion() {
        cargarTablaMaterias();
        inicializarEventos();
        inicializarEventosEliminar();
        inicializarValidaciones();
        inicializarPrevencionEntrada();
    }

    function cargarTablaMaterias() {
        $.ajax({
            url: 'index.php?c=MateriaPrimaControlador&m=listar',
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    renderizarTablaMaterias(response.data);
                } else {
                    mostrarErrorTabla(response.details);
                }
            },
            error: function(xhr, status, error) {
                mostrarErrorTabla('Error al cargar las materias primas: ' + error);
            }
        });
    }
    
    function renderizarTablaMaterias(materias) {
        const tbody = $('table tbody');
        tbody.empty();
        
        if (materias.length === 0) {
            tbody.append(`
                <tr>
                    <td colspan="6" class="text-center text-muted">
                        <i class="fas fa-boxes fa-2x mb-2"></i>
                        <p>No hay materias primas registradas</p>
                    </td>
                </tr>
            `);
            return;
        }
        
        materias.forEach(materia => {
            const fila = `
                <tr>
                    <td>${escapeHtml(materia.id_materia)}</td>
                    <td>${escapeHtml(materia.nombre)}</td>
                    <td>${escapeHtml(materia.unidad_medida)}</td>
                    <td>${escapeHtml(materia.stock)}</td>
                    <td>$${parseFloat(materia.costo).toFixed(2)}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <button data-id="${materia.id_materia}" type="button" class="btn btn-primary btn-sm btn-editar-materia">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" 
                                class="btn btn-danger btn-sm btn-eliminar-materia" 
                                data-id="${materia.id_materia}" 
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
        $('table tbody').html(`
            <tr>
                <td colspan="6" class="text-center text-danger">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                    <p>${mensaje}</p>
                    <button class="btn btn-sm btn-primary mt-2" onclick="cargarTablaMaterias()">
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
        $('#registrarMateriaModal .btn-secondary').on('click', function() {
            limpiarFormularioCrear();
        });
        
        $('#editarMateriaModal .btn-secondary').on('click', function() {
            limpiarFormularioEditar();
        });
        
        $('#registrarMateriaModal').on('hidden.bs.modal', function () {
            restaurarScroll();
        });
        
        $('#editarMateriaModal').on('hidden.bs.modal', function () {
            restaurarScroll();
        });
        
        $('#confirmarEliminarModal').on('hidden.bs.modal', function () {
            restaurarScroll();
        });
        
        $('#mensajeModal').on('hidden.bs.modal', function () {
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
        $('.btn-editar-materia').off('click').on('click', function() {
            const id = $(this).data('id');
            cargarDatosEditar(id);
        });

        $('.btn-eliminar-materia').off('click').on('click', function() {
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
        $('#nombre, #edit_nombre').on('input', function() {
            let value = $(this).val().replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/g, '');
            if (value.length > 100) value = value.substring(0, 100);
            $(this).val(value);
        });

        $('#stock, #edit_stock').on('input', function() {
            let value = $(this).val().replace(/\D/g, '');
            if (value.length > 10) value = value.substring(0, 10);
            $(this).val(value);
        });

        $('#costo, #edit_costo').on('input', function() {
            let value = $(this).val().replace(/[^\d.]/g, '');
            if (value.split('.').length > 2) {
                value = value.substring(0, value.lastIndexOf('.'));
            }
            if (value.length > 10) value = value.substring(0, 10);
            $(this).val(value);
        });
    }
    
    function inicializarValidaciones() {
        $('#nombre').on('input', validarNombreCrear);
        $('#id_unidad_medida').on('change', validarUnidadMedidaCrear);
        $('#stock').on('input', validarStockCrear);
        $('#costo').on('input', validarCostoCrear);

        $('#edit_nombre').on('input', validarNombreEditar);
        $('#edit_id_unidad_medida').on('change', validarUnidadMedidaEditar);
        $('#edit_stock').on('input', validarStockEditar);
        $('#edit_costo').on('input', validarCostoEditar);
    }

    function validarNombreCrear() {
        const nombre = $('#nombre').val().trim();
        const errorElement = $('#nombre_error');
        
        if (!nombre) {
            mostrarErrorCampo('nombre', 'El nombre es obligatorio', errorElement);
            return false;
        } else if (!expNombre.test(nombre)) {
            mostrarErrorCampo('nombre', 'El nombre debe tener entre 2 y 100 caracteres (solo letras)', errorElement);
            return false;
        } else {
            mostrarExitoCampo('nombre', errorElement);
            return true;
        }
    }

    function validarUnidadMedidaCrear() {
        const unidadMedida = $('#id_unidad_medida').val();
        const errorElement = $('#id_unidad_medida_error');
        
        if (!unidadMedida) {
            mostrarErrorCampo('id_unidad_medida', 'La unidad de medida es obligatoria', errorElement);
            return false;
        } else {
            mostrarExitoCampo('id_unidad_medida', errorElement);
            return true;
        }
    }

    function validarStockCrear() {
        const stock = $('#stock').val().trim();
        const errorElement = $('#stock_error');
        
        if (!stock) {
            mostrarErrorCampo('stock', 'El stock es obligatorio', errorElement);
            return false;
        } else if (!expStock.test(stock) || parseInt(stock) < 0) {
            mostrarErrorCampo('stock', 'El stock debe ser un número entero no negativo', errorElement);
            return false;
        } else {
            mostrarExitoCampo('stock', errorElement);
            return true;
        }
    }

    function validarCostoCrear() {
        const costo = $('#costo').val().trim();
        const errorElement = $('#costo_error');
        
        if (!costo) {
            mostrarErrorCampo('costo', 'El costo es obligatorio', errorElement);
            return false;
        } else if (!expCosto.test(costo) || parseFloat(costo) <= 0) {
            mostrarErrorCampo('costo', 'El costo debe ser un número mayor a 0 (ej: 10.50)', errorElement);
            return false;
        } else {
            mostrarExitoCampo('costo', errorElement);
            return true;
        }
    }

    function validarNombreEditar() {
        const nombre = $('#edit_nombre').val().trim();
        const errorElement = $('#edit_nombre_error');
        
        if (!nombre) {
            mostrarErrorCampo('edit_nombre', 'El nombre es obligatorio', errorElement);
            return false;
        } else if (!expNombre.test(nombre)) {
            mostrarErrorCampo('edit_nombre', 'El nombre debe tener entre 2 y 100 caracteres (solo letras)', errorElement);
            return false;
        } else {
            mostrarExitoCampo('edit_nombre', errorElement);
            return true;
        }
    }

    function validarUnidadMedidaEditar() {
        const unidadMedida = $('#edit_id_unidad_medida').val();
        const errorElement = $('#edit_id_unidad_medida_error');
        
        if (!unidadMedida) {
            mostrarErrorCampo('edit_id_unidad_medida', 'La unidad de medida es obligatoria', errorElement);
            return false;
        } else {
            mostrarExitoCampo('edit_id_unidad_medida', errorElement);
            return true;
        }
    }

    function validarStockEditar() {
        const stock = $('#edit_stock').val().trim();
        const errorElement = $('#edit_stock_error');
        
        if (!stock) {
            mostrarErrorCampo('edit_stock', 'El stock es obligatorio', errorElement);
            return false;
        } else if (!expStock.test(stock) || parseInt(stock) < 0) {
            mostrarErrorCampo('edit_stock', 'El stock debe ser un número entero no negativo', errorElement);
            return false;
        } else {
            mostrarExitoCampo('edit_stock', errorElement);
            return true;
        }
    }

    function validarCostoEditar() {
        const costo = $('#edit_costo').val().trim();
        const errorElement = $('#edit_costo_error');
        
        if (!costo) {
            mostrarErrorCampo('edit_costo', 'El costo es obligatorio', errorElement);
            return false;
        } else if (!expCosto.test(costo) || parseFloat(costo) <= 0) {
            mostrarErrorCampo('edit_costo', 'El costo debe ser un número mayor a 0 (ej: 10.50)', errorElement);
            return false;
        } else {
            mostrarExitoCampo('edit_costo', errorElement);
            return true;
        }
    }

    function mostrarErrorCampo(campoId, mensaje, errorElement) {
        $(`#${campoId}`).css('border', estilos.invalido);
        errorElement.removeClass('d-none').text(mensaje);
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
            url: 'index.php?c=MateriaPrimaControlador&m=obtenerMateria&id_materia=' + id,
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response) {
                    $('#edit_id_materia').val(response.id_materia);
                    $('#edit_nombre').val(response.nombre || '');
                    $('#edit_id_unidad_medida').val(response.id_unidad_medida || '');
                    $('#edit_stock').val(response.stock || 0);
                    $('#edit_costo').val(response.costo || 0.00);
                    
                    mostrarExitoCampo('edit_nombre', $('#edit_nombre_error'));
                    mostrarExitoCampo('edit_id_unidad_medida', $('#edit_id_unidad_medida_error'));
                    mostrarExitoCampo('edit_stock', $('#edit_stock_error'));
                    mostrarExitoCampo('edit_costo', $('#edit_costo_error'));
                    
                    $('#editarMateriaModal').modal('show');
                } else {
                    mostrarMensaje('error', 'Error', 'No se pudieron cargar los datos de la materia prima');
                }
            },
            error: function(xhr, status, error) {
                mostrarMensaje('error', 'Error', 'No se pudieron cargar los datos de la materia prima: ' + error);
            }
        });
    }
    
    function mostrarModalEliminar(id) {
        $('#btnEliminarConfirmado').data('id', id);
        $('#confirmarEliminarModal').modal('show');
    }
    
    function ejecutarEliminacion() {
        const id = $('#btnEliminarConfirmado').data('id');
        
        if (!id) {
            mostrarMensaje('error', 'Error', 'No se especificó el ID de la materia prima a eliminar');
            return;
        }
        
        $.ajax({
            url: 'index.php?c=MateriaPrimaControlador&m=eliminar',
            type: 'POST',
            data: { id_materia: id },
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
                    cargarTablaMaterias();
                } else {
                    mostrarMensaje('error', response.message, response.details);
                }
            },
            error: function(xhr, status, error) {
                $('#btnEliminarConfirmado').prop('disabled', false).html('<i class="fas fa-check me-2"></i> Confirmar');
                mostrarMensaje('error', 'Error', 'No se pudo eliminar la materia prima: ' + error);
            }
        });
    }
    
    function validarFormularioCreacion() {
        let isValid = true;
        
        if (!validarNombreCrear()) isValid = false;
        if (!validarUnidadMedidaCrear()) isValid = false;
        if (!validarStockCrear()) isValid = false;
        if (!validarCostoCrear()) isValid = false;
        
        return isValid;
    }
    
    function validarFormularioEdicion() {
        let isValid = true;
        
        if (!validarNombreEditar()) isValid = false;
        if (!validarUnidadMedidaEditar()) isValid = false;
        if (!validarStockEditar()) isValid = false;
        if (!validarCostoEditar()) isValid = false;
        
        return isValid;
    }
    
    $('#formCrearMateria').on('submit', function(e){
        e.preventDefault();
        
        if (!validarFormularioCreacion()) {
            return;
        }

        const formData = $(this).serialize();
        
        $.ajax({
            url: 'index.php?c=MateriaPrimaControlador&m=crear',
            type: 'POST',
            data: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            beforeSend: function() {
                $('#formCrearMateria button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Guardando...');
            },
            success: function(response) {
                $('#formCrearMateria button[type="submit"]').prop('disabled', false).html('<i class="fas fa-save me-2"></i> Guardar Materia Prima');
                
                if (response.success) {
                    $('#registrarMateriaModal').modal('hide');
                    $('#formCrearMateria')[0].reset();
                    mostrarMensaje('success', response.message, response.details);
                    cargarTablaMaterias();
                } else {
                    if (response.details) {
                        if (response.details.includes('nombre')) {
                            mostrarErrorCampo('nombre', response.details, $('#nombre_error'));
                        } else if (response.details.includes('unidad')) {
                            mostrarErrorCampo('id_unidad_medida', response.details, $('#id_unidad_medida_error'));
                        } else {
                            mostrarMensaje('error', response.message, response.details);
                        }
                    } else {
                        mostrarMensaje('error', response.message, response.details);
                    }
                }
            },
            error: function(xhr, status, error) {
                $('#formCrearMateria button[type="submit"]').prop('disabled', false).html('<i class="fas fa-save me-2"></i> Guardar Materia Prima');
                mostrarMensaje('error', 'Error', 'No se pudo registrar la materia prima: ' + error);
            }
        });
    });
    
    $('#formEditarMateria').on('submit', function(e){
        e.preventDefault();
        
        if (!validarFormularioEdicion()) {
            return;
        }

        const formData = $(this).serialize();
        
        $.ajax({
            url: 'index.php?c=MateriaPrimaControlador&m=actualizar',
            type: 'POST',
            data: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            beforeSend: function() {
                $('#formEditarMateria button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Guardando...');
            },
            success: function(response) {
                $('#formEditarMateria button[type="submit"]').prop('disabled', false).html('<i class="fas fa-sync-alt me-2"></i> Actualizar Materia Prima');
                
                if (response.success) {
                    $('#editarMateriaModal').modal('hide');
                    mostrarMensaje('success', response.message, response.details);
                    cargarTablaMaterias();
                } else {
                   
                    if (response.details) {
                        if (response.details.includes('nombre')) {
                            mostrarErrorCampo('edit_nombre', response.details, $('#edit_nombre_error'));
                        } else if (response.details.includes('unidad')) {
                            mostrarErrorCampo('edit_id_unidad_medida', response.details, $('#edit_id_unidad_medida_error'));
                        } else {
                            mostrarMensaje('error', response.message, response.details);
                        }
                    } else {
                        mostrarMensaje('error', response.message, response.details);
                    }
                }
            },
            error: function(xhr, status, error) {
                $('#formEditarMateria button[type="submit"]').prop('disabled', false).html('<i class="fas fa-sync-alt me-2"></i> Actualizar Materia Prima');
                mostrarMensaje('error', 'Error', 'No se pudo actualizar la materia prima: ' + error);
            }
        });
    });
    
    function limpiarFormularioCrear() {
        $('#formCrearMateria')[0].reset();
        $('.error-message').hide().text('');
        $('#formCrearMateria .form-control, #formCrearMateria .form-select').each(function() {
            resetearEstiloCampo($(this).attr('id'), $(`#${$(this).attr('id')}_error`));
        });
    }
    
    function limpiarFormularioEditar() {
        $('.error-message').hide().text('');
        $('#formEditarMateria .form-control, #formEditarMateria .form-select').each(function() {
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
                cargarTablaMaterias();
            }, 300);
        }
    }
    
    inicializarAplicacion();
    window.cargarTablaMaterias = cargarTablaMaterias;
});