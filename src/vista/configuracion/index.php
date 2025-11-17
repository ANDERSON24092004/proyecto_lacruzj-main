<?php require_once('src/vista/parcial/header.php'); ?>

<div class="main-content">
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-md-8">
                <h2 class="mb-0"><i class="fas fa-cogs me-2"></i>Configuración del Sistema</h2>
                <p class="text-muted"><i class="fas fa-info-circle me-1"></i>Administra las opciones de configuración de tu sistema</p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-5">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-sliders-h me-2"></i>Opciones de Configuración</h5>
                    </div>
                    <div class="list-group list-group-flush">

                        <a href="#condicion-pago" class="list-group-item list-group-item-action <?php echo ($activeTab === 'condicion-pago') ? 'active' : ''; ?>" data-bs-toggle="tab" data-tab="avanzado">
                            <i class="fas fa-credit-card me-2"></i> Condición de Pago
                        </a>

                        <a href="#descuento" class="list-group-item list-group-item-action <?php echo ($activeTab === 'descuento') ? 'active' : ''; ?>" data-bs-toggle="tab" data-tab="avanzado">
                            <i class="fas fa-percentage me-2"></i> Descuento
                        </a>

                        <a href="#iva" class="list-group-item list-group-item-action <?php echo ($activeTab === 'iva') ? 'active' : ''; ?>" data-bs-toggle="tab" data-tab="avanzado">
                            <i class="fas fa-receipt me-2"></i> IVA
                        </a>

                        <a href="#unidades" class="list-group-item list-group-item-action <?php echo ($activeTab === 'unidades') ? 'active' : ''; ?>" data-bs-toggle="tab" data-tab="unidades">
                            <i class="fas fa-ruler-combined me-2"></i> Unidades de Medida
                        </a>

                        <a href="#presentacion" class="list-group-item list-group-item-action <?php echo ($activeTab === 'presentacion') ? 'active' : ''; ?>" data-bs-toggle="tab" data-tab="avanzado">
                            <i class="fas fa-box me-2"></i> Presentación
                        </a>                                       
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="tab-content">
                     <!-- Sección Unidades de Medida Materia -->
                    <div class="tab-pane fade <?php echo ($activeTab === 'unidades') ? 'show active' : ''; ?>" id="unidades">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="fas fa-ruler-combined me-2"></i> Unidades de Medida</h5>
                                <div class="col-md-6 text-end">
                                    <a href="index.php?c=ConfigControlador&m=crear&tipo=medida&tab=unidades" class="p-btn" data-bs-toggle="modal" data-bs-target="#modalUnidad">
                                        <i class="fas fa-plus-circle me-1"></i> Registrar Unidad
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>ID</th>
                                                <th>Nombre</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbody-unidades">
                                            <tr id="loading-unidades">
                                                <td colspan="3" class="text-center">
                                                    <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Cargando...</span>
                                                    </div>
                                                    <p class="mt-2">Cargando Datos...</p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Descuento -->
                    <div class="tab-pane fade <?php echo ($activeTab === 'descuento') ? 'show active' : ''; ?>" id="descuento">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="fas fa-percentage me-2"></i> Descuento</h5>
                                <div class="col-md-6 text-end">
                                    <a href="index.php?c=ConfigControlador&m=crear&tipo=descuento&tab=descuento" class="p-btn" data-bs-toggle="modal" data-bs-target="#modalDescuento">
                                        <i class="fas fa-plus-circle me-1"></i> Registrar Descuento
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>ID</th>
                                                <th>Descuento</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbody-descuento">
                                            <tr id="loading-descuento">
                                                <td colspan="3" class="text-center">
                                                    <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Cargando...</span>
                                                    </div>
                                                    <p class="mt-2">Cargando Datos...</p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Presentacion -->
                    <div class="tab-pane fade <?php echo ($activeTab === 'presentacion') ? 'show active' : ''; ?>" id="presentacion">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="fas fa-box me-2"></i> Presentación</h5>
                                <div class="col-md-6 text-end">
                                    <a href="index.php?c=ConfigControlador&m=crear&tipo=presentacion&tab=presentacion" class="p-btn" data-bs-toggle="modal" data-bs-target="#modalPresentacion">
                                        <i class="fas fa-plus-circle me-1"></i> Registrar Presentación
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>ID</th>
                                                <th>Nombre</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbody-presentacion">
                                            <tr id="loading-presentacion">
                                                <td colspan="3" class="text-center">
                                                    <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Cargando...</span>
                                                    </div>
                                                    <p class="mt-2">Cargando Datos...</p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- IVA -->
                    <div class="tab-pane fade <?php echo ($activeTab === 'iva') ? 'show active' : ''; ?>" id="iva">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="fas fa-receipt me-2"></i> IVA</h5>
                                <div class="col-md-6 text-end">
                                    <a href="index.php?c=ConfigControlador&m=crear&tipo=iva&tab=iva" class="p-btn" data-bs-toggle="modal" data-bs-target="#modalIva">
                                        <i class="fas fa-plus-circle me-1"></i> Registrar IVA
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>ID</th>
                                                <th>IVA</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbody-iva">
                                            <tr id="loading-iva">
                                                <td colspan="3" class="text-center">
                                                    <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Cargando...</span>
                                                    </div>
                                                    <p class="mt-2">Cargando Datos...</p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
    
                    <!-- Configuracion de Condicion de Pago -->
                    <div class="tab-pane fade <?php echo ($activeTab === 'condicion-pago') ? 'show active' : ''; ?>" id="condicion-pago">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="fas fa-credit-card me-2"></i> Condición de Pago</h5>
                                <div class="col-md-6 text-end">
                                    <a href="index.php?c=ConfigControlador&m=crear&tipo=condicion-pago&tab=condicion-pago" class="p-btn" data-bs-toggle="modal" data-bs-target="#modalCondicionPago">
                                        <i class="fas fa-plus-circle me-1"></i> Registrar Condición de Pago
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>ID</th>
                                                <th>Condición de Pago</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbody-condiciones">
                                            <tr id="loading-condiciones">
                                                <td colspan="3" class="text-center">
                                                    <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Cargando...</span>
                                                    </div>
                                                    <p class="mt-2">Cargando Datos...</p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>            
            </div>
        </div>
    </div>
</div>

<?php require_once('src/vista/configuracion/unidades-de-medida/crear.php')?>
<?php require_once('src/vista/configuracion/condicion-pago/crear.php')?>
<?php require_once('src/vista/configuracion/iva/crear.php')?>
<?php require_once('src/vista/configuracion/descuento/crear.php')?>
<?php require_once('src/vista/configuracion/presentacion/crear.php')?>

<?php require_once('src/vista/configuracion/unidades-de-medida/editar.php')?>
<?php require_once('src/vista/configuracion/condicion-pago/editar.php')?>
<?php require_once('src/vista/configuracion/iva/editar.php')?>
<?php require_once('src/vista/configuracion/descuento/editar.php')?>
<?php require_once('src/vista/configuracion/presentacion/editar.php')?>

<?php require_once('src/vista/configuracion/unidades-de-medida/eliminar.php')?>
<?php require_once('src/vista/configuracion/condicion-pago/eliminar.php')?>
<?php require_once('src/vista/configuracion/iva/eliminar.php')?>
<?php require_once('src/vista/configuracion/descuento/eliminar.php')?>
<?php require_once('src/vista/configuracion/presentacion/eliminar.php')?>

<?php require_once('src/vista/parcial/mensaje_modal.php'); ?>
<?php require_once('src/vista/parcial/footer.php'); ?>

<script type="text/javascript" src="assets/js/mainCondicion.js"></script>
<script type="text/javascript" src="assets/js/mainDescuento.js"></script>
<script type="text/javascript" src="assets/js/mainPresentacion.js"></script>
<script type="text/javascript" src="assets/js/mainIva.js"></script>
<script type="text/javascript" src="assets/js/mainUnidades.js"></script>
