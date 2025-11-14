<?php
require_once 'modelo/Usuario.php';

switch ($metodo) {
    case 'login':
        if (isset($_SESSION['usuario'])) {
            header("Location: index.php?c=login&m=home");
            exit;
        }
        require 'vista/login.php';
        break;

    case 'validar':
        if (isset($_SESSION['usuario'])) {
            header("Location: index.php?c=login&m=home");
            exit;
        }
        
        $usuario = $_POST['usuario'] ?? '';
        $clave   = $_POST['clave'] ?? '';

        if (empty($usuario) || empty($clave)) {
            $error = "Usuario y clave son obligatorios.";
            require 'vista/login.php';
            exit;
        }

        $u = new Usuario();
        $usuarioEncontrado = $u->buscarPorCredenciales($usuario);

        if ($usuarioEncontrado && password_verify($clave, $usuarioEncontrado['clave'])) {
            $_SESSION['usuario'] = [
                'cedula' => $usuarioEncontrado['cedula'],
                'username' => $usuarioEncontrado['username'],
                'id_rol' => $usuarioEncontrado['id_rol']
            ];
            
            header("Location: index.php?c=login&m=home");
            exit;
        } else {
            $error = "Usuario o clave incorrectos.";
            require 'vista/login.php';
        }
        break;

    case 'logout':
        session_unset();
        session_destroy();
        header("Location: index.php?c=login&m=login");
        exit;
        break;

    case 'home':
        if (!isset($_SESSION['usuario'])) {
            header("Location: index.php?c=login&m=login");
            exit;
        }
        require 'vista/parcial/home.php';
        break;
        
    default:
        http_response_code(404);
        echo "Acción no válida.";
        break;
}