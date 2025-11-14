<?php
require_once 'modelo/PresentacionModelo.php';

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

$presentacion = new Presentacion();
$activeTab = $_GET['tab'] ?? $_POST['tab'] ?? 'presentacion';

switch ($metodo) {
    case 'index':
        $presentaciones = $presentacion->listar();
        require 'vista/configuracion/index.php';
        break;
        
    case 'crear':
        require 'vista/configuracion/index.php';
        break;
        
    case 'listarAjax':
        if (isAjaxRequest()) {
            try {
                $presentaciones = $presentacion->listar();
                sendJsonResponse(true, "Datos cargados", "", $presentaciones);
            } catch (Exception $e) {
                sendJsonResponse(false, "Error al cargar las presentaciones", $e->getMessage());
            }
        }
        break;

    case 'obtenerPresentacionAjax':
        if (isAjaxRequest() && isset($_GET['id_pres'])) {
            $id = $_GET['id_pres'];
            $presentacionData = $presentacion->buscarPorId($id);
            if ($presentacionData) {
                header('Content-Type: application/json');
                echo json_encode($presentacionData);
            } else {
                header('Content-Type: application/json');
                echo json_encode(null);
            }
            exit;
        }
        break;

    case 'guardarAjax':
        if (isAjaxRequest()) {
            try {
                if (empty($_POST['nombre'])) {
                    sendJsonResponse(false, "Error en el registro", "El nombre de la presentación es obligatorio");
                }

                $nombre = trim($_POST['nombre']);
                $presentacion->setNombre($nombre);

                if ($presentacion->registrar()) {
                    sendJsonResponse(true, "Presentación registrada correctamente", "Se registró la presentación: " . $nombre);
                } else {
                    sendJsonResponse(false, "Error en el registro", "No se pudo registrar la presentación");
                }
            } catch (Exception $e) {
                sendJsonResponse(false, "Error en el registro", $e->getMessage());
            }
        }
        break;

    case 'actualizarAjax':
        if (isAjaxRequest()) {
            try {
                $id = $_POST['id_pres'] ?? null;
                if (!$id) {
                    sendJsonResponse(false, "Error en la actualización", "ID no proporcionado");
                }

                if (empty($_POST['nombre'])) {
                    sendJsonResponse(false, "Error en la actualización", "El nombre de la presentación es obligatoria");
                }

                $nombre = trim($_POST['nombre']);
                $presentacion->setIdPresentacion($id);
                $presentacion->setNombre($nombre);

                if ($presentacion->actualizar()) {
                    sendJsonResponse(true, "Presentación actualizada correctamente", "Se actualizó la presentación: " . $nombre);
                } else {
                    sendJsonResponse(false, "Error en la actualización", "No se pudo actualizar la presentación");
                }
            } catch (Exception $e) {
                sendJsonResponse(false, "Error en la actualización", $e->getMessage());
            }
        }
        break;

    case 'eliminarAjax':
        if (isAjaxRequest()) {
            $id_pres = $_POST['id_pres'] ?? null;
            
            if (!$id_pres) {
                sendJsonResponse(false, "Error en la eliminación", "ID de presentación no proporcionado");
            }

            try {
                if ($presentacion->tieneProductos($id_pres)) {
                    sendJsonResponse(false, "Error en la eliminación", "No se puede eliminar la presentación porque está en uso por algún producto");
                }

                $presentacion->setIdPresentacion($id_pres);
                
                if ($presentacion->eliminar()) {
                    sendJsonResponse(true, "Presentación eliminada correctamente", "Se eliminó la presentación con ID " . $id_pres);
                } else {
                    sendJsonResponse(false, "Error en la eliminación", "No se pudo eliminar la presentación");
                }
            } catch (Exception $e) {
                sendJsonResponse(false, "Error en la eliminación", $e->getMessage());
            }
        }
    break;
}
?>