<?php require_once('src/vista/parcial/header.php'); ?>

<div class="main-content">
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-md-6">
                <h2 class="mb-0">Cuentas por Cobrar</h2>
                <p class="text-muted">Facturas a crédito pendientes de pago</p>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-white">
                            <tr>
                                <th>N° Factura</th>
                                <th>Cliente</th>
                                <th>Monto Total</th>
                                <th>Fecha Límite</th>
                                <th>Estado Pago</th>
                                <th>Vigencia</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="cuerpo-tabla-cuentas">
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                                    <p>Cargando cuentas...</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once('src/vista/parcial/mensaje_modal.php'); ?>
<?php require_once('src/vista/cuentas_cobrar/anular.php'); ?>
<?php require_once('src/vista/cuentas_cobrar/reactivar.php'); ?>
<?php require_once('src/vista/cuentas_cobrar/detalle.php'); ?>
<?php require_once('src/vista/cuentas_cobrar/registrar_pago.php'); ?>

<?php require_once('src/vista/parcial/footer.php'); ?>
<script type="text/javascript" src="assets/js/verCuentas.js"></script>
<script type="text/javascript" src="assets/js/mainCuentasCobrar.js"></script>