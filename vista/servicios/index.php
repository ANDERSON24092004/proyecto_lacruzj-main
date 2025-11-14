<?php require_once('vista/parcial/header.php'); ?>

<div class="main-content">
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-md-6">
                <h2 class="mb-0">Gestión de Servicios</h2>
            </div>
            <div class="col-md-6 text-end">
                <button type="button" href="index.php?c=ServicioControlador&m=crear" class="p-btn" data-bs-toggle="modal" data-bs-target="#registrarServicioModal">
                    <i class="fas fa-plus-circle"></i> Registrar Servicio
                </button>
            </div>
        </div>

        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="alert alert-<?= $_SESSION['tipo_mensaje'] ?> alert-dismissible fade show" role="alert">
                <?= $_SESSION['mensaje'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['mensaje']); unset($_SESSION['tipo_mensaje']); ?>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-white">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Unidad de Medida</th>
                                <th>Precio Base</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($servicios)): ?>
                                <tr>
                                    <td colspan="6" class="text-center">No hay servicios registrados</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($servicios as $s): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($s['id_servicio']) ?></td>
                                        <td><?= htmlspecialchars($s['nombre']) ?></td>
                                        <td><?= htmlspecialchars($s['descripcion']) ?></td>
                                        <td><?= htmlspecialchars($s['unidades_medida']) ?></td>
                                        <td><?= number_format($s['precio_base'], 2, ',', '.') ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button value="<?= $s['id_servicio'] ?>" type="button" class="btn btn-primary btn-sm btn-editar">
                                                <i class="fas fa-edit"></i>
                                                </button>
        
                                                <button type="button" 
                                                    class="btn btn-danger btn-sm btn-eliminar" 
                                                    data-id="<?= $s['id_servicio'] ?>" 
                                                    data-controlador="ServicioControlador"
                                                    title="Eliminar">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>  
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once('vista/servicios/crear.php'); ?>
<?php include_once('vista/servicios/editar.php'); ?>
<?php include_once('vista/servicios/eliminar.php'); ?>
<?php require_once('vista/parcial/footer.php'); ?>

<script type="text/javascript" src="vista/js/editarServicio.js"></script>
<script type="text/javascript" src="vista/js/eliminar.js"></script>
<script type="text/javascript" src="vista/js/mainServicio.js"></script>