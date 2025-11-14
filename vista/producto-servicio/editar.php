<div class="modal fade" id="editarProductoModal" tabindex="-1" aria-labelledby="editarProductoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
                <h5 class="modal-title" id="editarProductoModalLabel">
                    <i class="fas fa-edit me-2"></i> Editar Producto/Servicio
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="index.php?c=ProductoServicioControlador&m=actualizar" method="POST" id="formEditarProducto">
                <input type="hidden" name="id_inv" id="editar_id_inv">
                
                <div class="modal-body">
                    <div class="row g-3">
                        <div id="editar_btnerror" class="alert alert-danger d-none"></div>

                        <div class="col-md-6">
                            <label class="form-label">Tipo</span></label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tipo" id="editar_tipo_producto" value="1">
                                    <label class="form-check-label" for="editar_tipo_producto">
                                        Producto
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tipo" id="editar_tipo_servicio" value="2">
                                    <label class="form-check-label" for="editar_tipo_servicio">
                                        Servicio
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="editar_nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="editar_nombre" name="nombre" maxlength="255">
                            <div id="editar_nombre_error" class="text-danger small mt-1 d-none"></div>
                        </div>

                        <div class="col-md-6">
                            <label for="editar_unidad_medida" class="form-label">Unidad de Medida</label>
                            <select class="form-select" id="editar_unidad_medida" name="id_unidad_medida">
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
                            <div id="editar_unidad_medida_error" class="text-danger small mt-1 d-none"></div>
                        </div>

                        <div class="col-md-6">
                            <label for="editar_costo" class="form-label">Costo</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" min="0" class="form-control" id="editar_costo" name="costo"
                                >
                            </div>
                            <div id="editar_costo_error" class="text-danger small mt-1 d-none"></div>
                        </div>

                        <div class="row g-3 campos-producto-editar">
                            <div class="col-md-3">
                                <label for="editar_stock" class="form-label">Stock</label>
                                <input type="number" class="form-control" id="editar_stock" name="stock" min="0" step="1" value="0">
                                <div id="editar_stock_error" class="text-danger small mt-1 d-none"></div>
                            </div>

                            <div class="col-md-3">
                                <label for="editar_precio_mayor" class="form-label">Precio al por Mayor</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" min="0" class="form-control" id="editar_precio_mayor" name="costo_mayor" value="0">
                                    <div id="editar_precio_mayor_error" class="text-danger small mt-1 d-none"></div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="editar_presentacion" class="form-label">Presentación</label>
                                <select class="form-select" id="editar_presentacion" name="presentacion">
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
                                <div id="editar_presentacion_error" class="text-danger small mt-1 d-none"></div>
                            </div>

                            <div class="col-md-12 mt-2">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="editar_es_fabricado" name="es_fabricado" value="1">
                                    <label class="form-check-label" for="editar_es_fabricado">
                                        <i class="fas fa-industry me-1"></i> Producto fabricado en la empresa
                                    </label>
                                </div>
                            </div>
                            
                            <template id="templateMateriaPrimaEditar">
                                <div class="row g-2 mb-2 fila-materia-prima-editar">
                                    <div class="col-md-5">
                                        <select class="form-select select-materia-prima-editar">
                                            <option value="" selected disabled>Seleccione materia prima</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="number" class="form-control cantidad-materia-prima-editar" 
                                               min="0.1" step="0.1">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" class="form-control costo-unitario-editar" readonly placeholder="Costo unitario">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-danger btn-sm btn-eliminar-materia-editar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </template>

                            <div class="col-md-12" id="materiasPrimasSectionEditar" style="display: none;">
                                <div class="card border-primary mt-3">
                                    <div class="card-header bg-primary text-white">
                                        <i class="fas fa-boxes me-2"></i> Materias Primas
                                        <span class="badge bg-warning ms-2" id="stockWarning" style="display: none;">
                                            <i class="fas fa-exclamation-triangle me-1"></i> Stock insuficiente
                                        </span>
                                    </div>
                                    <div class="card-body">
                                        <div id="materiasPrimasContainerEditar"></div>
                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <button type="button" class="btn btn-sm btn-outline-primary" id="btnAgregarMateriaEditar">
                                                <i class="fas fa-plus-circle me-1"></i> Añadir Materia Prima
                                            </button>
                                            
                                            <div class="text-end">
                                                <div class="fw-bold text-success fs-5">
                                                    <i class="fas fa-dollar-sign me-1"></i>
                                                    <span id="costoTotalValorEditar">0.00</span>
                                                </div>
                                                <small class="text-muted">Costo total de materias primas</small>
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
                    <button type="submit" class="btn btn-primary" id="btnActualizar">
                        <i class="fas fa-save me-1"></i> Editar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>