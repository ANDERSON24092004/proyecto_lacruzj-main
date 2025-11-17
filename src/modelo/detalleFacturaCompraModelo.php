<?php
namespace src\modelo;
use src\modelo\conexion;
use PDO;
use PDOException;
use Exception;

class detalleFacturaCompraModelo extends conexion
{
    private $id_fact;
    private $id_materia_prima;
    private $id_fact_com;
    private $cantidad;
    private $costo_compra;

    public function getIdFact() {
        return $this->id_fact;
    }

    public function setIdFact($id_fact) {
        $this->id_fact = $id_fact;
        return $this;
    }

    public function getIdMateriaPrima() {
        return $this->id_materia_prima;
    }

    public function setIdMateriaPrima($id_materia_prima) {
        $this->id_materia_prima = $id_materia_prima;
        return $this;
    }

    public function getIdFactCom() {
        return $this->id_fact_com;
    }

    public function setIdFactCom($id_fact_com) {
        $this->id_fact_com = $id_fact_com;
        return $this;
    }

    public function getCantidad() {
        return $this->cantidad;
    }

    public function setCantidad($cantidad) {
        $this->cantidad = $cantidad;
        return $this;
    }

    public function getCostoCompra() {
        return $this->costo_compra;
    }

    public function setCostoCompra($costo_compra) {
        $this->costo_compra = $costo_compra;
        return $this;
    }

    public function registrarDetalle($detalles) {
        try {
            $this->validarDetalles($detalles);

            $this->pdo->beginTransaction();

            $sql = "INSERT INTO detalle_fact_compra (id_materia_prima, id_fact_com, cantidad, costo_compra) 
                   VALUES (?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);

            foreach ($detalles as $detalle) {
                $stmt->execute([
                    $detalle['id_materia_prima'],
                    $detalle['id_fact_com'],
                    $detalle['cantidad'],
                    $detalle['costo']
                ]);

                $this->actualizarStockMateriaPrima(
                    $detalle['id_materia_prima'], 
                    $detalle['cantidad'],
                    $detalle['costo']
                );
            }

            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {

            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log("Error en DetalleFacturaCompra->registrarDetalle(): " . $e->getMessage());
            throw new Exception("Error al registrar los detalles de la factura: " . $e->getMessage());
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

    private function validarDetalles($detalles) {
        if (empty($detalles) || !is_array($detalles)) {
            throw new Exception("No se proporcionaron detalles para registrar");
        }

        foreach ($detalles as $index => $detalle) {
            if (empty($detalle['id_materia_prima'])) {
                throw new Exception("El detalle #" . ($index + 1) . " no tiene materia prima seleccionada");
            }
            if (empty($detalle['id_fact_com'])) {
                throw new Exception("El detalle #" . ($index + 1) . " no tiene ID de factura");
            }
            if (empty($detalle['cantidad']) || $detalle['cantidad'] <= 0) {
                throw new Exception("El detalle #" . ($index + 1) . " tiene una cantidad inválida");
            }
            if (empty($detalle['costo']) || $detalle['costo'] <= 0) {
                throw new Exception("El detalle #" . ($index + 1) . " tiene un costo inválido");
            }
        }
    }

    private function actualizarStockMateriaPrima($id_materia_prima, $cantidad, $costo_compra) {
        try {
            $sql_check = "SELECT stock, costo FROM inv_materia_prima WHERE id_materia = ?";
            $stmt_check = $this->pdo->prepare($sql_check);
            $stmt_check->execute([$id_materia_prima]);
            $materia_existente = $stmt_check->fetch(PDO::FETCH_ASSOC);

            if (!$materia_existente) {
                throw new Exception("Materia prima con ID $id_materia_prima no encontrada");
            }

            $stock_actual = $materia_existente['stock'];
            $costo_promedio_actual = $materia_existente['costo'];
            
            if ($stock_actual > 0) {
                $nuevo_costo_promedio = 
                    (($stock_actual * $costo_promedio_actual) + ($cantidad * $costo_compra)) 
                    / ($stock_actual + $cantidad);
            } else {
                $nuevo_costo_promedio = $costo_compra;
            }
            
            $sql_update = "UPDATE inv_materia_prima 
                          SET stock = stock + ?, 
                              costo = ? 
                          WHERE id_materia = ?";
            $stmt_update = $this->pdo->prepare($sql_update);
            $stmt_update->execute([$cantidad, $nuevo_costo_promedio, $id_materia_prima]);
            
            error_log("Stock actualizado - ID: $id_materia_prima, Stock anterior: $stock_actual, Nuevo stock: " . ($stock_actual + $cantidad) . ", Nuevo costo promedio: $nuevo_costo_promedio");
            
            return true;
        } catch (PDOException $e) {
            error_log("Error en actualizarStockMateriaPrima(): " . $e->getMessage());
            throw new Exception("Error al actualizar el stock de materia prima: " . $e->getMessage());
        }
    }
}
?>