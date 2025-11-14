<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if (!isset($_GET['c']) && !isset($_GET['m'])) {
    header("Location: index.php?c=loginControlador&m=login");
    exit;
}

if (!isset($_SESSION['usuario']) && ($_GET['c'] ?? '') !== 'loginControlador') {
    header("Location: index.php?c=loginControlador&m=login");
    exit;
}

if (isset($_SESSION['usuario'])) {
    require_once 'modelo/Permiso.php';
    $permiso = new Permiso();
    
    $controlador = $_GET['c'] ?? '';
    $modulo = $permiso->getModuloPorControlador($controlador);
    $id_rol = $_SESSION['usuario']['id_rol'];
    
    if ($modulo !== 'loginControlador' && !$permiso->tienePermiso($id_rol, $modulo)) {
        http_response_code(403);
        echo "<h3>Acceso Denegado</h3>";
        echo "<p>No tienes permisos para acceder a esta sección.</p>";
        echo '<a href="index.php?c=loginControlador&m=home" class="btn btn-primary">Volver al Inicio</a>';
        exit;
    }
}

$controlador = $_GET['c'];
$metodo      = $_GET['m'];

$archivo = "controlador/{$controlador}.php";

if (file_exists($archivo)) {
    require_once $archivo;
} else {
    echo "<h3>Error: El controlador <strong>'$controlador'</strong> no existe.</h3>";
    echo "<p>Verifique que el archivo <code>controlador/$controlador.php</code> esté creado y que la URL esté bien escrita.</p>";
    exit;
}
?>