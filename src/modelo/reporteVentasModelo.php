<?php
namespace src\modelo;
use src\modelo\conexion;
use PDO;
use PDOException;
use Exception;

class reporteVentasModelo extends conexion {
    
    public function obtenerVentasPorDia($fecha = null) {
        try {
            if (!$fecha) {
                $fecha = date('Y-m-d');
            }
            
            $sql = "SELECT 
                        fv.nro_fact,
                        fv.fecha,
                        c.razon_social,
                        ps.nombre as producto_servicio,
                        CASE 
                            WHEN ps.tipo = 1 THEN 'Producto'
                            ELSE 'Servicio'
                        END as tipo,
                        df.cantidad,
                        CASE 
                            WHEN df.cantidad >= 20 THEN ps.costo_mayor
                            ELSE ps.costo
                        END as precio_unitario,
                        (df.cantidad * CASE 
                            WHEN df.cantidad >= 20 THEN ps.costo_mayor
                            ELSE ps.costo
                        END) as subtotal,
                        fv.total_iva,
                        fv.total_general
                    FROM factura_venta fv
                    INNER JOIN detalle_factura df ON fv.nro_fact = df.nro_fact
                    INNER JOIN inv_prod_serv ps ON df.id_inv = ps.id_inv
                    INNER JOIN cliente c ON fv.rif = c.rif
                    WHERE DATE(fv.fecha) = ?
                    AND fv.status = 0
                    ORDER BY fv.fecha DESC, fv.nro_fact DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$fecha]);
            $ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $totalVentas = 0;
            $totalIva = 0;
            $totalSubtotal = 0;
            foreach ($ventas as $venta) {
                $totalVentas += $venta['total_general'];
                $totalIva += $venta['total_iva'];
                $totalSubtotal += $venta['subtotal'];
            }
            
            return [
                'ventas' => $ventas,
                'total_ventas' => $totalVentas,
                'total_iva' => $totalIva,
                'total_subtotal' => $totalSubtotal,
                'fecha' => $fecha,
                'total_registros' => count($ventas)
            ];
            
        } catch (PDOException $e) {
            error_log("Error en ReporteVentasModelo->obtenerVentasPorDia(): " . $e->getMessage());
            throw new Exception("Error al obtener ventas del día: " . $e->getMessage());
        }
    }
    
    public function obtenerVentasPorSemana($fechaInicio = null, $fechaFin = null) {
        try {
            if (!$fechaInicio || !$fechaFin) {
                $fechaInicio = date('Y-m-d', strtotime('monday this week'));
                $fechaFin = date('Y-m-d', strtotime('sunday this week'));
            }
            
            $sql = "SELECT 
                        fv.nro_fact,
                        fv.fecha,
                        c.razon_social,
                        ps.nombre as producto_servicio,
                        CASE 
                            WHEN ps.tipo = 1 THEN 'Producto'
                            ELSE 'Servicio'
                        END as tipo,
                        df.cantidad,
                        CASE 
                            WHEN df.cantidad >= 20 THEN ps.costo_mayor
                            ELSE ps.costo
                        END as precio_unitario,
                        (df.cantidad * CASE 
                            WHEN df.cantidad >= 20 THEN ps.costo_mayor
                            ELSE ps.costo
                        END) as subtotal,
                        fv.total_iva,
                        fv.total_general
                    FROM factura_venta fv
                    INNER JOIN detalle_factura df ON fv.nro_fact = df.nro_fact
                    INNER JOIN inv_prod_serv ps ON df.id_inv = ps.id_inv
                    INNER JOIN cliente c ON fv.rif = c.rif
                    WHERE fv.fecha BETWEEN ? AND ?
                    AND fv.status = 0
                    ORDER BY fv.fecha DESC, fv.nro_fact DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$fechaInicio, $fechaFin]);
            $ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $totalVentas = 0;
            $totalIva = 0;
            $totalSubtotal = 0;
            foreach ($ventas as $venta) {
                $totalVentas += $venta['total_general'];
                $totalIva += $venta['total_iva'];
                $totalSubtotal += $venta['subtotal'];
            }
            
            return [
                'ventas' => $ventas,
                'total_ventas' => $totalVentas,
                'total_iva' => $totalIva,
                'total_subtotal' => $totalSubtotal,
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,
                'total_registros' => count($ventas)
            ];
            
        } catch (PDOException $e) {
            error_log("Error en ReporteVentasModelo->obtenerVentasPorSemana(): " . $e->getMessage());
            throw new Exception("Error al obtener ventas de la semana: " . $e->getMessage());
        }
    }
    
    public function obtenerVentasPorMes($mes = null, $anio = null) {
        try {
            if (!$mes) $mes = date('m');
            if (!$anio) $anio = date('Y');
            
            $sql = "SELECT 
                        fv.nro_fact,
                        fv.fecha,
                        c.razon_social,
                        ps.nombre as producto_servicio,
                        CASE 
                            WHEN ps.tipo = 1 THEN 'Producto'
                            ELSE 'Servicio'
                        END as tipo,
                        df.cantidad,
                        CASE 
                            WHEN df.cantidad >= 20 THEN ps.costo_mayor
                            ELSE ps.costo
                        END as precio_unitario,
                        (df.cantidad * CASE 
                            WHEN df.cantidad >= 20 THEN ps.costo_mayor
                            ELSE ps.costo
                        END) as subtotal,
                        fv.total_iva,
                        fv.total_general
                    FROM factura_venta fv
                    INNER JOIN detalle_factura df ON fv.nro_fact = df.nro_fact
                    INNER JOIN inv_prod_serv ps ON df.id_inv = ps.id_inv
                    INNER JOIN cliente c ON fv.rif = c.rif
                    WHERE MONTH(fv.fecha) = ? 
                    AND YEAR(fv.fecha) = ?
                    AND fv.status = 0
                    ORDER BY fv.fecha DESC, fv.nro_fact DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$mes, $anio]);
            $ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $totalVentas = 0;
            $totalIva = 0;
            $totalSubtotal = 0;
            foreach ($ventas as $venta) {
                $totalVentas += $venta['total_general'];
                $totalIva += $venta['total_iva'];
                $totalSubtotal += $venta['subtotal'];
            }
            
            return [
                'ventas' => $ventas,
                'total_ventas' => $totalVentas,
                'total_iva' => $totalIva,
                'total_subtotal' => $totalSubtotal,
                'mes' => $mes,
                'anio' => $anio,
                'total_registros' => count($ventas)
            ];
            
        } catch (PDOException $e) {
            error_log("Error en ReporteVentasModelo->obtenerVentasPorMes(): " . $e->getMessage());
            throw new Exception("Error al obtener ventas del mes: " . $e->getMessage());
        }
    }
    
    public function obtenerVentasPorAnio($anio = null) {
        try {
            if (!$anio) $anio = date('Y');
            
            $sql = "SELECT 
                        fv.nro_fact,
                        fv.fecha,
                        c.razon_social,
                        ps.nombre as producto_servicio,
                        CASE 
                            WHEN ps.tipo = 1 THEN 'Producto'
                            ELSE 'Servicio'
                        END as tipo,
                        df.cantidad,
                        CASE 
                            WHEN df.cantidad >= 20 THEN ps.costo_mayor
                            ELSE ps.costo
                        END as precio_unitario,
                        (df.cantidad * CASE 
                            WHEN df.cantidad >= 20 THEN ps.costo_mayor
                            ELSE ps.costo
                        END) as subtotal,
                        fv.total_iva,
                        fv.total_general
                    FROM factura_venta fv
                    INNER JOIN detalle_factura df ON fv.nro_fact = df.nro_fact
                    INNER JOIN inv_prod_serv ps ON df.id_inv = ps.id_inv
                    INNER JOIN cliente c ON fv.rif = c.rif
                    WHERE YEAR(fv.fecha) = ?
                    AND fv.status = 0
                    ORDER BY fv.fecha DESC, fv.nro_fact DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$anio]);
            $ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);
          
            $totalVentas = 0;
            $totalIva = 0;
            $totalSubtotal = 0;
            foreach ($ventas as $venta) {
                $totalVentas += $venta['total_general'];
                $totalIva += $venta['total_iva'];
                $totalSubtotal += $venta['subtotal'];
            }
            
            return [
                'ventas' => $ventas,
                'total_ventas' => $totalVentas,
                'total_iva' => $totalIva,
                'total_subtotal' => $totalSubtotal,
                'anio' => $anio,
                'total_registros' => count($ventas)
            ];
            
        } catch (PDOException $e) {
            error_log("Error en ReporteVentasModelo->obtenerVentasPorAnio(): " . $e->getMessage());
            throw new Exception("Error al obtener ventas del año: " . $e->getMessage());
        }
    }

    public function obtenerVentasParametrizadas($tipoProducto, $idItem, $periodo, $fechaDesde, $fechaHasta, $mes, $anio) {
        try {
            $sql = "SELECT 
                        fv.nro_fact,
                        fv.fecha,
                        c.razon_social,
                        ps.nombre as producto_servicio,
                        ps.id_inv,
                        CASE 
                            WHEN ps.tipo = 1 THEN 'Producto'
                            ELSE 'Servicio'
                        END as tipo,
                        df.cantidad,
                        CASE 
                            WHEN df.cantidad >= 20 THEN ps.costo_mayor
                            ELSE ps.costo
                        END as precio_unitario,
                        (df.cantidad * CASE 
                            WHEN df.cantidad >= 20 THEN ps.costo_mayor
                            ELSE ps.costo
                        END) as subtotal,
                        fv.total_iva,
                        fv.total_general
                    FROM factura_venta fv
                    INNER JOIN detalle_factura df ON fv.nro_fact = df.nro_fact
                    INNER JOIN inv_prod_serv ps ON df.id_inv = ps.id_inv
                    INNER JOIN cliente c ON fv.rif = c.rif
                    WHERE fv.status = 0";
            
            $params = [];
            
            if ($tipoProducto === 'productos') {
                $sql .= " AND ps.tipo = 1";
            } elseif ($tipoProducto === 'servicios') {
                $sql .= " AND ps.tipo = 2";
            } elseif ($tipoProducto === 'especifico' && $idItem) {
                $sql .= " AND ps.id_inv = ?";
                $params[] = $idItem;
            }
    
            switch ($periodo) {
                case 'dia':
                    $sql .= " AND DATE(fv.fecha) = ?";
                    $params[] = date('Y-m-d');
                    break;
                case 'semana':
                    $fechaInicioSemana = date('Y-m-d', strtotime('monday this week'));
                    $fechaFinSemana = date('Y-m-d', strtotime('sunday this week'));
                    $sql .= " AND fv.fecha BETWEEN ? AND ?";
                    $params[] = $fechaInicioSemana;
                    $params[] = $fechaFinSemana;
                    break;
                case 'mes':
                    if ($mes && $anio) {
                        $sql .= " AND MONTH(fv.fecha) = ? AND YEAR(fv.fecha) = ?";
                        $params[] = $mes;
                        $params[] = $anio;
                    }
                    break;
                case 'anio':
                    if ($anio) {
                        $sql .= " AND YEAR(fv.fecha) = ?";
                        $params[] = $anio;
                    }
                    break;
                case 'personalizado':
                    if ($fechaDesde && $fechaHasta) {
                        $sql .= " AND fv.fecha BETWEEN ? AND ?";
                        $params[] = $fechaDesde;
                        $params[] = $fechaHasta;
                    }
                    break;
            }
            
            $sql .= " ORDER BY fv.fecha DESC, fv.nro_fact DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $totalVentas = 0;
            $totalIva = 0;
            $totalSubtotal = 0;
            
            foreach ($ventas as $venta) {
                $totalVentas += $venta['total_general'];
                $totalIva += $venta['total_iva'];
                $totalSubtotal += $venta['subtotal'];
            }
            
            return [
                'ventas' => $ventas,
                'total_ventas' => $totalVentas,
                'total_iva' => $totalIva,
                'total_subtotal' => $totalSubtotal,
                'filtros' => [
                    'tipo_producto' => $tipoProducto,
                    'id_item' => $idItem,
                    'periodo' => $periodo,
                    'fecha_desde' => $fechaDesde,
                    'fecha_hasta' => $fechaHasta,
                    'mes' => $mes,
                    'anio' => $anio
                ],
                'total_registros' => count($ventas)
            ];
            
        } catch (PDOException $e) {
            error_log("Error en ReporteVentasModelo->obtenerVentasParametrizadas(): " . $e->getMessage());
            throw new Exception("Error al obtener ventas parametrizadas: " . $e->getMessage());
        }
    }

    public function obtenerItemsParaFiltro($tipo = 'todos') {
        try {
            $sql = "SELECT id_inv as id, nombre, tipo 
                    FROM inv_prod_serv 
                    WHERE status = 0";
            
            if ($tipo === 'productos') {
                $sql .= " AND tipo = 1";
            } elseif ($tipo === 'servicios') {
                $sql .= " AND tipo = 2";
            } elseif ($tipo === 'especifico') {
                $sql .= " AND (tipo = 1 OR tipo = 2)";
            }
            
            $sql .= " ORDER BY nombre ASC";
            
            error_log("Ejecutando consulta: " . $sql);
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            error_log("Items encontrados: " . count($items));
            
            return is_array($items) ? $items : [];
            
        } catch (PDOException $e) {
            error_log("Error en obtenerItemsParaFiltro(): " . $e->getMessage());
            return [];
        }
    }

    public function obtenerNombreItem($idItem) {
        try {
            $sql = "SELECT nombre FROM inv_prod_serv WHERE id_inv = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$idItem]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['nombre'] : 'Item no encontrado';
        } catch (PDOException $e) {
            error_log("Error en ReporteVentasModelo->obtenerNombreItem(): " . $e->getMessage());
            return 'Error al obtener nombre';
        }
    }
}
?>