$(document).ready(function() {
    let detalleCount = 0;
    const IVA_PORCENTAJE = 0.16; 
    
    function inicializarSelect2Proveedor() {
        $('#proveedor').select2({
            theme: 'bootstrap-5',
            placeholder: "Seleccionar proveedor...",
            allowClear: true,
            width: '100%',
            dropdownParent: $('#registrarFacturaModal'),
            language: {
                noResults: function() {
                    return "No se encontraron proveedores";
                },
                searching: function() {
                    return "Buscando...";
                }
            }
        });
    }
    

    function inicializarSelect2Materia($element) {
        $element.select2({
            theme: 'bootstrap-5',
            placeholder: "Seleccionar materia prima...",
            allowClear: true,
            width: '100%',
            dropdownParent: $('#registrarFacturaModal'),
            language: {
                noResults: function() {
                    return "No se encontraron materias primas";
                },
                searching: function() {
                    return "Buscando...";
                }
            }
        });
    }

    function mostrarMensaje(mensaje, tipo = 'warning') {
        const alertDiv = $('<div class="alert alert-' + tipo + ' alert-dismissible fade show" role="alert">' +
            '<i class="fas fa-exclamation-triangle me-2"></i>' + mensaje +
            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
            '</div>');

        $('#registrarFacturaModal .modal-body').prepend(alertDiv);

        setTimeout(function() {
            alertDiv.alert('close');
        }, 5000);
    }
    
    function calcularTotales() {
        let subtotal = 0;
        
        $('.detalle-item').each(function() {
            const cantidad = parseFloat($(this).find('.cantidad-input').val()) || 0;
            const costo = parseFloat($(this).find('.costo-input').val()) || 0;
            const itemSubtotal = cantidad * costo;
            subtotal += itemSubtotal;

            $(this).find('.subtotal-input').val(itemSubtotal.toFixed(2));
        });
        
        const iva = subtotal * IVA_PORCENTAJE;
        const totalGeneral = subtotal + iva;
        
        $('#subtotal').val(subtotal.toFixed(2));
        $('#total_iva').val(iva.toFixed(2));
        $('#total_general').val(totalGeneral.toFixed(2));
    }
    
    $('#agregar-detalle').on('click', function() {
        const template = document.getElementById('template-detalle').content.cloneNode(true);
        const nuevoDetalle = $(template);
        
        nuevoDetalle.find('select, input').each(function() {
            const name = $(this).attr('name');
            if (name) {
                $(this).attr('name', name.replace('[]', `[${detalleCount}]`));
            }
        });
        
        $('#detalles-container').append(nuevoDetalle);
        
        setTimeout(() => {
            const $selectMateria = nuevoDetalle.find('.select2-materia');
            if ($selectMateria.length && !$selectMateria.hasClass('select2-hidden-accessible')) {
                inicializarSelect2Materia($selectMateria);
            }
        }, 0);
        
        detalleCount++;
        calcularTotales();
    });
    
    $(document).on('click', '.remover-detalle', function() {
        if ($('.detalle-item').length > 1) {
            const detalleItem = $(this).closest('.detalle-item');
            const select2Instance = detalleItem.find('.select2-materia');
            
            if (select2Instance.length && select2Instance.hasClass('select2-hidden-accessible')) {
                select2Instance.select2('destroy');
            }
            
            detalleItem.remove();
            calcularTotales();
        } else {
            mostrarMensaje('Debe mantener al menos un detalle de materia prima', 'warning');
        }
    });

    $(document).on('change', '.select2-materia', function() {
        const selectedOption = $(this).find('option:selected');
        const detalleItem = $(this).closest('.detalle-item');
        const nuevaMPForm = detalleItem.find('.nueva-materia-prima-form');
        
        if (selectedOption.val() === 'nueva') {
            nuevaMPForm.show();
            detalleItem.find('.select2-materia').removeAttr('required');
            detalleItem.find('.cantidad-input').removeAttr('required');
            detalleItem.find('.costo-input').removeAttr('required');
            
            detalleItem.find('.unidad-medida').val('');
            detalleItem.find('.stock-info').text('Stock actual: 0');
            detalleItem.find('.costo-input').val('');
            detalleItem.find('.subtotal-input').val('0.00');
        } else {
            nuevaMPForm.hide();
            detalleItem.find('.select2-materia').attr('required', 'required');
            detalleItem.find('.cantidad-input').attr('required', 'required');
            detalleItem.find('.costo-input').attr('required', 'required');
            
            if (selectedOption.val()) {
                const nombre = selectedOption.data('nombre');
                const unidad = selectedOption.data('unidad');
                const stock = selectedOption.data('stock');
                const costo = selectedOption.data('costo');
                
                detalleItem.find('.unidad-medida').val(unidad);
                detalleItem.find('.stock-info').text(`Stock actual: ${stock}`);
                detalleItem.find('.costo-input').val(costo);

                const cantidad = parseInt(detalleItem.find('.cantidad-input').val()) || 0;
                const nuevoStock = parseInt(stock) + cantidad;
                detalleItem.find('.nuevo-stock-info').text(`Nuevo stock: ${nuevoStock}`);
            } else {
                detalleItem.find('.unidad-medida').val('');
                detalleItem.find('.stock-info').text('Stock actual: 0');
                detalleItem.find('.costo-input').val('');
                detalleItem.find('.nuevo-stock-info').text('Nuevo stock: 0');
                detalleItem.find('.subtotal-input').val('0.00');
            }
        }
        calcularTotales();
    });

    $(document).on('click', '.btn-nueva-mp', function() {
        const detalleItem = $(this).closest('.detalle-item');
        const select = detalleItem.find('.select2-materia');
        select.val('nueva').trigger('change');
    });

    $(document).on('click', '.guardar-nueva-mp', function() {
        const detalleItem = $(this).closest('.detalle-item');
        const nombre = detalleItem.find('.nueva-mp-nombre').val().trim();
        const unidadId = detalleItem.find('.nueva-mp-unidad').val();
        const costo = parseFloat(detalleItem.find('.nueva-mp-costo').val());

        if (!nombre) {
            mostrarMensaje('Por favor, ingrese el nombre de la materia prima', 'warning');
            return;
        }
        
        if (!unidadId) {
            mostrarMensaje('Por favor, seleccione una unidad de medida', 'warning');
            return;
        }
        
        if (!costo || costo <= 0) {
            mostrarMensaje('Por favor, ingrese un costo válido mayor a 0', 'warning');
            return;
        }
        
        const btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');

        $.ajax({
            url: 'index.php?c=MateriaPrimaControlador&m=crearDesdeFactura',
            type: 'POST',
            data: {
                nombre: nombre,
                id_unidad_medida: unidadId,
                costo: costo
            },
            success: function(response) {
                try {
                    const result = typeof response === 'string' ? JSON.parse(response) : response;
                    
                    if (result.success) {
                        const nuevaMateria = result.materia_prima;
                        const select = detalleItem.find('.select2-materia');

                        const newOption = new Option(
                            `${nuevaMateria.nombre} (Stock: ${nuevaMateria.stock} ${nuevaMateria.unidad_medida})`, 
                            nuevaMateria.id_materia,
                            false,
                            false
                        );
                        
                        $(newOption)
                            .data('nombre', nuevaMateria.nombre)
                            .data('unidad', nuevaMateria.unidad_medida)
                            .data('stock', nuevaMateria.stock)
                            .data('costo', nuevaMateria.costo);
                        
                        select.find('option[value="nueva"]').before(newOption);

                        select.val(nuevaMateria.id_materia).trigger('change');

                        detalleItem.find('.nueva-materia-prima-form').hide();
                        detalleItem.find('.nueva-mp-nombre').val('');
                        detalleItem.find('.nueva-mp-unidad').val('');
                        detalleItem.find('.nueva-mp-costo').val('');
                        
                        mostrarMensaje('Materia prima creada exitosamente', 'success');
                        
                    } else {
                        mostrarMensaje('Error al crear la materia prima: ' + (result.message || 'Error desconocido'), 'danger');
                    }
                } catch (e) {
                    console.error('Error parsing response:', e, response);
                    mostrarMensaje('Error al procesar la respuesta del servidor', 'danger');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                mostrarMensaje('Error de conexión al crear la materia prima: ' + error, 'danger');
            },
            complete: function() {
                btn.prop('disabled', false).html('<i class="fas fa-save"></i> Guardar');
            }
        });
    });
    
    $(document).on('input', '.cantidad-input, .costo-input', function() {
        const detalleItem = $(this).closest('.detalle-item');
        const cantidad = parseInt(detalleItem.find('.cantidad-input').val()) || 0;
        const costo = parseFloat(detalleItem.find('.costo-input').val()) || 0;
        const subtotal = cantidad * costo;
        
        detalleItem.find('.subtotal-input').val(subtotal.toFixed(2));
        
        const selectedOption = detalleItem.find('.select2-materia option:selected');
        if (selectedOption.val() && selectedOption.val() !== 'nueva') {
            const stockActual = parseInt(selectedOption.data('stock')) || 0;
            const nuevoStock = stockActual + cantidad;
            detalleItem.find('.nuevo-stock-info').text(`Nuevo stock: ${nuevoStock}`);
        }
        
        calcularTotales();
    });

    $('#formFacturaCompra').on('submit', function(e) {
        let isValid = true;
        let errorMessage = '';

        if (!$('#proveedor').val()) {
            isValid = false;
            errorMessage = 'Debe seleccionar un proveedor';
        }

        if ($('.detalle-item').length === 0) {
            isValid = false;
            errorMessage = 'Debe agregar al menos un detalle de materia prima';
        }
        
        $('.detalle-item').each(function() {
            const detalleItem = $(this);
            const nuevaMPForm = detalleItem.find('.nueva-materia-prima-form');
            
            if (nuevaMPForm.is(':visible')) {
                isValid = false;
                errorMessage = 'Complete o cancele la creación de nueva materia prima antes de guardar';
                return false;
            }
            
            const materiaSelect = detalleItem.find('.select2-materia');
            const cantidad = detalleItem.find('.cantidad-input');
            const costo = detalleItem.find('.costo-input');
            
            if (!materiaSelect.val() || materiaSelect.val() === 'nueva') {
                isValid = false;
                errorMessage = 'Seleccione una materia prima válida para todos los detalles';
                return false;
            }
            
            if (!cantidad.val() || parseInt(cantidad.val()) <= 0) {
                isValid = false;
                errorMessage = 'Ingrese una cantidad válida para todos los detalles';
                return false;
            }
            
            if (!costo.val() || parseFloat(costo.val()) <= 0) {
                isValid = false;
                errorMessage = 'Ingrese un costo válido para todos los detalles';
                return false;
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            mostrarMensaje(errorMessage, 'warning');
        }
    });

    $('#registrarFacturaModal').on('show.bs.modal', function() {
        $('#detalles-container').empty();
        detalleCount = 0;
        
        $('#registrarFacturaModal .alert').remove();
        
        $('#proveedor').val('').trigger('change');
        $('#num_factura').val('');
        $('#fecha').val(new Date().toISOString().split('T')[0]);
        
        $('#agregar-detalle').click();
    });

    $('#registrarFacturaModal').on('shown.bs.modal', function() {
        if ($('#proveedor').hasClass('select2-hidden-accessible')) {
            $('#proveedor').select2('destroy');
        }
        
        inicializarSelect2Proveedor();
        
        const primerDetalle = $('#detalles-container .select2-materia').first();
        if (primerDetalle.length && !primerDetalle.hasClass('select2-hidden-accessible')) {
            inicializarSelect2Materia(primerDetalle);
        }
    });
});