<div class="modal fade" id="editarProveedorModal" tabindex="-1" aria-labelledby="editarProveedorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i> Editar Proveedor
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <form id="formEditarProveedor" action="index.php?c=ProveedorControlador&m=actualizar_proveedor" method="POST">
                    <input type="hidden" name="id_proveedores" id="id_proveedores">
                    
                    <div class="row g-3">
                        <div id="editar_btnerror" class="text-danger mt-3"></div>
                        
                        <div class="col-md-6">
                            <label class="form-label">RIF</label>
                            <input type="text" class="form-control" name="rif" id="rif" readonly="">
                            <div id="rif_editar_error" style="color: #89879c;"></div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Nombre</label>
                            <input type="text" class="form-control" name="nombre" id="nombre" required>
                            <div id="enombre_editar_error" style="color: #89879c;"></div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="text" class="form-control" name="telefono" id="telefono" required>
                            <div id="telefono_editar_error" style="color: #89879c;"></div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" name="email" id="email" required>
                            <div id="email_editar_error" style="color: #89879c;"></div>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label">Dirección</label>
                            <textarea class="form-control" name="direccion" id="direccion" rows="3" required></textarea>
                            <div id="direccion_editar_error" style="color: #89879c;"></div>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="modal-footer" style="background-color: #f8f9fa;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i> Cancelar
                </button>
                <button type="submit" id="editar_proveedor" form="formEditarProveedor" class="btn btn-primary" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;">
                    <i class="fas fa-save me-2"></i> Guardar Cambios
                </button>
            </div>
        </div>
    </div>
</div>