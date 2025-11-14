$(document).ready(function() {
    let materiasPrimasDisponibles = [];
    let formularioModificado = false;

    function inicializarEventos() {
        $('input[name="tipo"]').on('change', manejarCambioTipo);
        $('#es_fabricado').on('change', manejarEsFabricado);
        $('#btnAgregarMateria').on('click', agregarFilaMateriaPrima);
        $('#formCrearProducto').on('submit', enviarFormulario);
        

        $('#formCrearProducto').on('input', function() {
            formularioModificado = true;
        });
        
        $('#registrarProductoModal .btn-secondary').on('click', function() {
            limpiarModal();
            $('#registrarProductoModal').modal('hide');
        });
        
        $('#registrarProductoModal').on('show.bs.modal', function() {
            formularioModificado = false;
            setTimeout(() => {
                limpiarModal();
            }, 100);
        });
        
        $(document).on('change', '.select-materia-prima', actualizarSelectsMateriasPrimas);
        $(document).on('change', '.select-materia-prima', calcularCostoTotal);
        $(document).on('input', '.cantidad-materia-prima', calcularCostoTotal);
        $(document).on('click', '.btn-eliminar-materia', eliminarFilaMateriaPrima);
        $('#costo').on('input', calcularPrecioMayor);
        
        inicializarValidacionesTiempoReal();
        
        $(document).on('change', '.select-materia-prima', verificarStockMateriasPrimas);
        $(document).on('input', '.cantidad-materia-prima', verificarStockMateriasPrimas);
    }

    function agregarFilaMateriaPrima() {
        const container = $('#materiasPrimasContainer');
        const index = $('.fila-materia-prima').length;
        
        const template = document.getElementById('templateMateriaPrima');
        const fila = template.content.cloneNode(true);
        
        const filaElement = $(fila).find('.fila-materia-prima');
        filaElement.attr('data-index', index);
        
        const selectElement = filaElement.find('.select-materia-prima');
        selectElement.attr('name', `materias_primas[${index}][id_materia]`);
        
        const cantidadElement = filaElement.find('.cantidad-materia-prima');
        cantidadElement.attr('name', `materias_primas[${index}][cantidad]`);
        
        container.append(fila);
        actualizarSelectsMateriasPrimas();
        verificarStockMateriasPrimas();
    }

    function actualizarSelectsMateriasPrimas() {
        $('.select-materia-prima').each(function() {
            const selectActual = $(this);
            const valorActual = selectActual.val();
            
            selectActual.html('<option value="" selected disabled>Seleccione materia prima</option>');
            
            materiasPrimasDisponibles.forEach(materia => {
                const yaSeleccionada = $('.select-materia-prima').not(selectActual).filter(function() {
                    return $(this).val() == materia.id_materia;
                }).length > 0;
                
                if (!yaSeleccionada) {
                    selectActual.append(`<option value="${materia.id_materia}" data-costo="${materia.costo}">${materia.nombre} (Stock: ${materia.stock})</option>`);
                }
            });
            
            if (valorActual) {
                selectActual.val(valorActual);
                const costo = selectActual.find('option:selected').data('costo');
                selectActual.closest('.fila-materia-prima').find('.costo-unitario').val(costo ? `$${parseFloat(costo).toFixed(2)}` : '');
            }
        });
        
        calcularCostoTotal();
    }

    function calcularCostoTotal() {
        let costoTotalMateriasPrimas = 0;
        
        $('.fila-materia-prima').each(function() {
            const select = $(this).find('.select-materia-prima');
            const cantidadInput = $(this).find('.cantidad-materia-prima');
            const costo = select.find('option:selected').data('costo') || 0;
            const cantidad = parseFloat(cantidadInput.val()) || 0;
            
            costoTotalMateriasPrimas += costo * cantidad;
        });
        
        $('#costoTotalValor').text(costoTotalMateriasPrimas.toFixed(2));
        
        if ($('#es_fabricado').is(':checked')) {
            $('#costo').val(costoTotalMateriasPrimas.toFixed(2));
            calcularPrecioMayor();
        }
    }

    function eliminarFilaMateriaPrima() {
        const fila = $(this).closest('.fila-materia-prima');
        fila.remove();
        reindexarFilasMateriasPrimas();
        actualizarSelectsMateriasPrimas();
        calcularCostoTotal();
        verificarStockMateriasPrimas();
    }

    function reindexarFilasMateriasPrimas() {
        $('.fila-materia-prima').each(function(index) {
            $(this).attr('data-index', index);
            $(this).find('.select-materia-prima').attr('name', `materias_primas[${index}][id_materia]`);
            $(this).find('.cantidad-materia-prima').attr('name', `materias_primas[${index}][cantidad]`);
        });
    }

    function calcularPrecioMayor() {
        const costo = parseFloat($('#costo').val()) || 0;
        const precioMayor = costo * 0.9; 
        $('#precio_mayor').val(precioMayor.toFixed(2));
    }

    function manejarCambioTipo() {
        const tipo = $(this).val();
        const camposProducto = $('.campos-producto');
        const esFabricadoCheckbox = $('#es_fabricado');
        const materiasPrimasSection = $('#materiasPrimasSection');
        const costoInput = $('#costo');
        
        if (tipo === '1') {
            camposProducto.show();
            esFabricadoCheckbox.prop('disabled', false);
            
            if (!esFabricadoCheckbox.is(':checked')) {
                materiasPrimasSection.hide();
                costoInput.prop('readonly', false);
            }
        } else { 
            camposProducto.hide();
            esFabricadoCheckbox.prop('disabled', true).prop('checked', false);
            materiasPrimasSection.hide();
            limpiarMateriasPrimas();
            costoInput.prop('readonly', false);
        }
        
        calcularPrecioMayor();
        limpiarValidaciones();
    }

    function manejarEsFabricado() {
        const esFabricado = $(this).is(':checked');
        const materiasPrimasSection = $('#materiasPrimasSection');
        const costoInput = $('#costo');
        
        if (esFabricado) {
            materiasPrimasSection.show();
            costoInput.prop('readonly', true);
            if ($('.fila-materia-prima').length === 0) {
                agregarFilaMateriaPrima();
            }
            calcularCostoTotal();
        } else {
            materiasPrimasSection.hide();
            costoInput.prop('readonly', false);
            limpiarMateriasPrimas();
        }
        limpiarValidaciones();
    }

    function cargarMateriasPrimasDisponibles() {
        $.ajax({
            url: 'index.php?c=ProductoServicioControlador&m=getMateriasPrimas',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    materiasPrimasDisponibles = response.materiasPrimas;
                    actualizarSelectsMateriasPrimas();
                } else {
                    console.error('Error al cargar materias primas:', response.error);
                    mostrarMensajeInterno('error', 'Error al cargar materias primas: ' + (response.message || 'Error desconocido'));
                }
            },
            error: function(xhr, status, error) {
                console.error('Error de conexión al cargar materias primas:', error);
                let errorMsg = 'Error de conexión al cargar materias primas';
                
                if (xhr.responseText) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        errorMsg = response.message || errorMsg;
                    } catch (e) {
                        errorMsg = xhr.responseText || errorMsg;
                    }
                }
                
                mostrarMensajeInterno('error', errorMsg);
            }
        });
    }

    function inicializarValidacionesTiempoReal() {
        $('#nombre').on('input', validarNombre);
        $('#unidad_medida').on('change', validarUnidadMedida);
        $('#costo').on('input', validarCosto);
        $('#stock').on('input', validarStock);
        $('#precio_mayor').on('input', validarPrecioMayor);
        $('#presentacion').on('change', validarPresentacion);
        
        $(document).on('change', '.select-materia-prima', validarMateriasPrimas);
        $(document).on('input', '.cantidad-materia-prima', validarMateriasPrimas);
    }

    function validarNombre() {
        const nombre = $('#nombre').val().trim();
        const errorElement = $('#nombre_error');
        
        if (!nombre) {
            mostrarErrorCampo('nombre', 'El nombre es obligatorio', errorElement);
            return false;
        } else if (nombre.length > 255) {
            mostrarErrorCampo('nombre', 'El nombre no puede tener más de 255 caracteres', errorElement);
            return false;
        } else {
            mostrarExitoCampo('nombre', errorElement);
            return true;
        }
    }

    function validarUnidadMedida() {
        const unidadMedida = $('#unidad_medida').val();
        const errorElement = $('#unidad_medida_error');
        
        if (!unidadMedida) {
            mostrarErrorCampo('unidad_medida', 'La unidad de medida es obligatoria', errorElement);
            return false;
        } else {
            mostrarExitoCampo('unidad_medida', errorElement);
            return true;
        }
    }

    function validarCosto() {
        const costo = parseFloat($('#costo').val());
        const errorElement = $('#costo_error');
        
        if (isNaN(costo) || costo <= 0) {
            mostrarErrorCampo('costo', 'El costo debe ser un número mayor que 0', errorElement);
            return false;
        } else if (costo > 999999.99) {
            mostrarErrorCampo('costo', 'El costo no puede ser mayor a 999,999.99', errorElement);
            return false;
        } else {
            mostrarExitoCampo('costo', errorElement);
            return true;
        }
    }

    function validarStock() {
        const stock = parseInt($('#stock').val());
        const errorElement = $('#stock_error');
        
        if (isNaN(stock) || stock < 0) {
            mostrarErrorCampo('stock', 'El stock debe ser un número mayor o igual a 0', errorElement);
            return false;
        } else if (stock > 999999) {
            mostrarErrorCampo('stock', 'El stock no puede ser mayor a 999,999', errorElement);
            return false;
        } else {
            mostrarExitoCampo('stock', errorElement);
            return true;
        }
    }

    function validarPrecioMayor() {
        const precioMayor = parseFloat($('#precio_mayor').val());
        const errorElement = $('#precio_mayor_error');
        const costo = parseFloat($('#costo').val()) || 0;
        
        if (isNaN(precioMayor) || precioMayor <= 0) {
            mostrarErrorCampo('precio_mayor', 'El precio al por mayor debe ser un número mayor que 0', errorElement);
            return false;
        } else if (precioMayor > 999999.99) {
            mostrarErrorCampo('precio_mayor', 'El precio al por mayor no puede ser mayor a 999,999.99', errorElement);
            return false;
        } else if (precioMayor >= costo) {
            mostrarErrorCampo('precio_mayor', 'El precio al por mayor debe ser menor al costo', errorElement);
            return false;
        } else {
            mostrarExitoCampo('precio_mayor', errorElement);
            return true;
        }
    }

    function validarPresentacion() {
        const presentacion = $('#presentacion').val();
        const errorElement = $('#presentacion_error');
        
        if (!presentacion) {
            mostrarErrorCampo('presentacion', 'La presentación es obligatoria para productos', errorElement);
            return false;
        } else {
            mostrarExitoCampo('presentacion', errorElement);
            return true;
        }
    }

    function validarMateriasPrimas() {
        let isValid = true;
        let tieneMateriasValidas = false;
        
        if (!$('#es_fabricado').is(':checked')) {
            return true;
        }
        
        $('.fila-materia-prima').each(function() {
            const select = $(this).find('.select-materia-prima');
            const cantidadInput = $(this).find('.cantidad-materia-prima');
            const idMateria = select.val();
            const cantidad = parseFloat(cantidadInput.val());
            
            if (!idMateria) {
                mostrarErrorCampoElement(select, 'Debe seleccionar una materia prima');
                isValid = false;
            } else {
                mostrarExitoCampoElement(select);
            }
            
            if (isNaN(cantidad) || cantidad <= 0) {
                mostrarErrorCampoElement(cantidadInput, 'La cantidad debe ser mayor a 0');
                isValid = false;
            } else if (cantidad > 999999.99) {
                mostrarErrorCampoElement(cantidadInput, 'La cantidad no puede ser mayor a 999,999.99');
                isValid = false;
            } else {
                mostrarExitoCampoElement(cantidadInput);
                tieneMateriasValidas = true;
            }
        });
        
        if ($('#es_fabricado').is(':checked') && !tieneMateriasValidas) {
            mostrarMensajeInterno('error', 'Debe agregar al menos una materia prima válida para productos fabricados');
            isValid = false;
        }
        
        return isValid;
    }

    function mostrarErrorCampoElement(element, mensaje) {
        element.addClass('is-invalid').removeClass('is-valid');
        let errorElement = element.next('.error-message-specific');
        if (errorElement.length === 0) {
            errorElement = $('<div class="error-message-specific text-danger small mt-1"></div>');
            element.after(errorElement);
        }
        errorElement.html(`<i class="fas fa-exclamation-circle me-1"></i>${mensaje}`).removeClass('d-none');
    }

    function mostrarExitoCampoElement(element) {
        element.removeClass('is-invalid').addClass('is-valid');
        element.next('.error-message-specific').addClass('d-none').text('');
    }

    function mostrarErrorCampo(campoId, mensaje, errorElement) {
        $(`#${campoId}`).addClass('is-invalid').removeClass('is-valid');
        if (errorElement) {
            errorElement.html(`<i class="fas fa-exclamation-circle me-1"></i>${mensaje}`).removeClass('d-none');
        }
    }

    function mostrarExitoCampo(campoId, errorElement) {
        $(`#${campoId}`).removeClass('is-invalid').addClass('is-valid');
        if (errorElement) {
            errorElement.addClass('d-none').text('');
        }
    }

    function limpiarValidaciones() {
        $('.is-invalid').removeClass('is-invalid');
        $('.is-valid').removeClass('is-valid');
        $('.error-message').addClass('d-none').text('');
        $('.error-message-specific').addClass('d-none').text('');
        $('#crear_btnerror').addClass('d-none').text('');
    }

    function validarFormulario() {
        let isValid = true;
        
        if (!validarNombre()) isValid = false;
        if (!validarUnidadMedida()) isValid = false;
        if (!validarCosto()) isValid = false;
        
        const tipo = $('input[name="tipo"]:checked').val();
        
        if (tipo === '1') {
            if (!validarStock()) isValid = false;
            if (!validarPrecioMayor()) isValid = false;
            if (!validarPresentacion()) isValid = false;
            
            if ($('#es_fabricado').is(':checked')) {
                if (!validarMateriasPrimas()) isValid = false;
                if (!verificarStockMateriasPrimas()) isValid = false;
            }
        }
        
        return isValid;
    }

    function enviarFormulario(e) {
        e.preventDefault();
        
        if (!validarFormulario()) {
            mostrarMensajeInterno('error', 'Por favor, corrija los errores en el formulario antes de enviar');
            return;
        }
        
        const formData = new FormData(this);
        const tipo = $('input[name="tipo"]:checked').val();
        
        if (tipo === '1' && $('#es_fabricado').is(':checked')) {
            const materiasPrimasData = [];
            
            $('.fila-materia-prima').each(function() {
                const idMateria = $(this).find('.select-materia-prima').val();
                const cantidad = $(this).find('.cantidad-materia-prima').val();
                
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
  
        $('#btnGuardar').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Guardando...');
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                $('#btnGuardar').prop('disabled', false).html('<i class="fas fa-save me-1"></i> Registrar');
                
                if (response.success) {
                    if (typeof window.mostrarMensaje === 'function') {
                        window.mostrarMensaje('success', response.message, 'El producto/servicio se ha registrado correctamente');
                    } else {
                        mostrarMensajeInterno('success', response.message);
                    }
                    
                    formularioModificado = false;
                    limpiarModal();
                    $('#registrarProductoModal').modal('hide');
                    
                    if(typeof cargarTablaProductosServicios === 'function'){
                        cargarTablaProductosServicios();
                    } else if(typeof window.cargarTablaProductosServicios === 'function'){
                        window.cargarTablaProductosServicios();
                    }
                } else {
                    if (typeof window.mostrarMensaje === 'function') {
                        window.mostrarMensaje('error', response.message, 'No se pudo completar la operación');
                    } else {
                        mostrarMensajeInterno('error', response.message);
                    }
                }
            },
            error: function(xhr, status, error) {
                $('#btnGuardar').prop('disabled', false).html('<i class="fas fa-save me-1"></i> Registrar');
                
                let errorMessage = 'Error de conexión al guardar el producto';
                
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
                    mostrarMensajeInterno('error', errorMessage);
                }
                console.error('Error detallado:', xhr.responseText);
            }
        });
    }

    function mostrarMensajeInterno(tipo, mensaje) {
        const alerta = $('#crear_btnerror');
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

    function limpiarModal() {
        $('#formCrearProducto')[0].reset();
        $('#materiasPrimasContainer').empty();
        $('#costoTotalValor').text('0.00');
        $('.campos-producto').show();
        $('#es_fabricado').prop('disabled', false).prop('checked', false);
        $('#materiasPrimasSection').hide();
        $('#tipo_producto').prop('checked', true);
        $('#crear_btnerror').addClass('d-none').text('');
        $('#costo').prop('readonly', false);
        calcularPrecioMayor();
        limpiarValidaciones();
        formularioModificado = false;
    }

    function limpiarMateriasPrimas() {
        $('#materiasPrimasContainer').empty();
        $('#costoTotalValor').text('0.00');
    }

    function verificarStockMateriasPrimas() {
        let hayStockInsuficiente = false;
        let mensajeStock = '';
        
        $('.fila-materia-prima').each(function() {
            const select = $(this).find('.select-materia-prima');
            const cantidadInput = $(this).find('.cantidad-materia-prima');
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
            $('#btnGuardar').prop('disabled', true);
            if (mensajeStock) {
                mostrarMensajeInterno('error', mensajeStock);
            }
        } else {
            $('#btnGuardar').prop('disabled', false);
        }
        
        return !hayStockInsuficiente;
    }

    inicializarEventos();
    cargarMateriasPrimasDisponibles();
});