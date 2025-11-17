<?php
namespace src\modelo;
use src\modelo\conexion;
use PDO;
use PDOException;
use Exception;
class facturaModelo extends conexion {
    private $nro_fact;
    private $rif;
    private $id_condicion_pago;
    private $numero_orden;
    private $total_iva;
    private $total_general;
    private $fecha;
    private $status;
    private $detalles = [];
    private $id_descuento;
    private $duracion_credito;
    private $id_iva;

    public function getNroFact() { return $this->nro_fact; }
    public function setNroFact($nro_fact) { $this->nro_fact = $nro_fact; return $this; }

    public function getRif() { return $this->rif; }
    public function setRif($rif) { $this->rif = $rif; return $this; }

    public function getIdCondicionPago() { return $this->id_condicion_pago; }
    public function setIdCondicionPago($id_condicion_pago) { $this->id_condicion_pago = $id_condicion_pago; return $this; }

    public function getNumeroOrden() { return $this->numero_orden; }
    public function setNumeroOrden($numero_orden) { $this->numero_orden = $numero_orden; return $this; }

    public function getTotalIva() { return $this->total_iva; }
    public function setTotalIva($total_iva) { $this->total_iva = $total_iva; return $this; }

    public function getTotalGeneral() { return $this->total_general; }
    public function setTotalGeneral($total_general) { $this->total_general = $total_general; return $this; }

    public function getFecha() { return $this->fecha; }
    public function setFecha($fecha) { $this->fecha = $fecha; return $this; }

    public function getStatus() { return $this->status; }
    public function setStatus($status) { $this->status = $status; return $this; }

    public function getDetalles() { return $this->detalles; }
    public function setDetalles($detalles) { $this->detalles = $detalles; return $this; }

    public function getIdDescuento() { return $this->id_descuento; }
    public function setIdDescuento($id_descuento) { $this->id_descuento = $id_descuento; return $this; }

    public function getDuracionCredito() { return $this->duracion_credito; }
    public function setDuracionCredito($duracion_credito) { $this->duracion_credito = $duracion_credito; return $this; }
    
    public function getIdIva() { return $this->id_iva; }
    public function setIdIva($id_iva) { $this->id_iva = $id_iva; return $this; }

    public function validarDatosFactura($datos) {
        $errores = [];

        if (empty($datos['rif'])) {
            $errores[] = "El RIF del cliente es obligatorio";
        }

        if (empty($datos['id_condicion_pago'])) {
            $errores[] = "La condición de pago es obligatoria";
        }

        if (empty($datos['fecha'])) {
            $errores[] = "La fecha es obligatoria";
        }

        if (!isset($datos['detalles']) || empty($datos['detalles'])) {
            $errores[] = "Debe agregar al menos un producto/servicio a la factura";
        } else {
            foreach ($datos['detalles'] as $index => $detalle) {
                if (empty($detalle['id_inv'])) {
                    $errores[] = "El producto/servicio en la línea " . ($index + 1) . " es obligatorio";
                }
                if (empty($detalle['cantidad']) || $detalle['cantidad'] <= 0) {
                    $errores[] = "La cantidad en la línea " . ($index + 1) . " debe ser mayor a 0";
                }
            }
        }

        return $errores;
    }

    public function registrarFactura() {
        try {
            $this->pdo->beginTransaction();

            $sql_factura = "INSERT INTO factura_venta (rif, id_condicion_pago, id_descuento, numero_orden, total_iva, total_general, fecha, status) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt_factura = $this->pdo->prepare($sql_factura);
            $id_descuento = $this->id_descuento ?: null;
            $stmt_factura->execute([
                $this->rif,
                $this->id_condicion_pago,
                $id_descuento,
                $this->numero_orden,
                $this->total_iva,
                $this->total_general,
                $this->fecha,
                0 
            ]);

            $nro_fact = $this->pdo->lastInsertId();

            if ($this->id_iva) {
                $sql_iva = "INSERT INTO iva_factura (id_iva, id_factura, fecha_cambio) VALUES (?, ?, ?)";
                $stmt_iva = $this->pdo->prepare($sql_iva);
                $stmt_iva->execute([$this->id_iva, $nro_fact, date('Y-m-d')]);
            }

            foreach ($this->detalles as $detalle) {
                $sql_detalle = "INSERT INTO detalle_factura (nro_fact, id_inv, cantidad) 
                               VALUES (?, ?, ?)";
                $stmt_detalle = $this->pdo->prepare($sql_detalle);
                $stmt_detalle->execute([
                    $nro_fact,
                    $detalle['id_inv'],
                    $detalle['cantidad']
                ]);

                if ($this->esProducto($detalle['id_inv'])) {
                    $sql_update_stock = "UPDATE inv_prod_serv SET stock = stock - ? WHERE id_inv = ? AND stock >= ? AND status = 0";
                    $stmt_update_stock = $this->pdo->prepare($sql_update_stock);
                    $stmt_update_stock->execute([
                        $detalle['cantidad'],
                        $detalle['id_inv'],
                        $detalle['cantidad']
                    ]);

                    if ($stmt_update_stock->rowCount() === 0) {
                        throw new Exception("Stock insuficiente para el producto: " . $this->obtenerNombreProducto($detalle['id_inv']));
                    }
                }
            }

            if ($this->id_condicion_pago == 2 && $this->duracion_credito) {
                $sql_cuenta = "INSERT INTO factura_cobrar (id_facturas, duracion, status) VALUES (?, ?, ?)";
                $stmt_cuenta = $this->pdo->prepare($sql_cuenta);
                $stmt_cuenta->execute([
                    $nro_fact,
                    $this->duracion_credito,
                    0 
                ]);
            }

            $this->pdo->commit();
            return $nro_fact;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Error en FacturaModel->registrarFactura(): " . $e->getMessage());
            throw new Exception("Error al registrar la factura: " . $e->getMessage());
        }
    }

    public function esProducto($id_inv) {
        try {
            $sql = "SELECT tipo FROM inv_prod_serv WHERE id_inv = ? AND status = 0";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id_inv]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result && $result['tipo'] == 1; 
        } catch (Exception $e) {
            return false;
        }
    }

    public function obtenerNombreProducto($id_inv) {
        try {
            $sql = "SELECT nombre FROM inv_prod_serv WHERE id_inv = ? AND status = 0";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id_inv]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['nombre'] : 'Producto desconocido';
        } catch (Exception $e) {
            return 'Producto desconocido';
        }
    }

    public function listarFacturas() {
        try {
            $sql = "SELECT 
                        fv.nro_fact,
                        fv.rif,
                        c.razon_social,
                        cp.forma as condicion_pago,
                        fv.numero_orden,
                        fv.total_iva,
                        fv.total_general,
                        fv.fecha,
                        fv.status,
                        fc.status as status_credito
                    FROM factura_venta fv
                    LEFT JOIN cliente c ON fv.rif = c.rif
                    LEFT JOIN condicion_pago cp ON fv.id_condicion_pago = cp.id_condicion_pago
                    LEFT JOIN factura_cobrar fc ON fv.nro_fact = fc.id_facturas
                    WHERE fv.id_condicion_pago != 4
                    ORDER BY fv.fecha DESC, fv.nro_fact DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en FacturaModel->listarFacturas(): " . $e->getMessage());
            return [];
        }
    }

    public function obtenerDetallesFacturaCompleta($nro_fact) {
        try {
            $sql_factura = "SELECT 
                            fv.nro_fact,
                            fv.rif,
                            fv.id_condicion_pago,
                            fv.numero_orden,
                            fv.total_iva,
                            fv.total_general,
                            fv.fecha,
                            fv.status,
                            c.razon_social,
                            c.telefono,
                            c.correo,
                            c.direccion,
                            cp.forma as condicion_pago,
                            d.porcentaje as descuento_porcentaje,
                            i.porcentaje as iva_porcentaje
                        FROM factura_venta fv
                        LEFT JOIN cliente c ON fv.rif = c.rif
                        LEFT JOIN condicion_pago cp ON fv.id_condicion_pago = cp.id_condicion_pago
                        LEFT JOIN descuento d ON fv.id_descuento = d.id
                        LEFT JOIN iva_factura ifa ON fv.nro_fact = ifa.id_factura
                        LEFT JOIN iva i ON ifa.id_iva = i.id_iva
                        WHERE fv.nro_fact = ?";
            
            $stmt_factura = $this->pdo->prepare($sql_factura);
            $stmt_factura->execute([$nro_fact]);
            $factura = $stmt_factura->fetch(PDO::FETCH_ASSOC);
            
            if (!$factura) {
                return null;
            }
            
            $sql_detalles = "SELECT 
                            df.id_inv,
                            df.cantidad,
                            ps.nombre,
                            ps.tipo,
                            ps.costo,
                            ps.costo_mayor,
                            um.nombre as unidad_medida,
                            CASE 
                                WHEN ps.tipo = 1 THEN 'Producto'
                                ELSE 'Servicio'
                            END as tipo_nombre
                        FROM detalle_factura df
                        LEFT JOIN inv_prod_serv ps ON df.id_inv = ps.id_inv
                        LEFT JOIN unidades_medida um ON ps.id_unidad_medida = um.id_unidad_medida
                        WHERE df.nro_fact = ?";
            
            $stmt_detalles = $this->pdo->prepare($sql_detalles);
            $stmt_detalles->execute([$nro_fact]);
            $detalles = $stmt_detalles->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($detalles as &$detalle) {
                $precio = $detalle['cantidad'] >= 20 ? $detalle['costo_mayor'] : $detalle['costo'];
                $detalle['precio_unitario'] = $precio;
                $detalle['subtotal'] = $precio * $detalle['cantidad'];
            }
            
            return [
                'factura' => $factura,
                'detalles' => $detalles
            ];
            
        } catch (PDOException $e) {
            error_log("Error en FacturaModel->obtenerDetallesFacturaCompleta(): " . $e->getMessage());
            return null;
        }
    }

    public function anularFactura($nro_fact) {
        try {
            $this->pdo->beginTransaction();

            $sql_verificar = "SELECT status FROM factura_venta WHERE nro_fact = ?";
            $stmt_verificar = $this->pdo->prepare($sql_verificar);
            $stmt_verificar->execute([$nro_fact]);
            $factura = $stmt_verificar->fetch(PDO::FETCH_ASSOC);

            if (!$factura) {
                throw new Exception("Factura no encontrada");
            }

            if ($factura['status'] == 1) {
                throw new Exception("La factura ya está anulada");
            }

            $sql_detalles = "SELECT df.id_inv, df.cantidad, ps.tipo 
                            FROM detalle_factura df
                            LEFT JOIN inv_prod_serv ps ON df.id_inv = ps.id_inv
                            WHERE df.nro_fact = ? AND ps.tipo = 1";
            $stmt_detalles = $this->pdo->prepare($sql_detalles);
            $stmt_detalles->execute([$nro_fact]);
            $detalles = $stmt_detalles->fetchAll(PDO::FETCH_ASSOC);

            foreach ($detalles as $detalle) {
                $sql_restaurar_stock = "UPDATE inv_prod_serv SET stock = stock + ? WHERE id_inv = ?";
                $stmt_restaurar_stock = $this->pdo->prepare($sql_restaurar_stock);
                $stmt_restaurar_stock->execute([$detalle['cantidad'], $detalle['id_inv']]);
            }

            $sql_anular = "UPDATE factura_venta SET status = 1 WHERE nro_fact = ?";
            $stmt_anular = $this->pdo->prepare($sql_anular);
            $result = $stmt_anular->execute([$nro_fact]);

            $this->pdo->commit();
            return $result;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Error en FacturaModel->anularFactura(): " . $e->getMessage());
            throw new Exception("Error al anular la factura: " . $e->getMessage());
        }
    }

    public function reactivarFactura($nro_fact) {
        try {
            $this->pdo->beginTransaction();

            $sql_verificar = "SELECT status FROM factura_venta WHERE nro_fact = ?";
            $stmt_verificar = $this->pdo->prepare($sql_verificar);
            $stmt_verificar->execute([$nro_fact]);
            $factura = $stmt_verificar->fetch(PDO::FETCH_ASSOC);

            if (!$factura) {
                throw new Exception("Factura no encontrada");
            }

            if ($factura['status'] == 0) {
                throw new Exception("La factura ya está activa");
            }

            $sql_detalles = "SELECT df.id_inv, df.cantidad, ps.tipo 
                            FROM detalle_factura df
                            LEFT JOIN inv_prod_serv ps ON df.id_inv = ps.id_inv
                            WHERE df.nro_fact = ? AND ps.tipo = 1";
            $stmt_detalles = $this->pdo->prepare($sql_detalles);
            $stmt_detalles->execute([$nro_fact]);
            $detalles = $stmt_detalles->fetchAll(PDO::FETCH_ASSOC);

            foreach ($detalles as $detalle) {
                $sql_reducir_stock = "UPDATE inv_prod_serv SET stock = stock - ? WHERE id_inv = ? AND stock >= ?";
                $stmt_reducir_stock = $this->pdo->prepare($sql_reducir_stock);
                $stmt_reducir_stock->execute([$detalle['cantidad'], $detalle['id_inv'], $detalle['cantidad']]);

                if ($stmt_reducir_stock->rowCount() === 0) {
                    throw new Exception("Stock insuficiente para reactivar la factura. Producto: " . $this->obtenerNombreProducto($detalle['id_inv']));
                }
            }

            $sql_reactivar = "UPDATE factura_venta SET status = 0 WHERE nro_fact = ?";
            $stmt_reactivar = $this->pdo->prepare($sql_reactivar);
            $result = $stmt_reactivar->execute([$nro_fact]);

            $this->pdo->commit();
            return $result;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Error en FacturaModel->reactivarFactura(): " . $e->getMessage());
            throw new Exception("Error al reactivar la factura: " . $e->getMessage());
        }
    }

    public function obtenerProductosDisponibles() {
        try {
            $sql = "SELECT 
                        ps.id_inv,
                        ps.nombre,
                        ps.costo,
                        ps.costo_mayor as precio_mayor,
                        ps.stock,
                        um.nombre as unidad_medida,
                        tp.nombre as presentacion
                    FROM inv_prod_serv ps
                    LEFT JOIN unidades_medida um ON ps.id_unidad_medida = um.id_unidad_medida
                    LEFT JOIN tipo_presentacion tp ON ps.id_pres = tp.id_pres
                    WHERE ps.tipo = 1
                    AND ps.stock > 0 AND ps.status = 0
                    ORDER BY ps.nombre";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en FacturaModel->obtenerProductosDisponibles(): " . $e->getMessage());
            return [];
        }
    }

    public function obtenerServiciosDisponibles() {
        try {
            $sql = "SELECT 
                        ps.id_inv,
                        ps.nombre,
                        ps.costo,
                        um.nombre as unidad_medida
                    FROM inv_prod_serv ps
                    LEFT JOIN unidades_medida um ON ps.id_unidad_medida = um.id_unidad_medida
                    WHERE ps.tipo = 2 AND ps.status = 0
                    ORDER BY ps.nombre";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en FacturaModel->obtenerServiciosDisponibles(): " . $e->getMessage());
            return [];
        }
    }

    public function obtenerCondicionesPago() {
        try {
            $sql = "SELECT * FROM condicion_pago WHERE id_condicion_pago != 4 AND status = 0 ORDER BY forma"; 
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en FacturaModel->obtenerCondicionesPago(): " . $e->getMessage());
            return [];
        }
    }

    public function obtenerDescuentos() {
        try {
            $sql = "SELECT * FROM descuento WHERE status = 0 ORDER BY porcentaje ASC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en FacturaModel->obtenerDescuentos(): " . $e->getMessage());
            return [];
        }
    }

    public function verificarStockDisponible($id_inv, $cantidad) {
        try {
            $sql = "SELECT tipo, stock FROM inv_prod_serv WHERE id_inv = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id_inv]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result && $result['tipo'] == 1) {
                return $result['stock'] >= $cantidad;
            }
            return true; 
        } catch (PDOException $e) {
            error_log("Error en FacturaModel->verificarStockDisponible(): " . $e->getMessage());
            return false;
        }
    }

    public function obtenerPrecioProducto($id_inv, $cantidad) {
        try {
            $sql = "SELECT costo, costo_mayor FROM inv_prod_serv WHERE id_inv = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id_inv]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                return $cantidad >= 20 ? $result['costo_mayor'] : $result['costo'];
            }
            return 0;
        } catch (PDOException $e) {
            error_log("Error en FacturaModel->obtenerPrecioProducto(): " . $e->getMessage());
            return 0;
        }
    }
}
?>