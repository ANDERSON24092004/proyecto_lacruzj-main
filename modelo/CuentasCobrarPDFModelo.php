<?php
require_once 'vendor/fpdf.php';

class CuentasCobrarPDFModelo extends FPDF {
    private $cuentaData;
    private $logoPath;
    private $firmaPath;
    
    public function __construct($cuentaData, $logoPath = '', $firmaPath = '') {
        parent::__construct('P', 'mm', 'Letter');
        $this->cuentaData = $cuentaData;
        $this->logoPath = $logoPath;
        $this->firmaPath = $firmaPath;
        $this->SetAutoPageBreak(true, 30);
        $this->AddPage();
    }
    
    public function Header() {
        $azul_principal = array(41, 128, 185);
        $azul_claro = array(236, 240, 245);
        
        $logoWidth = 0;
        if (!empty($this->logoPath) && file_exists($this->logoPath)) {
            $this->Image($this->logoPath, 10, 8, 25);
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
        $this->Cell(0, 5, 'Carrera 2 entre Calle 6 y 7 Local Casa N914 Nro, Local 3 Sector la Mata Cabudare, Edo.Lara', 0, 1);
        $this->SetX(10 + $logoWidth);
        $this->Cell(0, 5, 'Zona postal 3001 | Telefono: +58 424-5085666 0414-5718890', 0, 1);
        $this->SetX(10 + $logoWidth);
        $this->Cell(0, 5, 'Email: jlacruzca@gmail.com', 0, 1);

        $this->SetDrawColor($azul_principal[0], $azul_principal[1], $azul_principal[2]);
        $this->SetLineWidth(0.8);
        $this->Line(10, 40, 200, 40);
        $this->SetLineWidth(0.2);
        $this->Ln(10);
    }

    public function Footer() {
        $this->SetY(-20);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(150, 150, 150);
        
        if (!empty($this->firmaPath) && file_exists($this->firmaPath)) {
            $this->Image($this->firmaPath, 140, $this->GetY() - 15, 40);
            $this->SetX(140);
            $this->Cell(40, 2, 'Firma Autorizada', 0, 1, 'C');
            $this->Ln(3);
        }     
  
        $this->SetDrawColor(41, 128, 185);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(2);
        $this->Cell(0, 6, 'Pagina ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
        $this->Ln(3);
        $this->Cell(0, 6, 'J LACRUZ C.A. - Todos los derechos reservados', 0, 0, 'C');
    }

    public function infoCuenta() {
        $cuenta = $this->cuentaData['cuenta'];
        $azul_principal = array(41, 128, 185);
        $azul_claro = array(236, 240, 245);
    
        $this->SetFont('Arial', 'B', 14);
        $this->SetTextColor($azul_principal[0], $azul_principal[1], $azul_principal[2]);
        $this->Cell(0, 10, 'ESTADO DE CUENTA POR COBRAR', 0, 1, 'C');
        $this->Ln(5);
        
        $this->SetFillColor($azul_principal[0], $azul_principal[1], $azul_principal[2]);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 8, 'INFORMACION DE LA CUENTA', 1, 1, 'C', true);
        
        $this->SetTextColor(0, 0, 0);
        $this->SetFillColor($azul_claro[0], $azul_claro[1], $azul_claro[2]);
      
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(40, 7, 'Numero de Factura:', 1, 0, 'L', true);
        $this->SetFont('Arial', '', 9);
        $this->Cell(50, 7, '#' . $cuenta['nro_fact'], 1, 0);
        
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(43, 7, 'Fecha de Emision:', 1, 0, 'L', true);
        $this->SetFont('Arial', '', 9);
        $this->Cell(63, 7, $cuenta['fecha_factura'], 1, 1);
        
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(40, 7, 'Fecha Limite:', 1, 0, 'L', true);
        $this->SetFont('Arial', '', 9);
        $this->Cell(50, 7, $cuenta['fecha_limite'], 1, 0);
        
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(43, 7, 'Estado:', 1, 0, 'L', true);
        $this->SetFont('Arial', '', 9);
     
        switch($cuenta['estado_visual']) {
            case 'Pagada':
                $this->SetTextColor(0, 128, 0);
                break;
            case 'Vencida':
                $this->SetTextColor(255, 0, 0);
                break;
            default:
                $this->SetTextColor(255, 165, 0);
        }
        $this->Cell(63, 7, $cuenta['estado_visual'], 1, 1);
        $this->SetTextColor(0, 0, 0);
        
        $this->Ln(8);
        
        $this->SetFillColor($azul_principal[0], $azul_principal[1], $azul_principal[2]);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 8, 'INFORMACION DEL CLIENTE', 1, 1, 'C', true);
        
        $this->SetTextColor(0, 0, 0);
        $this->SetFillColor($azul_claro[0], $azul_claro[1], $azul_claro[2]);
        
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(33, 7, 'RIF:', 1, 0, 'L', true);
        $this->SetFont('Arial', '', 9);
        $this->Cell(63, 7, $cuenta['rif'], 1, 0);
        
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(30, 7, 'Telefono:', 1, 0, 'L', true);
        $this->SetFont('Arial', '', 9);
        $this->Cell(70, 7, !empty($cuenta['telefono']) ? $cuenta['telefono'] : 'N/A', 1, 1);
        
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(33, 7, 'Razon Social:', 1, 0, 'L', true);
        $this->SetFont('Arial', '', 9);
        $this->Cell(163, 7, $cuenta['razon_social'], 1, 1);
        
        if (!empty($cuenta['direccion'])) {
            $this->SetFont('Arial', 'B', 9);
            $this->Cell(33, 7, 'Direccion:', 1, 0, 'L', true);
            $this->SetFont('Arial', '', 9);
            $this->Cell(163, 7, $cuenta['direccion'], 1, 1);
        }
        
        $this->Ln(8);
    }

    public function resumenFinanciero() {
        $cuenta = $this->cuentaData['cuenta'];
        $azul_principal = array(41, 128, 185);
        
        $this->SetFillColor($azul_principal[0], $azul_principal[1], $azul_principal[2]);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 8, 'RESUMEN FINANCIERO', 1, 1, 'C', true);
        
        $this->SetTextColor(0, 0, 0);

        $this->SetFont('Arial', 'B', 10);
        $this->Cell(98, 8, 'Concepto', 1, 0, 'C');
        $this->Cell(98, 8, 'Monto (Bs.)', 1, 1, 'C');
        
        $this->SetFont('Arial', '', 10);

        $this->Cell(98, 8, 'Monto Total de la Factura', 1, 0, 'L');
        $this->Cell(98, 8, number_format($cuenta['total_general'], 2, ',', '.'), 1, 1, 'R');

        $this->Cell(98, 8, 'Total Abonado', 1, 0, 'L');
        $this->Cell(98, 8, number_format($cuenta['total_abonado'] ?? 0, 2, ',', '.'), 1, 1, 'R');
        
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(98, 8, 'Saldo Pendiente', 1, 0, 'L');
        
        if ($cuenta['saldo_pendiente'] > 0) {
            $this->SetTextColor(255, 0, 0);
        } else {
            $this->SetTextColor(0, 128, 0);
        }
        
        $this->Cell(98, 8, number_format($cuenta['saldo_pendiente'], 2, ',', '.'), 1, 1, 'R');
        $this->SetTextColor(0, 0, 0);
        
        $this->Ln(8);
    }
    
    public function historialAbonos() {
        $historial = $this->cuentaData['historial'] ?? [];
        $azul_principal = array(41, 128, 185);
        
        if (!empty($historial)) {
            $this->SetFillColor($azul_principal[0], $azul_principal[1], $azul_principal[2]);
            $this->SetTextColor(255, 255, 255);
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(0, 8, 'HISTORIAL DE ABONOS', 1, 1, 'C', true);
            
            $this->SetTextColor(0, 0, 0);
            
            $this->SetFont('Arial', 'B', 9);
            $this->Cell(40, 8, 'Fecha Abono', 1, 0, 'C');
            $this->Cell(50, 8, 'Monto Abonado', 1, 0, 'C');
            $this->Cell(50, 8, 'Saldo Anterior', 1, 0, 'C');
            $this->Cell(50, 8, 'Nuevo Saldo', 1, 1, 'C');
            
            $this->SetFont('Arial', '', 9);
            foreach ($historial as $abono) {
                $this->Cell(40, 8, $abono['fecha_abono'], 1, 0, 'C');
                $this->Cell(50, 8, number_format($abono['monto_abono'], 2, ',', '.'), 1, 0, 'R');
                $this->Cell(50, 8, number_format($abono['saldo_anterior'], 2, ',', '.'), 1, 0, 'R');
                $this->Cell(50, 8, number_format($abono['saldo_pendiente'], 2, ',', '.'), 1, 1, 'R');
            }
            
            $this->Ln(8);
        }
    }
    
    public function detallesFactura() {
        $detalles = $this->cuentaData['detalles'] ?? [];
        $azul_principal = array(41, 128, 185);
        
        if (!empty($detalles)) {
            $this->SetFillColor($azul_principal[0], $azul_principal[1], $azul_principal[2]);
            $this->SetTextColor(255, 255, 255);
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(0, 8, 'DETALLES DE LA FACTURA', 1, 1, 'C', true);
            
            $this->SetTextColor(0, 0, 0);
            
            $this->SetFont('Arial', 'B', 9);
            $this->Cell(15, 8, '#', 1, 0, 'C');
            $this->Cell(95, 8, 'Producto/Servicio', 1, 0, 'C');
            $this->Cell(20, 8, 'Cantidad', 1, 0, 'C');
            $this->Cell(30, 8, 'Precio Unit.', 1, 0, 'C');
            $this->Cell(36, 8, 'Subtotal', 1, 1, 'C');
            
            $this->SetFont('Arial', '', 9);
            $contador = 1;
            $total = 0;
            
            foreach ($detalles as $detalle) {
                $subtotal = $detalle['subtotal'];
                $total += $subtotal;
                
                $fill = ($contador % 2 == 0);
                if ($fill) {
                    $this->SetFillColor(248, 248, 248);
                } else {
                    $this->SetFillColor(255, 255, 255);
                }
                
                $this->Cell(15, 8, $contador, 1, 0, 'C', $fill);
                $this->Cell(95, 8, $this->truncateText($detalle['nombre'], 50), 1, 0, 'L', $fill);
                $this->Cell(20, 8, $detalle['cantidad'], 1, 0, 'C', $fill);
                $this->Cell(30, 8, number_format($detalle['precio_unitario'], 2, ',', '.'), 1, 0, 'R', $fill);
                $this->Cell(36, 8, number_format($subtotal, 2, ',', '.'), 1, 1, 'R', $fill);
                
                $contador++;
            }
            
            $this->Ln(8);
        }
    }

    public function observaciones() {
    $cuenta = $this->cuentaData['cuenta'];
    $azul_principal = array(41, 128, 185);
    
    $this->SetFillColor($azul_principal[0], $azul_principal[1], $azul_principal[2]);
    $this->SetTextColor(255, 255, 255);
    $this->SetFont('Arial', 'B', 12);
    $this->Cell(0, 8, 'OBSERVACIONES', 1, 1, 'C', true);
    
    $this->SetTextColor(0, 0, 0);
    $this->SetFont('Arial', '', 10);
    
    $observaciones = [];
    
    if ($cuenta['estado_visual'] == 'Vencida') {
        $observaciones[] = "• Esta cuenta se encuentra VENCIDA desde " . $cuenta['fecha_limite'];
    }
 
    if ($cuenta['vigencia_factura'] == 1) {
        $observaciones[] = "• Esta cuenta ha sido ANULADA";
    }
    
    if ($cuenta['saldo_pendiente'] == 0) {
        $observaciones[] = "• La cuenta está completamente PAGADA";
    } else {
        $observaciones[] = "• Saldo pendiente por cancelar: " . number_format($cuenta['saldo_pendiente'], 2, ',', '.') . " Bs.";
    }
    
    if (empty($observaciones)) {
        $observaciones[] = "• Cuenta en estado normal, pendiente por pago.";
    }
    
    foreach ($observaciones as $obs) {
        $this->MultiCell(0, 6, $obs);
        $this->Ln(1);
    }
    
    $this->Ln(5);
    
    $this->SetFont('Arial', 'I', 10);
    $this->SetTextColor(100, 100, 100);
    $this->Cell(0, 6, 'Documento generado el: ' . date('d/m/Y H:i:s'), 0, 1, 'C');
    $this->Cell(0, 6, 'Para consultas contactar: jlacruzca@gmail.com', 0, 1, 'C');
    }
    
    private function truncateText($text, $length) {
        if (strlen($text) > $length) {
            return substr($text, 0, $length - 3) . '...';
        }
        return $text;
    }
    
    public function generarPDF() {
        $this->AliasNbPages();
        $this->infoCuenta();
        $this->resumenFinanciero();
        $this->historialAbonos();
        $this->detallesFactura();
        $this->observaciones();
        
        return $this;
    }
}
?>