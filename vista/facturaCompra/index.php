<?php require_once('vista/parcial/header.php'); ?>

<div class="main-content">
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-md-6">
                <h2 class="mb-0">Facturas de Compra</h2>
            </div>
            <div class="col-md-6 text-end">
                <button type="button" class="p-btn" data-bs-toggle="modal" data-bs-target="#registrarFacturaModal">
                    <i class="fas fa-plus-circle"></i> Registrar Factura
                </button>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-white">
                            <tr>
                                <th>ID Factura</th>
                                <th>Numero de Factura</th>
                                <th>Proveedor</th>
                                <th>Fecha</th>
                                <th>Total General</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                           <tr>
                                <td colspan="7" class="text-center">
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

<?php require_once('vista/parcial/mensaje_modal.php'); ?>
<?php require_once('vista/facturaCompra/crear.php'); ?>
<?php require_once('vista/facturaCompra/anular.php'); ?>
<?php require_once('vista/facturaCompra/reactivar.php'); ?>
<?php require_once('vista/parcial/footer.php'); ?>

<script type="text/javascript" src="assets/js/mainFacturaCompra.js"></script>
