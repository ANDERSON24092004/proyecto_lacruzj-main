

<?php if (!isset($_SESSION)) session_start(); ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php?c=producto&m=index">Inventario</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuOpciones" aria-controls="menuOpciones" aria-expanded="false" aria-label="Menú">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="menuOpciones">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="index.php?c=producto&m=index">Productos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?c=categoria&m=index">Categorías</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?c=proveedor&m=index">Proveedores</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?c=usuario&m=index">Usuarios</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?c=venta&m=index">Ventas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="vista/configurar_tasa.php">Tasa de Cambio</a>
                </li>
            </ul>

            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <span class="navbar-text text-white me-3">
                        Usuario: <?= $_SESSION['usuario'] ?? 'Invitado' ?>
                    </span>
                </li>
                <li class="nav-item">
                    <a class="btn btn-outline-light btn-sm" href="index.php?c=login&m=logout">Cerrar sesión</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

