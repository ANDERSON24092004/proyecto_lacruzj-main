<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Sistema de Inventario</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link" href="index.php?c=producto&m=index">Productos</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="index.php?c=categoria&m=index">Categorías</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="index.php?c=ProveedorControlador&m=index">Proveedores</a>
        </li>
      </ul>
      <ul class="navbar-nav">
        <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="usuarioDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        Usuario
    </a>
    <ul class="dropdown-menu" aria-labelledby="usuarioDropdown">
        <li><a class="dropdown-item" href="index.php?c=usuario&m=index">Gestionar Usuarios</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item text-danger" href="index.php?c=login&m=logout">Cerrar sesión</a></li>
    </ul>
</li>

      </ul>
    </div>
  </div>
</nav>
