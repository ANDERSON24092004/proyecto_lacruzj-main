<?php

namespace src\modelo;
use FPDF;

class reporteVentasPDFModelo extends FPDF {
    private $datosReporte;
    private $tipoReporte;
    
    public function __construct($orientation = 'P', $unit = 'mm', $size = 'Letter') {
        parent::__construct($orientation, $unit, $size);
        $this->SetAutoPageBreak(true, 30);
    }
    
    public function setDatosReporte($datosReporte, $tipoReporte = '') {
        $this->datosReporte = $datosReporte;
        $this->tipoReporte = $tipoReporte;
    }
    
    public function Header() {
        $azul_principal = array(41, 128, 185);
        
        $logoPath = 'assets/images/logo2.png';
        $logoWidth = 0;
        if (file_exists($logoPath)) {
            $this->Image($logoPath, 10, 8, 25);
            $logoWidth = 30;
        }
        
        $this->SetX(10 + $logoWidth);
        $this->SetFont('Arial', 'B', 16);
        $this->SetTextColor($azul_principal[0], $azul_principal[1], $azul_principal[2]);
        $this->Cell(0, 8, 'J LACRUZ C.A.', 0, 1);
        
        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(80, 80, 80);
        $this->SetX(10 + $logoWidth);
        $this->Cell(0, 5, 'RIF: J-412192701', 0, 1);
        $this->SetX(10 + $logoWidth);
        $this->Cell(0, 5, 'Email: jlacruzca@gmail.com', 0, 1);
        
        $this->SetDrawColor($azul_principal[0], $azul_principal[1], $azul_principal[2]);
        $this->SetLineWidth(0.8);
        $this->Line(10, 32, 200, 32);
        $this->SetLineWidth(0.2);
        $this->Ln(5);
        
        $titulo = $this->getTituloReporte();
        $this->SetFont('Arial', 'B', 14);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(0, 10, $titulo, 0, 1, 'C');
        $this->Ln(2);
    }
    
    private function getTituloReporte() {
        if (empty($this->datosReporte)) {
            return 'REPORTE DE VENTAS';
        }
        
        if (isset($this->datosReporte['filtros'])) {
            $filtros = $this->datosReporte['filtros'];
            $tipoTexto = $this->getTipoItemTexto($filtros['tipo_producto'], $filtros['id_item']);
            $periodoTexto = $this->getPeriodoTexto($filtros);
            return 'REPORTE DE VENTAS - ' . $tipoTexto . ' - ' . $periodoTexto;
        }
        
        switch ($this->tipoReporte) {
            case 'dia':
                return 'REPORTE DE VENTAS DEL DÍA - ' . date('d/m/Y', strtotime($this->datosReporte['fecha']));
            case 'semana':
                return 'REPORTE DE VENTAS SEMANALES - ' . 
                       date('d/m/Y', strtotime($this->datosReporte['fecha_inicio'])) . ' al ' . 
                       date('d/m/Y', strtotime($this->datosReporte['fecha_fin']));
            case 'mes':
                $meses = [
                    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                    5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                    9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
                ];
                return 'REPORTE DE VENTAS MENSUALES - ' . $meses[$this->datosReporte['mes']] . ' ' . $this->datosReporte['anio'];
            case 'anio':
                return 'REPORTE DE VENTAS ANUALES - AÑO ' . $this->datosReporte['anio'];
            default:
                return 'REPORTE DE VENTAS';
        }
    }
    
    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(150, 150, 150);
        $this->Cell(0, 4, 'Pagina ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
    
    public function infoReporte() {
        if (empty($this->datosReporte)) {
            return;
        }
        
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 6, 'Fecha de generacion: ' . date('d/m/Y H:i:s'), 0, 1);
        $this->Cell(0, 6, 'Total de ventas registradas: ' . $this->datosReporte['total_registros'], 0, 1);
        
        if (isset($this->datosReporte['filtros'])) {
            $filtros = $this->datosReporte['filtros'];
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(0, 6, 'Filtros aplicados:', 0, 1);
            $this->SetFont('Arial', '', 10);
            
            $tipoTexto = $this->getTipoItemTexto($filtros['tipo_producto'], $filtros['id_item']);
            $this->Cell(0, 6, 'Tipo: ' . $tipoTexto, 0, 1);
            
            $periodoTexto = $this->getPeriodoTexto($filtros);
            $this->Cell(0, 6, 'Periodo: ' . $periodoTexto, 0, 1);
        }
        
        $this->Ln(5);
    }
    
    public function tablaVentas() {
        if (empty($this->datosReporte) || empty($this->datosReporte['ventas'])) {
            $this->SetFont('Arial', 'I', 12);
            $this->Cell(0, 20, 'No hay datos de ventas para mostrar', 0, 1, 'C');
            return;
        }
        
        $ventas = $this->datosReporte['ventas'];
        $azul_principal = array(41, 128, 185);
        
        $this->SetFillColor($azul_principal[0], $azul_principal[1], $azul_principal[2]);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 8);
        
        $this->Cell(15, 8, '# Fact', 1, 0, 'C', true);
        $this->Cell(20, 8, 'Fecha', 1, 0, 'C', true);
        $this->Cell(40, 8, 'Cliente', 1, 0, 'C', true);
        $this->Cell(45, 8, 'Producto/Servicio', 1, 0, 'C', true);
        $this->Cell(15, 8, 'Tipo', 1, 0, 'C', true);
        $this->Cell(15, 8, 'Cant.', 1, 0, 'C', true);
        $this->Cell(20, 8, 'P. Unit.', 1, 0, 'C', true);
        $this->Cell(20, 8, 'Subtotal', 1, 1, 'C', true);
        
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Arial', '', 7);
        
        $contador = 1;
        
        foreach ($ventas as $venta) {
            if ($this->GetY() > 250) {
                $this->AddPage();
                $this->tablaVentasHeader();
            }
            
            $fill = ($contador % 2 == 0) ? true : false;
            if ($fill) {
                $this->SetFillColor(248, 248, 248);
            } else {
                $this->SetFillColor(255, 255, 255);
            }
            
            $this->Cell(15, 6, $venta['nro_fact'], 1, 0, 'C', $fill);
            $this->Cell(20, 6, date('d/m/y', strtotime($venta['fecha'])), 1, 0, 'C', $fill);
            $this->Cell(40, 6, $this->truncateText($venta['razon_social'], 25), 1, 0, 'L', $fill);
            $this->Cell(45, 6, $this->truncateText($venta['producto_servicio'], 30), 1, 0, 'L', $fill);
            $this->Cell(15, 6, substr($venta['tipo'], 0, 3), 1, 0, 'C', $fill);
            $this->Cell(15, 6, $venta['cantidad'], 1, 0, 'C', $fill);
            $this->Cell(20, 6, number_format($venta['precio_unitario'], 2, ',', '.'), 1, 0, 'R', $fill);
            $this->Cell(20, 6, number_format($venta['subtotal'], 2, ',', '.'), 1, 1, 'R', $fill);
            
            $contador++;
        }
        
        $this->Ln(5);
        $this->resumenVentas();
    }
    
    private function tablaVentasHeader() {
        $azul_principal = array(41, 128, 185);
        
        $this->SetFillColor($azul_principal[0], $azul_principal[1], $azul_principal[2]);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 8);
        
        $this->Cell(15, 8, '# Fact', 1, 0, 'C', true);
        $this->Cell(20, 8, 'Fecha', 1, 0, 'C', true);
        $this->Cell(40, 8, 'Cliente', 1, 0, 'C', true);
        $this->Cell(45, 8, 'Producto/Servicio', 1, 0, 'C', true);
        $this->Cell(15, 8, 'Tipo', 1, 0, 'C', true);
        $this->Cell(15, 8, 'Cant.', 1, 0, 'C', true);
        $this->Cell(20, 8, 'P. Unit.', 1, 0, 'C', true);
        $this->Cell(20, 8, 'Subtotal', 1, 1, 'C', true);
        
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Arial', '', 7);
    }
    
    private function resumenVentas() {
        if (empty($this->datosReporte)) {
            return;
        }
        
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor(200, 200, 200);
        $this->Cell(0, 8, 'RESUMEN DE VENTAS', 1, 1, 'C', true);
        
        $this->SetFont('Arial', '', 10);
        $this->Cell(146, 7, 'Total Subtotal:', 1, 0, 'R');
        $this->Cell(50, 7, number_format($this->datosReporte['total_subtotal'], 2, ',', '.'), 1, 1, 'R');
        
        $this->Cell(146, 7, 'Total IVA:', 1, 0, 'R');
        $this->Cell(50, 7, number_format($this->datosReporte['total_iva'], 2, ',', '.'), 1, 1, 'R');
        
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(146, 7, 'TOTAL GENERAL:', 1, 0, 'R');
        $this->Cell(50, 7, number_format($this->datosReporte['total_ventas'], 2, ',', '.'), 1, 1, 'R');
    }
    
    private function truncateText($text, $length) {
        if (strlen($text) > $length) {
            return substr($text, 0, $length - 3) . '...';
        }
        return $text;
    }
    
    public function generarPDF($datos, $tipoReporte = '') {
        $this->setDatosReporte($datos, $tipoReporte);
        $this->AddPage();
        $this->AliasNbPages();
        $this->infoReporte();
        $this->tablaVentas();
        
        $nombreArchivo = $this->getNombreArchivo();
        $this->Output('I', $nombreArchivo);
        exit;
    }
    
    private function getNombreArchivo() {
        if (empty($this->datosReporte)) {
            return 'Ventas_' . date('Y-m-d') . '.pdf';
        }
        
        if (isset($this->datosReporte['filtros'])) {
            $filtros = $this->datosReporte['filtros'];
            $base = 'Ventas_Parametrizadas_';
            $base .= $filtros['tipo_producto'] . '_';
            $base .= $filtros['periodo'] . '_';
            $base .= date('Y-m-d');
            return $base . '.pdf';
        }
        
        switch ($this->tipoReporte) {
            case 'dia':
                return 'Ventas_Dia_' . date('Y-m-d') . '.pdf';
            case 'semana':
                return 'Ventas_Semana_' . date('Y-m-d') . '.pdf';
            case 'mes':
                return 'Ventas_Mes_' . $this->datosReporte['mes'] . '_' . $this->datosReporte['anio'] . '.pdf';
            case 'anio':
                return 'Ventas_Anio_' . $this->datosReporte['anio'] . '.pdf';
            default:
                return 'Ventas_' . date('Y-m-d') . '.pdf';
        }
    }

    private function getTipoItemTexto($tipoProducto, $idItem) {
        switch ($tipoProducto) {
            case 'todos':
                return 'Todos los items (Productos y Servicios)';
            case 'productos':
                return 'Solo Productos';
            case 'servicios':
                return 'Solo Servicios';
            case 'especifico':
                return 'Item Específico';
            default:
                return 'Todos';
        }
    }

    private function getPeriodoTexto($filtros) {
        switch ($filtros['periodo']) {
            case 'dia':
                return 'Día: ' . date('d/m/Y');
            case 'semana':
                $inicio = date('d/m/Y', strtotime('monday this week'));
                $fin = date('d/m/Y', strtotime('sunday this week'));
                return 'Semana: ' . $inicio . ' al ' . $fin;
            case 'mes':
                $meses = [1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',
                         7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre'];
                return 'Mes: ' . $meses[$filtros['mes']] . ' ' . $filtros['anio'];
            case 'anio':
                return 'Año: ' . $filtros['anio'];
            case 'personalizado':
                return 'Personalizado: ' . date('d/m/Y', strtotime($filtros['fecha_desde'])) . 
                       ' al ' . date('d/m/Y', strtotime($filtros['fecha_hasta']));
            default:
                return 'Día actual';
        }
    }
}
?>