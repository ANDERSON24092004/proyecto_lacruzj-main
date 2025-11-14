let itemCount = 0;
let ivaPorcentaje = 16;

window.agregarProducto = function() {
    agregarItem('producto');
}

window.agregarServicio = function() {
    agregarItem('servicio');
}

function agregarItem(tipo) {
    const templateElement = document.getElementById(`template-${tipo}`);
    if (!templateElement) return;

    const nuevoItem = templateElement.cloneNode(true);
    nuevoItem.style.display = 'flex';
    nuevoItem.classList.add('item-row');
    nuevoItem.setAttribute('data-tipo', tipo);
    nuevoItem.removeAttribute('id');

    const itemsContainer = document.getElementById('items-container');
    const itemsVisibles = itemsContainer.querySelectorAll('.item-row');
    const currentIndex = itemsVisibles.length;

    const inputs = nuevoItem.querySelectorAll('[name]');
    inputs.forEach(input => {
        const name = input.getAttribute('name').replace('[INDEX]', `[${currentIndex}]`);
        input.setAttribute('name', name);
    });

    const select = nuevoItem.querySelector('.item-select');
    const cantidad = nuevoItem.querySelector('.cantidad-item');
    const btnQuitar = nuevoItem.querySelector('.quitar-item');

    select.selectedIndex = 0;
    select.value = "";

    if (tipo === 'producto') {
        const stockInfo = nuevoItem.querySelector('.stock-info');
        
        select.addEventListener('change', function() {
            const stock = parseInt(this.selectedOptions[0]?.getAttribute('data-stock')) || 0;
            if (stockInfo) {
                stockInfo.textContent = `Stock disponible: ${stock}`;
                stockInfo.style.display = 'block';
                
                const cantActual = parseInt(cantidad.value) || 1;
                if (cantActual > stock) {
                    stockInfo.className = 'form-text text-danger';
                    cantidad.setCustomValidity('Stock insuficiente');
                } else {
                    stockInfo.className = 'form-text text-info';
                    cantidad.setCustomValidity('');
                }
            }
            limpiarErrorItem(nuevoItem);
            actualizarPrecioItem(nuevoItem);
        });

        cantidad.addEventListener('input', function() {
            const stock = parseInt(select.selectedOptions[0]?.getAttribute('data-stock')) || 0;
            const cant = parseInt(this.value) || 0;
            
            if (stockInfo) {
                if (cant > stock) {
                    stockInfo.className = 'form-text text-danger';
                    this.setCustomValidity('Stock insuficiente');
                } else {
                    stockInfo.className = 'form-text text-info';
                    this.setCustomValidity('');
                }
            }
            limpiarErrorItem(nuevoItem);
            actualizarPrecioItem(nuevoItem);
        });
    } else {
        select.addEventListener('change', function() {
            limpiarErrorItem(nuevoItem);
            actualizarPrecioItem(nuevoItem);
        });

        cantidad.addEventListener('input', function() {
            limpiarErrorItem(nuevoItem);
            actualizarPrecioItem(nuevoItem);
        });
    }

    btnQuitar.addEventListener('click', function() {
        nuevoItem.remove();
        validarItems();
        calcularTotales();
    });

    itemsContainer.appendChild(nuevoItem);
    itemCount = itemsVisibles.length + 1;
    
    const errorItems = document.getElementById('error-items');
    if (errorItems) errorItems.classList.add('d-none');
    
    calcularTotales();
}

function limpiarErrorItem(item) {
    const select = item.querySelector('.item-select');
    const cantidad = item.querySelector('.cantidad-item');
    if (select) select.classList.remove('is-invalid');
    if (cantidad) cantidad.classList.remove('is-invalid');
}

function actualizarPrecioItem(item) {
    const select = item.querySelector('.item-select');
    const cantidad = item.querySelector('.cantidad-item');
    const precioUnitario = item.querySelector('.precio-unitario');
    const subtotalItem = item.querySelector('.subtotal-item');

    if (select.value && cantidad.value) {
        const tipo = select.selectedOptions[0].getAttribute('data-tipo');
        const costoNormal = parseFloat(select.selectedOptions[0].getAttribute('data-costo')) || 0;
        const costoMayor = parseFloat(select.selectedOptions[0].getAttribute('data-costo-mayor')) || 0;
        const cant = parseInt(cantidad.value) || 1;
        
        let precio = costoNormal;
        if (tipo === '1' && cant > 20) {
            precio = costoMayor > 0 ? costoMayor : costoNormal;
        }
        
        const subtotal = precio * cant;
        
        precioUnitario.value = precio.toFixed(2);
        subtotalItem.value = subtotal.toFixed(2);
    } else {
        precioUnitario.value = '0.00';
        subtotalItem.value = '0.00';
    }
    
    calcularTotales();
}

window.calcularTotales = function() {
    let subtotal = 0;

    document.querySelectorAll('.item-row').forEach(item => {
        const subtotalItem = parseFloat(item.querySelector('.subtotal-item').value) || 0;
        subtotal += subtotalItem;
    });

    const descuentoSelect = document.getElementById('id_descuento');
    let descuentoPorcentaje = 0;
    
    if (descuentoSelect.value) {
        const textoDescuento = descuentoSelect.selectedOptions[0].textContent;
        const match = textoDescuento.match(/(\d+)%/);
        descuentoPorcentaje = match ? parseFloat(match[1]) : 0;
    }
    
    const descuentoMonto = subtotal * (descuentoPorcentaje / 100);
    const subtotalConDescuento = subtotal - descuentoMonto;
    const iva = subtotalConDescuento * (ivaPorcentaje / 100);
    const totalGeneral = subtotalConDescuento + iva;

    document.getElementById('subtotal-total').textContent = subtotal.toFixed(2);
    document.getElementById('descuento-total').textContent = descuentoMonto.toFixed(2);
    document.getElementById('subtotal-con-descuento').textContent = subtotalConDescuento.toFixed(2);
    document.getElementById('iva-total').textContent = iva.toFixed(2);
    document.getElementById('total-general').textContent = totalGeneral.toFixed(2);
    document.getElementById('total_iva_input').value = iva.toFixed(2);
    document.getElementById('total_general_input').value = totalGeneral.toFixed(2);

    updateIvaLabel();
}

function updateIvaLabel() {
    const ivaPorcentajeElem = document.getElementById('iva-porcentaje');
    if (ivaPorcentajeElem) ivaPorcentajeElem.textContent = ivaPorcentaje;
}

// CORREGIR: Cambiar los IDs de los elementos de error
function mostrarError(campo, mensaje) {
    const elemento = document.getElementById(campo);
    if (elemento) {
        elemento.classList.add('is-invalid');
        // CORREGIR: Usar los IDs correctos de los elementos de error
        let feedback;
        switch(campo) {
            case 'rif':
                feedback = document.getElementById('error-rif');
                break;
            case 'id_condicion_pago':
                feedback = document.getElementById('error-pago');
                break;
            case 'numero_orden':
                feedback = document.getElementById('error-orden');
                break;
            case 'fecha':
                feedback = document.getElementById('error-fecha');
                break;
            case 'id_iva':
                feedback = document.getElementById('error-iva');
                break;
            default:
                feedback = document.getElementById(`error-${campo}`);
        }
        
        if (feedback) {
            feedback.textContent = mensaje;
            feedback.style.display = 'block';
        }
    }
}

function limpiarError(campo) {
    const elemento = document.getElementById(campo);
    if (elemento) {
        elemento.classList.remove('is-invalid');
        // CORREGIR: Usar los IDs correctos de los elementos de error
        let feedback;
        switch(campo) {
            case 'rif':
                feedback = document.getElementById('error-rif');
                break;
            case 'id_condicion_pago':
                feedback = document.getElementById('error-pago');
                break;
            case 'numero_orden':
                feedback = document.getElementById('error-orden');
                break;
            case 'fecha':
                feedback = document.getElementById('error-fecha');
                break;
            case 'id_iva':
                feedback = document.getElementById('error-iva');
                break;
            default:
                feedback = document.getElementById(`error-${campo}`);
        }
        
        if (feedback) feedback.style.display = 'none';
    }
}

function mostrarErrorGeneral(mensaje) {
    const errorGeneral = document.getElementById('errores-generales');
    const mensajeError = document.getElementById('mensaje-error-general');
    if (errorGeneral && mensajeError) {
        mensajeError.textContent = mensaje;
        errorGeneral.classList.remove('d-none');
        errorGeneral.scrollIntoView({ behavior: 'smooth' });
    }
}

function limpiarErrorGeneral() {
    const errorGeneral = document.getElementById('errores-generales');
    if (errorGeneral) errorGeneral.classList.add('d-none');
}

function validarCamposBasicos() {
    let valido = true;
    
    const rif = document.getElementById('rif');
    if (!rif.value) {
        mostrarError('rif', 'Por favor seleccione un cliente');
        valido = false;
    } else {
        limpiarError('rif');
    }
    
    const pago = document.getElementById('id_condicion_pago');
    if (!pago.value) {
        mostrarError('id_condicion_pago', 'Por favor seleccione una condición de pago');
        valido = false;
    } else {
        limpiarError('id_condicion_pago');
    }
    
    const orden = document.getElementById('numero_orden');
    if (!orden.value || orden.value.trim() === '') {
        mostrarError('numero_orden', 'Por favor ingrese un número de orden');
        valido = false;
    } else if (!/^\d+$/.test(orden.value)) {
        mostrarError('numero_orden', 'El número de orden solo puede contener números');
        valido = false;
    } else {
        limpiarError('numero_orden');
    }
    
    const fecha = document.getElementById('fecha');
    if (!fecha.value) {
        mostrarError('fecha', 'Por favor ingrese una fecha válida');
        valido = false;
    } else {
        limpiarError('fecha');
    }
    
    const iva = document.getElementById('id_iva');
    if (!iva.value) {
        mostrarError('id_iva', 'Por favor seleccione un porcentaje de IVA');
        valido = false;
    } else {
        limpiarError('id_iva');
    }
    
    return valido;
}

function validarItems() {
    const itemsContainer = document.getElementById('items-container');
    const itemsReales = itemsContainer.querySelectorAll('.item-row');
    const errorItems = document.getElementById('error-items');
    
    if (itemsReales.length === 0) {
        if (errorItems) {
            errorItems.textContent = 'Debe agregar al menos un producto o servicio';
            errorItems.classList.remove('d-none');
        }
        return false;
    } else if (errorItems) {
        errorItems.classList.add('d-none');
    }
    
    let todosValidos = true;
    let hayAlMenosUnItemValido = false;
    
    itemsReales.forEach((item, index) => {
        const select = item.querySelector('.item-select');
        const cantidad = item.querySelector('.cantidad-item');

        select.classList.remove('is-invalid');
        cantidad.classList.remove('is-invalid');
        
        let itemValido = true;
        
        if (!select.value) {
            select.classList.add('is-invalid');
            itemValido = false;
            todosValidos = false;
        }

        if (!cantidad.value || parseInt(cantidad.value) <= 0) {
            cantidad.classList.add('is-invalid');
            itemValido = false;
            todosValidos = false;
        }
        
        if (select.value && cantidad.value) {
            const tipo = select.selectedOptions[0]?.getAttribute('data-tipo');
            if (tipo === '1') {
                const stock = parseInt(select.selectedOptions[0]?.getAttribute('data-stock')) || 0;
                const cant = parseInt(cantidad.value) || 0;
                if (cant > stock) {
                    cantidad.classList.add('is-invalid');
                    itemValido = false;
                    todosValidos = false;
                }
            }
        }
        
        if (itemValido) {
            hayAlMenosUnItemValido = true;
        }
    });
    
    return todosValidos && hayAlMenosUnItemValido;
}

function validarFormularioCompleto() {
    limpiarErrorGeneral();
    
    const camposValidos = validarCamposBasicos();
    const itemsValidos = validarItems();
    
    if (!camposValidos || !itemsValidos) {
        let mensajesError = [];
        
        if (!camposValidos) {
            mensajesError.push('Complete todos los campos obligatorios');
        }
        
        if (!itemsValidos) {
            mensajesError.push('Complete correctamente todos los items de productos/servicios');
        }
        
        mostrarErrorGeneral(mensajesError.join(' y '));
        return false;
    }
    
    return true;
}

function limpiarModalCompletamente() {
    const form = document.getElementById('formFactura');
    if (form) form.reset();
    
    const itemsContainer = document.getElementById('items-container');
    if (itemsContainer) itemsContainer.innerHTML = '';
    
    limpiarErrorGeneral();
    // CORREGIR: Usar los IDs correctos
    ['rif', 'id_condicion_pago', 'numero_orden', 'fecha', 'id_iva'].forEach(campo => limpiarError(campo));
    
    document.querySelectorAll('.is-invalid, .is-valid').forEach(el => {
        el.classList.remove('is-invalid', 'is-valid');
    });
    
    const fechaInput = document.getElementById('fecha');
    if (fechaInput) fechaInput.value = new Date().toISOString().split('T')[0];
    
    const ivaSelect = document.getElementById('id_iva');
    if (ivaSelect) {
        ivaSelect.value = '';
        ivaPorcentaje = 16;
    }
    
    calcularTotales();
}

function inicializarModalFactura() {
    const subtotalTotal = document.getElementById('subtotal-total');
    const descuentoTotal = document.getElementById('descuento-total');
    const subtotalConDescuentoElem = document.getElementById('subtotal-con-descuento');
    const ivaTotal = document.getElementById('iva-total');
    const totalGeneralElem = document.getElementById('total-general');
    
    if (subtotalTotal) subtotalTotal.textContent = '0.00';
    if (descuentoTotal) descuentoTotal.textContent = '0.00';
    if (subtotalConDescuentoElem) subtotalConDescuentoElem.textContent = '0.00';
    if (ivaTotal) ivaTotal.textContent = '0.00';
    if (totalGeneralElem) totalGeneralElem.textContent = '0.00';
    
    limpiarErrorGeneral();
    document.querySelectorAll('.is-invalid, .is-valid').forEach(el => {
        el.classList.remove('is-invalid', 'is-valid');
    });
}

function configurarValidacionTiempoReal() {
    const camposRequeridos = ['rif', 'id_condicion_pago', 'numero_orden', 'fecha', 'id_iva'];
    
    camposRequeridos.forEach(campo => {
        const elemento = document.getElementById(campo);
        if (elemento) {
            elemento.addEventListener('change', function() {
                if (this.value) {
                    limpiarError(campo);
                    if (campo === 'numero_orden' && this.value && !/^\d+$/.test(this.value)) {
                        mostrarError(campo, 'El número de orden solo puede contener números');
                    }
                }
            });
            
            if (campo === 'numero_orden') {
                elemento.addEventListener('input', function() {
                    this.value = this.value.replace(/[^0-9]/g, '');
                    if (this.value && /^\d+$/.test(this.value)) {
                        limpiarError(campo);
                    }
                });
            }
        }
    });
}

function configurarEventListenersModal() {
    const agregarProductoBtn = document.getElementById('agregarProducto');
    const agregarServicioBtn = document.getElementById('agregarServicio');
    const descuentoSelect = document.getElementById('id_descuento');
    const ivaSelect = document.getElementById('id_iva');
    const formFactura = document.getElementById('formFactura');
    
    if (agregarProductoBtn) agregarProductoBtn.addEventListener('click', agregarProducto);
    if (agregarServicioBtn) agregarServicioBtn.addEventListener('click', agregarServicio);

    if (descuentoSelect) descuentoSelect.addEventListener('change', calcularTotales);

    if (ivaSelect) {
        ivaSelect.addEventListener('change', function() {
            if (this.value) {
                const textoIva = this.selectedOptions[0].textContent;
                const match = textoIva.match(/(\d+)%/);
                ivaPorcentaje = match ? parseFloat(match[1]) : 16;
            } else ivaPorcentaje = 16;
            calcularTotales();
        });

        if (ivaSelect.value) {
            const textoIva = ivaSelect.selectedOptions[0].textContent;
            const match = textoIva.match(/(\d+)%/);
            ivaPorcentaje = match ? parseFloat(match[1]) : 16;
        }
    }

    if (formFactura) {
        formFactura.addEventListener('submit', function(e) {
            if (!validarFormularioCompleto()) {
                e.preventDefault();
                const primerError = document.querySelector('.is-invalid');
                if (primerError) {
                    primerError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
    }
    
    configurarValidacionTiempoReal();
    
    const btnCancelar = document.getElementById('btn-cancelar');
    if (btnCancelar) {
        btnCancelar.addEventListener('click', limpiarModalCompletamente);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('item-select') || e.target.classList.contains('cantidad-item')) {
            const item = e.target.closest('.item-row');
            if (item) limpiarErrorItem(item);
        }
    });

    const crearFacturaModal = document.getElementById('crearFacturaModal');
    if (crearFacturaModal) {
        crearFacturaModal.addEventListener('show.bs.modal', function() {
            inicializarModalFactura();
            setTimeout(() => configurarEventListenersModal(), 100);
        });

        crearFacturaModal.addEventListener('shown.bs.modal', function() {
            setTimeout(calcularTotales, 100);
        });
    }

if (formFactura) {
    formFactura.addEventListener('submit', function(e) {
        e.preventDefault(); 
        
        if (!validarFormularioCompleto()) {
            const primerError = document.querySelector('.is-invalid');
            if (primerError) {
                primerError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            return;
        }
        
        enviarFacturaAJAX();
    });
}

function enviarFacturaAJAX() {
    const formData = new FormData(document.getElementById('formFactura'));
    
    const submitBtn = document.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Guardando...';
    submitBtn.disabled = true;

    fetch('index.php?c=FacturaControlador&m=guardar_factura', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (typeof mostrarMensaje === 'function') {
                mostrarMensaje('success', data.message, data.details);
            } else {
                alert(data.message + ': ' + data.details);
            }
            
            const modal = bootstrap.Modal.getInstance(document.getElementById('crearFacturaModal'));
            if (modal) modal.hide();
            
            limpiarModalCompletamente();
            
            if (typeof cargarTablaFacturas === 'function') {
                cargarTablaFacturas();
            }
            
        } else {
            if (typeof mostrarMensaje === 'function') {
                mostrarMensaje('error', data.message, data.details);
            } else {
                alert('Error: ' + data.message + ': ' + data.details);
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (typeof mostrarMensaje === 'function') {
            mostrarMensaje('error', 'Error', 'Error de conexión: ' + error);
        } else {
            alert('Error de conexión: ' + error);
        }
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}
});