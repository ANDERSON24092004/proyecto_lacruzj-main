<?php
require_once 'vendor/fpdf.php';

class FacturaPDFModelo extends FPDF {
    private $facturaData;
    private $logoPath;
    private $firmaPath;
    
    public function __construct($facturaData, $logoPath = '', $firmaPath = '') {
        parent::__construct('P', 'mm', 'Letter');
        $this->facturaData = $facturaData;
        $this->logoPath = $logoPath;
        $this->firmaPath = $firmaPath;
        $this->SetAutoPageBreak(true, 30);
        $this->AddPage();
    }
    
    // Header de la factura
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
        
        // Linea  azul
        $this->SetDrawColor($azul_principal[0], $azul_principal[1], $azul_principal[2]);
        $this->SetLineWidth(0.8);
        $this->Line(10, 40, 200, 40);
        $this->SetLineWidth(0.2);
        $this->Ln(10);
    }
    
    // Footer de la factura
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
        
        // Línea de footer
        $this->SetDrawColor(41, 128, 185);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(2);
        $this->Cell(0, 6, 'Pagina ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
        $this->Ln(3);
        $this->Cell(0, 6, 'J LACRUZ C.A. - Todos los derechos reservados', 0, 0, 'C');
    }
    
    // Información del cliente
    public function infoCliente() {
        $factura = $this->facturaData['factura'];
        $azul_principal = array(41, 128, 185);
        $azul_claro = array(236, 240, 245);
        
        $this->SetFillColor($azul_principal[0], $azul_principal[1], $azul_principal[2]);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 8, 'INFORMACION DE FACTURA', 1, 1, 'C', true);
        
        $this->SetTextColor(0, 0, 0);
        $this->SetFillColor($azul_claro[0], $azul_claro[1], $azul_claro[2]);
        
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(48, 7, 'Numero de Factura:', 1, 0, 'L', true);
        $this->SetFont('Arial', '', 9);
        $this->Cell(50, 7, $factura['nro_fact'], 1, 0);
        
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(48, 7, 'Fecha:', 1, 0, 'L', true);
        $this->SetFont('Arial', '', 9);
        $this->Cell(50, 7, $factura['fecha'], 1, 1); 
        
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(48, 7, 'Condicion de Pago:', 1, 0, 'L', true);
        $this->SetFont('Arial', '', 9);
        $this->Cell(50, 7, $factura['condicion_pago'], 1, 0);
        
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(48, 7, 'Numero de Orden:', 1, 0, 'L', true);
        $this->SetFont('Arial', '', 9);
        $this->Cell(50, 7, !empty($factura['numero_orden']) ? $factura['numero_orden'] : 'N/A', 1, 1);
        
        $this->Ln(8);
        
        $this->SetFillColor($azul_principal[0], $azul_principal[1], $azul_principal[2]);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 8, 'INFORMACION DEL CLIENTE', 1, 1, 'C', true);
        
        $this->SetTextColor(0, 0, 0);
        $this->SetFillColor($azul_claro[0], $azul_claro[1], $azul_claro[2]);
  
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(36, 7, 'RIF:', 1, 0, 'L', true);
        $this->SetFont('Arial', '', 9);
        $this->Cell(59, 7, $factura['rif'], 1, 0);
        
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(35, 7, 'Telefono:', 1, 0, 'L', true);
        $this->SetFont('Arial', '', 9);
        $this->Cell(66, 7, !empty($factura['telefono']) ? $factura['telefono'] : 'N/A', 1, 1);
        
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(36, 7, 'Razon Social:', 1, 0, 'L', true);
        $this->SetFont('Arial', '', 9);
        $this->Cell(160, 7, $factura['razon_social'], 1, 1);
        
        if (!empty($factura['direccion'])) {
            $alturaDireccion = $this->calcularAlturaTexto($factura['direccion'], 160, 9);
            $alturaDireccion = max(7, $alturaDireccion);
            
            $this->SetFont('Arial', 'B', 9);
            $this->Cell(36, $alturaDireccion, 'Direccion:', 1, 0, 'L', true);
            $this->SetFont('Arial', '', 9);
            $this->MultiCell(160, 7, $factura['direccion'], 1, 'L');
        }
        
        $this->Ln(8);
    }
    
    private function calcularAlturaTexto($texto, $anchoMaximo, $tamanioFuente) {
        $this->SetFont('Arial', '', $tamanioFuente);
        $anchoTexto = $this->GetStringWidth($texto);
        $lineas = ceil($anchoTexto / $anchoMaximo);
        return $lineas * 7;
    }
    
    public function detallesFactura() {
        $detalles = $this->facturaData['detalles'];
        $azul_principal = array(41, 128, 185);
        $azul_claro = array(236, 240, 245);
        
        $this->SetFillColor($azul_principal[0], $azul_principal[1], $azul_principal[2]);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 8, 'DETALLES DE LA FACTURA', 1, 1, 'C', true);
        
        $this->SetFillColor($azul_claro[0], $azul_claro[1], $azul_claro[2]);
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(15, 8, '#', 1, 0, 'C', true);           
        $this->Cell(95, 8, 'Producto/Servicio', 1, 0, 'C', true); 
        $this->Cell(20, 8, 'Cantidad', 1, 0, 'C', true);   
        $this->Cell(30, 8, 'Precio Unit.', 1, 0, 'C', true); 
        $this->Cell(36, 8, 'Subtotal', 1, 1, 'C', true);    
        
        $this->SetFont('Arial', '', 9);
        $total = 0;
        $contador = 1;
        
        foreach ($detalles as $detalle) {
            $subtotal = $detalle['subtotal'];
            $total += $subtotal;
            
            $fill = ($contador % 2 == 0) ? true : false;
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
        return $total;
    }
    
    private function truncateText($text, $length) {
        if (strlen($text) > $length) {
            return substr($text, 0, $length - 3) . '...';
        }
        return $text;
    }

    public function totalesFactura($subtotal) {
        $factura = $this->facturaData['factura'];
        $azul_principal = array(41, 128, 185);
        
        $this->SetFont('Arial', '', 10);
        
      
        $anchoVacio = 125; 
        $anchoEtiqueta = 35;
        $anchoMonto = 30;
        
        $this->Cell($anchoVacio, 8, '', 0, 0);
        $this->Cell($anchoEtiqueta, 8, 'Subtotal:', 0, 0, 'R');
        $this->Cell($anchoMonto, 8, number_format($subtotal, 2, ',', '.'), 0, 1, 'R');
        
        if (isset($factura['descuento_porcentaje']) && $factura['descuento_porcentaje'] > 0) {
            $descuento = $subtotal * ($factura['descuento_porcentaje'] / 100);
            $this->Cell($anchoVacio, 8, '', 0, 0);
            $this->Cell($anchoEtiqueta, 8, 'Descuento ' . $factura['descuento_porcentaje'] . '%:', 0, 0, 'R');
            $this->Cell($anchoMonto, 8, '-' . number_format($descuento, 2, ',', '.'), 0, 1, 'R');
            $subtotalConDescuento = $subtotal - $descuento;
        } else {
            $subtotalConDescuento = $subtotal;
        }
       
        $ivaPorcentaje = isset($factura['iva_porcentaje']) ? $factura['iva_porcentaje'] : 16;
        $iva = $subtotalConDescuento * ($ivaPorcentaje / 100);
        
        $this->Cell($anchoVacio, 8, '', 0, 0);
        $this->Cell($anchoEtiqueta, 8, 'IVA ' . $ivaPorcentaje . '%:', 0, 0, 'R');
        $this->Cell($anchoMonto, 8, number_format($iva, 2, ',', '.'), 0, 1, 'R');
      
        $this->Cell($anchoVacio, 1, '', 0, 0);
        $this->SetDrawColor($azul_principal[0], $azul_principal[1], $azul_principal[2]);
        $this->Cell($anchoEtiqueta + $anchoMonto, 1, '', 'T', 1);
        
        // Total
        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor($azul_principal[0], $azul_principal[1], $azul_principal[2]);
        $this->Cell($anchoVacio, 10, '', 0, 0);
        $this->Cell($anchoEtiqueta, 10, 'TOTAL:', 0, 0, 'R');
        $this->Cell($anchoMonto, 10, number_format($factura['total_general'], 2, ',', '.'), 0, 1, 'R');
        
        $this->Ln(12);
        
        $this->SetTextColor(100, 100, 100);
        $this->SetFont('Arial', 'I', 10);
        $this->Cell(0, 6, '¡Gracias por su preferencia!', 0, 1, 'C');
        $this->Cell(0, 6, 'Para cualquier consulta, contacte a: jlacruzca@gmail.com', 0, 1, 'C');
    }
    
    public function generarPDF() {
        $this->AliasNbPages();
        $this->infoCliente();
        $subtotal = $this->detallesFactura();
        $this->totalesFactura($subtotal);
        
        return $this;
    }
}
?>