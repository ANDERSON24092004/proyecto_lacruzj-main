<?php require_once('src/vista/parcial/header.php'); ?>

<div class="main-content">
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-md-6">
                <h2 class="mb-0">Gestión de Clientes</h2>
            </div>
            <div class="col-md-6 text-end" >
                <button type="button" class="p-btn" data-bs-toggle="modal" data-bs-target="#crearClienteModal">
                    <i class="fas fa-plus-circle"></i> Registrar Cliente
                </button>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div id="loading-clientes" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando clientes...</span>
                    </div>
                    <p class="mt-2">Cargando clientes...</p>
                </div>
                <div id="tabla-clientes-container" class="table-responsive" style="display: none;">
                    <table class="table table-striped table-hover">
                        <thead class="table-white">
                            <tr>
                                <th>RIF</th>
                                <th>Razon Social</th>
                                <th>Correo</th>
                                <th>Teléfono</th>
                                <th>Direccion</th>
                                <th>Fecha Registro</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-clientes">
                        </tbody>
                    </table>
                </div>
                <div id="sin-clientes" class="text-center py-4" style="display: none;">
                    <p class="text-muted">No hay clientes registrados</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once('src/vista/parcial/mensaje_modal.php'); ?>
<?php require_once('src/vista/clientes/crear.php');?>
<?php require_once('src/vista/clientes/eliminar.php'); ?>
<?php require_once('src/vista/clientes/editar.php');?>
<?php require_once('src/vista/parcial/footer.php'); ?>

<script type="text/javascript" src="assets/js/mainCliente.js"></script>