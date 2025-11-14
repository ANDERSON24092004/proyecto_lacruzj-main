function cargarItemsVentas() {
    const tipo = document.getElementById('tipo_producto_ventas').value;
    const divItemEspecifico = document.getElementById('div_item_especifico_ventas');
    const selectItem = document.getElementById('id_item_ventas');
    
    console.log('cargarItemsVentas llamado, tipo:', tipo);
    
    if (tipo === 'especifico') {
        divItemEspecifico.style.display = 'block';
        selectItem.innerHTML = '<option value="">Cargando items...</option>';
        
        const url = `index.php?c=ReporteControlador&m=ajaxCargarItems&tipo=${encodeURIComponent('especifico')}&_=${Date.now()}`;
        
        console.log('Realizando petición AJAX a:', url);
        
        fetch(url)
            .then(response => {
                console.log('Respuesta recibida, status:', response.status);
                if (!response.ok) {
                    throw new Error(`Error HTTP: ${response.status} ${response.statusText}`);
                }
                return response.text();
            })
            .then(text => {
                console.log('Respuesta texto crudo:', text);
                
                if (!text || text.trim() === '') {
                    throw new Error('Respuesta vacía del servidor');
                }
                
                try {
                    const data = JSON.parse(text);
                    console.log('Datos recibidos:', data);
                
                    const items = data.data && data.data.data ? data.data.data : (data.data || []);
                    
                    if (data.success && Array.isArray(items)) {
                        if (items.length > 0) {
                            selectItem.innerHTML = '<option value="">Seleccione un item</option>';
                            items.forEach(item => {
                                if (item && item.id && item.nombre) {
                                    const tipoTexto = item.tipo == 1 ? ' (Producto)' : ' (Servicio)';
                                    selectItem.innerHTML += `<option value="${item.id}">${item.nombre}${tipoTexto}</option>`;
                                }
                            });
                            console.log('Items cargados exitosamente:', items.length);
                        } else {
                            selectItem.innerHTML = '<option value="">No hay items disponibles</option>';
                            console.log('No hay items disponibles');
                        }
                    } else {
                        selectItem.innerHTML = '<option value="">Error al cargar items</option>';
                        console.error('Error en respuesta:', data);
                        mostrarMensaje('error', 'Error al cargar items', data.message || 'No se pudieron cargar los items');
                    }
                } catch (e) {
                    console.error('Error parseando JSON:', e);
                    selectItem.innerHTML = '<option value="">Error en formato de datos</option>';
                    mostrarMensaje('error', 'Error de datos', 'El servidor respondió con datos inválidos');
                }
            })
            .catch(error => {
                console.error('Error en fetch:', error);
                selectItem.innerHTML = '<option value="">Error de conexión</option>';
                mostrarMensaje('error', 'Error de conexión', 'No se pudo conectar al servidor para cargar los items');
            });
    } else {
        divItemEspecifico.style.display = 'none';
        selectItem.value = '';
    }
}

function cargarMateriasPrimas() {
    const tipo = document.getElementById('tipo_materia').value;
    const divMateriaEspecifica = document.getElementById('div_materia_especifica');
    const selectMateria = document.getElementById('id_materia');
    
    console.log('cargarMateriasPrimas llamado, tipo:', tipo);
    
    if (tipo === 'especifico') {
        divMateriaEspecifica.style.display = 'block';
        selectMateria.innerHTML = '<option value="">Cargando materias primas...</option>';
        
        const url = `index.php?c=ReporteControlador&m=ajaxCargarMateriasPrimas&_=${Date.now()}`;
        
        console.log('Realizando petición AJAX a:', url);
        
        fetch(url)
            .then(response => {
                console.log('Respuesta recibida, status:', response.status);
                if (!response.ok) {
                    throw new Error(`Error HTTP: ${response.status} ${response.statusText}`);
                }
                return response.text();
            })
            .then(text => {
                console.log('Respuesta texto crudo:', text);
                
                if (!text || text.trim() === '') {
                    throw new Error('Respuesta vacía del servidor');
                }
                
                try {
                    const data = JSON.parse(text);
                    console.log('Datos recibidos:', data);
                    
                    const materias = data.data && data.data.data ? data.data.data : (data.data || []);
                    
                    if (data.success && Array.isArray(materias)) {
                        if (materias.length > 0) {
                            selectMateria.innerHTML = '<option value="">Seleccione una materia prima</option>';
                            materias.forEach(materia => {
                                if (materia && materia.id && materia.nombre) {
                                    selectMateria.innerHTML += `<option value="${materia.id}">${materia.nombre}</option>`;
                                }
                            });
                            console.log('Materias primas cargadas exitosamente:', materias.length);
                        } else {
                            selectMateria.innerHTML = '<option value="">No hay materias primas disponibles</option>';
                            console.log('No hay materias primas disponibles');
                        }
                    } else {
                        selectMateria.innerHTML = '<option value="">Error al cargar materias primas</option>';
                        console.error('Error en respuesta:', data);
                        mostrarMensaje('error', 'Error al cargar materias primas', data.message || 'No se pudieron cargar las materias primas');
                    }
                } catch (e) {
                    console.error('Error parseando JSON:', e);
                    selectMateria.innerHTML = '<option value="">Error en formato de datos</option>';
                    mostrarMensaje('error', 'Error de datos', 'El servidor respondió con datos inválidos');
                }
            })
            .catch(error => {
                console.error('Error en fetch:', error);
                selectMateria.innerHTML = '<option value="">Error de conexión</option>';
                mostrarMensaje('error', 'Error de conexión', 'No se pudo conectar al servidor para cargar las materias primas');
            });
    } else {
        divMateriaEspecifica.style.display = 'none';
        selectMateria.value = '';
    }
}

function mostrarCamposPeriodo(tipo) {
    const esVentas = tipo === 'ventas';
    const prefix = esVentas ? 'ventas' : 'compras';
    
    const periodoSelect = document.getElementById(`periodo_${prefix}`);
    const divPersonalizado = document.getElementById(`div_periodo_personalizado_${prefix}`);
    const divEspecifico = document.getElementById(`div_periodo_especifico_${prefix}`);
    const divMesEspecifico = document.getElementById(`div_mes_especifico_${prefix}`);
    const divAnioEspecifico = document.getElementById(`div_anio_especifico_${prefix}`);
    
    const periodo = periodoSelect.value;

    divPersonalizado.style.display = 'none';
    divEspecifico.style.display = 'none';
    
    switch (periodo) {
        case 'personalizado':
            divPersonalizado.style.display = 'block';
            const fechaHasta = new Date();
            const fechaDesde = new Date();
            fechaDesde.setDate(fechaHasta.getDate() - 30);
            
            document.getElementById(`fecha_desde_${prefix}`).value = fechaDesde.toISOString().split('T')[0];
            document.getElementById(`fecha_hasta_${prefix}`).value = fechaHasta.toISOString().split('T')[0];
            break;
            
        case 'mes':
        case 'anio':
            divEspecifico.style.display = 'block';
            
            if (periodo === 'mes') {
                divMesEspecifico.style.display = 'block';
                divAnioEspecifico.style.display = 'block';
                const ahora = new Date();
                document.getElementById(`mes_${prefix}`).value = ahora.getMonth() + 1; // Mes actual (1-12)
                document.getElementById(`anio_${prefix}`).value = ahora.getFullYear(); // Año actual
            } else {
                divMesEspecifico.style.display = 'none';
                divAnioEspecifico.style.display = 'block';
                document.getElementById(`anio_${prefix}`).value = new Date().getFullYear();
            }
            break;
            
        default:
            break;
    }
}

function inicializarPeriodos() {
    mostrarCamposPeriodo('ventas');
    mostrarCamposPeriodo('compras');
}

document.addEventListener('DOMContentLoaded', function() {
    inicializarPeriodos();
});