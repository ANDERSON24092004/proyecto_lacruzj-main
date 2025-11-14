<?php require_once 'vista/parcial/header.php' ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Reportes del Sistema</h1>
                <div class="text-muted">
                    <i class="fas fa-calendar-alt me-2"></i>
                    <?php echo date('d/m/Y'); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-filter me-2"></i>
                        Reportes de Ventas
                    </h5>
                </div>
                <div class="card-body">
                    <form id="formReporteVentas" action="index.php?c=ReporteControlador&m=ventasParametrizadas" method="POST" target="_blank">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="tipo_producto_ventas" class="form-label">Tipo de Item</label>
                                <select class="form-select" id="tipo_producto_ventas" name="tipo_producto" onchange="cargarItemsVentas()">
                                    <option value="todos">Todos los Items</option>
                                    <option value="productos">Solo Productos</option>
                                    <option value="servicios">Solo Servicios</option>
                                    <option value="especifico">Item Específico</option>
                                </select>
                            </div>
                            
                            <div class="col-md-4 mb-3" id="div_item_especifico_ventas" style="display: none;">
                                <label for="id_item_ventas" class="form-label">Seleccionar Item</label>
                                <select class="form-select" id="id_item_ventas" name="id_item">
                                    <option value="">Seleccione un tipo primero</option>
                                </select>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="periodo_ventas" class="form-label">Período</label>
                                <select class="form-select" id="periodo_ventas" name="periodo" onchange="mostrarCamposPeriodo('ventas')">
                                    <option value="dia">Día Actual</option>
                                    <option value="semana">Semana Actual</option>
                                    <option value="mes">Mes Actual</option>
                                    <option value="anio">Año Actual</option>
                                    <option value="personalizado">Personalizado</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row" id="div_periodo_personalizado_ventas" style="display: none;">
                            <div class="col-md-6 mb-3">
                                <label for="fecha_desde_ventas" class="form-label">Fecha Desde</label>
                                <input type="date" class="form-control" id="fecha_desde_ventas" name="fecha_desde">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="fecha_hasta_ventas" class="form-label">Fecha Hasta</label>
                                <input type="date" class="form-control" id="fecha_hasta_ventas" name="fecha_hasta">
                            </div>
                        </div>
                        
                        <div class="row" id="div_periodo_especifico_ventas" style="display: none;">
                            <div class="col-md-6 mb-3" id="div_mes_especifico_ventas">
                                <label for="mes_ventas" class="form-label">Mes</label>
                                <select class="form-select" id="mes_ventas" name="mes">
                                    <option value="1">Enero</option>
                                    <option value="2">Febrero</option>
                                    <option value="3">Marzo</option>
                                    <option value="4">Abril</option>
                                    <option value="5">Mayo</option>
                                    <option value="6">Junio</option>
                                    <option value="7">Julio</option>
                                    <option value="8">Agosto</option>
                                    <option value="9">Septiembre</option>
                                    <option value="10">Octubre</option>
                                    <option value="11">Noviembre</option>
                                    <option value="12">Diciembre</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3" id="div_anio_especifico_ventas">
                                <label for="anio_ventas" class="form-label">Año</label>
                                <select class="form-select" id="anio_ventas" name="anio">
                                    <?php
                                    $anio_actual = date('Y');
                                    for ($i = $anio_actual; $i >= $anio_actual - 5; $i--) {
                                        echo "<option value='$i'" . ($i == $anio_actual ? ' selected' : '') . ">$i</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-file-pdf me-2"></i>
                                    Generar Reporte de Ventas
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-filter me-2"></i>
                        Reportes de Compra
                    </h5>
                </div>
                <div class="card-body">
                    <form id="formReporteCompras" action="index.php?c=ReporteControlador&m=comprasParametrizadas" method="POST" target="_blank">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="tipo_materia" class="form-label">Tipo de Materia Prima</label>
                                <select class="form-select" id="tipo_materia" name="tipo_materia" onchange="cargarMateriasPrimas()">
                                    <option value="todos">Todas las Materias Primas</option>
                                    <option value="especifico">Selección Específica</option>
                                </select>
                            </div>
                            
                            <div class="col-md-4 mb-3" id="div_materia_especifica" style="display: none;">
                                <label for="id_materia" class="form-label">Seleccionar Materia Prima</label>
                                <select class="form-select" id="id_materia" name="id_materia">
                                    <option value="">Cargando materias primas...</option>
                                </select>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="periodo_compras" class="form-label">Período</label>
                                <select class="form-select" id="periodo_compras" name="periodo" onchange="mostrarCamposPeriodo('compras')">
                                    <option value="dia">Día Actual</option>
                                    <option value="semana">Semana Actual</option>
                                    <option value="mes">Mes Actual</option>
                                    <option value="anio">Año Actual</option>
                                    <option value="personalizado">Personalizado</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row" id="div_periodo_personalizado_compras" style="display: none;">
                            <div class="col-md-6 mb-3">
                                <label for="fecha_desde_compras" class="form-label">Fecha Desde</label>
                                <input type="date" class="form-control" id="fecha_desde_compras" name="fecha_desde">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="fecha_hasta_compras" class="form-label">Fecha Hasta</label>
                                <input type="date" class="form-control" id="fecha_hasta_compras" name="fecha_hasta">
                            </div>
                        </div>
                        
                        <div class="row" id="div_periodo_especifico_compras" style="display: none;">
                            <div class="col-md-6 mb-3" id="div_mes_especifico_compras">
                                <label for="mes_compras" class="form-label">Mes</label>
                                <select class="form-select" id="mes_compras" name="mes">
                                    <option value="1">Enero</option>
                                    <option value="2">Febrero</option>
                                    <option value="3">Marzo</option>
                                    <option value="4">Abril</option>
                                    <option value="5">Mayo</option>
                                    <option value="6">Junio</option>
                                    <option value="7">Julio</option>
                                    <option value="8">Agosto</option>
                                    <option value="9">Septiembre</option>
                                    <option value="10">Octubre</option>
                                    <option value="11">Noviembre</option>
                                    <option value="12">Diciembre</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3" id="div_anio_especifico_compras">
                                <label for="anio_compras" class="form-label">Año</label>
                                <select class="form-select" id="anio_compras" name="anio">
                                    <?php
                                    $anio_actual = date('Y');
                                    for ($i = $anio_actual; $i >= $anio_actual - 5; $i--) {
                                        echo "<option value='$i'" . ($i == $anio_actual ? ' selected' : '') . ">$i</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-file-pdf me-2"></i>
                                    Generar Reporte de Compras
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-concierge-bell me-2"></i>
                        Reporte de Servicios
                    </h5>
                </div>
                <div class="card-body">
                    <p class="card-text">
                        Genera un listado completo de todos los servicios disponibles en el sistema 
                        con sus respectivos costos y unidades de medida.
                    </p>
                    <div class="mb-3">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Incluye: nombre del servicio, unidad de medida y costo
                        </small>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <form action="index.php?c=ReporteControlador&m=listarServicios" method="POST" target="_blank">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-file-pdf me-2"></i>
                            Generar PDF
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-boxes me-2"></i>
                        Inventario de Productos
                    </h5>
                </div>
                <div class="card-body">
                    <p class="card-text">
                        Reporte detallado del inventario de productos, incluyendo stock actual, 
                        costos unitarios y valor total del inventario.
                    </p>
                    <div class="mb-3">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Incluye: stock, costos, unidades y valor total
                        </small>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <form action="index.php?c=ReporteControlador&m=inventarioProductos" method="POST" target="_blank">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-file-pdf me-2"></i>
                            Generar PDF
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-flask me-2"></i>
                        Inventario de Materias Primas
                    </h5>
                </div>
                <div class="card-body">
                    <p class="card-text">
                        Reporte completo del inventario de materias primas con información 
                        de stock, costos y valorización total del inventario.
                    </p>
                    <div class="mb-3">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Incluye: stock, costos, unidades y valor total
                        </small>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <form action="index.php?c=ReporteControlador&m=inventarioMateriasPrimas" method="POST" target="_blank">
                        <button type="submit" class="btn btn-warning text-dark w-100">
                            <i class="fas fa-file-pdf me-2"></i>
                            Generar PDF
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        Información sobre los Reportes
                    </h5>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary">Características de los Reportes:</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success me-2"></i> Formato PDF profesional</li>
                                <li><i class="fas fa-check text-success me-2"></i> Logo de la empresa incluido</li>
                                <li><i class="fas fa-check text-success me-2"></i> Información de contacto</li>
                                <li><i class="fas fa-check text-success me-2"></i> Fecha de generación automática</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary">Recomendaciones:</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-sync-alt text-info me-2"></i> Genera reportes regularmente</li>
                                <li><i class="fas fa-print text-info me-2"></i> Imprime para archivo físico</li>
                                <li><i class="fas fa-save text-info me-2"></i> Guarda copias digitales</li>
                                <li><i class="fas fa-chart-line text-info me-2"></i> Compara con reportes anteriores</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'vista/parcial/mensaje_modal.php' ?>
<?php require_once 'vista/parcial/footer.php' ?>

<script type="text/javascript" src="assets/js/reportes.js"></script>