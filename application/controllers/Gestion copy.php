<?php
// File: application/controllers/Productos.php
class Gestion extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Gestion_Model');
        $this->load->model('WhModel');
        $this->load->model('CortesModel');
        $this->load->view("verifica");
        $this->load->model("WarehouseModel");
        // $this->load->library('TokenMasterLib');
        $this->load->library('EmpresaLib');
        // $this->load->library('TokenLib');
        $this->load->library('CrearGuia');
    }

    public function index()
    {
        $this->load->view('gestion/form');
    }

    public function buscar_bag_number()
    {

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
            $corte = $this->CortesModel->obtener_activo();
            $strfecha = $corte->hora_corte;
        }

        if ($_SESSION['pais'] == 2) {
            $strfecha = date("Y-m-d") . '23:05:05';
        }


        $fecha_recoleccion = $strfecha;
        // echo $token_lmd;
        foreach ($request as $row) {
            // si el valor de la recolecta es ==0 ejecuta
            if ($row->pickup_number == 0) {

                if ($row->tipo_servicio == "SER") {
                    $montoCCE = 0;
                }
                if ($row->tipo_servicio == "CCE") {
                    $montoCCE = $row->cobro_final;
                }

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
                            "direccion"      => $row->consignee_address1 . " " . $row->consignee_address2 . " " . $row->consignee_address3,
                            "tipo_servicio"  => $row->tipo_servicio,
                            "monto_cce"      => $montoCCE,
                            "agencia_destino" =>  $row->agencia_destino,
                            "con_seguro"     => "0",
                            "con_liquidacion_documento" => "0",
                            "departamento_id"   =>  $row->departamento_id,
                            "municipio_id"      =>  $row->municipio_id,
                            "detalle" => array(
                                array(
                                    "guia"          => $row->tracking_number,
                                    "peso"          => $row->gweight,
                                    "contenido"     => $row->commodity,
                                    "unidad_medida" => "LB",
                                )
                            )
                        )
                    )
                );


                // Llamamos a la librería para crear la guía
                $response = $this->crearguia->crearGuia($token_lmd, $datos);


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
                } else {
                    // Si la respuesta no contiene los datos esperados, mostrar un mensaje de error
                    // echo "Error: No se pudieron obtener los datos de la respuesta.";
                    echo "Error: No se pudieron obtener los datos de la respuesta: " . $response;
                    //  echo "<br>Respuesta completa: " . print_r($response_data, true);  // Mostrar la respuesta completa para depuración
                }
            }
        }

        //datos para obtener token
        //  exit;
        $data['lista'] = $this->Gestion_Model->buscar_trackings($master_id, $bag_number);
        $this->load->view('gestion/cuerpo', $data);
    }

    public function push_events_apg()
    {
       // enviamos evento fallidos
       // exit;
        $req = $this->Gestion_Model->get_events_error();
        //  var_dump($req);
        $cantidad_filas = count($req);

      //  echo "La cantidad de filas es: " . $cantidad_filas;
       // exit;
        foreach ($req as $fila) {

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

            //   echo $response;
            $this->Gestion_Model->actualizar_respuesta($response, $fila->tracking);
            sleep(1);
        }
        //  echo $row->id."\n";

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
                $result= $data->objectResult->response;
               if ($result=="OK"){
                //actualizar valor aqui
                $this->Gestion_Model->ok_events($row->tracking,$row->id_estatus);
                break;
               }
            }


          
        }
    }
}
