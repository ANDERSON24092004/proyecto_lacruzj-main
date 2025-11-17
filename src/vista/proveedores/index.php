<?php require_once('src/vista/parcial/header.php'); ?>

<div class="main-content">
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-md-6">
                <h2 class="mb-0">Gestión de Proveedores</h2>
            </div>
            <div class="col-md-6 text-end">
                <button type="button" class="p-btn" data-bs-toggle="modal" data-bs-target="#registrarProveedorModal">
                    <i class="fas fa-plus-circle"></i> Registrar Proveedor
                </button>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-white">
                            <tr>
                                <th>RIF</th>
                                <th>Nombre</th>
                                <th>Teléfono</th>
                                <th>Correo</th>
                                <th>Dirección</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-proveedores">
                            <tr>
                                <td colspan="6" class="text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Cargando...</span>
                                    </div>
                                    <p class="mt-2">Cargando proveedores...</p>
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
<?php require_once('src/vista/proveedores/crear.php'); ?>
<?php require_once('src/vista/proveedores/editar.php'); ?>
<?php require_once('src/vista/proveedores/eliminar.php'); ?>
<?php require_once('src/vista/parcial/footer.php'); ?>

<script type="text/javascript" src="assets/js/mainProveedores.js"></script>