<?php require_once('vista/parcial/header.php'); ?>

<div class="main-content">
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-md-6">
                <h2 class="mb-0">Gestión de Facturas</h2>
            </div>
            <div class="col-md-6 text-end">
                <button type="button" class="p-btn" id="btnCrearFactura">
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
                                <th>N° Factura</th>
                                <th>RIF</th>
                                <th>Cliente</th>
                                <th>Condición Pago</th>
                                <th>N° Orden</th>
                                <th>Total</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="cuerpo-tabla-facturas">
                            <tr>
                                <td colspan="9" class="text-center text-muted">
                                    <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                                    <p>Cargando facturas...</p>
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
<?php require_once('vista/factura/ver.php'); ?>
<?php require_once('vista/factura/crear.php'); ?>
<?php require_once('vista/factura/anular.php'); ?>
<?php require_once('vista/factura/reactivar.php'); ?>

<?php require_once('vista/parcial/footer.php'); ?>

<script type="text/javascript" src="assets/js/verFactura.js"></script>
<script type="text/javascript" src="assets/js/crearFactura.js"></script>
<script type="text/javascript" src="assets/js/mainFactura.js"></script>