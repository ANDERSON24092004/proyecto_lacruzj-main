<?php
require_once 'modelo/conexion.php';

class CuentasCobrarModel extends Conexion {
    
    public function listarFacturasCredito() {
    try {
        $sql = "SELECT 
                    fv.nro_fact,
                    fv.rif,
                    c.razon_social,
                    fv.total_general,
                    fv.fecha as fecha_factura,
                    fc.duracion as fecha_limite,
                    fc.id as id_cuenta_cobrar,
                    fc.pago as total_abonado,
                    fc.fecha_pago,
                    fc.estado_pago,
                    (fv.total_general - COALESCE(fc.pago, 0)) as saldo_pendiente,
                    CASE 
                        WHEN fc.estado_pago = 1 THEN 'Pagada'
                        WHEN CURDATE() > fc.duracion THEN 'Vencida'
                        ELSE 'Pendiente'
                    END as estado_visual,
                    fv.status as vigencia_factura,
                    fv.numero_orden,
                    fv.total_iva
                FROM factura_venta fv
                INNER JOIN cliente c ON fv.rif = c.rif AND c.status = 0
                INNER JOIN factura_cobrar fc ON fv.nro_fact = fc.id_facturas
                WHERE fv.id_condicion_pago = 2 
                AND fc.estado_pago = 0
                ORDER BY 
                    CASE 
                        WHEN CURDATE() > fc.duracion THEN 0
                        ELSE 1
                    END,
                    fc.duracion ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log("Error en CuentasCobrarModel->listarFacturasCredito(): " . $e->getMessage());
        return [];
    }
    }
    public function obtenerDetallesCuenta($nro_fact) {
    try {
        $sql = "SELECT 
                fv.nro_fact,
                fv.rif,
                c.razon_social,
                c.telefono,
                c.correo,
                c.direccion,
                fv.total_general,
                fv.fecha as fecha_factura,
                fc.id as id_cuenta_cobrar,
                fc.duracion as fecha_limite,
                fc.pago as total_abonado,
                fc.fecha_pago,
                fc.estado_pago,
                (fv.total_general - COALESCE(fc.pago, 0)) as saldo_pendiente,
                CASE 
                    WHEN fc.estado_pago = 1 THEN 'Pagada'
                    WHEN CURDATE() > fc.duracion THEN 'Vencida'
                    ELSE 'Pendiente'
                END as estado_visual,
                fv.numero_orden,
                fv.total_iva,
                fv.status as vigencia_factura,
                cp.forma as condicion_pago
            FROM factura_venta fv
            INNER JOIN cliente c ON fv.rif = c.rif AND c.status = 0
            INNER JOIN factura_cobrar fc ON fv.nro_fact = fc.id_facturas
            INNER JOIN condicion_pago cp ON fv.id_condicion_pago = cp.id_condicion_pago
            WHERE fv.nro_fact = ?";  
            
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$nro_fact]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log("Error en CuentasCobrarModel->obtenerDetallesCuenta(): " . $e->getMessage());
        return null;
    }
    }

    public function registrarPagoCompleto($nro_fact, $fecha_pago) {
        try {
            $this->pdo->beginTransaction();

            $sql_datos = "SELECT fc.pago, fv.total_general, fc.duracion, fc.status, fc.estado_pago
                         FROM factura_cobrar fc
                         INNER JOIN factura_venta fv ON fc.id_facturas = fv.nro_fact
                         WHERE fc.id_facturas = ? AND fc.status = 0";
            $stmt_datos = $this->pdo->prepare($sql_datos);
            $stmt_datos->execute([$nro_fact]);
            $datos = $stmt_datos->fetch(PDO::FETCH_ASSOC);

            if (!$datos) {
                throw new Exception("Factura de crédito no encontrada");
            }

            if ($datos['estado_pago'] == 1) {
                throw new Exception("La factura ya está pagada completamente");
            }

            if ($datos['status'] == 1) {
                throw new Exception("No se puede pagar una factura anulada");
            }

            $sql_actualizar = "UPDATE factura_cobrar 
                              SET pago = ?, 
                                  fecha_pago = ?,
                                  estado_pago = 1
                              WHERE id_facturas = ? AND status = 0";
            $stmt_actualizar = $this->pdo->prepare($sql_actualizar);
            $stmt_actualizar->execute([
                $datos['total_general'], 
                $fecha_pago,
                $nro_fact
            ]);

            $this->pdo->commit();
            
            return [
                'success' => true,
                'monto_pagado' => $datos['total_general'],
                'fecha_pago' => $fecha_pago
            ];

        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Error en CuentasCobrarModel->registrarPagoCompleto(): " . $e->getMessage());
            throw new Exception("Error al registrar pago: " . $e->getMessage());
        }
    }

    public function anularCuenta($nro_fact) {
        try {
            $this->pdo->beginTransaction();

            $sql_verificar = "SELECT fv.status, fc.duracion, fc.estado_pago
                            FROM factura_venta fv
                            INNER JOIN factura_cobrar fc ON fv.nro_fact = fc.id_facturas
                            WHERE fv.nro_fact = ?";
            $stmt_verificar = $this->pdo->prepare($sql_verificar);
            $stmt_verificar->execute([$nro_fact]);
            $cuenta = $stmt_verificar->fetch(PDO::FETCH_ASSOC);

            if (!$cuenta) {
                throw new Exception("Cuenta no encontrada");
            }

            if ($cuenta['status'] == 1) {
                throw new Exception("La cuenta ya está anulada");
            }

            if ($cuenta['estado_pago'] == 1) {
                throw new Exception("No se puede anular una cuenta ya pagada");
            }

            $sql_anular_factura = "UPDATE factura_venta SET status = 1 WHERE nro_fact = ?";
            $stmt_anular_factura = $this->pdo->prepare($sql_anular_factura);
            $stmt_anular_factura->execute([$nro_fact]);

            $sql_anular_cuenta = "UPDATE factura_cobrar SET status = 1 WHERE id_facturas = ?";
            $stmt_anular_cuenta = $this->pdo->prepare($sql_anular_cuenta);
            $stmt_anular_cuenta->execute([$nro_fact]);

            $this->pdo->commit();
            return true;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Error en CuentasCobrarModel->anularCuenta(): " . $e->getMessage());
            throw new Exception("Error al anular la cuenta: " . $e->getMessage());
        }
    }

    public function reactivarCuenta($nro_fact) {
        try {
            $this->pdo->beginTransaction();

            $sql_verificar = "SELECT fv.status, fc.duracion, fc.estado_pago
                            FROM factura_venta fv
                            INNER JOIN factura_cobrar fc ON fv.nro_fact = fc.id_facturas
                            WHERE fv.nro_fact = ?";
            $stmt_verificar = $this->pdo->prepare($sql_verificar);
            $stmt_verificar->execute([$nro_fact]);
            $cuenta = $stmt_verificar->fetch(PDO::FETCH_ASSOC);

            if (!$cuenta) {
                throw new Exception("Cuenta no encontrada");
            }

            if ($cuenta['status'] == 0) {
                throw new Exception("La cuenta ya está activa");
            }

            if ($cuenta['estado_pago'] == 1) {
                throw new Exception("No se puede reactivar una cuenta ya pagada");
            }

            $sql_reactivar_factura = "UPDATE factura_venta SET status = 0 WHERE nro_fact = ?";
            $stmt_reactivar_factura = $this->pdo->prepare($sql_reactivar_factura);
            $stmt_reactivar_factura->execute([$nro_fact]);

            $sql_reactivar_cuenta = "UPDATE factura_cobrar SET status = 0 WHERE id_facturas = ?";
            $stmt_reactivar_cuenta = $this->pdo->prepare($sql_reactivar_cuenta);
            $stmt_reactivar_cuenta->execute([$nro_fact]);

            $this->pdo->commit();
            return true;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Error en CuentasCobrarModel->reactivarCuenta(): " . $e->getMessage());
            throw new Exception("Error al reactivar la cuenta: " . $e->getMessage());
        }
    }

    public function obtenerDetallesFactura($nro_fact) {
        try {
            $sql = "SELECT 
                        df.`id-det` as id_detalle,
                        df.nro_fact,
                        df.id_inv,
                        ips.nombre,
                        ips.tipo,
                        df.cantidad,
                        CASE 
                            WHEN ips.tipo = 1 THEN ips.costo_mayor
                            WHEN ips.tipo = 2 THEN ips.costo
                            ELSE 0
                        END as precio_unitario,
                        CASE 
                            WHEN ips.tipo = 1 THEN (df.cantidad * ips.costo_mayor)
                            WHEN ips.tipo = 2 THEN (df.cantidad * ips.costo)
                            ELSE 0
                        END as subtotal,
                        tpres.nombre as presentacion,
                        um.nombre as unidad_medida
                    FROM detalle_factura df
                    INNER JOIN inv_prod_serv ips ON df.id_inv = ips.id_inv WHERE ips.status = 0
                    LEFT JOIN tipo_presentacion tpres ON ips.id_pres = tpres.id_pres AND tpres.status = 0
                    LEFT JOIN unidades_medida um ON ips.id_unidad_medida = um.id_unidad_medida
                    WHERE df.nro_fact = ?
                    ORDER BY ips.tipo, ips.nombre";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$nro_fact]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error en CuentasCobrarModel->obtenerDetallesFactura(): " . $e->getMessage());
            return [];
        }
    }
}
?>