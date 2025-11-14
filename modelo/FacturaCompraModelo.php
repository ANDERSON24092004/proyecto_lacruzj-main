<?php
require_once 'modelo/conexion.php';

class FacturaCompra extends Conexion
{
    private $id_fact_com;
    private $id_proveedor;
    private $num_factura;
    private $total_iva;
    private $total_general;
    private $fecha;
    private $vigencia;

    public function getIdFactCom() {
        return $this->id_fact_com;
    }

    public function setIdFactCom($id_fact_com) {
        $this->id_fact_com = $id_fact_com;
        return $this;
    }

    public function getIdProveedor() {
        return $this->id_proveedor;
    }

    public function setIdProveedor($id_proveedor) {
        $this->id_proveedor = $id_proveedor;
        return $this;
    }

    public function getTotalIva() {
        return $this->total_iva;
    }

    public function setTotalIva($total_iva) {
        $this->total_iva = $total_iva;
        return $this;
    }

    public function getNum_factura() {
        return $this->num_factura;
    }

    public function setNum_factura($num_factura) {
        $this->num_factura = $num_factura;
        return $this;
    }

    public function getTotalGeneral() {
        return $this->total_general;
    }

    public function setTotalGeneral($total_general) {
        $this->total_general = $total_general;
        return $this;
    }

    public function getFecha() {
        return $this->fecha;
    }

    public function setFecha($fecha) {
        $this->fecha = $fecha;
        return $this;
    }

    private function validarDatos($detalles = []) {
        $errores = [];

        if (empty($this->id_proveedor)) {
            $errores[] = "Debe seleccionar un proveedor";
        }

        if (empty($this->num_factura)) {
            $errores[] = "El número de factura es obligatorio";
        }

        if (empty($this->fecha)) {
            $errores[] = "La fecha es obligatoria";
        }

        if (empty($detalles) || !is_array($detalles) || count($detalles) === 0) {
            $errores[] = "Debe agregar al menos un detalle de materia prima";
        }

        foreach ($detalles as $index => $detalle) {
            if (empty($detalle['id_materia_prima'])) {
                $errores[] = "El detalle #" . ($index + 1) . " no tiene materia prima seleccionada";
            }
            if (empty($detalle['cantidad']) || $detalle['cantidad'] <= 0) {
                $errores[] = "El detalle #" . ($index + 1) . " tiene una cantidad inválida";
            }
            if (empty($detalle['costo']) || $detalle['costo'] <= 0) {
                $errores[] = "El detalle #" . ($index + 1) . " tiene un costo inválido";
            }
        }

        return $errores;
    }

    public function listar() {
        try {
            $sql = "SELECT 
                        fc.id_fact_com,
                        fc.fecha,
                        fc.num_factura,
                        fc.total_general,
                        fc.total_iva,
                        fc.status,
                        p.nombre as proveedor,
                        p.id_proveedores
                    FROM factura_compra fc
                    LEFT JOIN proveedores p ON fc.id_proveedor = p.id_proveedores
                    ORDER BY fc.fecha DESC, fc.id_fact_com DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en FacturaCompra->listar(): " . $e->getMessage());
            return [];
        }
    }

    public function buscarPorId($id) {
        try {
            $sql = "SELECT fc.*, p.nombre as proveedor
                    FROM factura_compra fc
                    LEFT JOIN proveedores p ON fc.id_proveedor = p.id_proveedores
                    WHERE fc.id_fact_com = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en FacturaCompra->buscarPorId(): " . $e->getMessage());
            return false;
        }
    }

    public function buscarPorIdRe($id) {
        try {
            $sql = "SELECT fc.*, p.nombre as proveedor
                    FROM factura_compra fc
                    LEFT JOIN proveedores p ON fc.id_proveedor = p.id_proveedores
                    WHERE fc.id_fact_com = ? AND fc.status = 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en FacturaCompra->buscarPorId(): " . $e->getMessage());
            return false;
        }
    }

    public function registrar($detalles = []) {
        try {
            $errores = $this->validarDatos($detalles);
            if (!empty($errores)) {
                throw new Exception(implode("; ", $errores));
            }

            $this->pdo->beginTransaction();

            $sql = "INSERT INTO factura_compra (id_proveedor, num_factura, total_iva, total_general, fecha, status) 
                   VALUES (?, ?, ?, ?, ?, 0)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $this->id_proveedor,
                $this->num_factura,
                $this->total_iva,
                $this->total_general,
                $this->fecha,
            ]);

            $id_fact_com = $this->pdo->lastInsertId();
        
            $this->pdo->commit();
            
            return $id_fact_com;
        } catch (PDOException $e) {

            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log("Error en FacturaCompra->registrar(): " . $e->getMessage());
            throw new Exception("Error al registrar la factura de compra en la base de datos: " . $e->getMessage());
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

    public function anular($id) {
        try {
            $factura = $this->buscarPorId($id);
            if (!$factura) {
                throw new Exception("Factura no encontrada");
            }

            $sql = "UPDATE factura_compra SET status = 1 WHERE id_fact_com = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error en FacturaCompra->anular(): " . $e->getMessage());
            throw new Exception("Error al anular la factura en la base de datos");
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function obtenerDetallePorFactura($id_fact_com) {
        try {
            $sql = "SELECT dfc.*, mp.nombre as materia_prima, mp.stock, um.nombre as unidad_medida
                    FROM detalle_fact_compra dfc
                    LEFT JOIN inv_materia_prima mp ON dfc.id_materia_prima = mp.id_materia
                    LEFT JOIN unidades_medida um ON mp.id_unidad_medida = um.id_unidad_medida
                    WHERE dfc.id_fact_com = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id_fact_com]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en FacturaCompra->obtenerDetallePorFactura(): " . $e->getMessage());
            return [];
        }
    }

    public function reactivar($id) {
        try {
            $factura = $this->buscarPorId($id);
            if (!$factura) {
                throw new Exception("Factura no encontrada");
            }

            $sql = "UPDATE factura_compra SET status = 0 WHERE id_fact_com = ? AND status = 1";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error en FacturaCompra->reactivar(): " . $e->getMessage());
            throw new Exception("Error al reactivar la factura en la base de datos");
        } catch (Exception $e) {
            throw $e;
        }
    }
}
?>