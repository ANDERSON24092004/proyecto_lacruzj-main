<?php
require_once 'modelo/UnidadMedidaModelo.php';

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

$unidadMedida = new UnidadMedida();
$activeTab = $_GET['tab'] ?? $_POST['tab'] ?? 'unidades';

switch ($metodo) {
    case 'index':
        $unidades = $unidadMedida->listar();
        require 'vista/configuracion/index.php';
        break;
        
    case 'crear':
        require 'vista/configuracion/index.php';
        break;
        
    case 'listarAjax':
        if (isAjaxRequest()) {
            try {
                $unidades = $unidadMedida->listar();
                sendJsonResponse(true, "Datos cargados", "", $unidades);
            } catch (Exception $e) {
                sendJsonResponse(false, "Error al cargar unidades de medida", $e->getMessage());
            }
        }
        break;

    case 'obtenerUnidadAjax':
        if (isAjaxRequest() && isset($_GET['id_unidad_medida'])) {
            $id = $_GET['id_unidad_medida'];
            $unidadData = $unidadMedida->buscarPorId($id);
            if ($unidadData) {
                header('Content-Type: application/json');
                echo json_encode($unidadData);
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
                    sendJsonResponse(false, "Error en el registro", "El nombre de la unidad es obligatorio");
                }

                $nombre = trim($_POST['nombre']);
                
                if ($unidadMedida->existeNombre($nombre)) {
                    sendJsonResponse(false, "Error en el registro", "Ya existe una unidad con este nombre");
                }

                $unidadMedida->setNombre($nombre);

                if ($unidadMedida->registrar()) {
                    sendJsonResponse(true, "Unidad registrada correctamente", "Se registró la unidad: " . $nombre);
                } else {
                    sendJsonResponse(false, "Error en el registro", "No se pudo registrar la unidad");
                }
            } catch (Exception $e) {
                sendJsonResponse(false, "Error en el registro", $e->getMessage());
            }
        }
        break;

    case 'actualizarAjax':
        if (isAjaxRequest()) {
            try {
                $id = $_POST['id_unidad_medida'] ?? null;
                if (!$id) {
                    sendJsonResponse(false, "Error en la actualización", "ID no proporcionado");
                }

                if (empty($_POST['nombre'])) {
                    sendJsonResponse(false, "Error en la actualización", "El nombre de la unidad es obligatorio");
                }

                $nombre = trim($_POST['nombre']);
                
                if ($unidadMedida->existeNombre($nombre, $id)) {
                    sendJsonResponse(false, "Error en la actualización", "Ya existe una unidad con este nombre");
                }

                $unidadMedida->setIdUnidadMedida($id);
                $unidadMedida->setNombre($nombre);

                if ($unidadMedida->actualizar()) {
                    sendJsonResponse(true, "Unidad actualizada correctamente", "Se actualizó la unidad: " . $nombre);
                } else {
                    sendJsonResponse(false, "Error en la actualización", "No se pudo actualizar la unidad");
                }
            } catch (Exception $e) {
                sendJsonResponse(false, "Error en la actualización", $e->getMessage());
            }
        }
        break;

    case 'eliminarAjax':
        if (isAjaxRequest()) {
            $id_unidad_medida = $_POST['id_unidad_medida'] ?? null;
            
            if (!$id_unidad_medida) {
                sendJsonResponse(false, "Error en la eliminación", "ID de unidad de medida no proporcionado");
            }

            try {
                $unidadMedida->setIdUnidadMedida($id_unidad_medida);
                
                if ($unidadMedida->eliminar()) {
                    sendJsonResponse(true, "Unidad eliminada correctamente", "Se eliminó la unidad con ID " . $id_unidad_medida);
                } else {
                    sendJsonResponse(false, "Error en la eliminación", "No se pudo eliminar la unidad");
                }
            } catch (Exception $e) {
                sendJsonResponse(false, "Error en la eliminación", $e->getMessage());
            }
        }
    break;
}
?>