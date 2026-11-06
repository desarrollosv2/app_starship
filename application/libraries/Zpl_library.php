<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Zpl_library
{
    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
    }

    public function obtener_guias($orden, $token)
    {
        $url = 'https://app.c807.com/guia.php/reporte/guias?orden=' . $orden;

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

        return json_decode($response, true);
    }

    public function generar_zpl($guias)
    {
        if (!is_array($guias)) return false;

        $zpl = '';
        $total = count($guias);
        $i = 1;

        foreach ($guias as $g) {
            $zpl .= "^XA\n^CI28\n";
            $zpl .= $this->generar_etiqueta($g, $i, $total);
            $zpl .= "^XZ\n";
            $i++;
        }

        return $zpl;
    }


    private function generar_etiqueta($g, $indice, $total)
    {
          $contenido = mb_substr($g['contenido'], 0, 30);
        $direccion_parts = [];

        if (!empty($g['direccion'])) $direccion_parts[] = $g['direccion'];
        if (!empty($g['municipio'])) $direccion_parts[] = $g['municipio'];
        if (!empty($g['departamento'])) $direccion_parts[] = $g['departamento'];

        $direccion_completa = implode(', ', $direccion_parts);
        $direccion = "Dirección: " . mb_substr($direccion_completa, 0, 60);

        $municipio = strtoupper(trim($g['municipio']));
        $municipio_lineas = wordwrap($municipio, 14, "\n"); // corta en 14 máx sin romper palabras
        $lineas = explode("\n", $municipio_lineas);

        $zpl_municipio = '';
        $font_width = 20; // Aprox. ancho por carácter en puntos (ajusta si cambia el font)

        $y = 755;
        foreach ($lineas as $linea) {
            $longitud = mb_strlen($linea);
            $total_width = $longitud * $font_width;

            $x = max(0, round((273 - $total_width) / 2)); // Centrar dentro del panel izquierdo de 273pt de ancho

            $zpl_municipio .= "^FO{$x},{$y}^CF0,40^FD" . $linea . "^FS\n";
            $y += 35; // siguiente línea más abajo
        }


          // Manejo de salto de línea para "especiales" con distintas longitudes
            $primera_linea = mb_substr($g['indicaciones'], 0, 15);
            $resto_texto = mb_substr($g['indicaciones'], 15);
            $lineas_extra = wordwrap($resto_texto, 44, "\n");
            $lineas = explode("\n", $lineas_extra);

            $y = 820;
            $zpl_especiales = '';

            // Etiqueta "Especiales:"
            $zpl_especiales .= "^FO280,{$y}^CF0,25^FDEspeciales:^FS\n";

            // Primera línea (x400)
            $zpl_especiales .= "^FO400,{$y}^AN,N,20,15^FD" . trim($primera_linea) . "^FS\n";

            // Resto de líneas (x280)
            foreach ($lineas as $linea) {
                if (trim($linea) === '') continue;
                $y += 40;
                $zpl_especiales .= "^FO280,{$y}^AN,N,20,15^FD" . trim($linea) . "^FS\n";
            }


            $zpl_pll    = 'PLL';
            $zpl_0mar   = '0 MAR';
            $zpl_telefono   = '7888-9830';
            $zpl_contacto   = 'sacdelivery@grupoprobe.com';
            $zpl_direccion_fija = 'DETALLE SAN MARCOS  COLONIA LOS ALPES AVENIDA LOS APENINOS 1-A POLIGONO 20- PASAJE LIDIA 1 DP31-3- SAN MARCOS- SAN SALVADOR., SAN MARCOS, San Salvador';
            $zpl_departamento   = 'SLV';
            $zpl_pago       = 'Crédito';
            $zpl_peso       = '50.00 LB';
            $zpl_peso_maximo    = '25.00 LB';
            $zpl_docs_a_liquidar = '1) OTROS # DTE-03-M001P002-0000000000001605, Devolver copia de los documentos (1), Firmados y sellados';
            $zpl_lmxjv              = 'LMXJV';

            //Salto de linea para direccion fija
            $primera_linea_dir = mb_substr($zpl_direccion_fija, 0, 38);  
            $resto_dir = mb_substr($zpl_direccion_fija, 38);          

            $lineas_dir_extra = wordwrap($resto_dir, 55, "\n");
            $lineas_dir = explode("\n", $lineas_dir_extra);

            $y_dir = 280;
            $zpl_direccion = '';

            // Etiqueta Dirección:
            $zpl_direccion .= "^FO285,{$y_dir}^CF0,25^FDDirección:^FS\n";

            $zpl_direccion .= "^FO405,{$y_dir}^AN,N,15,10^FD" . $primera_linea_dir . "^FS\n";

            foreach ($lineas_dir as $linea) 
            {
                $y_dir += 40;
                $zpl_direccion .= "^FO285,{$y_dir}^AN,N,15,10^FD" . trim($linea) . "^FS\n";
            }

            //Salto de linea para documento a liquidar fijo
            $primera_linea_doc = mb_substr($zpl_docs_a_liquidar, 0, 54);  
            $resto_doc = mb_substr($zpl_docs_a_liquidar, 54);           

            $lineas_doc_extra = wordwrap($resto_doc, 55, "\n");
            $lineas_doc = explode("\n", $lineas_doc_extra);

            $y_doc = 980;
            $zpl_documentos = '';

            $zpl_documentos .= "^FO280,{$y_doc}^AN,N,15,10^FD" . $primera_linea_doc . "^FS\n";

            foreach ($lineas_doc as $linea) 
            {
                $y_doc += 40;
                $zpl_documentos .= "^FO280,{$y_doc}^AN,N,15,10^FD" . trim($linea) . "^FS\n";
            }

        return "

                FO00,00^GB813,1150,4^FS
                ^FO275,00^GB2,1150,3^FS

                ^FO05,05^GFA,4080,4080,34,,:::::::I07iFC,001iHF,007iHFC,00iIFE,00iJF,01iJF,03iJF8,:03iJFC,07iJFC,:::07gHF8M07CL03FFEM03TFC,07gHFCM01CM07FCM03TFC,07gHFEN0EM03FCM03TFC,07gIFN07M01FCM07TFC,07gIFCM03CM0F8M07TFC,07gJFM03FM0F8M0UFC,07gJFM03F8L07N0UFC,07gFBFFEM03FM07N0UFC,07TF8M01EI03I03EM07M01UFC,07TFCN0CI0F8003CM06M01UFC,07TFEN0C001F80038M06M01UFC,07UFM018001FI03N06M03UFC,07UFCL018003FI07I01CI06M03UFC,07VFL018003EI06I03CI07IF8I07UFC,07UFEL03I03EI0EI07CI07IFJ0VFC,07UFCL03J08001CI07CI07FFEI01VFC,07UF8L078L038I0FCI0IFCI03VFC,07UFJ03IF8L078I0FCI0IF8I03VFC,07UFI01JF8L0F8001F8I0IFJ07VFC,07TFEI07JF8L0FI01F8I0FFEJ0WFC,07TFCI0JFEM0FI03FI01FFCI01WFC,07TFC001JFCM06I03FI01FFCI03WFC,07TF8003JF8M06I03FI03FF8I03WFC,07TF8003JFN06I07EI03FFJ07WFC,07TFI07IFEI01EI06I07EI07FEJ0XFC,07TFI07IFEI03EI04I07CI07FCI01XFC,07TFI07IFCI07EI04I07CI0FF8I03XFC,07TFI07IFCI07CI04I078I0FFJ03XFC,07TFI03IF8I0FCI0CM01FEJ07XFC,07SFEI01FFD8I0F8I0CM01FCJ0YFC,07SFEJ07818I07I01CM03F8I01YFC,07TFL018M01CM07FJ03YFC,07TFL018M03CM0FEJ03YFC,07TFL038M07CL01FCJ07YFC,07TFL03CM0FCL03FCJ0gFC,07TF8K03CL03FEL07F8I01gFC,07TFCK07EL07FEL0FFJ03gFC,07TFEK0FFK01IFK03FEJ03gFC,07UFJ03FFCJ0JF8J0FFCJ07gFC,07UFE003JFI07JFEI03FFCJ0gGFC,07gRFE07gOFC,07iJFC,:::07WFE7LFDJFE3FF7KF8001IFBKFDPFC,07WFEI07CL07007EI03C18I01FFK0F8J07KFC,07WFEI07CL06001FI0381CJ07F8J03CK0KFC,07MFEM03FF80038007M0F800303EJ03FCK0EK07JFC,07MFCM03FFC003I0F8L0FC00203EJ01FCK0FK03JFC,07MF8M07FFC002I0F8L07CJ03CK0F8K0EK07JFC,07MF8L01IFEK01FM07CJ038K0F8J01CK0KFC,07XFEK03FM03CJ03L0FK03CK0KFC,07YFK07FM038J06L07K038J01KFC,07YFK0FFM038J060038006003F07800F83KFC,07YFK0FFM038J04007C006I0FCF8007F7KFC,07KFE003PF8I01FEI0FC0038J0400FE006I01FF8I07LFC,07KFC001PF8I03FE001FC0038M0FC006J03FJ01LFC,07KF8I0PFCI07FE001FE003K08L06J01F8J07KFC,07KFJ07OFCI0FFE003FE003I01F8L06K078J03KFC,07YFCI0FFE003FE003I03FM07K078J01KFC,07YF8I0FFC003FC003I07FM07K03CJ01KFC,07YF8I0FFC003FC007I0FFM0FCJ03EJ01KFC,07YFJ07FC003F8006001FFI0FF3FFEJ03F8J0KFC,07IFCI01PFEJ07FC001FI06001FF001FF3IFCI03FEI01KFC,07IF8I01PFCJ03FCM0E001FFI0FE1FE3F80039FE001KFC,07IFJ03PF8J03F8M0E003FFI0780FC0FE00303F001KFC,07IFJ07PF8J03F8L01C003FF8K07C004006002001KFC,07XFK01F8L03C003FF8K038K04K03KFC,07WFEK01F8L03C003FF8K03L08K03KFC,07WFC002I0F8L07C007FFCK06L08K07KFC,07WF8007I0FM0FC007FFEK0EK018K0LFC,07WFI07I0FL01F8007IFJ03F8J03CJ01LFC,07WFI0FI07I0C007F8007IF8I07FCJ0FFJ03LFC,07WF003FC00F001E01FFC00JFE003IF8003FFE001MFC,07gKF001gVFC,07gJFE001gVFC,::07gJFE003gVFC,07gJFC003gVFC,:07gJFE007gVFC,07iJFC,:03iJFC,03iJF8,:01iJF,00iJF,00iIFE,007iHFC,001iHF,I07iFC,,::::^FS

                ^FX
                ^FO25,150^BQN,2,10
                ^FDLA,{$g['guia']}^FS
                ^FO05,460^GB273,4,4^FS

                ^FO101,488^CF0,55^FD{$zpl_pll}^FS
                ^FO05,550^GB273,4,4^FS
                ^FO75,578^CF0,55^FD{$zpl_0mar}^FS
                ^FO05,640^GB273,4,4^FS
                ^FO101,668^CF0,55^FD{$zpl_departamento}^FS
                ^FO05,730^GB273,4,4^FS
                ^FO40,755^CF0,40^FD{$g['municipio']}^FS
                ^FO05,840^GB273,4,4^FS
                ^FO101,868^CF0,55^FD{$g['servicio']}^FS
                ^FO05,930^GB273,4,4^FS
                ^FO35,970^CF0,40^FD^FS
                ^FO05,1085^GB813,4,4^FS
                ^FO90,1110^AEN,35,20^FD{$zpl_lmxjv}^FS

                ^FO285,20^CF0,40^FD{$g['sede']}^FS
                ^FO285,80^CF0,25^FDTeléfono:^FS
                ^FO405,80^AN,N,15,10^FD{$zpl_telefono}^FS
                ^FO285,120^CF0,33^FDDestino:^FS
                ^FO405,120^AN,N,15,10^FD{$g['municipio']}^FS
                ^FO285,160^CF0,25^FDNombre:^FS
                ^FO405,160^AN,N,15,10^FD{$g['destinatario']}^FS
                ^FO285,200^CF0,25^FDTeléfono:^FS
                ^FO405,200^AN,N,15,10^FD{$zpl_telefono}^FS
                ^FO285,240^CF0,25^FDContacto:^FS
                ^FO405,240^AN,N,15,10^FD{$zpl_contacto}^FS
                {$zpl_direccion}


                ^FO285,440^CF0,25^FDReferencia:^FS
                ^FO405,440^AN,N,15,10^FD{$g['referencia']}^FS
                ^FO285,480^CF0,25^FDContenido:^FS
                ^FO405,480^AN,N,15,10^FD{$g['contenido']}^FS
                ^FO285,520^CF0,25^FDNo. Orden:^FS
                ^FO405,520^CF0,25^FD{$g['orden']}^FS
                ^FO280,560^GB528,1,1^FS

                ^FO280,590^CF0,30^FD{$g['guia']}^FS
                ^FO720,590^CF0,30^FD{$indice} de {$total}^FS

                ^FO280,660^CF0,25^FDPAGO:^FS
                ^FO460,660^AN,N,20,15^FD{$zpl_pago}^FS
                ^FO280,700^CF0,25^FDPeso:^FS
                ^FO460,700^AN,N,20,15^FD{$zpl_peso}^FS
                ^FO280,740^CF0,25^FDPeso Máximo:^FS
                ^FO460,740^AN,N,20,15^FD{$zpl_peso_maximo}^FS
                ^FO280,780^CF0,25^FDTipo de entrega:^FS
                ^FO460,780^CF0,30^FD{$g['tipo_entrega']}^FS
                {$zpl_especiales}
                ^FO280,940^CF0,25^FDDocs a liquidar:^FS
                {$zpl_documentos}
                ^FO360,1055^CF0,30^FDCobrar a destinatario: {$g['cod_monto']}^FS

                ^FO300,1120^ADN,10,4^FDwww.c807xpress.com/sv^FS
                ^FO575,1095^GB2,50,2^FS
                ^FO630,1120^ADN,10,4^FD72087895^FS

                ^FX
                ^FO575,635^BQN,2,8
                ^FDLA,{$g['guia']}^FS



                ";
    }
}
