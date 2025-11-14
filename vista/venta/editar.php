<?php require_once 'vista/parcial/header.php.php'; ?>
<?php
require_once 'modelo/TasaCambio.php';
$tasaCambio = new TasaCambio();
$tasaOficial = $tasaCambio->obtenerTasa('oficial');
$tasaPersonalizada = $tasaCambio->obtenerTasa('personalizada');
?>

<div class="container mt-4">
    <h2>Editar Venta</h2>
    <form method="POST" action="index.php?c=venta&m=actualizar">
        <input type="hidden" name="id" value="<?= $venta['id'] ?>">

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Producto</label>
                <select name="producto_id" class="form-select" required>
                    <?php foreach ($productos as $producto): ?>
                        <option value="<?= $producto['id'] ?>"
                            <?= $producto['id'] == $venta['producto_id'] ? 'selected' : '' ?>>
                            <?= $producto['nombre'] ?> (Stock: <?= $producto['stock'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Cantidad</label>
                <input type="number" name="cantidad" class="form-control"
                    value="<?= $venta['cantidad'] ?>" min="1" required>
            </div>

            <div class="col-md-3">
                <label class="form-label">Precio Unitario ($)</label>
                <input type="number" name="precio" class="form-control"
                    value="<?= $venta['precio'] ?>" step="0.01" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">Tipo de Tasa</label>
                <select name="tipo_tasa" class="form-select" id="tipoTasa" onchange="calcularTotales()">
                    <option value="oficial" data-tasa="<?= $tasaOficial ?>"
                        <?= $venta['tipo_tasa'] == 'oficial' ? 'selected' : '' ?>>Oficial (<?= $tasaOficial ?>)</option>
                    <option value="personalizada" data-tasa="<?= $tasaPersonalizada ?>"
                        <?= $venta['tipo_tasa'] == 'personalizada' ? 'selected' : '' ?>>Personalizada (<?= $tasaPersonalizada ?>)</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Total en Dólares</label>
                <input type="number" name="total_dolares" id="totalDolares" class="form-control"
                    value="<?= $venta['total_dolares'] ?>" step="0.01" readonly>
            </div>

            <div class="col-md-4">
                <label class="form-label">Total en Bolívares</label>
                <input type="number" name="total_bolivares" id="totalBolivares" class="form-control"
                    value="<?= $venta['total_bolivares'] ?>" step="0.01" readonly>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Actualizar Venta</button>
    </form>
</div>

<script>
    const tipoTasa = document.getElementById('tipoTasa');
    const totalDolares = document.getElementById('totalDolares');
    const totalBolivares = document.getElementById('totalBolivares');

    document.querySelector('[name="cantidad"]').addEventListener('input', calcularTotales);
    document.querySelector('[name="precio"]').addEventListener('input', calcularTotales);
    tipoTasa.addEventListener('change', calcularTotales);

    function calcularTotales() {
        const cantidad = parseFloat(document.querySelector('[name="cantidad"]').value) || 0;
        const precio = parseFloat(document.querySelector('[name="precio"]').value) || 0;
        const tasa = parseFloat(tipoTasa.options[tipoTasa.selectedIndex].getAttribute('data-tasa')) || 0;

        const totalUSD = cantidad * precio;
        const totalBs = totalUSD * tasa;

        totalDolares.value = totalUSD.toFixed(2);
        totalBolivares.value = totalBs.toFixed(2);
    }
</script>

<?php require_once 'vista/parcial/footer.php.php'; ?>

