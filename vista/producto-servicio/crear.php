<div class="modal fade" id="registrarProductoModal" tabindex="-1" aria-labelledby="registrarProductoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
                <h5 class="modal-title" id="registrarProductoModalLabel">
                    <i class="fas fa-plus-circle me-2"></i> Registrar Nuevo Producto
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="index.php?c=ProductoServicioControlador&m=guardar" method="POST" id="formCrearProducto">
                <div class="modal-body">
                    <div class="row g-3">
                        <div id="crear_btnerror" class="alert alert-danger d-none"></div>

                        <div class="col-md-6">
                            <label class="form-label">Tipo</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tipo" id="tipo_producto" value="1" checked>
                                    <label class="form-check-label" for="tipo_producto">
                                    Producto
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tipo" id="tipo_servicio" value="2">
                                    <label class="form-check-label" for="tipo_servicio">
                                    Servicio
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" maxlength="255" 
                            placeholder="Ingrese el nombre del producto o servicio">
                            <div id="nombre_error" class="text-danger small mt-1 d-none"></div>
                        </div>

                        <div class="col-md-6">
                            <label for="unidad_medida" class="form-label">Unidad de Medida</label>
                            <select class="form-select" id="unidad_medida" name="id_unidad_medida">
                                <option value="" selected disabled>Seleccione una unidad</option>
                                <?php if(isset($unidades) && !empty($unidades)): ?>
                                    <?php foreach ($unidades as $unidad): ?>
                                        <option value="<?= $unidad['id_unidad_medida'] ?>">
                                            <?= htmlspecialchars($unidad['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="" disabled>No hay unidades disponibles</option>
                                <?php endif; ?>
                            </select>
                            <div id="unidad_medida_error" class="text-danger small mt-1 d-none"></div>
                        </div>

                        <div class="col-md-6">
                            <label for="costo" class="form-label">Costo</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" min="0" class="form-control" id="costo" name="costo" value="0">
                            </div>
                            <div id="costo_error" class="text-danger small mt-1 d-none"></div>
                        </div>

                        <div class="row g-3 campos-producto">
                            <div class="col-md-3">
                                <label for="stock" class="form-label">Stock Inicial</label>
                                <input type="number" class="form-control" id="stock" name="stock" min="0" value="0" step="1">
                                <div id="stock_error" class="text-danger small mt-1 d-none"></div>
                            </div>

                            <div class="col-md-3">
                                <label for="precio_mayor" class="form-label">Precio al por Mayor</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" min="0" class="form-control" id="precio_mayor" name="precio_mayor" value="0">
                                </div>
                                <div id="precio_mayor_error" class=" text-danger small mt-1 d-none"></div>
                            </div>

                            <div class="col-md-6">
                                <label for="presentacion" class="form-label">Presentación</label>
                                <select class="form-select" id="presentacion" name="presentacion">
                                    <option value="" selected disabled>Seleccione presentación</option>
                                    <?php 
                                    if(isset($presentaciones) && !empty($presentaciones)):
                                        foreach($presentaciones as $pres): 
                                    ?>
                                        <option value="<?= htmlspecialchars($pres['nombre']) ?>">
                                            <?= htmlspecialchars($pres['nombre']) ?>
                                        </option>
                                    <?php 
                                        endforeach;
                                    endif; 
                                    ?>
                                </select>
                                <div id="presentacion_error" class=" text-danger small mt-1 d-none"></div>
                            </div>

                            <div class="col-md-12 mt-2">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="es_fabricado" name="es_fabricado" value="1">
                                    <label class="form-check-label" for="es_fabricado">
                                        <i class="fas fa-industry me-1"></i> Producto fabricado en la empresa
                                    </label>
                                </div>
                            </div>
                            
                            <template id="templateMateriaPrima">
                                <div class="row g-2 mb-2 fila-materia-prima">
                                    <div class="col-md-5">
                                        <select class="form-select select-materia-prima">
                                            <option value="" selected disabled>Seleccione materia prima</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="number" class="form-control cantidad-materia-prima" 
                                        min="0.1" step="0.1" value="1">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" class="form-control costo-unitario" readonly placeholder="Costo unitario">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-danger btn-sm btn-eliminar-materia">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </template>

                            <div class="col-md-12" id="materiasPrimasSection" style="display: none;">
                                <div class="card border-primary mt-3">
                                    <div class="card-header bg-primary text-white">
                                        <i class="fas fa-boxes me-2"></i> Materias Primas
                                    </div>
                                    <div class="card-body">
                                        <div id="materiasPrimasContainer"></div>
                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <button type="button" class="btn btn-sm btn-outline-primary" id="btnAgregarMateria">
                                                <i class="fas fa-plus-circle me-1"></i> Añadir Materia Prima
                                            </button>
                                            
                                            <div class="text-end">
                                                <div class="fw-bold text-success fs-5">
                                                    <i class="fas fa-dollar-sign me-1"></i>
                                                    <span id="costoTotalValor">0.00</span>
                                                </div>
                                                <small class="text-muted">Costo total</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnGuardar">
                        <i class="fas fa-save me-1"></i>Registrar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>