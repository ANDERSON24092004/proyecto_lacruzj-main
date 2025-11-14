document.addEventListener('DOMContentLoaded', function() {
    let servicioCount = 0;
    let productoCount = 0;
    const ivaPorcentaje = 16;

    // Función principal para cargar el presupuesto en el modal de edición
    window.cargarPresupuestoEditar = function(presupuestoId) {
        console.log('=== INICIANDO CARGA DE PRESUPUESTO PARA EDICIÓN ===');
        console.log('ID:', presupuestoId);
        
        if (!presupuestoId) {
            alert('ID de presupuesto no válido');
            return;
        }
        
        // Mostrar loading o indicador de carga
        const modalElement = document.getElementById('editarPresupuestoModal');
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
        
        // Mostrar mensaje de carga
        const serviciosContainer = document.getElementById('editar_servicios-container');
        const productosContainer = document.getElementById('editar_productos-container');
        serviciosContainer.innerHTML = '<div class="text-center p-3"><i class="fas fa-spinner fa-spin"></i> Cargando servicios...</div>';
        productosContainer.innerHTML = '<div class="text-center p-3"><i class="fas fa-spinner fa-spin"></i> Cargando productos...</div>';
        
        fetch(`index.php?c=PresupuestoControlador&m=obtener&id=${presupuestoId}`)
            .then(response => {
                console.log('Estado de respuesta:', response.status);
                
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor: ' + response.status);
                }
                
                return response.json();
            })
            .then(data => {
                console.log('Datos completos recibidos:', data);
                console.log('Presupuesto:', data.presupuesto);
                console.log('Productos:', data.detalles.productos);
                console.log('Servicios:', data.detalles.servicios);
                
                if (data && data.success) {
                    const presupuesto = data.presupuesto;
                    const detalles = data.detalles;

                    // Llenar los campos del formulario
                    document.getElementById('editar_id_factura').textContent = presupuesto.id_factura;
                    document.getElementById('editar_id_factura_input').value = presupuesto.id_factura;
                    document.getElementById('editar_cliente').value = presupuesto.rif;
                    document.getElementById('editar_fecha').value = presupuesto.fecha;
                    document.getElementById('editar_orden_compra').value = presupuesto.numero_orden || '';
                    
                    // Limpiar contenedores
                    serviciosContainer.innerHTML = '';
                    productosContainer.innerHTML = '';
                    
                    servicioCount = 0;
                    productoCount = 0;
                    
                    // Cargar servicios
                    const serviciosDetalles = detalles.servicios || [];
                    console.log('Servicios a cargar:', serviciosDetalles);
                    
                    if (serviciosDetalles.length > 0) {
                        serviciosDetalles.forEach(detalle => {
                            agregarServicioEditar(detalle);
                        });
                    } else {
                        agregarServicioEditar();
                    }
                    
                    // Cargar productos
                    const productosDetalles = detalles.productos || [];
                    console.log('Productos a cargar:', productosDetalles);
                    
                    if (productosDetalles.length > 0) {
                        productosDetalles.forEach(detalle => {
                            agregarProductoEditar(detalle);
                        });
                    } else {
                        agregarProductoEditar();
                    }
                    
                    // Calcular totales iniciales
                    calcularTotalesEditar();
                    
                } else {
                    const mensaje = data?.message || 'Error desconocido al cargar el presupuesto';
                    mostrarError('editar_btnerror', mensaje);
                    console.error('Error del servidor:', data);
                }
            })
            .catch(error => {
                console.error('Error completo:', error);
                console.error('Stack:', error.stack);
                mostrarError('editar_btnerror', 'Error al cargar el presupuesto: ' + error.message);
                serviciosContainer.innerHTML = '';
                productosContainer.innerHTML = '';
            });
    };

    // Función para mostrar errores
    function mostrarError(elementId, mensaje) {
        const errorElement = document.getElementById(elementId);
        if (errorElement) {
            errorElement.innerHTML = `<div class="alert alert-danger">${mensaje}</div>`;
        } else {
            console.error('Elemento de error no encontrado:', elementId);
        }
    }

    function calcularTotalesEditar() {
        let subtotal = 0;

        // Calcular subtotal de servicios - SOLO PRECIO * CANTIDAD
        document.querySelectorAll('#editar_servicios-container .servicio-item-dinamico').forEach(item => {
            const select = item.querySelector('.servicio-select');
            const cantidad = item.querySelector('.cantidad-servicio');
            const subtotalInput = item.querySelector('.subtotal-servicio');

            if (select.value && cantidad.value) {
                const precio = parseFloat(select.selectedOptions[0].getAttribute('data-precio')) || 0;
                const cant = parseInt(cantidad.value) || 0;
                const subtotalItem = precio * cant; // SOLO PRECIO * CANTIDAD
                
                subtotalInput.value = subtotalItem.toFixed(2);
                subtotal += subtotalItem;
            } else {
                subtotalInput.value = '0.00';
            }
        });

        // Calcular subtotal de productos
        document.querySelectorAll('#editar_productos-container .producto-item-dinamico').forEach(item => {
            const select = item.querySelector('.producto-select');
            const cantidad = item.querySelector('.cantidad-producto');
            const subtotalInput = item.querySelector('.subtotal-producto');

            if (select.value && cantidad.value) {
                const precio = parseFloat(select.selectedOptions[0].getAttribute('data-precio')) || 0;
                const cant = parseInt(cantidad.value) || 0;
                const subtotalItem = precio * cant;
                
                subtotalInput.value = subtotalItem.toFixed(2);
                subtotal += subtotalItem;
            } else {
                subtotalInput.value = '0.00';
            }
        });

        const iva = subtotal * (ivaPorcentaje / 100);
        const totalGeneral = subtotal + iva;

        // Actualizar la interfaz
        document.getElementById('editar_subtotal-total').textContent = subtotal.toFixed(2);
        document.getElementById('editar_iva-total').textContent = iva.toFixed(2);
        document.getElementById('editar_total-general').textContent = totalGeneral.toFixed(2);

        // Actualizar inputs hidden
        document.getElementById('editar_total_iva_input').value = iva.toFixed(2);
        document.getElementById('editar_total_general_input').value = totalGeneral.toFixed(2);
    }

    // Función para agregar un servicio en edición
    function agregarServicioEditar(detalle = null) {
        const template = document.getElementById('template-servicio-editar');
        if (!template) {
            console.error('No se encontró la plantilla de servicio');
            return;
        }

        const nuevoServicio = template.cloneNode(true);
        nuevoServicio.style.display = 'block';
        nuevoServicio.classList.add('servicio-item-dinamico');
        nuevoServicio.removeAttribute('id');

        const currentIndex = servicioCount;
        const inputs = nuevoServicio.querySelectorAll('[name]');
        inputs.forEach(input => {
            const name = input.getAttribute('name').replace('[INDEX]', `[${currentIndex}]`);
            input.setAttribute('name', name);
        });

        const select = nuevoServicio.querySelector('.servicio-select');
        const cantidad = nuevoServicio.querySelector('.cantidad-servicio');
        const cantidadUnidad = nuevoServicio.querySelector('.cantidad-unidad-servicio');
        const btnQuitar = nuevoServicio.querySelector('.quitar-servicio');

        // Si se proporciona un detalle, llenar los campos
        if (detalle) {
            select.value = detalle.id || '';
            cantidad.value = detalle.cantidad_servicio || 1;
            cantidadUnidad.value = detalle.cantidad_unidad || 1;
            
            // Actualizar unidad de medida
            if (select.value) {
                const unidad = select.selectedOptions[0]?.getAttribute('data-unidad') || 'Unidad';
                nuevoServicio.querySelector('.unidad-text').textContent = unidad;
            }
        }

        // Actualizar unidad de medida cuando cambia el servicio
        select.addEventListener('change', function() {
            const unidad = this.selectedOptions[0]?.getAttribute('data-unidad') || 'Unidad';
            nuevoServicio.querySelector('.unidad-text').textContent = unidad;
            calcularTotalesEditar();
        });

        cantidad.addEventListener('input', calcularTotalesEditar);
        cantidadUnidad.addEventListener('input', calcularTotalesEditar);

        btnQuitar.addEventListener('click', function() {
            nuevoServicio.remove();
            calcularTotalesEditar();
        });

        document.getElementById('editar_servicios-container').appendChild(nuevoServicio);
        servicioCount++;
        
        // Calcular subtotal inicial si hay datos
        if (detalle) {
            calcularTotalesEditar();
        }
    }

    // Función para agregar un producto en edición
    function agregarProductoEditar(detalle = null) {
        const template = document.getElementById('template-producto-editar');
        if (!template) {
            console.error('No se encontró la plantilla de producto');
            return;
        }

        const nuevoProducto = template.cloneNode(true);
        nuevoProducto.style.display = 'block';
        nuevoProducto.classList.add('producto-item-dinamico');
        nuevoProducto.removeAttribute('id');

        const currentIndex = productoCount;
        const inputs = nuevoProducto.querySelectorAll('[name]');
        inputs.forEach(input => {
            const name = input.getAttribute('name').replace('[INDEX]', `[${currentIndex}]`);
            input.setAttribute('name', name);
        });

        const select = nuevoProducto.querySelector('.producto-select');
        const cantidad = nuevoProducto.querySelector('.cantidad-producto');
        const btnQuitar = nuevoProducto.querySelector('.quitar-producto');

        // Si se proporciona un detalle, llenar los campos
        if (detalle) {
            select.value = detalle.id || '';
            cantidad.value = detalle.cantidad_producto || 1;
        }

        select.addEventListener('change', calcularTotalesEditar);
        cantidad.addEventListener('input', calcularTotalesEditar);

        btnQuitar.addEventListener('click', function() {
            nuevoProducto.remove();
            calcularTotalesEditar();
        });

        document.getElementById('editar_productos-container').appendChild(nuevoProducto);
        productoCount++;
        
        // Calcular subtotal inicial si hay datos
        if (detalle) {
            calcularTotalesEditar();
        }
    }

    // Event listeners para botones de agregar
    document.getElementById('editar_agregarServicio').addEventListener('click', function() {
        agregarServicioEditar();
    });

    document.getElementById('editar_agregarProducto').addEventListener('click', function() {
        agregarProductoEditar();
    });

// Validación del formulario
document.getElementById('formEditarPresupuesto').addEventListener('submit', function(e) {
    e.preventDefault();
    
    console.log('=== VALIDANDO FORMULARIO DE EDICIÓN ===');
    
    const cliente = document.getElementById('editar_cliente').value;
    const fecha = document.getElementById('editar_fecha').value;

    // Validar campos obligatorios
    if (!cliente || !fecha) {
        alert('Todos los campos obligatorios (*) deben ser completados');
        return false;
    }

    // Validar que haya al menos un producto o servicio válido
    let serviciosValidos = 0;
    let productosValidos = 0;
    
    document.querySelectorAll('#editar_servicios-container .servicio-item-dinamico').forEach(item => {
        const servicioId = item.querySelector('.servicio-select').value;
        const cantidad = parseInt(item.querySelector('.cantidad-servicio').value) || 0;
        
        if (servicioId && cantidad > 0) {
            serviciosValidos++;
        }
    });
    
    document.querySelectorAll('#editar_productos-container .producto-item-dinamico').forEach(item => {
        const productoId = item.querySelector('.producto-select').value;
        const cantidad = parseInt(item.querySelector('.cantidad-producto').value) || 0;
        
        if (productoId && cantidad > 0) {
            productosValidos++;
        }
    });
    
    if (serviciosValidos === 0 && productosValidos === 0) {
        alert('Debe agregar al menos un producto o servicio válido al presupuesto');
        return false;
    }

    // Validar que el subtotal sea mayor a 0
    const subtotal = parseFloat(document.getElementById('editar_subtotal-total').textContent) || 0;
    if (subtotal <= 0) {
        alert('El subtotal debe ser mayor a 0');
        return false;
    }

    console.log('Formulario válido, enviando...');

    // Buscar el botón de envío de forma más específica
    const submitBtn = document.querySelector('#formEditarPresupuesto button[type="submit"]');
    
    if (submitBtn) {
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
        submitBtn.disabled = true;
        
        // Enviar formulario después de un pequeño delay para permitir que la UI se actualice
        setTimeout(() => {
            this.submit();
        }, 100);
        
        // Restaurar botón después de 5 segundos (por si hay error)
        setTimeout(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }, 5000);
    } else {
        // Si no encuentra el botón, enviar directamente
        console.warn('No se encontró el botón de envío, enviando formulario directamente');
        this.submit();
    }
});
    // Limpiar cuando se cierra el modal
    document.getElementById('editarPresupuestoModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('editar_btnerror').innerHTML = '';
        servicioCount = 0;
        productoCount = 0;
    });
});