<?php require_once 'vista/parcial/header.php'; ?>

<div class="container mt-4">
    <h2>Registrar Nueva Venta</h2>
    <form method="POST" action="index.php?c=venta&m=guardar">

        <div class="mb-3">
    <label class="form-label">Tasa Oficial (Bs/USD):</label>
    <input type="text" readonly class="form-control" value="<?= $tasaOficial ?? 'No disponible' ?>">
</div>

<div class="mb-3">
    <label class="form-label">Tasa Personalizada (Bs/USD):</label>
    <input type="text" readonly class="form-control" value="<?= $tasaPersonalizada ?? 'No disponible' ?>">
</div>

<!-- Opcionalmente usar radio buttons para que el usuario seleccione con cuál trabajar -->
<div class="mb-3">
    <label class="form-label">Seleccione tasa a aplicar:</label><br>
    <input type="radio" name="tasa" value="<?= $tasaOficial ?>" checked> Oficial (<?= $tasaOficial ?>)<br>
    <input type="radio" name="tasa" value="<?= $tasaPersonalizada ?>"> Personalizada (<?= $tasaPersonalizada ?>)
</div>


        <div id="productos-container">
            <div class="producto-row row align-items-end mb-3">
                <div class="col-md-5">
                    <label class="form-label">Producto</label>
                    <select name="producto_id[]" class="form-select producto-select" required>
                        <option value="">Seleccione un producto</option>
                        <?php foreach ($productos as $prod): ?>
                            <option value="<?= $prod['id'] ?>" data-precio="<?= $prod['precio'] ?>">
                                <?= htmlspecialchars($prod['nombre']) ?> (Stock: <?= $prod['stock'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Cantidad</label>
                    <input type="number" name="cantidades[]" class="form-control" placeholder="Cantidad" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Precio USD</label>
                    <input type="number" step="0.01" name="precios[]" class="form-control precio-input" placeholder="Precio USD" required>
                </div>

                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-remove mt-4">Quitar</button>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <button type="button" id="btn-agregar-producto" class="btn btn-secondary">Agregar otro producto</button>
        </div>

        <button type="submit" class="btn btn-success">Registrar Venta</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Asignar automáticamente el precio según el producto
    document.body.addEventListener('change', function (e) {
        if (e.target.classList.contains('producto-select')) {
            const selected = e.target.selectedOptions[0];
            const precio = selected.getAttribute('data-precio');
            const precioInput = e.target.closest('.producto-row').querySelector('.precio-input');
            if (precio) {
                precioInput.value = parseFloat(precio).toFixed(2);
            }
        }
    });

    // Duplicar la fila de producto
    document.getElementById('btn-agregar-producto').addEventListener('click', function () {
        const container = document.getElementById('productos-container');
        const clone = container.querySelector('.producto-row').cloneNode(true);
        clone.querySelectorAll('input').forEach(input => input.value = '');
        clone.querySelector('select').selectedIndex = 0;
        container.appendChild(clone);
    });

    // Eliminar fila
    document.body.addEventListener('click', function (e) {
        if (e.target.classList.contains('btn-remove')) {
            const rows = document.querySelectorAll('.producto-row');
            if (rows.length > 1) {
                e.target.closest('.producto-row').remove();
            } else {
                alert('Debe haber al menos un producto en la venta.');
            }
        }
    });
});
</script>

<?php require_once 'vista/parcial/footer.php'; ?>
