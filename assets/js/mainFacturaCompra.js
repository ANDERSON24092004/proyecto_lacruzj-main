$(document).ready(function() {
    let detalleCount = 0;
    const IVA_PORCENTAJE = 0.16;

    cargarFacturas();

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

     $('#registrarFacturaModal').on('hidden.bs.modal', function () {
        restaurarScroll();
    });
    
    // $('#confirmarAnularModal').on('hidden.bs.modal', function () {
    //     restaurarScroll();
    // });
    
    // $('#confirmarReactivarModal').on('hidden.bs.modal', function () {
    //     restaurarScroll();
    // });
    
    // $('#detalleFacturaModal').on('hidden.bs.modal', function () {
    //     restaurarScroll();
    // });
    
    // $('#mensajeModal').on('hidden.bs.modal', function () {
    //     restaurarScroll();
    // });

    function cargarFacturas() {
        $.ajax({
            url: 'index.php?c=FacturaCompraControlador&m=listar',
            type: 'GET',
            dataType: 'json',
            beforeSend: function() {
                $('tbody').html(`
                    <tr>
                        <td colspan="7" class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                            <p class="mt-2">Cargando Datos...</p>
                        </td>
                    </tr>
                `);
            },
            success: function(response) {
                if (response.success && response.data && response.data.facturas) {
                    actualizarTablaFacturas(response.data.facturas);
                } else {
                    mostrarErrorTabla(response.mensaje || 'Error al cargar las facturas');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error AJAX:', error);
                mostrarErrorTabla('Error de conexión al cargar las facturas');
            }
        });
    }

    function actualizarTablaFacturas(facturas) {
        if (!Array.isArray(facturas)) {
            console.error('facturas no es un array:', facturas);
            mostrarErrorTabla('Error en el formato de datos recibido');
            return;
        }

        if (facturas.length === 0) {
            $('tbody').html(`
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        <i class="fas fa-inbox fa-3x mb-3"></i>
                        <p>No hay facturas registradas</p>
                    </td>
                </tr>
            `);
            return;
        }

        let html = '';
        facturas.forEach(factura => {
            const estado = factura.status || 0;
            const estadoBadge = estado == 1 ? 
                '<span class="badge bg-danger">Anulado</span>' : 
                '<span class="badge bg-success">Vigente</span>';
            
            const botones = estado == 1 ? 
                `<button class="btn btn-success btn-sm btn-reactivar" data-id="${factura.id_fact_com}" title="Reactivar">
                    <i class="fas fa-undo"></i>
                </button>` :
                `<button class="btn btn-info btn-sm btn-ver-detalle" data-id="${factura.id_fact_com}" title="Ver Detalle">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="btn btn-danger btn-sm btn-anular" data-id="${factura.id_fact_com}" title="Anular">
                    <i class="fas fa-ban"></i>
                </button>`;

            html += `
                <tr>
                    <td>${factura.id_fact_com || ''}</td>
                    <td>${factura.num_factura || ''}</td>
                    <td>${factura.proveedor || 'N/A'}</td>
                    <td>${factura.fecha || ''}</td>
                    <td>$${parseFloat(factura.total_general || 0).toFixed(2)}</td>
                    <td>${estadoBadge}</td>
                    <td>
                        <div class="btn-group" role="group">
                            ${botones}
                        </div>
                    </td>
                </tr>
            `;
        });

        $('tbody').html(html);
    }

    function mostrarErrorTabla(mensaje) {
        $('tbody').html(`
            <tr>
                <td colspan="7" class="text-center text-danger py-4">
                    <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                    <p>${mensaje}</p>
                    <button class="btn btn-primary btn-sm mt-2" onclick="window.cargarFacturas()">
                        <i class="fas fa-redo"></i> Reintentar
                    </button>
                </td>
            </tr>
        `);
    }

    function anularFactura(id) {
        $.ajax({
            url: 'index.php?c=FacturaCompraControlador&m=anular',
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            beforeSend: function() {
                $('#confirmarAnularModal').modal('hide');
            },
            success: function(response) {
                if (response.success) {
                    mostrarMensaje('success', response.mensaje, response.mensaje_detalle || '');
                    cargarFacturas();
                } else {
                    mostrarMensaje('error', response.mensaje, response.mensaje_detalle || '');
                }
            },
            error: function() {
                mostrarMensaje('error', 'Error de conexión', 'No se pudo conectar con el servidor');
            }
        });
    }

    function reactivarFactura(id) {
        $.ajax({
            url: 'index.php?c=FacturaCompraControlador&m=reactivar',
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            beforeSend: function() {
                $('#confirmarReactivarModal').modal('hide');
            },
            success: function(response) {
                if (response.success) {
                    mostrarMensaje('success', response.mensaje, response.mensaje_detalle || '');
                    cargarFacturas();
                } else {
                    mostrarMensaje('error', response.mensaje, response.mensaje_detalle || '');
                }
            },
            error: function() {
                mostrarMensaje('error', 'Error de conexión', 'No se pudo conectar con el servidor');
            }
        });
    }

    function mostrarMensajeModal(tipo, mensaje, detalle = '') {
        if (typeof mostrarMensaje === 'function') {
            mostrarMensaje(tipo, mensaje, detalle);
        } else {
            alert(mensaje + (detalle ? '\n' + detalle : ''));
        }
    }

    $(document).on('click', '.btn-anular', function() {
        const id = $(this).data('id');
        $('#btnAnularConfirmado').data('id', id);
        $('#confirmarAnularModal').modal('show');
    });

    $(document).on('click', '.btn-reactivar', function() {
        const id = $(this).data('id');
        $('#btnReactivarConfirmado').data('id', id);
        $('#confirmarReactivarModal').modal('show');
    });

    $(document).on('click', '.btn-ver-detalle', function() {
        const id = $(this).data('id');
        verDetalleFactura(id);
    });

    $('#btnAnularConfirmado').on('click', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        anularFactura(id);
    });

    $('#btnReactivarConfirmado').on('click', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        reactivarFactura(id);
    });

    $(document).off('submit', '#formFacturaCompra').on('submit', '#formFacturaCompra', function(e) {
        e.preventDefault();
        
        if (!validarFormulario()) {
            return false;
        }
        
        const formData = new FormData(this);
        
        $('.detalle-item').each(function(index) {
            const materiaSelect = $(this).find('.select2-materia');
            const cantidad = $(this).find('.cantidad-input');
            const costo = $(this).find('.costo-input');
            
            if (materiaSelect.val() && materiaSelect.val() !== 'nueva') {
                formData.append(`detalles[${index}][id_materia_prima]`, materiaSelect.val());
                formData.append(`detalles[${index}][cantidad]`, cantidad.val());
                formData.append(`detalles[${index}][costo]`, costo.val());
            }
        });

        $.ajax({
            url: 'index.php?c=FacturaCompraControlador&m=registrar',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend: function() {
                $('#registrarFacturaModal').modal('hide');
                $('body').append(`
                    <div class="modal-backdrop fade show" id="loadingBackdrop">
                        <div class="d-flex justify-content-center align-items-center h-100">
                            <div class="spinner-border text-white" role="status">
                                <span class="visually-hidden">Procesando...</span>
                            </div>
                            <span class="text-white ms-3">Procesando factura...</span>
                        </div>
                    </div>
                `);
            },
            success: function(response) {
                $('#loadingBackdrop').remove();
                if (response.success) {
                    mostrarMensaje('success', response.mensaje, response.mensaje_detalle || '');
                    $('#registrarFacturaModal').modal('hide');
                    cargarFacturas();
                    $('#formFacturaCompra')[0].reset();
                    $('#detalles-container').empty();
                    detalleCount = 0;
                    limpiarValidaciones();
                } else {
                    mostrarMensaje('error', response.mensaje, response.mensaje_detalle || '');
                    $('#registrarFacturaModal').modal('show');
                }
            },
            error: function(xhr, status, error) {
                $('#loadingBackdrop').remove();
                mostrarMensaje('error', 'Error de conexión', 'No se pudo registrar la factura: ' + error);
                $('#registrarFacturaModal').modal('show');
            }
        });
    });

    function verDetalleFactura(id) {
        $.ajax({
            url: 'index.php?c=FacturaCompraControlador&m=verDetalle',
            type: 'GET',
            data: { id: id },
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data) {
                    mostrarDetalleModal(response.data.factura, response.data.detalles);
                } else {
                    mostrarMensaje('error', 'Error', response.mensaje || 'No se pudo cargar el detalle');
                }
            },
            error: function() {
                mostrarMensaje('error', 'Error de conexión', 'No se pudo cargar el detalle');
            }
        });
    }

    function mostrarDetalleModal(factura, detalles) {
        let detallesHtml = '';
        if (Array.isArray(detalles)) {
            detalles.forEach(detalle => {
                detallesHtml += `
                    <tr>
                        <td>${detalle.materia_prima || 'N/A'}</td>
                        <td>${detalle.cantidad || 0}</td>
                        <td>$${parseFloat(detalle.costo_compra || 0).toFixed(2)}</td>
                        <td>$${parseFloat((detalle.cantidad || 0) * (detalle.costo_compra || 0)).toFixed(2)}</td>
                    </tr>
                `;
            });
        }

        const modalHtml = `
            <div class="modal fade" id="detalleFacturaModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-file-invoice me-2"></i> Detalle Factura #${factura.num_factura || ''}
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Proveedor:</strong> ${factura.proveedor || 'N/A'}<br>
                                    <strong>Fecha:</strong> ${factura.fecha || ''}
                                </div>
                                <div class="col-md-6">
                                    <strong>Total IVA:</strong> $${parseFloat(factura.total_iva || 0).toFixed(2)}<br>
                                    <strong>Total General:</strong> $${parseFloat(factura.total_general || 0).toFixed(2)}
                                </div>
                            </div>
                            <h6>Detalles de Materia Prima:</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>Materia Prima</th>
                                            <th>Cantidad</th>
                                            <th>Costo Unitario</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${detallesHtml || '<tr><td colspan="4" class="text-center">No hay detalles</td></tr>'}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        $('#detalleFacturaModal').remove();

        $('body').append(modalHtml);
        $('#detalleFacturaModal').modal('show');
    }

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

                console.log('Datos recibidos:', {nombre, unidad, stock, costo});
                
                detalleItem.find('.unidad-medida').val(unidad);
                detalleItem.find('.stock-info').text(`Stock actual: ${stock}`);
                detalleItem.find('.costo-input').val(costo || '');

                const cantidad = parseInt(detalleItem.find('.cantidad-input').val()) || 0;
                const nuevoStock = parseInt(stock) + cantidad;
                detalleItem.find('.nuevo-stock-info').text(`Nuevo stock: ${nuevoStock}`);
                calcularSubtotalItem(detalleItem);
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

    function calcularSubtotalItem(detalleItem) {
        const cantidad = parseFloat(detalleItem.find('.cantidad-input').val()) || 0;
        const costo = parseFloat(detalleItem.find('.costo-input').val()) || 0;
        const subtotal = cantidad * costo;
        detalleItem.find('.subtotal-input').val(subtotal.toFixed(2));
    }

    $(document).on('input', '.cantidad-input, .costo-input', function() {
        const detalleItem = $(this).closest('.detalle-item');
        calcularSubtotalItem(detalleItem);
        calcularTotales();

        const selectedOption = detalleItem.find('.select2-materia').find('option:selected');
            if (selectedOption.val() && selectedOption.val() !== 'nueva') {
            const stock = parseInt(selectedOption.data('stock')) || 0;
            const cantidad = parseInt(detalleItem.find('.cantidad-input').val()) || 0;
            const nuevoStock = stock + cantidad;
            detalleItem.find('.nuevo-stock-info').text(`Nuevo stock: ${nuevoStock}`);
        }
    });

    $('#registrarFacturaModal').on('show.bs.modal', function() {
        if ($('#detalles-container').children().length === 0) {
            $('#detalles-container').empty();
            detalleCount = 0;
            $('#agregar-detalle').click();
        }
        
        $('#registrarFacturaModal .alert').remove();
        
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

    $('#btnGuardar').on('click', function() {
        if (validarFormulario()) {
            $('#formFacturaCompra').submit();
        } else {
            const erroresGenerales = $('#errores-generales');
            const textoError = $('#texto-error-general');
            textoError.text('Por favor complete todos los campos requeridos correctamente');
            erroresGenerales.removeClass('d-none');
            
            $('.modal-body').scrollTop(0);
        }
    });

    $('#btnCancelar').on('click', function() {
        $('#formFacturaCompra')[0].reset();
        limpiarValidaciones();
        $('#detalles-container').empty();
        detalleCount = 0;
        $('#agregar-detalle').click();
    });

    $('#registrarFacturaModal').on('hide.bs.modal', function(event) {
       
    });
});

window.validarFormulario = function() {
    let esValido = true;
    
    window.limpiarValidaciones();
    
    const proveedor = $('#proveedor').val();
    const numFactura = $('#num_factura').val().trim();
    const fecha = $('#fecha').val();
    
    if (!proveedor) {
        $('#proveedor').addClass('is-invalid');
        esValido = false;
    } else {
        $('#proveedor').removeClass('is-invalid').addClass('is-valid');
    }
    
    if (!numFactura) {
        $('#num_factura').addClass('is-invalid');
        esValido = false;
    } else {
        const regex = /^[0-9\-]+$/;
        if (!regex.test(numFactura)) {
            $('#num_factura').addClass('is-invalid');
            esValido = false;
        } else {
            $('#num_factura').removeClass('is-invalid').addClass('is-valid');
        }
    }
    
    if (!fecha) {
        $('#fecha').addClass('is-invalid');
        esValido = false;
    } else {
        const fechaInput = new Date(fecha);
        const hoy = new Date();
        hoy.setHours(0, 0, 0, 0);
        
        if (fechaInput > hoy) {
            $('#fecha').addClass('is-invalid');
            esValido = false;
        } else {
            $('#fecha').removeClass('is-invalid').addClass('is-valid');
        }
    }
    
    const detalles = $('.detalle-item');
    if (detalles.length === 0) {
        $('#error-detalles').removeClass('d-none');
        esValido = false;
    } else {
        $('#error-detalles').addClass('d-none');
        
        let hayDetallesValidos = false;
        
        detalles.each(function(index) {
            const materia = $(this).find('.select2-materia');
            const cantidad = $(this).find('.cantidad-input');
            const costo = $(this).find('.costo-input');
            
            let detalleValido = true;
            
            if (!materia.val()) {
                materia.addClass('is-invalid');
                detalleValido = false;
                esValido = false;
            } else {
                materia.removeClass('is-invalid').addClass('is-valid');
            }
            
            const cantidadVal = parseFloat(cantidad.val());
            if (!cantidad.val() || isNaN(cantidadVal) || cantidadVal <= 0) {
                cantidad.addClass('is-invalid');
                detalleValido = false;
                esValido = false;
            } else {
                cantidad.removeClass('is-invalid').addClass('is-valid');
            }
            
            const costoVal = parseFloat(costo.val());
            if (!costo.val() || isNaN(costoVal) || costoVal <= 0) {
                costo.addClass('is-invalid');
                detalleValido = false;
                esValido = false;
            } else {
                costo.removeClass('is-invalid').addClass('is-valid');
            }
            
            if (detalleValido) {
                hayDetallesValidos = true;
            }
        });
        
        if (!hayDetallesValidos) {
            esValido = false;
            $('#error-detalles').text('Debe agregar al menos un detalle de materia prima válido').removeClass('d-none');
        }
    }
    
    return esValido;
};

window.limpiarValidaciones = function() {
    $('#formFacturaCompra').find('input, select').removeClass('is-invalid is-valid');
    $('#errores-generales').addClass('d-none');
    $('#error-detalles').addClass('d-none');
};

window.validarNumeroFactura = function(input) {
    const valor = input.value;
    const regex = /^[0-9\-]*$/;
    
    if (!regex.test(valor)) {
        input.value = valor.replace(/[^0-9\-]/g, '');
    }
 
    if (input.value.trim() === '') {
        input.classList.add('is-invalid');
        input.classList.remove('is-valid');
    } else {
        input.classList.remove('is-invalid');
        input.classList.add('is-valid');
    }
};

window.validarCantidad = function(input) {
    const valor = parseFloat(input.value);
    
    if (isNaN(valor) || valor <= 0) {
        input.classList.add('is-invalid');
        input.classList.remove('is-valid');
    } else {
        input.classList.remove('is-invalid');
        input.classList.add('is-valid');
    }
};

window.validarCosto = function(input) {
    const valor = parseFloat(input.value);
    
    if (isNaN(valor) || valor <= 0) {
        input.classList.add('is-invalid');
        input.classList.remove('is-valid');
    } else {
        input.classList.remove('is-invalid');
        input.classList.add('is-valid');
    }
};

window.cargarFacturas = function() {
    $(document).ready(function() {
        if (typeof cargarFacturas === 'function') {
            cargarFacturas();
        }
    });
};