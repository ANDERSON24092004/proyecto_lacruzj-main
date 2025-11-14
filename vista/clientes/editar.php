<div class="modal fade" id="editarClienteModal" tabindex="-1" aria-labelledby="editarClienteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
                <h5 class="modal-title text-white" id="editarClienteModalLabel">
                    <i class="fas fa-user-edit me-2"></i> Editar Cliente
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form action="index.php?c=ClienteControlador&m=actualizarAjax" method="POST" id="formEditarCliente">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="rif_editar" class="form-label">RIF</label>
                            <input type="text" class="form-control" id="rif_editar" name="rif" readonly>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="razon_social_editar" class="form-label">Razón Social</label>
                            <input type="text" class="form-control" id="razon_social_editar" name="razon_social"
                            placeholder="Ingrese la razón social del cliente">
                            <div id="razonerror_editar" style="color: #dc3545; font-size: 0.875em; margin-top: 0.25rem; font-weight: 500;"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="telefono_editar" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="telefono_editar" name="telefono" 
                            placeholder="Ej: 0412-1234567" maxlength="12">
                            <div id="telefonoerror_editar" style="color: #dc3545; font-size: 0.875em; margin-top: 0.25rem; font-weight: 500;"></div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="email_editar" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="email_editar" name="email"
                            placeholder="Ej: cliente@example.com">
                            <div id="emailerror_editar" style="color: #dc3545; font-size: 0.875em; margin-top: 0.25rem; font-weight: 500;"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="direccion_editar" class="form-label">Dirección</label>
                        <textarea class="form-control" id="direccion_editar" name="direccion" rows="3"
                        placeholder="Ingrese la dirección completa del cliente"></textarea>
                        <div id="direccionerror_editar" style="color: #dc3545; font-size: 0.875em; margin-top: 0.25rem; font-weight: 500;"></div>
                    </div>
                </div>
                <div class="modal-footer" style="background-color: #f8f9fa;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>