<?php
require_once 'modelo/conexion.php';

class ReporteComprasModelo extends Conexion {
    
    public function obtenerComprasPorDia($fecha = null) {
        try {
            if (!$fecha) {
                $fecha = date('Y-m-d');
            }
            
            $sql = "SELECT 
                        fc.id_fact_com,
                        fc.num_factura,
                        fc.fecha,
                        p.nombre as proveedor,
                        mp.nombre as materia_prima,
                        dfc.cantidad,
                        dfc.costo_compra as precio_unitario,
                        (dfc.cantidad * dfc.costo_compra) as subtotal,
                        fc.total_iva,
                        fc.total_general
                    FROM factura_compra fc
                    INNER JOIN detalle_fact_compra dfc ON fc.id_fact_com = dfc.id_fact_com
                    INNER JOIN inv_materia_prima mp ON dfc.id_materia_prima = mp.id_materia
                    INNER JOIN proveedores p ON fc.id_proveedor = p.id_proveedores
                    WHERE DATE(fc.fecha) = ?
                    AND fc.status = 0
                    ORDER BY fc.fecha DESC, fc.id_fact_com DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$fecha]);
            $compras = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $totalCompras = 0;
            $totalIva = 0;
            $totalSubtotal = 0;
            foreach ($compras as $compra) {
                $totalCompras += $compra['total_general'];
                $totalIva += $compra['total_iva'];
                $totalSubtotal += $compra['subtotal'];
            }
            
            return [
                'compras' => $compras,
                'total_compras' => $totalCompras,
                'total_iva' => $totalIva,
                'total_subtotal' => $totalSubtotal,
                'fecha' => $fecha,
                'total_registros' => count($compras)
            ];
            
        } catch (PDOException $e) {
            error_log("Error en ReporteComprasModelo->obtenerComprasPorDia(): " . $e->getMessage());
            throw new Exception("Error al obtener compras del día: " . $e->getMessage());
        }
    }
    
    public function obtenerComprasPorSemana($fechaInicio = null, $fechaFin = null) {
        try {
            if (!$fechaInicio || !$fechaFin) {
                $fechaInicio = date('Y-m-d', strtotime('monday this week'));
                $fechaFin = date('Y-m-d', strtotime('sunday this week'));
            }
            
            $sql = "SELECT 
                        fc.id_fact_com,
                        fc.num_factura,
                        fc.fecha,
                        p.nombre as proveedor,
                        mp.nombre as materia_prima,
                        dfc.cantidad,
                        dfc.costo_compra as precio_unitario,
                        (dfc.cantidad * dfc.costo_compra) as subtotal,
                        fc.total_iva,
                        fc.total_general
                    FROM factura_compra fc
                    INNER JOIN detalle_fact_compra dfc ON fc.id_fact_com = dfc.id_fact_com
                    INNER JOIN inv_materia_prima mp ON dfc.id_materia_prima = mp.id_materia
                    INNER JOIN proveedores p ON fc.id_proveedor = p.id_proveedores
                    WHERE fc.fecha BETWEEN ? AND ?
                    AND fc.status = 0
                    ORDER BY fc.fecha DESC, fc.id_fact_com DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$fechaInicio, $fechaFin]);
            $compras = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $totalCompras = 0;
            $totalIva = 0;
            $totalSubtotal = 0;
            foreach ($compras as $compra) {
                $totalCompras += $compra['total_general'];
                $totalIva += $compra['total_iva'];
                $totalSubtotal += $compra['subtotal'];
            }
            
            return [
                'compras' => $compras,
                'total_compras' => $totalCompras,
                'total_iva' => $totalIva,
                'total_subtotal' => $totalSubtotal,
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,
                'total_registros' => count($compras)
            ];
            
        } catch (PDOException $e) {
            error_log("Error en ReporteComprasModelo->obtenerComprasPorSemana(): " . $e->getMessage());
            throw new Exception("Error al obtener compras de la semana: " . $e->getMessage());
        }
    }
    
    public function obtenerComprasPorMes($mes = null, $anio = null) {
        try {
            if (!$mes) $mes = date('m');
            if (!$anio) $anio = date('Y');
            
            $sql = "SELECT 
                        fc.id_fact_com,
                        fc.num_factura,
                        fc.fecha,
                        p.nombre as proveedor,
                        mp.nombre as materia_prima,
                        dfc.cantidad,
                        dfc.costo_compra as precio_unitario,
                        (dfc.cantidad * dfc.costo_compra) as subtotal,
                        fc.total_iva,
                        fc.total_general
                    FROM factura_compra fc
                    INNER JOIN detalle_fact_compra dfc ON fc.id_fact_com = dfc.id_fact_com
                    INNER JOIN inv_materia_prima mp ON dfc.id_materia_prima = mp.id_materia
                    INNER JOIN proveedores p ON fc.id_proveedor = p.id_proveedores
                    WHERE MONTH(fc.fecha) = ? 
                    AND YEAR(fc.fecha) = ?
                    AND fc.status = 0
                    ORDER BY fc.fecha DESC, fc.id_fact_com DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$mes, $anio]);
            $compras = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $totalCompras = 0;
            $totalIva = 0;
            $totalSubtotal = 0;
            foreach ($compras as $compra) {
                $totalCompras += $compra['total_general'];
                $totalIva += $compra['total_iva'];
                $totalSubtotal += $compra['subtotal'];
            }
            
            return [
                'compras' => $compras,
                'total_compras' => $totalCompras,
                'total_iva' => $totalIva,
                'total_subtotal' => $totalSubtotal,
                'mes' => $mes,
                'anio' => $anio,
                'total_registros' => count($compras)
            ];
            
        } catch (PDOException $e) {
            error_log("Error en ReporteComprasModelo->obtenerComprasPorMes(): " . $e->getMessage());
            throw new Exception("Error al obtener compras del mes: " . $e->getMessage());
        }
    }
    
    public function obtenerComprasPorAnio($anio = null) {
        try {
            if (!$anio) $anio = date('Y');
            
            $sql = "SELECT 
                        fc.id_fact_com,
                        fc.num_factura,
                        fc.fecha,
                        p.nombre as proveedor,
                        mp.nombre as materia_prima,
                        dfc.cantidad,
                        dfc.costo_compra as precio_unitario,
                        (dfc.cantidad * dfc.costo_compra) as subtotal,
                        fc.total_iva,
                        fc.total_general
                    FROM factura_compra fc
                    INNER JOIN detalle_fact_compra dfc ON fc.id_fact_com = dfc.id_fact_com
                    INNER JOIN inv_materia_prima mp ON dfc.id_materia_prima = mp.id_materia
                    INNER JOIN proveedores p ON fc.id_proveedor = p.id_proveedores
                    WHERE YEAR(fc.fecha) = ?
                    AND fc.status = 0
                    ORDER BY fc.fecha DESC, fc.id_fact_com DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$anio]);
            $compras = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $totalCompras = 0;
            $totalIva = 0;
            $totalSubtotal = 0;
            foreach ($compras as $compra) {
                $totalCompras += $compra['total_general'];
                $totalIva += $compra['total_iva'];
                $totalSubtotal += $compra['subtotal'];
            }
            
            return [
                'compras' => $compras,
                'total_compras' => $totalCompras,
                'total_iva' => $totalIva,
                'total_subtotal' => $totalSubtotal,
                'anio' => $anio,
                'total_registros' => count($compras)
            ];
            
        } catch (PDOException $e) {
            error_log("Error en ReporteComprasModelo->obtenerComprasPorAnio(): " . $e->getMessage());
            throw new Exception("Error al obtener compras del año: " . $e->getMessage());
        }
    }

    public function obtenerComprasParametrizadas($tipoMateria, $idMateria, $periodo, $fechaDesde, $fechaHasta, $mes, $anio) {
        try {
            $sql = "SELECT 
                        fc.id_fact_com,
                        fc.num_factura,
                        fc.fecha,
                        p.nombre as proveedor,
                        mp.nombre as materia_prima,
                        mp.id_materia,
                        dfc.cantidad,
                        dfc.costo_compra as precio_unitario,
                        (dfc.cantidad * dfc.costo_compra) as subtotal,
                        fc.total_iva,
                        fc.total_general
                    FROM factura_compra fc
                    INNER JOIN detalle_fact_compra dfc ON fc.id_fact_com = dfc.id_fact_com
                    INNER JOIN inv_materia_prima mp ON dfc.id_materia_prima = mp.id_materia
                    INNER JOIN proveedores p ON fc.id_proveedor = p.id_proveedores
                    WHERE fc.status = 0";
            
            $params = [];
            
            if ($tipoMateria === 'especifico' && $idMateria) {
                $sql .= " AND mp.id_materia = ?";
                $params[] = $idMateria;
            }
    
            switch ($periodo) {
                case 'dia':
                    $sql .= " AND DATE(fc.fecha) = ?";
                    $params[] = date('Y-m-d');
                    break;
                case 'semana':
                    $fechaInicioSemana = date('Y-m-d', strtotime('monday this week'));
                    $fechaFinSemana = date('Y-m-d', strtotime('sunday this week'));
                    $sql .= " AND fc.fecha BETWEEN ? AND ?";
                    $params[] = $fechaInicioSemana;
                    $params[] = $fechaFinSemana;
                    break;
                case 'mes':
                    if ($mes && $anio) {
                        $sql .= " AND MONTH(fc.fecha) = ? AND YEAR(fc.fecha) = ?";
                        $params[] = $mes;
                        $params[] = $anio;
                    }
                    break;
                case 'anio':
                    if ($anio) {
                        $sql .= " AND YEAR(fc.fecha) = ?";
                        $params[] = $anio;
                    }
                    break;
                case 'personalizado':
                    if ($fechaDesde && $fechaHasta) {
                        $sql .= " AND fc.fecha BETWEEN ? AND ?";
                        $params[] = $fechaDesde;
                        $params[] = $fechaHasta;
                    }
                    break;
            }
            
            $sql .= " ORDER BY fc.fecha DESC, fc.id_fact_com DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $compras = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $totalCompras = 0;
            $totalIva = 0;
            $totalSubtotal = 0;
            
            foreach ($compras as $compra) {
                $totalCompras += $compra['total_general'];
                $totalIva += $compra['total_iva'];
                $totalSubtotal += $compra['subtotal'];
            }
            
            return [
                'compras' => $compras,
                'total_compras' => $totalCompras,
                'total_iva' => $totalIva,
                'total_subtotal' => $totalSubtotal,
                'filtros' => [
                    'tipo_materia' => $tipoMateria,
                    'id_materia' => $idMateria,
                    'periodo' => $periodo,
                    'fecha_desde' => $fechaDesde,
                    'fecha_hasta' => $fechaHasta,
                    'mes' => $mes,
                    'anio' => $anio
                ],
                'total_registros' => count($compras)
            ];
            
        } catch (PDOException $e) {
            error_log("Error en ReporteComprasModelo->obtenerComprasParametrizadas(): " . $e->getMessage());
            throw new Exception("Error al obtener compras parametrizadas: " . $e->getMessage());
        }
    }

    public function obtenerMateriasPrimasParaFiltro() {
        try {
            $sql = "SELECT id_materia as id, nombre 
                    FROM inv_materia_prima 
                    WHERE status = 0
                    ORDER BY nombre ASC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $materias = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return is_array($materias) ? $materias : [];
            
        } catch (PDOException $e) {
            error_log("Error en obtenerMateriasPrimasParaFiltro(): " . $e->getMessage());
            return [];
        }
    }

    public function obtenerNombreMateriaPrima($idMateria) {
        try {
            $sql = "SELECT nombre FROM inv_materia_prima WHERE id_materia = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$idMateria]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['nombre'] : 'Materia prima no encontrada';
        } catch (PDOException $e) {
            error_log("Error en ReporteComprasModelo->obtenerNombreMateriaPrima(): " . $e->getMessage());
            return 'Error al obtener nombre';
        }
    }
}
?>