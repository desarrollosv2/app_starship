<?php

class Reporte_impuestos_gt
{
    public function procesar($pdf_path)
    {
        require_once FCPATH . 'application/libraries/pdfparser/src/Smalot/PdfParser/Parser.php';

        try {

            $parser = new \Smalot\PdfParser\Parser();

            $pdf = $parser->parseFile($pdf_path);

            $text = $pdf->getText();

        } catch (\Throwable $e) {

            log_message(
                'error',
                'PDF no compatible: '.$pdf_path.' => '.$e->getMessage()
            );

            return [];
        }
        

        $text = preg_replace('/\s+/', ' ', $text);


        // Extraer y devolver los valores relevantes
        return $this->extraerDatos($text);
    }

    private function extraerDatos($text)
    {
        

        $resultado = [
            'orden' => '',
            'acceso' => '',
            'formulario' => ''
        ];


        // Intentar capturar el número OEA (Número de Orden) buscando el patrón OEA seguido de dígitos
        if (preg_match('/\bOEA\s*([0-9]+)/iu', $text, $m)) {
            $resultado['orden'] = 'OEA' . $m[1];
        } else {
            // Fallback: buscar texto "Número de Orden" y extraer alfanuméricos
            if (preg_match('/N[úu]mero\s+de\s+Orden(?:\s*\([^)]*\))?\s*:??\s*([A-Z0-9\-\s]+)/iu', $text, $m)) {
                $orden = preg_replace('/[^A-Z0-9]/i', '', $m[1]);
                $resultado['orden'] = $orden;
            }
        }

        // Número de Acceso (ej. "Número de Acceso 254 230 669") -> "254 230 669"
        if (preg_match('/N[úu]mero\s+de\s+Acceso\s*:??\s*([0-9]{1,3}(?:\s+[0-9]{1,3})+)/iu', $text, $m)) {
            $acceso = preg_replace('/\s+/', ' ', trim($m[1]));
            $resultado['acceso'] = $acceso;
        }

        // Número de Formulario (ej. "Número de Formulario 51 677 702 418"), eliminar sufijos de página como "3" o "3 de 4"
        if (preg_match('/N[úu]mero\s+de\s+Formulario\s*:??\s*([0-9]{1,3}(?:\s+[0-9]{1,3})+)/iu', $text, $m)) {
            $form = preg_replace('/\s+/', ' ', trim($m[1]));
            // Eliminar sufijo de página si existe (ej. " 3" o " 3 de 4")
            $form = preg_replace('/\s+\d+(?:\s+de\s+\d+)?$/i', '', $form);
            $resultado['formulario'] = $form;
        }

        return $resultado;
    }
}