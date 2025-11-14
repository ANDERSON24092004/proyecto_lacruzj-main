<div class="modal fade mensaje-modal" id="mensajeModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg animate__animated">
            <div class="modal-header border-0" id="mensajeModalHeader" style="padding: 1.5rem 1.5rem 1rem 1.5rem;">
                <div class="d-flex align-items-center w-100">
                    <div class="flex-shrink-0" id="mensajeModalIconContainer">
                        <i class="fas fa-2x text-white" id="mensajeModalIcon"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h5 class="modal-title fw-bold mb-1 text-white" id="mensajeModalTitle">Información</h5>
                        <p class="text-white small mb-0 opacity-10" id="mensajeModalSubtitle">Proceso completado</p>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            
            <div class="modal-body py-4">
                <div class="text-center mb-3">
                    <h4 class="fw-bold mb-2" id="mensajeModalText"></h4>
                    <p class="text-muted mb-0" id="mensajeModalDetalle"></p>
                </div>
                
                <div class="progress mt-3" id="mensajeProgress" style="height: 4px; display: none;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" 
                         role="progressbar" style="width: 100%"></div>
                </div>
            </div>
            
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-lg btn-primary px-4 py-2 shadow-sm" data-bs-dismiss="modal" id="mensajeModalBtn">
                    <i class="fas fa-check me-2"></i> Continuar
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.mensaje-modal{
    z-index: 99999 !important;
}
.mensaje-modal-success {
    background: linear-gradient(135deg, #28a745, #20c997);
    border-left: 4px solid #198754;
}

.mensaje-modal-error {
    background: linear-gradient(135deg, #dc3545, #e83e8c);
    border-left: 4px solid #dc3545;
}

.mensaje-modal-warning {
    background: linear-gradient(135deg, #ffc107, #fd7e14);
    border-left: 4px solid #ffc107;
}

.mensaje-modal-info {
    background: linear-gradient(135deg, #17a2b8, #6f42c1);
    border-left: 4px solid #17a2b8;
}

#mensajeModal .modal-content {
    border-radius: 16px;
    overflow: hidden;
    transform: scale(0.9);
    transition: all 0.3s ease;
}

#mensajeModal.show .modal-content {
    transform: scale(1);
}

#mensajeModalIconContainer {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    margin-top: 0px;
    margin-bottom: 15px;
}

.mensaje-icon-success {
    background: linear-gradient(135deg, #28a745, #20c997);
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
}

.mensaje-icon-error {
    background: linear-gradient(135deg, #dc3545, #e83e8c);
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
}

.mensaje-icon-warning {
    background: linear-gradient(135deg, #ffc107, #fd7e14);
    box-shadow: 0 4px 15px rgba(255, 193, 7, 0.3);
}

.mensaje-icon-info {
    background: linear-gradient(135deg, #17a2b8, #6f42c1);
    box-shadow: 0 4px 15px rgba(23, 162, 184, 0.3);
}

#mensajeModalBtn {
    border-radius: 12px;
    font-weight: 600;
    transition: all 0.3s ease;
}

#mensajeModalBtn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
}

.modal-backdrop.show {
    opacity: 0.5;
    background: #000;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translateY(-50px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes bounceIn {
    0% {
        opacity: 0;
        transform: scale(0.3);
    }
    50% {
        opacity: 1;
        transform: scale(1.05);
    }
    70% {
        transform: scale(0.9);
    }
    100% {
        opacity: 1;
        transform: scale(1);
    }
}

.animate-pulse {
    animation: pulse 2s infinite;
}

.animate-slide-in {
    animation: slideInDown 0.5s ease-out;
}

.animate-bounce-in {
    animation: bounceIn 0.6s ease-out;
}

#mensajeModal .modal-header {
    padding: 1.5rem 1.5rem 1rem 1.5rem;
}

#mensajeModal .modal-body {
    padding: 0.5rem 1.5rem 1rem 1.5rem;
}

#mensajeModal .modal-footer {
    padding: 0 1.5rem 1.5rem 1.5rem;
}
</style>

<script>
function mostrarMensaje(tipo, mensaje, detalle = '', opciones = {}) {
    const modalElement = document.getElementById('mensajeModal');
    const modal = new bootstrap.Modal(modalElement);
    const header = document.getElementById('mensajeModalHeader');
    const iconContainer = document.getElementById('mensajeModalIconContainer');
    const icon = document.getElementById('mensajeModalIcon');
    const title = document.getElementById('mensajeModalTitle');
    const subtitle = document.getElementById('mensajeModalSubtitle');
    const text = document.getElementById('mensajeModalText');
    const detalleElem = document.getElementById('mensajeModalDetalle');
    const progress = document.getElementById('mensajeProgress');
    const btn = document.getElementById('mensajeModalBtn');

    const config = {
        autoClose: opciones.autoClose || false,
        closeTime: opciones.closeTime || 3000,
        showProgress: opciones.showProgress || false,
        animation: opciones.animation || 'bounceIn'
    };
    
    header.className = 'modal-header border-0 pb-0';
    iconContainer.className = 'flex-shrink-0';
    modalElement.querySelector('.modal-content').className = 'modal-content border-0 shadow-lg animate__animated';
    
    switch(tipo) {
        case 'success':
            header.classList.add('mensaje-modal-success', 'text-white');
            iconContainer.classList.add('mensaje-icon-success', 'animate-pulse');
            icon.className = 'fas fa-check text-white';
            title.innerHTML = '¡Éxito!';
            subtitle.textContent = 'Operación completada correctamente';
            btn.className = 'btn btn-lg btn-success px-4 py-2 shadow-sm';
            break;
            
        case 'error':
            header.classList.add('mensaje-modal-error', 'text-white');
            iconContainer.classList.add('mensaje-icon-error');
            icon.className = 'fas fa-exclamation-triangle text-white';
            title.innerHTML = 'Error';
            subtitle.textContent = 'Ha ocurrido un problema';
            btn.className = 'btn btn-lg btn-danger px-4 py-2 shadow-sm';
            break;
            
        case 'warning':
            header.classList.add('mensaje-modal-warning', 'text-white');
            iconContainer.classList.add('mensaje-icon-warning');
            icon.className = 'fas fa-exclamation-circle text-white';
            title.innerHTML = 'Advertencia';
            subtitle.textContent = 'Atención requerida';
            btn.className = 'btn btn-lg btn-warning px-4 py-2 shadow-sm text-white';
            break;
            
        case 'info':
            header.classList.add('mensaje-modal-info', 'text-white');
            iconContainer.classList.add('mensaje-icon-info');
            icon.className = 'fas fa-info-circle text-white';
            title.innerHTML = 'Información';
            subtitle.textContent = 'Proceso completado';
            btn.className = 'btn btn-lg btn-info px-4 py-2 shadow-sm text-white';
            break;
    }
    
    modalElement.querySelector('.modal-content').classList.add(`animate__${config.animation}`);
    
    text.textContent = mensaje;
    detalleElem.textContent = detalle;
    
    if (config.showProgress) {
        progress.style.display = 'block';
    } else {
        progress.style.display = 'none';
    }

    if (config.autoClose) {
        setTimeout(() => {
            modal.hide();
        }, config.closeTime);
    }
    
    modalElement.addEventListener('shown.bs.modal', function () {
        iconContainer.style.transform = 'scale(0)';
        setTimeout(() => {
            iconContainer.style.transition = 'transform 0.5s ease';
            iconContainer.style.transform = 'scale(1)';
        }, 100);
    });

    modalElement.addEventListener('hidden.bs.modal', function () {
        iconContainer.style.transform = 'scale(1)';
    });
    
    modal.show();
    
    return modal;
}

function mostrarMensajeCarga(mensaje = 'Procesando...') {
    return mostrarMensaje('info', mensaje, 'Por favor espere', {
        autoClose: false,
        showProgress: true,
        animation: 'slideInDown'
    });
}

function mostrarMensajeTemporal(tipo, mensaje, detalle = '', tiempo = 2000) {
    return mostrarMensaje(tipo, mensaje, detalle, {
        autoClose: true,
        closeTime: tiempo,
        showProgress: false,
        animation: 'slideInDown'
    });
}

document.addEventListener('DOMContentLoaded', function() {
    <?php if (isset($_SESSION['tipo_mensaje']) && isset($_SESSION['mensaje']) && isset($_SESSION['mensaje_detalle'])): ?>
        setTimeout(() => {
            mostrarMensaje('<?= $_SESSION['tipo_mensaje'] ?>', 
                          '<?= addslashes($_SESSION['mensaje']) ?>', 
                          '<?= addslashes($_SESSION['mensaje_detalle']) ?>');
        }, 500);
        
        <?php 
        unset($_SESSION['tipo_mensaje']);
        unset($_SESSION['mensaje']);
        unset($_SESSION['mensaje_detalle']);
        ?>
    <?php endif; ?>
});
</script>