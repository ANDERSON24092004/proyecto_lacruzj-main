<?php
require_once('header.php');
?>

<div class="container py-5">
    <h2 class="text-center mb-5">Bienvenido a J.Lacruz</h2>
    <div class="row g-4 justify-content-center">
        
        <?php if ($permiso->tienePermiso($_SESSION['usuario']['id_rol'], 'clientes')): ?>
        <div class="col-md-4 col-lg-3">
            <a href="index.php?c=ClienteControlador&m=index" style="text-decoration: none; color: inherit;">
                <div class="card step-card h-100">
                    <div class="card-body text-center p-4">
                        <div class="step-icon mx-auto mb-3">
                            <i class="fas fa-users fa-3x text-dark"></i>
                        </div>
                        <h5 class="card-title fw-bold">Clientes</h5>
                        <p class="card-text">Gestiona y registra los clientes asociados</p>
                    </div>
                </div>
            </a>
        </div>
        <?php endif; ?>
        
        <?php if ($permiso->tienePermiso($_SESSION['usuario']['id_rol'], 'presupuestos')): ?>
        <div class="col-md-4 col-lg-3">
            <a href="index.php?c=PresupuestoControlador&m=index" style="text-decoration: none; color: inherit;">
                <div class="card step-card h-100">
                    <div class="card-body text-center p-4">
                        <div class="step-icon mx-auto mb-3">
                            <i class="fas fa-calculator fa-3x text-dark"></i>
                        </div>
                        <h5 class="card-title fw-bold">Presupuesto</h5>
                        <p class="card-text">Calcula el presupuesto de los Servicios-Productos</p>
                    </div>
                </div>
            </a>
        </div>
        <?php endif; ?>
        
        <?php if ($permiso->tienePermiso($_SESSION['usuario']['id_rol'], 'facturacion')): ?>
        <div class="col-md-4 col-lg-3">
            <a href="index.php?c=FacturaControlador&m=index" style="text-decoration: none; color: inherit;">
                <div class="card step-card h-100">
                    <div class="card-body text-center p-4">
                        <div class="step-icon mx-auto mb-3">
                            <i class="fas fa-file-invoice fa-3x text-dark"></i>
                        </div>
                        <h5 class="card-title fw-bold">Facturación</h5>
                        <p class="card-text">Crea y calcula la factura de los Servicios-Productos</p>
                    </div>
                </div>
            </a>
        </div>
        <?php endif; ?>

        <?php if ($permiso->tienePermiso($_SESSION['usuario']['id_rol'], 'cuentas_cobrar')): ?>
        <div class="col-md-4 col-lg-3">
            <a href="index.php?c=CuentasCobrarControlador&m=index" style="text-decoration: none; color: inherit;">
                <div class="card step-card h-100">
                    <div class="card-body text-center p-4">
                        <div class="step-icon mx-auto mb-3">
                            <i class="fas fa-file-invoice fa-3x text-dark"></i>
                        </div>
                        <h5 class="card-title fw-bold">Cuentas por Cobrar</h5>
                        <p class="card-text">Gestiona las cuentas por cobrar de la empresa</p>
                    </div>
                </div>
            </a>
        </div>
        <?php endif; ?>

        <?php if ($permiso->tienePermiso($_SESSION['usuario']['id_rol'], 'productos_servicios')): ?>
        <div class="col-md-4 col-lg-3">
            <a href="index.php?c=ProductoServicioControlador&m=index" style="text-decoration: none; color: inherit;">
                <div class="card step-card h-100">
                    <div class="card-body text-center p-4">
                        <div class="step-icon mx-auto mb-3">
                            <i class="fas fa-box-open fa-3x text-dark"></i>
                        </div>
                        <h5 class="card-title fw-bold">Productos y Servicios</h5>
                        <p class="card-text">Gestiona los productos y servicios de la empresa</p>
                    </div>
                </div>
            </a>
        </div>
        <?php endif; ?>

        <?php if ($permiso->tienePermiso($_SESSION['usuario']['id_rol'], 'materia_prima')): ?>
        <div class="col-md-4 col-lg-3">
            <a href="index.php?c=MateriaPrimaControlador&m=index" style="text-decoration: none; color: inherit;">
                <div class="card step-card h-100">
                    <div class="card-body text-center p-4">
                        <div class="step-icon mx-auto mb-3">
                            <i class="fas fa-industry fa-3x text-dark"></i>
                        </div>
                        <h5 class="card-title fw-bold">Materia Prima</h5>
                        <p class="card-text">Gestiona la materia prima de la empresa</p>
                    </div>
                </div>
            </a>
        </div>
        <?php endif; ?>
        
        <?php if ($permiso->tienePermiso($_SESSION['usuario']['id_rol'], 'proveedores')): ?>
        <div class="col-md-4 col-lg-3">
            <a href="index.php?c=ProveedorControlador&m=index" style="text-decoration: none; color: inherit;">
                <div class="card step-card h-100">
                    <div class="card-body text-center p-4">
                        <div class="step-icon mx-auto mb-3">
                            <i class="fas fa-truck fa-3x text-dark"></i>
                        </div>
                        <h5 class="card-title fw-bold">Proveedores</h5>
                        <p class="card-text">Gestiona los proveedores de la empresa</p>
                    </div>
                </div>
            </a>
        </div>
        <?php endif; ?>
        
        <?php if ($permiso->tienePermiso($_SESSION['usuario']['id_rol'], 'facturas_compra')): ?>
        <div class="col-md-4 col-lg-3">
            <a href="index.php?c=FacturaCompraControlador&m=index" style="text-decoration: none; color: inherit;">
                <div class="card step-card h-100">
                    <div class="card-body text-center p-4">
                        <div class="step-icon mx-auto mb-3">
                            <i class="fas fa-file-invoice-dollar fa-3x text-dark"></i>
                        </div>
                        <h5 class="card-title fw-bold">Factura de Compra</h5>
                        <p class="card-text">Gestiona facturas de compra de tu empresa</p>
                    </div>
                </div>
            </a>
        </div>
        <?php endif; ?>
    </div>   
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        
        if (sidebarToggle && sidebar) {
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('active');
                mainContent.classList.toggle('sidebar-active');
            });
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
</body>
</html>