<?php require_once('vista/parcial/header.php'); ?>

<div class="main-content">
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-md-6">
                <h2 class="mb-0">Gestión de Materia Prima</h2>
            </div>
            <div class="col-md-6 text-end">
                <button type="button" class="p-btn" data-bs-toggle="modal" data-bs-target="#registrarMateriaModal">
                    <i class="fas fa-plus-circle"></i> Registrar Materia Prima
                </button>
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
                                <th>Unidad de Medida</th>
                                <th>Stock</th>
                                <th>Costo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                           <tr>
                                <td colspan="6" class="text-center">
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
<?php include_once('vista/materiaPrima/crear.php'); ?>
<?php include_once('vista/materiaPrima/editar.php'); ?>
<?php include_once('vista/materiaPrima/eliminar.php'); ?>
<?php require_once('vista/parcial/footer.php'); ?>

<script type="text/javascript" src="assets/js/mainMateria.js"></script>