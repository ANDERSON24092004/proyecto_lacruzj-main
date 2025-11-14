$(document).ready(function() {
    let materiasPrimasDisponibles = [];
    let materiasPrimasOriginales = [];
    let productoId = null;

    inicializarEventosEditar();

    function inicializarEventosEditar() {
        $('#editarProductoModal').on('change', 'input[name="tipo"]', manejarCambioTipoEditar);
        $('#editarProductoModal').on('change', '#editar_es_fabricado', manejarEsFabricadoEditar);
        $('#editarProductoModal').on('click', '#btnAgregarMateriaEditar', agregarFilaMateriaPrimaEditar);
        $('#editarProductoModal').on('submit', '#formEditarProducto', enviarFormularioEditar);
        $('#editarProductoModal').on('hidden.bs.modal', limpiarModalEditar);
        
        $(document).on('change', '.select-materia-prima-editar', actualizarSelectsMateriasPrimasEditar);
        $(document).on('change', '.select-materia-prima-editar', calcularCostoTotalEditar);
        $(document).on('input', '.cantidad-materia-prima-editar', calcularCostoTotalEditar);
        $(document).on('click', '.btn-eliminar-materia-editar', eliminarFilaMateriaPrimaEditar);
    }

    window.cargarDatosEditar = function(id) {
        productoId = id;
        
        $('#btnActualizar').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Cargando...');
        
        $.ajax({
            url: 'index.php?c=ProductoServicioControlador&m=editar&id=' + id,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    cargarMateriasPrimasDisponiblesEditar().then(() => {
                        mostrarDatosProducto(response.data);
                        $('#btnActualizar').prop('disabled', false).html('<i class="fas fa-save me-1"></i> Actualizar');
                    }).catch((error) => {
                        console.error('Error al cargar materias primas:', error);
                        $('#btnActualizar').prop('disabled', false).html('<i class="fas fa-save me-1"></i> Actualizar');
                    });
                } else {
                    mostrarMensajeEditar('error', response.message);
                    $('#btnActualizar').prop('disabled', false).html('<i class="fas fa-save me-1"></i> Actualizar');
                }
            },
            error: function(xhr, status, error) {
                // Manejar error de JSON
                let errorMessage = 'Error al cargar los datos del producto';
                try {
                    const responseText = xhr.responseText;
                    if (responseText.includes('<!DOCTYPE')) {
                        errorMessage = 'Error del servidor: Respuesta no es JSON';
                    }
                } catch (e) {
                    console.error('Error parsing response:', e);
                }
                
                mostrarMensajeEditar('error', errorMessage);
                console.error('Error:', error);
                $('#btnActualizar').prop('disabled', false).html('<i class="fas fa-save me-1"></i> Actualizar');
            }
        });
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
                        console.error('Error al cargar materias primas:', response.error);
                        reject(response.error);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error de conexión al cargar materias primas:', error);
                    reject(error);
                }
            });
        });
    }

    function mostrarDatosProducto(producto) {
        console.log('Mostrando datos del producto:', producto);
        
        limpiarModalEditar();

        $('#editar_id_inv').val(producto.id_inv);
        $('#editar_nombre').val(producto.nombre);
        $('#editar_unidad_medida').val(producto.id_unidad_medida);
        $('#editar_costo').val(parseFloat(producto.costo).toFixed(2));
        
        if (producto.tipo == 1) {
            $('#editar_tipo_producto').prop('checked', true);
            $('.campos-producto-editar').show();
            
            $('#editar_stock').val(producto.stock);
            $('#editar_precio_mayor').val(parseFloat(producto.costo_mayor).toFixed(2));
            $('#editar_presentacion').val(producto.presentacion);
            $('#editar_es_fabricado').prop('checked', producto.es_fabricado == 1);
            
            if (producto.es_fabricado == 1) {
                $('#editar_costo').prop('readonly', true).addClass('bg-light');
                $('#editar_precio_mayor').prop('readonly', true).addClass('bg-light');
            }
            
            if (producto.es_fabricado == 1 && producto.materias_primas) {
                materiasPrimasOriginales = producto.materias_primas;
                $('#materiasPrimasSectionEditar').show();
                cargarMateriasPrimasEditar(producto.materias_primas);
            }
        } else {
            $('#editar_tipo_servicio').prop('checked', true);
            $('.campos-producto-editar').hide();
        }
    }

    function cargarMateriasPrimasEditar(materiasPrimas) {
        const container = $('#materiasPrimasContainerEditar');
        container.empty();

        if (materiasPrimas && materiasPrimas.length > 0) {
            materiasPrimas.forEach((materia, index) => {
                const template = document.getElementById('templateMateriaPrimaEditar');
                const fila = template.content.cloneNode(true);
                
                const filaElement = $(fila).find('.fila-materia-prima-editar');
                filaElement.attr('data-index', index);
                filaElement.attr('data-materia-id', materia.id_materia);
                
                const selectElement = filaElement.find('.select-materia-prima-editar');
                selectElement.attr('name', `materias_primas[${index}][id_materia]`);
                
                const cantidadElement = filaElement.find('.cantidad-materia-prima-editar');
                cantidadElement.attr('name', `materias_primas[${index}][cantidad]`);
                cantidadElement.val(materia.cantidad);
                
                container.append(fila);
            });

            actualizarSelectsMateriasPrimasEditar();
            calcularCostoTotalEditar();
            verificarStockMateriasPrimas();
        }
    }

    function manejarCambioTipoEditar() {
        const tipo = $(this).val();
        const camposProducto = $('.campos-producto-editar');
        const esFabricadoCheckbox = $('#editar_es_fabricado');
        const materiasPrimasSection = $('#materiasPrimasSectionEditar');
        
        if (tipo === '1') {
            camposProducto.show();
            esFabricadoCheckbox.prop('disabled', false);
            
            if (!esFabricadoCheckbox.is(':checked')) {
                materiasPrimasSection.hide();
                limpiarMateriasPrimasEditar();
                $('#editar_costo').prop('readonly', false).removeClass('bg-light');
                $('#editar_precio_mayor').prop('readonly', false).removeClass('bg-light');
            } else {
                $('#editar_costo').prop('readonly', true).addClass('bg-light');
                $('#editar_precio_mayor').prop('readonly', true).addClass('bg-light');
            }
        } else { 
            camposProducto.hide();
            esFabricadoCheckbox.prop('disabled', true).prop('checked', false);
            materiasPrimasSection.hide();
            limpiarMateriasPrimasEditar();
            $('#editar_costo').prop('readonly', false).removeClass('bg-light');
            $('#editar_precio_mayor').prop('readonly', false).removeClass('bg-light');
        }
    }

    function manejarEsFabricadoEditar() {
        const esFabricado = $(this).is(':checked');
        const materiasPrimasSection = $('#materiasPrimasSectionEditar');
        
        if (esFabricado) {
            materiasPrimasSection.show();
            if ($('.fila-materia-prima-editar').length === 0) {
                agregarFilaMateriaPrimaEditar();
            }
            
            $('#editar_costo').prop('readonly', true).addClass('bg-light');
            $('#editar_precio_mayor').prop('readonly', true).addClass('bg-light');
            
            calcularCostoTotalEditar();
        } else {
            materiasPrimasSection.hide();
            limpiarMateriasPrimasEditar();

            $('#editar_costo').prop('readonly', false).removeClass('bg-light');
            $('#editar_precio_mayor').prop('readonly', false).removeClass('bg-light');
        }
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
        verificarStockMateriasPrimas();
    }

    function actualizarSelectsMateriasPrimasEditar() {
        $('.select-materia-prima-editar').each(function() {
            const selectActual = $(this);
            const fila = selectActual.closest('.fila-materia-prima-editar');
            const materiaIdOriginal = fila.attr('data-materia-id');
            const valorActual = materiaIdOriginal || selectActual.val();
            
            selectActual.html('<option value="" selected disabled>Seleccione materia prima</option>');
            
            let opcionesDisponibles = 0;
            
            materiasPrimasDisponibles.forEach(materia => {
                const yaSeleccionada = $('.select-materia-prima-editar').not(selectActual).filter(function() {
                    return $(this).val() == materia.id_materia;
                }).length > 0;
                
                if (!yaSeleccionada || materia.id_materia == valorActual) {
                    const option = $('<option>', {
                        value: materia.id_materia,
                        text: `${materia.nombre} (Stock: ${materia.stock})`,
                        'data-costo': materia.costo
                    });
                    
                    if (materia.id_materia == valorActual) {
                        option.prop('selected', true);
                        const costoInput = selectActual.closest('.fila-materia-prima-editar').find('.costo-unitario-editar');
                        costoInput.val(`$${parseFloat(materia.costo).toFixed(2)}`);
                    }
                    
                    selectActual.append(option);
                    opcionesDisponibles++;
                }
            });
            
            if (opcionesDisponibles === 0) {
                selectActual.html('<option value="" disabled>No hay materias primas disponibles</option>');
            }
            
            if (materiaIdOriginal) {
                fila.removeAttr('data-materia-id');
            }
        });
        
        calcularCostoTotalEditar();
        verificarStockMateriasPrimas();
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
        
        if ($('#editar_es_fabricado').is(':checked') && $('#editar_tipo_producto').is(':checked')) {
            $('#editar_costo').val(costoTotalMateriasPrimas.toFixed(2));
        
            const precioMayor = costoTotalMateriasPrimas * 0.9;
            $('#editar_precio_mayor').val(precioMayor.toFixed(2));
        }
    }

    function eliminarFilaMateriaPrimaEditar() {
        const fila = $(this).closest('.fila-materia-prima-editar');
        fila.remove();
        reindexarFilasMateriasPrimasEditar();
        actualizarSelectsMateriasPrimasEditar();
        calcularCostoTotalEditar();
        verificarStockMateriasPrimas();
    }

    function reindexarFilasMateriasPrimasEditar() {
        $('.fila-materia-prima-editar').each(function(index) {
            $(this).attr('data-index', index);
            $(this).find('.select-materia-prima-editar').attr('name', `materias_primas[${index}][id_materia]`);
            $(this).find('.cantidad-materia-prima-editar').attr('name', `materias_primas[${index}][cantidad]`);
        });
    }

    function verificarStockMateriasPrimas() {
        let hayStockInsuficiente = false;
        let mensajesError = [];
        
        $('.fila-materia-prima-editar').each(function() {
            const select = $(this).find('.select-materia-prima-editar');
            const cantidadInput = $(this).find('.cantidad-materia-prima-editar');
            const materiaId = select.val();
            const cantidad = parseFloat(cantidadInput.val()) || 0;
            
            if (materiaId) {
                const materia = materiasPrimasDisponibles.find(m => m.id_materia == materiaId);
                if (materia) {
                    if (cantidad > materia.stock) {
                        hayStockInsuficiente = true;
                        cantidadInput.addClass('is-invalid');
                        mensajesError.push(`Stock insuficiente para ${materia.nombre}. Disponible: ${materia.stock}`);
                    } else {
                        cantidadInput.removeClass('is-invalid');
                    }
                }
            }
        });
        
        if (hayStockInsuficiente) {
            $('#stockWarning').show().attr('title', mensajesError.join('\n'));
            $('#btnActualizar').prop('disabled', true);
        } else {
            $('#stockWarning').hide();
            $('#btnActualizar').prop('disabled', false);
        }
    }

    function validarFormularioEditar() {
        const tipo = $('#editarProductoModal input[name="tipo"]:checked').val();
        let errores = [];

        if (!$('#editar_nombre').val().trim()) {
            errores.push('El nombre es obligatorio');
            $('#editar_nombre').addClass('is-invalid');
        } else {
            $('#editar_nombre').removeClass('is-invalid');
        }

        if (!$('#editar_unidad_medida').val()) {
            errores.push('La unidad de medida es obligatoria');
            $('#editar_unidad_medida').addClass('is-invalid');
        } else {
            $('#editar_unidad_medida').removeClass('is-invalid');
        }

        const costo = parseFloat($('#editar_costo').val());
        if (isNaN(costo) || costo < 0) {
            errores.push('El costo debe ser un número mayor o igual a 0');
            $('#editar_costo').addClass('is-invalid');
        } else {
            $('#editar_costo').removeClass('is-invalid');
        }

        if (tipo === '1') {
            const stock = parseInt($('#editar_stock').val());
            if (isNaN(stock) || stock < 0) {
                errores.push('El stock debe ser un número mayor o igual a 0');
                $('#editar_stock').addClass('is-invalid');
            } else {
                $('#editar_stock').removeClass('is-invalid');
            }

            const precioMayor = parseFloat($('#editar_precio_mayor').val());
            if (isNaN(precioMayor) || precioMayor < 0) {
                errores.push('El precio al por mayor debe ser un número mayor o igual a 0');
                $('#editar_precio_mayor').addClass('is-invalid');
            } else {
                $('#editar_precio_mayor').removeClass('is-invalid');
            }

            if (!$('#editar_presentacion').val()) {
                errores.push('La presentación es obligatoria para productos');
                $('#editar_presentacion').addClass('is-invalid');
            } else {
                $('#editar_presentacion').removeClass('is-invalid');
            }

            if ($('#editar_es_fabricado').is(':checked')) {
                let tieneMateriasPrimas = false;
                $('.fila-materia-prima-editar').each(function() {
                    const idMateria = $(this).find('.select-materia-prima-editar').val();
                    const cantidad = $(this).find('.cantidad-materia-prima-editar').val();
                    if (idMateria && cantidad) {
                        tieneMateriasPrimas = true;
                    }
                });

                if (!tieneMateriasPrimas) {
                    errores.push('Debe agregar al menos una materia prima para productos fabricados');
                }
            }
        }

        return errores;
    }

    function enviarFormularioEditar(e) {
        e.preventDefault();
        
        const errores = validarFormularioEditar();
        if (errores.length > 0) {
            mostrarMensajeEditar('error', errores.join('<br>'));
            return;
        }
        
        const formData = new FormData(this);
        const tipo = $('#editarProductoModal input[name="tipo"]:checked').val();
        
        if (tipo === '2') {
            formData.set('stock', '0');
            formData.set('costo_mayor', '0');
            formData.set('es_fabricado', '0');
        }
        
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
                if (response.success) {
                    mostrarMensajeEditar('success', response.message);
                    setTimeout(() => {
                        $('#editarProductoModal').modal('hide');
                        location.reload();
                    }, 1500);
                } else {
                    mostrarMensajeEditar('error', response.message);
                    $('#btnActualizar').prop('disabled', false).html('<i class="fas fa-save me-1"></i> Actualizar');
                }
            },
            error: function(xhr, status, error) {
                let errorMessage = 'Error de conexión al actualizar el producto';
                try {
                    const responseText = xhr.responseText;
                    if (responseText.includes('<!DOCTYPE')) {
                        errorMessage = 'Error del servidor. Por favor, contacte al administrador.';
                    } else {
                        const jsonResponse = JSON.parse(responseText);
                        if (jsonResponse.message) {
                            errorMessage = jsonResponse.message;
                        }
                    }
                } catch (e) {
                    console.error('Error parsing error response:', e);
                }
                
                mostrarMensajeEditar('error', errorMessage);
                console.error('Error:', error);
                $('#btnActualizar').prop('disabled', false).html('<i class="fas fa-save me-1"></i> Actualizar');
            }
        });
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
        $('#stockWarning').hide();
        $('#btnActualizar').prop('disabled', false).html('<i class="fas fa-save me-1"></i> Actualizar');
        materiasPrimasOriginales = [];
        
        $('#editar_costo').prop('readonly', false).removeClass('bg-light');
        $('#editar_precio_mayor').prop('readonly', false).removeClass('bg-light');
        
        $('.is-invalid').removeClass('is-invalid');
    }

    function limpiarMateriasPrimasEditar() {
        $('#materiasPrimasContainerEditar').empty();
        $('#costoTotalValorEditar').text('0.00');
    }

    function mostrarMensajeEditar(tipo, mensaje) {
        const alerta = $('#editar_btnerror');
        alerta.removeClass('d-none alert-success alert-danger')
              .addClass(tipo === 'success' ? 'alert-success' : 'alert-danger')
              .html(`<i class="fas ${tipo === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'} me-2"></i>${mensaje}`);
              
        if (tipo === 'success') {
            setTimeout(() => {
                alerta.addClass('d-none');
            }, 5000);
        }
    }
});