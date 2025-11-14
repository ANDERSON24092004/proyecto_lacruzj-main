<div class="modal fade" id="registrarProveedorModal" tabindex="-1" aria-labelledby="registrarProveedorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
                <h5 class="modal-title" id="registrarProveedorModalLabel">
                    <i class="fas fa-truck me-2"></i> Registrar Nuevo Proveedor
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <form id="formProveedor" action="index.php?c=ProveedorControlador&m=guardar_proveedores" method="POST">
                    <div class="row g-3">
                        <div id="crear_btnerror" class="text-danger mt-3"></div>
                        
                        <div class="col-md-6">
                            <label for="crear_rif" class="form-label">RIF</label>
                            <input type="text" class="form-control" id="crear_rif" name="rif">
                            <div id="rif_error" style="color: #fc5656cc;"></div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="crear_nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="crear_nombre" name="nombre">
                            <div id="nombre_error" style="color: #fc5656cc;"></div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="crear_telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="crear_telefono" name="telefono">
                            <div id="telefono_error" style="color:  #fc5656cc;"></div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="crear_email" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="crear_email" name="email">
                            <div id="email_error" style="color:  #fc5656cc;"></div>
                        </div>
                        
                        <div class="col-12">
                            <label for="crear_direccion" class="form-label">Dirección</label>
                            <textarea class="form-control" id="crear_direccion" name="direccion" rows="3"></textarea>
                            <div id="direccion_error" style="color:  #fc5656cc;"></div>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="modal-footer" style="background-color: #f8f9fa;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i> Cancelar
                </button>
                <button type="submit" id="guardar_proveedor" form="formProveedor" class="btn btn-primary" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;">
                    <i class="fas fa-save me-2"></i> Guardar Proveedor
                </button>
            </div>
        </div>
    </div>
</div>