<?php
namespace src\modelo;
use src\modelo\conexion;
use PDO;
use PDOException;
use Exception;


class presupuestoModelo extends conexion {
    private $nro_presupuesto;
    private $rif;
    private $numero_orden;
    private $total_iva;
    private $total_general;
    private $fecha;
    private $status;
    private $detalles = [];
    private $id_descuento;
    private $id_iva;

    public function getNroPresupuesto() { return $this->nro_presupuesto; }
    public function setNroPresupuesto($nro_presupuesto) { $this->nro_presupuesto = $nro_presupuesto; return $this; }

    public function getRif() { return $this->rif; }
    public function setRif($rif) { $this->rif = $rif; return $this; }

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

    public function getIdIva() { return $this->id_iva; }
    public function setIdIva($id_iva) { $this->id_iva = $id_iva; return $this; }

    public function validarDatosPresupuesto($datos) {
        $errores = [];

        if (empty($datos['rif'])) {
            $errores[] = "El RIF del cliente es obligatorio";
        }

        if (empty($datos['fecha'])) {
            $errores[] = "La fecha es obligatoria";
        }

        if (!isset($datos['detalles']) || empty($datos['detalles'])) {
            $errores[] = "Debe agregar al menos un producto/servicio al presupuesto";
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

    public function registrarPresupuesto() {
        try {
            $this->pdo->beginTransaction();

            $sql_presupuesto = "INSERT INTO factura_venta (rif, id_condicion_pago, id_descuento, numero_orden, total_iva, total_general, fecha, status) 
                               VALUES (?, 4, ?, ?, ?, ?, ?, ?)";
            $stmt_presupuesto = $this->pdo->prepare($sql_presupuesto);
            
            $id_descuento = $this->id_descuento ?: null;
            
            $stmt_presupuesto->execute([
                $this->rif,
                $id_descuento,
                $this->numero_orden,
                $this->total_iva,
                $this->total_general,
                $this->fecha,
                0 
            ]);

            $nro_presupuesto = $this->pdo->lastInsertId();

            if (!$nro_presupuesto || $nro_presupuesto == 0) {
                throw new Exception("No se pudo obtener el ID del presupuesto creado");
            }

            if ($this->id_iva) {
                $sql_iva = "INSERT INTO iva_factura (id_iva, id_factura, fecha_cambio) VALUES (?, ?, ?)";
                $stmt_iva = $this->pdo->prepare($sql_iva);
                $stmt_iva->execute([$this->id_iva, $nro_presupuesto, date('Y-m-d')]);
            }

            foreach ($this->detalles as $detalle) {
                $sql_verificar = "SELECT id_inv FROM inv_prod_serv WHERE id_inv = ? AND status = 0";
                $stmt_verificar = $this->pdo->prepare($sql_verificar);
                $stmt_verificar->execute([$detalle['id_inv']]);
                $existe = $stmt_verificar->fetch(PDO::FETCH_ASSOC);

                if (!$existe) {
                    throw new Exception("El producto/servicio con ID {$detalle['id_inv']} no existe o está inactivo");
                }

                $sql_detalle = "INSERT INTO detalle_factura (nro_fact, id_inv, cantidad) 
                               VALUES (?, ?, ?)";
                $stmt_detalle = $this->pdo->prepare($sql_detalle);
                $stmt_detalle->execute([
                    $nro_presupuesto,
                    $detalle['id_inv'],
                    $detalle['cantidad']
                ]);

                error_log("Insertado detalle: Presupuesto #$nro_presupuesto, Producto: {$detalle['id_inv']}, Cantidad: {$detalle['cantidad']}");
            }

            $this->pdo->commit();
            return $nro_presupuesto;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Error en PresupuestoModel->registrarPresupuesto(): " . $e->getMessage());
            throw new Exception("Error al registrar el presupuesto: " . $e->getMessage());
        }
    }

    public function listarPresupuestos() {
        try {
            $sql = "SELECT 
                        fv.nro_fact as nro_presupuesto,
                        fv.rif,
                        c.razon_social,
                        fv.numero_orden,
                        fv.total_iva,
                        fv.total_general,
                        fv.fecha,
                        fv.status,
                        CASE 
                            WHEN fv.status = 0 THEN 'Vigente'
                            ELSE 'Anulado'
                        END as estado
                    FROM factura_venta fv
                    LEFT JOIN cliente c ON fv.rif = c.rif
                    WHERE fv.id_condicion_pago = 4 
                    ORDER BY fv.fecha DESC, fv.nro_fact DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en PresupuestoModel->listarPresupuestos(): " . $e->getMessage());
            return [];
        }
    }

    public function obtenerDetallesPresupuestoCompleto($nro_presupuesto) {
        try {
            $sql_presupuesto = "SELECT 
                        fv.nro_fact as nro_presupuesto,
                        fv.rif,
                        fv.numero_orden,
                        fv.total_iva,
                        fv.total_general,
                        fv.fecha,
                        fv.status,
                        c.razon_social,
                        c.telefono,
                        c.correo,
                        c.direccion,
                        d.porcentaje as descuento_porcentaje,
                        i.porcentaje as iva_porcentaje
                    FROM factura_venta fv
                    LEFT JOIN cliente c ON fv.rif = c.rif
                    LEFT JOIN descuento d ON fv.id_descuento = d.id
                    LEFT JOIN iva_factura ifa ON fv.nro_fact = ifa.id_factura
                    LEFT JOIN iva i ON ifa.id_iva = i.id_iva
                    WHERE fv.nro_fact = ? AND fv.id_condicion_pago = 4";
            
            $stmt_presupuesto = $this->pdo->prepare($sql_presupuesto);
            $stmt_presupuesto->execute([$nro_presupuesto]);
            $presupuesto = $stmt_presupuesto->fetch(PDO::FETCH_ASSOC);
            
            if (!$presupuesto) {
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
            $stmt_detalles->execute([$nro_presupuesto]);
            $detalles = $stmt_detalles->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($detalles as &$detalle) {
                $precio = $detalle['cantidad'] >= 20 ? $detalle['costo_mayor'] : $detalle['costo'];
                $detalle['precio_unitario'] = $precio;
                $detalle['subtotal'] = $precio * $detalle['cantidad'];
            }
            
            return [
                'presupuesto' => $presupuesto,
                'detalles' => $detalles
            ];
            
        } catch (PDOException $e) {
            error_log("Error en PresupuestoModel->obtenerDetallesPresupuestoCompleto(): " . $e->getMessage());
            return null;
        }
    }

    public function anularPresupuesto($nro_presupuesto) {
        try {
            $sql_verificar = "SELECT status FROM factura_venta WHERE nro_fact = ? AND id_condicion_pago = 4";
            $stmt_verificar = $this->pdo->prepare($sql_verificar);
            $stmt_verificar->execute([$nro_presupuesto]);
            $presupuesto = $stmt_verificar->fetch(PDO::FETCH_ASSOC);

            if (!$presupuesto) {
                throw new Exception("Presupuesto no encontrado");
            }

            if ($presupuesto['status'] == 1) {
                throw new Exception("El presupuesto ya está anulado");
            }

            $sql_anular = "UPDATE factura_venta SET status = 1 WHERE nro_fact = ? AND id_condicion_pago = 4";
            $stmt_anular = $this->pdo->prepare($sql_anular);
            $result = $stmt_anular->execute([$nro_presupuesto]);

            return $result;

        } catch (Exception $e) {
            error_log("Error en PresupuestoModel->anularPresupuesto(): " . $e->getMessage());
            throw new Exception("Error al anular el presupuesto: " . $e->getMessage());
        }
    }

    public function reactivarPresupuesto($nro_presupuesto) {
        try {
            $sql_verificar = "SELECT status FROM factura_venta WHERE nro_fact = ? AND id_condicion_pago = 4";
            $stmt_verificar = $this->pdo->prepare($sql_verificar);
            $stmt_verificar->execute([$nro_presupuesto]);
            $presupuesto = $stmt_verificar->fetch(PDO::FETCH_ASSOC);

            if (!$presupuesto) {
                throw new Exception("Presupuesto no encontrado");
            }

            if ($presupuesto['status'] == 0) {
                throw new Exception("El presupuesto ya está activo");
            }

            $sql_reactivar = "UPDATE factura_venta SET status = 0 WHERE nro_fact = ? AND id_condicion_pago = 4";
            $stmt_reactivar = $this->pdo->prepare($sql_reactivar);
            $result = $stmt_reactivar->execute([$nro_presupuesto]);

            return $result;

        } catch (Exception $e) {
            error_log("Error en PresupuestoModel->reactivarPresupuesto(): " . $e->getMessage());
            throw new Exception("Error al reactivar el presupuesto: " . $e->getMessage());
        }
    }

    public function obtenerProductosDisponibles() {
        try {
            $sql = "SELECT 
                    ps.id_inv,
                    ps.nombre,
                    ps.costo,
                    ps.costo_mayor,
                    ps.stock,
                    um.nombre as unidad_medida,
                    tp.nombre as presentacion,
                    ps.tipo
                FROM inv_prod_serv ps
                LEFT JOIN unidades_medida um ON ps.id_unidad_medida = um.id_unidad_medida
                LEFT JOIN tipo_presentacion tp ON ps.id_pres = tp.id_pres
                WHERE ps.tipo = 1 AND ps.status = 0
                ORDER BY ps.nombre";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en PresupuestoModel->obtenerProductosDisponibles(): " . $e->getMessage());
            return [];
        }
    }

    public function obtenerServiciosDisponibles() {
        try {
            $sql = "SELECT 
                    ps.id_inv,
                    ps.nombre,
                    ps.costo,
                    ps.costo_mayor,
                    um.nombre as unidad_medida,
                    tp.nombre as presentacion,
                    ps.tipo
                FROM inv_prod_serv ps
                LEFT JOIN unidades_medida um ON ps.id_unidad_medida = um.id_unidad_medida
                LEFT JOIN tipo_presentacion tp ON ps.id_pres = tp.id_pres
                WHERE ps.tipo = 2 AND ps.status = 0
                ORDER BY ps.nombre";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en PresupuestoModel->obtenerServiciosDisponibles(): " . $e->getMessage());
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
            error_log("Error en PresupuestoModel->obtenerDescuentos(): " . $e->getMessage());
            return [];
        }
    }

    public function obtenerPrecioProducto($id_inv, $cantidad) {
        try {
            $sql = "SELECT costo, costo_mayor FROM inv_prod_serv WHERE id_inv = ? AND status = 0";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id_inv]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                return $cantidad >= 20 ? $result['costo_mayor'] : $result['costo'];
            }
            return 0;
        } catch (PDOException $e) {
            error_log("Error en PresupuestoModel->obtenerPrecioProducto(): " . $e->getMessage());
            return 0;
        }
    }
}
?>