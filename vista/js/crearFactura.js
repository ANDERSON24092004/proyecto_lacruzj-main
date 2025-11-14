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

document.addEventListener('DOMContentLoaded', function() {
    const condicionPagoSelect = document.getElementById('id_condicion_pago');
    const duracionContainer = document.getElementById('duracion_credito_container');
    
    if (condicionPagoSelect) {
        condicionPagoSelect.addEventListener('change', function() {
            if (this.value == '2') {
                duracionContainer.style.display = 'block';
                document.getElementById('duracion_credito').required = true;
            } else {
                duracionContainer.style.display = 'none';
                document.getElementById('duracion_credito').required = false;
            }
        });
    }

    const ivaSelect = document.getElementById('id_iva');
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

    const agregarProductoBtn = document.getElementById('agregarProducto');
    const agregarServicioBtn = document.getElementById('agregarServicio');
    
    if (agregarProductoBtn) {
        agregarProductoBtn.addEventListener('click', agregarProducto);
    }
    
    if (agregarServicioBtn) {
        agregarServicioBtn.addEventListener('click', agregarServicio);
    }

    const descuentoSelect = document.getElementById('id_descuento');
    if (descuentoSelect) {
        descuentoSelect.addEventListener('change', calcularTotales);
    }

    const crearFacturaModal = document.getElementById('crearFacturaModal');
    if (crearFacturaModal) {
        crearFacturaModal.addEventListener('show.bs.modal', function() {
            console.log('Modal abierto - resetando todo');
            const itemsContainer = document.getElementById('items-container');
            if (itemsContainer) {
                itemsContainer.innerHTML = '';
                itemCount = 0;
                
                ivaPorcentaje = 16;
                const ivaSelect = document.getElementById('id_iva');
                if (ivaSelect) {
                    const opcion16 = Array.from(ivaSelect.options).find(option => 
                        option.textContent.includes('16%')
                    );
                    if (opcion16) {
                        ivaSelect.value = opcion16.value;
                    }
                }
                calcularTotales(); 
            }
        });

        crearFacturaModal.addEventListener('shown.bs.modal', function() {
            setTimeout(calcularTotales, 100);
        });
    }

    document.getElementById('formFactura').addEventListener('submit', function(e) {
        console.log('=== VALIDANDO FORMULARIO ===');
        
        const itemsContainer = document.getElementById('items-container');
        const itemsReales = itemsContainer.querySelectorAll('.item-row');
        
        console.log('Items REALES en container:', itemsReales.length);

        itemsReales.forEach((item, index) => {
            const select = item.querySelector('.item-select');
            const cantidad = item.querySelector('.cantidad-item');
            console.log(`Item REAL ${index}: select="${select.value}", cantidad="${cantidad.value}"`);
        });

        if (itemsReales.length === 0) {
            e.preventDefault();
            alert('Debe agregar al menos un producto o servicio');
            return;
        }
        
        let todosValidos = true;
        let mensajeError = '';
        
        itemsReales.forEach((item, index) => {
            const select = item.querySelector('.item-select');
            const cantidad = item.querySelector('.cantidad-item');
            
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
                console.log(`Item ${index} válido`);
            }
        });
        
        if (!todosValidos) {
            e.preventDefault();
            alert(mensajeError);
            return;
        }

        const ivaSelect = document.getElementById('id_iva');
        if (ivaSelect && !ivaSelect.value) {
            e.preventDefault();
            alert('Debe seleccionar un porcentaje de IVA');
            ivaSelect.focus();
            return;
        }

        console.log('Formulario válido, enviando...');
        console.log('Total de items REALES a enviar:', itemsReales.length);
        console.log('IVA utilizado:', ivaPorcentaje + '%');
        
        const formData = new FormData(this);
        console.log('=== DATOS FINALES DEL FORMULARIO ===');
        for (let [key, value] of formData.entries()) {
            console.log(key + ': ' + value);
        }
    });

});

function updateIvaLabel() {
    const ivaElements = document.querySelectorAll('#subtotal-con-descuento').length > 0 ? 
        document.querySelectorAll('p') : [];
    
    ivaElements.forEach(element => {
        if (element.innerHTML.includes('IVA')) {
            const nuevoHTML = element.innerHTML.replace(/IVA\s*\(\d+%\):/g, `IVA (${ivaPorcentaje}%):`);
            element.innerHTML = nuevoHTML;
        }
    });
    
    const ivaLabel = document.querySelector('p strong');
    if (ivaLabel && ivaLabel.textContent.includes('IVA')) {
        ivaLabel.textContent = `IVA (${ivaPorcentaje}%):`;
    }
}

const originalCalcularTotales = window.calcularTotales;
window.calcularTotales = function() {
    originalCalcularTotales();
    updateIvaLabel();
};