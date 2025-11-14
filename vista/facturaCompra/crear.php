<div class="modal fade" id="registrarFacturaModal" tabindex="-1" aria-labelledby="registrarFacturaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
                <h5 class="modal-title" id="registrarFacturaModalLabel">
                    <i class="fas fa-file-invoice me-2"></i> Registrar Factura de Compra
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <div id="errores-generales" class="alert alert-danger d-none" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <span id="texto-error-general"></span>
                </div>

                <form id="formFacturaCompra" action="index.php?c=FacturaCompraControlador&m=registrar" method="POST" novalidate>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label for="proveedor" class="form-label">Proveedor</label>
                            <select class="form-select select2-proveedor" id="proveedor" name="id_proveedor" required>
                                <option value="">Seleccionar proveedor...</option>
                                <?php foreach ($proveedores as $proveedor): ?>
                                    <option value="<?= $proveedor['id_proveedores'] ?>" 
                                            data-nombre="<?= htmlspecialchars($proveedor['nombre']) ?>"
                                            data-rif="<?= $proveedor['id_proveedores'] ?>">
                                        <?= htmlspecialchars($proveedor['nombre']) ?> (RIF: <?= $proveedor['id_proveedores'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback" id="error-proveedor">
                                Por favor seleccione un proveedor
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <label for="num_factura" class="form-label">Número de Factura</label>
                            <input type="text" class="form-control" id="num_factura" name="num_factura" 
                                   placeholder="Ej: 001-001-0000001" pattern="[0-9\-]+" required
                                   oninput="validarNumeroFactura(this)">
                            <div class="invalid-feedback" id="error-num_factura">
                                El número de factura es obligatorio y solo puede contener números y guiones
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <label for="fecha" class="form-label">Fecha</label>
                            <input type="date" class="form-control" id="fecha" name="fecha" value="<?= date('Y-m-d') ?>" required
                                   max="<?= date('Y-m-d') ?>">
                            <div class="invalid-feedback" id="error-fecha">
                                La fecha es obligatoria y no puede ser futura
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="fas fa-list me-2"></i> Detalles de Materia Prima
                            </h6>
                        </div>
                        <div class="card-body">
                            <div id="detalles-container">
                            </div>
                            
                            <button type="button" id="agregar-detalle" class="btn btn-outline-primary btn-sm mt-3">
                                <i class="fas fa-plus me-1"></i> Agregar Materia Prima
                            </button>
                            
                            <div class="alert alert-warning mt-3 d-none" id="error-detalles">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                Debe agregar al menos un detalle de materia prima
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="fas fa-calculator me-2"></i> Resumen
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label">Subtotal</label>
                                    <input type="text" class="form-control" id="subtotal" value="0.00" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">IVA (16%)</label>
                                    <input type="text" class="form-control" id="total_iva" name="total_iva" value="0.00" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Total General</label>
                                    <input type="text" class="form-control" id="total_general" name="total_general" value="0.00" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="modal-footer" style="background-color: #f8f9fa;">
                <button type="button" class="btn btn-secondary" id="btnCancelar" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i> Cancelar
                </button>
                <button type="button" id="btnGuardar" class="btn btn-primary" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;">
                    <i class="fas fa-save me-2"></i> Guardar Factura
                </button>
            </div>
        </div>
    </div>
</div>

<template id="template-detalle">
    <div class="detalle-item card mb-3">
        <div class="card-body">
            <div class="row g-3 align-items-center">
                <div class="col-md-3">
                    <label class="form-label">Materia Prima</label>
                    <select class="form-select select2-materia" name="detalles[][id_materia_prima]" required>
                        <option value="">Seleccionar materia prima...</option>
                        <?php foreach ($materiasPrimas as $materia): ?>
                            <option value="<?= $materia['id_materia'] ?>" 
                                    data-nombre="<?= htmlspecialchars($materia['nombre']) ?>"
                                    data-unidad="<?= htmlspecialchars($materia['unidad_medida']) ?>"
                                    data-stock="<?= $materia['stock'] ?>"
                                    data-costo="<?= $materia['costo'] ?>">
                                <?= htmlspecialchars($materia['nombre']) ?> (Stock: <?= $materia['stock'] ?> <?= $materia['unidad_medida'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">
                        Por favor seleccione una materia prima
                    </div>
                </div>
                
                <div class="col-md-2">
                    <label class="form-label">Cantidad</label>
                    <input type="number" class="form-control cantidad-input" name="detalles[][cantidad]" min="1" step="1" required
                           oninput="validarCantidad(this)">
                    <div class="invalid-feedback">
                        La cantidad debe ser mayor a 0
                    </div>
                </div>
                
                <div class="col-md-2">
                    <label class="form-label">Costo</label>
                    <input type="number" class="form-control costo-input" name="detalles[][costo]" min="0.01" step="0.01" required
                           oninput="validarCosto(this)">
                    <div class="invalid-feedback">
                        El costo debe ser mayor a 0
                    </div>
                </div>
                
                <div class="col-md-2">
                    <label class="form-label">Unidad de Medida</label>
                    <input type="text" class="form-control unidad-medida" readonly>
                </div>
                
                <div class="col-md-2">
                    <label class="form-label">Subtotal</label>
                    <input type="text" class="form-control subtotal-input" value="0.00" readonly>
                </div>
                
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <button type="button" class="btn btn-danger btn-sm w-100 remover-detalle">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            
            <div class="row mt-2">
                <div class="col-md-6">
                    <small class="text-muted stock-info">Stock actual: 0</small>
                </div>
                <div class="col-md-6 text-end">
                    <small class="text-muted nuevo-stock-info">Nuevo stock: 0</small>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
function validarNumeroFactura(input) {
    const valor = input.value;
    const regex = /^[0-9\-]*$/;
    
    if (!regex.test(valor)) {
        input.value = valor.replace(/[^0-9\-]/g, '');
    }
    
    if (input.value.trim() === '') {
        input.classList.add('is-invalid');
    } else {
        input.classList.remove('is-invalid');
        input.classList.add('is-valid');
    }
}

function validarCantidad(input) {
    const valor = parseFloat(input.value);
    
    if (isNaN(valor) || valor <= 0) {
        input.classList.add('is-invalid');
        input.classList.remove('is-valid');
    } else {
        input.classList.remove('is-invalid');
        input.classList.add('is-valid');
    }
}

function validarCosto(input) {
    const valor = parseFloat(input.value);
    
    if (isNaN(valor) || valor <= 0) {
        input.classList.add('is-invalid');
        input.classList.remove('is-valid');
    } else {
        input.classList.remove('is-invalid');
        input.classList.add('is-valid');
    }
}

function limpiarValidaciones() {
    const form = document.getElementById('formFacturaCompra');
    const inputs = form.querySelectorAll('input, select');
    
    inputs.forEach(input => {
        input.classList.remove('is-invalid', 'is-valid');
    });
    
    document.getElementById('errores-generales').classList.add('d-none');
    document.getElementById('error-detalles').classList.add('d-none');
}

function validarFormulario() {
    let esValido = true;
    
    limpiarValidaciones();
    
    const proveedor = document.getElementById('proveedor');
    const numFactura = document.getElementById('num_factura');
    const fecha = document.getElementById('fecha');
    
    if (!proveedor.value) {
        proveedor.classList.add('is-invalid');
        esValido = false;
    }
    
    if (!numFactura.value.trim()) {
        numFactura.classList.add('is-invalid');
        esValido = false;
    }
    
    if (!fecha.value) {
        fecha.classList.add('is-invalid');
        esValido = false;
    }
    
    const detalles = document.querySelectorAll('.detalle-item');
    if (detalles.length === 0) {
        document.getElementById('error-detalles').classList.remove('d-none');
        esValido = false;
    } else {
        detalles.forEach((detalle, index) => {
            const materia = detalle.querySelector('.select2-materia');
            const cantidad = detalle.querySelector('.cantidad-input');
            const costo = detalle.querySelector('.costo-input');
            
            if (!materia.value) {
                materia.classList.add('is-invalid');
                esValido = false;
            }
            
            if (!cantidad.value || parseFloat(cantidad.value) <= 0) {
                cantidad.classList.add('is-invalid');
                esValido = false;
            }
            
            if (!costo.value || parseFloat(costo.value) <= 0) {
                costo.classList.add('is-invalid');
                esValido = false;
            }
        });
    }
    
    return esValido;
}

document.getElementById('btnGuardar').addEventListener('click', function() {
    if (validarFormulario()) {
        document.getElementById('formFacturaCompra').dispatchEvent(new Event('submit'));
    } else {
        const erroresGenerales = document.getElementById('errores-generales');
        const textoError = document.getElementById('texto-error-general');
        textoError.textContent = 'Por favor complete todos los campos requeridos correctamente';
        erroresGenerales.classList.remove('d-none');
        
        document.querySelector('.modal-body').scrollTop = 0;
    }
});

document.getElementById('btnCancelar').addEventListener('click', function() {
    document.getElementById('formFacturaCompra').reset();
    limpiarValidaciones();
    document.getElementById('detalles-container').innerHTML = '';
});

document.getElementById('registrarFacturaModal').addEventListener('hide.bs.modal', function(event) {

});
</script>