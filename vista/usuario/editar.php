<div class="modal fade" id="editarUsuarioModal" tabindex="-1" aria-labelledby="editarUsuarioModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i> Editar Usuario
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="formEditarUsuario" method="POST">
                <div class="modal-body">
                    <div id="editar_btnerror" class="error-message text-danger small mt-2 mb-3 p-2 bg-light rounded" style="display: none;"></div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <input type="hidden" id="cedula_editar" name="cedula">
                            <label for="cedula_display" class="form-label">Cédula</label>
                            <input type="text" class="form-control" id="cedula_display" readonly 
                                   placeholder="Ej: 12345678">
                            <div id="editar_cedula_error" class="text-danger small mt-1"></div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="nombre_editar" class="form-label">Nombre de Usuario</label>
                            <input type="text" class="form-control" id="nombre_editar" name="nombre"
                                   placeholder="Letras, números, espacios y guiones">
                            <div id="editar_nombre_error" class="text-danger small mt-1"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="telefono_editar" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="telefono_editar" name="telefono"
                                   placeholder="Ej: 0412-1234567">
                            <div id="editar_telefono_error" class="text-danger small mt-1"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="correo_editar" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="correo_editar" name="correo" 
                                   placeholder="Ej: usuario@empresa.com">
                            <div id="editar_correo_error" class="text-danger small mt-1"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="rol_editar" class="form-label">Rol</label>
                            <select class="form-select" id="rol_editar" name="id_rol">
                                <option value="">Seleccione un rol</option>
                            </select>
                            <div id="editar_rol_error" class="text-danger small mt-1"></div>
                        </div>
                      
                        <div class="col-md-6 mb-3">
                            <label for="clave_editar" class="form-label">Contraseña (dejar en blanco para no cambiar)</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" class="form-control" id="clave_editar" name="clave" 
                                placeholder="Mínimo 6 caracteres">
                            </div>
                            <div id="editar_clave_error" class="text-danger small mt-1"></div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer" style="background-color: #f8f9fa;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;">
                        <i class="fas fa-save me-2"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>