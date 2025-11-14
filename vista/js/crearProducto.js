$(document).ready(function() {
    let materiasPrimasDisponibles = [];

    inicializarEventos();
    cargarMateriasPrimasDisponibles();

    function inicializarEventos() {
        $('input[name="tipo"]').on('change', manejarCambioTipo);
        $('#es_fabricado').on('change', manejarEsFabricado);
        $('#btnAgregarMateria').on('click', agregarFilaMateriaPrima);
        $('#formCrearProducto').on('submit', enviarFormulario);
        $('#registrarProductoModal').on('hidden.bs.modal', limpiarModal);
        
        $(document).on('change', '.select-materia-prima', actualizarSelectsMateriasPrimas);
        $(document).on('change', '.select-materia-prima', calcularCostoTotal);
        $(document).on('input', '.cantidad-materia-prima', calcularCostoTotal);
        $(document).on('click', '.btn-eliminar-materia', eliminarFilaMateriaPrima);
        $('#costo').on('input', calcularPrecioMayor);
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
                }
            },
            error: function(xhr, status, error) {
                console.error('Error de conexión al cargar materias primas:', error);
            }
        });
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

    function calcularPrecioMayor() {
        const costo = parseFloat($('#costo').val()) || 0;
        const precioMayor = costo * 0.9; 
        $('#precio_mayor').val(precioMayor.toFixed(2));
    }

    function eliminarFilaMateriaPrima() {
        const fila = $(this).closest('.fila-materia-prima');
        fila.remove();
        reindexarFilasMateriasPrimas();
        actualizarSelectsMateriasPrimas();
        calcularCostoTotal();
    }

    function reindexarFilasMateriasPrimas() {
        $('.fila-materia-prima').each(function(index) {
            $(this).attr('data-index', index);
            $(this).find('.select-materia-prima').attr('name', `materias_primas[${index}][id_materia]`);
            $(this).find('.cantidad-materia-prima').attr('name', `materias_primas[${index}][cantidad]`);
        });
    }

    function enviarFormulario(e) {
        e.preventDefault();
        
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
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    mostrarMensaje('success', response.message);
                    $('#registrarProductoModal').modal('hide');
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    mostrarMensaje('error', response.message);
                }
            },
            error: function(xhr, status, error) {
                mostrarMensaje('error', 'Error de conexión al guardar el producto');
                console.error('Error:', error);
            }
        });
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
    }

    function limpiarMateriasPrimas() {
        $('#materiasPrimasContainer').empty();
        $('#costoTotalValor').text('0.00');
    }

    function mostrarMensaje(tipo, mensaje) {
        const alerta = $('#crear_btnerror');
        alerta.removeClass('d-none alert-success alert-danger')
              .addClass(tipo === 'success' ? 'alert-success' : 'alert-danger')
              .text(mensaje);
    }

    function verificarStockMateriasPrimas() {
    let hayStockInsuficiente = false;
    
    $('.fila-materia-prima').each(function() {
        const select = $(this).find('.select-materia-prima');
        const cantidadInput = $(this).find('.cantidad-materia-prima');
        const materiaId = select.val();
        const cantidad = parseFloat(cantidadInput.val()) || 0;
        
        if (materiaId) {
            const materia = materiasPrimasDisponibles.find(m => m.id_materia == materiaId);
            if (materia && cantidad > materia.stock) {
                hayStockInsuficiente = true;
                cantidadInput.addClass('is-invalid');
            } else {
                cantidadInput.removeClass('is-invalid');
            }
        }
    });
    
    if (hayStockInsuficiente) {
        $('#btnGuardar').prop('disabled', true);
        mostrarMensaje('error', 'Stock insuficiente en algunas materias primas');
    } else {
        $('#btnGuardar').prop('disabled', false);
    }
}

$(document).on('change', '.select-materia-prima', verificarStockMateriasPrimas);
$(document).on('input', '.cantidad-materia-prima', verificarStockMateriasPrimas);
});