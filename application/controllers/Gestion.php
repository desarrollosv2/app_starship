<?php
// File: application/controllers/Productos.php
class Gestion extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('America/El_Salvador');

        $this->load->model('Gestion_Model');

        $this->load->model('WhModel');
        $this->load->model('CortesModel');
        $this->load->view("verifica");
        $this->load->model("WarehouseModel");
        // $this->load->library('TokenMasterLib');
        $this->load->library('EmpresaLib');
        // $this->load->library('TokenLib');
        $this->load->library('CrearGuia');


        $this->load->helper(['url', 'form']);
        $this->load->library('form_validation');
    }

    public function index()
    {
        $this->load->view('gestion/form');
    }


    public function consulta_all_tracks()
    {
        $this->load->view("all_tracks/form");
    }


    public function list_warehouse()
    {

        $listW = $_GET["txtListWarehouse"];

        $decodedString = urldecode($listW);
        $array = explode("\r\n", $decodedString);

        $list_warehouse = $this->Gestion_Model->getLastStatus($array);

        $this->datos["list_warehouse"] = $list_warehouse;

        $this->load->view("all_tracks/list_track", $this->datos);
    }


    public function status_warehouse($warehouse)
    {
        $list_status = $this->Gestion_Model->getStatusByWarehouse($warehouse);
        $this->datos["list_status"] = $list_status;
        $this->load->view("all_tracks/list_status", $this->datos);
    }


    public function rendimiento_status()
    {


        for ($i = 0; $i < 300; $i++) {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://stargateserp.com/app_starship/ws/index.php/api/estados',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '{
			    "guia": "9999999999999",
			    "codigo": "19",
			    "estatus": "Ingresado a bodega",
			    "fecha": "2025-12-02T14:30:59-06:00",
			    "latitud": "13.91637",
			    "devolucion": false,
			    "longitud": "-89.850021",
			        "pod": [
			            
			        ]  
			}',
                CURLOPT_HTTPHEADER => array(
                    'x-api-key: 4a4da5bb348e648e51d84e24c8652d1284c31156',
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            echo $response;

            $data = array(
                'respuesta' => $response,
            );


            $this->Gestion_Model->guardar_rendimiento($data);

            sleep(1);
        }
    }




    public function buscar_bag_number()
    {

        
        $this->load->view("verifica");

        $bag_number = $this->input->post('tracking_number', TRUE);
        $bag_number = trim($bag_number);

        $master_id = $this->input->post('master-id', TRUE);

        


        // Llamamos al método de la librería para obtener los datos de la empresa
        $datosEmpresa = $this->empresalib->obtenerDatosDeEmpresa($master_id);


        

        if ($datosEmpresa) {
            // Si se encuentran los datos, podemos usarlos para cualquier operación
            $id_tokencliente = $datosEmpresa['id_tokencliente'];
            $token_cliente = $datosEmpresa['token_cliente'];
            $token_lmd = $datosEmpresa['token_lmd'];

            // Mostrar los datos obtenidos
            //  echo "Token id: " . $id_tokencliente . "<br>";
            //   echo "Token Cliente: " . $token_cliente . "<br>";
            //  echo "Token LMD: " . $token_lmd . "<br>";


            // Aquí puedes agregar más lógica, como generar el token utilizando cURL, etc.
        } else {
            // Si no se encuentran datos
            echo "No se encontraron datos para la empresa con master-id: " . $master_id;
        }

        $request = $this->Gestion_Model->obtener_bolsa($master_id, $bag_number);

        if ($_SESSION['pais'] == 1) {
            /** Toma la hora activa para la recolecta */
            //      $corte = $this->CortesModel->obtener_activo();
            //    $strfecha = $corte->hora_corte;
        }

        



        if ($_SESSION['pais'] == 2) {
            //  $strfecha = date("Y-m-d") . '23:05:05';
        }

        $strfecha = date("Y-m-d") . '23:05:05';
        $fecha_recoleccion = $strfecha;
        // echo $token_lmd;
        foreach ($request as $row) {

            if ($row->pickup_number == 0) {


                $rsl = $this->WarehouseModel->datosMaster($row->id_manifiesto);

                $numeroMaster = $rsl->manifiesto;
                $fecha_arribo_miami = $rsl->fecha_arribo_miami;


                $numero_guia = $row->tracking_number;

                if ($fecha_arribo_miami === null || $fecha_arribo_miami === '') {

                    return $this->output
                        ->set_content_type('application/json')
                        ->set_status_header(400)
                        ->set_output(json_encode(['error' => 'La fecha de arribo es requerida.']));
                }

                $request216 = $this->WarehouseModel->getfecha_evento($row->tracking_number, 216);
                $request288 = $this->WarehouseModel->getfecha_evento($row->tracking_number, 288);
                // si el valor de la recolecta es ==0 ejecuta
                //  $telefono = $rsl->consignee_phone;

                if ($row->tipo_servicio == "SER") {
                    $montoCCE = 0;
                }
                if ($row->tipo_servicio == "CCE") {
                    $montoCCE = $row->cobro_final;
                }
                $guia_interna = $row->tracking_number;

                if (!empty($numeroMaster) &&   !empty($fecha_arribo_miami) &&  $numeroMaster != 0) {

                    if ($_SESSION['pais'] == 1) {


                        $datos = array(
                            "recolecta_fecha" => $fecha_recoleccion,
                            "recolecta_comentario" => "StarShip",
                            "tipo_entrega" => $row->tipo_entrega,
                            "guias" => array(
                                array(
                                    "identificacion" => "100",
                                    "nombre"         => $row->consignee,
                                    "correo"         => $row->consignee_email,
                                    "telefono"       => $row->consignee_phone,
                                    "direccion"      => $row->consignee_address1,
                                    "tipo_servicio"  => $row->tipo_servicio,
                                    "monto_cce"      => $montoCCE,
                                    "agencia_destino" =>  $row->agencia_destino,
                                    "con_seguro"     => "0",
                                    "con_liquidacion_documento" => "0",
                                    "departamento_id"   =>  $row->departamento_id,
                                    "municipio_id"      =>  $row->municipio_id,
                                    "master"            => array(
                                        "numero"          => $numeroMaster,
                                        "ata_miami"       => $fecha_arribo_miami,
                                        "arribo_aduana"   => $request216->fecha,
                                        "liberado_aduana" => $request288->fecha,


                                    ),
                                    "detalle" => array(
                                        array(
                                            "guia"          => $numero_guia,
                                            "peso"          => $row->gweight,
                                            "contenido"     => $row->commodity,
                                            "unidad_medida" => "LB",
                                        )
                                    )
                                )
                            )
                        );
                        //cho   json_encode($datos);

                    }


                    if ($_SESSION['pais'] == 2 || $_SESSION['pais'] == 3) {

                        $datos = array(
                            "recolecta_fecha" => $fecha_recoleccion,
                            "recolecta_comentario" => "StarShip",
                            "tipo_entrega" => $row->tipo_entrega,

                            "facturacion" => array(
                                //"nit" => $rsl->consignee_account,
                                "nit" => null,
                                "nombre" => $row->consignee,
                                "correo" => $row->consignee_email,
                                "direccion"      => $row->consignee_address1,
                            ),
                            "guias" => array(
                                array(
                                    "identificacion" => "100",
                                    "nombre"         => $row->consignee,
                                    "correo"         => $row->consignee_email,
                                    "telefono"       => $row->consignee_phone,
                                    "direccion"      => $row->consignee_address1,
                                    "tipo_servicio"  => $row->tipo_servicio,
                                    "monto_cce"      => $row->cobro_final,
                                    "agencia_destino" =>  $row->agencia_destino,
                                    "con_seguro"     => "0",
                                    "con_liquidacion_documento" => "0",
                                    "departamento_id"   =>  $row->departamento_id,
                                    "municipio_id"      =>  $row->municipio_id,
                                    "master"            => array(
                                        "numero"          => $numeroMaster,
                                        "ata_miami"       => $fecha_arribo_miami,
                                        "arribo_aduana"   => $request216->fecha,
                                        "liberado_aduana" => $request288->fecha,


                                    ),
                                    "detalle" => array(
                                        array(
                                            "guia"          => $numero_guia,
                                            "codigo"        => null,
                                            "peso"          => $row->gweight,
                                            "contenido"     => $row->commodity,
                                            "unidad_medida" => "LB",
                                            "cobro" => array(
                                                array(
                                                    "codigo" => "IVA",
                                                    "monto"  => $row->iva
                                                ),
                                                array(
                                                    "codigo" => "DAI",
                                                    "monto"  => $row->dai
                                                ),
                                                array(
                                                    "codigo" => "FLETE",
                                                    "monto"  => "0.00"
                                                ),
                                                array(
                                                    "codigo" =>  "DOCS",
                                                    "monto"  => number_format($row->manejo + $row->total_ivag, 2, '.', '.')
                                                )

                                            )
                                        )

                                    )
                                )
                            )
                        );
                        // echo json_encode($datos);
                        //	echo $token;

                    }
                }


                

                //actualizamos el payload para crear la guia
                $this->Gestion_Model->payload_guia($numeroMaster, $numero_guia, json_encode($datos));
                //  echo "finnnnnnnnnnnnnnnnnnnnnnnnnnnnnnn";
                //  echo json_encode($datos);
                //  exit;   
                // Llamamos a la librería para crear la guía

                //echo $token_lmd;
                //echo json_encode($datos);
                //die();

                $response = $this->crearguia->crearGuia($token_lmd, $datos);


                

                usleep(300000);
                // Comprobar si la respuesta de cURL fue exitosa
                if ($response === false) {
                    echo "Error al realizar la solicitud cURL.";
                    exit;
                }

                // Mostrar la respuesta cruda de la API para depuración
                //  echo "Respuesta cruda de la API: " . $response . "<br>";

                // Decodificar la respuesta JSON en un arreglo asociativo
                $response_data = json_decode($response, true);

                // Verificar si la decodificación fue exitosa
                if ($response_data === null) {
                    // echo "Error: No se pudo decodificar la respuesta JSON. Respuesta cruda: " . $response;
                    // exit;
                }

                // Verificar si la respuesta contiene un array de errores
                if (isset($response_data['errores']) && is_array($response_data['errores']) && count($response_data['errores']) > 0) {
                    // Si la respuesta contiene errores, mostramos el mensaje de error
                    foreach ($response_data['errores'] as $error) {
                        echo "Error: " . $error['mensaje'] . "<br>";
                    }
                    exit;  // Detenemos la ejecución ya que no se debe procesar más
                }

                // Verificar si la respuesta contiene los datos correctos
                if (isset($response_data['recolecta']) && isset($response_data['guias'][0])) {
                    // Extraer los valores específicos de la respuesta
                    $recolecta = $response_data['recolecta'];  // "RECOLECTA12022-33"
                    $guia = $response_data['guias'][0]['guia'];  // "c807-1-2022080943"
                    $seguimiento = $response_data['guias'][0]['seguimiento'];  // "https://app.c807.com/guia.php/seguimiento/c807-1-2022080943"

                    // Usar los valores en las variables para actualización o cualquier lógica
                    //  echo "Recolecta: " . $recolecta . "<br>";
                    //   echo "Guía: " . $guia . "<br>";
                    //  echo "Seguimiento: " . $seguimiento . "<br>";

                    // Aquí puedes actualizar las variables en la base de datos o hacer lo que necesites con los valores.
                    $this->WarehouseModel->update_tracking_id($row->id, $recolecta, $guia);
                    $this->WarehouseModel->update_creacion($guia_interna);
                } else {
                    // Si la respuesta no contiene los datos esperados, mostrar un mensaje de error
                    // echo "Error: No se pudieron obtener los datos de la respuesta.";
                    echo "Error: No se pudieron obtener los datos de la respuesta: " . $response;
                    //  echo "<br>Respuesta completa: " . print_r($response_data, true);  // Mostrar la respuesta completa para depuración
                }
            }
            //cerrar aqui
        }


        $data['lista'] = $this->Gestion_Model->buscar_trackings($master_id, $bag_number);
        $this->load->view('gestion/cuerpo', $data);
    }

    public function push_events_apg_old()
    {
        // Carga la librería
        ini_set('display_errors', '1');
        ini_set('display_startup_errors', '1');
        error_reporting(E_ALL);
        // enviamos evento fallidos
        // exit;
        $this->load->library('TokenApg');
        $req = $this->Gestion_Model->get_events_error();
        // var_dump($req);
        $cantidad_filas = count($req);
        $token = $this->tokenapg->getToken();

        //  echo "La cantidad de filas es: " . $cantidad_filas;
        echo $token;
        //exit;
        $id_estatus = '';
        foreach ($req as $fila) {

            $id_estatus = $fila->id_estatus;
            // echo "1-".$fila->tracking. "\n";



            //    if ($id_estatus == 16 || $id_estatus == 19) {
            if ($id_estatus == 16) {

                $resultado = $this->Gestion_Model->buscarPorDescripcion($problemaGestion ?? '');
                $statusName = $resultado ? $resultado[0]['codigo_cliente'] : '';
            } else {
                $resultado = $this->Gestion_Model->buscarPorCodigo($id_estatus);
                $statusName = $resultado ? $resultado[0]['codigo_cliente'] : '';
            }
            $this->_responderInmediatamente([
                'error' => false,
                'message' => 'Recibido correctamente.',
                'tracking' => ""
            ]);
            //echo $statusName;
            //break;

            $url = 'https://apiprod.apg-global.com/api/account/save-status';
            $data = [
                'tracking' => $fila->tracking,
                'status' => [
                    [
                        'statusName' => $statusName,
                        'location' => 'El Salvador',
                        'statusDate' => $fila->fecha,
                        'pod' => null,
                    ],
                ],
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json',
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            $response = curl_exec($ch);
            $error = curl_error($ch);
            curl_close($ch);

            //   echo $response;
            $data = json_decode($response, true);
            $valor = $data['objectResult']['response'] ?? null;

            $this->Gestion_Model->actualizar_respuesta($response, $fila->tracking, $valor, $id_estatus);

            sleep(1);
        }
        //  echo $row->id."\n";

    }
    public function push_events_apg()
    {

        // enviamos evento fallidos
        // exit;
        $req = $this->Gestion_Model->get_events_error();
        //  var_dump($req);
        $cantidad_filas = count($req);

        //   echo "La cantidad de filas es: " . $cantidad_filas;
        //  exit;

        $t = "";
        $total = is_array($req) ? count($req) : 0;
        $procesados = 0;
        $ok = 0;
        $fail = 0;
        $t0 = microtime(true);
        foreach ($req as $fila) {

            //  echo $fila->tracking;
            //  break;
            $latitud  = "13.91637";
            $longitud = "-89.850021";
            $pod      = []; // Puedes llenarlo si es necesario

            $data = [
                "guia"     => $fila->tracking,
                "codigo"   => $fila->id_estatus,
                "estatus"  => $fila->nombre,
                "fecha"    => $fila->fecha,
                "latitud"  => $latitud,
                "longitud" => $longitud,
                "pod"      => $pod,
            ];

            //  var_dump($data);

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://stargateserp.com/app_starship/ws/index.php/apg_events/estadosApg',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_HTTPHEADER => array(
                    'x-api-key: 4a4da5bb348e648e51d84e24c8652d1284c31156',
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);

            //   echo $response;


            //  echo $response;
            //  $data = json_decode($response, true);
            //   $valor = $data['objectResult']['response'] ?? null;

            // $this->Gestion_Model->actualizar_respuesta($response, $fila->tracking, $valor, $fila->id_estatus);

            $t = $fila->tracking;
            $ms = (int) round((microtime(true) - $t0) * 1000);
            // si este método se llama por HTTP y quieres devolver solo un mensaje final:
            log_message('debug', "[BATCH] Finalizado: {$procesados}/{$total} procesados en {$ms} ms (OK={$ok}, FAIL={$fail})");
            header('Content-Type: application/json');
            echo json_encode([
                'ok'        => true,
                'message'   => "Finalizado: {$procesados}/{$total} procesados en {$ms} ms",
                'resumen'   => ['ok' => $ok, 'fail' => $fail],
                'tracking'  => $t

            ]);
            sleep(1);
        }



        // ✅ un solo mensaje final (modo depuración y/o respuesta)
        //  log_message('debug', "[BATCH] Finalizado: {$procesados}/{$total} procesados en {$ms} ms (OK={$ok}, FAIL={$fail})");


    }
    private function _responderInmediatamente($response)
    {
        ignore_user_abort(true); // el script sigue si el cliente cierra
        if (ob_get_length()) {
            ob_clean();
        }

        $json = json_encode($response);

        header('Content-Type: application/json');
        header('Connection: close');
        header('Content-Length: ' . strlen($json));

        echo $json;

        // ✂️ Cierra la conexión HTTP
        ob_flush();
        flush();
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request(); // ✨ para FPM
        }
    }


    public function push_events() //para MA
    {
        exit;
        $empresa = 109;
        $request = $this->Gestion_Model->push_events($empresa);
        foreach ($request as $row) {
            $req = $this->Gestion_Model->get_events($row->tracking_number);
            foreach ($req as $fila) {

                $latitud  = "13.91637";
                $longitud = "-89.850021";
                $pod      = []; // Puedes llenarlo si es necesario

                $data = [
                    "guia"     => $fila->warehouse,
                    "codigo"   => $fila->id_estatus,
                    "estatus"  => $fila->nombre_estatus,
                    "fecha"    => $fila->fechaiso,
                    "latitud"  => $latitud,
                    "longitud" => $longitud,
                    "pod"      => $pod,
                ];

                //  var_dump($data);

                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://stargateserp.com/app_starship/ws/index.php/api/estados',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => json_encode($data),
                    CURLOPT_HTTPHEADER => array(
                        'x-api-key: 4a4da5bb348e648e51d84e24c8652d1284c31156',
                        'Content-Type: application/json'
                    ),
                ));

                $response = curl_exec($curl);
                curl_close($curl);

                echo $response;
            }
            echo $row->id . "\n";
        }
    }

    public function validate_events()
    {
        $empresa = 107;
        $request = $this->Gestion_Model->validate_events();  // muestra todos aquellos registro del campo entregado="NO"

        //   $cantidad_filas = count($request);


        foreach ($request as $row) {
            $tracking = $this->Gestion_Model->get_validate_events($row->tracking, $row->id_estatus);
            foreach ($tracking as $fila) {
                // echo $fila->tracking . " " . $fila->id_estatus . " " . $fila->respuesta . "\n";
                $json_string = $fila->respuesta;
                $data = json_decode($json_string);
                $result = $data->objectResult->response;
                if ($result == "OK") {
                    //actualizar valor aqui
                    $this->Gestion_Model->ok_events($row->tracking, $row->id_estatus);
                    break;
                }
            }
        }
    }
    public function push()
    {
        $this->load->view('gestion/push');
    }

    public function sincronizar_modal()
    {
        $this->load->view("gestion/sincronizar");
    }

    public function sincronizar($master)
    {

        ini_set('display_errors', '1');
        ini_set('display_startup_errors', '1');
        error_reporting(E_ALL);

        //$rsl = $this->WarehouseModel->get_tracking($guia);
        $request = $this->WhModel->get_pending_events($master);
        $empresaid = "";
        foreach ($request as $row) {
            $empresaid = $row->id_empresa;
            $empresa = $this->WarehouseModel->obtener_token($empresaid);
            $token_cliente = $empresa->token_cliente;
            $id_tokencliente = $empresa->id_tokencliente;
            $token_xpress = $empresa->token;
            break;
        }
        //echo $token_cliente . ' ==>' .         $id_tokencliente . '==> ' .        $token_xpress;
        foreach ($request as $row) {

            // $tracking = 'T1903728175FDD2A7E'; // $row->tracking_number;
            $tracking =  $row->tracking_number;
            $curl = curl_init();
            echo $tracking . "\n";
            curl_setopt_array($curl, array(

                CURLOPT_URL => 'https://app.c807.com/guia.php/api/get_historia/' . $tracking,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: ' . $token_xpress
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);

            $data = json_decode($response);


            $this->datos['lista'] = $data;
            // foreach ($this->datos['lista'] as $row) {
            //     if ($row->codigo == 15) {
            //         var_dump($row->pod);
            //     }
            // }

            if (!empty($this->datos['lista'])) {
                foreach ($this->datos['lista'] as $row) {


                    // Manejar tanto objeto como array
                    if (is_object($row)) {
                        $codigo = $row->codigo;
                        $estatus = $row->estatus;
                        $fecha = $row->fecha;
                    } elseif (is_array($row)) {
                        $codigo = $row['codigo'] ?? null;
                        $estatus = $row['estatus'] ?? null;
                        $fecha = $row['fecha'] ?? null;
                    } else {
                        // Si no es objeto ni array, saltar este registro
                        continue;
                    }

                    // Validar que los datos esenciales existan
                    if (empty($codigo)) {
                        continue; // Saltar registro si no tiene código
                    }

                    $result =  $this->WarehouseModel->buscar_estatus($row->codigo, $tracking);

                    if ($result) {
                        //  echo 'uno';
                    } else {
                        // echo "gggggggggggg" . $row->estatus;
                        date_default_timezone_set("America/El_Salvador");
                        $date = date('Y-m-d H:i:s');
                        $fecha_tmp = DateTime::createFromFormat('Y-m-d H:i:s', $row->fecha);
                        $fecha_iso = $fecha_tmp->format(DateTime::ATOM);
                        $latitud  = "13.91637";
                        $longitud = "-89.850021";
                        $pod      = []; // Puedes llenarlo si es necesario

                        $data = [
                            "guia"     => $tracking,
                            "codigo"   => $row->codigo,
                            "estatus"  => $row->estatus,
                            "fecha"    => $row->fecha,
                            "latitud"  => $latitud,
                            "longitud" => $longitud,
                            "pod"      => $pod,
                        ];



                        $curl = curl_init();

                        curl_setopt_array($curl, array(
                            CURLOPT_URL => 'https://stargateserp.com/app_starship/ws/index.php/api/estadosApg',
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS => json_encode($data),
                            CURLOPT_HTTPHEADER => array(
                                'x-api-key: 4a4da5bb348e648e51d84e24c8652d1284c31156',
                                'Content-Type: application/json'
                            ),
                        ));

                        $response = curl_exec($curl);
                        curl_close($curl);


                        sleep(1);
                    }
                }
            }
            //  break;
        }
        //  echo $response;


        // $this->load->view("seguimiento/aplicar_estatus", $this->datos);
    }

    public function make_guide($id = null)
    {

        // ini_set('display_errors', 1);
        // ini_set('display_startup_errors', 1);
        // error_reporting(E_ALL);
        //       $id = 2278526;

        $this->load->view("verifica");

        $this->WarehouseModel->update_creacion($id);

        $rsl = $this->WarehouseModel->obtener_guia($id);

        $master = $rsl->preclasificacion_manifiesto;
        $trackingNumber = $rsl->tracking_number;
        $fecha_arribo_miami = $rsl->fecha_arribo_miami;


        $request = $this->WarehouseModel->obtener_token($rsl->id_empresa);

        $request216 = $this->WarehouseModel->getfecha_evento($trackingNumber, 216);
        $request288 = $this->WarehouseModel->getfecha_evento($trackingNumber, 288);


        $token = $request->token;


        if ($_SESSION['pais'] == 1) {
            /** Toma la hora activa para la recolecta */
            $corte = $this->CortesModel->obtener_activo($id);
            $strfecha = $corte->hora_corte;
        }

        if ($_SESSION['pais'] == 2  || $_SESSION['pais'] == 3) {
            $strfecha = date("Y-m-d") . '23:05:05';
        }


        //$strfecha = date("Y-m-d") . '23:05:05';
        //$strfecha = date("Y-m-d") . '17:00:02';
        //$strfecha = date("Y-m-d") . '23:07:01';
        //$strfecha = date("Y-m-d") . '13:04:01';
        // $strfecha = date("Y-m-d") . '20:04:01';


        //inicio para siguiente dia
        // Combinar fecha y hora
        //$fecha = date("Y-m-d") . '08:01:05';

        // Crear objeto DateTime
        //$dt = new DateTime($fecha);

        // Sumar un día
        //$dt->modify('+1 day');

        // Obtener la nueva fecha como string
        //$strfecha = $dt->format('Y-m-d H:i:s');
        //fin
        $fecha_recoleccion = $strfecha; //date("Y-m-d H:i:s"); //"2023-08-21 16:30";

        $telefono = $rsl->consignee_phone;

        $numero_guia = '';
        if ($rsl->tracking_replace == null) {
            $numero_guia = $rsl->tracking_number;
        } else {
            $numero_guia = $rsl->tracking_replace;
        }

        if ($rsl->tipo_servicio == "SER") {
            $montoCCE = 0;
        }
        if ($rsl->tipo_servicio == "CCE") {
            $montoCCE = $rsl->cobro_final;
        }


        if ($_SESSION['pais'] == 1) {


            $datos = array(
                "recolecta_fecha" => $fecha_recoleccion,
                "recolecta_comentario" => "StarShip",
                "tipo_entrega" => $rsl->tipo_entrega,
                "guias" => array(
                    array(
                        "identificacion" => "100",
                        "nombre"         => $rsl->consignee,
                        "correo"         => $rsl->consignee_email,
                        "telefono"       => $telefono,
                        "direccion"      => $rsl->consignee_address1,
                        "tipo_servicio"  => $rsl->tipo_servicio,
                        "monto_cce"      => $montoCCE,
                        "agencia_destino" =>  $rsl->agencia_destino,
                        "con_seguro"     => "0",
                        "con_liquidacion_documento" => "0",
                        "departamento_id"   => 4, // $_POST['depto'], descoemtna
                        "municipio_id"      => 41, // $_POST['munic'], descomentar
                        "master"            => array(

                            "numero"          => $master,
                            "ata_miami"       => $fecha_arribo_miami,
                            "arribo_aduana"   => $request216->fecha,
                            "liberado_aduana" => $request288->fecha,


                        ),
                        "detalle" => array(
                            array(
                                "guia"          => $numero_guia,
                                "peso"          => $rsl->gweight,
                                "contenido"     => $rsl->commodity,
                                "unidad_medida" => "LB",
                            )
                        )
                    )
                )
            );
            echo   json_encode($datos);
        }


        if ($_SESSION['pais'] == 2 || $_SESSION['pais'] == 3) {

            $datos = array(
                "recolecta_fecha" => $fecha_recoleccion,
                "recolecta_comentario" => "StarShip",
                "tipo_entrega" => $rsl->tipo_entrega,

                "facturacion" => array(
                    //"nit" => $rsl->consignee_account,
                    "nit" => null,
                    "nombre" => $rsl->consignee,
                    "correo" => $rsl->consignee_email,
                    "direccion"      => $rsl->consignee_address1,
                ),
                "guias" => array(
                    array(
                        "identificacion" => "100",
                        "nombre"         => $rsl->consignee,
                        "correo"         => $rsl->consignee_email,
                        "telefono"       => $telefono,
                        "direccion"      => $rsl->consignee_address1,
                        "tipo_servicio"  => $rsl->tipo_servicio,
                        "monto_cce"      => $rsl->cobro_final,
                        "agencia_destino" =>  $rsl->agencia_destino,
                        "con_seguro"     => "0",
                        "con_liquidacion_documento" => "0",
                        "departamento_id"   =>  $_POST['depto'],
                        "municipio_id"      =>  $_POST['munic'],
                        "master"            => array(
                            array(
                                "numero" => "0",
                                "ata_miami"   =>  $_POST['depto'],
                                "arribo_aduana"      =>  $_POST['munic'],
                                "liverado_aduana"      =>  $_POST['munic'],
                            )

                        ),
                        "detalle" => array(
                            array(
                                "guia"          => $numero_guia,
                                "codigo"        => null,
                                "peso"          => $rsl->gweight,
                                "contenido"     => $rsl->commodity,
                                "unidad_medida" => "LB",
                                "cobro" => array(
                                    array(
                                        "codigo" => "IVA",
                                        "monto"  => $rsl->iva
                                    ),
                                    array(
                                        "codigo" => "DAI",
                                        "monto"  => $rsl->dai
                                    ),
                                    array(
                                        "codigo" => "FLETE",
                                        "monto"  => "0.00"
                                    ),
                                    array(
                                        "codigo" =>  "DOCS",
                                        "monto"  => number_format($rsl->manejo + $rsl->total_ivag, 2, '.', '.')
                                    )

                                )
                            )

                        )
                    )
                )
            );
            //echo json_encode($datos);
            //	echo $token;
            //exit;
        }


        $url = "";
        $url = 'https://app.c807.com/guia.php/api/set_registro';
        $url = 'https://qaapp.c807.com/guia.php/api/set_registro';


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false, //TODO: quitar esto en produccion
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $json = json_encode($datos),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: ' . $token,

            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        echo $response;
    }

    public function update_fechaArribo($id)
    {
        $id_master = $this->input->post('id_master');
        $fecha     = $this->input->post('fecha');
        $this->WarehouseModel->update_fechaArribo($id_master, $fecha);
    }

    public function ver_contenido_tracking($id)
    {
        $data['contenido'] = $this->Gestion_Model->ver_contenido_tracking($id);
        $this->load->view('gestion/ver_contenido_tracking', $data);
    }

    public function obtener_resumen_referencias()
    {

        //ini_set("display_errors",1);
        $id_manifiesto = $this->input->post('id_manifiesto');

        

        $datos = $this->Gestion_Model->obtener_resumen_referencias($id_manifiesto);

        echo json_encode([
            'status' => true,
            'data'   => $datos
        ]);
    }

    public function facturar_ma_pendiente()
    {

        $tracking_pending = $this->Gestion_Model->verify_ma_pending();

        //var_dump($tracking_pending);
        //die();

        $count = 0;

        foreach ($tracking_pending as $key) {
            echo $key->tracking . "-" . $key->id_estatus . "<br>";

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://stargateserp.com/app_starship/ws/index.php/api/facturafel/' . $key->tracking . '/' . $key->id_estatus,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_HTTPHEADER => array(
                    'x-api-key: 4a4da5bb348e648e51d84e24c8652d1284c31156'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            echo $response;

            $this->Gestion_Model->update_status_ma($key->tracking);

            $count++;

            //die(); //only one
        }

        echo "Cantidad de facturas emitidas " . $count;
    }



    //Code Misa
    public function conversor_csv()
    {
        $this->load->view('conversor/conversor');
    }

    


    public function convertir_duca()
    {
        $ma = $this->input->post('ma');

        if ($ma) {
            $this->procesar_ma();
        } else {
            $this->procesar_duca_normal();
        }
    }

    private function registrar_resultado_duca(&$resultados, $nombre, $estado, $mensaje)
    {
        $resultados[] = [
            'nombre' => $nombre,
            'estado' => $estado,
            'mensaje' => $mensaje,
        ];
    }

    private function enviar_resultados_duca($resultados)
    {
        header('X-DUCA-Resultados: ' . base64_encode(json_encode($resultados)));
    }

    public function procesar_ma()
    {
        if (!isset($_FILES['duca'])) {
            show_error("No se recibió ningún archivo.");
            return;
        }

        $this->load->library('Duca_parser');

        $data_final = [];
        $resultados = [];
        $total_files = count($_FILES['duca']['name']);

        for ($i = 0; $i < $total_files; $i++) {
            if ($_FILES['duca']['error'][$i] != 0) {
                $this->registrar_resultado_duca($resultados, $_FILES['duca']['name'][$i], 'ilegible', 'Error al cargar el archivo.');
                continue;
            }

            $pdf_path = $_FILES['duca']['tmp_name'][$i];
            $nombre_original = $_FILES['duca']['name'][$i];

            $duca = pathinfo($nombre_original, PATHINFO_FILENAME);

            $data = $this->duca_parser->procesar($pdf_path, $duca);
            $resultado_parser = $this->duca_parser->obtener_ultimo_resultado();

            if (empty($data)) {
                $this->registrar_resultado_duca($resultados, $nombre_original, $resultado_parser['estado'], $resultado_parser['mensaje']);
                continue;
            }

            $this->registrar_resultado_duca($resultados, $nombre_original, $resultado_parser['estado'], $resultado_parser['mensaje']);

            foreach ($data as $tracking => $info) {
                $info['tracking'] = $tracking;

                $unique_key = $info['duca'] . '_' . $tracking;

                if (!isset($data_final[$unique_key])) {

                    $data_final[$unique_key] = $info;
                } else {

                    $data_final[$unique_key]['lineas'] = array_merge(
                        $data_final[$unique_key]['lineas'],
                        $info['lineas']
                    );

                    $data_final[$unique_key]['aduana'] += $info['aduana'];
                    $data_final[$unique_key]['dai'] += $info['dai'];
                    $data_final[$unique_key]['isv'] += $info['isv'];
                    $data_final[$unique_key]['sel'] += $info['sel'];
                    $data_final[$unique_key]['total_general'] += $info['total_general'];
                }
            }
        }

        if (empty($data_final)) {
            $this->enviar_resultados_duca($resultados);
            http_response_code(422);
            exit('No se pudieron extraer datos de los PDFs.');
            return;
        }

        $this->enviar_resultados_duca($resultados);
        $this->generar_csv_ma($data_final);
    }

    /*public function procesar_ma()
    {

        
        if (!isset($_FILES['duca'])) {
            show_error("No se recibió ningún archivo.");
            return;
        }

        $this->load->library('Duca_parser');

        $data_final = [];
        $total_files = count($_FILES['duca']['name']);

        for ($i = 0; $i < $total_files; $i++) {
            if ($_FILES['duca']['error'][$i] != 0) {
                continue;
            }

            $pdf_path = $_FILES['duca']['tmp_name'][$i];
            $nombre_original = $_FILES['duca']['name'][$i];

            $duca = pathinfo($nombre_original, PATHINFO_FILENAME);

            $data = $this->duca_parser->procesar($pdf_path, $duca);

            if (empty($data)) continue;

            foreach ($data as $tracking => $info) {
                $info['tracking'] = $tracking;

                $unique_key = $info['duca'] . '_' . $tracking;

                if (!isset($data_final[$unique_key])) {

                    $data_final[$unique_key] = $info;
                } else {

                    $data_final[$unique_key]['lineas'] = array_merge(
                        $data_final[$unique_key]['lineas'],
                        $info['lineas']
                    );

                    $data_final[$unique_key]['aduana'] += $info['aduana'];
                    $data_final[$unique_key]['dai'] += $info['dai'];
                    $data_final[$unique_key]['isv'] += $info['isv'];
                    $data_final[$unique_key]['sel'] += $info['sel'];
                    $data_final[$unique_key]['total_general'] += $info['total_general'];
                }
            }
        }

        if (empty($data_final)) {
            show_error("No se pudieron extraer datos de los PDFs.");
            return;
        }

        $this->generar_csv_ma($data_final);
    }*/

    public function generar_csv_ma($data)
    {
        $filename = 'REPORTE_MA_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        $output = fopen('php://output', 'w');

        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        fputcsv($output, [
            'Tracking',
            'DAI',
            'ISV',
            'SEL',
        ]);

        foreach ($data as $info) {
            fputcsv($output, [
                $info['tracking'],
                number_format($info['dai'], 4, '.', ''),
                number_format($info['isv'], 4, '.', ''),
                number_format($info['sel'], 4, '.', ''),
            ]);
        }

        fclose($output);
        exit;
    }

    public function actualizar_impuestos_pendientes(){

        $res = $this->Gestion_Model->ver_impuestos_pendientes();

        foreach ($res as $item) {
            echo $item->package_number . "\n";
        }





    }


    public function procesar_duca_normal()
    {

        ini_set("display_errors", 1);
        set_time_limit(0);
        ini_set('memory_limit', '2048M');

        if (!isset($_FILES['duca'])) {
            show_error("No se recibió ningún archivo.");
            return;
        }

        $this->load->library('Duca_parser');

        $data_final = [];
        $resultados = [];
        $toInsert = [];
        $total_files = count($_FILES['duca']['name']);

        for ($i = 0; $i < $total_files; $i++) {
            if ($_FILES['duca']['error'][$i] != 0) {
                $this->registrar_resultado_duca($resultados, $_FILES['duca']['name'][$i], 'ilegible', 'Error al cargar el archivo.');
                continue;
            }

            $pdf_path = $_FILES['duca']['tmp_name'][$i];
            $nombre_original = $_FILES['duca']['name'][$i];


            $duca = pathinfo($nombre_original, PATHINFO_FILENAME);

            $data = $this->duca_parser->procesar($pdf_path, $duca);
            $resultado_parser = $this->duca_parser->obtener_ultimo_resultado();

            if (empty($data)) {
                $this->registrar_resultado_duca($resultados, $nombre_original, $resultado_parser['estado'], $resultado_parser['mensaje']);
                continue;
            }

            $this->registrar_resultado_duca($resultados, $nombre_original, $resultado_parser['estado'], $resultado_parser['mensaje']);

            $primer_info = reset($data);
            $chepito = trim((string) ($primer_info['chepito'] ?? ''));
            $referencia = $chepito !== ''
                ? $this->Gestion_Model->obtener_id_manifiesto_por_chepito($chepito)
                : null;
            $id_manifiesto = $referencia ? $referencia->id_manifiesto : null;

            if ($id_manifiesto === null) {
                log_message(
                    'error',
                    'No se encontró id_manifiesto para el chepito: '
                        . ($chepito ?: '[vacío]')
                        . ' en DUCA: ' . $nombre_original
                );
            }


            //var_dump($data);
            //die();

            foreach ($data as $tracking => $info) {
                // Guardar tracking dentro del array
                $info['tracking'] = $tracking;

                $unique_key = $info['duca'] . '_' . $tracking;

                if (!isset($data_final[$unique_key])) {

                    $data_final[$unique_key] = $info;
                } else {

                    $data_final[$unique_key]['lineas'] = array_merge(
                        $data_final[$unique_key]['lineas'],
                        $info['lineas']
                    );

                    $data_final[$unique_key]['aduana'] += $info['aduana'];
                    $data_final[$unique_key]['dai'] += $info['dai'];
                    $data_final[$unique_key]['isv'] += $info['isv'];
                    $data_final[$unique_key]['sel'] += $info['sel'];
                    $data_final[$unique_key]['total_general'] += $info['total_general'];
                }

                $impuestos = $data_final[$unique_key]['total_general'];



                $toInsert[] = array(
                    'package_number' => $tracking,
                    'total_impuestos' => $impuestos,
                    'id_manifiesto' => $id_manifiesto
                );
            }
        }

        if (count($toInsert) > 0) {
            $this->Gestion_Model->insertar_impuestos_hn_by_batch($toInsert);
        }

        if (empty($data_final)) {
            $this->enviar_resultados_duca($resultados);
            http_response_code(422);
            exit('No se pudieron extraer datos de los PDFs.');
        }

        $this->enviar_resultados_duca($resultados);
        $this->generar_zip_con_reportes($data_final);
    }




    /*public function procesar_duca_normal(){

        ini_set("display_errors",1);
        set_time_limit(0);
        ini_set('memory_limit', '2048M');

        //var_dump($_FILES['duca']['error']);
        //var_dump($_FILES['duca']);
        //die();

        if (!isset($_FILES['duca'])) {
            show_error("No se recibió ningún archivo.");
            return;
        }

        $this->load->library('Duca_parser');

        $data_final = [];
        $total_files = count($_FILES['duca']['name']);

        for ($i = 0; $i < $total_files; $i++) {


            $toInsert = array();

            if ($_FILES['duca']['error'][$i] != 0) {
                continue;
            }

            $pdf_path = $_FILES['duca']['tmp_name'][$i];
            $nombre_original = $_FILES['duca']['name'][$i];

            

            $duca = pathinfo($nombre_original, PATHINFO_FILENAME);

            $data = $this->duca_parser->procesar($pdf_path, $duca);

            if (empty($data)) continue;


            //var_dump($data);
            //die();

            $y = 0;
            $cantidad_cargas = (int) (count($data) / 500);

            $cant_carga = 0;
            
            foreach ($data as $tracking => $info) {

                $y++;
                // Guardar tracking dentro del array
                $info['tracking'] = $tracking;

                // La clave evita automáticamente los duplicados
                $tracking_array[$tracking] = $tracking;

                // Cantidad de tracking únicos
                $cantidad = count($tracking_array);

                $cant_meta = count($data);




                $res = $cant_meta - $cantidad;

                


                if ($y == 501 && $cant_carga < $cantidad_cargas) {

                    //$this->Gestion_Model->actualizar_impuestos_hn_by_batch($toUpdate);
                    

                    //var_dump($toInsert);
                    //die();
                    
                    $this->Gestion_Model->insertar_impuestos_hn_by_batch($toInsert);

                    $toInsert = array(); //reset

                    $y = 0;

                    $cant_carga++;


                    //echo "Terminamos";
                    //die();

                }


                // Clave única DUCA + tracking
                $unique_key = $info['duca'] . '_' . $tracking;

                if (!isset($data_final[$unique_key])) {

                    $data_final[$unique_key] = $info;
                } else {

                    $data_final[$unique_key]['lineas'] = array_merge(
                        $data_final[$unique_key]['lineas'],
                        $info['lineas']
                    );

                    $data_final[$unique_key]['aduana'] += $info['aduana'];
                    $data_final[$unique_key]['dai'] += $info['dai'];
                    $data_final[$unique_key]['isv'] += $info['isv'];
                    $data_final[$unique_key]['sel'] += $info['sel'];
                    $data_final[$unique_key]['total_general'] += $info['total_general'];
                }

                $impuestos = $data_final[$unique_key]['total_general'];



                $toInsert[] = array(
                    'package_number' => $tracking,
                    'total_impuestos' => $impuestos
                );
                

                



                //$this->Gestion_Model->actualizar_impuestos_hn($tracking, $impuestos);



                //die();
            }

            if(count($toInsert)>0){
                $this->Gestion_Model->insertar_impuestos_hn_by_batch($toInsert);
            }

            
        }


        //var_dump($data_final);

        

       
        //echo "Ya terminamos la lectura";

        //die();

        //$this->generar_csv($data_final);
        $this->generar_zip_con_reportes($data_final);
    }*/


    private function generar_csv($data, $ruta_archivo = null)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $filename = 'REPORTE_DUCA_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        if ($ruta_archivo) {
            $output = fopen($ruta_archivo, 'w');
        } else {
            $output = fopen('php://output', 'w');
        }

        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
        /*fputcsv($output, [
            'DUCA',
            'Fecha',
            'Tracking',
            'Lineas',
            'Valor Aduana',
            'DAI',
            'ISV',
            'SEL',
            'Total Tracking',
            'Tasa Cambio',
            'Total USD'
        ]);*/

        fputcsv($output, [
            '运单号（必填）|Tracking number (required)',                    // A
            'Platform包裹号（必填）|Platform Package Number (required)',  // B
            '包裹清关所属国家（必填）|Country (required)',                // C
            '包裹清关所在省（非必填）|Province of customs clearance (optional)', // D
            '包裹收货所在省（非必填）|Province of receipt (optional)',    // E
            '币种（申报金额与税费）（必填）|Currency (required)',         // F
            '币种（税费）（必填）|Currency (required)',                  // G
            '服务商申报包裹代缴税费总金额（必填）|Total payable amount (required)', // H
            '服务商申报包裹代缴关税总金额（必填）|Total Duty (required)', // I
            '服务商申报包裹代缴消费税金额（必填）|Total Excise Tax (required)', // J
            '服务商申报包裹代缴增值税金额（必填）|Total GST (required)', // K
            '服务商申报包裹代缴反倾销税金额（必填）|Total SIMA (required)', // L
            '服务商申报包裹代缴地方消费税金额（必填）|Total Provincial tax (required)', // M
            '币种（手续费）|Currency（Service Fee)',                      // N
            '服务商申报包裹手续费（非必填）|Service Fee (optional)',     // O
            '税单号（必填）|Transaction number (required)',               // P
            '商品序号（非必填）|Duca Row No(optional)',                  // Q
            '申报日期（必填）|Declaration date（required)',              // R
            '汇率（必填）|Exchange Rate (required)',                     // S
            'CIF-HNL',                                                   // T
            'CIF-USD'                                                    // U
        ]);

        $trackings = array_column($data, 'tracking');

        // Consultar todos los BG en una sola query
        $bg_data = $this->Gestion_Model->obtener_tracking_bg($trackings);

        foreach ($data as $key => $info) {

            $tracking = $info['tracking'];

            $bg_tracking = isset($bg_data[$tracking]) ? $bg_data[$tracking] : '';
            $lineas = implode(',', array_unique($info['lineas']));
            $duca_lineas = $info['duca'] . '(' . $lineas . ')';

            $total_lps = $info['aduana'];
            $tasa = $info['tasa_cambio'];

            $total_usd = 0;

            if ($tasa > 0) {
                $total_usd = round($total_lps / $tasa, 2);
            }

            /*fputcsv($output, [
                '="' . $info['duca'] . '"',
                $info['fecha'],
                '="'.$info['tracking'].'"',
                '="'.$duca_lineas.'"',
                number_format($info['aduana'], 2, '.', ''),
                number_format($info['dai'], 4, '.', ''),
                number_format($info['isv'], 4, '.', ''),
                number_format($info['sel'], 4, '.', ''),
                number_format($info['total_general'], 4, '.', ''),
                number_format($info['tasa_cambio'], 6, '.', ''),
                number_format($total_usd, 2, '.', '')
            ]);*/

            fputcsv($output, [

                // A
                '="' . $info['tracking'] . '"',

                // B
                '="' . $bg_tracking . '"',

                // C
                'Honduras',

                // D
                '',

                // E
                '',

                // F
                'HNL',

                // G
                'HNL',

                // H
                number_format($info['total_general'], 4, '.', ''),

                // I
                number_format($info['dai'], 4, '.', ''),

                // J
                number_format($info['sel'], 4, '.', ''),

                // K
                number_format($info['isv'], 4, '.', ''),

                // L
                '0',

                // M
                '0',

                // N
                'HNL',

                // O
                '0',

                // P
                '="' . $info['duca'] . '"',

                // Q
                '="' . $duca_lineas . '"',

                // R
                '="' . $info['fecha'] . '"',

                // S
                number_format($info['tasa_cambio'], 6, '.', ''),

                // T
                number_format($info['aduana'], 2, '.', ''),

                // U
                number_format($total_usd, 2, '.', '')

            ]);
        }


        fclose($output);
        if (!$ruta_archivo) {
            exit;
        }
    }

    private function generar_zip_con_reportes($data_final)
    {
        $temp_path = FCPATH . 'temp/';
        if (!file_exists($temp_path)) {
            mkdir($temp_path, 0777, true);
        }

        $fecha = date('Ymd_His');

        $detalle_file = $temp_path . "DETALLE_DUCA_$fecha.csv";
        $resumen_file = $temp_path . "RESUMEN_DUCA_$fecha.csv";

        $this->generar_csv($data_final, $detalle_file);

        $resumen = [];

        foreach ($data_final as $info) {
            $duca = $info['duca'];

            if (!isset($resumen[$duca])) {
                $resumen[$duca] = [
                    'duca' => $duca,
                    'total_general' => 0,
                    'dai' => 0,
                    'sel' => 0,
                    'isv' => 0
                ];
            }

            $resumen[$duca]['total_general'] += $info['total_general'];
            $resumen[$duca]['dai'] += $info['dai'];
            $resumen[$duca]['sel'] += $info['sel'];
            $resumen[$duca]['isv'] += $info['isv'];
        }

        $output = fopen($resumen_file, 'w');
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        fputcsv($output, [
            '税单号（必填）|Transaction number (required)',
            '提单清关所属国家（必填）|Country (required)',
            '币种（申报金额与税费）（必填）|Currency (required)',
            '币种（税费）（必填）|Currency (required)',
            '服务商申报提单代缴税费总金额（必填）|Total payable amount (required)',
            '服务商申报提单代缴关税总金额（必填）|Total Duty (required)',
            '服务商申报提单代缴消费税金额（必填）|Total Excise Tax (required)',
            '服务商申报提单代缴增值税金额（必填）|Total GST (required)',
            '服务商申报提单代缴反倾销税金额（必填）|Total SIMA (required)',
            '服务商申报包裹代缴地方消费税金额（必填）|Total Provincial tax (required)',
            '币种（手续费）|Currency (Service Fee)',
            '服务商申报提单手续费（必填）|Service Fee (required)',
            '提单代缴其他费用金额（非必填）|Other Fees (optional)',
            '服务商提单代缴其他费用增值税金额（必填）|Other Tax related to service fee (required)'
        ]);

        foreach ($resumen as $row) {
            fputcsv($output, [
                $row['duca'],
                'Honduras',
                'HNL',
                'HNL',
                number_format($row['total_general'], 4, '.', ''),
                number_format($row['dai'], 4, '.', ''),
                number_format($row['sel'], 4, '.', ''),
                number_format($row['isv'], 4, '.', ''),
                '0',
                '0',
                'HNL',
                '0',
                '0',
                '0'
            ]);
        }

        fclose($output);

        if (!$this->Gestion_Model->sincronizar_impuestos_hn()) {
            log_message('error', 'No se pudieron sincronizar los impuestos antes de generar el ZIP DUCA.');
        }

        $zip_name = "REPORTES_DUCA_$fecha.zip";
        $zip_path = $temp_path . $zip_name;

        $zip = new ZipArchive();
        $zip->open($zip_path, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $zip->addFile($detalle_file, basename($detalle_file));
        $zip->addFile($resumen_file, basename($resumen_file));
        $zip->close();

        if (ob_get_length()) ob_end_clean();

        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $zip_name . '"');
        header('Content-Length: ' . filesize($zip_path));

        readfile($zip_path);

        unlink($detalle_file);
        unlink($resumen_file);
        unlink($zip_path);

        exit;
    }

    public function asignar_poblados()
    {
        $actualizados = $this->Gestion_Model->asignar_poblados();

        echo "<h3>Proceso finalizado</h3>";
        echo "Registros actualizados: ".$actualizados;
    }

    public function healthcheck()
    {
      //  echo "hola";
       $rsl = $this->Gestion_Model->healthcheck();

        
    }

    public function reporte_impuestos_gt()
    {
        $this->load->view('conversor/conversor_gt');
    }

    /*public function procesar_impuestos_gt()
    {
        ini_set('max_file_uploads', 5000);

        if (!isset($_FILES['xml']) && !isset($_FILES['pdf'])) {
            show_error("No se recibió ningún archivo.");
            return;
        }

        if(!isset($_POST["manifiesto"])){
            show_error(
                "No has ingresado numero de manifiesto"
            );
            return;
        }

        $manifiesto = $_POST["manifiesto"];
        $id_pre = $this->Gestion_Model->get_idpreclasificacion($manifiesto);
        
        if($id_pre == null || empty($id_pre)){
            show_error(
                "Manifiesto no existe"
            );
            return;
        }
        
        $id_preclasificacion = $id_pre->idpreclasificacion;

        //echo $id_preclasificacion;
        //die();


        $this->load->library('Reporte_impuestos_gt');

        $data_final = [];
        $total_xml = count($_FILES['xml']['name']);
        $total_pdf = count($_FILES['pdf']['name']);

        //echo "total_xml ".$total_xml." total_pdf ".$total_pdf;
        
        //var_dump($_FILES['pdf']['name']);
        
        //die();

        $pdfs = [];

        // Validaciones básicas de extensiones
        // Revisar XMLs
        for ($i = 0; $i < $total_xml; $i++) {
            $name = isset($_FILES['xml']['name'][$i]) ? $_FILES['xml']['name'][$i] : '';
            if ($name === '') continue;
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if ($ext !== 'xml') {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'msg' => "El archivo XML no tiene extensión .xml: {$name}"]);
                return;
            }
        }

        // Revisar PDFs
        for ($i = 0; $i < $total_pdf; $i++) {
            $name = isset($_FILES['pdf']['name'][$i]) ? $_FILES['pdf']['name'][$i] : '';
            if ($name === '') continue;
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if ($ext !== 'pdf') {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'msg' => "El archivo PDF no tiene extensión .pdf: {$name}"]);
                return;
            }
        }

        // Validar que la cantidad de XML coincida con la de PDF
        if ($total_xml !== $total_pdf) {
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'error',
                'msg' => 'La cantidad de archivos XML debe coincidir con la cantidad de archivos PDF.'
            ]);
            return;
        }
        foreach ($_FILES['pdf']['tmp_name'] as $i => $tmpFile) {
            $nombreArchivo = $_FILES['pdf']['name'][$i];

            $datos = $this->reporte_impuestos_gt->procesar($tmpFile);

            if (!empty($datos['orden'])) {
                $pdfs[$datos['orden']] = $datos;
                $pdfs[$datos['orden']]['archivo'] = $nombreArchivo;
            }
        }

        $xmls = [];

        foreach ($_FILES['xml']['tmp_name'] as $i => $tmpFile) {
            $nombreArchivo = $_FILES['xml']['name'][$i];

            if (!file_exists($tmpFile)) continue;

            $xmlContent = simplexml_load_file($tmpFile);

            if (!$xmlContent) continue;

            // asegurar nodo correcto
            $declaracion = $xmlContent->declaracion ?? $xmlContent;

            // OEA
            $noDeclaracion = (string) $declaracion->noDeclaracion;
            $oea = preg_replace('/[^A-Z0-9]/', '', $noDeclaracion);

            // FECHA
            $fecha = (string) $declaracion->fechaValidacion;

            // TIPO CAMBIO
            $tipoCambio = (string) $declaracion->tipoDeCambio;

            // fracciones
            $items = [];

            if (isset($declaracion->fraccion)) {

                foreach ($declaracion->fraccion as $f) {
                    $desc = (string) $f->descripcionMercancias;

                    //preg_match('/(RR\d+XX)/', $desc, $m);
                    preg_match('/\b(RR\d+XX|FD\d+)\b/', $desc, $m);


                    $tracking = $m[1] ?? '';

                    $items[] = [
                        'oea' => $noDeclaracion,
                        'tracking' => $tracking,
                        'descripcion' => trim(str_replace($tracking, '', $desc)),
                        'cif' => (float) $f->valorEnQuetzales,
                        'dai' => (float) $f->impuestoGuia
                    ];
                }
            }

            $xmls[$oea] = [
                'oea' => $noDeclaracion,
                'fecha' => $fecha,
                'tipo_cambio' => $tipoCambio,
                'items' => $items
            ];
        }


        $data_final = [];

        foreach ($xmls as $oeaKey => $xml) {
            // validar que exista PDF para ese OEA
            if (!isset($pdfs[$oeaKey])) {
                continue;
            }


            //cif es el campo value de manifiesto, ya es enviado por temu
            //cif = fob + flete + seguro;
            //0432 dai 15%
            //iva = (cif + dai) * 0.12;
            //suma_impuestos = iva + dai;
            

            foreach ($xml['items'] as $item) {

                $iva = (($item['dai'] + $item['cif']) * 0.12);

                $data_final[] = [
                    // ITEM DATA
                    'tracking'     => $item['tracking'],
                    'cif'          => $item['cif'],
                    'dai'          => $item['dai'],
                    'iva' => $iva, 
                    't_impuestos'      => $iva + $item['dai'] ,

                    // OEA DATA (XML)
                    'oea'          => $xml['oea'],
                    'fecha'        => $xml['fecha'],
                    'tipo_cambio'  => $xml['tipo_cambio'],

                    // PDF DATA
                    'formulario'   => $pdfs[$oeaKey]['formulario'],
                    'acceso'       => $pdfs[$oeaKey]['acceso'],
                ];
            }
        }

        $this->generar_csv_detalle($data_final,$id_preclasificacion);
        exit;
    }*/


    public function procesar_impuestos_gt()
    {
        if (!isset($_FILES['xml']) && !isset($_FILES['pdf'])) {
            //show_error("No se recibió ningún archivo.");
            //return;
            http_response_code(400);
            exit("No se recibió ningún archivo.");
        }

        $this->load->library('Reporte_impuestos_gt');

        $data_final = [];
        $total_xml = count($_FILES['xml']['name']);
        $total_pdf = count($_FILES['pdf']['name']);

        $pdfs = [];

        $xmlSinPdf = [];
        $pdfSinXml = [];

        // Validaciones básicas de extensiones
        // Revisar XMLs
        for ($i = 0; $i < $total_xml; $i++) {
            $name = isset($_FILES['xml']['name'][$i]) ? $_FILES['xml']['name'][$i] : '';
            if ($name === '') continue;
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if ($ext !== 'xml') {
                
                http_response_code(400);
                exit("El archivo XML no tiene extensión .xml: {$name}");
            }
        }

        // Revisar PDFs
        for ($i = 0; $i < $total_pdf; $i++) {
            $name = isset($_FILES['pdf']['name'][$i]) ? $_FILES['pdf']['name'][$i] : '';
            if ($name === '') continue;
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if ($ext !== 'pdf') {
                

                http_response_code(400);
                exit("El archivo PDF no tiene extensión .pdf: {$name}");
            }
        }

        // Validar que la cantidad de XML coincida con la de PDF
        if ($total_xml !== $total_pdf) {
            

            http_response_code(400);
            exit("La cantidad de archivos XML debe coincidir con la cantidad de archivos PDF.");
        }
        foreach ($_FILES['pdf']['tmp_name'] as $i => $tmpFile) {
            $nombreArchivo = $_FILES['pdf']['name'][$i];

            $datos = $this->reporte_impuestos_gt->procesar($tmpFile);

            if (!empty($datos['orden'])) {
                $pdfs[$datos['orden']] = $datos;
                $pdfs[$datos['orden']]['archivo'] = $nombreArchivo;
            }
        }

        $xmls = [];

        foreach ($_FILES['xml']['tmp_name'] as $i => $tmpFile) {
            $nombreArchivo = $_FILES['xml']['name'][$i];

            if (!file_exists($tmpFile)) continue;

            $xmlContent = simplexml_load_file($tmpFile);

            if (!$xmlContent) continue;

            // asegurar nodo correcto
            $declaracion = $xmlContent->declaracion ?? $xmlContent;

            // OEA
            $noDeclaracion = (string) $declaracion->noDeclaracion;
            $oea = preg_replace('/[^A-Z0-9]/', '', $noDeclaracion);

            // FECHA
            $fecha_sin_formato = (string) $declaracion->fechaValidacion;

            $fecha = DateTime::createFromFormat('j/n/Y', $fecha_sin_formato);

            $fecha = $fecha ? $fecha->format('Y/m/d') : '';

            // TIPO CAMBIO
            $tipoCambio = (string) $declaracion->tipoDeCambio;

            // fracciones
            $items = [];

            if (isset($declaracion->fraccion)) {

                foreach ($declaracion->fraccion as $f) {
                    $desc = (string) $f->descripcionMercancias;

                    preg_match('/\b(RR\d+XX|FD\d+)\b/', $desc, $m);

                    $tracking = $m[1] ?? '';

                    $items[] = [
                        'oea' => $noDeclaracion,
                        'tracking' => $tracking,
                        'descripcion' => trim(str_replace($tracking, '', $desc)),
                        'cif' => (float) $f->valorEnQuetzales,
                        'dai' => (float) $f->impuestoGuia
                    ];
                }
            }

            

            $xmls[$oea] = [
                'archivo'      => $nombreArchivo,
                'oea'          => $noDeclaracion,
                'fecha'        => $fecha,
                'tipo_cambio'  => $tipoCambio,
                'items'        => $items
            ];
        }


        $data_final = [];

        

        foreach ($xmls as $oeaKey => $xml) {

            

            if (!isset($pdfs[$oeaKey])) {
                $xmlSinPdf[] = $xml['archivo'];
                continue;
            }

            foreach ($xml['items'] as $item) {
                $bg_data = $this->Gestion_Model->obtener_tracking_number_GT($item['tracking']);
                $bg = $bg_data->tracking_number;
                $fob = $bg_data->fob_gt;
                $flete = $bg_data->flete_gt;
                $seguro = $bg_data->seguro_gt;

                $cif_dolares = $fob + $flete + $seguro;

                $iva = ($item['cif'] + $item['dai']) * 0.12;
                $impuestos = $item['dai'] + $iva;


                $data_final[] = [
                    'tracking'     => $item['tracking'],
                    'bg'           => $bg,
                    'cif_dolares'          => $cif_dolares,
                    'dai'          => $item['dai'],
                    'impuestos'      => $impuestos,

                    'oea'          => $xml['oea'],
                    'fecha'        => $xml['fecha'],
                    'tipo_cambio'  => $xml['tipo_cambio'],

                    'formulario'   => $pdfs[$oeaKey]['formulario'],
                    'acceso'       => $pdfs[$oeaKey]['acceso'],
                    'fob'          => $fob,
                    'flete'        => $flete,
                    'seguro'       => $seguro,
                    'iva' => $iva
                    
                ];

                $tracking = $item['tracking'];
                $this->Gestion_Model->actualizar_impuestos_gt($tracking, $impuestos);
            }
        }

        foreach ($pdfs as $oeaKey => $pdf) {
            if (!isset($xmls[$oeaKey])) {
                $pdfSinXml[] = $pdf['archivo'];
            }
        }

        
        
        $this->generar_csv_detalle($data_final, $xmlSinPdf, $pdfSinXml);
        exit;

        //$this->generar_csv_detalle($data_final);
        //exit;

    }


    public function generar_copy_oea()
    {
        if (!isset($_FILES['xml'])) {
            //show_error("No se recibió ningún archivo.");
            //return;
            http_response_code(400);
            exit("No se recibió ningún archivo.");
        }

        

        $data_final = [];
        $total_xml = count($_FILES['xml']['name']);
        

        $pdfs = [];

        $xmlSinPdf = [];
        $pdfSinXml = [];

        // Validaciones básicas de extensiones
        // Revisar XMLs
        for ($i = 0; $i < $total_xml; $i++) {
            $name = isset($_FILES['xml']['name'][$i]) ? $_FILES['xml']['name'][$i] : '';
            if ($name === '') continue;
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if ($ext !== 'xml') {

                http_response_code(400);
                exit("El archivo XML no tiene extensión .xml: {$name}");
            }
        }

        
        
       

        $xmls = [];

        foreach ($_FILES['xml']['tmp_name'] as $i => $tmpFile) {
            $nombreArchivo = $_FILES['xml']['name'][$i];

            if (!file_exists($tmpFile)) continue;

            $xmlContent = simplexml_load_file($tmpFile);

            //var_dump($xmlContent);
            //die();

            if (!$xmlContent) continue;

            // asegurar nodo correcto
            $declaracion = $xmlContent->declaracion ?? $xmlContent;

            // OEA
            $noDeclaracion = (string) $declaracion->noDeclaracion;
            $oea = preg_replace('/[^A-Z0-9]/', '', $noDeclaracion);

            // FECHA
            $fecha_sin_formato = (string) $declaracion->fechaValidacion;

            $fecha = DateTime::createFromFormat('j/n/Y', $fecha_sin_formato);

            $fecha = $fecha ? $fecha->format('Y/m/d') : '';

            // TIPO CAMBIO
            $tipoCambio = (string) $declaracion->tipoDeCambio;

            // fracciones
            $items = [];

            if (isset($declaracion->fraccion)) {

                foreach ($declaracion->fraccion as $f) {
                    $desc = (string) $f->descripcionMercancias;

                    preg_match('/\b(RR\d+XX|FD\d+)\b/', $desc, $m);

                    $tracking = $m[1] ?? '';

                    $items[] = [
                        'oea' => $noDeclaracion,
                        'tracking' => $tracking,
                        'descripcion' => trim(str_replace($tracking, '', $desc)),
                        'cif' => (float) $f->valorEnQuetzales,
                        'dai' => (float) $f->impuestoGuia
                    ];
                }
            }


            var_dump($items);
            die();

            $xmls[$oea] = [
                'archivo'      => $nombreArchivo,
                'oea'          => $noDeclaracion,
                'fecha'        => $fecha,
                'tipo_cambio'  => $tipoCambio,
                'items'        => $items
            ];
        }


        $data_final = [];



        foreach ($xmls as $oeaKey => $xml) {
            if (!isset($pdfs[$oeaKey])) {
                $xmlSinPdf[] = $xml['archivo'];
                continue;
            }

            foreach ($xml['items'] as $item) {
                $data_final[] = [
                    'tracking'     => $item['tracking'],
                    'cif'          => $item['cif'],
                    'dai'          => $item['dai'],
                    'cif_dai'      => $item['cif'] + $item['dai'],

                    'oea'          => $xml['oea'],
                    'fecha'        => $xml['fecha'],
                    'tipo_cambio'  => $xml['tipo_cambio']
                ];

                $impuestos = $item['cif'] + $item['dai'];
                $tracking = $item['tracking'];
                
            }
        }

        

       

    }

    private function generar_csv_detalle($data_final, $xmlSinPdf = [], $pdfSinXml = [])
    {
        $filename = 'CONSOLIDADO_GT_' . date('Ymd_His') . '.csv';

        header('X-XML-Sin-PDF: ' . json_encode($xmlSinPdf));
        header('X-PDF-Sin-XML: ' . json_encode($pdfSinXml));

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        $output = fopen('php://output', 'w');

        // UTF-8 BOM para Excel
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // ENCABEZADOS
        fputcsv($output, [
            '运单号（必填）|Tracking number (required)', //Tracking No. en la OEA
            'Platform包裹号（必填）|Platform Package Number (required)',
            '包裹清关所属国家（必填）|Country (required)',
            '包裹清关所在省（非必填）|Province of customs clearance (optional)',
            '包裹收货所在省（非必填）|Province of receipt (optional)',
            '币种（申报金额与税费）（必填）|Currency (required)',
            '包裹的申报价值金额（必填）|Value For Duty (required)', //CIF en USD
            '服务商申报包裹代缴税费总金额（必填）|Total payable amount (required)', //Suma CIF + DAI
            '服务商申报包裹代缴关税总金额（必填）|Total Duty (required)', //DAI en GTQ
            '服务商申报包裹代缴消费税金额（必填）|Total Excise Tax (required)',
            '服务商申报包裹代缴增值税金额（必填）|Total GST (required)',
            '服务商申报包裹代缴反倾销税金额（必填）|Total SIMA (required)',
            '服务商申报包裹代缴地方消费税金额（必填）|Total Provincial tax (required)',
            '币种（手续费）（非必填）|Currency（Service Fee)(optional)',
            '服务商申报包裹手续费（非必填）|Service Fee (optional)',
            '提单号（非必填）|AWB Number (optional)',
            '通用匹配单号（必填）|Transaction number (required)', //Número de Declaración
            'FOB价（必填）|FOB Price (required)',
            '运费（必填）|Freight (required)',
            '保险费（必填）|Insurance(required)',
            '申告日期（必填）|Date of Clearance(required)', //Fecha en OEA
            '汇率（必填）|Exchange Rate (required)', //Tasa de Cambio
            '表单编号（非必填）|Formulario (optional)', //Formulario
            '访问号码（非必填）|Acceso (optional)' //Acceso
        ]);

        foreach ($data_final as $item) {

            $cif = (float) $item['cif_dolares'];
            $dai = (float) $item['dai'];
            $fob = (float) $item['fob'];
            $flete = (float) $item['flete'];
            $seguro = (float) $item['seguro'];
            $iva = (float)$item['iva'];
            $impuestos = (float)$item['impuestos'];
            $oea = str_replace(' ', '', $item['oea']);

            fputcsv($output, [
                $item['tracking'],
                $item['bg'],
                'Guatemala',
                '',
                '',
                'GTQ',
                number_format($cif, 2, '.', ''),
                number_format($impuestos, 2, '.', ''),
                number_format($dai, 2, '.', ''),
                '0',
                number_format($iva, 2, '.', ''),
                '0',
                '0',
                '',
                '',
                '',
                $oea,
                $fob,
                $flete,
                $seguro,
                $item['fecha'],
                $item['tipo_cambio'],

                $item['formulario'] ?? '',
                $item['acceso'] ?? ''
            ]);
        }

        fclose($output);
        exit();
    }

    public function reporte_impuestos_sv()
    {
        $this->load->view('conversor/impuestos_sv');
    }

    public function convertir_xml_sv()
    {
        if (!isset($_FILES['xml'])) {
            show_error("No se recibió ningún archivo.");
            return;
        }

        $datos = [];
        foreach ($_FILES['xml']['tmp_name'] as $i => $tmpFile) {
            if (!file_exists($tmpFile)) continue;

            $xml = simplexml_load_file($tmpFile);
            $referencia = (string)$xml->Declarant->Reference->Number;

            foreach ($xml->Item as $item) {
                $tracking = (string)$item->Previous_doc->Summary_declaration;
                $fob = (float)$item->Tarification->Item_price;
                $flete = (float)$item->Valuation_item->item_external_freight->Amount_national_currency;
                $seguro = (float)$item->Valuation_item->item_insurance->Amount_national_currency;
                $cif = (float)$item->Valuation_item->Total_CIF_itm;


                $data_tracking = $this->Gestion_Model->obtener_tracking_bg_sv($tracking, $referencia);
                $id_manifiesto = $data_tracking->id_manifiesto;

                //echo "<pre>";
                //print_r($data_tracking);
                //echo "</pre>";

                $master_ch = $this->Gestion_Model->obtener_master_ch_sv($id_manifiesto);

                $dai = 0;
                $iva = 0;

                foreach ($item->Taxation->Taxation_line as $tax) {
                    $codigo = trim((string)$tax->Duty_tax_code);

                    if ($codigo == 'DAI') {
                        $dai = (float)$tax->Duty_tax_amount;
                    }

                    if ($codigo == 'IVA') {
                        $iva = (float)$tax->Duty_tax_amount;
                    }
                }

                $id            = $data_tracking->id;
                $impuestos = $dai + $iva;

                $this->Gestion_Model->actualizar_impuestos_sv($id, $impuestos);


                $datos[] = [
                    'referencia' => $referencia,
                    'tracking' => $tracking,
                    'fob' => $fob,
                    'flete' => $flete,
                    'seguro' => $seguro,
                    'dai' => $dai,
                    'iva' => $iva,
                    'impuestos' => $dai + $iva,
                    'value' => $cif ?? 0,
                    'bg' => $data_tracking->tracking_number ?? '',
                    'master_ch' => $master_ch->manifiesto_tempo ?? ''
                ];
            }
        }

        //echo "<pre>";
        //print_r($datos);
        //echo "</pre>";

        $this->generar_reporte_impuestos_sv($datos);
        exit;
    }

    public function generar_reporte_impuestos_sv($datos)
    {
        $filename = $datos[0]['master_ch'] . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        $output = fopen('php://output', 'w');

        // UTF-8 BOM para Excel
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        fputcsv($output, [
            'Platform包裹号（必填）|Platform Package Number (required)',
            '包裹清关所属国家（必填）|Country (required)',
            '包裹清关所在省（非必填）|Province of customs clearance (optional)',
            '包裹收货所在省（非必填）|Province of receipt (optional)',
            '币种（申报金额与税费）（必填）|Currency (required)',
            '包裹的申报价值金额（必填）|Value For Duty (required)',
            '服务商申报包裹代缴税费总金额（必填）|Total payable amount (required)',
            '服务商申报包裹代缴关税总金额（必填）|Total Duty (required)',
            '服务商申报包裹代缴消费税金额（必填）|Total Excise Tax (required)',
            '服务商申报包裹代缴增值税金额（必填）|Total GST (required)',
            '服务商申报包裹代缴反倾销税金额（必填）|Total SIMA (required)',
            '服务商申报包裹代缴其他税金额（必填）|Total Other Tax (required)',
            '币种（手续费）|Currency（Service Fee)',
            '服务商申报包裹手续费（必填）|Service Fee (required)',
            '服务商单号（必填）|Logistics number(required)',
            '提单号（非必填）|AWB Number(optional)',
            'FOB价（必填）|FOB Price (required)',
            '运费（必填）|Freight (required)',
            '保险费（必填）|Insurance(required)'

        ]);

        foreach ($datos as $item) {
            $dif = $item['value'] - ($item['fob'] + $item['flete'] + $item['seguro']);
            $item['flete'] = $item['flete'] + $dif;

            fputcsv($output, [

                '="' . $item['bg'] . '"',
                'El Salvador',
                '',
                '',
                'USD',
                number_format($item['value'], 2, '.', ''),
                number_format($item['impuestos'], 2, '.', ''),
                '0',
                '0',
                number_format($item['iva'], 2, '.', ''),
                '0',
                '0',
                'USD',
                '0',
                '="' . $item['tracking'] . '"',
                $item['master_ch'],
                number_format($item['fob'], 2, '.', ''),
                number_format($item['flete'], 2, '.', ''),
                number_format($item['seguro'], 2, '.', '')
            ]);
        }
        fclose($output);
        exit();
    }

    public function renombrar_ducas()
    {
        $this->load->view('conversor/renombrar_ducas');
    }

    public function zip_ducas_renombradas()
    {
        $nombreZip = trim($this->input->post('nombre_zip'));

        if ($nombreZip == '') {
            http_response_code(400);
            exit('Debe indicar un nombre para el ZIP.');
        }

        if (
            !isset($_FILES['ducas']) ||
            empty($_FILES['ducas']['name']) ||
            count($_FILES['ducas']['name']) == 0
        ) {
            http_response_code(400);
            exit('No se recibieron archivos.');
        }

        $noRenombrados = [];
        $duplicados = [];
        $ducasProcesadas = [];

        $zip = new ZipArchive();

        $zipTemporal = tempnam(sys_get_temp_dir(), 'DUCAS_') . '.zip';

        if ($zip->open($zipTemporal, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            http_response_code(500);
            exit('No fue posible crear el archivo ZIP.');
        }

        $cantidadAgregados = 0;

        $totalArchivos = count($_FILES['ducas']['name']);

        for ($i = 0; $i < $totalArchivos; $i++) {
            $nombreOriginal = $_FILES['ducas']['name'][$i];
            $rutaTemporal = $_FILES['ducas']['tmp_name'][$i];

            if (!is_uploaded_file($rutaTemporal)) {
                continue;
            }

            //---------------------------------------------
            // Quitamos la extensión
            //---------------------------------------------

            $nombreSinExtension = pathinfo($nombreOriginal, PATHINFO_FILENAME);

            if (!preg_match('/^DUCA(?:SIMPLIFICADA)?/i', $nombreSinExtension)){
                $noRenombrados[] = $nombreOriginal;
                continue;
            }

            //---------------------------------------------
            // Quitamos "(1)", "(2)", "(15)" al final
            //---------------------------------------------

            $nombreSinExtension = preg_replace('/\s*\(\d+\)\s*$/', '', $nombreSinExtension);

            //---------------------------------------------
            // Buscamos todos los grupos de números
            //---------------------------------------------

            preg_match_all('/\d+/', $nombreSinExtension, $coincidencias);

            if (empty($coincidencias[0])) {
                $noRenombrados[] = $nombreOriginal;
                continue;
            }

            //---------------------------------------------
            // Unimos todos los números encontrados
            //---------------------------------------------

            $numeroDuca = implode('', $coincidencias[0]);

            $nuevoNombre = 'DUCA_' . $numeroDuca . '.pdf';

            //---------------------------------------------
            // Evitamos duplicados
            //---------------------------------------------

            if (isset($ducasProcesadas[$nuevoNombre])) {
                $duplicados[] = $nombreOriginal;
                continue;
            }

            $ducasProcesadas[$nuevoNombre] = true;

            //---------------------------------------------
            // Agregamos al ZIP
            //---------------------------------------------

            $zip->addFile($rutaTemporal, $nuevoNombre);

            $cantidadAgregados++;
        }

        $zip->close();

        //---------------------------------------------
        // Si no hubo ningún PDF válido
        //---------------------------------------------

        if ($cantidadAgregados == 0) {
            @unlink($zipTemporal);

            http_response_code(400);
            exit('No se encontró ninguna DUCA válida para generar el ZIP.');
        }

        //---------------------------------------------
        // Headers personalizados
        //---------------------------------------------

        header('X-No-Renombrados: ' . json_encode($noRenombrados));
        header('X-Duplicados: ' . json_encode($duplicados));

        //---------------------------------------------
        // Descarga
        //---------------------------------------------

        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $nombreZip . '_DUCA.zip"');
        header('Content-Length: ' . filesize($zipTemporal));

        readfile($zipTemporal);

        @unlink($zipTemporal);

        exit;
    }

    public function conciliacion_pago_temu()
    {
        $empresas = [112, 113, 114];
        $data['empresa'] = $this->Gestion_Model->obtener_empresa_temu($empresas);
        $this->load->view('conversor/conciliacion_pago_temu', $data);
    }

    public function cargar_detalle_impuesto_temu()
    {
        $empresas = [112, 113, 114];
        $data['empresa'] = $this->Gestion_Model->obtener_empresa_temu($empresas);
        //var_dump($data);
        $this->load->view('conversor/form_detalle_temu', $data);
    }

    public function detalle_impuestos_temu()
    {
        if (!isset($_FILES['detalle_temu'])) {
            echo json_encode(['status'  => 'false', 'mensaje' => 'No se recibió ningún archivo.']);
            return;
        }

        $id_empresa = $this->input->post('empresas');

        if (empty($id_empresa)) {
            echo json_encode(['status'  => 'false', 'mensaje' => 'Debe seleccionar una empresa.']);
            return;
        }

        $errorLevel = error_reporting();

        // Ocultar warnings/deprecated de PHPExcel
        error_reporting($errorLevel & ~E_DEPRECATED & ~E_USER_DEPRECATED);

        try {

            $this->load->library('PHPEXCEL/PHPExcel.php');

            $actualizados = 0;
            $no_encontrados = 0;
            $filas_invalidas = 0;
            $archivos_procesados = 0;

            $no_encontrados_lista = [];

            foreach ($_FILES['detalle_temu']['tmp_name'] as $indice => $tmpFile) {
                if (!is_uploaded_file($tmpFile)) {
                    continue;
                }

                $archivos_procesados++;
                $objPHPExcel = PHPExcel_IOFactory::load($tmpFile);
                $sheet = $objPHPExcel->getActiveSheet();
                $datos = $sheet->toArray(null, true, true, true);

                for ($i = 2; $i <= count($datos); $i++) {

                    // Columna B = impuesto
                    $pago_imp_temu = $datos[$i]['B'] ?? null;

                    // Columna C = package_number
                    $package_number = $datos[$i]['C'] ?? null;

                    // Limpiar valores
                    $pago_imp_temu = trim((string) $pago_imp_temu);
                    $package_number = trim((string) $package_number);

                    // Si no hay package_number, ignoramos la fila
                    if ($package_number === '') {
                        $filas_invalidas++;
                        continue;
                    }

                    // Si no hay impuesto, ignoramos la fila
                    if ($pago_imp_temu === '' || !is_numeric($pago_imp_temu)) {
                        $filas_invalidas++;
                        continue;
                    }

                    // Convertir a decimal
                    $pago_imp_temu = number_format((float) $pago_imp_temu, 4, '.', '');

                    // Buscar y actualizar manifiesto
                    $resultado = $this->Gestion_Model->actualizar_pago_imp_temu($package_number, $pago_imp_temu);

                    if ($resultado > 0) {
                        $actualizados++;
                    } else {

                        // Puede significar que no encontró el tracking
                        // o que el valor ya era exactamente el mismo
                        $existe = $this->Gestion_Model->existe_tracking_number($package_number);

                        if ($existe) {

                            // Ya tenía el mismo valor
                            $actualizados++;
                        } else {

                            $no_encontrados++;

                            $no_encontrados_lista[] = $package_number;
                        }
                    }
                }
            }

            echo json_encode([
                'status'              => true,
                'mensaje'             => 'Archivos procesados correctamente.',
                'archivos_procesados' => $archivos_procesados,
                'actualizados'        => $actualizados,
                'no_encontrados'      => $no_encontrados,
                'filas_invalidas'     => $filas_invalidas,
                'no_encontrados_lista' => $no_encontrados_lista
            ]);
        } catch (Exception $e) {

            echo json_encode([
                'status'  => 'false',
                'mensaje' => $e->getMessage()
            ]);
        } finally {

            // Restaurar configuración original
            error_reporting($errorLevel);
        }
    }

    public function buscar_detalle_impuestos_temu()
    {
        /*ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);*/

        $id_empresa = $this->input->post('empresa');
        $estado = $this->input->post('estado');
        $master = $this->input->post('master');
        $referencia = $this->input->post('referencia');

        $detalle = $this->Gestion_Model->obtener_detalle_impuestos_temu($id_empresa, $estado, $master, $referencia);
        $data['detalle'] = $detalle;

        $this->load->view('conversor/detalle_impuestos_temu', $data);
    }

    public function dashboard_detalle_impuestos_temu()
    {
        $id_empresa = $this->input->post('empresa');
        $estado = $this->input->post('estado');
        $master = $this->input->post('master');
        $referencia = $this->input->post('referencia');

        $dashboard = $this->Gestion_Model->obtener_dashboard_impuestos_temu($id_empresa, $estado, $master, $referencia);

        $data['dash'] = $dashboard;

        $this->load->view('conversor/dashboard_temu', $data);
    }


    /*private function generar_csv_detalle($data_final,$id_preclasificacion)
    {

        ini_set("display_errors",1);
        $filename = 'CONSOLIDADO_GT_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        $output = fopen('php://output', 'w');

        // UTF-8 BOM para Excel
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // ENCABEZADOS
        fputcsv($output, [
            '运单号（必填）|Tracking number (required)', //Tracking No. en la OEA
            'Platform包裹号（必填）|Platform Package Number (required)',
            '包裹清关所属国家（必填）|Country (required)',
            '包裹清关所在省（非必填）|Province of customs clearance (optional)',
            '包裹收货所在省（非必填）|Province of receipt (optional)',
            '币种（申报金额与税费）（必填）|Currency (required)',
            '包裹的申报价值金额（必填）|Value For Duty (required)', //CIF en USD
            '服务商申报包裹代缴税费总金额（必填）|Total payable amount (required)', //Suma CIF + DAI
            '服务商申报包裹代缴关税总金额（必填）|Total Duty (required)', //DAI en GTQ
            '服务商申报包裹代缴消费税金额（必填）|Total Excise Tax (required)',
            '服务商申报包裹代缴增值税金额（必填）|Total GST (required)',
            '服务商申报包裹代缴反倾销税金额（必填）|Total SIMA (required)',
            '服务商申报包裹代缴地方消费税金额（必填）|Total Provincial tax (required)',
            '币种（手续费）（非必填）|Currency（Service Fee)(optional)',
            '服务商申报包裹手续费（非必填）|Service Fee (optional)',
            '提单号（非必填）|AWB Number (optional)',
            '通用匹配单号（必填）|Transaction number (required)', //Número de Declaración
            'FOB价（必填）|FOB Price (required)',
            '运费（必填）|Freight (required)',
            '保险费（必填）|Insurance(required)',
            '申告日期（必填）|Date of Clearance(required)', //Fecha en OEA
            '汇率（必填）|Exchange Rate (required)', //Tasa de Cambio
            '表单编号（非必填）|Formulario (optional)', //Formulario
            '访问号码（非必填）|Acceso (optional)' //Acceso
        ]);

        foreach ($data_final as $item) {

            $cif = (float) $item['cif'];
            $dai = (float) $item['dai'];
            $t_impuestos = (float) $item['t_impuestos'];
            $iva = (float) $item['iva'];


            //echo $item["tracking"]." ".$id_preclasificacion;


            $data_gt = $this->Gestion_Model->data_gt_x_tracking($item["tracking"],$id_preclasificacion);

            //$fecha = DateTime::createFromFormat('Y/n/j', $item['fecha']);
            //$item['fecha'] = $fecha->format('Y/m/d');

            //$item['fecha'] = DateTime::createFromFormat('Y/n/j', $item['fecha'])->format('Y/m/d');

            $fecha = DateTime::createFromFormat('d/m/Y', $item['fecha']);
            $item['fecha'] = $fecha->format('Y/m/d');
          
            fputcsv($output, [
                $item['tracking'],
                $data_gt->tracking_number,
                'Guatemala',
                '',
                '',
                'GTQ',
                number_format($data_gt->value, 2, '.', ''),
                number_format($t_impuestos, 2, '.', ''),
                number_format($dai, 2, '.', ''),
                '0',
                number_format($iva, 2, '.', ''),
                '0',
                '0',
                '',
                '',
                '',
                $item['oea'],
                $data_gt->fob_gt,
                $data_gt->flete_gt,
                $data_gt->seguro_gt,
                $item['fecha'],
                $item['tipo_cambio'],
                $item['formulario'] ?? '',
                $item['acceso'] ?? ''
            ]);
        }

        fclose($output);
        exit();
    }*/

}
