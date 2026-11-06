<?php

class Generarpartidas
{
    // Funcion Para Generar Las Partidas de Ventas
    public function generarpartida_venta($idTipoComprobante, $idEncaComprobante, $codigopartida, $fecha_comprobante)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        $_SESSION["empresa_id"] = 47;
                                                   
         $_SESSION["user_id"]   = 5693;

        $CI = &get_instance();
        $CI->load->model('Partidas_Model');

        $id_banco          =  null;
        $id_proveedor      =  null;
        $id_vendedor       =  null;
        $codigo_partida    =  $codigopartida;
        $fecha             =  $fecha_comprobante;
        $idventa           =  $idEncaComprobante;
        $tipogenerar       =  1;      // 1- Partida de venta por comprobante,  2 partida de venta consolidada
        $iddet_partida     =  null;   // Representa el id del detalle de la partida para cada item
        $idpartida         =  null;   // Representa el id de la partida que se generara
        $ultima_linea      =  null;
        $valor_cheque      =  0;      // Se utiliza cuando se trate de un abono por cuenta por cobrar o pagar y el pago se haga en  cheque
        $nombre_cheque     =  null;
        $numero_cxp        =  null;   // Se utilizara cuando la partida se genere por cuenta por pagar
        $fecha_cxp         =  null;   // Se utilizara cuando la partida se genere por cuenta por pagar
        $prov_cxp          =  null;   // Se utilizara cuando la partida se genere por cuenta por pagar
        $datosCabecera     =  $CI->Partidas_Model->obtenerdatos_cabecera_partida($codigo_partida);
        $idpartida         =  $this->crear_partida($datosCabecera);
        ECHO 'el valor retornado de la insercion es'. $idpartida;

        if($idpartida != 0) {
            $arrDatosCabecera   =   array(
                                            'idtipopartida'    =>   $datosCabecera[0]->id,
                                            'nombre_partida'   =>   $datosCabecera[0]->nombre_partida,
                                            'correlativopart'  =>   $datosCabecera[0]->correlativo,
                                            'idaniopartida'    =>   $datosCabecera[0]->id_anio_partida,
                                            'idmespartida'     =>   $datosCabecera[0]->id_mes_partida,
                                    );

            // Creando linea de clientes
            $detalleClientespartidas  = $CI->Partidas_Model->obtenerdatos_detalleclientes_partida($tipogenerar, $idventa, $fecha);

            if (!empty($detalleClientespartidas)) {
                foreach ($detalleClientespartidas as  $row) {
                    $tipo_comprobante   =  null;
                    $numero_partida     =  $datosCabecera[0]->correlativo;
                    $fecha_ingreso      =  $fecha;
                    $linea              =  1;
                    $cuenta_mayor       =  substr(strval($row->cuenta_contable), 0, 4);
                    $cuenta             =  $row->cuenta_contable;
                    $concepto           =  $row->nombre_cliente;

                    if($codigo_partida == '006' || $codigo_partida == '009' || $codigo_partida == '010') { // Nota de Credito
                        $valor_cargo      =  0;
                        $valor_abono      =  $row->debe;
                    } else {
                        // Partida de Venta
                        $valor_cargo      =  $row->debe;
                        $valor_abono      =  0;
                    }

                    // Segmento Para Cambiar el Orden de Impresion
                    $orden_impresion    =   1;
                    $numero_cxp         =   $numero_cxp;
                    $fecha_cxp          =   $fecha_cxp ;
                    $prov_cxp           =   $prov_cxp;

                    $arrDetallePartida  =   array(
                                                    'id_encabezado'        =>   $idpartida,
                                                    'id_tipo_comprobante'  =>   $tipo_comprobante,
                                                    'numero_partida'       =>   $numero_partida,
                                                    'fecha_ingreso'        =>   $fecha_ingreso,
                                                    'linea'                =>   $linea,
                                                    'cuenta_mayor'         =>   $cuenta_mayor,
                                                    'cuenta_contable'      =>   $cuenta,
                                                    'concepto'             =>   $concepto,
                                                    'valor_cargo'          =>   $valor_cargo,
                                                    'valor_abono'          =>   $valor_abono,
                                                    'orden_impresion'      =>   $orden_impresion,
                                                    'numero_cxp'           =>   $numero_cxp,
                                                    'fecha_cxp'            =>   $fecha_cxp,
                                                    'prov_cxp'             =>   $prov_cxp,
                                            );

                    $this->generar_detalle_partida($arrDetallePartida, $iddet_partida);
                }
            }

            // Creando IVA Ventas
            $detalleivapartidas = $CI->Partidas_Model->obtenerdatos_ivanventas_partidaind($idventa, $idTipoComprobante);

            if(!empty($detalleivapartidas)) {
                foreach($detalleivapartidas as $row) {
                    $valor_cargo        =     0;
                    $valor_abono        =     0;
                    $tipo_comprobante   =     null;
                    $numero_partida     =     $datosCabecera[0]->correlativo;
                    $fecha_ingreso      =     $fecha; // fecha del comprobante
                    $linea              =     1;
                    $cuenta_mayor       =     '2107';

                    if($idTipoComprobante == 2 || $idTipoComprobante == 7) {
                        $cuenta         =     210701;
                        $concepto       =     'IVA VENTAS CONSUMIDORES FINALES';
                    } elseif($idTipoComprobante == 1 && $row->total_iva > 0) {
                        $cuenta         =     210702;
                        $concepto       =     'IVA VENTAS CONTRIBUYENTES';
                    } elseif($idTipoComprobante == 3 && $row->total_iva > 0) {
                        $cuenta         =     210702;
                        $concepto       =     'IVA DEBITO FISCAL';
                    } elseif ($idTipoComprobante == 5 && $row->total_iva > 0) {
                        $cuenta         =     210701;
                        $concepto       =     'IVA DEBITO FISCAL';
                    }
                    if($idTipoComprobante == 3 || $idTipoComprobante == 5) {
                        $valor_cargo    =     $row->total_iva;
                        $valor_abono    =     0;//$row->total_iva;
                    } else {
                        $valor_cargo    =     0;
                        $valor_abono    =     $row->total_iva;
                    }

                    // $valor_cargo      =  0;
                    // $valor_abono      =  $row->total_iva;

                    if($codigo_partida == '006' || $codigo_partida == '009' || $codigo_partida == '010') {
                        $orden_impresion  =  2;
                    } else {
                        $orden_impresion  =  1;
                    }

                    $arrDetallePartida  =   array(
                                                    'id_encabezado'             =>          $idpartida,
                                                    'id_tipo_comprobante'       =>          $tipo_comprobante,
                                                    'numero_partida'            =>          $numero_partida,
                                                    'fecha_ingreso'             =>          $fecha_ingreso,
                                                    'linea'                     =>          $linea,
                                                    'cuenta_mayor'              =>          $cuenta_mayor,
                                                    'cuenta_contable'           =>          $cuenta,
                                                    'concepto'                  =>          $concepto,
                                                    'valor_cargo'               =>          $valor_cargo,
                                                    'valor_abono'               =>          $valor_abono,
                                                    'orden_impresion'           =>          $orden_impresion,
                                                    'numero_cxp'                =>          $numero_cxp,
                                                    'fecha_cxp'                 =>          $fecha_cxp,
                                                    'prov_cxp'                  =>          $prov_cxp,
                                            );

                    $this->generar_detalle_partida($arrDetallePartida, $iddet_partida);
                }
            }

            // Para Rebajas y Devoluciones
            $detallerebpartidas  =  $CI->Partidas_Model->obtenerdatos_servicios_partida($fecha, $idventa);

            if(!empty($detallerebpartidas)) {
                foreach($detallerebpartidas as $row) {
                    $id                   =     null;
                    $tipo_comprobante     =     null;
                    $numero_partida       =     $datosCabecera[0]->correlativo;
                    $fecha_ingreso        =     $fecha; // date('Y-m-d', strtotime($row->fecha_comprobante));
                    $linea                =     1;
                    $cuenta_mayor         =     '5102';
                    $total_gravado        =     $row->kdx_gravado;
                    $total_no_sujeto      =     $row->kdx_no_sujeto;
                    $total_exento         =     $row->kdx_exento;
                    $total_exportacion    =     $row->kdx_exportacion;

                    if($total_gravado > 0) {
                        $cuenta           =     $row->cuenta_contable;
                        $concepto         =     $row->descripcion;
                        $valor_cargo      =     $total_gravado;
                        $valor_abono      =     0;
                    }

                    if($total_no_sujeto > 0) {
                        $cuenta           =     $row->cuenta_contable;
                        $concepto         =     $row->descripcion;
                        $valor_cargo      =     $total_no_sujeto;
                        $valor_abono      =     0;
                    }

                    if($total_exento > 0) {
                        $cuenta           =     $row->cuenta_contable;
                        $concepto         =     $row->descripcion;
                        $valor_cargo      =     $total_exento;
                        $valor_abono      =     0;
                    } elseif($total_exportacion > 0) {
                        $cuenta           =     $row->cuenta_contable;
                        $concepto         =     $row->descripcion;
                        $valor_cargo      =     $total_exportacion;
                        $valor_abono      =     0;
                    }

                    $orden_impresion      =     2;
                    $numero_cxp           =     null;
                    $fecha_cxp            =     null;
                    $prov_cxp             =     null;
                    $data                 =     array(
                                                        'id_encabezado'             =>          $idpartida,
                                                        'id_tipo_comprobante'       =>          $tipo_comprobante,
                                                        'numero_partida'            =>          $numero_partida,
                                                        'fecha_ingreso'             =>          $fecha_ingreso,
                                                        'linea'                     =>          $linea,
                                                        'cuenta_mayor'              =>          $cuenta_mayor,
                                                        'cuenta_contable'           =>          $cuenta,
                                                        'concepto'                  =>          $concepto,
                                                        'valor_cargo'               =>          round($valor_cargo, 2),
                                                        'valor_abono'               =>          round($valor_abono, 2),
                                                        'orden_impresion'           =>          $orden_impresion,
                                                        'numero_cxp'                =>          $numero_cxp,
                                                        'fecha_cxp'                 =>          $fecha_cxp,
                                                        'prov_cxp'                  =>          $prov_cxp,
                                                );

                    $CI->Partidas_Model->guardar_detalle_partida($data, $id);
                }
            }

            // Para Grandes Contribuyentes
            $detalleretpartidas  =  $CI->Partidas_Model->obtenerdatos_retencion_partida($fecha, $idventa, $idTipoComprobante);

            if(!empty($detalleretpartidas)) {
                foreach($detalleretpartidas as $row) {
                    $id                   =   null;
                    $tipo_comprobante     =   null;
                    $numero_partida       =   $datosCabecera[0]->correlativo;
                    $fecha_ingreso        =   $fecha ;//date('Y-m-d', strtotime($row->fecha_comprobante));
                    $linea                =   1;
                    $cuenta_mayor         =   '2107';

                    if($row->retencion_1_por_ciento > 0 && $idTipoComprobante == 3 || $idTipoComprobante == 5 || $idTipoComprobante == 6) {
                        $cuenta           =    210703;
                        $concepto         =    'IVA RETENIDO';
                        $valor_cargo      =    0;
                        $valor_abono      =    $row->retencion_1_por_ciento;
                    } else {
                        $cuenta           =    210703;
                        $concepto         =    'IVA RETENIDO';
                        $valor_cargo      =    $row->retencion_1_por_ciento;
                        $valor_abono      =    0;
                    }

                    // $concepto        =  $row->descripcion;

                    $orden_impresion      =     2;
                    $numero_cxp           =     null;
                    $fecha_cxp            =     null;
                    $prov_cxp             =     null;
                    $data                 =     array(
                                                        'id_encabezado'         =>      $idpartida,
                                                        'id_tipo_comprobante'   =>      $tipo_comprobante,
                                                        'numero_partida'        =>      $numero_partida,
                                                        'fecha_ingreso'         =>      $fecha_ingreso,
                                                        'linea'                 =>      $linea,
                                                        'cuenta_mayor'          =>      $cuenta_mayor,
                                                        'cuenta_contable'       =>      $cuenta,
                                                        'concepto'              =>      $concepto,
                                                        'valor_cargo'           =>      round($valor_cargo, 2),
                                                        'valor_abono'           =>      round($valor_abono, 2),
                                                        'orden_impresion'       =>      $orden_impresion,
                                                        'numero_cxp'            =>      $numero_cxp,
                                                        'fecha_cxp'             =>      $fecha_cxp,
                                                        'prov_cxp'              =>      $prov_cxp,
                                                );

                    $CI->Partidas_Model->guardar_detalle_partida($data, $id);
                }
            }

            // Creamos el Detalle de Los Servicios de la Factura

            // Obteniendo el Detalle de Servicios
            $det_servicios_partida = $CI->Partidas_Model->obtenerdatos_detalleservicios_partida($tipogenerar, $idventa, $fecha);

            if(!empty($det_servicios_partida)) {
                foreach ($det_servicios_partida as $row) {
                    // $tipo_comprobante =  null;
                    $tipo_comprobante  =  $row->id_tipo_comprobante;
                    $numero_partida    =  $datosCabecera[0]->correlativo;
                    $cuenta_mayor      =  substr(strval($row->cuenta_contable), 0, 4);
                    $cuenta            =  $row->cuenta_contable;
                    $fecha             =  $fecha;// strtotime('Y-m-d',$row->fecha_comprobante);
                    $descripcion       =  str_replace('STAR SHIP', '', $row->descripcion);

                    // Segmento que determina si se trata de una nota de credito
                    if($codigo_partida == '006' || $codigo_partida == '009' || $codigo_partida == '010') {
                        $venta_total      = 0;
                        $valor_cargo      = $row->haber;
                    } else {
                        // Se mantiene el segmento original
                        $venta_total      =  $row->haber;
                        $valor_cargo      =  0;
                    }

                    $linea            =  1;
                    // $venta_total      =  $row->haber;
                    // $valor_cargo      =  0;

                    // Cambiar Orden de Impresion de Acuerdo al Tipo de Partida Que Se Esta Generando
                    $orden_impresion  =  2;
                    $numero_cxp       =  $numero_cxp;
                    $fecha_cxp        =  $fecha_cxp;
                    $prov_cxp         =  $prov_cxp;

                    $arrDetallePartida  =   array(
                                                    'id_encabezado'             =>          $idpartida,
                                                    'id_tipo_comprobante'       =>          $tipo_comprobante,
                                                    'numero_partida'            =>          $numero_partida,
                                                    'fecha_ingreso'             =>          $fecha,
                                                    'linea'                     =>          $linea,
                                                    'cuenta_mayor'              =>          $cuenta_mayor,
                                                    'cuenta_contable'           =>          $cuenta,
                                                    'concepto'                  =>          $descripcion,
                                                    'valor_cargo'               =>          $valor_cargo,
                                                    'valor_abono'               =>          $venta_total,
                                                    'orden_impresion'           =>          $orden_impresion,
                                                    'numero_cxp'                =>          $numero_cxp,
                                                    'fecha_cxp'                 =>          $fecha_cxp,
                                                    'prov_cxp'                  =>          $prov_cxp,
                                            );
                    $this->generar_detalle_partida($arrDetallePartida, $iddet_partida);
                }
            }

            // Generando la Sumatoria de la Partida
            $sumdetpartidas = $CI->Partidas_Model->sumar_detalle_partida($idpartida);
            if (!empty($sumdetpartidas)) {
                $arrEncabezadoPartida   =   array(
                                                    'idventa'                   =>          $idventa,
                                                    'id_empresa'                =>          $_SESSION["empresa_id"],
                                                    'id_banco'                  =>          $id_banco,
                                                    'id_usuario'                =>          $_SESSION["user_id"],
                                                    'id_proveedor'              =>          $id_proveedor,
                                                    'id_tipo_partida'           =>          $datosCabecera[0]->id,
                                                    'id_vendedor'               =>          $id_vendedor,
                                                    'tipo_partida'              =>          substr($datosCabecera[0]->nombre_partida, 0, 2),
                                                    'numero_partida'            =>          $datosCabecera[0]->correlativo,
                                                    'fecha_ingreso'             =>          $fecha,
                                                    'concepto'                  =>          $datosCabecera[0]->nombre_partida,
                                                    'total_cargos'              =>          $sumdetpartidas[0]->cargo,
                                                    'total_abonos'              =>          $sumdetpartidas[0]->abono,
                                                    'ultima_linea'              =>          $ultima_linea,
                                                    'valor_cheque'              =>          $valor_cheque,
                                                    'nombre_cheque'             =>          $nombre_cheque
                                            );

                $this->generar_cabecera_partida($arrEncabezadoPartida, $idpartida);
                $this->actualizar_correlativo_partida($arrDatosCabecera);
            }
        } else {
            echo 'Surgio Un Error Durante la Generacion de Partida';
        }
    }

    public function crear_partida($datosCabecera)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $CI =  &get_instance();
        $CI->load->model('Partidas_Model');

        $idpartida = null; //  Para Que Cree la Cabecera de la Partida

        if(!empty($datosCabecera)) {
            $data = array('id_empresa'  =>  47,);
            $idpartida = $CI->Partidas_Model->guardar_partida($data, $idpartida);
        }

        return $idpartida;
    }

    public function generar_detalle_partida($arrDetallePartida, $iddet_partida)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $CI =  &get_instance();
        $CI->load->model('Partidas_Model');
        $data   =   array(
                            'id_encabezado'        =>   $arrDetallePartida['id_encabezado'],
                            'id_tipo_comprobante'  =>   $arrDetallePartida['id_tipo_comprobante'],
                            'numero_partida'       =>   $arrDetallePartida['numero_partida'],
                            'fecha_ingreso'        =>   $arrDetallePartida['fecha_ingreso'],
                            'linea'                =>   $arrDetallePartida['linea'],
                            'cuenta_mayor'         =>   $arrDetallePartida['cuenta_mayor'],
                            'cuenta_contable'      =>   $arrDetallePartida['cuenta_contable'],
                            'concepto'             =>   $arrDetallePartida['concepto'],
                            'valor_cargo'          =>   $arrDetallePartida['valor_cargo'],
                            'valor_abono'          =>   $arrDetallePartida['valor_abono'],
                            'orden_impresion'      =>   $arrDetallePartida['orden_impresion'],
                            'numero_cxp'           =>   $arrDetallePartida['numero_cxp'],
                            'fecha_cxp'            =>   $arrDetallePartida['fecha_cxp'],
                            'prov_cxp'             =>   $arrDetallePartida['prov_cxp'],
                    );
        // echo 'Insertando Detalle de Servicios de Partida';
        $CI->Partidas_Model->guardar_detalle_partida($data, $iddet_partida);
    }

    public function generar_cabecera_partida($arrEncabezadoPartida, $idpartida)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $CI =  &get_instance();
        $CI->load->model('Partidas_Model');

        $data   =   array(
                            'idventa'          =>   $arrEncabezadoPartida['idventa'],
                            'id_empresa'       =>   $_SESSION["empresa_id"],
                            'id_banco'         =>   $arrEncabezadoPartida['id_banco'],
                            'id_usuario'       =>   $_SESSION["user_id"],
                            'id_proveedor'     =>   $arrEncabezadoPartida['id_proveedor'],
                            'id_tipo_partida'  =>   $arrEncabezadoPartida['id_tipo_partida'],
                            'id_vendedor'      =>   $arrEncabezadoPartida['id_vendedor'],
                            'tipo_partida'     =>   substr($arrEncabezadoPartida['tipo_partida'], 0, 2),
                            'numero_partida'   =>   $arrEncabezadoPartida['numero_partida'],
                            'fecha_ingreso'    =>   $arrEncabezadoPartida['fecha_ingreso'],
                            'concepto'         =>   $arrEncabezadoPartida['concepto'],
                            'total_cargos'     =>   number_format($arrEncabezadoPartida['total_cargos'], 2),
                            'total_abonos'     =>   number_format($arrEncabezadoPartida['total_abonos'], 2),
                            'ultima_linea'     =>   $arrEncabezadoPartida['ultima_linea'],
                            'valor_cheque'     =>   number_format($arrEncabezadoPartida['valor_cheque'], 2),
                            'nombre_cheque'    =>   $arrEncabezadoPartida['nombre_cheque'],
                    );

        $CI->Partidas_Model->guardar_partida($data, $idpartida);
    }

    public function actualizar_correlativo_partida($arrDatosCabecera)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $CI =  &get_instance();
        $CI->load->model('Partidas_Model');
        $id_tipo_partida  = $arrDatosCabecera['idtipopartida'];
        $nuevocorrelativo = $arrDatosCabecera['correlativopart'] + 1;
        $idaniopartida    = $arrDatosCabecera['idaniopartida'];
        $idmespartida     = $arrDatosCabecera['idmespartida'];

        $CI->Partidas_Model->actualizar_correlativo_partida($nuevocorrelativo, $id_tipo_partida, $idaniopartida, $idmespartida);
    }

    public function anular_partida($idventa)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $CI =  &get_instance();
        $CI->load->model('Partidas_Model');
        $CI->Partidas_Model->anular_partida_venta($idventa);
    }

    // Generar partida para las remesas
    public function generar_partida_diaria($idTipoComprobante, $idEncaComprobante, $codigopartida, $fecha_comprobante)
    {
        // echo 'LOS DATOS CAPTURADOS SON '. $idTipoComprobante . ' ID_RECIBO' .$idEncaComprobante. 'cODIGOPARTIDA' .$codigopartida.  '--' .$fecha_comprobante;
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $CI =  &get_instance();
        $CI->load->model('Partidas_Model');

        $id_banco            = null;
        $id_proveedor        = null;
        $id_vendedor         = null;
        $codigo_partida      = $codigopartida;
        $fecha               = $fecha_comprobante;
        $idventa             = $idEncaComprobante;
        $tipogenerar         = 1;    // 1- partida de venta por comprobante,  2 partida de venta consolidada
        $iddet_partida       = null; // Representa el id del detalle de la partida para cada item
        $idpartida           = null; // Representa el id de la partida que se generara
        $ultima_linea        = null;
        $valor_cheque        = 0;    // Se utiliza cuando se trate de un abono por cuenta por cobrar o pagar y el pago se haga en  cheque
        $nombre_cheque       = null;
        $numero_cxp          = null; // Se utilizara cuando la partida se genere por cuenta por pagar
        $fecha_cxp           = null; // Se utilizara cuando la partida se genere por cuenta por pagar
        $prov_cxp            = null; // Se utilizara cuando la partida se genere por cuenta por pagar
        $forma_pago          = null; // captura la forma en la que se hace el  abono

        echo 'El Codigo de Partida a Enviar Es ' . $codigo_partida;
        $datosCabecera    = $CI->Partidas_Model->obtenerdatos_cabecera_partida($codigo_partida);
        var_dump($datosCabecera);
        $idpartida        =  $this->crear_partida($datosCabecera);

        if ($idpartida != 0) {
            $arrDatosCabecera   =   array(
                                            'idtipopartida'    =>   $datosCabecera[0]->id,
                                            'nombre_partida'   =>   $datosCabecera[0]->nombre_partida,
                                            'correlativopart'  =>   $datosCabecera[0]->correlativo,
                                            'idaniopartida'    =>   $datosCabecera[0]->id_anio_partida,
                                            'idmespartida'     =>   $datosCabecera[0]->id_mes_partida,
                                    );

            // Creando la linea de banco
            $detalledebanco  =  $CI->Partidas_Model->get_detalle_bancabonocuenta_cliente($idventa);
            $id_banco        =  $detalledebanco[0]->id_banco;// se captura el  banco donde se hizo la remesa  ;
            $forma_pago      =  $detalledebanco[0]->formapago;

            if(!empty($detalledebanco)) {
                foreach($detalledebanco as $row) {
                    $tipo_comprobante =  null;
                    $numero_partida   =  $datosCabecera[0]->correlativo;
                    $fecha_ingreso    =  $fecha;
                    $linea            =  1;
                    $cuenta_mayor     =  substr(strval($row->cuenta_contable), 0, 4);
                    $cuenta           =  $row->cuenta_contable;
                    $concepto         =  $row->nombre;
                    $valor_cargo      =  $row->debe;
                    $valor_abono      =  0;
                    $orden_impresion  =  1;
                    $numero_cxp       =  $numero_cxp;
                    $fecha_cxp        =  $fecha_cxp ;
                    $prov_cxp         =  $prov_cxp;

                    $arrDetallePartida  =   array(
                                                    'id_encabezado'        =>   $idpartida,
                                                    'id_tipo_comprobante'  =>   $tipo_comprobante,
                                                    'numero_partida'       =>   $numero_partida,
                                                    'fecha_ingreso'        =>   $fecha_ingreso,
                                                    'linea'                =>   $linea,
                                                    'cuenta_mayor'         =>   $cuenta_mayor,
                                                    'cuenta_contable'      =>   $cuenta,
                                                    'concepto'             =>   $concepto,
                                                    'valor_cargo'          =>   $valor_cargo,
                                                    'valor_abono'          =>   $valor_abono,
                                                    'orden_impresion'      =>   $orden_impresion,
                                                    'numero_cxp'           =>   $numero_cxp,
                                                    'fecha_cxp'            =>   $fecha_cxp,
                                                    'prov_cxp'             =>   $prov_cxp,
                                            );

                    $this->generar_detalle_partida($arrDetallePartida, $iddet_partida);
                }
            }

            // generando el  detalle de abonos del cliente
            $detalle_abono  =  $CI->Partidas_Model->get_detalle_abonocuenta_cliente($idventa);

            if(!empty($detalle_abono)) {
                foreach ($detalle_abono as  $row) {
                    $tipo_comprobante =  null;
                    $numero_partida   =  $datosCabecera[0]->correlativo;
                    $fecha_ingreso    =  $fecha;
                    $linea            =  1;
                    $cuenta_mayor     =  substr(strval($row->cuenta_contable), 0, 4);
                    $cuenta           =  $row->cuenta_contable;
                    $concepto         =  'Pago de cliente con comprobante #' . $row->numero_comprobante;
                    $valor_cargo      =  0 ;
                    $valor_abono      =  $row->abono;
                    $orden_impresion  =  1;
                    $numero_cxp       =  $numero_cxp;
                    $fecha_cxp        =  $fecha_cxp ;
                    $prov_cxp         =  $prov_cxp;

                    $arrDetallePartida  =   array(
                                                    'id_encabezado'        =>   $idpartida,
                                                    'id_tipo_comprobante'  =>   $tipo_comprobante,
                                                    'numero_partida'       =>   $numero_partida,
                                                    'fecha_ingreso'        =>   $fecha_ingreso,
                                                    'linea'                =>   $linea,
                                                    'cuenta_mayor'         =>   $cuenta_mayor,
                                                    'cuenta_contable'      =>   $cuenta,
                                                    'concepto'             =>   $concepto,
                                                    'valor_cargo'          =>   $valor_cargo,
                                                    'valor_abono'          =>   $valor_abono,
                                                    'orden_impresion'      =>   $orden_impresion,
                                                    'numero_cxp'           =>   $numero_cxp,
                                                    'fecha_cxp'            =>   $fecha_cxp,
                                                    'prov_cxp'             =>   $prov_cxp,
                                            );
                    $this->generar_detalle_partida($arrDetallePartida, $iddet_partida);
                }
            }

            // Generando la Sumatoria de la Partida
            $sumdetpartida = $CI->Partidas_Model->sumar_detalle_partida($idpartida);
            if(!empty($sumdetpartida)) {
                $arrEncabezadoPartida   =   array(
                                                    'idventa'          =>   $idventa,
                                                    'id_empresa'       =>   $_SESSION["empresa_id"],
                                                    'id_banco'         =>   $id_banco,
                                                    'id_usuario'       =>   $_SESSION["user_id"],
                                                    'id_proveedor'     =>   $id_proveedor,
                                                    'id_tipo_partida'  =>   $datosCabecera[0]->id,
                                                    'id_vendedor'      =>   $id_vendedor,
                                                    'tipo_partida'     =>   substr($datosCabecera[0]->nombre_partida, 0, 2),
                                                    'numero_partida'   =>   $datosCabecera[0]->correlativo,
                                                    'fecha_ingreso'    =>   $fecha,
                                                    'concepto'         =>   $datosCabecera[0]->nombre_partida,
                                                    'total_cargos'     =>   $sumdetpartida[0]->cargo,
                                                    'total_abonos'     =>   $sumdetpartida[0]->abono,
                                                    'ultima_linea'     =>   $ultima_linea,
                                                    'valor_cheque'     =>   $sumdetpartida[0]->cargo,
                                                    'nombre_cheque'    =>   $forma_pago,
                                            );

                $this->generar_cabecera_partida($arrEncabezadoPartida, $idpartida);
                $this->actualizar_correlativo_partida($arrDatosCabecera);
            }
        } else {
            echo 'No se Pudo Generar la Partida';
        }
    }


}
