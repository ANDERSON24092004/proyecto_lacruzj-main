<?php require_once('vista/parcial/header.php'); ?>

<div class="main-content">
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-md-6">
                <h2 class="mb-0">Gestión de Presupuestos</h2>
            </div>
            <div class="col-md-6 text-end">
                <button type="button" class="p-btn" id="btnCrearPresupuesto">
                    <i class="fas fa-plus-circle"></i> Registrar Presupuesto
                </button>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-white">
                            <tr>
                                <th>N° Presupuesto</th>
                                <th>Cliente</th>
                                <th>RIF</th>
                                <th>N° Orden</th>
                                <th>Total</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="cuerpo-tabla-presupuestos">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once('vista/parcial/mensaje_modal.php'); ?>
<?php require_once('vista/presupuesto/ver.php'); ?>
<?php require_once('vista/presupuesto/anular.php'); ?>
<?php require_once('vista/presupuesto/reactivar.php'); ?>
<?php require_once('vista/presupuesto/crear.php'); ?>

<?php require_once('vista/parcial/footer.php'); ?>

<script type="text/javascript" src="assets/js/verPresupuesto.js"></script>
<script type="text/javascript" src="assets/js/crearPresupuesto.js"></script>
<script type="text/javascript" src="assets/js/mainPresupuesto.js"></script>