<?php
require_once 'modelo/ProductoServicioModelo.php';
require_once 'modelo/UnidadMedidaModelo.php';
require_once 'modelo/PresentacionModelo.php';
require_once 'modelo/MateriaPrimaModelo.php';
require_once 'controlador/verificar_sesion.php';

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

$productoServicio = new ProductoServicio();
$unidadMedida = new UnidadMedida();
$presentacion = new Presentacion();
$materiaPrima = new MateriaPrima();

switch ($metodo) {
    case 'index':
        $unidades = $unidadMedida->listar();
        $presentaciones = $presentacion->listar();
        require 'vista/producto-servicio/index.php';
        break;

    case 'listar':
        if (isAjaxRequest()) {
            try {
                $productos = $productoServicio->listar();
                sendJsonResponse(true, "Datos cargados", "", $productos);
            } catch (Exception $e) {
                sendJsonResponse(false, "Error al cargar los datos", $e->getMessage());
            }
        }
        break;

    case 'guardar':
        if (isAjaxRequest()) {
            header('Content-Type: application/json');
            try {
                if (empty(trim($_POST['nombre'] ?? ''))) {
                    throw new Exception("El nombre es obligatorio");
                }
                if (empty($_POST['id_unidad_medida'] ?? '')) {
                    throw new Exception("La unidad de medida es obligatoria");
                }
                if (!isset($_POST['costo']) || floatval($_POST['costo']) < 0) {
                    throw new Exception("El costo debe ser mayor o igual a 0");
                }

                $productoServicio->setNombre(trim($_POST['nombre']));
                $productoServicio->setIdUnidadMedida($_POST['id_unidad_medida']);
                $productoServicio->setCosto(floatval($_POST['costo']));
                $productoServicio->setTipo(intval($_POST['tipo']));

                if ($_POST['tipo'] == 1) { 
                    if (!isset($_POST['stock']) || intval($_POST['stock']) < 0) {
                        throw new Exception("El stock debe ser mayor o igual a 0");
                    }
                    
                    if (empty($_POST['presentacion'] ?? '')) {
                        throw new Exception("La presentación es obligatoria para productos");
                    }
                    
                    $productoServicio->setStock(intval($_POST['stock']));
  
                    $costoMayor = isset($_POST['precio_mayor']) ? floatval($_POST['precio_mayor']) : 0;
                    $productoServicio->setPrecioMayor($costoMayor);
                    
                    $productoServicio->setEsFabricado(isset($_POST['es_fabricado']) ? 1 : 0);
                    
                    $presentacionModel = new Presentacion();
                    $id_pres = $presentacionModel->buscarIdPorNombre($_POST['presentacion']);
                    if (!$id_pres) {
                        throw new Exception("Presentación no válida: " . $_POST['presentacion']);
                    }
                    $productoServicio->setIdPres($id_pres);

                    if (isset($_POST['es_fabricado']) && $_POST['es_fabricado'] == 1) {
                        if (isset($_POST['materias_primas_json'])) {
                            $materiasPrimas = json_decode($_POST['materias_primas_json'], true);
                            if (json_last_error() !== JSON_ERROR_NONE) {
                                throw new Exception("Error en el formato de materias primas");
                            }
                            $productoServicio->setMateriasPrimas($materiasPrimas);
                        }
                    }
                } else {
            
                    $productoServicio->setStock(0);
                    $productoServicio->setPrecioMayor(0);
                    $productoServicio->setEsFabricado(0);
                    
                    $presentacionModel = new Presentacion();
                    $id_pres_no_aplica = $presentacionModel->buscarIdPorNombre('No aplica');
                    if (!$id_pres_no_aplica) {
                        throw new Exception("No se encontró la presentación 'No aplica'");
                    }
                    $productoServicio->setIdPres($id_pres_no_aplica);
                }

            
                $id_producto = $productoServicio->crear();
                
                if ($id_producto) {
                    $mensaje = $_POST['tipo'] == 1 ? "Producto registrado correctamente" : "Servicio registrado correctamente";
                    echo json_encode([
                        'success' => true, 
                        'message' => $mensaje,
                        'id_producto' => $id_producto
                    ]);
                } else {
                    throw new Exception("No se pudo crear el registro en la base de datos");
                }

            } catch (Exception $e) {
                http_response_code(400);
                echo json_encode([
                    'success' => false, 
                    'message' => 'Error al registrar: ' . $e->getMessage()
                ]);
            }
            exit;
        }
        break;

    case 'editar':
        if (!isAjaxRequest()) {
            header("HTTP/1.0 403 Forbidden");
            exit;
        }
        
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false, 
                'message' => 'ID no proporcionado'
            ]);
            exit;
        }
        
        try {
            $productoData = $productoServicio->buscarPorId($id);
            if (!$productoData) {
                throw new Exception("Producto/servicio no encontrado");
            }

            error_log("Datos del Producto ID $id - es_fabricado" .$productoData['es_fabricado']);
            error_log("Materias Primas" . print_r($productoData['materias_primas'] ?? [], true));
            
            $productoData['costo'] = isset($productoData['costo']) ? floatval($productoData['costo']) : 0;
            $productoData['precio_mayor'] = isset($productoData['precio_mayor']) ? floatval($productoData['precio_mayor']) : 0;
            $productoData['stock'] = isset($productoData['stock']) ? intval($productoData['stock']) : 0;
            $productoData['tipo'] = isset($productoData['tipo']) ? intval($productoData['tipo']) : 1;
            $productoData['es_fabricado'] = isset($productoData['es_fabricado']) ? intval($productoData['es_fabricado']) : 0;
            
            if (!isset($productoData['materias_primas']) || !is_array($productoData['materias_primas'])) {
                $productoData['materias_primas'] = [];
            }
            error_log("Datos cargados para edición - ID: $id, Materias: " . count($productoData['materias_primas']));

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Datos cargados correctamente',
                'data' => $productoData
            ]);
            exit;
            
        } catch (Exception $e) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al cargar datos: ' . $e->getMessage()
            ]);
            exit;
        }
        break;

    case 'actualizar':
        if (isAjaxRequest()) {
            header('Content-Type: application/json');
            try {
                if (empty($_POST['id_inv'] ?? '')) {
                    throw new Exception("ID del producto/servicio no proporcionado");
                }

                $productoServicio->setIdInv($_POST['id_inv']);
                $productoServicio->setNombre(trim($_POST['nombre']));
                $productoServicio->setIdUnidadMedida($_POST['id_unidad_medida']);
                
                $costo = isset($_POST['costo']) ? floatval($_POST['costo']) : 0;
                $productoServicio->setCosto($costo);
                
                $tipo = isset($_POST['tipo']) ? intval($_POST['tipo']) : 1;
                $productoServicio->setTipo($tipo);

                if ($tipo === 1) { 
                    if (!isset($_POST['stock']) || intval($_POST['stock']) < 0) {
                        throw new Exception("El stock debe ser mayor o igual a 0");
                    }
                    
                    if (empty($_POST['presentacion'] ?? '')) {
                        throw new Exception("La presentación es obligatoria para productos");
                    }
                    
                    $productoServicio->setStock(intval($_POST['stock']));
  
                    $precioMayor = isset($_POST['costo_mayor']) ? floatval($_POST['costo_mayor']) : 0;
                    $productoServicio->setPrecioMayor($precioMayor);
                    
                    $esFabricado = isset($_POST['es_fabricado']) ? 1 : 0;
                    $productoServicio->setEsFabricado($esFabricado);
                    
                    $presentacionModel = new Presentacion();
                    $id_pres = $presentacionModel->buscarIdPorNombre($_POST['presentacion']);
                    if (!$id_pres) {
                        throw new Exception("Presentación no válida");
                    }
                    $productoServicio->setIdPres($id_pres);

                    if ($esFabricado == 1) {
                        $materiasPrimas = [];
                        if (isset($_POST['materias_primas_json'])) {
                            $materiasPrimas = json_decode($_POST['materias_primas_json'], true);
                            if (json_last_error() !== JSON_ERROR_NONE) {
                                throw new Exception("Error en el formato de materias primas: " . json_last_error_msg());
                            }
                        }
                        $productoServicio->setMateriasPrimas($materiasPrimas);
                    } else {
                        $productoServicio->setMateriasPrimas([]);
                    }
                } else {
                    $productoServicio->setStock(0);
                    $productoServicio->setPrecioMayor(0);
                    $productoServicio->setEsFabricado(0);
                    $productoServicio->setMateriasPrimas([]);
                    
                    $presentacionModel = new Presentacion();
                    $id_pres_no_aplica = $presentacionModel->buscarIdPorNombre('No aplica');
                    if (!$id_pres_no_aplica) {
                        throw new Exception("No se encontró la presentación 'No aplica' en el sistema");
                    }
                    $productoServicio->setIdPres($id_pres_no_aplica);
                }

                if ($productoServicio->actualizar()) {
                    $mensaje = $tipo == 1 ? "Producto actualizado correctamente" : "Servicio actualizado correctamente";
                    echo json_encode([
                        'success' => true, 
                        'message' => $mensaje
                    ]);
                } else {
                    throw new Exception("No se pudo actualizar el producto/servicio");
                }

            } catch (Exception $e) {
                http_response_code(400);
                echo json_encode([
                    'success' => false, 
                    'message' => 'Error al actualizar: ' . $e->getMessage()
                ]);
            }
            exit;
        }
        break;

    case 'eliminar':
        if (isAjaxRequest()) {
            header('Content-Type: application/json');
            $id_inv = $_POST['inv_prod_serv'] ?? null;
            
            if (!$id_inv) {
                sendJsonResponse(false, "Error en la eliminación", "ID del producto-servicio no proporcionado");
            }

            try {
                if ($productoServicio->eliminar($id_inv)) {
                    sendJsonResponse(true, "Producto/Servicio eliminado exitosamente");
                } else {
                    throw new Exception("No se pudo eliminar el producto/servicio");
                }
            } catch (Exception $e) {
                sendJsonResponse(false, "Error en la eliminación", $e->getMessage());
            }
        }
        break;

    case 'getMateriasPrimas':
        if (!isAjaxRequest()) {
            header("HTTP/1.0 403 Forbidden");
            exit;
        }
        
        header('Content-Type: application/json');
        try {
            $materiasPrimas = $productoServicio->getMateriasPrimasDisponibles();
            echo json_encode([
                'success' => true,
                'materiasPrimas' => $materiasPrimas,
                'message' => 'Materias primas cargadas correctamente'
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Error al cargar materias primas'
            ]);
        }
        exit;
        break;

    case 'obtenerDetalle':
        if (!isAjaxRequest()) {
            header("HTTP/1.0 403 Forbidden");
            exit;
        }
        
        $id = $_GET['id'] ?? null;
        if (!$id) {
            sendJsonResponse(false, 'ID no proporcionado', 'No se especificó el ID del producto');
        }
        
        try {
            $productoData = $productoServicio->buscarPorId($id);
            if (!$productoData) {
                throw new Exception("Producto no encontrado");
            }

            if ($productoData['es_fabricado'] != 1) {
                sendJsonResponse(false, 'Este producto no es fabricado', 'Solo los productos fabricados tienen materias primas');
            }

            $materiasPrimas = [];
            if (isset($productoData['materias_primas']) && is_array($productoData['materias_primas'])) {
                $materiasPrimas = $productoData['materias_primas'];
            }

            sendJsonResponse(true, 'Detalles cargados correctamente', '', [
                'materias_primas' => $materiasPrimas,
                'producto' => [
                    'nombre' => $productoData['nombre'],
                    'es_fabricado' => $productoData['es_fabricado']
                ]
            ]);
        } catch (Exception $e) {
            sendJsonResponse(false, 'Error al cargar detalles', $e->getMessage());
        }
        break;

    default:
        $productos = $productoServicio->listar();
        $unidades = $unidadMedida->listar();
        $presentaciones = $presentacion->listar();
        $materiasPrimas = $materiaPrima->listar();
        require_once 'vista/producto-servicio/index.php';
        break;
}
?>