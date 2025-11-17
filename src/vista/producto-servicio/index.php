<?php 
    require_once('src/vista/parcial/header.php'); 
?>
<div class="main-content">
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-md-6">
                <h2 class="mb-0">Gestión de Productos y Servicios</h2>
            </div>
            <div class="col-md-6 text-end">
                <a href="index.php?c=ProductoServicioControlador&m=crear" class="p-btn" data-bs-toggle="modal" data-bs-target="#registrarProductoModal">
                    <i class="fas fa-plus-circle"></i> Registrar Producto / Servicio
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-white">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Stock</th>
                                <th>Costo</th>
                                <th>Precio Mayor</th>
                                <th>Unidad de Medida</th>
                                <th>Presentacion</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                           <tr>
                                <td colspan="8" class="text-center">
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

<?php require_once('src/vista/producto-servicio/crear.php')?>
<?php require_once('src/vista/producto-servicio/detalle.php')?>
<?php require_once('src/vista/producto-servicio/editar.php')?>
<?php require_once('src/vista/producto-servicio/eliminar.php')?>
<?php require_once('src/vista/parcial/mensaje_modal.php'); ?>
<?php require_once('src/vista/parcial/footer.php'); ?>

<script type="text/javascript" src="assets/js/detalleProducto.js"></script>
<script type="text/javascript" src="assets/js/crearProducto.js"></script>
<script type="text/javascript" src="assets/js/editarProductoServicio.js"></script>
<script type="text/javascript" src="assets/js/mainProductoServicio.js"></script>
