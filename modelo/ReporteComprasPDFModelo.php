<?php

require_once 'vendor/fpdf.php';

class ReporteComprasPDFModelo extends FPDF {
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
            return 'REPORTE DE COMPRAS';
        }
        
        if (isset($this->datosReporte['filtros'])) {
            $filtros = $this->datosReporte['filtros'];
            $tipoTexto = $this->getTipoMateriaTexto($filtros['tipo_materia'], $filtros['id_materia']);
            $periodoTexto = $this->getPeriodoTexto($filtros);
            return 'REPORTE DE COMPRAS - ' . $tipoTexto . ' - ' . $periodoTexto;
        }
        
        switch ($this->tipoReporte) {
            case 'dia':
                return 'REPORTE DE COMPRAS DEL DÍA - ' . date('d/m/Y', strtotime($this->datosReporte['fecha']));
            case 'semana':
                return 'REPORTE DE COMPRAS SEMANALES - ' . 
                       date('d/m/Y', strtotime($this->datosReporte['fecha_inicio'])) . ' al ' . 
                       date('d/m/Y', strtotime($this->datosReporte['fecha_fin']));
            case 'mes':
                $meses = [
                    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                    5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                    9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
                ];
                return 'REPORTE DE COMPRAS MENSUALES - ' . $meses[$this->datosReporte['mes']] . ' ' . $this->datosReporte['anio'];
            case 'anio':
                return 'REPORTE DE COMPRAS ANUALES - AÑO ' . $this->datosReporte['anio'];
            default:
                return 'REPORTE DE COMPRAS';
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
        $this->Cell(0, 6, 'Total de compras registradas: ' . $this->datosReporte['total_registros'], 0, 1);
        
        if (isset($this->datosReporte['filtros'])) {
            $filtros = $this->datosReporte['filtros'];
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(0, 6, 'Filtros aplicados:', 0, 1);
            $this->SetFont('Arial', '', 10);
            
            $tipoTexto = $this->getTipoMateriaTexto($filtros['tipo_materia'], $filtros['id_materia']);
            $this->Cell(0, 6, 'Tipo: ' . $tipoTexto, 0, 1);
            
            $periodoTexto = $this->getPeriodoTexto($filtros);
            $this->Cell(0, 6, 'Periodo: ' . $periodoTexto, 0, 1);
        }
        
        $this->Ln(5);
    }
    
    public function tablaCompras() {
        if (empty($this->datosReporte) || empty($this->datosReporte['compras'])) {
            $this->SetFont('Arial', 'I', 12);
            $this->Cell(0, 20, 'No hay datos de compras para mostrar', 0, 1, 'C');
            return;
        }
        
        $compras = $this->datosReporte['compras'];
        $azul_principal = array(41, 128, 185);
        
        $this->SetFillColor($azul_principal[0], $azul_principal[1], $azul_principal[2]);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 8);
        
        $this->Cell(15, 8, '# Fact', 1, 0, 'C', true);
        $this->Cell(25, 8, 'Fecha', 1, 0, 'C', true);
        $this->Cell(40, 8, 'Proveedor', 1, 0, 'C', true);
        $this->Cell(45, 8, 'Materia Prima', 1, 0, 'C', true);
        $this->Cell(15, 8, 'Cant.', 1, 0, 'C', true);
        $this->Cell(25, 8, 'P. Unit.', 1, 0, 'C', true);
        $this->Cell(25, 8, 'Subtotal', 1, 1, 'C', true);
        
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Arial', '', 7);
        
        $contador = 1;
        
        foreach ($compras as $compra) {
            if ($this->GetY() > 250) {
                $this->AddPage();
                $this->tablaComprasHeader();
            }
            
            $fill = ($contador % 2 == 0) ? true : false;
            if ($fill) {
                $this->SetFillColor(248, 248, 248);
            } else {
                $this->SetFillColor(255, 255, 255);
            }
            
            $this->Cell(15, 6, $compra['id_fact_com'], 1, 0, 'C', $fill);
            $this->Cell(25, 6, date('d/m/y', strtotime($compra['fecha'])), 1, 0, 'C', $fill);
            $this->Cell(40, 6, $this->truncateText($compra['proveedor'], 25), 1, 0, 'L', $fill);
            $this->Cell(45, 6, $this->truncateText($compra['materia_prima'], 30), 1, 0, 'L', $fill);
            $this->Cell(15, 6, $compra['cantidad'], 1, 0, 'C', $fill);
            $this->Cell(25, 6, number_format($compra['precio_unitario'], 2, ',', '.'), 1, 0, 'R', $fill);
            $this->Cell(25, 6, number_format($compra['subtotal'], 2, ',', '.'), 1, 1, 'R', $fill);
            
            $contador++;
        }
        
        $this->Ln(5);
        $this->resumenCompras();
    }
    
    private function tablaComprasHeader() {
        $azul_principal = array(41, 128, 185);
        
        $this->SetFillColor($azul_principal[0], $azul_principal[1], $azul_principal[2]);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 8);
        
        $this->Cell(15, 8, '# Fact', 1, 0, 'C', true);
        $this->Cell(25, 8, 'Fecha', 1, 0, 'C', true);
        $this->Cell(40, 8, 'Proveedor', 1, 0, 'C', true);
        $this->Cell(45, 8, 'Materia Prima', 1, 0, 'C', true);
        $this->Cell(15, 8, 'Cant.', 1, 0, 'C', true);
        $this->Cell(25, 8, 'P. Unit.', 1, 0, 'C', true);
        $this->Cell(25, 8, 'Subtotal', 1, 1, 'C', true);
        
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Arial', '', 7);
    }
    
    private function resumenCompras() {
        if (empty($this->datosReporte)) {
            return;
        }
        
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor(200, 200, 200);
        $this->Cell(0, 8, 'RESUMEN DE COMPRAS', 1, 1, 'C', true);
        
        $this->SetFont('Arial', '', 10);
        $this->Cell(146, 7, 'Total Subtotal:', 1, 0, 'R');
        $this->Cell(50, 7, number_format($this->datosReporte['total_subtotal'], 2, ',', '.'), 1, 1, 'R');
        
        $this->Cell(146, 7, 'Total IVA:', 1, 0, 'R');
        $this->Cell(50, 7, number_format($this->datosReporte['total_iva'], 2, ',', '.'), 1, 1, 'R');
        
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(146, 7, 'TOTAL GENERAL:', 1, 0, 'R');
        $this->Cell(50, 7, number_format($this->datosReporte['total_compras'], 2, ',', '.'), 1, 1, 'R');
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
        $this->tablaCompras();
        
        $nombreArchivo = $this->getNombreArchivo();
        $this->Output('I', $nombreArchivo);
        exit;
    }
    
    private function getNombreArchivo() {
        if (empty($this->datosReporte)) {
            return 'Compras_' . date('Y-m-d') . '.pdf';
        }
        
        if (isset($this->datosReporte['filtros'])) {
            $filtros = $this->datosReporte['filtros'];
            $base = 'Compras_Parametrizadas_';
            $base .= $filtros['tipo_materia'] . '_';
            $base .= $filtros['periodo'] . '_';
            $base .= date('Y-m-d');
            return $base . '.pdf';
        }
        
        switch ($this->tipoReporte) {
            case 'dia':
                return 'Compras_Dia_' . date('Y-m-d') . '.pdf';
            case 'semana':
                return 'Compras_Semana_' . date('Y-m-d') . '.pdf';
            case 'mes':
                return 'Compras_Mes_' . $this->datosReporte['mes'] . '_' . $this->datosReporte['anio'] . '.pdf';
            case 'anio':
                return 'Compras_Anio_' . $this->datosReporte['anio'] . '.pdf';
            default:
                return 'Compras_' . date('Y-m-d') . '.pdf';
        }
    }

    private function getTipoMateriaTexto($tipoMateria, $idMateria) {
        switch ($tipoMateria) {
            case 'todos':
                return 'Todas las materias primas';
            case 'especifico':
                return 'Materia prima específica';
            default:
                return 'Todas';
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