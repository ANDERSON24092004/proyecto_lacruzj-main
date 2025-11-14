<?php
require_once 'modelo/Usuario.php';

function isAjaxRequest() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

function sendJsonResponse($success, $message, $details = '', $data = []) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'details' => $details,
        'data' => $data
    ]);
    exit;
}

$usuario = new Usuario();

switch ($metodo) {
    case 'index':    
        require 'vista/usuario/index.php';
        break;

    case 'listar':
        if (!isAjaxRequest()) {
            sendJsonResponse(false, "Acceso no permitido");
        }
        
        try {
            $usuarios = $usuario->listar();
            sendJsonResponse(true, "Datos cargados", "", $usuarios);
        } catch (Exception $e) {
            sendJsonResponse(false, "Error al cargar usuarios", $e->getMessage());
        }
        break;

    case 'crear':
        if (!isAjaxRequest()) {
            sendJsonResponse(false, "Acceso no permitido");
        }
        
        $roles = $usuario->listarRol();
        sendJsonResponse(true, "Roles cargados", "", $roles);
        break;

    case 'guardar':
        if (!isAjaxRequest()) {
            sendJsonResponse(false, "Acceso no permitido");
        }
        
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
                sendJsonResponse(true, "Usuario registrado correctamente", "El usuario ha sido registrado exitosamente en el sistema");
            }
        } catch (Exception $e) {
            error_log("Error al registrar usuario: " . $e->getMessage());
            sendJsonResponse(false, "Error al registrar el usuario", $e->getMessage());
        }
        break;
            
    case 'editar':
        $cedula = $_GET['cedula'] ?? null;
        if (!$cedula) {
            sendJsonResponse(false, "La cédula no es proporcionada");
        }
        
        $usuarioData = $usuario->buscarPorId($cedula);

        if (!$usuarioData) {
            sendJsonResponse(false, "Usuario no encontrado");
        }
           
        sendJsonResponse(true, "Datos del usuario", "", [
            'cedula' => $usuarioData['cedula'],
            'id_rol' => $usuarioData['id_rol'],
            'nombre' => $usuarioData['username'],
            'telefono' => $usuarioData['telefono'],
            'correo' => $usuarioData['correo']
        ]);
        break;  
           
    case 'actualizar':
        if (!isAjaxRequest()) {
            sendJsonResponse(false, "Acceso no permitido");
        }
        
        $cedula = $_POST['cedula'] ?? null;
        $id_rol = $_POST['id_rol'] ?? '';
        $nombre = $_POST['nombre'] ?? '';
        $telefono = $_POST['telefono'] ?? '';
        $correo = $_POST['correo'] ?? '';
        $clave = $_POST['clave'] ?? '';

        try {
            if ($usuario->actualizar($cedula, $id_rol, $nombre, $telefono, $correo, $clave)) {
                sendJsonResponse(true, "Usuario actualizado correctamente", "Los cambios del usuario " . htmlspecialchars($nombre) . " han sido guardados exitosamente");
            }
        } catch (Exception $e) {
            sendJsonResponse(false, "Error al actualizar el usuario", $e->getMessage());
        }
        break;

    case 'eliminar':
        if (!isAjaxRequest()) {
            sendJsonResponse(false, "Acceso no permitido");
        }
        
        $cedula = $_POST['cedula'] ?? null;
        
        try {
            if ($usuario->eliminar($cedula)) {
                $usuarioData = $usuario->buscarPorId($cedula);
                $nombreUsuario = $usuarioData ? $usuarioData['username'] : 'Usuario';
                
                sendJsonResponse(true, "Usuario eliminado correctamente", "El usuario " . htmlspecialchars($nombreUsuario) . " ha sido eliminado del sistema");
            }
        } catch (Exception $e) {
            sendJsonResponse(false, "Error al eliminar el usuario", $e->getMessage());
        }
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

    case 'perfil':
        $cedulaUsuario = $_SESSION['usuario']['cedula'] ?? null;
        if (!$cedulaUsuario) {
            $_SESSION['tipo_mensaje'] = "error";
            $_SESSION['mensaje'] = "Error al cargar el perfil";
            $_SESSION['mensaje_detalle'] = "No se pudo identificar al usuario";
            header("Location: index.php?c=loginControlador&m=home");
            exit;
        }
        
        $usuarioData = $usuario->buscarPorId($cedulaUsuario);
        if (!$usuarioData) {
            $_SESSION['tipo_mensaje'] = "error";
            $_SESSION['mensaje'] = "Usuario no encontrado";
            $_SESSION['mensaje_detalle'] = "No se encontraron los datos del usuario";
            header("Location: index.php?c=loginControlador&m=home");
            exit;
        }
        
        $roles = $usuario->listarRol();
        require 'vista/usuario/perfil.php';
        break;

    case 'actualizarPerfil':
        
        $cedula = $_SESSION['usuario']['cedula'] ?? null;
        $id_rol = $_POST['id_rol'] ?? '';
        $nombre = $_POST['nombre'] ?? '';
        $telefono = $_POST['telefono'] ?? '';
        $correo = $_POST['correo'] ?? '';
        $clave_actual = $_POST['clave_actual'] ?? '';
        $clave_nueva = $_POST['clave_nueva'] ?? '';
        $confirmar_clave = $_POST['confirmar_clave'] ?? '';

        try {
            if (!empty($clave_nueva)) {
                $usuarioData = $usuario->buscarPorId($cedula);
                if (!$usuarioData || !password_verify($clave_actual, $usuarioData['clave'])) {
                    throw new Exception("La contraseña actual es incorrecta");
                }
                
                if ($clave_nueva !== $confirmar_clave) {
                    throw new Exception("Las contraseñas nuevas no coinciden");
                }
                
                $clave = $clave_nueva;
            } else {
                $clave = null;
            }

            if ($usuario->actualizar($cedula, $id_rol, $nombre, $telefono, $correo, $clave)) {
                $_SESSION['usuario']['username'] = $nombre;
                $_SESSION['usuario']['id_rol'] = $id_rol;
                
                sendJsonResponse(true, "Perfil actualizado correctamente", "Tus datos han sido actualizados exitosamente");
            }
        } catch (Exception $e) {
            sendJsonResponse(false, "Error al actualizar el perfil", $e->getMessage());
        }
        break;

    default:
        require_once 'vista/usuario/index.php';
        break;
}
?>