<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Zip_splitter extends CI_Controller
{
    public function index()
    {
        $this->load->view('zip_splitter');
    }

    public function procesar_prueba(){
        echo "hola";
    }

    public function procesar()
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);

        $this->limpiar_temporales();

        $token = uniqid('split_', true);

        $tempDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $token;

        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        $tempPdfDir = $tempDir . '/pdfs_temp';

        if (!is_dir($tempPdfDir)) {
            mkdir($tempPdfDir, 0777, true);
        }

        $zipOriginal = $tempDir . DIRECTORY_SEPARATOR . $_FILES['zip_original']['name'];

        move_uploaded_file(
            $_FILES['zip_original']['tmp_name'],
            $zipOriginal
        );

        $zip = new ZipArchive();

        if ($zip->open($zipOriginal) !== TRUE) {
            show_error('No fue posible abrir el ZIP.');
        }

        /*
         * INDEXAMOS UNA SOLA VEZ
         */

        $indice = [];

        for ($i = 0; $i < $zip->numFiles; $i++) {

            $nombreArchivo = $zip->getNameIndex($i);

            $basename = basename($nombreArchivo);

            if (!preg_match('/^(RR\d+XX)/i', $basename, $match)) {
                continue;
            }

            $guia = strtoupper($match[1]);


            $indice[$guia][] = [
                'index' => $i,
                'file'  => $nombreArchivo
            ];
            //$indice[$guia][] = $nombreArchivo;
        }

        $faltantes = [];

        $resultado = [];

        foreach ($_FILES['jsons']['tmp_name'] as $key => $tmpJson) {

            $jsonName = $_FILES['jsons']['name'][$key];

            $contenidoJson = file_get_contents($tmpJson);

            $json = json_decode($contenidoJson, true);

            if (!$json) {
                continue;
            }

            $nombreZipSalida = pathinfo($jsonName, PATHINFO_FILENAME) . '.zip';

            $rutaZipSalida = $tempDir . DIRECTORY_SEPARATOR . $nombreZipSalida;

            $zipSalida = new ZipArchive();

            $zipSalida->open(
                $rutaZipSalida,
                ZipArchive::CREATE | ZipArchive::OVERWRITE
            );

            $agregados = [];

            foreach ($json['Facturas'] as $factura) {

                $guia = strtoupper(trim($factura['Guia']));

                if (!isset($indice[$guia])) {

                    $faltantes[] = [
                        'json' => $jsonName,
                        'guia' => $guia
                    ];

                    continue;
                }

                foreach ($indice[$guia] as $item) {

                    $archivoZip = $item['file'];

                    if (isset($agregados[$archivoZip])) {
                        continue;
                    }

                    $contenido = $zip->getFromName($archivoZip);

                    if ($contenido === false) {
                        continue;
                    }

                    $nombrePdf = basename($archivoZip);

                    $rutaTemporalPdf = $tempPdfDir . '/' . $nombrePdf;

                    // Crear PDF físico temporal
                    file_put_contents(
                        $rutaTemporalPdf,
                        $contenido
                    );

                    // Agregar desde archivo físico
                    /*$zipSalida->addFile(
                        $rutaTemporalPdf,
                        $nombrePdf
                    );*/

                    $zipSalida->addFromString(
                        $i."-".$nombrePdf,
                        $archivoZip
                    );

                    echo $archivoZip." <br>";

                    $agregados[$archivoZip] = true;
                }

                

            }

            

            $zipSalida->close();

            $resultado[] = [
                'nombre' => $nombreZipSalida,
                'token'  => $token
            ];
        }

        $zip->close();

        foreach (glob($tempPdfDir . '/*') as $file) {
            unlink($file);
        }

        rmdir($tempPdfDir);


        /*
         * REPORTE DE FALTANTES
         */

        if (!empty($faltantes)) {

            $csv = fopen(
                $tempDir . DIRECTORY_SEPARATOR . 'faltantes.csv',
                'w'
            );

            fputcsv($csv, ['JSON', 'GUIA']);

            foreach ($faltantes as $row) {

                fputcsv($csv, [
                    $row['json'],
                    $row['guia']
                ]);
            }

            fclose($csv);

            $resultado[] = [
                'nombre' => 'faltantes.csv',
                'token' => $token
            ];
        }

        $data['archivos'] = $resultado;

        $this->load->view(
            'zip_splitter_resultado',
            $data
        );
    }

    public function descargar()
    {
        $token = $this->input->get('token');

        $archivo = $this->input->get('archivo');

        $ruta = sys_get_temp_dir()
            . DIRECTORY_SEPARATOR
            . $token
            . DIRECTORY_SEPARATOR
            . $archivo;

        /*$verifica = new ZipArchive();

        $verifica->open($ruta);

        echo "<pre>";

        for ($x = 0; $x < $verifica->numFiles; $x++) {
            echo $verifica->getNameIndex($x) . PHP_EOL;
        }

        echo "</pre>";
        exit;*/

        

        if (!file_exists($ruta)) {
            show_404();
        }

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($ruta) . '"');
        header('Content-Length: ' . filesize($ruta));

        readfile($ruta);

        exit;
    }

    private function limpiar_temporales()
    {
        foreach (glob(sys_get_temp_dir() . '/split_*') as $folder) {

            if (!is_dir($folder)) {
                continue;
            }

            if ((time() - filemtime($folder)) < 3600) {
                continue;
            }

            $this->eliminar_directorio($folder);
        }
    }

    private function eliminar_directorio($dir)
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);

        foreach ($files as $file) {

            $path = $dir . DIRECTORY_SEPARATOR . $file;

            if (is_dir($path)) {

                $this->eliminar_directorio($path);
            } else {

                unlink($path);
            }
        }

        rmdir($dir);
    }
}
