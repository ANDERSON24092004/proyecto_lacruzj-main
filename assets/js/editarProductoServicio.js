$(document).ready(function() {
    let materiasPrimasDisponibles = [];
    let formularioModificado = false;

    function inicializarEventosEditar() {
        $('#editarProductoModal').on('change', 'input[name="tipo"]', manejarCambioTipoEditar);
        $('#editarProductoModal').on('change', '#editar_es_fabricado', manejarEsFabricadoEditar);
        $('#editarProductoModal').on('click', '#btnAgregarMateriaEditar', agregarFilaMateriaPrimaEditar);
        $('#editarProductoModal').on('submit', '#formEditarProducto', enviarFormularioEditar);
        $('#editarProductoModal').on('input', '#formEditarProducto', function() {
            formularioModificado = true;
        });
        
        $('#editarProductoModal .btn-secondary').on('click', function() {
            $('#editarProductoModal').modal('hide');
        });
        
        $('#editarProductoModal').on('show.bs.modal', function() {
            formularioModificado = false;
        });
        
        $(document).on('change', '.select-materia-prima-editar', actualizarSelectsMateriasPrimasEditar);
        $(document).on('change', '.select-materia-prima-editar', calcularCostoTotalEditar);
        $(document).on('input', '.cantidad-materia-prima-editar', calcularCostoTotalEditar);
        $(document).on('click', '.btn-eliminar-materia-editar', eliminarFilaMateriaPrimaEditar);
        $('#editar_costo').on('input', calcularPrecioMayorEditar);
        
        inicializarValidacionesTiempoRealEditar();
        
        $(document).on('change', '.select-materia-prima-editar', verificarStockMateriasPrimasEditar);
        $(document).on('input', '.cantidad-materia-prima-editar', verificarStockMateriasPrimasEditar);
    }

    function agregarFilaMateriaPrimaEditar() {
        const container = $('#materiasPrimasContainerEditar');
        const index = $('.fila-materia-prima-editar').length;
        
        const template = document.getElementById('templateMateriaPrimaEditar');
        const fila = template.content.cloneNode(true);
        
        const filaElement = $(fila).find('.fila-materia-prima-editar');
        filaElement.attr('data-index', index);
        
        const selectElement = filaElement.find('.select-materia-prima-editar');
        selectElement.attr('name', `materias_primas[${index}][id_materia]`);
        
        const cantidadElement = filaElement.find('.cantidad-materia-prima-editar');
        cantidadElement.attr('name', `materias_primas[${index}][cantidad]`);
        
        container.append(fila);
        actualizarSelectsMateriasPrimasEditar();
        verificarStockMateriasPrimasEditar();
    }

    function actualizarSelectsMateriasPrimasEditar() {
    console.log('Actualizando selects de materias primas...');
    
    $('.select-materia-prima-editar').each(function() {
        const selectActual = $(this);
        const valorActual = selectActual.val();
        const idActual = selectActual.attr('id');
        
        console.log(`Procesando select ${idActual} con valor:`, valorActual);
        
        selectActual.html('<option value="" selected disabled>Seleccione materia prima</option>');
        
        materiasPrimasDisponibles.forEach(materia => {
            const yaSeleccionada = $('.select-materia-prima-editar').not(selectActual).filter(function() {
                return $(this).val() == materia.id_materia;
            }).length > 0;
            
            if (!yaSeleccionada || materia.id_materia == valorActual) {
                const option = $(`<option value="${materia.id_materia}" data-costo="${materia.costo}">${materia.nombre} (Stock: ${materia.stock})</option>`);
                
                if (materia.id_materia == valorActual) {
                    option.prop('selected', true);
                    console.log(`Estableciendo materia ${materia.id_materia} como seleccionada en ${idActual}`);
                }
                
                selectActual.append(option);
            }
        });
        
        if (valorActual) {
            const costo = selectActual.find('option:selected').data('costo');
            console.log(`Costo para materia ${valorActual}:`, costo);
            selectActual.closest('.fila-materia-prima-editar').find('.costo-unitario-editar').val(costo ? `$${parseFloat(costo).toFixed(2)}` : '');
        }
    });
    
    calcularCostoTotalEditar();
}

    function calcularCostoTotalEditar() {
        let costoTotalMateriasPrimas = 0;
        
        $('.fila-materia-prima-editar').each(function() {
            const select = $(this).find('.select-materia-prima-editar');
            const cantidadInput = $(this).find('.cantidad-materia-prima-editar');
            const costo = select.find('option:selected').data('costo') || 0;
            const cantidad = parseFloat(cantidadInput.val()) || 0;
            
            costoTotalMateriasPrimas += costo * cantidad;
        });
        
        $('#costoTotalValorEditar').text(costoTotalMateriasPrimas.toFixed(2));
        
        if ($('#editar_es_fabricado').is(':checked')) {
            $('#editar_costo').val(costoTotalMateriasPrimas.toFixed(2));
            calcularPrecioMayorEditar();
        }
    }

    function eliminarFilaMateriaPrimaEditar() {
        const fila = $(this).closest('.fila-materia-prima-editar');
        fila.remove();
        reindexarFilasMateriasPrimasEditar();
        actualizarSelectsMateriasPrimasEditar();
        calcularCostoTotalEditar();
        verificarStockMateriasPrimasEditar();
    }

    function reindexarFilasMateriasPrimasEditar() {
        $('.fila-materia-prima-editar').each(function(index) {
            $(this).attr('data-index', index);
            $(this).find('.select-materia-prima-editar').attr('name', `materias_primas[${index}][id_materia]`);
            $(this).find('.cantidad-materia-prima-editar').attr('name', `materias_primas[${index}][cantidad]`);
        });
    }

    function calcularPrecioMayorEditar() {
        const costo = parseFloat($('#editar_costo').val()) || 0;
        const precioMayor = costo * 0.9;
        $('#editar_precio_mayor').val(precioMayor.toFixed(2));
    }

    function manejarCambioTipoEditar() {
        const tipo = $(this).val();
        const camposProducto = $('.campos-producto-editar');
        const esFabricadoCheckbox = $('#editar_es_fabricado');
        const materiasPrimasSection = $('#materiasPrimasSectionEditar');
        const costoInput = $('#editar_costo');
        
        if (tipo === '1') {
            camposProducto.show();
            esFabricadoCheckbox.prop('disabled', false);
            
            if (!esFabricadoCheckbox.is(':checked')) {
                materiasPrimasSection.hide();
                costoInput.prop('readonly', false).removeClass('bg-light');
            }
        } else {
            camposProducto.hide();
            esFabricadoCheckbox.prop('disabled', true).prop('checked', false);
            materiasPrimasSection.hide();
            limpiarMateriasPrimasEditar();
            costoInput.prop('readonly', false).removeClass('bg-light');
        }
        
        calcularPrecioMayorEditar();
        limpiarValidacionesEditar();
    }

    function manejarEsFabricadoEditar() {
        const esFabricado = $(this).is(':checked');
        const materiasPrimasSection = $('#materiasPrimasSectionEditar');
        const costoInput = $('#editar_costo');
        
        if (esFabricado) {
            materiasPrimasSection.show();
            costoInput.prop('readonly', true).addClass('bg-light');
            if ($('.fila-materia-prima-editar').length === 0) {
                agregarFilaMateriaPrimaEditar();
            }
            calcularCostoTotalEditar();
        } else {
            materiasPrimasSection.hide();
            costoInput.prop('readonly', false).removeClass('bg-light');
            limpiarMateriasPrimasEditar();
        }
        limpiarValidacionesEditar();
    }

    function cargarMateriasPrimasDisponiblesEditar() {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: 'index.php?c=ProductoServicioControlador&m=getMateriasPrimas',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        materiasPrimasDisponibles = response.materiasPrimas;
                        resolve();
                    } else {
                        reject(response.error);
                    }
                },
                error: function(xhr, status, error) {
                    reject(error);
                }
            });
        });
    }

    function inicializarValidacionesTiempoRealEditar() {
        $('#editar_nombre').on('input', validarNombreEditar);
        $('#editar_unidad_medida').on('change', validarUnidadMedidaEditar);
        $('#editar_costo').on('input', validarCostoEditar);
        $('#editar_stock').on('input', validarStockEditar);
        $('#editar_precio_mayor').on('input', validarPrecioMayorEditar);
        $('#editar_presentacion').on('change', validarPresentacionEditar);
        
        $(document).on('change', '.select-materia-prima-editar', validarMateriasPrimasEditar);
        $(document).on('input', '.cantidad-materia-prima-editar', validarMateriasPrimasEditar);
    }

    function validarNombreEditar() {
        const nombre = $('#editar_nombre').val().trim();
        const errorElement = $('#editar_nombre_error');
        
        if (!nombre) {
            mostrarErrorCampoEditar('editar_nombre', 'El nombre es obligatorio', errorElement);
            return false;
        } else if (nombre.length > 255) {
            mostrarErrorCampoEditar('editar_nombre', 'El nombre no puede tener más de 255 caracteres', errorElement);
            return false;
        } else {
            mostrarExitoCampoEditar('editar_nombre', errorElement);
            return true;
        }
    }

    function validarUnidadMedidaEditar() {
        const unidadMedida = $('#editar_unidad_medida').val();
        const errorElement = $('#editar_unidad_medida_error');
        
        if (!unidadMedida) {
            mostrarErrorCampoEditar('editar_unidad_medida', 'La unidad de medida es obligatoria', errorElement);
            return false;
        } else {
            mostrarExitoCampoEditar('editar_unidad_medida', errorElement);
            return true;
        }
    }

    function validarCostoEditar() {
        const costo = parseFloat($('#editar_costo').val());
        const errorElement = $('#editar_costo_error');
        
        if (isNaN(costo) || costo <= 0) {
            mostrarErrorCampoEditar('editar_costo', 'El costo debe ser un número mayor que 0', errorElement);
            return false;
        } else if (costo > 999999.99) {
            mostrarErrorCampoEditar('editar_costo', 'El costo no puede ser mayor a 999,999.99', errorElement);
            return false;
        } else {
            mostrarExitoCampoEditar('editar_costo', errorElement);
            return true;
        }
    }

    function validarStockEditar() {
        const stock = parseInt($('#editar_stock').val());
        const errorElement = $('#editar_stock_error');
        
        if (isNaN(stock) || stock < 0) {
            mostrarErrorCampoEditar('editar_stock', 'El stock debe ser un número mayor o igual a 0', errorElement);
            return false;
        } else if (stock > 999999) {
            mostrarErrorCampoEditar('editar_stock', 'El stock no puede ser mayor a 999,999', errorElement);
            return false;
        } else {
            mostrarExitoCampoEditar('editar_stock', errorElement);
            return true;
        }
    }

    function validarPrecioMayorEditar() {
        const precioMayor = parseFloat($('#editar_precio_mayor').val());
        const errorElement = $('#editar_precio_mayor_error');
        const costo = parseFloat($('#editar_costo').val()) || 0;
        
        if (isNaN(precioMayor) || precioMayor <= 0) {
            mostrarErrorCampoEditar('editar_precio_mayor', 'El precio al por mayor debe ser un número mayor que 0', errorElement);
            return false;
        } else if (precioMayor > 999999.99) {
            mostrarErrorCampoEditar('editar_precio_mayor', 'El precio al por mayor no puede ser mayor a 999,999.99', errorElement);
            return false;
        } else if (precioMayor >= costo) {
            mostrarErrorCampoEditar('editar_precio_mayor', 'El precio al por mayor debe ser menor al costo', errorElement);
            return false;
        } else {
            mostrarExitoCampoEditar('editar_precio_mayor', errorElement);
            return true;
        }
    }

    function validarPresentacionEditar() {
        const presentacion = $('#editar_presentacion').val();
        const errorElement = $('#editar_presentacion_error');
        
        if (!presentacion) {
            mostrarErrorCampoEditar('editar_presentacion', 'La presentación es obligatoria para productos', errorElement);
            return false;
        } else {
            mostrarExitoCampoEditar('editar_presentacion', errorElement);
            return true;
        }
    }

    function validarMateriasPrimasEditar() {
        let isValid = true;
        let tieneMateriasValidas = false;
        
        if (!$('#editar_es_fabricado').is(':checked')) {
            return true;
        }
        
        $('.fila-materia-prima-editar').each(function() {
            const select = $(this).find('.select-materia-prima-editar');
            const cantidadInput = $(this).find('.cantidad-materia-prima-editar');
            const idMateria = select.val();
            const cantidad = parseFloat(cantidadInput.val());
            
            if (!idMateria) {
                mostrarErrorCampoElementEditar(select, 'Debe seleccionar una materia prima');
                isValid = false;
            } else {
                mostrarExitoCampoElementEditar(select);
            }
            
            if (isNaN(cantidad) || cantidad <= 0) {
                mostrarErrorCampoElementEditar(cantidadInput, 'La cantidad debe ser mayor a 0');
                isValid = false;
            } else if (cantidad > 999999.99) {
                mostrarErrorCampoElementEditar(cantidadInput, 'La cantidad no puede ser mayor a 999,999.99');
                isValid = false;
            } else {
                mostrarExitoCampoElementEditar(cantidadInput);
                tieneMateriasValidas = true;
            }
        });
        
        if ($('#editar_es_fabricado').is(':checked') && !tieneMateriasValidas) {
            mostrarMensajeEditar('error', 'Debe agregar al menos una materia prima válida para productos fabricados');
            isValid = false;
        }
        
        return isValid;
    }

    function mostrarErrorCampoElementEditar(element, mensaje) {
        element.addClass('is-invalid').removeClass('is-valid');
        let errorElement = element.next('.error-message-specific');
        if (errorElement.length === 0) {
            errorElement = $('<div class="error-message-specific text-danger small mt-1"></div>');
            element.after(errorElement);
        }
        errorElement.html(`<i class="fas fa-exclamation-circle me-1"></i>${mensaje}`).removeClass('d-none');
    }

    function mostrarExitoCampoElementEditar(element) {
        element.removeClass('is-invalid').addClass('is-valid');
        element.next('.error-message-specific').addClass('d-none').text('');
    }

    function mostrarErrorCampoEditar(campoId, mensaje, errorElement) {
        $(`#${campoId}`).addClass('is-invalid').removeClass('is-valid');
        if (errorElement) {
            errorElement.html(`<i class="fas fa-exclamation-circle me-1"></i>${mensaje}`).removeClass('d-none');
        }
    }

    function mostrarExitoCampoEditar(campoId, errorElement) {
        $(`#${campoId}`).removeClass('is-invalid').addClass('is-valid');
        if (errorElement) {
            errorElement.addClass('d-none').text('');
        }
    }

    function limpiarValidacionesEditar() {
        $('#editarProductoModal .is-invalid').removeClass('is-invalid');
        $('#editarProductoModal .is-valid').removeClass('is-valid');
        $('#editarProductoModal .error-message').addClass('d-none').text('');
        $('#editarProductoModal .error-message-specific').addClass('d-none').text('');
        $('#editar_btnerror').addClass('d-none').text('');
    }

    function validarFormularioEditar() {
        let isValid = true;
        
        if (!validarNombreEditar()) isValid = false;
        if (!validarUnidadMedidaEditar()) isValid = false;
        if (!validarCostoEditar()) isValid = false;
        
        const tipo = $('#editarProductoModal input[name="tipo"]:checked').val();
        
        if (tipo === '1') {
            if (!validarStockEditar()) isValid = false;
            if (!validarPrecioMayorEditar()) isValid = false;
            if (!validarPresentacionEditar()) isValid = false;
            
            if ($('#editar_es_fabricado').is(':checked')) {
                if (!validarMateriasPrimasEditar()) isValid = false;
                if (!verificarStockMateriasPrimasEditar()) isValid = false;
            }
        }
        
        return isValid;
    }

    function enviarFormularioEditar(e) {
        e.preventDefault();
        
        if (!validarFormularioEditar()) {
            mostrarMensajeEditar('error', 'Por favor, corrija los errores en el formulario antes de enviar');
            return;
        }
        
        const formData = new FormData(this);
        const tipo = $('#editarProductoModal input[name="tipo"]:checked').val();
        
        if (tipo === '1' && $('#editar_es_fabricado').is(':checked')) {
            const materiasPrimasData = [];
            
            $('.fila-materia-prima-editar').each(function() {
                const idMateria = $(this).find('.select-materia-prima-editar').val();
                const cantidad = $(this).find('.cantidad-materia-prima-editar').val();
                
                if (idMateria && cantidad) {
                    materiasPrimasData.push({
                        id_materia: idMateria,
                        cantidad: parseFloat(cantidad)
                    });
                }
            });
            
            if (materiasPrimasData.length > 0) {
                formData.append('materias_primas_json', JSON.stringify(materiasPrimasData));
            }
        }
  
        $('#btnActualizar').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Guardando...');
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                $('#btnActualizar').prop('disabled', false).html('<i class="fas fa-save me-1"></i> Actualizar');
                
                if (response.success) {
                    if (typeof window.mostrarMensaje === 'function') {
                        window.mostrarMensaje('success', response.message, 'El producto/servicio se ha actualizado correctamente');
                    } else {
                        mostrarMensajeEditar('success', response.message);
                    }
                    
                    formularioModificado = false;
                    $('#editarProductoModal').modal('hide');
                    
                    if(typeof cargarTablaProductosServicios === 'function'){
                        cargarTablaProductosServicios();
                    } else if(typeof window.cargarTablaProductosServicios === 'function'){
                        window.cargarTablaProductosServicios();
                    }
                } else {
                    if (typeof window.mostrarMensaje === 'function') {
                        window.mostrarMensaje('error', response.message, 'No se pudo completar la operación');
                    } else {
                        mostrarMensajeEditar('error', response.message);
                    }
                }
            },
            error: function(xhr, status, error) {
                $('#btnActualizar').prop('disabled', false).html('<i class="fas fa-save me-1"></i> Actualizar');
                
                let errorMessage = 'Error de conexión al actualizar el producto';
                
                if (xhr.responseText) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        errorMessage = response.message || errorMessage;
                    } catch (e) {
                        if (xhr.status === 500) {
                            errorMessage = 'Error interno del servidor. Por favor, contacte al administrador.';
                        } else if (xhr.status === 0) {
                            errorMessage = 'Error de conexión. Verifique su conexión a internet.';
                        }
                    }
                }
                
                if (typeof window.mostrarMensaje === 'function') {
                    window.mostrarMensaje('error', errorMessage, 'Error de comunicación con el servidor');
                } else {
                    mostrarMensajeEditar('error', errorMessage);
                }
                console.error('Error detallado:', xhr.responseText);
            }
        });
    }

    function mostrarMensajeEditar(tipo, mensaje) {
        const alerta = $('#editar_btnerror');
        alerta.removeClass('d-none alert-success alert-danger')
              .addClass(tipo === 'success' ? 'alert-success' : 'alert-danger')
              .html(`<i class="fas ${tipo === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'} me-2"></i>${mensaje}`)
              .show();
        
        alerta[0].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        
        if (tipo === 'success') {
            setTimeout(() => {
                alerta.fadeOut();
            }, 5000);
        }
    }

    function limpiarModalEditar() {
        $('#formEditarProducto')[0].reset();
        $('#materiasPrimasContainerEditar').empty();
        $('#costoTotalValorEditar').text('0.00');
        $('.campos-producto-editar').show();
        $('#editar_es_fabricado').prop('disabled', false).prop('checked', false);
        $('#materiasPrimasSectionEditar').hide();
        $('#editar_tipo_producto').prop('checked', true);
        $('#editar_btnerror').addClass('d-none').text('');
        $('#editar_costo').prop('readonly', false).removeClass('bg-light');
        calcularPrecioMayorEditar();
        limpiarValidacionesEditar();
        formularioModificado = false;
    }

    function limpiarMateriasPrimasEditar() {
        $('#materiasPrimasContainerEditar').empty();
        $('#costoTotalValorEditar').text('0.00');
    }

    function verificarStockMateriasPrimasEditar() {
        let hayStockInsuficiente = false;
        let mensajeStock = '';
        
        $('.fila-materia-prima-editar').each(function() {
            const select = $(this).find('.select-materia-prima-editar');
            const cantidadInput = $(this).find('.cantidad-materia-prima-editar');
            const materiaId = select.val();
            const cantidad = parseFloat(cantidadInput.val()) || 0;
            
            if (materiaId && cantidad > 0) {
                const materia = materiasPrimasDisponibles.find(m => m.id_materia == materiaId);
                if (materia && cantidad > materia.stock) {
                    hayStockInsuficiente = true;
                    cantidadInput.addClass('is-invalid');
                    mensajeStock = `Stock insuficiente para "${materia.nombre}". Disponible: ${materia.stock}`;
                } else {
                    cantidadInput.removeClass('is-invalid');
                }
            } else {
                cantidadInput.removeClass('is-invalid');
            }
        });
        
        if (hayStockInsuficiente) {
            $('#btnActualizar').prop('disabled', true);
            if (mensajeStock) {
                mostrarMensajeEditar('error', mensajeStock);
            }
        } else {
            $('#btnActualizar').prop('disabled', false);
        }
        
        return !hayStockInsuficiente;
    }

    window.cargarDatosEditar = function(id) {
        $('#btnActualizar').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Cargando...');
        
        $('#editarProductoModal').modal('show');
        
        cargarMateriasPrimasDisponiblesEditar().then(() => {
            $.ajax({
                url: 'index.php?c=ProductoServicioControlador&m=editar&id=' + id,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        if (response.data && typeof response.data === 'object') {
                            mostrarDatosProducto(response.data);
                            $('#btnActualizar').prop('disabled', false).html('<i class="fas fa-save me-1"></i> Guardar');
                        } else {
                            mostrarMensajeEditar('error', 'Estructura de datos inválida del servidor');
                            $('#btnActualizar').prop('disabled', false).html('<i class="fas fa-save me-1"></i> Guardar');
                        }
                    } else {
                        mostrarMensajeEditar('error', response.message || 'Error al cargar los datos');
                        $('#btnActualizar').prop('disabled', false).html('<i class="fas fa-save me-1"></i> Guardar');
                    }
                },
                error: function(xhr, status, error) {
                    let errorMessage = 'Error al cargar los datos del producto';
                    try {
                        const responseText = xhr.responseText;
                        if (responseText.includes('<!DOCTYPE')) {
                            errorMessage = 'Error del servidor: Respuesta no es JSON. Verifique la configuración.';
                        } else {
                            const jsonResponse = JSON.parse(responseText);
                            if (jsonResponse.message) {
                                errorMessage = jsonResponse.message;
                            }
                        }
                    } catch (e) {
                        errorMessage = 'Error en el formato de respuesta del servidor';
                    }
                    
                    mostrarMensajeEditar('error', errorMessage);
                    $('#btnActualizar').prop('disabled', false).html('<i class="fas fa-save me-1"></i> Guardar');
                }
            });
        }).catch((error) => {
            console.error('Error al cargar materias primas:', error);
            mostrarMensajeEditar('error', 'Error al cargar materias primas: ' + error);
            $('#btnActualizar').prop('disabled', false).html('<i class="fas fa-save me-1"></i> Guardar');
        });
    };

function mostrarDatosProducto(producto) {
    console.log('=== MOSTRANDO DATOS DEL PRODUCTO ===');
    console.log('Producto completo:', producto);
    
    limpiarModalEditar();

    $('#editar_id_inv').val(producto.id_inv || '');
    $('#editar_nombre').val(producto.nombre || '');
    $('#editar_unidad_medida').val(producto.id_unidad_medida || '');
    
    const costo = parseFloat(producto.costo) || 0;
    $('#editar_costo').val(costo.toFixed(2));
    
    if (parseInt(producto.tipo) === 1) {
        $('#editar_tipo_producto').prop('checked', true);
        $('.campos-producto-editar').show();
        
        const stock = parseInt(producto.stock) || 0;
        $('#editar_stock').val(stock);
        
        const precioMayor = parseFloat(producto.costo_mayor) || 0;
        $('#editar_precio_mayor').val(precioMayor.toFixed(2));
        
        $('#editar_presentacion').val(producto.presentacion || '');
        
        const esFabricado = parseInt(producto.es_fabricado) === 1;
        $('#editar_es_fabricado').prop('checked', esFabricado);
        
        console.log('Producto es fabricado:', esFabricado);
        console.log('Materias primas del producto:', producto.materias_primas);
        
        if (esFabricado) {
            $('#editar_costo').prop('readonly', true).addClass('bg-light');
            $('#materiasPrimasSectionEditar').show();
            
            console.log('Sección de materias primas mostrada');

            if (producto.materias_primas && producto.materias_primas.length > 0) {
                console.log('Iniciando carga de materias primas...');
                cargarMateriasPrimasEditar(producto.materias_primas);
            } else {
                console.log('Producto fabricado pero sin materias primas, agregando fila vacía');
                agregarFilaMateriaPrimaEditar();
            }
        } else {
            $('#materiasPrimasSectionEditar').hide();
            $('#editar_costo').prop('readonly', false).removeClass('bg-light');
        }
    } else {
        $('#editar_tipo_servicio').prop('checked', true);
        $('.campos-producto-editar').hide();
        $('#materiasPrimasSectionEditar').hide();
        $('#editar_es_fabricado').prop('disabled', true).prop('checked', false);
        $('#editar_costo').prop('readonly', false).removeClass('bg-light');
    }
    
    calcularPrecioMayorEditar();
    console.log('=== FIN DE MOSTRAR DATOS DEL PRODUCTO ===');
}

function cargarMateriasPrimasEditar(materiasPrimas) {
    const container = $('#materiasPrimasContainerEditar');
    container.empty();

    console.log('Cargando materias primas para editar:', materiasPrimas);
    console.log('Materias primas disponibles:', materiasPrimasDisponibles);

    if (materiasPrimas && materiasPrimas.length > 0) {
        materiasPrimas.forEach((materia, index) => {
            console.log(`Procesando materia prima ${index}:`, materia);
            
            const template = document.getElementById('templateMateriaPrimaEditar');
            const fila = template.content.cloneNode(true);
            
            const filaElement = $(fila).find('.fila-materia-prima-editar');
            filaElement.attr('data-index', index);
            
            const selectElement = filaElement.find('.select-materia-prima-editar');
            selectElement.attr('name', `materias_primas[${index}][id_materia]`);
            selectElement.attr('id', `materia_select_${index}`);
            
            const cantidadElement = filaElement.find('.cantidad-materia-prima-editar');
            cantidadElement.attr('name', `materias_primas[${index}][cantidad]`);
            cantidadElement.attr('id', `materia_cantidad_${index}`);
            cantidadElement.val(materia.cantidad || 0);
            
            container.append(fila);
            
            const currentSelect = $(`#materia_select_${index}`);
            if (currentSelect.length) {
                poblarSelectMateriaPrima(currentSelect, materia.id_materia);
            
                currentSelect.val(materia.id_materia);
            
                const materiaData = materiasPrimasDisponibles.find(m => m.id_materia == materia.id_materia);
                console.log(`Materia data encontrada para ID ${materia.id_materia}:`, materiaData);
                
                if (materiaData) {
                    currentSelect.closest('.fila-materia-prima-editar').find('.costo-unitario-editar').val(`$${parseFloat(materiaData.costo).toFixed(2)}`);
                } else {
                    currentSelect.closest('.fila-materia-prima-editar').find('.costo-unitario-editar').val(`$${parseFloat(materia.costo || 0).toFixed(2)}`);
                }
            }
        });

        setTimeout(() => {
            console.log('Actualizando selects y calculando costo total...');
            actualizarSelectsMateriasPrimasEditar();
            calcularCostoTotalEditar();
            verificarStockMateriasPrimasEditar();
        }, 100);
    } else {
        console.log('No hay materias primas para este producto fabricado');
        if ($('#editar_es_fabricado').is(':checked')) {
            console.log('Agregando fila vacía para producto fabricado sin materias primas');
            agregarFilaMateriaPrimaEditar();
        }
    }
}

function poblarSelectMateriaPrima(selectElement, idMateriaSeleccionada) {
    selectElement.html('<option value="" selected disabled>Seleccione materia prima</option>');
    
    materiasPrimasDisponibles.forEach(materia => {
        const yaSeleccionada = $('.select-materia-prima-editar').not(selectElement).filter(function() {
            return $(this).val() == materia.id_materia;
        }).length > 0;
        
        if (!yaSeleccionada || materia.id_materia == idMateriaSeleccionada) {
            const option = $(`<option value="${materia.id_materia}" data-costo="${materia.costo}">${materia.nombre} (Stock: ${materia.stock})</option>`);
            
            if (materia.id_materia == idMateriaSeleccionada) {
                option.prop('selected', true);
            }
            
            selectElement.append(option);
        }
    });
}
    inicializarEventosEditar();
});