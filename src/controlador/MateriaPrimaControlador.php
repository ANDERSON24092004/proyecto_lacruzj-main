<?php

use src\modelo\materiaPrimaModelo;
use src\modelo\unidadMedidaModelo;
require_once 'src/controlador/verificar_sesion.php';

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

$materia = new materiaPrimaModelo();
$unidadMedida = new unidadMedidaModelo();

switch ($metodo) {
    case 'index':
        require 'src/vista/materiaPrima/index.php';
    break;

    case 'listar':
    if (isAjaxRequest()) {
        try {
            $materias = $materia->listar();
            sendJsonResponse(true, "Datos cargados", "", $materias);
        } catch (Exception $e) {
            sendJsonResponse(false, "Error al cargar los datos", $e->getMessage());
        }
    }
    break;

    case 'obtenerMateria':
        if (isAjaxRequest() && isset($_GET['id_materia'])) {
            $id = $_GET['id_materia'];
            $materiaData = $materia->buscarPorId($id);
            if ($materiaData) {
                header('Content-Type: application/json');
                echo json_encode($materiaData);
            } else {
                header('Content-Type: application/json');
                echo json_encode(null);
            }
            exit;
        }
    break;

    case 'crear':
        if (isAjaxRequest()) {
            try {
                $materia->setNombre(trim($_POST['nombre']));
                $materia->setIdUnidadMedida($_POST['id_unidad_medida']);
                $materia->setStock($_POST['stock']);
                $materia->setCosto($_POST['costo']);
                
                if ($materia->crear()) {
                    sendJsonResponse(true, "Materia prima registrada exitosamente", "Se registró la materia prima " . $_POST['nombre']);
                }
            } catch (Exception $e) {
                sendJsonResponse(false, "Error en el registro", $e->getMessage());
            }
            header("Location: index.php?c=MateriaPrimaControlador&m=index");
            exit;
        }
    break;

    case 'actualizar':
        if (isAjaxRequest()) {
            try {
                $materia->setIdMateria($_POST['id_materia']);
                $materia->setNombre(trim($_POST['nombre']));
                $materia->setIdUnidadMedida($_POST['id_unidad_medida']);
                $materia->setStock($_POST['stock']);
                $materia->setCosto($_POST['costo']);
                
                if ($materia->actualizar()) {
                    sendJsonResponse(true, "Materia prima editada exitosamente", "Se editó la meteria prima de nombre:  " . $_POST['nombre']);
                }
            } catch (Exception $e) {
                sendJsonResponse(false, "Error en la actualización", $e->getMessage());
            }
        }
    break;

    case 'eliminar':
        if (isAjaxRequest()) {
            $id_materia = $_POST['id_materia'] ?? null;
            
            if (!$id_materia) {
                sendJsonResponse(false, "Error en la eliminación", "ID de la materia no proporcionado");
            }

            try {
                $materia->setIdMateria($id_materia);
                
                if ($materia->eliminar()) {
                    sendJsonResponse(true, "Materia prima eliminada exitosamente");
                }
            } catch (Exception $e) {
                sendJsonResponse(false, "Error en la eliminación   ", $e->getMessage());
            }
        }
    break;

    case 'crearDesdeFactura':
        if (!isAjaxRequest()) {
            header("HTTP/1.0 403 Forbidden");
            exit;
        }
       
        header('Content-Type: application/json');
        
        try {
            $nombre = trim($_POST['nombre']);
            $id_unidad_medida = $_POST['id_unidad_medida'];
            $costo = floatval($_POST['costo']);
            
            $nuevaMateria = $materia->crearDesdeFactura($nombre, $id_unidad_medida, $costo);
            
            if ($nuevaMateria) {
                echo json_encode([
                    'success' => true,
                    'materia_prima' => $nuevaMateria
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al crear la materia prima en la base de datos'
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        exit;
    break;

    default:
        $materias = $materia->listar();
        require_once 'src/vista/materiaPrima/index.php';
    break;
}
?>