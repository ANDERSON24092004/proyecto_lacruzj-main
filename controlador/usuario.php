<?php
require_once 'modelo/Usuario.php';

function isAjaxRequest() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

$usuario = new Usuario();

switch ($metodo) {
    case 'index':    
        $usuarios = $usuario->listar();
        $roles = $usuario->listarRol();
        require 'vista/usuario/index.php';
        break;

    case 'crear':
        $roles = $usuario->listarRol();
        require 'vista/usuario/crear.php';
        break;

    case 'guardar':
        error_log("Datos POST recibidos para usuario: " . print_r($_POST, true));
        
        $cedula = $_POST['cedula'] ?? '';
        $id_rol = $_POST['id_rol'] ?? '';
        $nombre = $_POST['nombre'] ?? '';
        $telefono = $_POST['telefono'] ?? '';
        $correo = $_POST['correo'] ?? '';
        $clave = $_POST['clave'] ?? '';

        try {
            $claveHash = password_hash($clave, PASSWORD_DEFAULT);
            
            if ($usuario->registrar($cedula, $id_rol, $nombre, $telefono, $correo, $claveHash)) {
                $_SESSION['tipo_mensaje'] = "success";
                $_SESSION['mensaje'] = "Usuario registrado correctamente";
                $_SESSION['mensaje_detalle'] = "El usuario " . htmlspecialchars($nombre) . " ha sido registrado exitosamente en el sistema";
                header("Location: index.php?c=usuario&m=index");
                exit;
            }
        } catch (Exception $e) {
            error_log("Error al registrar usuario: " . $e->getMessage());
            $_SESSION['tipo_mensaje'] = "error";
            $_SESSION['mensaje'] = "Error al registrar el usuario";
            $_SESSION['mensaje_detalle'] = $e->getMessage();
            header("Location: index.php?c=usuario&m=index");
            exit;
        }
        break;
            
    case 'editar':
        $roles = $usuario->listarRol();
        $cedula = $_GET['cedula'] ?? null;
        if (!$cedula) {
            if (isAjaxRequest()) {
                header('Content-Type: application/json');
                echo json_encode(['error' => true, 'message' => 'La cédula no es proporcionada']);
                exit;
            }
            header("Location: index.php?c=usuario&m=index");
            exit;
        }
        
        $usuarioData = $usuario->buscarPorId($cedula);

        if (!$usuarioData) {
            if (isAjaxRequest()) {
                header('Content-Type: application/json');
                echo json_encode(['error' => true, 'message' => 'Usuario no encontrado']);
                exit;
            }
            $_SESSION['tipo_mensaje'] = "error";
            $_SESSION['mensaje'] = "Usuario no encontrado";
            header("Location: index.php?c=usuario&m=index");
            exit;
        }
           
        if (isAjaxRequest()) {
            header('Content-Type: application/json');
            echo json_encode([
                'cedula' => $usuarioData['cedula'],
                'id_rol' => $usuarioData['id_rol'],
                'nombre' => $usuarioData['username'],
                'telefono' => $usuarioData['telefono'],
                'correo' => $usuarioData['correo']
            ]);
            exit;
        }
        
        $usuarios = $usuario->listar();
        require 'vista/usuario/index.php';
        break;  
           
    case 'actualizar':
        $cedula = $_POST['cedula'] ?? null;
        $id_rol = $_POST['id_rol'] ?? '';
        $nombre = $_POST['nombre'] ?? '';
        $telefono = $_POST['telefono'] ?? '';
        $correo = $_POST['correo'] ?? '';
        $clave = $_POST['clave'] ?? '';

        try {
            if ($usuario->actualizar($cedula, $id_rol, $nombre, $telefono, $correo, $clave)) {
                $_SESSION['tipo_mensaje'] = "success";
                $_SESSION['mensaje'] = "Usuario actualizado correctamente";
                $_SESSION['mensaje_detalle'] = "Los cambios del usuario " . htmlspecialchars($nombre) . " han sido guardados exitosamente";
            }
        } catch (Exception $e) {
            $_SESSION['tipo_mensaje'] = "error";
            $_SESSION['mensaje'] = "Error al actualizar el usuario";
            $_SESSION['mensaje_detalle'] = $e->getMessage();
        }
        header("Location: index.php?c=usuario&m=index");
        exit;
        break;

    case 'eliminar':
        $cedula = $_GET['cedula'] ?? null;
        
        try {
            if ($usuario->eliminar($cedula)) {
                $usuarioData = $usuario->buscarPorId($cedula);
                $nombreUsuario = $usuarioData ? $usuarioData['username'] : 'Usuario';
                
                $_SESSION['tipo_mensaje'] = "success";
                $_SESSION['mensaje'] = "Usuario eliminado correctamente";
                $_SESSION['mensaje_detalle'] = "El usuario " . htmlspecialchars($nombreUsuario) . " ha sido eliminado del sistema";
            }
        } catch (Exception $e) {
            $_SESSION['tipo_mensaje'] = "error";
            $_SESSION['mensaje'] = "Error al eliminar el usuario";
            $_SESSION['mensaje_detalle'] = $e->getMessage();
        }
        header("Location: index.php?c=usuario&m=index");
        exit;
        break;
    
    case 'verificarNombre':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'] ?? '';
            $cedula = $_POST['cedula'] ?? null;
            
            header('Content-Type: application/json');
            
            if (empty($nombre)) {
                echo json_encode(['disponible' => false]);
                exit;
            }
            
            $disponible = $usuario->verificarNombreDisponible($nombre, $cedula);
            echo json_encode(['disponible' => $disponible]);
        }
        exit;
        break;

    default:
        $usuarios = $usuario->listar();
        require_once 'vista/usuario/index.php';
        break;
}
?>