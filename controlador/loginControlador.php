<?php
require_once 'modelo/Usuario.php';

switch ($metodo) {
    case 'login':
        if (isset($_SESSION['usuario'])) {
            header("Location: index.php?c=loginControlador&m=home");
            exit;
        }
        require 'vista/login.php';
        break;

    case 'crearUsuario':
        if (isset($_SESSION['usuario'])) {
            header("Location: index.php?c=loginControlador&m=home");
            exit;
        }
        
        try {
            $cedula = $_POST['cedula'] ?? '';
            $nombre = $_POST['nombre'] ?? '';
            $telefono = $_POST['telefono'] ?? '';
            $correo = $_POST['correo'] ?? '';
            $clave = $_POST['clave'] ?? '';

            if (empty($cedula) || empty($nombre) || empty($telefono) || empty($correo) || empty($clave)) {
                $_SESSION['registro_error'] = "Todos los campos son obligatorios.";
                $_SESSION['form_data'] = $_POST;
                header("Location: index.php?c=loginControlador&m=login");
                exit;
            }

            if (!preg_match('/^\d{6,10}$/', $cedula)) {
                $_SESSION['registro_error'] = "La cédula debe contener solo números (6-10 dígitos).";
                $_SESSION['form_data'] = $_POST;
                header("Location: index.php?c=loginControlador&m=login");
                exit;
            }

            if (!preg_match('/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{2,50}$/', $nombre)) {
                $_SESSION['registro_error'] = "El nombre solo puede contener letras y espacios (2-50 caracteres).";
                $_SESSION['form_data'] = $_POST;
                header("Location: index.php?c=loginControlador&m=login");
                exit;
            }

            if (!preg_match('/^\d{4}-\d{7}$/', $telefono)) {
                $_SESSION['registro_error'] = "El teléfono debe tener el formato: xxxx-xxxxxxx (11 dígitos).";
                $_SESSION['form_data'] = $_POST;
                header("Location: index.php?c=loginControlador&m=login");
                exit;
            }

            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['registro_error'] = "Ingrese una dirección de correo electrónico válida.";
                $_SESSION['form_data'] = $_POST;
                header("Location: index.php?c=loginControlador&m=login");
                exit;
            }

            if (strlen($clave) < 6) {
                $_SESSION['registro_error'] = "La contraseña debe tener al menos 6 caracteres.";
                $_SESSION['form_data'] = $_POST;
                header("Location: index.php?c=loginControlador&m=login");
                exit;
            }

            $claveHash = password_hash($clave, PASSWORD_DEFAULT);
            $id_rol_oficina = 2;

            $usuario = new Usuario();
            $resultado = $usuario->registrar($cedula, $id_rol_oficina, $nombre, $telefono, $correo, $claveHash);

            if ($resultado) {
                $_SESSION['registro_success'] = "Usuario registrado exitosamente. Ahora puede iniciar sesión.";
                unset($_SESSION['form_data']);
                header("Location: index.php?c=loginControlador&m=login");
                exit;
            }

        } catch (Exception $e) {
            $_SESSION['registro_error'] = $e->getMessage();
            $_SESSION['form_data'] = $_POST;
            header("Location: index.php?c=loginControlador&m=login");
            exit;
        }
        break;

    case 'validar':
        if (isset($_SESSION['usuario'])) {
            header("Location: index.php?c=loginControlador&m=home");
            exit;
        }
        
        $usuario = $_POST['usuario'] ?? '';
        $clave   = $_POST['clave'] ?? '';

        if (empty($usuario) || empty($clave)) {
            $_SESSION['login_error'] = "Por favor, ingrese tanto el usuario como la contraseña.";
            header("Location: index.php?c=loginControlador&m=login");
            exit;
        }

        $u = new Usuario();
        $usuarioEncontrado = $u->buscarPorCredenciales($usuario);

        if (!$usuarioEncontrado) {
            $_SESSION['login_error'] = "El usuario no existe en el sistema.";
            header("Location: index.php?c=loginControlador&m=login");
            exit;
        }

        if (!password_verify($clave, $usuarioEncontrado['clave'])) {
            $_SESSION['login_error'] = "La contraseña es incorrecta. Por favor, intente nuevamente.";
            header("Location: index.php?c=loginControlador&m=login");
            exit;
        }

        $_SESSION['usuario'] = [
            'cedula' => $usuarioEncontrado['cedula'],
            'username' => $usuarioEncontrado['username'],
            'id_rol' => $usuarioEncontrado['id_rol']
        ];
        
        $_SESSION['login_success'] = "¡Bienvenido " . $usuarioEncontrado['username'] . "!";
        
        header("Location: index.php?c=loginControlador&m=home");
        exit;
        break;

    case 'logout':
        session_unset();
        session_destroy();
        header("Location: index.php?c=loginControlador&m=login");
        exit;
        break;

    case 'home':
        if (!isset($_SESSION['usuario'])) {
            header("Location: index.php?c=loginControlador&m=login");
            exit;
        }
        require 'vista/parcial/home.php';
        break;
        
    default:
        http_response_code(404);
        echo "Acción no válida.";
        break;
}