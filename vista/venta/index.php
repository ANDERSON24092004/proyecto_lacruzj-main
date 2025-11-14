<?php include 'vista/parcial/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Listado de Ventas</h2>
        <a href="index.php?c=venta&m=crear" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Nueva Venta
        </a>
    </div>

    <?php if (!empty($datos)): ?>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Productos Vendidos</th>
                    <th>Total USD</th>
                    <th>Total Bs</th>
                    <th>Tasa</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($datos as $venta): ?>
                    <tr>
                        <td><?= $venta['id'] ?></td>
                        <td><?= !empty($venta['productos']) ? htmlspecialchars($venta['productos']) : 'Sin producto' ?></td>
                        <td><?= number_format((float)$venta['total_usd'], 2) ?></td>
                        <td><?= number_format((float)$venta['total_bs'], 2) ?></td>
                        <td><?= $venta['tasa'] ?></td>
                        <td><?= $venta['fecha'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay ventas registradas.</p>
    <?php endif; ?>
</div>

<?php include 'vista/parcial/footer.php'; ?>

