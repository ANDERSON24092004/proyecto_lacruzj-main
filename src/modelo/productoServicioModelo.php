<?php
namespace src\modelo;
use src\modelo\conexion;
use PDO;
use PDOException;
use Exception;

class productoServicioModelo extends conexion
{
    private $id_inv;
    private $id_pres;
    private $id_unidad_medida;
    private $nombre;
    private $costo;
    private $precio_mayor;
    private $stock;
    private $tipo;
    private $es_fabricado;
    private $materias_primas = [];

    public function getIdInv() {
        return $this->id_inv;
    }

    public function setIdInv($id_inv) {
        $this->id_inv = $id_inv;
        return $this;
    }

    public function getIdPres() {
        return $this->id_pres;
    }

    public function setIdPres($id_pres) {
        $this->id_pres = $id_pres;
        return $this;
    }

    public function getIdUnidadMedida() {
        return $this->id_unidad_medida;
    }

    public function setIdUnidadMedida($id_unidad_medida) {
        $this->id_unidad_medida = $id_unidad_medida;
        return $this;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function setNombre($nombre) {
        $this->nombre = $nombre;
        return $this;
    }

    public function getCosto() {
        return $this->costo;
    }

    public function setCosto($costo) {
        $this->costo = $costo;
        return $this;
    }

    public function getPrecioMayor() {
        return $this->precio_mayor;
    }

    public function setPrecioMayor($precio_mayor) {
        $this->precio_mayor = $precio_mayor;
        return $this;
    }

    public function getStock() {
        return $this->stock;
    }

    public function setStock($stock) {
        $this->stock = $stock;
        return $this;
    }

    public function getTipo() {
        return $this->tipo;
    }

    public function setTipo($tipo) {
        $this->tipo = $tipo;
        return $this;
    }

    public function getEsFabricado() {
        return $this->es_fabricado;
    }

    public function setEsFabricado($es_fabricado) {
        $this->es_fabricado = $es_fabricado;
        return $this;
    }

    public function setMateriasPrimas($materias_primas) {
        $this->materias_primas = $materias_primas;
        return $this;
    }

    public function getMateriasPrimas() {
        return $this->materias_primas;
    }

    private function validarDatos($esCreacion = true) {
        $errores = [];

        if (empty(trim($this->nombre))) {
            $errores[] = "El nombre es obligatorio";
        }

        if (empty($this->id_unidad_medida)) {
            $errores[] = "La unidad de medida es obligatoria";
        }

        if ($this->costo === null || $this->costo < 0) {
            $errores[] = "El costo debe ser mayor o igual a 0";
        }

        if ($this->tipo == 1) {
            if ($this->stock === null || $this->stock < 0) {
                $errores[] = "El stock debe ser mayor o igual a 0";
            }
            if ($this->precio_mayor === null || $this->precio_mayor < 0) {
                $errores[] = "El precio al por mayor debe ser mayor o igual a 0";
            }
            if (empty($this->id_pres)) {
                $errores[] = "La presentación es obligatoria para productos";
            }
        } else { 
            $this->stock = 0;
            $this->precio_mayor = 0;
            $this->es_fabricado = 0;
        }

        return $errores;
    }

    private function obtenerIdPresentacionNoAplica() {
        try {
            $sql = "SELECT id_pres FROM tipo_presentacion WHERE nombre = 'No aplica' AND status = 0 LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['id_pres'] : null;
        } catch (PDOException $e) {
            error_log("Error al obtener presentación 'No aplica': " . $e->getMessage());
            return null;
        }
    }

    public function crear() {
        try {
            $errores = $this->validarDatos(true);
            if (!empty($errores)) {
                throw new Exception(implode("; ", $errores));
            }

            if ($this->tipo == 2) {
                $id_pres_no_aplica = $this->obtenerIdPresentacionNoAplica();
                if (!$id_pres_no_aplica) {
                    throw new Exception("No se encontró la presentación 'No aplica' en el sistema");
                }
                $this->id_pres = $id_pres_no_aplica;
            }

            $this->pdo->beginTransaction();

            $sql = "INSERT INTO inv_prod_serv (id_pres, id_unidad_medida, nombre, costo, costo_mayor, stock, tipo, es_fabricado, status) 
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $this->id_pres,
                $this->id_unidad_medida,
                $this->nombre,
                $this->costo,
                $this->precio_mayor,
                $this->stock,
                $this->tipo,
                $this->es_fabricado
            ]);

            $id_producto = $this->pdo->lastInsertId();

            if ($this->tipo == 1 && $this->es_fabricado == 1 && !empty($this->materias_primas)) {
                foreach ($this->materias_primas as $materia) {
                    $sql_detalle = "INSERT INTO detalle_producto (id_materia, id_producto, cantidad) 
                                   VALUES (?, ?, ?)";
                    $stmt_detalle = $this->pdo->prepare($sql_detalle);
                    $stmt_detalle->execute([
                        $materia['id_materia'],
                        $id_producto,
                        $materia['cantidad']
                    ]);
                }
            }

            $this->pdo->commit();
            return $id_producto;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Error en ProductoServicio->crear(): " . $e->getMessage());
            throw new Exception("Error al registrar el producto/servicio: " . $e->getMessage());
        }
    }

    public function actualizar() {
    try {
        $errores = $this->validarDatos(false);
        if (!empty($errores)) {
            throw new Exception(implode("; ", $errores));
        }

        $productoActual = $this->buscarPorId($this->id_inv);
        if (!$productoActual) {
            throw new Exception("Producto/servicio no encontrado");
        }

        $this->pdo->beginTransaction();

        $materiasPrimasActuales = [];
        if ($productoActual['es_fabricado'] == 1) {
            $sql_materias_actuales = "SELECT * FROM detalle_producto WHERE id_producto = ?";
            $stmt_materias_actuales = $this->pdo->prepare($sql_materias_actuales);
            $stmt_materias_actuales->execute([$this->id_inv]);
            $materiasPrimasActuales = $stmt_materias_actuales->fetchAll(PDO::FETCH_ASSOC);
        }

        $sql = "UPDATE inv_prod_serv 
               SET id_pres = ?, id_unidad_medida = ?, nombre = ?, costo = ?, 
                   costo_mayor = ?, stock = ?, tipo = ?, es_fabricado = ?
               WHERE id_inv = ? AND status = 0";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $this->id_pres,
            $this->id_unidad_medida,
            $this->nombre,
            $this->costo,
            $this->precio_mayor,
            $this->stock,
            $this->tipo,
            $this->es_fabricado,
            $this->id_inv
        ]);

        if ($this->tipo == 1 && $this->es_fabricado == 1) {
            foreach ($materiasPrimasActuales as $materiaActual) {
                $materiaEnNuevaLista = false;
                if (!empty($this->materias_primas)) {
                    foreach ($this->materias_primas as $materiaNueva) {
                        if ($materiaNueva['id_materia'] == $materiaActual['id_materia']) {
                            $materiaEnNuevaLista = true;
                            break;
                        }
                    }
                }
                
                if (!$materiaEnNuevaLista) {
                    $sql_restaurar_stock = "UPDATE inv_materia_prima 
                                           SET stock = stock + ? 
                                           WHERE id_materia = ? AND status = 0";
                    $stmt_restaurar_stock = $this->pdo->prepare($sql_restaurar_stock);
                    $stmt_restaurar_stock->execute([
                        $materiaActual['cantidad'],
                        $materiaActual['id_materia']
                    ]);
                }
            }

            $sql_eliminar = "DELETE FROM detalle_producto WHERE id_producto = ?";
            $stmt_eliminar = $this->pdo->prepare($sql_eliminar);
            $stmt_eliminar->execute([$this->id_inv]);

            if (!empty($this->materias_primas)) {
                foreach ($this->materias_primas as $materia) {
                    $cantidadAnterior = 0;
                    foreach ($materiasPrimasActuales as $materiaActual) {
                        if ($materiaActual['id_materia'] == $materia['id_materia']) {
                            $cantidadAnterior = $materiaActual['cantidad'];
                            break;
                        }
                    }

                    $diferenciaStock = $materia['cantidad'] - $cantidadAnterior;

                    if ($diferenciaStock != 0) {
                        $sql_actualizar_stock = "UPDATE inv_materia_prima 
                                               SET stock = stock - ? 
                                               WHERE id_materia = ? AND stock >= ? AND status = 0";
                        $stmt_actualizar_stock = $this->pdo->prepare($sql_actualizar_stock);
                        $stmt_actualizar_stock->execute([
                            $diferenciaStock,
                            $materia['id_materia'],
                            $diferenciaStock
                        ]);

                        if ($stmt_actualizar_stock->rowCount() === 0) {
                            throw new Exception("Stock insuficiente para la materia prima: " . $this->obtenerNombreMateriaPrima($materia['id_materia']));
                        }
                    }

                    $sql_detalle = "INSERT INTO detalle_producto (id_materia, id_producto, cantidad) 
                                   VALUES (?, ?, ?)";
                    $stmt_detalle = $this->pdo->prepare($sql_detalle);
                    $stmt_detalle->execute([
                        $materia['id_materia'],
                        $this->id_inv,
                        $materia['cantidad']
                    ]);
                }
            }
        } else {
            if (!empty($materiasPrimasActuales)) {
                foreach ($materiasPrimasActuales as $materiaActual) {
                    $sql_restaurar_stock = "UPDATE inv_materia_prima 
                                           SET stock = stock + ? 
                                           WHERE id_materia = ? AND status = 0";
                    $stmt_restaurar_stock = $this->pdo->prepare($sql_restaurar_stock);
                    $stmt_restaurar_stock->execute([
                        $materiaActual['cantidad'],
                        $materiaActual['id_materia']
                    ]);
                }
                
                $sql_eliminar = "DELETE FROM detalle_producto WHERE id_producto = ?";
                $stmt_eliminar = $this->pdo->prepare($sql_eliminar);
                $stmt_eliminar->execute([$this->id_inv]);
            }
        }

        $this->pdo->commit();
        return true;

    } catch (Exception $e) {
        $this->pdo->rollBack();
        error_log("Error en ProductoServicio->actualizar(): " . $e->getMessage());
        throw new Exception("Error al actualizar el producto/servicio: " . $e->getMessage());
    }
    }

    private function obtenerNombreMateriaPrima($idMateria) {
        try {
            $sql = "SELECT nombre FROM inv_materia_prima WHERE id_materia = ? AND status = 0";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$idMateria]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['nombre'] : 'Materia prima desconocida';
        } catch (Exception $e) {
            return 'Materia prima desconocida';
        }
    }

    public function listar() {
        try {
            $sql = "SELECT 
                        ps.id_inv,
                        ps.nombre,
                        ps.costo,
                        ps.costo_mayor,
                        ps.stock,
                        ps.tipo,
                        ps.es_fabricado,
                        um.nombre as unidad_medida,
                        tp.nombre as presentacion
                    FROM inv_prod_serv ps
                    LEFT JOIN unidades_medida um ON ps.id_unidad_medida = um.id_unidad_medida
                    LEFT JOIN tipo_presentacion tp ON ps.id_pres = tp.id_pres 
                    WHERE ps.status = 0
                    ORDER BY ps.id_inv DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error en ProductoServicio->listar(): ' . $e->getMessage());
            return [];
        }
    }

    public function buscarPorId($id) {
        try {
            $sql = "SELECT 
                        ps.*,
                        um.nombre as unidad_medida,
                        tp.nombre as presentacion
                    FROM inv_prod_serv ps
                    LEFT JOIN unidades_medida um ON ps.id_unidad_medida = um.id_unidad_medida
                    LEFT JOIN tipo_presentacion tp ON ps.id_pres = tp.id_pres
                    WHERE ps.id_inv = ? AND ps.status = 0";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            $producto = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($producto) {
                if ($producto['es_fabricado'] == 1) {
                    $sql_materias = "SELECT 
                                dp.id_materia,
                                    mp.nombre,
                                    dp.cantidad,
                                    mp.costo,
                                    um.nombre as unidad_medida
                                FROM detalle_producto dp
                                JOIN inv_materia_prima mp ON dp.id_materia = mp.id_materia AND mp.status = 0
                                LEFT JOIN unidades_medida um ON mp.id_unidad_medida = um.id_unidad_medida
                                WHERE dp.id_producto = ?";
                    $stmt_materias = $this->pdo->prepare($sql_materias);
                    $stmt_materias->execute([$id]);
                    $producto['materias_primas'] = $stmt_materias->fetchAll(PDO::FETCH_ASSOC);
                } else {
                    $producto['materias_primas'] = [];
                }
            }

            return $producto;
        } catch (PDOException $e) {
            error_log("Error en ProductoServicio->buscarPorId(): " . $e->getMessage());
            return false;
        }
    }

    public function eliminar($id) {
        try {
            $producto = $this->buscarPorId($id);
            if (!$producto) {
                throw new Exception("Producto/servicio no encontrado");
            }

            $this->pdo->beginTransaction();

            $sql_eliminar_detalle = "DELETE FROM detalle_producto WHERE id_producto = ?";
            $stmt_eliminar_detalle = $this->pdo->prepare($sql_eliminar_detalle);
            $stmt_eliminar_detalle->execute([$id]);

            $sql_eliminar = "UPDATE inv_prod_serv SET status = 1 WHERE id_inv = ? AND status = 0";
            $stmt_eliminar = $this->pdo->prepare($sql_eliminar);
            $result = $stmt_eliminar->execute([$id]);

            $this->pdo->commit();
            return $result;

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Error en ProductoServicio->eliminar(): " . $e->getMessage());
            throw new Exception("Error al eliminar el producto/servicio de la base de datos");
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function getMateriasPrimasDisponibles() {
        try {
            $sql = "SELECT 
                        mp.id_materia,
                        mp.nombre,
                        mp.stock,
                        mp.costo,
                        um.nombre as unidad_medida
                    FROM inv_materia_prima mp
                    LEFT JOIN unidades_medida um ON mp.id_unidad_medida = um.id_unidad_medida
                    WHERE mp.stock > 0 AND mp.status = 0
                    ORDER BY mp.nombre ASC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en ProductoServicio->getMateriasPrimasDisponibles(): " . $e->getMessage());
            return [];
        }
    }
}
?>