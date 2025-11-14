<?php
require_once 'modelo/IvaModelo.php';

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

$Iva = new Iva();
$activeTab = $_GET['tab'] ?? $_POST['tab'] ?? 'iva';

switch ($metodo) {
    case 'index':
        $iva = $Iva->listar();
        require 'vista/configuracion/index.php';
        break;
        
    case 'crear':
        require 'vista/configuracion/index.php';
        break;
        
    case 'listarAjax':
        if (isAjaxRequest()) {
            try {
                $ivas = $Iva->listar();
                sendJsonResponse(true, "Datos cargados", "", $ivas);
            } catch (Exception $e) {
                sendJsonResponse(false, "Error al cargar IVAs", $e->getMessage());
            }
        }
        break;

    case 'obtenerIvaAjax':
        if (isAjaxRequest() && isset($_GET['id_iva'])) {
            $id = $_GET['id_iva'];
            $ivaData = $Iva->buscarPorId($id);
            if ($ivaData) {
                header('Content-Type: application/json');
                echo json_encode($ivaData);
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
                if (empty($_POST['porcentaje'])) {
                    sendJsonResponse(false, "Error en el registro", "El porcentaje de IVA es obligatorio");
                }

                $porcentaje = trim($_POST['porcentaje']);
          
                if (!is_numeric($porcentaje) || $porcentaje < 0 || $porcentaje > 100) {
                    sendJsonResponse(false, "Error en el registro", "El porcentaje debe ser un número entre 0 y 100");
                }

                $Iva->setPorcentaje($porcentaje);

                if ($Iva->registrar()) {
                    sendJsonResponse(true, "IVA registrado correctamente", "Se registró el IVA: " . $porcentaje . "%");
                } else {
                    sendJsonResponse(false, "Error en el registro", "No se pudo registrar el IVA");
                }
            } catch (Exception $e) {
                sendJsonResponse(false, "Error en el registro", $e->getMessage());
            }
        }
        break;

    case 'actualizarAjax':
        if (isAjaxRequest()) {
            try {
                $id = $_POST['id_iva'] ?? null;
                if (!$id) {
                    sendJsonResponse(false, "Error en la actualización", "ID no proporcionado");
                }

                if (empty($_POST['porcentaje'])) {
                    sendJsonResponse(false, "Error en la actualización", "El porcentaje de IVA es obligatorio");
                }

                $porcentaje = trim($_POST['porcentaje']);
                
                if (!is_numeric($porcentaje) || $porcentaje < 0 || $porcentaje > 100) {
                    sendJsonResponse(false, "Error en la actualización", "El porcentaje debe ser un número entre 0 y 100");
                }

                $Iva->setIdIva($id);
                $Iva->setPorcentaje($porcentaje);

                if ($Iva->actualizar()) {
                    sendJsonResponse(true, "IVA actualizado correctamente", "Se actualizó el IVA: " . $porcentaje . "%");
                } else {
                    sendJsonResponse(false, "Error en la actualización", "No se pudo actualizar el IVA");
                }
            } catch (Exception $e) {
                sendJsonResponse(false, "Error en la actualización", $e->getMessage());
            }
        }
        break;

    case 'eliminarAjax':
        if (isAjaxRequest()) {
            $id_iva = $_POST['id_iva'] ?? null;
            
            if (!$id_iva) {
                sendJsonResponse(false, "Error en la eliminación", "ID de IVA no proporcionado");
            }

            try {
                $Iva->setIdIva($id_iva);
                
                if ($Iva->eliminar()) {
                    sendJsonResponse(true, "IVA eliminado correctamente", "Se eliminó el IVA con ID " . $id_iva);
                } else {
                    sendJsonResponse(false, "Error en la eliminación", "No se pudo eliminar el IVA");
                }
            } catch (Exception $e) {
                sendJsonResponse(false, "Error en la eliminación", $e->getMessage());
            }
        }
        break;
}
?>