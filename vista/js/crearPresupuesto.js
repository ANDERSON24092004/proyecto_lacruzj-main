let itemCount = 0;
let ivaPorcentaje = 16;

window.agregarProducto = function() {
    agregarItem('producto');
}

window.agregarServicio = function() {
    agregarItem('servicio');
}

function agregarItem(tipo) {
    console.log('Agregando item de tipo:', tipo);
    
    const templateElement = document.getElementById(`template-${tipo}`);
    if (!templateElement) {
        console.error(`No se encontró la plantilla de ${tipo}`);
        return;
    }

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
            actualizarPrecioItem(nuevoItem);
        });
    } else {
        select.addEventListener('change', function() {
            actualizarPrecioItem(nuevoItem);
        });

        cantidad.addEventListener('input', function() {
            actualizarPrecioItem(nuevoItem);
        });
    }

    btnQuitar.addEventListener('click', function() {
        nuevoItem.remove();
        calcularTotales();
    });

    itemsContainer.appendChild(nuevoItem);
    itemCount = itemsVisibles.length + 1;
    calcularTotales();
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
    
    if (descuentoSelect && descuentoSelect.value) {
        const textoDescuento = descuentoSelect.selectedOptions[0].textContent;
        const match = textoDescuento.match(/(\d+)%/);
        descuentoPorcentaje = match ? parseFloat(match[1]) : 0;
    }
    
    const descuentoMonto = subtotal * (descuentoPorcentaje / 100);
    const subtotalConDescuento = subtotal - descuentoMonto;

    const iva = subtotalConDescuento * (ivaPorcentaje / 100);
    const totalGeneral = subtotalConDescuento + iva;

    const subtotalTotal = document.getElementById('subtotal-total');
    const descuentoTotal = document.getElementById('descuento-total');
    const subtotalConDescuentoElem = document.getElementById('subtotal-con-descuento');
    const ivaTotal = document.getElementById('iva-total');
    const totalGeneralElem = document.getElementById('total-general');
    const totalIvaInput = document.getElementById('total_iva_input');
    const totalGeneralInput = document.getElementById('total_general_input');

    if (subtotalTotal) subtotalTotal.textContent = subtotal.toFixed(2);
    if (descuentoTotal) descuentoTotal.textContent = descuentoMonto.toFixed(2);
    if (subtotalConDescuentoElem) subtotalConDescuentoElem.textContent = subtotalConDescuento.toFixed(2);
    if (ivaTotal) ivaTotal.textContent = iva.toFixed(2);
    if (totalGeneralElem) totalGeneralElem.textContent = totalGeneral.toFixed(2);
    if (totalIvaInput) totalIvaInput.value = iva.toFixed(2);
    if (totalGeneralInput) totalGeneralInput.value = totalGeneral.toFixed(2);

    updateIvaLabel();
}

function updateIvaLabel() {
    const ivaPorcentajeElem = document.getElementById('iva-porcentaje');
    if (ivaPorcentajeElem) {
        ivaPorcentajeElem.textContent = ivaPorcentaje;
    }
    
    document.querySelectorAll('p').forEach(element => {
        if (element.innerHTML.includes('IVA')) {
            const nuevoHTML = element.innerHTML.replace(/IVA\s*\(\d+%\):/g, `IVA (${ivaPorcentaje}%):`);
            element.innerHTML = nuevoHTML;
        }
    });
}

function configurarEventListenersModal() {
    console.log('Configurando event listeners del modal...');
    
    const agregarProductoBtn = document.getElementById('agregarProducto');
    const agregarServicioBtn = document.getElementById('agregarServicio');
    const descuentoSelect = document.getElementById('id_descuento');
    const ivaSelect = document.getElementById('id_iva');
    const formPresupuesto = document.getElementById('formPresupuesto');
    
    if (agregarProductoBtn) {
        agregarProductoBtn.addEventListener('click', agregarProducto);
        console.log('Event listener agregado a botón agregarProducto');
    } else {
        console.error('No se encontró el botón agregarProducto');
    }
    
    if (agregarServicioBtn) {
        agregarServicioBtn.addEventListener('click', agregarServicio);
        console.log('Event listener agregado a botón agregarServicio');
    } else {
        console.error('No se encontró el botón agregarServicio');
    }

    if (descuentoSelect) {
        descuentoSelect.addEventListener('change', calcularTotales);
    }

    if (ivaSelect) {
        ivaSelect.addEventListener('change', function() {
            if (this.value) {
                const textoIva = this.selectedOptions[0].textContent;
                const match = textoIva.match(/(\d+)%/);
                ivaPorcentaje = match ? parseFloat(match[1]) : 16;
            } else {
                ivaPorcentaje = 16; 
            }
            calcularTotales();
        });

        if (ivaSelect.value) {
            const textoIva = ivaSelect.selectedOptions[0].textContent;
            const match = textoIva.match(/(\d+)%/);
            ivaPorcentaje = match ? parseFloat(match[1]) : 16;
        }
    }

    if (formPresupuesto) {
        formPresupuesto.addEventListener('submit', function(e) {
            console.log('=== VALIDANDO FORMULARIO DE PRESUPUESTO ===');
            
            if (!validarFormularioPresupuesto()) {
                e.preventDefault();
                return;
            }

            console.log('Formulario de presupuesto válido, enviando...');
        });
    }
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('Inicializando módulo de presupuestos...');

    const crearPresupuestoModal = document.getElementById('crearPresupuestoModal');
    if (crearPresupuestoModal) {
        crearPresupuestoModal.addEventListener('show.bs.modal', function() {
            console.log('Modal de presupuesto abierto - inicializando');
            inicializarModalPresupuesto();
            
            setTimeout(() => {
                configurarEventListenersModal();
            }, 100);
        });

        crearPresupuestoModal.addEventListener('shown.bs.modal', function() {
            setTimeout(calcularTotales, 100);
        });
    }
});

function inicializarModalPresupuesto() {
    const itemsContainer = document.getElementById('items-container');
    if (itemsContainer) {
        itemsContainer.innerHTML = '';
    }
    
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
    
    const fechaInput = document.getElementById('fecha');
    if (fechaInput && !fechaInput.value) {
        fechaInput.value = new Date().toISOString().split('T')[0];
    }

    console.log('Modal inicializado. El usuario debe agregar items manualmente.');
}

function validarFormularioPresupuesto() {
    const itemsContainer = document.getElementById('items-container');
    if (!itemsContainer) {
        alert('Error: No se puede validar el formulario');
        return false;
    }
    
    const itemsReales = itemsContainer.querySelectorAll('.item-row');
    
    console.log('Items REALES en container:', itemsReales.length);

    itemsReales.forEach((item, index) => {
        const select = item.querySelector('.item-select');
        const cantidad = item.querySelector('.cantidad-item');
        console.log(`Item REAL ${index}: select="${select?.value}", cantidad="${cantidad?.value}"`);
    });

    if (itemsReales.length === 0) {
        alert('Debe agregar al menos un producto o servicio al presupuesto');
        return false;
    }
    
    let todosValidos = true;
    let mensajeError = '';
    
    itemsReales.forEach((item, index) => {
        const select = item.querySelector('.item-select');
        const cantidad = item.querySelector('.cantidad-item');
        
        if (!select || !cantidad) {
            todosValidos = false;
            mensajeError = `Error en el item ${index + 1}`;
            return;
        }
     
        select.classList.remove('is-invalid');
        cantidad.classList.remove('is-invalid');

        if (!select.value) {
            todosValidos = false;
            select.classList.add('is-invalid');
            mensajeError = `El item ${index + 1} no tiene un producto/servicio seleccionado`;
            console.log(`Item ${index} inválido: select vacío`);
        }
        
        if (!cantidad.value || parseInt(cantidad.value) <= 0) {
            todosValidos = false;
            cantidad.classList.add('is-invalid');
            mensajeError = `El item ${index + 1} no tiene una cantidad válida`;
            console.log(`Item ${index} inválido: cantidad inválida`);
        }
        
        if (select.value && cantidad.value) {
            const tipo = select.selectedOptions[0]?.getAttribute('data-tipo');
            if (tipo === '1') {
                const stock = parseInt(select.selectedOptions[0]?.getAttribute('data-stock')) || 0;
                const cant = parseInt(cantidad.value) || 0;
                if (cant > stock) {
                    todosValidos = false;
                    cantidad.classList.add('is-invalid');
                    mensajeError = `El item ${index + 1} excede el stock disponible (${stock} unidades)`;
                    console.log(`Item ${index} inválido: stock insuficiente`);
                }
            }
        }
        
        if (select.value && cantidad.value) {
            console.log(`Item ${index} válido`);
        }
    });
    
    if (!todosValidos) {
        alert(mensajeError);
        return false;
    }

    const ivaSelect = document.getElementById('id_iva');
    if (ivaSelect && !ivaSelect.value) {
        alert('Debe seleccionar un porcentaje de IVA');
        ivaSelect.focus();
        return false;
    }

    const rifSelect = document.getElementById('rif');
    if (rifSelect && !rifSelect.value) {
        alert('Debe seleccionar un cliente');
        rifSelect.focus();
        return false;
    }

    console.log('Validación de presupuesto exitosa');
    console.log('Total de items REALES a enviar:', itemsReales.length);
    console.log('IVA utilizado:', ivaPorcentaje + '%');
    
    return true;
}

function obtenerCliente(rif) {
    if (!rif) return;
    
    fetch(`index.php?c=PresupuestoControlador&m=obtenerClienteAjax&rif=${rif}`)
        .then(response => response.json())
        .then(data => {
            console.log('Datos del cliente:', data);
        })
        .catch(error => {
            console.error('Error al obtener datos del cliente:', error);
        });
}

function obtenerPrecioProducto(id_inv, cantidad) {
    const formData = new FormData();
    formData.append('id_inv', id_inv);
    formData.append('cantidad', cantidad);
    
    fetch('index.php?c=PresupuestoControlador&m=obtenerPrecioProductoAjax', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        return data.precio;
    })
    .catch(error => {
        console.error('Error al obtener precio:', error);
        return 0;
    });
}