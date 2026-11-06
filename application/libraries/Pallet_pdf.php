<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'third_party/tcpdf/tcpdf.php';

/**
 * Etiqueta del pallet: numero y tipo de producto en grande, para pegar en el pallet.
 * Se genera al vuelo, por lo que puede reimprimirse en cualquier momento.
 */
class Pallet_pdf
{

    const MM_POR_PT = 0.3528;

    /**
     * @param object $pallet  fila de PalletModel::get_with_relations
     * @param string $formato 'A4' (horizontal) | 'ETIQUETA' (100x150 mm vertical)
     * @return string bytes del PDF
     */
    public function generar_etiqueta($pallet, $formato = 'A4')
    {
        if ($formato === 'ETIQUETA') {
            $pdf = new TCPDF('P', 'mm', array(100, 150), TRUE, 'UTF-8', FALSE);
            $margen = 5;
        } else {
            $pdf = new TCPDF('L', 'mm', 'A4', TRUE, 'UTF-8', FALSE);
            $margen = 12;
        }

        // Prefijo del tipo + correlativo del tipo (ej. CN-000013): es lo que se escanea para retomar el pallet
        $numero = $pallet->tipo_prefijo . '-' . str_pad($pallet->numero_pallet, 6, '0', STR_PAD_LEFT);

        $pdf->SetCreator('Logistica Inversa');
        $pdf->SetTitle('Pallet ' . $numero);
        $pdf->SetPrintHeader(FALSE);
        $pdf->SetPrintFooter(FALSE);
        $pdf->SetMargins($margen, $margen, $margen);
        $pdf->SetAutoPageBreak(FALSE);
        $pdf->AddPage();

        $ancho = $pdf->getPageWidth() - 2 * $margen;
        $alto = $pdf->getPageHeight() - 2 * $margen;
        $y = $margen;

        $pdf->SetLineWidth(0.8);
        $pdf->Rect($margen, $margen, $ancho, $alto);

        // Cliente y fecha
        $h = $alto * 0.08;
        $this->texto($pdf, 'CLIENTE: ' . mb_strtoupper($pallet->cliente_nombre, 'UTF-8'), '', $margen, $y, $ancho, $h, 0.55);
        $y += $h;

        $h = $alto * 0.07;
        $this->texto($pdf, 'PALLET N°', 'B', $margen, $y, $ancho, $h, 0.7);
        $y += $h;

        // Numero de pallet: lo mas grande posible
        $h = $alto * 0.30;
        $this->texto($pdf, $numero, 'B', $margen, $y, $ancho, $h, 0.95);
        $y += $h;

        $pdf->Line($margen + 4, $y, $margen + $ancho - 4, $y);
        $y += $alto * 0.02;

        // Tipo de producto
        $h = $alto * 0.20;
        $this->texto($pdf, mb_strtoupper($pallet->tipo_legible, 'UTF-8'), 'B', $margen, $y, $ancho, $h, 0.8);
        $y += $h + $alto * 0.02;

        // Codigo de barras del pallet
        $h = $alto * 0.20;
        $bw = min($ancho * 0.6, 140);
        $pdf->write1DBarcode(
            $numero,
            'C128',
            $margen + ($ancho - $bw) / 2,
            $y,
            $bw,
            $h * 0.75,
            0.4,
            array('text' => FALSE, 'stretch' => TRUE)
        );
        $y += $h * 0.8;

        $h = $alto * 0.08;
        $this->texto($pdf, 'Creado: ' . date('d/m/Y H:i', strtotime($pallet->created_at)), '', $margen, $y, $ancho, $h, 0.45);

        return $pdf->Output('pallet_' . $numero . '.pdf', 'S');
    }

    /**
     * Escribe una linea centrada con el mayor tamano de fuente que quepa en el ancho y en
     * la fraccion $relleno del alto de la franja.
     */
    private function texto($pdf, $texto, $estilo, $x, $y, $ancho, $alto, $relleno)
    {
        $pdf->SetFont('helvetica', $estilo, 100);
        $por_ancho = 100 * ($ancho - 8) / max($pdf->GetStringWidth($texto), 1);
        $por_alto = ($alto * $relleno) / self::MM_POR_PT;
        $pdf->SetFont('helvetica', $estilo, min($por_ancho, $por_alto));

        $pdf->SetXY($x, $y);
        $pdf->Cell($ancho, $alto, $texto, 0, 0, 'C', FALSE, '', 0, FALSE, 'T', 'M');
    }
}
