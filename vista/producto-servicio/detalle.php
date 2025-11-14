<div class="modal fade" id="detalleMateriasModal" tabindex="-1" aria-labelledby="detalleMateriasModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #0d6efd, #0b5ed7);">
                <h5 class="modal-title" id="detalleMateriasModalLabel">
                    <i class="fas fa-boxes me-2"></i> Detalle de Materias Primas
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0" id="nombreProductoDetalle"></h4>
                    <span class="badge bg-primary" id="fabricadoBadge">Fabricado</span>
                </div>
                
                <div class="table-responsive mt-4">
                    <table class="table table-hover align-middle" id="tablaMateriasDetalle">
                        <thead class="table-primary">
                            <tr>
                                <th>Materia Prima</th>
                                <th>Cantidad</th>
                                <th>Unidad de Medida</th>
                            </tr>
                        </thead>
                        <tbody>
                    
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>