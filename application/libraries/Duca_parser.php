<?php
//use Smalot\PdfParser\Parser;

class Duca_parser
{
    protected $CI;

    private $ultimo_resultado = [
        'estado' => 'sin_datos',
        'mensaje' => 'No se encontraron datos extraíbles.',
    ];

    public function obtener_ultimo_resultado()
    {
        return $this->ultimo_resultado;
    }
    
    public function __construct()
    {
        // Asignamos la instancia de CodeIgniter a una variable de clase
        $this->CI = &get_instance();
    }

    public function procesar($pdf_path, $duca = '')
    {
        require_once FCPATH . 'application/libraries/pdfparser/src/Smalot/PdfParser/Parser.php';

        //$parser = new Parser();
        /*$parser = new \Smalot\PdfParser\Parser();
        $pdf = $parser->parseFile($pdf_path);
        $text = $pdf->getText();*/

        try {

            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($pdf_path);
            $text = $pdf->getText();
        } catch (\Throwable $e) {

            log_message(
                'error',
                'PDF no compatible: ' . $pdf_path . ' => ' . $e->getMessage()
            );

            $this->ultimo_resultado = [
                'estado' => 'ilegible',
                'mensaje' => 'PDF dañado o ilegible.',
            ];

            return [];
        }

        /*echo "<pre>"; 
        print_r($text);
        exit;*/

        $this->CI->load->model('Gestion_Model');

        $chepito_duca = '';

        /*if (preg_match('/Domicilio\s+Fiscal\s*([A-Z]{3,}[A-Z0-9]+)/i', $text, $match)) {
            $chepito_duca = trim($match[1]);
        }

        $chepito = str_replace($duca, '', $chepito_duca);*/

        $texto_normalizado = preg_replace('/\s+/', ' ', $text);

        if (preg_match('/Domicilio\s+Fiscal\s*:?\s*([A-Z0-9]{4,})/i', $texto_normalizado, $match)) {
            $chepito_duca = trim($match[1]);
        }

        $chepito = str_replace($duca, '', $chepito_duca);

        //Modelo para casos que no tenemos numero tracking correcto en DUCA PDF

        $documentosTransporte = $this->CI->Gestion_Model->obtener_documentostransporte_by_chep($chepito);




        // ============================
        // EXTRAER DUCA Y FECHA
        // ============================

        $text = preg_replace('/\s+/', ' ', $text);

        $fecha = '';

        if (preg_match('/Fecha\s+de\s+Aceptación.*?([0-9]{2}\/[0-9]{2}\/[0-9]{4})/i', $text, $match)) {

            $fecha_original = $match[1];

            $partes = explode('/', $fecha_original);

            if (count($partes) === 3) {
                $fecha = $partes[2] . '-' . $partes[1] . '-' . $partes[0];
            }
        }

        $tasa_cambio = 0;

        if (preg_match('/31\.\s*Tasa\s+de\s+Cambio\s*([\d\.]+)/i', $text, $tc_match)) {
            $tasa_cambio = floatval($tc_match[1]);
        }

        // ============================
        // DIVIDIR POR LINEAS
        // ============================

        $bloques = preg_split('/Liquidación\s+por\s+Línea/i', $text);

        $tracking_data = [];

        $j = 0;


        foreach ($bloques as $bloque) {
            /*echo "<pre>";
        print_r($bloque);
        exit;*/
            if (trim($bloque) == '') continue;

            $bloque = preg_replace('/\s+/', ' ', $bloque);

            if (strpos($bloque, 'Valor en Aduana') === false) continue;
            
            $linea = 1;

            /*if (preg_match('/40\.\sNúmero\s+Línea\b/i', $bloque)) {
                $linea = 1;
            }*/

            // Caso 2: "40. Número de Línea X" → capturar número
            if (preg_match('/40\.\sNúmero\s+de\s+Línea\s([\d,]+)/i', $bloque, $line_match)) {
                $linea = intval(str_replace(',', '', $line_match[1]));
            }

            // ============================
            // TRACKING
            // ============================
            if (preg_match('/Observaciones:\s*([0-9]{13})/i', $bloque, $track_match)) {
                $tracking = $track_match[1];
            } else if (preg_match('/Observaciones:\s*([0-9]+(?:\.[0-9]+)?E\+?[0-9]+)/i', $bloque, $track_match)) {
                $tracking = $documentosTransporte[$j]->DocumentoTransporte;
            } else if (preg_match('/Observaciones:\s*(RR[0-9]+XX)/i', $bloque, $track_match)) {
                $tracking = $track_match[1];
            } else if (preg_match('/Observaciones:\s*(MLSV[0-9]+RG)/i', $bloque, $track_match)) {
                $tracking = $track_match[1];
            } else if (preg_match('/Observaciones:\s*(FD\d{8})/i', $bloque, $track_match)) {
                $tracking = $track_match[1];
            }
             else {
                continue;
            }

            // ============================
            // INICIALIZAR VARIABLES
            // ============================
            $dai = 0;
            $isv = 0;
            $sel = 0;
            $total_general = 0;
            $valor_aduana = 0;

            // ============================
            // 52 - VALOR EN ADUANA
            // ============================
            if (preg_match('/52\.\s*Valor\s+en\s+Aduana\s*([\d,]+\.\d+)/i', $bloque, $m)) {

                $valor_aduana = floatval(str_replace(',', '', $m[1]));
            } elseif (preg_match('/([\d,]+\.\d+)\s+53\.5\.\s*Total\s+General/i', $bloque, $m)) {

                $valor_aduana = floatval(str_replace(',', '', $m[1]));
            }


            // ============================
            // TRIBUTOS (DAI / ISV / SEL)
            // ============================

            if (preg_match('/DAI.*?(?:PAGAR)/i', $bloque, $seccion_match)) {
                $seccion = $seccion_match[0];

                // Extraer todos los números decimales
                preg_match_all('/\d{1,3}(?:,\d{3})*\.\d+/', $seccion, $numeros);
                $valores = $numeros[0];

                // Detectar si existe SEL
                $tiene_sel = strpos($seccion, 'SEL') !== false;

                if ($tiene_sel && count($valores) >= 6) {
                    // Tomar los últimos 3 valores
                    $ultimos = array_slice($valores, -3);

                    $dai = floatval(str_replace(',', '', $ultimos[0]));
                    $isv = floatval(str_replace(',', '', $ultimos[1]));
                    $sel = floatval(str_replace(',', '', $ultimos[2]));
                } elseif (!$tiene_sel && count($valores) >= 4) {
                    // Tomar los últimos 2 valores
                    $ultimos = array_slice($valores, -2);

                    $dai = floatval(str_replace(',', '', $ultimos[0]));
                    $isv = floatval(str_replace(',', '', $ultimos[1]));
                    $sel = 0;
                }
            }


            

            // ============================
            // INICIALIZAR TRACKING SI NO EXISTE
            // ============================
            if (!isset($tracking_data[$tracking])) {
                $tracking_data[$tracking] = [
                    'chepito' => $chepito,
                    'duca' => $duca,
                    'fecha' => $fecha,
                    'lineas' => [],
                    'aduana' => 0,
                    'dai' => 0,
                    'isv' => 0,
                    'sel' => 0,
                    'total_general' => 0,
                    'tasa_cambio' => $tasa_cambio
                ];
            }

            // ============================
            // ACUMULAR POR TRACKING
            // ============================
            $tracking_data[$tracking]['lineas'][] = $linea;
            $tracking_data[$tracking]['aduana'] += $valor_aduana;
            $tracking_data[$tracking]['dai'] += $dai;
            $tracking_data[$tracking]['isv'] += $isv;
            $tracking_data[$tracking]['sel'] += $sel;
            $total_calculado = round($dai + $isv + $sel, 4);

            $tracking_data[$tracking]['total_general'] += $total_calculado;
        
            $j++;


        }


        //var_dump($tracking_data);
        //die();

        if (empty($tracking_data)) {
            $this->ultimo_resultado = [
                'estado' => 'sin_datos',
                'mensaje' => 'PDF leído, pero sin datos extraíbles.',
            ];
        } else {
            $this->ultimo_resultado = [
                'estado' => 'correcta',
                'mensaje' => 'Se extrajeron datos, pero quedaron bloques sin tracking.',
            ];
        }


        return $tracking_data;
    }
}
