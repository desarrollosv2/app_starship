<?php
defined('BASEPATH') or exit('No direct script access allowed');


class ZplController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function imprimir_guia()
    {
        // Paso 1: Obtener las guías desde un endpoint externo
        $miOrden = 'GD0086673';
        $url = 'https://app.c807.com/guia.php/reporte/guias?orden=' . $miOrden; // ← reemplaza con tu URL real
        $token = 'f4b64fdd2a0f04640cf50a6c79257c38d4f1771c'; // ← reemplaza con tu token real si aplica

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_HTTPHEADER => array(
                "Authorization: $token",
                "Accept: application/json"
            )
        ));
        $response = curl_exec($curl);
        curl_close($curl);

        // Decodificar JSON
        $guias = json_decode($response, true);

        // Validación básica
        if (!is_array($guias)) {
            echo "Error: no se pudo obtener la información de guías.";
            return;
        }

        $zpl = '';

        // Paso 2: Generar etiquetas (2 copias por guía)
        foreach ($guias as $guia) {
            for ($i = 1; $i <= 2; $i++) {
                $zpl .= "^XA\n^CI28\n";
                $zpl .= $this->generar_etiqueta_zpl($guia, $i);
                $zpl .= "^XZ\n";
            }
        }

        // Paso 3: Manejar salida
        if ($this->input->get('view') === '1') {
            header('Content-Type: text/plain');
            echo $zpl;
            return;
        }

        if ($this->input->get('download') === '1') {
            $this->load->helper('download');
            force_download("etiquetas_guias.zpl", $zpl);
            return;
        }

        if ($this->input->get('print') === '1') {
            $this->enviar_a_impresora($zpl, '192.168.1.100', 9100);
            echo "Etiqueta enviada a impresora.";
            return;
        }

        echo "Parámetro faltante. Usa ?view=1, ?download=1 o ?print=1";
    }


    private function generar_etiqueta_zpl($g, $copia = 1)
    {
        $contenido = mb_substr($g['contenido'], 0, 30);
        $direccion_parts = [];

        if (!empty($g['direccion'])) $direccion_parts[] = $g['direccion'];
        if (!empty($g['municipio'])) $direccion_parts[] = $g['municipio'];
        if (!empty($g['departamento'])) $direccion_parts[] = $g['departamento'];

        $direccion_completa = implode(', ', $direccion_parts);
        $direccion = "Dirección: " . mb_substr($direccion_completa, 0, 60);
        return "
^CF0,26
^FO30,30^BQN,2,10
^FDLA,{$g['guia']}^FS

^CF0,26

^FO250,30^FDTeléfono:^FS
^FO250,60^FDDestino: {$g['municipio']}^FS
^FO250,90^FDNombre: {$g['destinatario']}^FS
^FO250,120^FDContacto:^FS

^FO250,150^FD{$direccion}^FS
^FO250,180^FDTeléfono:^FS
^FO250,210^FDReferencia: {$g['referencia']}^FS
^FO250,240^FDContenido: {$contenido}^FS
^FO250,270^FDNo. Orden: {$g['orden']}^FS

^FO30,310^GB750,2,2^FS

^CF0,30

^CF0,40

^FO30,330^FDHCOJ^FS
^FO31,330^FDHCOJ^FS

^FO30,370^FDC-CD002^FS
^FO31,370^FDC-CD002^FS

^FO30,410^FDC-CJ003^FS
^FO31,410^FDC-CJ003^FS

^FO30,450^FDCAB^FS
^FO31,450^FDCAB^FS

^FO30,490^FD{$g['municipio']}^FS
^FO31,490^FD{$g['municipio']}^FS

^FO30,530^FDSER^FS
^FO31,530^FDSER^FS

^FO30,570^FDL X V^FS
^FO31,570^FDL X V^FS





^CF0,28
^FO300,360^FB450,1,,C^FD807-17-{$g['id']}-{$copia}^FS
^FO300,395^FB450,1,,C^FD{$copia} de 2^FS



^CF0,26
^FO300,510^FDPeso:^FS
^FO650,510^FD2.00 LB^FS

^FO300,540^FDPeso Máximo:^FS
^FO650,540^FD50.00 LB^FS

^FO300,570^FDEspeciales:^FS
^FO650,570^FDPRUEBA^FS

^FO300,600^FDPAGO:^FS
^FO650,600^FDCrédito^FS

^FO300,630^FDTipo de entrega:^FS
^FO650,630^FD{$g['tipo_entrega']}^FS


^FO250,660^FDwww.c807xpress.com/sv^FS
^FO600,660^FD72087895^FS

^FO30,700^GB750,2,2^FS
";
    }

    private function enviar_a_impresora($zpl, $ip, $puerto)
    {
        $fp = fsockopen($ip, $puerto, $errno, $errstr, 10);
        if (!$fp) {
            echo "Error al conectar con la impresora: $errstr ($errno)";
        } else {
            fwrite($fp, $zpl);
            fclose($fp);
        }
    }
}
