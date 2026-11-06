<?php

class Facturaelectronica
{
    //   funccion  para  validar  campos  requeridos antes de  emitir el comprobante
    //  esta funcion la  pondremos  antes de emitir el  DTE
    public function ValidaDatosCl($id_cliente, $id_tipo_comprobante)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        //echo  'valores  capturadoss' . $id_tipoevento .'---' .$idVenta;
        //echo 'echo el  tipo de comprobante que se va  a emitir es ' . $id_tipo_comprobante. " cliente: " . $id_cliente;

        $OK         = 'ok';
        $retornoval =  '';
        $campos_rellenar  =   array();


        $id_empresa  =  $_SESSION["empresa_id"];
        $MP          =  &get_instance();
        $MP->load->model('DTES_Model');
        if ($id_tipo_comprobante ==  1) {
            //echo 'Validando Credito Fiscal';
            $dato_Client =  $MP->DTES_Model->DatosValidarCCF($id_cliente);
        } elseif ($id_tipo_comprobante == 2  || $id_tipo_comprobante == 7) {
            $dato_Client =  $MP->DTES_Model->DatosValidarFC($id_cliente);
        } elseif ($id_tipo_comprobante == 4) {
            $dato_Client =  $MP->DTES_Model->DatosValidarFEX($id_cliente);
        }
        //var_dump($dato_Client);

        foreach ($dato_Client as   $clave => $valor) {
            $valre = '';
            $valre = $this->retornarValue($valor);
            if ($valre != '') {
                $OK = $valre;
            } else {
                $OK         = 'ok';
            }
            if ($OK == 'Error') {
                $retornoval  .= $clave . ', ';
                //$campos_rellenar  =   array();
                array_push(
                    $campos_rellenar,
                    $clave
                );
            }
        }
        if ($retornoval != '') {
            array_push(
                $campos_rellenar,
                'IDC' . $id_cliente
            );
            $OK = $campos_rellenar;
        }
        //var_dump($OK);

        return json_encode($OK);
    }
    public function retornarValue($valor)
    {
        if ($valor == '' || $valor  == null ||  $valor  == 'null') {
            // echo  'valores  vacios!!!!!'.'<br>';
            return 'Error';
        }
    }

    //  Funcion para controlar los errores de generacion de DTE
    public function CtrlError($resulgeneracio, $id_cliente, $proceso, $idventa)
    {
        ini_set('memory_limit', '2048M');
        error_reporting(E_ALL);
        ini_set("display_errors", 1);
        ini_set('max_allowed_packet', '504288000');
        ini_set('innodb_log_file_size', '3000000');
        ini_set('wait_timeout', '4500000');

        $MP          =  &get_instance();
        $MP->load->model('DTES_Model');
        $expM          =  &get_instance();
        $expM->load->model('Exportacion_Model');
        $guia_ma      = 0;
        $guia_no      =  '';


        $datos['Guias_Cuerpoexp']  =   $expM->Exportacion_Model->guias_expfacturadasFE($idventa);
        foreach ($datos['Guias_Cuerpoexp'] as $rowma) {
            $guia_ma = $rowma->ma;
            if ($guia_ma == 1) {
                $guia_no = $rowma->guia_no;
            }
        }


        $mensaReresult =  json_decode($resulgeneracio, true);
        $dato_Client =  $MP->DTES_Model->DatosValidar($id_cliente);
        //var_dump($dato_Client);
        $casillero    = $dato_Client->codigo_cliente;
        $id_tipo_comprobante =  $dato_Client->id_tipo_comprobante;
        $tcomp  =  "";
        if ($id_tipo_comprobante == 1) {
            $tcomp  =  "CCF";
        } elseif ($id_tipo_comprobante == 2) {
            $tcomp  =  "FC";
        } elseif ($id_tipo_comprobante == 7) {
            $tcomp  =  "FCdtK";
        } elseif ($id_tipo_comprobante == 4) {
            $tcomp  =  "FEX";
        }

        // segmento para  manejar los errores
        $error = '';
        $eRR   = '';
        // var_dump($mensaReresult);
        if (isset($mensaReresult['errores'])) {
            $error = $mensaReresult['errores'];    //echo  $error;
        }
        if ($error != '') {       // echo  'Se tiene que  notificar porque  ha  surgido    un error en la creacion del DTE' .'<br>';
            //var_dump($error);

            // echo '#######################################'.'<BR>';
            //var_dump($error);
            // receptor:numero_documento

            if (isset($error["receptor: codigo_actividad"])) {
                $eRR =  "receptor: " . $error["receptor: codigo_actividad"];
                //  echo '1';
            } elseif (isset($error["receptor: numero_documento"])) {
                //echo '2';
                $eRR =  "receptor: " . $error["receptor: numero_documento"];
            } elseif (isset($error["receptor:codigo_actividad"])) {
                //echo '3';
                $eRR =  "receptor: " . $error["receptor: codigo_actividad"];
            } elseif (isset($error["receptor:numero_documento"])) {
                // echo '4';
                $eRR = "receptor: " . $error["receptor: numero_documento"];
            } elseif (isset($error["municipio"])) {
                $eRR =  $error["municipio"];
            } elseif (isset($error["departamento"])) {

                $eRR =  $error["departamento"];
                //  echo 'el mensaje de  error  a mostrar es :'. $eRR . '<BR>';
            } elseif (isset($error['responsable: numero_documento'])) {
                $eRR =  'responsable: ' . $error['responsable: numero_documento'];
            } elseif (isset($error['solicitante: numero_documento'])) {
                $eRR =  'solicitante: ' . $error['solicitante: numero_documento'];
            } else {
                $eRR = 'Error  fuera de los parametos controlados';
            }
            /*if($eRR!='') {
                $eRR ='Error  fuera de los parametos controlados';
                //sendmails($eRR);

            }*/
            //return  $eRR;

            $this->sendmails(json_encode($mensaReresult['errores']), $casillero, $proceso, $tcomp, $guia_no);
            if ($proceso == 1) {
                $MP->DTES_Model->reenviar_gfe($idventa);
            }

            return  json_encode($mensaReresult['errores']);
        } else {
            return  $eRR;
        }
    }



    /* ESPACIO PARA FACTURACION ELECTRONICA */
    public function generarfacturaelectronica($id_tipoevento, $idVenta)
    {
        ini_set('memory_limit', '2048M');
        error_reporting(E_ALL);
        ini_set("display_errors", 1);
        ini_set('slave_max_allowed_packet', '9073741824');
        ini_set('innodb_log_file_size', '3000000');
        ini_set('wait_timeout', '4500000');
        echo  'valores  capturadoss' . $id_tipoevento . '---' . $idVenta . '<br>';
        //echo 'echo el  tipo de comprobante que se va  a emitir es' . $id_tipo_comprobante;
        $_SESSION["empresa_id"]     = 47;
        $_SESSION["nombre_sucursal"] = 1;



        $id_empresa  =  $_SESSION["empresa_id"];
        $MP          =  &get_instance();
        $MP->load->model('DTES_Model');
        $expM          =  &get_instance();
        $expM->load->model('Exportacion_Model');
        $impM          =  &get_instance();
        $impM->load->model('Importacion_Model');

        $confM          =  &get_instance();
        $confM->load->model('Conf_model');
        $wh_numerotodoIN = '';



        $dat_Cabz                    =   $MP->DTES_Model->DTES_Cabecera($idVenta);
        $datos['Cuerpo']             =   $expM->Exportacion_Model->obtener_servicios_facturadosFE($idVenta);
        //var_dump($datos['Cuerpo']);

        //  echo  'el  tipo  de  evento  a procesar es  ' . $id_tipoevento;
        if ($id_tipoevento ==  1) {

            $datos['Cuerpo_Guias']   =   $impM->Importacion_Model->guias_impfacturadas($idVenta);
            //  echo 'se tienen que buscar  quias de  IMPORTACION';

            //  var_dump($datos['Cuerpo_Guias']);
        } elseif ($id_tipoevento ==  2) {
            // echo 'se tienen que buscar  quias de  exportacion';
            $datos['Guias_Cuerpoexp']  =   $expM->Exportacion_Model->guias_expfacturadasFE($idVenta);
            //  var_dump($datos['Guias_Cuerpoexp']);
        }

        if (isset($datos['Cuerpo_Guias'])) {
            if (!empty($datos['Cuerpo_Guias'])) {
                // echo  'las   guias de   importacion facturadas  son ';
                // var_dump($datos['Cuerpo_Guias']);

            }
        }
        // exit;
        $receptor                    =   array();
        $detalle1                    =   array();
        $detalle2                    =   array();
        $detalle3                    =   array();
        $documento_rel               =   array();
        $venta_tercero               =   array();
        $footer                      =   array();
        $apendice1                   =   array();
        $apendice2                   =   array();
        $apendice3                   =   array();
        $extension                   =  null; // array(null);

        $acumulado                   =   0;
        $total                       =   0;
        $contador                    =   0;
        $contador1                   =   1;
        $codigo_pais                 =   '9300';
        $retener_iva                 =  false;



        $niterecero                  = '';
        $nombre_cl_tercero           = '';
        $cod_generacion              = '';
        $fecha_comprobanteCCF        = '';
        $venta_tercero               =  null;
        $pCaracter                   = '';
        $tipo_documento              = '0';
        $kdx_iva_gravado1           = 0;

        //  generamos los  encabezados del  comprobate
        if (!empty($dat_Cabz)) {
            $id                          =   $dat_Cabz->id;
            $dias_credito                =   intval($dat_Cabz->dias_credito);
            $codigo_cliente              =   $dat_Cabz->codigo_cliente;
            $nombre_cliente              =   $dat_Cabz->nombre_cliente;
            $direccion                   =   $dat_Cabz->direccion . ', ' . $dat_Cabz->nombre_municipio;
            $nombre_contribuyente        =   $dat_Cabz->nombre_contribuyente;
            $id_clasificacion_fiscal     =   $dat_Cabz->id_clasificacion_fiscal;
            $registro_iva                =   str_replace([' ', '-', '&'], '', $dat_Cabz->registro_iva);
            $giro_iva                    =   $dat_Cabz->giro_iva;
            //$tipo_documento              =   $dat_Cabz->tipo_documento;
            $nit                         =   trim(str_replace([' ', '-', '&'], '', $dat_Cabz->nit));
            $dui                         =   $dat_Cabz->dui;
            $email_contacto              =   trim($dat_Cabz->email_contacto);
            $email_contacto1             =   $dat_Cabz->email_contacto1;
            $email_contacto2             =   $dat_Cabz->email_contacto2;
            $email_contacto3             =   $dat_Cabz->email_contacto3;

            $correos_cc                  =   $dat_Cabz->correo_cc;
            $id_tipo_pago                =   intval($dat_Cabz->id_tipo_pago);
            //  echo 'TIPO PAGO  CLIENTE' . $id_tipo_pago;
            $descripcion                 =   $dat_Cabz->descripcion;
            $id_tipo_contribuyente       =   $dat_Cabz->id_tipo_contribuyente;
            $sujeto_ret_1                =   $dat_Cabz->sujeto_ret_1;
            $telefono_contacto           =   str_replace([' ', '-', '&'], '', $dat_Cabz->telefono_contacto);

            $id_sucursal_empresa         =   $dat_Cabz->id_sucursal_empresa;
            $cod_activ_economica         =   $dat_Cabz->cod_activ_economica;
            $venta_propia                =    (float)$dat_Cabz->venta_propia;
            $retencion_1_por_ciento      =   $dat_Cabz->retencion_1_por_ciento;
            $sub_total_cuenta_ajena      =    (float)$dat_Cabz->sub_total_cuenta_ajena;
            $venta_total                 =   (float)$dat_Cabz->venta_total;


            $establecimiento             =   str_pad($id_sucursal_empresa, 4, "0", STR_PAD_LEFT);
            $establecimiento             =   str_pad($id_sucursal_empresa, 4, "0", STR_PAD_LEFT);
            $establecimiento             =   str_pad($id_sucursal_empresa, 4, "0", STR_PAD_LEFT);
            $id_tipo_comprobante         =   $dat_Cabz->id_tipo_comprobante;
            $id_comprob_afecto           =    $dat_Cabz->id_comprob_afecto;



            $id_depatamento              =   $dat_Cabz->cod_departamento;
            //$id_municipio                =   $dat_Cabz->cod_municipio;

            $id_municipio                =   $dat_Cabz->codDistrito;

            //$id_depatamento              =   str_pad($dat_Cabz->id_depatamento, 2, "0", STR_PAD_LEFT);
            //$id_municipio                =   str_pad($dat_Cabz->id_municipio, 2, "0", STR_PAD_LEFT);
            $id_tipo_empresa             =   intval($dat_Cabz->id_tipo_empresa);
            $numero_comprobante          =    $dat_Cabz->comprobante_afecto;


            # para la  aplicacion de la  nota de  credito

            //$numero_control              =   $dat_Cabz->numero_control;
            // $cod_generacion              =   $dat_Cabz->cod_generacion;
            $idCliente                   =   $dat_Cabz->id_cliente;

            if ($establecimiento != '0001' || $establecimiento != '0002') {
                $establecimiento = '0001';
            }
            //   evaluaremos que   tipo  de  documento  tiene  rgistradoe el cliente
            if ($id_tipo_comprobante == 1 || $id_tipo_comprobante ==  3 || $id_tipo_comprobante == 4) {


                if ($_SESSION["empresa_id"]  == 46) {

                    if ($id_tipo_empresa ==  1) { // persona natural
                        $numero_documento  = $dui;
                        $tipo_documento   = '13';
                    } elseif ($id_tipo_empresa ==  2) {
                        $numero_documento  = $nit;  //'0021245-5'
                        $tipo_documento   = '36';
                    }
                } elseif ($_SESSION["empresa_id"]  == 47) {

                    if ($id_tipo_empresa ==  1) { // persona natural
                        if ($dat_Cabz->nit == $dat_Cabz->dui) {
                            // echo    'son niguales '.  '<br>';
                            $numero_documento  = $dat_Cabz->dui;
                            $tipo_documento   = '13';
                        } else {
                            //   echo    'no...............................................son niguales '.  '<br>';
                            $numero_documento  = $nit;
                            $tipo_documento   = '36';
                        }
                    } elseif ($id_tipo_empresa ==  2) {
                        $numero_documento  = $nit;  //'0021245-5'
                        $tipo_documento   = '36';
                    }

                    $pCaracter  = substr($dat_Cabz->dui, 0, 3);

                    if ($pCaracter == 'PSP') {
                        $tipo_documento   = '03';
                        $numero_documento = substr($dat_Cabz->dui, 4);
                    } elseif ($pCaracter == 'RSD') {
                        $tipo_documento   = '02';
                        $numero_documento = substr($dat_Cabz->dui, 4);
                    }

                    $pCaracter  = substr($dat_Cabz->nit, 0, 3);

                    if ($pCaracter == 'RTN') {
                        $tipo_documento   = '37';
                    }
                }



                //$tipo_documento   = '36';
                //$numero_documento = $nit;




            } elseif ($id_tipo_comprobante == 2 || $id_tipo_comprobante ==  7) {
                $numero_documento = $dat_Cabz->dui;

                if ($numero_documento == 'null' || $numero_documento == null || $numero_documento == null   || $numero_documento == 'NULL' || $numero_documento == '' || $numero_documento == '0'  || $numero_documento == 0) {
                    $numero_documento = '';
                } else {

                    $pCaracter  = substr($dat_Cabz->dui, 0, 3);

                    if ($pCaracter == 'PSP') {
                        $tipo_documento   = '03';
                    } elseif ($pCaracter == 'RSD') {
                        $tipo_documento   = '02';
                    } else {
                        $tipo_documento   = '13';
                    }
                }

                if ($_SESSION["empresa_id"]  == 46) {

                    if ($id_tipo_empresa ==  1) { // persona natural
                        $numero_documento  = $dui;
                        $tipo_documento   = '13';
                    } elseif ($id_tipo_empresa ==  2) {
                        $numero_documento  = $nit;  //'0021245-5'
                        $tipo_documento   = '36';
                    }
                } elseif ($_SESSION["empresa_id"]  == 47) {
                    if ($id_tipo_empresa ==  1) { // persona natural
                        $numero_documento  = $dui;
                        $tipo_documento   = '13';
                    } elseif ($id_tipo_empresa ==  2) {
                        $numero_documento  = $nit;  //'0021245-5'
                        $tipo_documento   = '36';
                    }

                    $pCaracter  = substr($dat_Cabz->dui, 0, 3);

                    if ($pCaracter == 'PSP') {
                        $tipo_documento   = '03';
                        $numero_documento = substr($dat_Cabz->dui, 4);
                    } elseif ($pCaracter == 'RSD') {
                        $tipo_documento   = '02';
                        $numero_documento = substr($dat_Cabz->dui, 4);
                    }
                } elseif ($_SESSION["empresa_id"]  == 47) {
                    if ($id_tipo_empresa ==  1) { // persona natural
                        if ($dat_Cabz->nit == $dat_Cabz->dui) {
                            // echo    'son niguales '.  '<br>';
                            $numero_documento  = $dat_Cabz->dui;
                            $tipo_documento   = '13';
                        } else {

                            $numero_documento  = $nit;
                            $tipo_documento    = '36';
                        }
                    } elseif ($id_tipo_empresa == 2) {
                        $numero_documento  = $nit;  //'0021245-5'
                        $tipo_documento   = '36';

                        $pCaracter  = substr($dat_Cabz->dui, 0, 3);

                        if ($pCaracter == 'RFC' || $pCaracter == 'RUC') {
                            $tipo_documento   = '37';
                            $numero_documento = $dat_Cabz->dui;
                        }
                        $pCaracter  = substr($dat_Cabz->dui, 0, 3);

                        if ($pCaracter == 'PSP') {
                            $tipo_documento   = '03';
                            $numero_documento = $pCaracter;
                        } elseif ($pCaracter == 'RSD') {
                            $tipo_documento   = '02';
                        } else {
                            //  $tipo_documento   = '13';
                        }
                    }
                }


                // $tipo_documento   = '13';

            }






            /* if($dat_Cabz->nit!=0 || $dat_Cabz->nit!='0'  ||  $dat_Cabz->nit!= '') {
                 $tipo_documento   = '36';
                 $numero_documento = $nit;
                // echo 'SE PRESENTAA EL NIT';
             }elseif($dat_Cabz->dui != 0 || $dat_Cabz->dui != '0') {
                 //echo 'SE PRESENTAA EL dui************';

                 // extraermos los primeros caracteres  del  DUI
                 // RSD = carnet de  residente
                 // PSP = pasaporte
                 $numero_documento = $dat_Cabz->dui;
                 $pCaracter  = substr($dat_Cabz->dui, 0, 3);


                 if($pCaracter=='PSP') {
                     $tipo_documento   = '03';
                 } elseif($pCaracter=='RES') {
                     $tipo_documento   = '02';
                 } else {

                     /*$longitud =    strlen($dui) ;
                     if($longitud <10) {
                         //echo   'Se tiene que  genarar  un  formato para el   documento';
                         //  extrremos el    ultimo caracter
                         $ultimocaracter = substr($numero_documento, -1);
                         $primeros       = substr($numero_documento, 0, -1);  // devuelve "abcde"
                         $duiformateado  =  $primeros . '-' .$ultimocaracter ;
                         $numero_documento            = $duiformateado;

                     }
                     $tipo_documento   = '13';
                 }
             }*/
            // ESTOS  DATOS  TIENE QUE SER  REEMPLAZADO CON LOS  DATOS  DEL   CLIENTE
            //$codigo_actividad   = '61101'; //
            $codigo_actividad   = $cod_activ_economica;

            $departamento       = $id_depatamento;
            $municipio          = $id_municipio;

            //$departamento       = '06';
            //$municipio          = '14';


            /*if($_SESSION["empresa_id"] == 46 ){
                 $correo             ='oscarprogram@gmail.com';
                 $correos_cc         = 'desarrollosv@c807.com;majrpor010@gmail.com;alfredo.bran@c807.com,ritadelpilar@tntsal.com;ritadelpilar@c807.com;ritadelpilar@mistarship.com';

            }else{*/
            // $correos_cc . ';'.
            //'oscarprogram@gmail.com';

            $correo             = 'desarrollosv2@c807.com';

            $correos_cc1        = $email_contacto1 . ';' . $email_contacto2 . ';' . $email_contacto3;
            $correo             = 'desarrollosv2@c807.com';

            $correos_cc         =  '';
            $correos_cc1       =  '';
            //}




            //$correo             ='oscarprogram@gmail.com';
            //$correos_cc         = '';
            // $correos_cc         = $email_contacto1 .';' .$email_contacto2 . ';'.  $email_contacto3 ;

            // $correos_cc         = 'desarrollosv@c807.com;majrpor010@gmail.com;alfredo.bran@c807.com,ritadelpilar@tntsal.com;ritadelpilar@c807.com;';
            //  $correos_cc1         = 'desarrollosv@c807.com;majrpor010@gmail.com;alfredo.bran@c807.com,ritadelpilar@tntsal.com;ritadelpilar@c807.com;ritadelpilar@mistarship.com';



            // echo  'el numero de  documento que se enviara es'.  $tipo_documento  . '###'. $numero_documento ;

            // . ';' .
            //  echo  'Notificar  a los   siguientes corrreos:  ' . $correos_cc;
            //$correos_cc         = 'desarrollosv@c807.com;majrpor010@gmail.com;alfredo.bran@c807.com';
            //echo  'El registro de  iva es:  ' . $registro_iva .' ###';

            $correos_cc1        = $email_contacto1 . ';' . $email_contacto2 . ';' .  $email_contacto3;
            $correo             = $email_contacto;
            $correos_cc         =  'desarrollosv@c807.com;desarrollosv2@c807.com;desarrollosv3@mistarship.com;ritadelpilar@c807.com;ritadelpilar@mistarship.com' . ';' . $correos_cc1;

            if ($id_tipo_comprobante == 1) {
                //  echo  'generando receptor para CREDITO FISCAAL';
                $receptor = array(
                    'tipo_documento'         =>  $tipo_documento,
                    'numero_documento'       =>  $numero_documento,
                    'nrc'                    =>  $registro_iva,
                    'nombre'                 =>  $nombre_cliente,
                    'codigo_actividad'       =>  $codigo_actividad,
                    'direccion' => array(
                        'departamento'       =>    $departamento,
                        'municipio'          =>    $municipio,
                        'complemento'        =>    $direccion
                    ),
                    'correo'                 =>  $correo,
                    'correos_cc'             =>   $correos_cc,
                    'nombre_comercial'       =>   $nombre_contribuyente,
                );
            } elseif ($id_tipo_comprobante == 2 || $id_tipo_comprobante == 7) {

                //  echo  'generando receptor para tipo  de  comprobante  factura  o ticket';
                /*
                                    $nit                         =   trim(str_replace([' ','-','&'], '', $dat_Cabz->nit));
                                    $dui                         =   $dat_Cabz->dui;

                                    if($id_tipo_empresa ==  1)
                                    {
                                        // persona natural
                                        $numero_documento  = $dui;
                                        $tipo_documento   = '13';
                                    }
                                    elseif($id_tipo_empresa ==  2)
                                    {
                                        $numero_documento  = $nit;  //'0021245-5'
                                        $tipo_documento   = '36';
                                    }
                                */
                if ($tipo_documento ==  '0' &&  $numero_documento == '' ||  $numero_documento == null) {
                    $receptor = array(
                        // 'tipo_documento'        =>   $tipo_documento,
                        //'numero_documento'      =>   $numero_documento,
                        'nombre'                =>   $nombre_cliente,
                        'correo'                =>   $correo,
                        'correos_cc'            =>   $correos_cc,
                        //'telefono'               =>   $telefono_contacto
                    );
                } else {

                    $receptor = array(
                        'tipo_documento'        =>   $tipo_documento,
                        'numero_documento'      =>   $numero_documento,
                        'nombre'                =>   $nombre_cliente,
                        'correo'                =>   $correo,
                        'correos_cc'            =>   $correos_cc
                        //'telefono'               =>   $telefono_contacto
                    );
                }
            } elseif ($id_tipo_comprobante == 4) {

                $receptor = array(
                    'tipo_documento'            =>   $tipo_documento,
                    'numero_documento'          =>   $numero_documento,
                    'nombre'                    =>   $nombre_cliente,
                    'descripcion_actividad'     =>   $giro_iva,
                    'codigo_pais'               =>   $codigo_pais,
                    'complemento'               =>   $direccion,
                    'tipo_persona'              =>   $id_tipo_empresa,
                    'correo'                    =>   $correo,
                    'correos_cc'                =>   $correos_cc,
                    //'telefono'                  =>   $telefono_contacto
                );
            } elseif ($id_tipo_comprobante == 3) {
                //echo  "se va  a emitir  una nota de credito";
                $receptor =  array(
                    'tipo_documento'        =>  $tipo_documento,
                    'numero_documento'      =>  $numero_documento,
                    'nrc'                   =>  $registro_iva,
                    'nombre'                =>  $nombre_cliente,
                    'codigo_actividad'      =>  $codigo_actividad,

                    'direccion'             =>  array(
                        'departamento'   =>  $departamento,
                        'municipio'      =>  $municipio,
                        'complemento'    =>  $direccion
                    ),
                    'correo'                =>   $correo,
                    'correos_cc'            =>   $correos_cc,
                    //'telefono'               =>   $telefono_contacto
                );
                //  datos del   comprobante  que  se va   a  anular  con la   nota de  credito
                //  para  obtener los  datos del  dte que se  genero cuando se creo la  factura
                $DATOS_ccf =   $confM->Conf_model->validar_comprobante_cxcFE($id_comprob_afecto);
                //var_dump($DATOS_ccf);
                if (!empty($DATOS_ccf)) {
                    $cod_generacion    = $DATOS_ccf[0]->cod_generacion;
                    $fecha_comprobanteCCF = date('Y-m-d', strtotime($DATOS_ccf[0]->fecha_comprobante));
                    //echo  'Los  datos que  con los que  se sgenro   son '.'<br>';
                    //echo  $cod_generacion.'<br>';
                    // echo  $fecha_comprobanteCCF.'<br>';


                }
                if ($cod_generacion != 'NULL' || $cod_generacion != null) {
                    array_push(
                        $documento_rel,
                        array(
                            'tipo_documento'        =>  '03',
                            'tipo_generacion'       =>   2,
                            'numero_documento'      =>   $cod_generacion,
                            'fecha_emision'         =>   $fecha_comprobanteCCF
                        )
                    );
                }
            }
        }
        //  determinamos si  se trata de  una  nota de  credito que  se va  emitir para  obtener los  datos de la
        //  fatura que se  habia  emitido
        // if($id_tipo_comprobante == 3){
        //  $DATOS_ccf =   $confM->Conf_model->validar_comprobante_cxc($idCliente, $numeroComprobante);
        // }



        if ($retencion_1_por_ciento > 0) {
            $retener_iva =  true;
        }
        $cctaj           =  0;
        $cltotal         =  1;
        $totalCtaj       =  0;
        $sumaventapropia =  0;
        $sumaventactaj   =  0;
        $acumulado1      =  0;
        $kdx_gravado     =  0;
        $NOkdx_gravado   =  0;
        $kdx_iva_gravado_a_terceros = 0;
        $kdx_gravadoTercero =  0;
        foreach ($datos['Cuerpo'] as $fila) {
            $kdx_cuenta_ajena           =  0;
            // $id_catalogo_producto       =  0;
            $id_tipo_venta              =   $fila->id_tipo_venta;
            $cantidad                   =   intval($fila->cantidad);
            $id_catalogo_producto       =   $fila->id_catalogo_producto;
            $descripcion                =   $fila->descripcion;
            $id_hacienda                =   $fila->id_hacienda;
            $precio_neto                =   (float)$fila->precio_neto;
            $kdx_cuenta_ajena           =   $fila->kdx_cuenta_ajena;
            $kdx_iva_gravado            =   (float)$fila->kdx_iva_gravado;
            $descuento                  =   (float)$fila->descuento;
            $kdx_vta_total              =   (float)$fila->kdx_vta_total;
            $acumulado1                 +=    $kdx_vta_total;
            $kdx_iva_gravado_a_terceros =  (float)$fila->kdx_iva_gravado_a_terceros;
            $IVA                        =   number_format(($fila->precio_neto * 1.13), 6);

            /*if($kdx_iva_gravado ==   0   &&  $kdx_iva_gravado_a_terceros>0) {
                $kdx_iva_gravado = $kdx_iva_gravado_a_terceros;
            }*/

            //$kdx_gravado_a_terceros,


            //echo  'el catalod de  producto extraido es ' . $id_catalogo_producto .'<br>';
            /*if($id_tipoevento == 1 || $id_tipoevento == 2) {
                if($id_catalogo_producto !=21  && $id_catalogo_producto !=31  && $id_catalogo_producto !=32) {
                    //  echo  'Acumulando####### '  .'<br>';
                    $acumulado                  +=  (float)$fila->kdx_vta_total;
                    //   echo  'Acumulando####### '  . $acumulado  .'<br>';
                }

            } else {
                $acumulado                  +=  (float)$fila->kdx_vta_total;
            }*/
            //$cctaj                      +=1;
            //$totalCtaj                  +=1;
            // echo  'el tipo de  venta a procesar es'. $id_hacienda .'<br>';
            /*if($id_hacienda == 1) { // para la venta  gravada
                // echo  'el tipo de  venta a procesar es'. $id_hacienda .  'producto ' . $id_catalogo_producto.'<br>';
                if($id_tipoevento == 1 || $id_tipoevento == 2) {
                    if($id_catalogo_producto !=21  && $id_catalogo_producto !=31  && $id_catalogo_producto !=32) {
                        $sumaventapropia +=  $fila->kdx_vta_total;
                        $kdx_gravado     +=  $fila->kdx_gravado;
                        $kdx_gravadoTercero += $fila->kdx_gravado_a_terceros;


                    }
                } else {

                    //   echo  'el tipo de  venta a procesar es'. $id_hacienda .  'producto ' . $id_catalogo_producto.'<br>';

                    $sumaventapropia    +=  $fila->kdx_vta_total;
                    $kdx_gravado        +=  $fila->kdx_gravado;
                    $kdx_gravadoTercero += $fila->kdx_gravado_a_terceros;


                }
            } elseif($id_hacienda == 0) {
                $sumaventactaj +=  $fila->kdx_vta_total;

                //echo  'el tipo de  venta a procesar es'. $id_hacienda .  'producto ' . $id_catalogo_producto.'<br>';


            } else {

                // echo  'el tipo de  venta a procesar es'. $id_hacienda .  'producto ' . $id_catalogo_producto.'<br>';

                if($id_tipoevento == 1 || $id_tipoevento == 2) {
                    if($id_catalogo_producto !=21  && $id_catalogo_producto !=31  && $id_catalogo_producto !=32) {
                        //    echo  'el producto' . $id_catalogo_producto. 'ALMACENANDO EN EL KARDEX NO GRAAVADO'.'<br>';

                        $NOkdx_gravado   +=  (float)$fila->precio_neto;
                    }
                } else {
                    $NOkdx_gravado   +=  (float)$fila->precio_neto;

                }
            }
            $totalCtaj  +=$kdx_cuenta_ajena;*/


            // GENERACION DE LOS  CUERPOS  PARA EL  DTE
            if ($id_tipo_comprobante == 1) { // Credito  fiscal
                if ($id_tipoevento == 1 || $id_tipoevento == 2) {
                    if ($id_catalogo_producto != 21 && $id_catalogo_producto != 31 && $id_catalogo_producto != 32) {
                        if ($id_hacienda == '1') {
                            array_push(
                                $detalle1,
                                array(
                                    'tipo'              =>  2,
                                    'cantidad'          =>  intval($cantidad),
                                    'unidad_medida'     =>  59,
                                    'descripcion'       =>  $descripcion,
                                    'precio_unitario'   =>  $precio_neto,
                                    'tipo_venta'        =>  $id_hacienda,
                                    'descuento'         =>  $descuento,
                                    'tributos'          =>  array(
                                        0 => array(
                                            'codigo'    =>   "20",
                                            'monto'     =>   (float)round($kdx_iva_gravado, 6) //0, //  $kdx_iva_gravado
                                        )
                                    )
                                )
                            );
                        } elseif ($id_hacienda == '2') {
                            array_push(
                                $detalle2,
                                array(
                                    'tipo'              =>    2,
                                    'cantidad'          =>    intval($cantidad),
                                    'unidad_medida'     =>    59,
                                    'descripcion'       =>    $descripcion,
                                    'descuento'         =>    $descuento,
                                    'precio_unitario'   =>    $precio_neto,
                                    'tipo_venta'        =>    $id_hacienda
                                )
                            );
                        }
                    }
                } else {
                    if ($id_hacienda == '1') {
                        array_push(
                            $detalle1,
                            array(
                                'tipo'              =>  2,
                                'cantidad'          =>  intval($cantidad),
                                'unidad_medida'     =>  59,
                                'descripcion'       =>  $descripcion,
                                'precio_unitario'   =>  $precio_neto,
                                'tipo_venta'        =>  $id_hacienda,
                                'descuento'         =>  $descuento,
                                'tributos'          =>  array(
                                    0 => array(
                                        'codigo'    =>   "20",
                                        'monto'     =>  (float)round($kdx_iva_gravado, 6)
                                    )
                                )
                            )
                        );
                        // para que los valores  excentos  y excentos  a terceros los  maneje como  valores no
                        //  sujetos
                    } elseif ($id_hacienda == '2' || $id_hacienda == '3') {
                        array_push(
                            $detalle2,
                            array(
                                'tipo'              =>    2,
                                'cantidad'          =>    intval($cantidad),
                                'unidad_medida'     =>    59,
                                'descripcion'       =>    $descripcion,
                                'descuento'         =>    $descuento,
                                'precio_unitario'   =>    $precio_neto,
                                'tipo_venta'        =>    $id_hacienda
                            )
                        );
                    }
                }
            } elseif ($id_tipo_comprobante == 2 || $id_tipo_comprobante == 7) {


                //$precio_neto                =   (float)$fila->precio_neto;
                //$kdx_cuenta_ajena           =   $fila->kdx_cuenta_ajena;
                //$kdx_iva_gravado            =   (float)$fila->kdx_iva_gravado;


                if ($id_tipoevento == 1 || $id_tipoevento == 2) {
                    if ($id_catalogo_producto != 21 && $id_catalogo_producto != 31 && $id_catalogo_producto != 32) {
                        if ($id_hacienda == '1') {
                            array_push(
                                $detalle1,
                                array(
                                    'tipo'              =>  2,
                                    'cantidad'          =>  intval($cantidad),
                                    'unidad_medida'     =>  59,
                                    'descripcion'       =>  $descripcion,
                                    'precio_unitario'   => (float)(round($kdx_vta_total, 6)),
                                    'descuento'         =>  (float)(number_format($descuento, 2)),
                                    'codigo'            =>  '0'
                                )
                            );
                        } elseif ($id_hacienda == '2' || $id_hacienda == '3') {
                            array_push(
                                $detalle2,
                                array(
                                    'tipo'               =>    2,
                                    'cantidad'           =>    intval($cantidad),
                                    'unidad_medida'      =>    59,
                                    'descripcion'        =>    $descripcion,
                                    'precio_unitario'    =>    $precio_neto,
                                    'tipo_venta'         =>    $id_hacienda,
                                    'descuento'          =>    (float)(number_format($descuento, 2)),
                                    'codigo'             =>    '0'
                                )
                            );
                        }
                    }
                } else {
                    if ($id_hacienda == '1') {
                        array_push(
                            $detalle1,
                            array(
                                'tipo'              =>  2,
                                'cantidad'          =>  intval($cantidad),
                                'unidad_medida'     =>  59,
                                'descripcion'       =>  $descripcion,
                                'precio_unitario'   =>  round($kdx_vta_total, 2),
                                'descuento'         =>  (float)(number_format($descuento, 2)),
                                'codigo'            =>  '0'
                            )
                        );
                    } elseif ($id_hacienda == '2' || $id_hacienda == '3') {
                        array_push(
                            $detalle2,
                            array(
                                'tipo'               =>    2,
                                'cantidad'           =>    intval($cantidad),
                                'unidad_medida'      =>    59,
                                'descripcion'        =>    $descripcion,
                                'precio_unitario'    =>    $precio_neto,
                                'tipo_venta'         =>    $id_hacienda,
                                'descuento'          =>    (float)(number_format($descuento, 2)),
                                'codigo'             =>    '0'
                            )
                        );
                    }
                }
            } elseif ($id_tipo_comprobante == 4) {
                if ($id_tipoevento == 1 || $id_tipoevento == 2) {
                    if ($id_catalogo_producto != 21 && $id_catalogo_producto != 31 && $id_catalogo_producto != 32) {
                        if ($id_hacienda == '1') {
                            array_push(
                                $detalle1,
                                array(
                                    'cantidad'          =>      intval($cantidad),
                                    'unidad_medida'     =>      59,
                                    'descripcion'       =>      $descripcion,
                                    'precio_unitario'   =>      (float)$IVA,
                                    'codigo'            =>      '0',
                                    'descuento'         =>      $descuento
                                )
                            );
                        } elseif ($id_hacienda == '2' || $id_hacienda == '3') {
                            array_push(
                                $detalle2,
                                array(
                                    'cantidad'          =>      intval($cantidad),
                                    'unidad_medida'     =>      59,
                                    'descripcion'       =>      $descripcion,
                                    'precio_unitario'   =>      $precio_neto,
                                    'tipo_venta'        =>      $id_hacienda,
                                    'codigo'            =>      "0",
                                    'descuento'         =>      $descuento
                                )
                            );
                        }
                    }
                } else {
                    if ($id_hacienda == '1') {
                        array_push(
                            $detalle1,
                            array(
                                'cantidad'          =>      intval($cantidad),
                                'unidad_medida'     =>      59,
                                'descripcion'       =>      $descripcion,
                                'precio_unitario'   =>      (float)$IVA,
                                'codigo'            =>      '0',
                                'descuento'         =>      $descuento
                            )
                        );
                    } elseif ($id_hacienda == '2' || $id_hacienda == '3') {
                        array_push(
                            $detalle2,
                            array(
                                'cantidad'          =>      intval($cantidad),
                                'unidad_medida'     =>      59,
                                'descripcion'       =>      $descripcion,
                                'precio_unitario'   =>      $precio_neto,
                                'tipo_venta'        =>      $id_hacienda,
                                'codigo'            =>      "0",
                                'descuento'         =>      $descuento
                            )
                        );
                    }
                }
            } elseif ($id_tipo_comprobante == 3) {
                if ($id_hacienda == '1') {
                    array_push(
                        $detalle1,
                        array(
                            'tipo'              =>  2,
                            'cantidad'          =>  intval($cantidad),
                            'unidad_medida'     =>  59,
                            'descripcion'       =>  $descripcion,
                            'precio_unitario'   =>  $precio_neto,
                            'tipo_venta'        =>  $id_hacienda,
                            'numero_documento'  =>  $cod_generacion,

                            'tributos'          =>  array(
                                0  =>  array(
                                    'codigo'      =>   '20',
                                    'monto'       =>   $kdx_iva_gravado
                                )
                            )
                        )
                    );
                } else {
                    array_push(
                        $detalle2,
                        array(
                            'tipo'              =>    2,
                            'cantidad'          =>    intval($cantidad),
                            'unidad_medida'     =>    59,
                            'descripcion'       =>    $descripcion,
                            'precio_unitario'   =>    $precio_neto,
                            'tipo_venta'        =>    $id_hacienda,
                            'numero_documento'  =>    $cod_generacion
                        )
                    );
                }
            }
            // creamos el segmento de la  informacion del proveedor de servicios de terceros
            if ($id_catalogo_producto == 139) {
                $venta_tercero  =   array(
                    'nit'           =>      '94502404871017',
                    'nombre'        =>      'UNITED AIRLINES, INC.SUCURSAL EL SALVADOR'
                );
            }
            // segmento para  dibujar los  apendices  donde  va la  cuenta  ajena
            /*if($sumaventactaj > 0) {*/
            if ($kdx_cuenta_ajena > 0) {
                $cctaj                      += 1;
                $cltotal                    += 1;
                array_push(
                    $apendice1,
                    array(
                        'campo'             =>    'Linea ' . $cctaj,
                        'etiqueta'          =>    $descripcion,
                        'valor'             =>    '$' . number_format($kdx_cuenta_ajena, 2)
                    )
                );
            }
            /*}*/
        }
        //  echo  'EL  ACUMULADO DE LOS SERVICIOS ES ' . $acumulado.'<BR>';
        //  echo 'el tipo de evento  antes DRE  RECORRER LAS  GUIAS  ES '.$id_tipoevento;
        if ($id_tipoevento == 1) {
            //  buscamos las guias de  importacion

            if (isset($datos['Cuerpo_Guias'])) {
                if (!empty($datos['Cuerpo_Guias'])) {
                    //var_dump(empty($datos['Cuerpo_Guias']));
                    foreach ($datos['Cuerpo_Guias'] as   $rowguia) {
                        $fecha        = date("Y-m-d", strtotime($rowguia->fecha_comprobante));
                        $guia_no      = $rowguia->wh_numero;
                        $pais         = $rowguia->Destino;
                        $peso         = $rowguia->peso_neto;
                        $precio_neto  = $rowguia->cargo_basico;

                        $cantidad     = 1;
                        $id_hacienda  = '2';
                        $descuento    = 0;
                        //$acumulado                  +=  (float)$precio_neto;
                        //echo 'Sumando al  kardex no sujeto las  guias' .'<br>';
                        //$NOkdx_gravado +=  (float)$precio_neto;

                        //echo 'EL NUEVO KARDEX NO GRAVADO' .$NOkdx_gravado.'<br>';


                        //' ' .$fecha.
                        if ($id_tipo_comprobante == 1 || $id_tipo_comprobante == 2 || $id_tipo_comprobante == 7) {
                            array_push(
                                $detalle3,
                                array(
                                    'tipo'              =>    2,
                                    'cantidad'          =>    intval($cantidad),
                                    'unidad_medida'     =>    59,
                                    'descripcion'       =>    ' ' . $guia_no . ' ' . $pais . ' ' . $peso . ' Lb',
                                    'precio_unitario'   =>    (float)$precio_neto,
                                    'tipo_venta'        =>    $id_hacienda, //  se  muestran como  ventas  no sujetas
                                    'descuento'         =>    (float)(number_format($descuento, 2)),
                                    'codigo'             =>    '0'
                                )
                            );
                        } elseif ($id_tipo_comprobante == 4) {
                            array_push(
                                $detalle3,
                                array(
                                    'cantidad'          =>      intval($cantidad),
                                    'unidad_medida'     =>      59,
                                    'descripcion'       =>      ' ' . $guia_no . ' ' . $pais . ' ' . $peso . ' kg',
                                    'precio_unitario'   =>      (float)$precio_neto,
                                    'tipo_venta'        =>      2,
                                    'codigo'            =>      "0",
                                    'descuento'         =>      $descuento
                                )
                            );
                            // $NOkdx_gravado   +=  (float)$precio_neto;
                        }
                    }
                    //echo  'EL  ACUMULADO DE LOS SERVICIOS ES ' . $acumulado .'DESPUES DE GUIAS IMPORTACION'.'<BR>';
                }
            }
        } elseif ($id_tipoevento == 2) {
            //  echo  'a  generar   guias  de  exportacion';
            if (isset($datos['Guias_Cuerpoexp'])) {
                if (!empty($datos['Guias_Cuerpoexp'])) {
                    $sumaiva = 0;
                    $sumaprecio_neto = 0;

                    foreach ($datos['Guias_Cuerpoexp'] as   $rowguiaexp) {
                        $ma              = $rowguiaexp->ma;
                        $cantidad        = 1;
                        $fecha           = $rowguiaexp->fecha;
                        $guia_no         = $rowguiaexp->guia_no;
                        $pais            = $rowguiaexp->destino;
                        $peso            = $rowguiaexp->peso;
                        $precio_neto     = $rowguiaexp->cargo_basico;
                        $descuento       =   0;

                        //   segmento para  faccturar paquetes  MA
                        if ($ma == 1) {
                            if ($id_tipo_comprobante   ==  2) {
                                $kdx_iva_gravado1 =  (float)($rowguiaexp->cargo_basico +  $rowguiaexp->iva);
                                // echo $kdx_iva_gravado1. '<br>';

                                $guia_no           = $rowguiaexp->guia_no;
                                $resultado         = $MP->DTES_Model->tracking_usps($guia_no);
                                # para  tratar la gongitud del  telefono
                                if (strlen($resultado->consignee_phone) <> 8) {
                                    $telefono_contacto = null;
                                } else {
                                    $telefono_contacto = $resultado->consignee_phone;
                                }
                                //$telefono_contacto = $resultado->consignee_phone;

                                // $correos_cc        = 'desarrollosv@c807.com';
                                //var_dump($resultado);
                                //echo $resultado->consignee;
                                //exit;

                                $receptor = array(
                                    'nombre'                =>  $resultado->consignee,
                                    'correo'                =>   $resultado->consignee_email, //'majrpor010@gmail.com',//
                                    'correos_cc'            =>   $correos_cc,
                                    'telefono'              =>  str_replace("+503/", "", $telefono_contacto)
                                );

                                array_push(
                                    $detalle3,
                                    array(
                                        'tipo'              =>      2,
                                        'cantidad'          =>      intval($cantidad),
                                        'unidad_medida'     =>      59,
                                        'tipo_venta'        =>      "1",
                                        'descripcion'       =>      $guia_no . ' MANEJO',
                                        'precio_unitario'   =>      (float)number_format($kdx_iva_gravado1, 6),

                                        'descuento'         =>      $descuento

                                    )
                                );
                                // echo $kdx_iva_gravado1. '<br>'. ' Tolo';
                            }
                        }

                        // var_dump($detalle3);


                        /*if($ma == 1)
                        {
                            $guia_no         = $rowguiaexp->guia_no;
                            $resultado      =  $MP->DTES_Model->tracking_usps($guia_no);
                           // echo $resultado->consignee;
                            //exit;

                            $receptor = array(
                                                'tipo_documento'        =>   null,
                                                'numero_documento'      =>   null,
                                                'nombre'                =>   $resultado->consignee,
                                                'correo'                =>   $resultado->consignee_email,
                                                'correos_cc'            =>   $correos_cc
                                                    //'telefono'               =>   $telefono_contacto
                            );

                        }*/
                        //$sumaprecio_neto += $rowguiaexp->cargo_basico;
                        // $kdx_gravado    += $rowguiaexp->cargo_basico;


                        if ($id_tipo_contribuyente == 1) {
                            $id_hacienda     = '1';
                        } elseif ($id_tipo_contribuyente == 2) {
                            $id_hacienda     = '3';
                        } elseif ($id_tipo_contribuyente == 3) {
                            $id_hacienda     = '2';
                        }


                        $descuento       = 0;

                        if ($id_tipo_contribuyente ==  1) {
                            $kdx_iva_gravado1 = (float)$rowguiaexp->cargo_basico * 0.13;
                        }


                        // $kdx_iva_gravado1 = (float)$rowguiaexp->cargo_basico * 0.13;
                        // $sumaiva          += (float)$rowguiaexp->cargo_basico * 0.13;
                        //$kdx_iva_gravado1 = round($kdx_iva_gravado1, 6);

                        //$acumulado        +=  (float)$precio_neto + $kdx_iva_gravado1;
                        //$sumaventapropia  +=  (float)$precio_neto + $kdx_iva_gravado1;
                        // $sumaventapropianeta  +=0;
                        //  $kdx_iva_gravado +=  ($precio_neto * 0.13 );
                        if ($id_tipo_comprobante == 4) {
                            array_push(
                                $detalle3,
                                array(
                                    'cantidad'          =>      intval($cantidad),
                                    'unidad_medida'     =>      59,
                                    'descripcion'       =>      ' ' . $guia_no . ' ' . $pais . ' ' . $peso . ' kg',
                                    'precio_unitario'   =>      (float)$precio_neto,
                                    'tipo_venta'        =>      2,
                                    'codigo'            =>      "0",
                                    'descuento'         =>      $descuento
                                )
                            );
                            //$NOkdx_gravado   +=  (float)$precio_neto;
                        } elseif ($id_tipo_comprobante == 2) {
                            //echo  'procesando  de nueco ' .  '<br>';
                            $precio_unitario = 0;

                            if ($id_tipo_contribuyente == 1) {
                                $id_hacienda     = '1';
                                $precio_unitario = $precio_neto + $kdx_iva_gravado1;
                            } elseif ($id_tipo_contribuyente == 2) {
                                $id_hacienda     = '3';
                                $precio_unitario = $precio_neto;
                            } elseif ($id_tipo_contribuyente == 3) {
                                $id_hacienda     = '2';
                                $precio_unitario = $precio_neto;
                            }
                            if ($ma == 0) {
                                if ($id_hacienda == '1') {
                                    array_push(
                                        $detalle3,
                                        array(
                                            'tipo'              =>  2,
                                            'cantidad'          =>  intval($cantidad),
                                            'unidad_medida'     =>  59,
                                            'descripcion'       =>  ' ' . $guia_no . ' ' . $pais . ' ' . $peso . ' kg',
                                            'precio_unitario'   =>  (float)(round(($precio_unitario), 2)),
                                            'tipo_venta'        =>   $id_hacienda,
                                            'descuento'         =>  (float)(number_format($descuento, 2)),
                                            'codigo'            =>  '0'
                                        )
                                    );
                                } elseif ($id_hacienda == '2') {
                                    array_push(
                                        $detalle3,
                                        array(
                                            'tipo'              =>  2,
                                            'cantidad'          =>  intval($cantidad),
                                            'unidad_medida'     =>  59,
                                            'descripcion'       =>  ' ' . $guia_no . ' ' . $pais . ' ' . $peso . ' kg',
                                            'precio_unitario'   =>  (float)(number_format(($precio_unitario), 2)),
                                            'tipo_venta'        =>   $id_hacienda,
                                            'descuento'         =>  (float)(number_format($descuento, 2)),
                                            'codigo'            =>  '0'
                                        )
                                    );
                                } elseif ($id_hacienda == '3') {
                                    array_push(
                                        $detalle3,
                                        array(
                                            'tipo'              =>  2,
                                            'cantidad'          =>  intval($cantidad),
                                            'unidad_medida'     =>  59,
                                            'descripcion'       =>  ' ' . $guia_no . ' ' . $pais . ' ' . $peso . ' kg',
                                            'precio_unitario'   =>  (float)(number_format(($precio_unitario), 2)),
                                            'tipo_venta'        =>   $id_hacienda,
                                            'descuento'         =>  (float)(number_format($descuento, 2)),
                                            'codigo'            =>  '0'
                                        )
                                    );
                                }
                            }
                            //$NOkdx_gravado   +=  (float)$precio_neto;
                        } else {


                            if ($id_hacienda == '1') {
                                array_push(
                                    $detalle3,
                                    array(
                                        'tipo'              =>  2,
                                        'cantidad'          =>  intval($cantidad),
                                        'unidad_medida'     =>  59,
                                        'descripcion'       =>  ' ' . $guia_no . ' ' . $pais . ' ' . $peso . ' kg',
                                        'precio_unitario'   =>   (float)$precio_neto,
                                        'tipo_venta'        =>  $id_hacienda,
                                        'descuento'         =>  $descuento,
                                        'tributos'          =>  array(
                                            0 => array(
                                                'codigo'    =>  "20",
                                                'monto'     => (float)number_format($kdx_iva_gravado1, 6) //0,//(float)$kdx_iva_gravado1
                                            )
                                        )
                                    )
                                );


                                //$iva  = $kdx_gravado * 0.13;
                                //$kdx_gravado     +=  $rowguiaexp->cargo_basico;
                            }
                        }
                    }
                    //  $acumulado        +=  (float)$sumaprecio_neto + round($sumaiva,2);
                    //  $sumaventapropia  +=  (float)$sumaprecio_neto + round($sumaiva, 2);
                    //echo  'EL  ACUMULADO DE LOS SERVICIOS ES ' . $acumulado .'DESPUES DE GUIAS EXP'.'<BR>';

                }
            }
        }

        // var_dump($detalle3);
        if ($id_tipoevento == 0) {
            $detalle = array_merge($detalle1, $detalle2);
        } else {
            $detalle = array_merge($detalle3, $detalle1, $detalle2);
            // $detalle = array_merge($detalle1, $detalle2);
        }
        /* var_dump($detalle);
         //  obtenemos la retension
         $iva            =  0;
         $iva_retenido   =  0;
         if($kdx_gravado>0) {
             $iva            = ($kdx_gravado + $kdx_gravadoTercero) * 0.13;

         }
         if($retener_iva==  true) {
             $iva_retenido   =  round((($kdx_gravado)* 0.01), 2);
         }
         //echo    'EL  IVA  RETENIDO ES' .  $iva_retenido ;
         // TOTAL  A CANCELAR
         $TOTAL_CANCELAR = 0;                                //$iva  = $kdx_gravado * 0.13;
         $TOTAL_CANCELAR  =  ((($kdx_gravado + $kdx_gravadoTercero)- $iva_retenido)+  $iva +  $NOkdx_gravado +    $totalCtaj);

         echo  'EL TOTAL A CANCELAR ES' .  $TOTAL_CANCELAR ;*/

        /*$venta_propia                =    (float)$dat_Cabz->venta_propia;
            $retencion_1_por_ciento      =   $dat_Cabz->retencion_1_por_ciento;
            $sub_total_cuenta_ajena      =    (float)$dat_Cabz->sub_total_cuenta_ajena;
            $venta_total                 =   (float)$dat_Cabz->venta_total;*/
        //echo $kdx_iva_gravado1. '<br>'. ' Tolo';
        $cctaj++;
        $cltotal++;
        $apendice2 = array(
            0 => array(
                'campo'         =>    'Linea ' . $cctaj,
                'etiqueta'      =>    'Total Cuenta Ajena',
                'valor'         =>    '$' . number_format($sub_total_cuenta_ajena, 2)
            )
        );
        $apendice3 = array(
            0   =>  array(
                'campo'         =>    'Linea ' . $cltotal,
                'etiqueta'      =>    'Total a Cancelar',
                'valor'         =>    '$' . number_format(($venta_total), 2), // $acumulado  $acumulado1
                "condicionOperacion" => $id_tipo_pago,
            )
        );
        $apendice = array_merge($apendice1, $apendice2, $apendice3);

        if ($wh_numerotodoIN != '') {
            //  echo 'se dibuja la Eztension' .  '<br>';
            $extension =
                array(
                    'observaciones' => $wh_numerotodoIN
                );
        }

        //  creamos el  arrar para las  formas de pago del cliente
        // echo  'el tipo de pago del   cliente es' . $id_tipo_pago .'nnn'.  round(($TOTAL_CANCELAR-$totalCtaj), 2);
        if ($id_tipo_pago == 1) {

            array_push(
                $footer,
                array(
                    'tipo'  => '01', //str_pad($id_tipo_pago, 2, "0", STR_PAD_LEFT),
                    'monto' =>  round(($venta_propia), 2)
                )
            );
        } else {
            array_push(
                $footer,
                array(
                    'tipo'          =>    '01', //  str_pad($id_tipo_pago, 2, "0", STR_PAD_LEFT),
                    'monto'         =>     round($venta_propia, 2),
                    'plazo'         =>     '01',
                    'periodo'       =>     intval($dias_credito)
                )
            );
        }
        // echo  'lo que va  a  REFLEJAR EN PAGOS ES'.'<BR>';

        //var_dump($footer);
        // echo  '' .$sumaventapropia
        /* definiendo TIPO DE  DTE  */
        if ($id_tipo_comprobante == 1) {
            $tipo_dte = '03';
        } elseif ($id_tipo_comprobante == 2 || $id_tipo_comprobante == 7) {
            $tipo_dte = '01';
        } elseif ($id_tipo_comprobante == 4) {
            $tipo_dte = '11';
        } elseif ($id_tipo_comprobante == 3) {
            $tipo_dte = '05';
        }

        /**########  los  stablecimiento se  manejan de la  siguiente manera
         *
         'establecimiento'   =>   '0001'(SAN FRANCISCO),
         'establecimiento'   =>   '0002'(SAN FRANCISCO),
         PARA  ES RESTO DE  SUCURSALES  REMPLAZAR SU ID  POR  '0001'
         */
        $establecimiento = 'M001';
        $punto_venta = 'P001';

        if ($id_tipo_comprobante == 1 || $id_tipo_comprobante == 2 || $id_tipo_comprobante == 7) {
            $enca = array(
                'documento'  =>  array(
                    'tipo_dte'          =>   $tipo_dte,
                    'establecimiento'   =>   $establecimiento,
                    'punto_venta'       =>   $punto_venta,
                    'retener_iva'       =>   $retener_iva,
                    'condicion_pago'    =>   $id_tipo_pago,
                    'receptor'          =>   $receptor,
                    'venta_tercero'     =>   $venta_tercero,
                    'items'             =>   $detalle,
                    'pagos'             =>   $footer,
                    'extension'         =>   $extension,
                    'apendice'          =>   $apendice

                )
            );
        } elseif ($id_tipo_comprobante == 4) {
            $enca = array(
                'documento' => array(
                    'tipo_dte'                  =>   $tipo_dte,
                    'establecimiento'   =>   $establecimiento,
                    'punto_venta'       =>   $punto_venta,
                    'retener_iva'               =>   $retener_iva,
                    'condicion_pago'            =>   intval($id_tipo_pago),
                    'tipo_item_exportacion'     =>   2,
                    'receptor'                  =>   $receptor,
                    'items'                     =>   $detalle,
                    'pagos'                     =>   $footer,
                    'extension'                 =>   $extension,
                    'apendice'                  =>   $apendice
                )
            );
        } elseif ($id_tipo_comprobante == 3) {
            $enca = array(
                'documento'  =>  array(
                    'tipo_dte'                  =>      $tipo_dte,
                    'establecimiento'   =>   $establecimiento,
                    'punto_venta'       =>   $punto_venta,
                    'retener_iva'               =>      $retener_iva,
                    'condicion_pago'            =>      $id_tipo_pago,
                    'documentos_relacionados'   =>      $documento_rel,
                    'receptor'                  =>      $receptor,
                    'items'                     =>      $detalle,
                    'pagos'                     =>      $footer,
                    'apendice'                  =>      $apendice
                )
            );
        }
        $datas = json_encode($enca);
        echo ($datas);
        //  exit;
        if ($_SESSION["empresa_id"]  == 46) {

            $url =   'https://certificador.infile.com.sv/api/v1/certificacion/prod/documento/certificar?usuario=06140402001010&llave=d067b0b30f376f6c49eb1ce56b94c5f4';
            $HTTPHEADER = array(
                'usuario: 06140402001010',
                'llave: d067b0b30f376f6c49eb1ce56b94c5f4',
                'Content-Type: application/json'
            );

            /* $url =   'https://certificador.infile.com.sv/api/v1/certificacion/test/documento/certificar?usuario=06140402001010&llave=ac6f090581c2e20421c4e8053d23ea4f';
            $HTTPHEADER = array('usuario: 06140402001010',
                                'llave: ac6f090581c2e20421c4e8053d23ea4f',
                               'Content-Type: application/json'
                              );*/
        } elseif ($_SESSION["empresa_id"] == 47) {

            //$url  ='https://certificador.infile.com.sv/api/v1/certificacion/test/documento/certificar?usuario=06140402001036&llave=9bcb7126d79ca4df79212d822606ebc9';
            //'llave: 9bcb7126d79ca4df79212d822606ebc9',
            $url  = 'https://certificador.infile.com.sv/api/v1/certificacion/prod/documento/certificar?usuario=06140402001036&llave=f216f8d5278a1ca27e38c033cb96c640';
            $HTTPHEADER = array(
                'usuario: 06140402001036',
                'llave: f216f8d5278a1ca27e38c033cb96c640',
                'Content-Type: application/json'
            );



            /*$url  = 'https://certificador.infile.com.sv/api/v1/certificacion/test/documento/certificar?usuario=06140402001036&llave=9bcb7126d79ca4df79212d822606ebc9';
            $HTTPHEADER = array('usuario: 06140402001036',
                                 'llave: 9bcb7126d79ca4df79212d822606ebc9',
                                 'Content-Type: application/json'
                               );*/
        }

        $curl = curl_init();
        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL             =>    $url, //'https://certificador.infile.com.sv/api/v1/certificacion/test/documento/certificar?usuario=06140402001010&llave=ac6f090581c2e20421c4e8053d23ea4f',
                CURLOPT_RETURNTRANSFER  =>    true,
                CURLOPT_ENCODING        =>    '',
                CURLOPT_MAXREDIRS       =>    10,
                CURLOPT_TIMEOUT         =>    0,
                CURLOPT_FOLLOWLOCATION  =>    true,
                CURLOPT_HTTP_VERSION    =>    CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST   =>    'POST',
                CURLOPT_POSTFIELDS      =>    $datas,
                CURLOPT_HTTPHEADER      =>    $HTTPHEADER,
            )
        );

        $respuesta =  '';
        $pdf       =  '';
        $data      = null;
        $pdf_path  = '';
        $respuesta_dgi = '';

        $cod_generacion  =  '';
        $numero_control  = '';
        $ambiente = '';
        $selloRecibido      =   '';

        $response = curl_exec($curl);

        $respuesta = json_decode($response, true);
        $pdf       = json_decode($response, true);
        $respuesta_dgi = json_decode($response, true);

        curl_close($curl);
        echo  'generandon la  salida del  DTE' . '<BR>';
        echo $response;

        echo 'Fin de la  salida de la DE LA  SALIDA DEL  DTE' . '<BR>';
        if (isset($respuesta['respuesta'])) {
            $cod_generacion  =  $respuesta['respuesta']['codigoGeneracion'];
            $numero_control  = $respuesta['respuesta']['numeroControl'];
        }
        if (isset($pdf['pdf_path'])) {
            $pdf_path        =  $pdf['pdf_path'];
        }
        if (isset($respuesta_dgi['respuesta_dgi'])) {
            $ambiente        =  $respuesta_dgi['respuesta_dgi']['ambiente'];
            $selloRecibido   =  $respuesta_dgi['respuesta_dgi']['selloRecibido'];
        }

        if ($pdf_path != '' && $cod_generacion != '' &&  $numero_control  != '' && $ambiente != '') {
            $data = array(
                'cod_generacion'    =>  $cod_generacion,
                'numero_control'    =>  $numero_control,
                'pdf_path'          =>  $pdf_path,
                'dte_prod'          =>  $ambiente,
                'selloRecibido'     =>  $selloRecibido,
                'create_api'        => 'S'
            );
        }
        if ($data !=  null) {
            $MP->DTES_Model->guardar_DTE($data, $idVenta);
        }


        return $response;
    }

    // funcion para   notificar por  correo electronico cuando  algo  salga mal
    public function sendmails($msg, $casillero, $proceso, $tcomp, $guia_no)
    {

        error_reporting(E_ALL);
        ini_set('display_errors', '1');
        // echo   'CARGAR FUNCION  PARA  ENVIAR CORREO ';
        //include getcwd()."/application/libraries/fpdf/fpdf.php";
        //echo getcwd();
        include getcwd() . "/system/libraries/Email.php";

        $email = new CI_Email();
        //$this->load->library('/system/libraries/email');



        //C:\xampp\htdocs\grupo_erp\ventas\system\libraries

        //$this->load->library('email');
        //$this->load->library('\system\libraries\email');

        //$this->load->library('email');
        //echo   'CARGAR FUNCION  PARA  ENVIAR CORREO ';

        /* $config = array(
         'protocol'  => 'smtp',
         'smtp_host' => 'ssl://smtp.gmail.com',
         'smtp_port' => 465,
         'smtp_user' => 'desarrollosv2@c807.com',
         'smtp_pass' => '0skAr$_2023',
         'mailtype' => 'html',
         'charset' => 'utf-8',
         );*/



        $config = array(
            'protocol'  => 'smtp',
            'smtp_host' => 'localhost',
            'smtp_port' => 25,
            'smtp_user' => 'adminserver@stargates.site',
            'smtp_pass' => 'adminserver2022',
            'charset'   => 'utf-8',
            'mailtype' => 'html',
        );


        /*$config = array(
                          'protocol'  => 'smtp',
                          'smtp_host' => 'ssl://smtp.gmail.com',
                          'smtp_port' => 25,
                         // 'smtp_user' => 'elmer.guardado@gmail.com',
                        //  'smtp_pass' => 'Rulo2020',
                          'smtp_user' => 'oscarprogram@gmail.com',
                          'smtp_pass' => 'samaritano#',
                          'charset'   => 'utf-8',
                          );*/



        //$msg = '';
        //$status = 'Error al generar el DTE para el Casillero: ' . $casillero;;
        if ($proceso == 2) {
            $status = 'Error al invalidar DTE para el Casillero: ' . $casillero . ' CompTip: ' . $tcomp . ' - ' . $_SESSION["empresa_id"] . ' - ' . $_SESSION["nombre_sucursal"];
        } else {
            if ($guia_no != '') {
                $status = 'Error al generar el DTE para el Casillero: ' . $casillero . ' CompTip: ' . $tcomp . ' - ' . $_SESSION["empresa_id"] . ' - ' . $_SESSION["nombre_sucursal"] . 'GUIA ' . $guia_no;
            } else {
                $status = 'Error al generar el DTE para el Casillero: ' . $casillero . ' CompTip: ' . $tcomp . ' - ' . $_SESSION["empresa_id"] . ' - ' . $_SESSION["nombre_sucursal"];
            }
            //  $status = 'Error al generar el DTE para el Casillero: ' . $casillero .' CompTip: ' . $tcomp . ' - ' . $_SESSION["empresa_id"] . ' - ' . $_SESSION["nombre_sucursal"];

        }
        $CCemal = '';
        //$msg ='';
        //$status = 'Verificar  datos del cliente'.$data->num_reclamo.' el tipo  de  docuento no se consigue validar'; desarrollosv2@c807.com
        $correo = 'desarrollosv2@c807.com';


        //$CCemal = 'alfredo.bran@c807.com, desarrollosv2@c807.com, desarrollosv@c807.com, desarrollosv3@mistarship.com';

        if ($guia_no != '') {
            $CCemal = 'alfredo.bran@c807.com, desarrollosv2@c807.com, desarrollosv@c807.com, desarrollosv3@mistarship.com,servicioalcliente7@mistarship.com';
        } else {
            $CCemal = 'alfredo.bran@c807.com, desarrollosv2@c807.com, desarrollosv@c807.com, desarrollosv3@mistarship.com';
        }



        /* $this->email->initialize($config);
         $this->email->set_newline("\r\n");
         $this->email->from('desarrollosv2@c807.com', 'Factura electronica ');
         $this->email->to($correo);
        // $this->email->cc('desarrollosv2@c807.com,desarrollosv@c807.com');
         $this->email->cc($CCemal);
         $this->email->subject($status);
         $this->email->message($msg);*/


        $email->initialize($config);
        $email->set_newline("\r\n");
        $email->from('desarrollosv3@mistarship.com', 'Factura electronica ');
        $email->to($correo);
        // $this->email->cc('desarrollosv2@c807.com,desarrollosv@c807.com');
        $email->cc($CCemal);
        $email->subject($status);
        $email->message($msg);


        /*if ($this->email->send()) {
                    echo 'Your email was sent.';
                } else {
                    show_error($this->email->print_debugger());
                }
                $this->email->clear(true);*/


        if ($email->send()) {
            // echo 'Your email was sent.';
        } else {
            show_error($email->print_debugger());
        }
        $email->clear(true);
        $email  = '';
    }
}
