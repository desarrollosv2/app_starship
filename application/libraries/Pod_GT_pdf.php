<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . 'libraries/fpdf/fpdf.php');
require_once APPPATH.'libraries/phpqrcode/qrlib.php';


class Pod_GT_pdf extends FPDF
{
    public function __construct()
    {
        parent::__construct('P', 'mm', 'Letter');
    }

    public function setDatos($data)
    {
        $this->data = $data;
    }

    protected function getMultiCellHeight($w, $h, $txt)
    {
        $x = $this->GetX();
        $y = $this->GetY();

        $this->MultiCell($w, $h, $txt, 0);
        $height = $this->GetY() - $y;

        $this->SetXY($x, $y);
        return $height;
    }

    protected function NbLines($w, $txt)
    {
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0) {
            $w = $this->w - $this->rMargin - $this->x;
        }
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 && $s[$nb - 1] == "\n") {
            $nb--;
        }
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;

        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ') {
                $sep = $i;
            }
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j) {
                        $i++;
                    }
                } else {
                    $i = $sep + 1;
                }
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else {
                $i++;
            }
        }
        return $nl;
    }



    public function Logo()
    {
        $this->Image(getcwd(). '/public/imagenes/logo_2.png', 15, 15, 70);
        $this->SetFont('Arial', 'B', 12);
        $this->SetXY(15, 40);
        $this->Cell(185.9, 7, utf8_decode('PRUEBA DE ENTREGA DE PAQUETERÍA A ÚLTIMA MILLA'), 0, 1, 'C');
    }
    
    public function QR($url)
    {
        $qrSize = 25;
        $pageWidth = $this->GetPageWidth();
        $marginRight = 15;

        $x = $pageWidth - $marginRight - $qrSize;
        $y = 15;

        // Crear archivo temporal
        $tempDir = sys_get_temp_dir();
        $filePath = $tempDir . DIRECTORY_SEPARATOR . 'qr_' . md5($url) . '.png';

        // Generar QR local
        if (!file_exists($filePath)) {
            QRcode::png(
                $url,            // contenido
                $filePath,       // archivo
                QR_ECLEVEL_L,    // corrección de errores
                4,               // tamaño
                1                // margen
            );
        }

        // Insertar imagen QR en PDF
        $this->Image($filePath, $x, $y, $qrSize, $qrSize);

        // Borrar archivo temporal
        unlink($filePath);
    }


    public function Encabezado()
    {
        $margenIzq = $this->lMargin;
        $margenDer = $this->rMargin;
        $anchoUtil = $this->GetPageWidth() - $margenIzq - $margenDer;

        $gap = 2;

        $anchoCol1 = $anchoUtil * 0.40; 
        $anchoCol2 = $anchoUtil * 0.35; 
        $anchoCol3 = $anchoUtil * 0.25; 

        $xCol1 = $margenIzq;
        $xCol2 = $xCol1 + $anchoCol1 + $gap;
        $xCol3 = $xCol2 + $anchoCol2 + $gap;

        $yInicio = 55;

        $this->SetFont('Arial', 'B', 11);

        $this->SetXY($xCol1, $yInicio);
        $this->Cell($anchoCol1, 6, 'Datos de entrega', 0, 0);

        $this->SetXY($xCol2, $yInicio);
        $this->Cell($anchoCol2, 6, 'Datos del Manifiesto', 0, 0);

        $this->SetXY($xCol3, $yInicio);
        $this->Cell($anchoCol3, 6, 'Estado de Entrega', 0, 1);

        $yContenido = $this->GetY() + 2;

        $this->SetXY($xCol1, $yContenido);

        $this->SetFont('Arial', 'B', 9);
        $this->Cell(25, 5, 'Fecha:', 0, 0);
        $this->SetFont('Arial', '', 9);
        $this->Cell($anchoCol1 - 25, 5, $this->data['fecha_entrega'], 0, 1);

        $this->SetX($xCol1);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(25, 5, 'Piloto:', 0, 0);

        $xPiloto = $this->GetX();
        $yPiloto = $this->GetY();

        $this->SetFont('Arial', '', 9);
        $this->SetXY($xPiloto, $yPiloto);
        $this->MultiCell($anchoCol1 - 25, 5, utf8_decode($this->data['piloto']), 0, 'L');
        $this->SetX($xCol1);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(25, 5, 'Placa:', 0, 0);
        $this->SetFont('Arial', '', 9);
        $this->Cell($anchoCol1 - 25, 5, utf8_decode($this->data['placa']), 0, 1);

        $this->SetX($xCol1);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(25, 5, 'Marchamo:', 0, 0);
        $this->SetFont('Arial', '', 9);
        $this->Cell($anchoCol1 - 25, 5, utf8_decode($this->data['marchamo']), 0, 1);

        $this->SetX($xCol1);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(25, 5, 'Proveedor LMD:', 0, 0);
        $this->SetFont('Arial', '', 9);
        $this->MultiCell($anchoCol1 - 25, 5, utf8_decode($this->data['proveedor']), 0, 1);

        // Dirección (MultiCell)
        $this->SetX($xCol1);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(25, 5, utf8_decode('Dirección:'), 0, 0);

        $xDir = $this->GetX();
        $yDir = $this->GetY();

        $this->SetFont('Arial', '', 9);
        $this->SetXY($xDir, $yDir);
        $this->MultiCell($anchoCol1 - 25, 5, utf8_decode($this->data['direccion']), 0, 'L');

        $yFinalCol1 = $this->GetY();


        $this->SetXY($xCol2, $yContenido);

        $this->SetFont('Arial', 'B', 9);
        $this->Cell(35, 5, 'AWB (MIA-SAL):', 0, 0);
        $this->SetFont('Arial', '', 9);
        $this->Cell($anchoCol2 - 35, 5, $this->data['master'], 0, 1);

        
        $this->SetX($xCol2);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(35, 5, 'AWB (CN-MIA):', 0, 0);
        $this->SetFont('Arial', '', 9);
        $this->Cell($anchoCol2 - 35, 5, $this->data['master_cn'], 0, 1);

        $this->SetX($xCol2);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(35, 5, 'Paquetes Master:', 0, 0);
        $this->SetFont('Arial', '', 9);
        $this->Cell($anchoCol2 - 35, 5, number_format($this->data['total_paquetes_master']), 0, 1);

        $this->SetX($xCol2);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(35, 5, 'Cajas Master:', 0, 0);
        $this->SetFont('Arial', '', 9);
        $this->Cell($anchoCol2 - 35, 5, number_format($this->data['total_cajas_master']), 0, 1);

        $this->SetX($xCol2);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(35, 5, 'Paquetes entregados:', 0, 0);
        $this->SetFont('Arial', '', 9);
        $this->Cell($anchoCol2 - 35, 5, number_format($this->data['paquetes_entregados']), 0, 1);

        $yFinalCol2 = $this->GetY();

 
        $this->SetXY($xCol3, $yContenido);

        $this->SetFont('Arial', 'B', 9);
        $this->Cell(34, 5, 'Paquetes a entregar:', 0, 0);
        $this->SetFont('Arial', '', 9);
        $this->Cell($anchoCol3 - 34, 5, number_format($this->data['paquetes_actual']), 0, 1);


        $this->SetX($xCol3);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(34, 5, 'Paquetes Pendientes:', 0, 0);
        $this->SetFont('Arial', '', 9);
        $this->Cell($anchoCol3 - 34, 5, number_format($this->data['paquetes_pendientes']), 0, 1);


        $yFinalCol3 = $this->GetY();


        $yLineaInicio = $yInicio;
        $yLineaFin    = max($yFinalCol1, $yFinalCol2, $yFinalCol3);

        $this->Line($xCol2 - ($gap / 2), $yLineaInicio, $xCol2 - ($gap / 2), $yLineaFin);
        $this->Line($xCol3 - ($gap / 2), $yLineaInicio, $xCol3 - ($gap / 2), $yLineaFin);

        $this->SetY($yLineaFin + 10);
    }



    public function Detalle()
    {
            $margenIzq = $this->lMargin;
            $margenDer = $this->rMargin;
            $anchoUtil = $this->GetPageWidth() - $margenIzq - $margenDer;

        if (!empty($this->data['detalle']))
        {

            $this->SetFont('Arial', 'B', 11);
            $this->Cell($anchoUtil, 7, utf8_decode('Detalle de paquetes'), 0, 1, 'C');
            $this->Ln(2);

            $anchoRef = $anchoUtil * 0.70;
            $anchoPaq = $anchoUtil * 0.30;

            $this->SetFont('Arial', 'B', 9);
            $this->Cell($anchoRef, 7, 'Referencia', 1, 0, 'C');
            $this->Cell($anchoPaq, 7, 'Paquetes', 1, 1, 'C');

            $this->SetFont('Arial', '', 9);

            foreach ($this->data['detalle'] as $detalle) 
            {

                // Si se acerca al final de la página
                if ($this->GetY() > 250) {
                    $this->AddPage();

                    $this->SetFont('Arial', 'B', 9);
                    $this->Cell($anchoRef, 7, 'Referencia', 1, 0, 'C');
                    $this->Cell($anchoPaq, 7, 'Paquetes', 1, 1, 'C');
                    $this->SetFont('Arial', '', 9);
                }

                $this->Cell($anchoRef, 7, utf8_decode($detalle->referencia), 1, 0);

                $this->Cell( $anchoPaq, 7, number_format($detalle->paquetes_agregados), 1, 1, 'C');
            }

            $this->Ln(10);
        }



        if ($this->GetY() > 240) {
            $this->AddPage();
        }

        $this->SetFont('Arial', 'B', 10);
        $this->Cell($anchoUtil, 7, utf8_decode('Resumen de entrega'), 0, 1, 'C');
        $this->Ln(3);

        $this->SetFont('Arial', 'B', 9);
        $this->Cell(20, 6, 'MAWB:', 0, 0);
        $this->SetFont('Arial', '', 9);
        $this->Cell(70, 6, $this->data['master'], 0, 0);

        $this->SetFont('Arial', 'B', 9);
        $this->Cell(20, 6, '', 0, 0);
        $this->SetFont('Arial', '', 9);
        $this->Cell(20, 6, '', 0, 0);

        $this->SetFont('Arial', 'B', 9);
        $this->Cell(20, 6, 'Paquetes:', 0, 0);
        $this->SetFont('Arial', '', 9);
        $this->Cell(20, 6, number_format($this->data['paquetes_actual']), 0, 1);

        $this->Ln(8);

        $label = 40;
        $linea = 50;
        $espacio = 5;

        $this->SetFont('Arial', 'B', 9);
        $this->Cell($label, 7, 'Nombre de recibido:', 0, 0);
        $this->SetFont('Arial', '', 9);
        $this->Cell($linea, 7, '', 'B', 0);

        $this->Cell($espacio, 7, '', 0, 0);

        $this->SetFont('Arial', 'B', 9);
        $this->Cell($label, 7, 'Firma de recibido:', 0, 0);
        $this->SetFont('Arial', '', 9);
        $this->Cell($linea, 7, '', 'B', 1);

        $this->Ln(6);

        $this->SetFont('Arial', 'B', 9);
        $this->Cell($label, 7, 'Fecha y hora:', 0, 0);
        $this->SetFont('Arial', '', 9);
        $this->Cell($linea, 7, '', 'B', 0);

        $this->Cell($espacio, 7, '', 0, 0);

        $this->SetFont('Arial', 'B', 9);
        $this->Cell($label, 7, 'Sello:', 0, 0);
        $this->Cell($linea, 7, '', 0, 1);
    }

}