<?php 
require_once 'modelo/Permiso.php';
$permiso = new Permiso();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>J.Lacruz</title>
    <link rel="stylesheet" href="/proyecto-lacruz-j/assets/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/proyecto-lacruz-j/assets/css/header.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/simple-line-icons/2.4.1/css/simple-line-icons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

</head>
<body>
    <nav class="navbar navbar-expand-lg noselec">
        <div class="container-fluid">
            <div class="d-flex align-items-center">
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <img src="assets/images/logo2.png" class="navbar-logo" alt="logo">
                <a class="navbar-brand" href="index.php?c=loginControlador&m=home">J.Lacruz</a>
            </div>
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" 
                       role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="user-avatar">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <span class="user-name ms-2">
                            <?php echo htmlspecialchars($_SESSION['usuario']['username'] ?? 'Usuario'); ?>
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li>
                            <a class="dropdown-item" href="index.php?c=usuarioControlador&m=perfil">
                                <i class="fas fa-user me-2"></i>
                                Mi Perfil
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

<div class="sidebar noselec" id="sidebar">
    <nav>
        <ul class="sidebar-menu">
            <li>
                <a href="index.php?c=loginControlador&m=home" title="Inicio">
                    <i class="fas fa-home"></i>
                    <span>Inicio</span>
                </a>
            </li>
            
            <?php if ($permiso->tienePermiso($_SESSION['usuario']['id_rol'], 'clientes')): ?>
            <li>
                <a href="index.php?c=ClienteControlador&m=index" title="Clientes">
                    <i class="fas fa-users"></i>
                    <span>Clientes</span>
                </a>
            </li>
            <?php endif; ?>
            
            <?php if ($permiso->tienePermiso($_SESSION['usuario']['id_rol'], 'presupuestos')): ?>
            <li>
                <a href="index.php?c=PresupuestoControlador&m=index" title="Presupuestos">
                    <i class="fas fa-calculator"></i>
                    <span>Presupuestos</span>
                </a>
            </li>
            <?php endif; ?>
            
            <?php if ($permiso->tienePermiso($_SESSION['usuario']['id_rol'], 'facturacion')): ?>
            <li>
                <a href="index.php?c=FacturaControlador&m=index" title="Facturas">
                    <i class="fas fa-file-invoice"></i>
                    <span>Facturación</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if ($permiso->tienePermiso($_SESSION['usuario']['id_rol'], 'cuentas_cobrar')): ?>
            <li>
                <a href="index.php?c=CuentasCobrarControlador&m=index" title="Cuentas por Cobrar">
                    <i class="fas fa-file-invoice"></i>
                    <span>Cuentas por Cobrar</span>
                </a>
            </li>
            <?php endif; ?>
            
            <?php if ($permiso->tienePermiso($_SESSION['usuario']['id_rol'], 'productos_servicios')): ?>
            <li>
                <a href="index.php?c=ProductoServicioControlador&m=index" title="Productos">
                    <i class="fas fa-box-open"></i>
                    <span>Productos y Servicios</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if ($permiso->tienePermiso($_SESSION['usuario']['id_rol'], 'materia_prima')): ?>
            <li>
                <a href="index.php?c=MateriaPrimaControlador&m=index" title="Materia Prima">
                    <i class="fas fa-industry"></i>
                    <span>Materias Primas</span>
                </a>
            </li>
            <?php endif; ?>
            
            <?php if ($permiso->tienePermiso($_SESSION['usuario']['id_rol'], 'proveedores')): ?>
            <li>
                <a href="index.php?c=ProveedorControlador&m=index" title="Proveedores">
                    <i class="fas fa-truck"></i>
                    <span>Proveedores</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if ($permiso->tienePermiso($_SESSION['usuario']['id_rol'], 'facturas_compra')): ?>
            <li>
                <a href="index.php?c=FacturaCompraControlador&m=index" title="Facturas de Compra">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <span>Factura de Compra</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if ($permiso->tienePermiso($_SESSION['usuario']['id_rol'], 'usuarios')): ?>
            <li>
                <a href="index.php?c=usuarioControlador&m=index" title="Usuarios">
                    <i class="fas fa-user-cog"></i>
                    <span>Usuarios</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if ($permiso->tienePermiso($_SESSION['usuario']['id_rol'], 'reportes')): ?>
            <li class="nav-item dropdown">
                <a href="index.php?c=ReporteControlador&m=index" title="Reportes">
                    <i class="fas fa-chart-bar"></i>
                    <span>Reportes</span>
                </a>
            </li>
            <?php endif; ?>
            
            <?php if ($permiso->tienePermiso($_SESSION['usuario']['id_rol'], 'configuracion')): ?>
            <li>
                <a href="index.php?c=ConfigControlador&m=index" title="Configuración">
                    <i class="fas fa-cogs"></i>
                    <span>Configuración</span>
                </a>
            </li>
            <?php endif; ?> 

             <li>
                <a href="assets/manual_de_usuario.pdf" title="Ayuda" target="blank">
                    <i class="fas fa-info-circle"></i>
                    <span>Ayuda</span>
                </a>
            </li>
            
            <li class="sidebar-divider"></li>
            
            <li>
                <a class="logout-btn" href="index.php?c=loginControlador&m=logout">
                    <i class="fas fa-sign-out-alt"></i> 
                    <span>Cerrar sesión</span>
                </a>
            </li>         
        </ul>
    </nav>
</div>
<div class="main-content" id="mainContent">