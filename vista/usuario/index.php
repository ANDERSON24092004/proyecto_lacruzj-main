<?php require_once('vista/parcial/header.php'); ?>

<div class="main-content">
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-md-6">
                <h2 class="mb-0">Gestión de Usuario</h2>
            </div>
            
            <div class="col-md-6 text-end">
                <button type="button" class="p-btn" data-bs-toggle="modal" data-bs-target="#registrarUsuarioModal">
                    <i class="fas fa-plus-circle"></i> Registrar Usuario
                </button>
            </div>
        </div>
               
        <div class="card">
            <div class="card-body">
                <div class="table-responsive" id="tabla-usuarios-container">
                    <table class="table table-striped table-hover" id="tabla-usuarios">
                        <thead class="table-white">
                            <tr>
                                <th>Cedula</th>
                                <th>Nombre</th>
                                <th>Correo</th>
                                <th>Teléfono</th>
                                <th>Rol</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="cuerpo-tabla-usuarios">
                            <tr>
                                <td colspan="6" class="text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Cargando...</span>
                                    </div>
                                    <p class="mt-2">Cargando usuarios...</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once('vista/parcial/mensaje_modal.php'); ?>
<?php include_once('vista/usuario/crear.php'); ?>
<?php include_once('vista/usuario/editar.php'); ?>
<?php include_once('vista/usuario/eliminar.php'); ?>
<?php require_once('vista/parcial/footer.php'); ?>

<script type="text/javascript" src="assets/js/mainUsuario.js"></script>