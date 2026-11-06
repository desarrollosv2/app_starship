<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods:GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, x-xsrf-token');

defined('BASEPATH') or exit('No direct script access allowed');
//include getcwd() . "/libraries/RestController.php";

require(APPPATH . '/libraries/REST_Controller.php');
include getcwd() . "/application/libraries/conversor/facturaelectronica.php";
include getcwd() . "/application/libraries/conversor/generarpartidas.php";
//use Restserver\Libraries\RestController;
class Api extends REST_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->model('Casillero_model');
		$this->load->model('Warehouse_model');
		$this->load->model('Usuario_model');
		$this->load->model('Manifiesto_model');
		$this->load->model('Cotizador_model');
		$this->load->model('DTES_Model');
		$this->load->model('Exportacion_Model');
		$this->load->model('Ventas_Model');
		$this->load->model('Importacion_Model');
		$this->load->model('Entrega_model');
		$this->load->model('Conf_model');
	}

	public function casillero_get()
	{
		$id = $this->uri->segment(3);

		if (!isset($id)) {
			$respuesta = array(
				'err' => true,
				'mensaje' => 'Es necesario el número de casillero'
			);
			$this->response($respuesta, REST_Controller::HTTP_BAD_REQUEST);
			return;
		}

		if ($id == " ") {
		} else {
			$id = str_replace("%20", " ", $id);
		}
		$casillero = $this->Casillero_model->get_casillero($id);
		if (($casillero)) {
			$respuesta = array(
				'err' => false,
				'mensaje' => 'Registro cargado correctamente',
				'cliente' => $casillero
			);
			$this->response($respuesta);
		} else {
			$respuesta = array(
				'err' => true,
				'mensaje' => 'El casillero con el número ' . $id . ', no existe ',
				'casillero' => null
			);
			$this->response($respuesta, REST_Controller::HTTP_NOT_FOUND);
		}
	}


	public function warehouse_get()
	{
		$id = $this->uri->segment(3);

		if (!isset($id)) {
			$respuesta = array(
				'err' => true,
				'mensaje' => 'Es necesario el número de warehouse'
			);
			$this->response($respuesta, REST_Controller::HTTP_BAD_REQUEST);
			return;
		}

		if ($id == " ") {
		} else {
			$id = str_replace("%20", " ", $id);
		}
		$warehouse = $this->Warehouse_model->get_warehouse($id);

		if (($warehouse)) {
			$respuesta = array(
				'err' => false,
				'mensaje' => 'Registro cargado correctamente',
				'cliente' => $warehouse
			);
			$this->response($respuesta);
		} else {
			$respuesta = array(
				'err' => true,
				'mensaje' => 'El warehouse ' . $id . ', no existe ',
				'warehouse' => null
			);
			$this->response($respuesta, REST_Controller::HTTP_NOT_FOUND);
		}
	}


	public function estatus_get()
	{
		$id = $this->uri->segment(3);

		if (!isset($id)) {
			$respuesta = array(
				'err' => true,
				'mensaje' => 'Es necesario el número de warehouse'
			);
			$this->response($respuesta, REST_Controller::HTTP_BAD_REQUEST);
			return;
		}

		if ($id == " ") {
		} else {
			$id = str_replace("%20", " ", $id);
		}
		$warehouse = $this->Warehouse_model->get_estatus($id);

		if (($warehouse)) {
			$respuesta = array(
				'err' => false,
				'mensaje' => 'Registro cargado correctamente',
				'cliente' => $warehouse
			);
			$this->response($respuesta);
		} else {
			$respuesta = array(
				'err' => true,
				'mensaje' => 'El warehouse ' . $id . ', no existe ',
				'warehouse' => null
			);
			$this->response($respuesta, REST_Controller::HTTP_NOT_FOUND);
		}
	}



	public function correo_get()
	{
		$id = $this->uri->segment(3);

		if (!isset($id)) {
			$respuesta = array(
				'err' => true,
				'mensaje' => 'Es necesario el número de warehouse'
			);
			$this->response($respuesta, REST_Controller::HTTP_BAD_REQUEST);
			return;
		}

		if ($id == " ") {
		} else {
			$id = str_replace("%20", " ", $id);
		}
		$warehouse = $this->Warehouse_model->get_estatus($id);

		if (($warehouse)) {
			$respuesta = array(
				'err' => false,
				'mensaje' => 'Registro cargado correctamente',
				'cliente' => $warehouse
			);
			$this->response($respuesta);
		} else {
			$respuesta = array(
				'err' => true,
				'mensaje' => 'El warehouse ' . $id . ', no existe ',
				'warehouse' => null
			);
			$this->response($respuesta, REST_Controller::HTTP_NOT_FOUND);
		}
	}

	public function login_get()
	{
		$email = $this->uri->segment(3);
		$pass = $this->uri->segment(4);


		$datosparm = array(

			'correo' => $email,

			'clave'   =>  $pass,

		);


		$credenciales = $this->Usuario_model->getLogin($datosparm);

		if (($credenciales)) {
			$respuesta = array(
				'err' => false,
				'mensaje' => 'las credenciales son correctas',
				'login' => $credenciales
			);
			$this->response($respuesta);
		} else {
			$respuesta = array(
				'err' => true,
				'mensaje' => 'Credenciales incorrectas ',
				'login' => null
			);
			$this->response($respuesta, REST_Controller::HTTP_NOT_FOUND);
		}
	}

	public function password_post()
	{
		$data = $this->post();

		$this->load->library('form_validation');

		$this->form_validation->set_data($data);


		if ($this->form_validation->run('password_post')) {

			$hecho = $this->Usuario_model->update_password($data);

			if ($hecho == true) {
				$respuesta = array(
					'err' => FALSE,
					'mensaje' => 'Registro actualizado correctamente',
				);

				$this->response($respuesta);
			} else {
				$respuesta = array(
					'err' => TRUE,
					'mensaje' => 'Error al actualizar',
				);

				$this->response($respuesta);
			}
		} else {
			$respuesta = array(
				'err' => TRUE,
				'mensaje' => 'Hay errores en el envio de informacion',
				'errores' => $this->form_validation->get_errores_arreglo()
			);

			$this->response($respuesta, REST_Controller::HTTP_BAD_REQUEST);
		}
	}

	public function mail_post()
	{
		$data = $this->post();

		$this->load->library('form_validation');

		$this->form_validation->set_data($data);
		// $casillero = $this->uri->segment(3);
		//$casillero = 'SAL 50005';
		//$data['casillero'] = $casillero;

		if ($this->form_validation->run('mail_post')) {

			$hecho = $this->Usuario_model->update_correo($data);

			if ($hecho == true) {
				$respuesta = array(
					'err' => FALSE,
					'mensaje' => 'Registro actualizado correctamente',
				);

				$this->response($respuesta);
			} else {
				$respuesta = array(
					'err' => TRUE,
					'mensaje' => 'Error al actualizar',
				);

				$this->response($respuesta);
			}
		} else {
			$respuesta = array(
				'err' => TRUE,
				'mensaje' => 'Hay errores en el envio de informacion',
				'errores' => $this->form_validation->get_errores_arreglo()
			);

			$this->response($respuesta, REST_Controller::HTTP_BAD_REQUEST);
		}
	}

	public function sucursal_post()
	{
		$data = $this->post();

		$this->load->library('form_validation');

		$this->form_validation->set_data($data);


		if ($this->form_validation->run('sucursal_post')) {

			$hecho = $this->Usuario_model->update_sucursal($data);

			if ($hecho == true) {
				$respuesta = array(
					'err' => FALSE,
					'mensaje' => 'Registro actualizado correctamente',
				);

				$this->response($respuesta);
			} else {
				$respuesta = array(
					'err' => TRUE,
					'mensaje' => 'Error al actualizar',
				);

				$this->response($respuesta);
			}
		} else {
			$respuesta = array(
				'err' => TRUE,
				'mensaje' => 'Hay errores en el envio de informacion',
				'errores' => $this->form_validation->get_errores_arreglo()
			);

			$this->response($respuesta, REST_Controller::HTTP_BAD_REQUEST);
		}
	}

	public function registro_put()
	{

		$data = $this->put();

		$this->load->library('form_validation');

		$this->form_validation->set_data($data);
		if ($this->form_validation->run('registro_put')) {

			$registro = array(
				'nombre' => $data['nombre'],
				'correo' => $data['correo'],
				'telefono' => $data['telefono'],


			);

			$hecho = $this->Manifiesto_model->guardar_ios($registro);
			if ($hecho) {
				$respuesta = array(
					'err' => FALSE,
					'mensaje' => 'Registro insertado correctamente',
					'registro_id' => $this->db->insert_id()
				);
				$this->response($respuesta);
			} else {
			}
		} else {
			$respuesta = array(
				'err' => TRUE,
				'mensaje' => 'Hay errores en el envio de informacion',
				'errores' => $this->form_validation->get_errores_arreglo()
			);

			$this->response($respuesta, REST_Controller::HTTP_BAD_REQUEST);
		}
	}

	public function obtner_sucursales_get()
	{

		$sucursal = $this->Casillero_model->obtner_sucursales();


		if (($sucursal)) {
			$respuesta = array(
				'err' => false,
				'mensaje' => 'Consulta realizada correctamente',
				'sucursal' => $sucursal
			);
			$this->response($respuesta);
		} else {
			$respuesta = array(
				'err' => true,
				'mensaje' => 'No ha sipo posile obtener registros ',
				'sucursal' => null
			);
			$this->response($respuesta, REST_Controller::HTTP_NOT_FOUND);
		}
	}

	public function obtener_arancel_get()
	{

		$arancel = $this->Cotizador_model->obtner_arancel();

		//  var_dump( $arancel);
		if (($arancel)) {
			$respuesta = array(
				'err' => false,
				'mensaje' => 'Consulta realizada correctamente',
				'arancel' => $arancel
			);
			$this->response($respuesta);
		} else {
			$respuesta = array(
				'err' => true,
				'mensaje' => 'No ha sipo posile obtener registros ',
				'arancel' => null
			);
			$this->response($respuesta, REST_Controller::HTTP_NOT_FOUND);
		}
	}

	public function calcular_get()
	{
		$peso = $this->uri->segment(3);
		$valor = $this->uri->segment(4);
		$tipo_menrcancia = $this->uri->segment(5);

		$arancelcot = $this->Cotizador_model->get_arancel($tipo_menrcancia);
		$flete = $this->Cotizador_model->get_flete($peso);
		$tramite = $this->Cotizador_model->get_tramite($valor);
		$seguro = $this->Cotizador_model->get_seguro($valor);
		// $almacenaje = $this->Cotizador_model->get_almacenaje($peso);
		//var_dump($almacenaje);
		$tarifa_flete = $flete->valor_libra;
		$tarifa_tramite = $tramite->valor_tramite;
		$tarifa_seguro = $seguro->valor_seguro;
		$rango_almacenaje = 0; //$almacenaje->valor_almacenaje;
		$peso_cotizar = $peso;


		$costolibras = $tarifa_flete * $peso_cotizar;
		$costotramite = $tarifa_tramite;
		$costoseguro = $tarifa_seguro;
		$primaseguro = $valor * 0.015;

		$costototal = $costolibras + 0 + $valor;
		$totdai = ($costototal  + $costoseguro) * ($arancelcot->porcentaje / 100);
		$totiva = ($costototal + $costoseguro) * 0.13;
		$impuesto = 0;

		if ($valor < 200) {

			$impuesto = number_format(($totiva), 2);
		} else {
			$impuesto = number_format(($totdai + $totiva), 2);
		}
		$corteguia = 0;
		$transferenciaymanejo = 0;
		$cesc = 0;
		$inspeccionointrusiva = 0;
		$totalsts = 0;
		//Inspecci¨®n no intrusiva
		$inspeccionointrusiva = 0;
		//CESC
		if ($arancelcot->cesc == 1) {
			$cesc = ($costototal + $totdai) * ($arancelcot->porcentaje_cesc / 100);
		} else {
			$cesc = 0;
		}
		$totalsts = number_format($impuesto, 2) + number_format($costotramite, 2) + number_format($costolibras, 2) + number_format($corteguia, 2) + number_format($transferenciaymanejo, 2) + number_format($inspeccionointrusiva, 2)  + number_format($rango_almacenaje, 2);
		// +   +  + number_format($almacenaje,2)
		$total = 0;
		//Total
		//  echo $valor+$totalsts;
		$total = $totalsts + $valor + $costoseguro - $rango_almacenaje;
		// $total=number_format($total,2);

		if (($total)) {
			$respuesta = array(
				'err' => false,
				'mensaje' => 'Cálculo realizado correctamente',
				'calculo_estimado' => $total,
				'valor_articulo' => $valor,
				'envio' => $costolibras,
				'seguro' => $costoseguro,
				'impuesto' => $impuesto,
				'costo_tramite' => $costotramite


			);
			$this->response($respuesta);
		} else {
			$respuesta = array(
				'err' => true,
				'mensaje' => 'No ha sido posible calcular el producto ',
				'total' => null
			);
			$this->response($respuesta, REST_Controller::HTTP_NOT_FOUND);
		}
	}
	public function manifiestoap_post()
	{
		$_SESSION['pais'] = 2;
		$data = $this->post();
		$country = 2;
		$awb = $data['mawb'];
		$etd = $data['etd'];
		$eta = $data['eta'];
		//  $pais=$data['country'];
		$enca = array(
			'manifiesto' => $data['mawb'],
			'fecha' => $data['etd'],
			'descripcion' => "Pruebas API",
			'referencia' => "Pendiente",
			'paquetes' => "0",
			'sacos' => $data['bag_number'],
			'tipo' => "ALL",
			'client_id' => $data['client_id'],
			'userid' => $data['client_id']

		);
		// 'id_pais' => $country,
		$id_manifiesto =  $this->Manifiesto_model->guardar_manifiesto($enca);



		if ($id_manifiesto) {

			foreach ($data['guias'] as $key => $detalle) {
				$rate['tarifa'] = $this->Manifiesto_model->tarifas(); //toma el valor de la guia para evaluacion de acuerdo al rango de tarifa (tabla tarifa)
				//     var_dump( $rate['tarifa']);
				$tarifa = 0;
				foreach ($rate['tarifa'] as $f) {

					if ($tarifa == 0) {
						if ($detalle['value']  >= $f->desde &&  $detalle['value']  <= $f->hasta) {
							$tarifa = $f->tarifa;
						}
					}
				}
				/* 'mawb'                   => $detalle['mawb'],
                'bag_number'             => $detalle['bag_number'],
                'etd'                    => $detalle['etd'],
                'eta'                    => $detalle['eta'], */
				$deta = array(
					'etd'                    => $etd,
					'eta'                    => $eta,
					'order_number'           => $detalle['order_number'],
					'tracking_number'        => $detalle['tracking_number'],
					'origin'                 => $detalle['origin'],
					'destination'            => $detalle['destination'],
					'consignee_account'      => $detalle['consignee_account'],
					'consignee'              => $detalle['consignee'],
					'consignee_address1'     => $detalle['consignee_address1'],
					'consignee_address2'     => $detalle['consignee_address2'],
					'consignee_address3'     => $detalle['consignee_address3'],
					'consignee_neighborhood' => $detalle['consignee_neighborhood'],
					'consignee_city'         => $detalle['consignee_city'],
					'consignee_state'        => $detalle['consignee_state'],
					'consignee_zip'          => $detalle['consignee_zip'],
					'consignee_country'      => $detalle['consignee_country'],
					'consignee_email'        => $detalle['consignee_email'],
					'consignee_phone'        => $detalle['consignee_phone'],
					'consignee_mobile'       => $detalle['consignee_mobile'],
					'consignee_taxid'        => $detalle['consignee_taxid'],
					'pieces'                 => $detalle['pieces'],
					'gweight'                => $detalle['gweight'],
					'cweight'                => $detalle['cweight'],
					'weight_type'            => $detalle['weight_type'],
					'height'                 => $detalle['height'],
					'length'                 => $detalle['length'],
					'width'                  => $detalle['width'],
					'commodity'              => $detalle['commodity'],
					'value'                  => $detalle['value'],
					'freight'                => $detalle['freight'],
					'currency'               => $detalle['currency'],
					'service_type'           => $detalle['service_type'],
					'service_level'          => $detalle['service_level'],
					'shipper_account'        => $detalle['shipper_account'],
					'shipper_name'           => $detalle['shipper_name'],
					'shipper_address1'       => $detalle['shipper_address1'],
					'shipper_address2'       => $detalle['shipper_address2'],
					'shipper_city'           => $detalle['shipper_city'],
					'shipper_state'          => $detalle['shipper_state'],
					'shipper_zip'            => $detalle['shipper_zip'],
					'shipper_country'        => $detalle['shipper_country'],
					'shipper_email'          => $detalle['shipper_email'],
					'shipper_phone'          => $detalle['shipper_phone'],
					'id_pais'                => $_SESSION["pais"],
					'id_manifiesto'          => $id_manifiesto,
					'tarifa'                 => $tarifa
				);


				$hecho = $this->Manifiesto_model->guardar_manifiesto_detalle($deta);
			}

			if ($hecho) {
				$respuesta = array(
					'err' => FALSE,
					'mensaje' => 'Registro insertado correctamente',

				);
				$this->response($respuesta);
			}
		}
	}

	public function manifiestoma_post()
	{

		$country = 0;
		$tasa = 0;
		$metodo = "P"; //si metodo es igual [P] indica que utiliza correlativos propios (clientes), si metodo igual [E], utiliza correlativos xpresss
		$data = $this->post();
		$id_empresa = $data['client_id'];
		$referencia = "Pendiente";
		//SV
		if ($data['client_id'] == 100) {
			$country = 1;
			//$metodo="P";
			$id_empresa = 100;
		}
		//GT
		if ($data['client_id'] == 101) {
			$country = 2;
			//$metodo="E";
			$id_empresa = 101;
			$referencia = "ROJO";
		}
		//HN
		if ($data['client_id'] == 102) {
			$country = 3;
			//  $metodo="E";
			$id_empresa = 102;
		}

		// $this->Manifiesto_model->guardar_manifiesto($enca);
		$awb = $data['awb'];
		$pais = $data['country'];

		$enca = array(
			'manifiesto' => $data['awb'],
			'fecha' => $data['fecha'],
			'descripcion' => "MailAmericas",
			'referencia' => "Pendiente",
			'paquetes' => "0",
			'sacos' => $data['bag'],
			'tipo' => "ALL",
			'client_id' => $data['client_id'],
			'id_pais' => $country,
			'userid' => $data['client_id'],
			'tipo_factura'          => "MA"

		);
		$id_manifiesto =  $this->Manifiesto_model->guardar_manifiesto($enca);



		if ($id_manifiesto) {
			$plataforma = "none";
			foreach ($data['guias'] as $key => $detalle) {

				$kilos = $detalle['weight'];
				$libras = $kilos * 2.2046;
				$plataforma = $detalle['client_id'];
				$deta = array(
					'id_manifiesto'          => $id_manifiesto,
					'tracking_number'        => $detalle['tracking_number'],
					'gweight'                => $detalle['weight'],
					'value'                  => $detalle['value'],
					'items'                  => $detalle['items'],
					'commodity'              => $detalle['items_description'],
					'consignee_account'      => $detalle['buyer_id'],
					'consignee'              => $detalle['buyer'],
					'buyer_company'          => $detalle['buyer_company'],
					'consignee_address1'     => $detalle['buyer_address1'],
					'buyer_address1_number'  => $detalle['buyer_address1_number'],
					'consignee_address2'     => $detalle['buyer_address2'],
					'consignee_address3'     => $detalle['buyer_address3'],
					'buyer_district'         => $detalle['buyer_district'],
					'consignee_city'         => $detalle['buyer_city'],
					'consignee_state'        => $detalle['buyer_state'],
					'consignee_country'      => $detalle['buyer_location'],
					'consignee_zip'          => $detalle['buyer_zip'],
					'consignee_phone'        => $detalle['buyer_phone'],
					'consignee_email'        => $detalle['buyer_email'],
					'hts'                    => $detalle['hts'],
					'id_pais'                => $country,
					'pieces'                 => $detalle['pieces'],
					'departamento'           => $detalle['departamento'],
					'municipio'              => $detalle['municipio'],
					'wpounds'                => $kilos,
					'bag_number'             => $detalle['bag_number'],
					'client_id'             => $detalle['client_id'],
					'metodo'                => $metodo,
					'id_empresa'            => $id_empresa,
					'tipo_entrega'           => "NRML",
					'tipo_servicio'          => "CCE",
					'Bol_type_code'          => 'HCO',
					'natcod'                 => 23,
					'items_quantities'       => $detalle['items_quantities']



				);


				$hecho = $this->Manifiesto_model->guardar_manifiesto_detalle($deta);
			}

			if ($data['client_id'] == 100) {
				if ($plataforma == "Cainiao- AliExpress") {
					$id_empresa = 108;
				}

				if ($plataforma == "SHEIN") {
					$id_empresa = 109;
				}

				$this->Manifiesto_model->update_empresaDetalle($id_empresa, $id_manifiesto);
				$this->Manifiesto_model->update_empresaEnca($id_empresa, $id_manifiesto, $plataforma);
			}

			if ($hecho) {
				$respuesta = array(
					'err' => FALSE,
					'mensaje' => 'Registro insertado correctamente',

				);
				$this->response($respuesta);
			}
		}
	}

	public function manifiesto_post()
	{
		// Source - https://stackoverflow.com/a
		// Posted by Fancy John, modified by community. See post 'Timeline' for change history
		// Retrieved 2025-11-30, License - CC BY-SA 4.0

		ini_set('display_errors', '1');
		ini_set('display_startup_errors', '1');
		error_reporting(E_ALL);

		//exit;
		$country = 0;
		$tasa = 0;
		$metodo = "P"; //si metodo es igual [P] indica que utiliza correlativos propios (clientes), si metodo igual [E], utiliza correlativos xpresss
		$data = $this->post();
		$id_empresa = $data['client_id'];
		if ($data['client_id'] == 107) {
			$tipo_factura = "AP";
			$country = 1;
			//$metodo="P";
			//$id_empresa = 100;
		}
		if ($data['client_id'] == 100) {
			$tipo_factura = "MA";
			$country = 1;
			//$metodo="P";
			//$id_empresa = 100;
		}
		if ($data['client_id'] == 103) {
			$tipo_factura = "IM";
			$country = 1;
			exit;
			//$metodo="P";
			//$id_empresa = 100;
		}

		if ($data['client_id'] == 104) {
			$tipo_factura = "IM";
			$country = 2;
			exit;
			//$metodo="P";
			//$id_empresa = 100;
		}

		if ($data['client_id'] == 105) {
			$tipo_factura = "IM";
			$country = 3;
			exit;
			//$metodo="P";
			//$id_empresa = 100;
		}

		if ($data['client_id'] == 110) {
			$tipo_factura = "CH";
			$country = 1;
		}
		if ($data['client_id'] == 112) {
			$tipo_factura = "TM";
			$country = 1;
		}
		if ($data['client_id'] == 113) {
			$tipo_factura = "TM";
			$country = 2;
		}
		if ($data['client_id'] == 114) {
			$tipo_factura = "TM";
			$country = 3;
		}

		// $this->Manifiesto_model->guardar_manifiesto($enca);
		$awb = $data['awb'];
		$pais = $data['country'];

		$enca = array(
			'manifiesto' => $data['awb'],
			'fecha' => $data['fecha'],
			'descripcion' => "Pruebas API",
			'referencia' => "Pendiente",
			'paquetes' => "0",
			'sacos' => $data['bag'],
			'tipo' => "ALL",
			'client_id' => $data['client_id'],
			'id_pais' => $country,
			'userid' => $data['client_id'],
			'tipo_factura' => $tipo_factura,
			'linea_master'    => 0,

		);
		$id_manifiesto =  $this->Manifiesto_model->guardar_manifiesto($enca);



		if ($id_manifiesto) {
			if ($data['client_id'] == 112 || $data['client_id'] == 113 || $data['client_id'] == 114) {
				foreach ($data['guias'] as $key => $detalle) {

					$kilos = $detalle['weight'];
					$libras = $kilos * 2.2046;
					$deta = array(
						'id_manifiesto'          => $id_manifiesto,
						'tracking_number'        => $detalle['tracking_number'],
						'gweight'                => $detalle['weight'],
						'value'                  => $detalle['value'],
						'weight_unit'            => $detalle['weight_unit'],
						'value_unit'             => $detalle['value_unit'],
						'items'                  => $detalle['items'],
						'commodity'              => $detalle['items_description'],
						'consignee_account'      => $detalle['buyer_id'],
						'consignee'              => $detalle['buyer'],
						'buyer_company'          => $detalle['buyer_company'],
						'consignee_address1'     => $detalle['buyer_address1'],
						'buyer_address1_number'  => $detalle['buyer_address1_number'],
						'consignee_address2'     => $detalle['buyer_address2'],
						'consignee_address3'     => $detalle['buyer_address3'],
						'buyer_district'         => $detalle['buyer_district'],
						'consignee_city'         => $detalle['buyer_city'],
						'consignee_state'        => $detalle['buyer_state'],
						'consignee_country'      => $detalle['buyer_location'],
						'consignee_zip'          => $detalle['buyer_zip'],
						'consignee_phone'        => $detalle['buyer_phone'],
						'consignee_email'        => $detalle['buyer_email'],
						'hscode'                 => $detalle['hts'],
						'id_pais'                => $country,
						'pieces'                 => $detalle['pieces'],
						'departamento'           => $detalle['departamento'],
						'municipio'              => $detalle['municipio'],
						'wpounds'                => $kilos,
						'bag_number'             => $detalle['bag_number'],
						'client_id'              => "TMU", //$detalle['client_id'],
						'metodo'                 => $metodo,
						'id_empresa'             => $id_empresa,
						'tipo_entrega'           => "NRML",
						'tipo_servicio'          => "SER",
						'Bol_type_code'          => 'HCO',
						'natcod'                 => 23,
						'departamento_id'        => $detalle['departamento_id'],
						'municipio_id'           => $detalle['municipio_id'],
						'url'                    => $detalle['url'],

					);
				}
			} else {
				foreach ($data['guias'] as $key => $detalle) {

					$kilos = $detalle['weight'];
					$libras = $kilos * 2.2046;
					$deta = array(
						'id_manifiesto'          => $id_manifiesto,
						'tracking_number'        => $detalle['tracking_number'],
						'gweight'                => $detalle['weight'],
						'value'                  => $detalle['value'],
						'items'                  => $detalle['items'],
						'commodity'              => $detalle['items_description'],
						'consignee_account'      => $detalle['buyer_id'],
						'consignee'              => $detalle['buyer'],
						'buyer_company'          => $detalle['buyer_company'],
						'consignee_address1'     => $detalle['buyer_address1'],
						'buyer_address1_number'  => $detalle['buyer_address1_number'],
						'consignee_address2'     => $detalle['buyer_address2'],
						'consignee_address3'     => $detalle['buyer_address3'],
						'buyer_district'         => $detalle['buyer_district'],
						'consignee_city'         => $detalle['buyer_city'],
						'consignee_state'        => $detalle['buyer_state'],
						'consignee_country'      => $detalle['buyer_location'],
						'consignee_zip'          => $detalle['buyer_zip'],
						'consignee_phone'        => $detalle['buyer_phone'],
						'consignee_email'        => $detalle['buyer_email'],
						'hscode'                 => $detalle['hts'],
						'id_pais'                => $country,
						'pieces'                 => $detalle['pieces'],
						'departamento'           => $detalle['departamento'],
						'municipio'              => $detalle['municipio'],
						'wpounds'                => $kilos,
						'bag_number'             => $detalle['bag_number'],
						'client_id'              => "TMU", //$detalle['client_id'],
						'metodo'                 => $metodo,
						'id_empresa'             => $id_empresa,
						'tipo_entrega'           => "NRML",
						'tipo_servicio'          => "SER",
						'Bol_type_code'          => 'HCO',
						'natcod'                 => 23,
						'departamento_id'        => $detalle['departamento_id'],
						'municipio_id'           => $detalle['departamento_id'],
						'agencia_destino'        => 0,

					);
				}
			}
			$hecho = $this->Manifiesto_model->guardar_manifiesto_detalle($deta);
			//'items_quantities'       => '[1]',
			if ($hecho) {
				$respuesta = array(
					'err' => FALSE,
					'mensaje' => 'Registro insertado correctamente',

				);
				$this->response($respuesta);
			}
		}
	}

	/* public function addmanifiesto_post()
    {

        $data = $this->post();

        $this->load->library('form_validation');

        $this->form_validation->set_data($data);
        if ($this->form_validation->run('addmanifiesto_post')) {



            $hecho = $this->Manifiesto_model->guardar_manifiesto($data);
            if ($hecho) {
                $respuesta = array(
                    'err' => FALSE,
                    'mensaje' => 'Registro insertado correctamente',
                    'registro_id' => $this->db->insert_id()
                );
                $this->response($respuesta);
            } else {
            }
        } else {
            $respuesta = array(
                'err' => TRUE,
                'mensaje' => 'Hay errores en el envio de informacion',
                'errores' => $this->form_validation->get_errores_arreglo()
            );

            $this->response($respuesta, REST_Controller::HTTP_BAD_REQUEST);
        }
    }*/

	//integracion con xpress
	public function addestatus_post()

	{
		$data = $this->post(); //contiene los datos

		$item = "";
		$fecha = "";
		$warehouse = "";
		$id_estatus = "";
		$casillero = "";
		$id_pais =  "";


		// ******

		//$gg = "kkkk";
		//$r2 = json_decode($data);
		//$this->response($data);

		// *********



		//echo $data[0]->guia;

		//$this->response($data);

		//$result = json_decode($data);
		$result = json_decode($data);
		//$this->response($result); 

		//var_dump($result);

		foreach ($result as $fila) {
			$id_estatus = $fila->codigo;
			$id_estatus = $fila['codigo'];
		}

		//
		//$this->response$id_estatus);

		$item = 1;
		$fecha = date('Y-m-d H:i:s');
		//$warehouse= $data['guia'];
		$warehouse = "";
		$id_estatus = $id_estatus;
		$casillero = "100";
		$id_pais =  1;

		//foreach ($data['pod'] as $key => $detalle) {
		//echo $detalle['nombre'];
		//echo $detalle['valor'];
		//}
		//$this->response($data);

		$datos = array(
			'item' => $item,
			'fecha' => $fecha,
			'warehouse' => $warehouse,
			'id_estatus' => $id_estatus,
			'casillero' => $casillero,
			'id_pais' =>  $id_pais,
		);

		//$hecho = $this->Manifiesto_model->guardar_estatus($datos);


	}

	public function estadosmAILaMERICA_post()
	{


		date_default_timezone_set("America/El_Salvador");
		$date = date('Y-m-d H:i:s');
		$fecha_tmp = DateTime::createFromFormat('Y-m-d H:i:s', $date);
		$fecha_iso = $fecha_tmp->format(DateTime::ATOM);

		$item = "";
		$fecha = "";
		$warehouse = "";
		$id_estatus = "";
		$casillero = "";
		$id_pais =  "";



		$data = $this->post(); //contiene los datos

		$data =  file_get_contents('php://input', true);
		//  echo $data ;
		$row = json_decode($data, true);



		/* aqui prueba */

		//$datos = array(
		//'json_string' => $data
		// );
		//   $this->Manifiesto_model->guardar_json($datos);



		/* fin aui prueba */

		$item = 1;
		$fecha = date('Y-m-d H:i:s');
		$warehouse =  $row['guia'];

		/** Recibe la guia de xpreess (internal_tracking) y luego busca la guia de Mailamericas(tracking_number) */
		$query =	 $this->Manifiesto_model->get_tracking($warehouse);

		$cliente = $this->Manifiesto_model->datos_empresa($query[0]->id_empresa);

		$codigoEmpresa = $query[0]->id_empresa; //se utiliza para determinar si se crea FEL o no

		$id_tokencliente = $cliente[0]->id_tokencliente;
		$token_cliente = $cliente[0]->token_cliente;


		if ($query[0]->metodo == "E") {

			$warehouse = $query[0]->tracking_number;
			// echo "estoy en correlativos de xpress";
		} else {
			//echo "estoy en correlativos de MA";
		}

		//	echo $cliente. " ". $codigoEmpresa. " ". $id_tokencliente. " ".$token_cliente;
		//exit;

		$id_estatus = $row['codigo'];
		$nombre_estatus = $row['estatus'];

		$description = "";
		if ($id_estatus == 16) {
			$description    =  $row['estatus'] . ": " .  $row['observaciones'];
		} else {
			$description    =  $row['estatus'];
		}

		$casillero = "100";
		$id_pais =  1;
		$pod_nombre = "";
		$pod_valor = "";
		$document_name = "";
		$ruta = "";
		//    $this->response($row);
		if ($id_estatus == 15) {
			$ruta = "https://stargateserp.com/documentos_ma/";
			// foreach ( $row['pod'] as $key => $detalle) {


			foreach ($row['pod'] as  $detalle) {
				$pod_nombre = $detalle['nombre'];
				$pod_valor  = $detalle['valor'];
				$img = str_replace('data:image/png;base64,', '', $pod_valor);
				$img = str_replace('data:image/png;base64,', '', $img);
				$img = str_replace(' ', '+', $img);
				$data1 = base64_decode($img);
				$document_name = uniqid() . '.png';
				//  $file = "/home/km96wogi8pjb/public_html/documentos_ma/". uniqid() . '.jpg';
				// $file = "/home/km96wogi8pjb/public_html/documentos_ma/" . $document_name;
				$file = "/var/www/stargateserp/documentos_ma/" . $document_name;


				$success = file_put_contents($file, $data1);
				//    echo "bytes fueron escritos en $file";     

			}
		}
		//   $this->response($row);

		if ($id_estatus == 15) {
			$ruta = $ruta . $document_name;
		} else {
			$ruta = null;
		}



		if ($id_estatus == 15 && ($row['devolucion'] ?? 0) == 1) {
			$id_estatus = "FP-2";
			$description = "Entregado en dirección de retorno";
		}
		$this->Manifiesto_model->update_estatus($warehouse, $id_estatus); //guardar estas en tablas locales

		$datos = array(

			'item' => $item,

			'fecha' => date('Y-m-d H:i:s'),

			'warehouse' => $warehouse,

			'id_estatus' => $id_estatus,

			'casillero' =>  $casillero,

			'id_pais' =>  $id_pais,

			'pod_nombre' =>  $pod_nombre,

			'pod_valor' => "",

			'json_string' => '{    "guia": ""}', //$data, data trae el json cpmpleto, utilizarlo para testing

			'ruta_foto' => $ruta,

			'fechaiso' => $fecha_iso

		);


		$this->Manifiesto_model->guardar_estatus($datos);

		$token_ma = $this->token_ma($id_tokencliente, $token_cliente); //obtiene el token



		$header = array(
			'Host: tracking.mailamericas.com',
			'Content-Type: application/json',
			'Cache-Control: no-cache',
			'Authorization: Bearer ' . $token_ma
		);

		$data = array(
			"tracking" => $warehouse,
			"date" =>  $fecha_iso,
			"description" =>   $description,
			"city" => "CCO",
			"received_by" => null,
			"relationship" => null,
			"delivery_proof" => [
				$ruta . $document_name
			],

			"gcs" => "",
			"details" => ""
		);

		$url = "https://tracking.mlasalvador.com/api/v1/providers/events";
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.52 Safari/537.17');
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		$resultStr = curl_exec($ch);

		if (curl_errno($ch)) {
			$error_msg = curl_error($ch);
		}
		curl_close($ch);
		//  echo  $error_msg;
		//   echo $resultStr;

		$data = array(
			"respuesta" => $resultStr,
			"tracking_number" =>  $warehouse,
			"descripcion" =>  $description, // m
			"fecha" =>  $fecha,
			"fechaiso" =>  $fecha_iso

		);

		// $this->Manifiesto_model->guardar_logma($data);

		$this->DTES_Model->updatetracking_usps($id_estatus, $warehouse);

		if ($codigoEmpresa == 108 || $codigoEmpresa == 109 || $codigoEmpresa == 100) {
			//bloque para facturar MA-FEL
			if ($id_estatus == 15) {
				$guiaFel = $this->Manifiesto_model->get_guiaFel($warehouse);
				$json_data = json_encode($guiaFel);

				$curl = curl_init();

				curl_setopt_array($curl, array(
					CURLOPT_URL => 'https://stargates.site/app_starship/ws/index.php/api/guiaaws',
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_POST => true,  // Forzar solicitud POST
					CURLOPT_POSTFIELDS => $json_data,  // Enviar el JSON como datos POST
					CURLOPT_HTTPHEADER => array(
						'X-API-KEY: 4a4da5bb348e648e51d84e24c8652d1284c31156',
						'Content-Type: application/json',
						'Content-Length: ' . strlen($json_data)  // Longitud del contenido
					),
				));

				$response = curl_exec($curl);
				$error_no = curl_errno($curl);
				$error_msg = curl_error($curl);

				curl_close($curl);

				// Manejo de errores
				if ($error_no) {
					echo "cURL Error ($error_no): " . $error_msg . PHP_EOL;
				} else {
					echo $response;
				}
			}
		}

		$warehouse = '';
		# SEGMENTO PARA LA FACTURACION ELECTRONICA
		if ($warehouse != '') {
			if ($id_estatus == 15) {
				ini_set('display_errors', 1);
				ini_set('display_startup_errors', 1);
				error_reporting(E_ALL);
				$ejecuta    = false;
				if ($ejecuta == true) {
					$_SESSION["user_id"] = date("his");
					$seleccionado        = 1;
					$ma                  = 1;
					$guiaMAupdate        = 0;
					$idEncaComprobante   = 0;
					$datoupcabecera      = 0;
					$datosdfacturacion   = 0;
					$guiaSeleccionada    = 0;
					$datosguiaMA         = 0;
					$datosguiaMA         =  $this->getItemsForGma($warehouse); // DESCARGAMOS LA GUIA
					//echo  'la guia  maRetorno'. $datosguiaMA;
					# SEGMENTO PARA LA EJECUCION  DE  SELECCION Y OTROS PROCESOS  SI SE  DESCARGA LA GUIA
					if ($datosguiaMA != 0) {
						$guiaSeleccionada = $this->seleccionar_guia($warehouse, $seleccionado, $ma);
						if ($guiaSeleccionada == 0) { // 0  indica que la guia  no  tuvo ninguna incosistencia y se selecciono
							# SEGMENTO  PARA LA  GENERACION  DE LA  VENTA
							// echo  'Ingresando al  proceso de facturacion';
							$clasificacioFiscal     = 0;   // 1- grande  2-mediano  3- otros
							$tipoContribuyente      = 0;   // 1- gravado 2- excento 3- tasa cero 4-sujeto excluido
							$sujetoRetencion        = 0;   // solo si la   si la venta gravada es >=100  y es 1- grande ciontribuyente
							$idTipoComprobante      = 0;   // 1-CCF 2-FCf 3- NCR(nota credito) 4- fex(factura exportacion) 5- ANF (anulacion factura consumidor final)
							$numero_comprobante     = 0;
							$fecha_comprobante      = date('Y-m-d'); // cambiar por la indicada
							$id_tipo_transac        = '';
							$idEmpresa              = 0;
							$idSucursal             = 0;
							$idCliente              = 0;
							$idDestinatario         = 0;
							$idCatalogoProducto     = 0;
							$ot_numero              = 0; // orden de trabajo
							// Capturando valriables de session
							$idEmpresa              = 47; //$_SESSION["empresa_id"];// se cambia para variable de inicion de sesion
							$idSucursal             = '1'; // factura para  global cargo, se direcciona todo para la sucursal san francisco
							$idEncaComprobante      = 0;
							// para calculos
							$cantidad               = 1; //  evaluar
							$precio_neto            = 0; //  es el valor del  servicio
							$venta_neta             = 0;
							$id_tipo_evento         = 2; // 2- exportacion
							$fact_manual            =  0;
							$id_comprob_afecto      = null;
							$DatosClientes        = $this->Exportacion_Model->obtenerClienteAgrupadosMA(); // se obtien los datos de la guias(el detalle)
							if ($DatosClientes ==  null) {
								echo 0; // variable que indica si existen datos a facturar
								echo 'NO HAY DATOS A FACTURAR '; // variable que indica si existen datos a facturar
							} else {
								// echo 1;
								foreach ($DatosClientes as $datosClientes) { //  recorremos el numero  de  clientes  pendientes  a facturar
									$idCliente   = $datosClientes->id;
									//var_dump( 'el Cliente a procesar !!!!!!!!!!!!!!!!!!!!!!! '.$idCliente) ;
									$destinatarios = $this->Conf_model->get_destinatario($idCliente);
									if (!empty($destinatarios)) {
										$id_destinatario = $destinatarios[0]->id;
									} else {
										$id_destinatario =  1033;
									}
									$clasificacioFiscal = $datosClientes->id_clasificacion_fiscal;
									// 1 GRANDE 2- MEDIANO 3- OTRO
									$id_tipo_pago =  $datosClientes->id_tipo_pago;
									$tipoContribuyente  = $datosClientes->id_tipo_contribuyente;
									if ($tipoContribuyente == '1') { // GRAVADO
										$tasaCero = 'NO';
										$exento   = 'NO'; // SI ES NO INDICA QUE SE LE APLICA IVA
									} elseif ($tipoContribuyente == '2') { // EXCENTO
										$tasaCero = 'NO';
										$exento   = 'SI'; // NO SE SE LE APLICA IVA
									} elseif ($tipoContribuyente == '3') { // TASA CERO
										$tasaCero = 'SI';
										$exento   = 'NO';
									}

									$sujetoRetencion    = $datosClientes->sujeto_ret_1;
									if ($sujetoRetencion == 'S'  || $sujetoRetencion == 's') { // EL  CLIENTE ESTA SUJETO A RETENSION
										$percep1 = 'SI';
									} else {
										$percep1 = 'NO';
									}
									$idTipoComprobante  = $datosClientes->id_tipo_comprobante;
									//  var_dump( 'el tipo de contribuyecte es'.$tipoContribuyente.'el tipo de comprobante a crear es' . $idTipoComprobante . 'valor de tasa cero'.$tasaCero.'el valor de excento' .$exento);
									$correlativoFactura =  $this->Importacion_Model->obtenerCorrelativoCom($idEmpresa, $idSucursal, $idTipoComprobante);
									// var_dump($correlativoFactura);
									$numero_comprobante =  $correlativoFactura->correlativo;
									// se incrementa el correlativo de factura
									$serie_factura      =  $correlativoFactura->serie;
									$nuevoCorrelativo   = strval($numero_comprobante + 1);
									// var_dump('el nuevo correlativo sera '.$nuevoCorrelativo);
									// 1- generar un encabezado con el  idautomatico
									$data = array(
										'id_empresa' 		  => $idEmpresa,
										'id_sucursal_empresa' => $idSucursal,
										'id_cliente'		  => $idCliente,
										'id_destinatario'	  => $id_destinatario,
										'id_tipo_comprobante' => $idTipoComprobante,
										'numero_comprobante'  => $numero_comprobante,
										'comprobante_afecto'  => $numero_comprobante,
										'serie_factura'		  => $serie_factura,
										'fecha_comprobante'   => $fecha_comprobante,
									);
									// 1- llamar la funcion de almacenar encabezado generar el espacio en la tabla
									$idEncaComprobante  = $this->Ventas_Model->guardar_venta($data);
									//$datos_encabezado  = $this->Importacion_Model->obtener_id_encabezado_venta($idEmpresa, $idSucursal, $idCliente, $idTipoComprobante, $numero_comprobante, $fecha_comprobante);
									//$idEncaComprobante = $datos_encabezado->id;
									$seleccionado      = 1;
									$Catalogos_a_Facturar =  $this->Exportacion_Model->obtner_valores_detalle_GuiaMA($warehouse);
									if (!empty($Catalogos_a_Facturar)) {

										foreach ($Catalogos_a_Facturar as $catalogos_a_Facturar) {
											// obtener la descripcion del servicio por catalago de producto
											// catalogos a facturar  cargo basico, inspeccion no intrusiva, fuel, seguro


											//$cargo_corte_guia = $catalogos_a_Facturar->D15;

											if ($catalogos_a_Facturar->D15 > 0) {
												$precio_neto  = $catalogos_a_Facturar->D15;
												$venta_neta   = ($cantidad * $precio_neto);
												$idCatalogoProducto = '31';
												// se busca la informacion del producto
												$DatosCatalogoProducto =  $this->Importacion_Model->obtenerDatosCatalogoProductos($idCatalogoProducto);
												foreach ($DatosCatalogoProducto as $key => $datosCatalogoProducto) {
													$id                     = $datosCatalogoProducto->id;
													$descripcion            = $datosCatalogoProducto->descripcion;
													$id_clasificacion_inv   = $datosCatalogoProducto->id_clasificacion_inv;
													$id_tipo_venta          = $datosCatalogoProducto->id_tipo_venta;
													$id_tipo_mh             = $datosCatalogoProducto->id_tipo_mh;
													$id_presentacion        = $datosCatalogoProducto->id_presentacion;
													$id_tc_producto         = $datosCatalogoProducto->id_tc_producto;
												}
												$DatosKardex = array(
													'id_catalogo_producto'  => $id,
													'descripcion'           => $descripcion,
													'cantidad'              => $cantidad,
													'precio_neto'           => $precio_neto, //precio del servicio,
													'venta_neta'            => $venta_neta, // precioServicio  * cantidad
													'id_tipo_venta'         => $id_tipo_venta, //1.ventas propias, 2.ventas a terceros, 3.ventas_ajenas
													'id_tipo_mh'            => $id_tipo_mh, //afecto o gravado, excento, no sujeto
													'id_tipo_comprobante'   => $idTipoComprobante //1.CCF, 2.FCF, 3.NCR, 4.FEX

												);

												// var_dump($DatosKardex);
												// $this->Exportacion_Controller->guardar_detalle_venta($idEncaComprobante, $exento, $tasaCero, $percep1, $DatosKardex);
												$this->guardar_detalle_venta($idEncaComprobante, $exento, $tasaCero, $percep1, $DatosKardex);
											}


											//$argo_manejo_y_transp = $catalogos_a_Facturar->argo_manejo_y_transp;
											if ($catalogos_a_Facturar->N15 > 0) {
												$precio_neto  = $catalogos_a_Facturar->N15;
												$venta_neta   = ($cantidad * $precio_neto);
												$idCatalogoProducto = '32';
												// se busca la informacion del producto
												$DatosCatalogoProducto =  $this->Importacion_Model->obtenerDatosCatalogoProductos($idCatalogoProducto);
												foreach ($DatosCatalogoProducto as $key => $datosCatalogoProducto) {
													$id                     = $datosCatalogoProducto->id;
													$descripcion            = $datosCatalogoProducto->descripcion;
													$id_clasificacion_inv   = $datosCatalogoProducto->id_clasificacion_inv;
													$id_tipo_venta          = $datosCatalogoProducto->id_tipo_venta;
													$id_tipo_mh             = $datosCatalogoProducto->id_tipo_mh;
													$id_presentacion        = $datosCatalogoProducto->id_presentacion;
													$id_tc_producto         = $datosCatalogoProducto->id_tc_producto;
												}
												$DatosKardex = array(
													'id_catalogo_producto'  => $id,
													'descripcion'           => $descripcion,
													'cantidad'              => $cantidad,
													'precio_neto'           => $precio_neto, //precio del servicio,
													'venta_neta'            => $venta_neta, // precioServicio  * cantidad
													'id_tipo_venta'         => $id_tipo_venta, //1.ventas propias, 2.ventas a terceros, 3.ventas_ajenas
													'id_tipo_mh'            => $id_tipo_mh, //afecto o gravado, excento, no sujeto
													'id_tipo_comprobante'   => $idTipoComprobante //1.CCF, 2.FCF, 3.NCR, 4.FEX

												);

												// var_dump($DatosKardex);
												$this->guardar_detalle_venta($idEncaComprobante, $exento, $tasaCero, $percep1, $DatosKardex);
											}


											//$cargo_seguro = $catalogos_a_Facturar->cargo_seguro;
											if ($catalogos_a_Facturar->N48 > 0) {
												$precio_neto  = $catalogos_a_Facturar->N48;
												$venta_neta   = ($cantidad * $precio_neto);
												$idCatalogoProducto = '33';
												// se busca la informacion del producto
												$DatosCatalogoProducto =  $this->Importacion_Model->obtenerDatosCatalogoProductos($idCatalogoProducto);
												foreach ($DatosCatalogoProducto as $key => $datosCatalogoProducto) {
													$id                     = $datosCatalogoProducto->id;
													$descripcion            = $datosCatalogoProducto->descripcion;
													$id_clasificacion_inv   = $datosCatalogoProducto->id_clasificacion_inv;
													$id_tipo_venta          = $datosCatalogoProducto->id_tipo_venta;
													$id_tipo_mh             = $datosCatalogoProducto->id_tipo_mh;
													$id_presentacion        = $datosCatalogoProducto->id_presentacion;
													$id_tc_producto         = $datosCatalogoProducto->id_tc_producto;
												}
												$DatosKardex = array(
													'id_catalogo_producto'  => $id,
													'descripcion'           => $descripcion,
													'cantidad'              => $cantidad,
													'precio_neto'           => $precio_neto, //precio del servicio,
													'venta_neta'            => $venta_neta, // precioServicio  * cantidad
													'id_tipo_venta'         => $id_tipo_venta, //1.ventas propias, 2.ventas a terceros, 3.ventas_ajenas
													'id_tipo_mh'            => $id_tipo_mh, //afecto o gravado, excento, no sujeto
													'id_tipo_comprobante'   => $idTipoComprobante //1.CCF, 2.FCF, 3.NCR, 4.FEX

												);

												//var_dump($DatosKardex);
												$this->guardar_detalle_venta($idEncaComprobante, $exento, $tasaCero, $percep1, $DatosKardex);
											}


											//$cargo_entrega_local        = $catalogos_a_Facturar->insp_no_intrusiva;
											if ($catalogos_a_Facturar->cargo_seguro > 0) {
												$precio_neto  = $catalogos_a_Facturar->cargo_seguro;
												$venta_neta   = ($cantidad * $precio_neto);
												$idCatalogoProducto = '82';
												// se busca la informacion del producto
												$DatosCatalogoProducto =  $this->Importacion_Model->obtenerDatosCatalogoProductos($idCatalogoProducto);
												foreach ($DatosCatalogoProducto as $key => $datosCatalogoProducto) {
													$id                     = $datosCatalogoProducto->id;
													$descripcion            = $datosCatalogoProducto->descripcion;
													$id_clasificacion_inv   = $datosCatalogoProducto->id_clasificacion_inv;
													$id_tipo_venta          = $datosCatalogoProducto->id_tipo_venta;
													$id_tipo_mh             = $datosCatalogoProducto->id_tipo_mh;
													$id_presentacion        = $datosCatalogoProducto->id_presentacion;
													$id_tc_producto         = $datosCatalogoProducto->id_tc_producto;
												}
												$DatosKardex = array(
													'id_catalogo_producto'  => $id,
													'descripcion'           => $descripcion,
													'cantidad'              => $cantidad,
													'precio_neto'           => $precio_neto, //precio del servicio,
													'venta_neta'            => $venta_neta, // precioServicio  * cantidad
													'id_tipo_venta'         => $id_tipo_venta, //1.ventas propias, 2.ventas a terceros, 3.ventas_ajenas
													'id_tipo_mh'            => $id_tipo_mh, //afecto o gravado, excento, no sujeto
													'id_tipo_comprobante'   => $idTipoComprobante //1.CCF, 2.FCF, 3.NCR, 4.FEX

												);

												//var_dump($DatosKardex);
												$this->guardar_detalle_venta($idEncaComprobante, $exento, $tasaCero, $percep1, $DatosKardex);
											}

											// se ingresa a kardex  como  tramite de visado
											//$cargo_permiso = $catalogos_a_Facturar->insp_no_intrusiva;
											if ($catalogos_a_Facturar->insp_no_intrusiva > 0) {
												$precio_neto  = $catalogos_a_Facturar->insp_no_intrusiva;
												$venta_neta   = ($cantidad * $precio_neto);
												$idCatalogoProducto = '84';
												// se busca la informacion del producto
												$DatosCatalogoProducto =  $this->Importacion_Model->obtenerDatosCatalogoProductos($idCatalogoProducto);
												// var_dump('los datos de inspeccioj no intrusiva son #################');
												//var_dump($DatosCatalogoProducto);

												foreach ($DatosCatalogoProducto as $key => $datosCatalogoProducto) {
													$id                     = $datosCatalogoProducto->id;
													$descripcion            = $datosCatalogoProducto->descripcion;
													$id_clasificacion_inv   = $datosCatalogoProducto->id_clasificacion_inv;
													$id_tipo_venta          = $datosCatalogoProducto->id_tipo_venta;
													$id_tipo_mh             = $datosCatalogoProducto->id_tipo_mh;
													$id_presentacion        = $datosCatalogoProducto->id_presentacion;
													$id_tc_producto         = $datosCatalogoProducto->id_tc_producto;
												}
												$DatosKardex = array(
													'id_catalogo_producto'  => $id,
													'descripcion'           => $descripcion,
													'cantidad'              => $cantidad,
													'precio_neto'           => $precio_neto, //precio del servicio,
													'venta_neta'            => $venta_neta, // precioServicio  * cantidad
													'id_tipo_venta' 		=> $id_tipo_venta, //1.ventas propias, 2.ventas a terceros, 3.ventas_ajenas
													'id_tipo_mh' 			=> $id_tipo_mh, //afecto o gravado, excento, no sujeto
													'id_tipo_comprobante'   => $idTipoComprobante //1.CCF, 2.FCF, 3.NCR, 4.FEX

												);
												// var_dump('************************ guardando inspeccion no instrusica******************');
												//  var_dump($DatosKardex);
												//$this->guardar_detalle_venta($idEncaComprobante, $exento, $tasaCero, $percep1, $DatosKardex);
												$this->guardar_detalle_venta($idEncaComprobante, $exento, $tasaCero, $percep1, $DatosKardex);
											}



											// $cargo_tramite_aduanal = $catalogos_a_Facturar->fuel;
											if ($catalogos_a_Facturar->fuel > 0) {
												$precio_neto  = $catalogos_a_Facturar->fuel;
												$venta_neta   = ($cantidad * $precio_neto);
												$idCatalogoProducto = '123';
												// se busca la informacion del producto
												$DatosCatalogoProducto =  $this->Importacion_Model->obtenerDatosCatalogoProductos($idCatalogoProducto);
												foreach ($DatosCatalogoProducto as $key => $datosCatalogoProducto) {
													$id                     = $datosCatalogoProducto->id;
													$descripcion            = $datosCatalogoProducto->descripcion;
													$id_clasificacion_inv   = $datosCatalogoProducto->id_clasificacion_inv;
													$id_tipo_venta          = $datosCatalogoProducto->id_tipo_venta;
													$id_tipo_mh             = $datosCatalogoProducto->id_tipo_mh;
													$id_presentacion        = $datosCatalogoProducto->id_presentacion;
													$id_tc_producto         = $datosCatalogoProducto->id_tc_producto;
												}
												$DatosKardex = array(
													'id_catalogo_producto'  => $id,
													'descripcion'           => $descripcion,
													'cantidad'              => $cantidad,
													'precio_neto'           => $precio_neto, //precio del servicio,
													'venta_neta'            => $venta_neta, // precioServicio  * cantidad
													'id_tipo_venta'         => $id_tipo_venta, //1.ventas propias, 2.ventas a terceros, 3.ventas_ajenas
													'id_tipo_mh'            => $id_tipo_mh, //afecto o gravado, excento, no sujeto
													'id_tipo_comprobante'   => $idTipoComprobante //1.CCF, 2.FCF, 3.NCR, 4.FEX

												);
												// var_dump('Datos  a facturar en kardex');

												// var_dump($DatosKardex);
												$this->guardar_detalle_venta($idEncaComprobante, $exento, $tasaCero, $percep1, $DatosKardex);
											}


											//para  facturar el  nuevo servicio de cuenta  ajena;
											if ($catalogos_a_Facturar->iva_adn > 0) {
												$precio_neto  = $catalogos_a_Facturar->iva_adn;
												$venta_neta   = ($cantidad * $precio_neto);
												$idCatalogoProducto = '145';
												// se busca la informacion del producto
												$DatosCatalogoProducto =  $this->Importacion_Model->obtenerDatosCatalogoProductos($idCatalogoProducto);
												foreach ($DatosCatalogoProducto as $key => $datosCatalogoProducto) {
													$id                     = $datosCatalogoProducto->id;
													$descripcion            = $datosCatalogoProducto->descripcion;
													$id_clasificacion_inv   = $datosCatalogoProducto->id_clasificacion_inv;
													$id_tipo_venta          = $datosCatalogoProducto->id_tipo_venta;
													$id_tipo_mh             = $datosCatalogoProducto->id_tipo_mh;
													$id_presentacion        = $datosCatalogoProducto->id_presentacion;
													$id_tc_producto         = $datosCatalogoProducto->id_tc_producto;
												}
												$DatosKardex = array(
													'id_catalogo_producto'  => $id,
													'descripcion'           => $descripcion,
													'cantidad'              => $cantidad,
													'precio_neto'           => $precio_neto, //precio del servicio,
													'venta_neta'            => $venta_neta, // precioServicio  * cantidad
													'id_tipo_venta'         => $id_tipo_venta, //1.ventas propias, 2.ventas a terceros, 3.ventas_ajenas
													'id_tipo_mh'            => $id_tipo_mh, //afecto o gravado, excento, no sujeto
													'id_tipo_comprobante'   => $idTipoComprobante //1.CCF, 2.FCF, 3.NCR, 4.FEX

												);
												// var_dump('Datos  a facturar en kardex');

												// var_dump($DatosKardex);
												$this->guardar_detalle_venta($idEncaComprobante, $exento, $tasaCero, $percep1, $DatosKardex);
											}



											// Generando los montos totales de los encabezados del comprobante
											$DatossumKardex = $this->Ventas_Model->calcularTotalesventa($idEncaComprobante);
											// pasando valores a variables para realizar calculos
											$totalVentaNetaAjena        = $DatossumKardex[0]->totalKardexCuentaAjena;
											$totalKardexGravado         = $DatossumKardex[0]->totalKardexGravado;
											$truncar = 10 ** 2;
											// $totalKardexIvaGravado      =   intval(($DatossumKardex[0]->totalKardexGravado *0.13) * $truncar) / $truncar;
											$totalKardexIvaGravado      = $DatossumKardex[0]->totalKardexIvaGravado;
											// $totalKardexIvaGravado = parseFloat(data[0].totalKardexIvaGravado;
											$totalKardexExento          = $DatossumKardex[0]->totalKardexExento;
											$totalKardexExportacion     = $DatossumKardex[0]->totalKardexExportacion;
											$totalKardexNosujeto        = $DatossumKardex[0]->totalKardexNosujeto;
											//   almacena el   subtotal de la  venta
											$subTotal = round(($totalKardexGravado + $totalKardexIvaGravado + $totalKardexExento + $totalKardexExportacion + $totalKardexNosujeto), 2);
											//  calculando si para el  cliente se le hace retencion
											if ($percep1 == 'SI') {
												if ($totalKardexGravado >= 100) {
													$retencionUnoPorCiento = round(($totalKardexGravado * 0.01), 2);
												}
											} else {
												$retencionUnoPorCiento = 0.00;
											}
											$totalLiquidocobrar1PorCiento = ($subTotal - $retencionUnoPorCiento);
											//  almacena el   total  de l
											$totalKardexVentaTotal = number_format(($totalLiquidocobrar1PorCiento + $totalVentaNetaAjena), 2);
											$totalKardexVentaTotal =   str_replace(',', '', $totalKardexVentaTotal);
											$nombrecliente         = '';
											$documentopresentar    = '';
											// generando el array para actualizar el encabezado del  comprobante
											$data = array(
												'id_empresa'				=> $idEmpresa,
												'id_sucursal_empresa'		=> $idSucursal,
												'id_cliente'				=> $idCliente,
												'nombrecliente'            => $nombrecliente,
												'documentopresentar'        => $documentopresentar,
												'id_destinatario'			=> $id_destinatario,
												'id_tipo_comprobante'		=> $idTipoComprobante,
												'numero_comprobante'		=> $numero_comprobante,
												'comprobante_afecto'		=> $numero_comprobante,
												'id_comprob_afecto'         => $id_comprob_afecto,
												'serie_factura'		        => $serie_factura,
												'ot_numero'					=> $ot_numero,
												'fecha_comprobante'			=> $fecha_comprobante,
												'total_exento'				=> $totalKardexExento,
												'total_exportacion'			=> $totalKardexExportacion,
												'total_no_sujeto'			=> $totalKardexNosujeto,
												'total_gravado'				=> $totalKardexGravado,
												'total_iva_gravado'			=> $totalKardexIvaGravado,
												'sub_total_ventas'			=> $subTotal,
												'retencion_1_por_ciento'	=> $retencionUnoPorCiento,
												'sub_total_cuenta_ajena'	=> $totalVentaNetaAjena,
												'venta_total'				=> $totalKardexVentaTotal,
												'id_tipo_evento'            => $id_tipo_evento,
												'fact_manual'               => $fact_manual,
											);

											//var_dump('totales de factura............................................');
											if ($totalKardexVentaTotal > 0) {
												$datoupcabecera  =  $this->Ventas_Model->guardar_enca($data, $idEncaComprobante);
												if ($datoupcabecera !=  0) {
													// segmento para actualizar en  guias_import_ss  los warehouse que ya se procesaron
													//actualizar el control de correlativos
													$this->Importacion_Model->actualizar_correlativos($idEmpresa, $idSucursal, $idTipoComprobante, $nuevoCorrelativo);

													$procesado = 1; // variable que indicara que los warehouse ya fueron  procesados
													//  funcion  para  obtener las  guias a actualizar
													//  arrar  de datos  de facturacion
													$data =  array(
														'id_tipo_comprobante'    => $idTipoComprobante,
														'numero_comprobante'     => $numero_comprobante,
														'fecha_comprobante' 	 => $fecha_comprobante,
														'facturacion_pendiente'  => 1,
														'seleccionado'           => 0
													);
													// echo  'modificar los datos de las  guias' se  ponen como facturado;
													$datosdfacturacion = $this->Exportacion_Model->asigna_datosdfacturacion($idEncaComprobante, $idCliente, $warehouse, $data);
													//  obtenermos  las  guias  facturadas para  casos  MA
													if ($datosdfacturacion != 0) {
														$guias_g  =   $this->Exportacion_Model->guias_expfacturadasFE($idEncaComprobante);
														if (!empty($guias_g)) {
															foreach ($guias_g as $gma) {
																if ($gma->ma   == 1) {
																	$tracking_number = $gma->guia_no;
																	// echo 'actualizando para  MA' . '<br>';
																	$guiaMAupdate = $this->Exportacion_Model->update_facturadoUSPS($tracking_number);
																}
															}
														}
													}
													# generamos la partida de venta
													$codigopartida   = '008';
													//$generarpartida  = new Generarpartidas();
													//$generarpartida->generarpartida_venta($idTipoComprobante, $idEncaComprobante, $codigopartida, $fecha_comprobante);
													# fin de la generacion de partida de venta

													$id_tipo_pago    = 2;
													if ($guia_ncancelada = 0) { // indica que ya se cancelaron  todas las guias de la factura
														if ($id_tipo_pago == 1) { //  el cliente es de contado
															//  quitar la bandera de incluir cobro
															$incluir_cobro = 0; //  quitamos la marca de cobrado /
															$this->Ventas_Model->incluir_cobro($incluir_cobro, $idEncaComprobante);
														}
													}
													$valor_abono        = 0.00;
													$valor_cargo        = $totalKardexVentaTotal;
													$comprobante_afecto = $numero_comprobante;
													$tipo_transac       =  'c';                                    //}

													echo 'ingresando al  proceso de crear cuenta por cobrar!!!!!!!!!!';

													if ($id_tipo_pago ==   2) {
														echo 'ingresando al  proceso de crear cuenta por cobrar';
														$dataCxC = array(
															'id_cliente'             => $idCliente,
															'id_destinatario'        => $idCliente,
															'id_empresa'             => $idEmpresa,
															'id_comprob_venta'       => $idEncaComprobante,
															'num_comrpobante'        => $numero_comprobante,
															'id_tipo_comprobante'    => $idTipoComprobante,
															'id_comprob_afecto'      => $idEncaComprobante,
															'valor_cargo'            => $valor_cargo,
															'valor_abono'            => $valor_abono,
															'fecha_emision'          => $fecha_comprobante,
															'tipo_transac'          => $tipo_transac
														);
														//var_dump($dataCxC);
														//var_dump('proceso  de almacenado en  det_cxc');
														$idcxc = $this->Ventas_Model->buscarIdCxC($idEncaComprobante);
														// siempre se genera  ya que crea la cuenta por cobrar despues del  proceso  de facturacion
														$this->Ventas_Model->guardar_detalle_cxc($dataCxC, $idcxc->id);
													}
												}
											}
										}
									}
									# FIN DEL SEGMENTO  DE LA  GENERACION  DE LA  VENTA
								}
							}
							//  recorriendo  los datos del detalle de la guia para obtener a que cliente se le va a facturar
						}
						if ($guiaMAupdate != 0) {
							$id_venta = 0;
							$datos    = $this->DTES_Model->datguia_exp($warehouse);
							if (!empty($datos)) {
								$id_venta = $datos->id_venta;
								//   con ese  id  obtenemos los datos de la  venta
								if ($id_venta !=  0) {
									$datosv =    $this->DTES_Model->datventa($id_venta);
									if (!empty($datosv)) {
										// var_dump($datosv);
										$id = $datosv->id;
										$id_tipo_evento = $datosv->id_tipo_evento;
										$cod_generacion = $datosv->cod_generacion;
										$resultadoERR   = '';
										$id_cliente     =  12165;
										//echo   'eñ  codigo  de  generacion  es ' .  $cod_generacion;
										if ($cod_generacion ==   null || $cod_generacion ==   null  || $cod_generacion == '') {
											$proceso  = 1;
											$facturaelectronica  = new Facturaelectronica();
											$resulgeneracio      =   $facturaelectronica->generarfacturaelectronica($id_tipo_evento, $id);
											$resultadoERR =   $facturaelectronica->CtrlError($resulgeneracio, $id_cliente, $proceso, $id);
										} else {
											echo  'ya se  genero facura  electronica';
										}
									}
								}
							} else {
								echo   'Esta guia  no se encuentra descargada en  el  sistema de facturacion';
							}
						}
					} else {
						echo  'esta guia ya se  proceso!!!!!!!!!!!!!!!!!!!!';
					}
				}
			}
		}
		# FIN DEL SEGMENTO PARA LA FACTURACION  ELECTRONICA

		//  actualizar  estatus  de  manifiesto 
		//echo 'los  daos para  actualizar son  ' .  $id_estatus .  ' hw'. $warehouse; 

	}


	public function estados_post()
	{


		date_default_timezone_set("America/El_Salvador");
		$fecha     = date('Y-m-d H:i:s');
		$fecha_tmp = DateTime::createFromFormat('Y-m-d H:i:s', $fecha);
		$fecha_iso = $fecha_tmp->format(DateTime::ATOM);

		$item        = 1;
		$warehouse   = "";
		$id_estatus  = "";
		$casillero   = "100";
		$id_pais     = 1;
		$pod_nombre  = "";
		$pod_valor   = "";
		$document_name = "";
		$ruta        = null;

		// ==== 1) Leer JSON crudo ====
		$raw = file_get_contents('php://input', true);
		$row = json_decode($raw, true);

		if (!$row || !isset($row['guia']) || !isset($row['codigo']) || !isset($row['estatus'])) {
			return $this->response([
				'err'  => true,
				'msg'  => 'JSON inválido o campos requeridos faltantes (guia, codigo, estatus)',
			], 400);
		}

		// Datos que vienen del cliente (APG / quien sea)
		$warehouse_input = $row['guia'];        // guía que te envían (internal_tracking)
		$id_estatus      = $row['codigo'];      // código de estatus
		$nombre_estatus  = $row['estatus'];     // texto de estatus
		$observaciones   = $row['observaciones'] ?? '';
		$devolucion      = $row['devolucion'] ?? 0;

         // ==== 2) Buscar la guía en tu sistema ====
		/** Recibe la guia de xpreess (internal_tracking) y luego busca la guia de Mailamericas(tracking_number) */
		$query = $this->Manifiesto_model->get_tracking($warehouse_input);

		if (empty($query)) {
			return $this->response([
				'err'  => true,
				'msg'  => 'Guía no encontrada en el sistema',
			], 404);
		}

		// Datos de la guía / empresa
		$guiaRow  = $query[0];
		$cliente  = $this->Manifiesto_model->datos_empresa($guiaRow->id_empresa);

		$codigoEmpresa   = $guiaRow->id_empresa; // se utiliza para determinar si se crea FEL o no
		$id_tokencliente = $cliente[0]->id_tokencliente ?? null;
		$token_cliente   = $cliente[0]->token_cliente   ?? null;

		// Si el método es "E", usas el tracking_number de MA para reportar
		if ($guiaRow->metodo == "E") {
			$warehouse = $guiaRow->tracking_number;
		} else {
			$warehouse = $warehouse_input;
		}

		// ==== 3) Descripción / ajustes especiales ====
		if ($id_estatus == 16) {
			$description = $nombre_estatus . ": " . $observaciones;
		} else {
			$description = $nombre_estatus;
		}

		// Si llega estatus 15 (entregado) y viene devolucion=1, lo conviertes a FP-2
		if ($id_estatus == 15 && ($devolucion == 1 || $devolucion === true)) {
			$id_estatus   = "FP-2";
			$description  = "Entregado en dirección de retorno";
		}

		// ==== 4) Procesar POD (imagen) si aplica ====
		// Solo si el estatus es 15 y viene arreglo "pod"
		if ($id_estatus == 15 && !empty($row['pod']) && is_array($row['pod'])) {
			$ruta_base = "/var/www/stargateserp/documentos_ma/";
			$url_base  = "https://stargateserp.com/documentos_ma/";

			// foreach ($row['pod'] as $detalle) {
			//     $pod_nombre = $detalle['nombre'] ?? '';
			//     $pod_valor  = $detalle['valor']  ?? '';

			//     $img = str_replace('data:image/png;base64,', '', $pod_valor);
			//     $img = str_replace(' ', '+', $img);
			//     $data1 = base64_decode($img);

			//     $document_name = uniqid() . '.png';
			//     $file = $ruta_base . $document_name;

			//     file_put_contents($file, $data1);

			//     // Para MailAmericas y para guardar en tu tabla
			//     $ruta = $url_base . $document_name;
			// }
			$ruta = null;
		} else {
			$ruta = null;
		}

		// ==== 5) Guardar estatus local ====
		$this->Manifiesto_model->update_estatus($warehouse, $id_estatus); //guardar estatus en tablas locales

		$datos = array(
			'item'        => $item,
			'fecha'       => $fecha,
			'warehouse'   => $warehouse,
			'id_estatus'  => $id_estatus,
			'casillero'   => $casillero,
			'id_pais'     => $id_pais,
			'pod_nombre'  => $pod_nombre,
			'pod_valor'   => "",                // ya no guardamos el base64
			'json_string' => $raw,              // guardamos el json completo que vino
			'ruta_foto'   => $ruta,
			'fechaiso'    => $fecha_iso
		);

		$this->Manifiesto_model->guardar_estatus($datos);
		//$this->DTES_Model->updatetracking_usps($id_estatus, $warehouse);

		// ==== 6) Armar payload para MailAmericas (OUTBOX) ====
		$payload_ma = [
			"tracking"       => $warehouse,          // mismo tracking que usas en MA
			"date"           => $fecha_iso,          // ISO-8601
			"description"    => $description,
			"city"           => "CCO",
			"received_by"    => null,
			"relationship"   => null,
			"delivery_proof" => ($id_estatus == 15 && !empty($ruta)) ? [$ruta] : [],
			"gcs"            => "",
			"details"        => ""
		];

		// ==== 7) Encolar SIEMPRE el evento para MA (envío asincrónico) ====
		// Ya NO hacemos cURL directo aquí, solo encolamos.
		$this->Entrega_model->enqueueOutboxma(
			$warehouse,
			(int)$id_estatus,
			$nombre_estatus,
			$fecha,
			$payload_ma,
			null,  // last_error inicial
			5      // retryMinutes (o lo que uses por defecto)
		);

		if ($codigoEmpresa == 108 || $codigoEmpresa == 109 || $codigoEmpresa == 100) {
			if ($id_estatus == 15) {
				$data_fel = [
					"tracking"       => $warehouse,          // mismo tracking que usas en MA
					"id_estatus"           => $id_estatus,          // ISO-8601
					"facturado"        => 0
				];
				$this->Entrega_model->facturar($data_fel);
			}
		}

		// ==== 10) Responder RÁPIDO al cliente que te consume ====
		return $this->response([
			"err"          => false,
			"msg"          => "Evento recibido y encolado para procesamiento",
			"warehouse"    => $warehouse_input,   // la guía que te mandaron
			"tracking_ma"  => $warehouse,         // la guía que usas con MailAmericas (puede ser igual o distinta)
			"estatus"      => $id_estatus,
			"descripcion"  => $description,
			"fecha"        => $fecha,
			"fechaiso"     => $fecha_iso
		], 200);

		// (Opcional) Podrías también encolar aquí un job de facturación FEL si lo deseas,
		// en vez de ejecutarlo inline.

		// ==== 8) LOG simple opcional para saber que se encoló ====
		// Si quieres mantener tu tabla log_ma, puedes registrar que se encoló:
		/*
			$this->Manifiesto_model->guardar_logma([
				"respuesta"       => 'ENQUEUED_NOT_SENT',
				"tracking_number" => $warehouse,
				"descripcion"     => $description,
				"fecha"           => $fecha,
				"fechaiso"        => $fecha_iso
			]);
			*/

		// ==== 9) (Tu bloque gigante de FEL sigue intacto y NO se ejecuta) ====







		$warehouse = '';
		# SEGMENTO PARA LA FACTURACION ELECTRONICA
		if ($warehouse != '') {
			if ($id_estatus == 15) {
				ini_set('display_errors', 1);
				ini_set('display_startup_errors', 1);
				error_reporting(E_ALL);
				$ejecuta    = false;
				if ($ejecuta == true) {
					$_SESSION["user_id"] = date("his");
					$seleccionado        = 1;
					$ma                  = 1;
					$guiaMAupdate        = 0;
					$idEncaComprobante   = 0;
					$datoupcabecera      = 0;
					$datosdfacturacion   = 0;
					$guiaSeleccionada    = 0;
					$datosguiaMA         = 0;
					$datosguiaMA         =  $this->getItemsForGma($warehouse); // DESCARGAMOS LA GUIA
					//echo  'la guia  maRetorno'. $datosguiaMA;
					# SEGMENTO PARA LA EJECUCION  DE  SELECCION Y OTROS PROCESOS  SI SE  DESCARGA LA GUIA
					if ($datosguiaMA != 0) {
						$guiaSeleccionada = $this->seleccionar_guia($warehouse, $seleccionado, $ma);
						if ($guiaSeleccionada == 0) { // 0  indica que la guia  no  tuvo ninguna incosistencia y se selecciono
							# SEGMENTO  PARA LA  GENERACION  DE LA  VENTA
							// echo  'Ingresando al  proceso de facturacion';
							$clasificacioFiscal     = 0;   // 1- grande  2-mediano  3- otros
							$tipoContribuyente      = 0;   // 1- gravado 2- excento 3- tasa cero 4-sujeto excluido
							$sujetoRetencion        = 0;   // solo si la   si la venta gravada es >=100  y es 1- grande ciontribuyente
							$idTipoComprobante      = 0;   // 1-CCF 2-FCf 3- NCR(nota credito) 4- fex(factura exportacion) 5- ANF (anulacion factura consumidor final)
							$numero_comprobante     = 0;
							$fecha_comprobante      = date('Y-m-d'); // cambiar por la indicada
							$id_tipo_transac        = '';
							$idEmpresa              = 0;
							$idSucursal             = 0;
							$idCliente              = 0;
							$idDestinatario         = 0;
							$idCatalogoProducto     = 0;
							$ot_numero              = 0; // orden de trabajo
							// Capturando valriables de session
							$idEmpresa              = 47; //$_SESSION["empresa_id"];// se cambia para variable de inicion de sesion
							$idSucursal             = '1'; // factura para  global cargo, se direcciona todo para la sucursal san francisco
							$idEncaComprobante      = 0;
							// para calculos
							$cantidad               = 1; //  evaluar
							$precio_neto            = 0; //  es el valor del  servicio
							$venta_neta             = 0;
							$id_tipo_evento         = 2; // 2- exportacion
							$fact_manual            =  0;
							$id_comprob_afecto      = null;
							$DatosClientes        = $this->Exportacion_Model->obtenerClienteAgrupadosMA(); // se obtien los datos de la guias(el detalle)
							if ($DatosClientes ==  null) {
								echo 0; // variable que indica si existen datos a facturar
								echo 'NO HAY DATOS A FACTURAR '; // variable que indica si existen datos a facturar
							} else {
								// echo 1;
								foreach ($DatosClientes as $datosClientes) { //  recorremos el numero  de  clientes  pendientes  a facturar
									$idCliente   = $datosClientes->id;
									//var_dump( 'el Cliente a procesar !!!!!!!!!!!!!!!!!!!!!!! '.$idCliente) ;
									$destinatarios = $this->Conf_model->get_destinatario($idCliente);
									if (!empty($destinatarios)) {
										$id_destinatario = $destinatarios[0]->id;
									} else {
										$id_destinatario =  1033;
									}
									$clasificacioFiscal = $datosClientes->id_clasificacion_fiscal;
									// 1 GRANDE 2- MEDIANO 3- OTRO
									$id_tipo_pago =  $datosClientes->id_tipo_pago;
									$tipoContribuyente  = $datosClientes->id_tipo_contribuyente;
									if ($tipoContribuyente == '1') { // GRAVADO
										$tasaCero = 'NO';
										$exento   = 'NO'; // SI ES NO INDICA QUE SE LE APLICA IVA
									} elseif ($tipoContribuyente == '2') { // EXCENTO
										$tasaCero = 'NO';
										$exento   = 'SI'; // NO SE SE LE APLICA IVA
									} elseif ($tipoContribuyente == '3') { // TASA CERO
										$tasaCero = 'SI';
										$exento   = 'NO';
									}

									$sujetoRetencion    = $datosClientes->sujeto_ret_1;
									if ($sujetoRetencion == 'S'  || $sujetoRetencion == 's') { // EL  CLIENTE ESTA SUJETO A RETENSION
										$percep1 = 'SI';
									} else {
										$percep1 = 'NO';
									}
									$idTipoComprobante  = $datosClientes->id_tipo_comprobante;
									//  var_dump( 'el tipo de contribuyecte es'.$tipoContribuyente.'el tipo de comprobante a crear es' . $idTipoComprobante . 'valor de tasa cero'.$tasaCero.'el valor de excento' .$exento);
									$correlativoFactura =  $this->Importacion_Model->obtenerCorrelativoCom($idEmpresa, $idSucursal, $idTipoComprobante);
									// var_dump($correlativoFactura);
									$numero_comprobante =  $correlativoFactura->correlativo;
									// se incrementa el correlativo de factura
									$serie_factura      =  $correlativoFactura->serie;
									$nuevoCorrelativo   = strval($numero_comprobante + 1);
									// var_dump('el nuevo correlativo sera '.$nuevoCorrelativo);
									// 1- generar un encabezado con el  idautomatico
									$data = array(
										'id_empresa' 		  => $idEmpresa,
										'id_sucursal_empresa' => $idSucursal,
										'id_cliente'		  => $idCliente,
										'id_destinatario'	  => $id_destinatario,
										'id_tipo_comprobante' => $idTipoComprobante,
										'numero_comprobante'  => $numero_comprobante,
										'comprobante_afecto'  => $numero_comprobante,
										'serie_factura'		  => $serie_factura,
										'fecha_comprobante'   => $fecha_comprobante,
									);
									// 1- llamar la funcion de almacenar encabezado generar el espacio en la tabla
									$idEncaComprobante  = $this->Ventas_Model->guardar_venta($data);
									//$datos_encabezado  = $this->Importacion_Model->obtener_id_encabezado_venta($idEmpresa, $idSucursal, $idCliente, $idTipoComprobante, $numero_comprobante, $fecha_comprobante);
									//$idEncaComprobante = $datos_encabezado->id;
									$seleccionado      = 1;
									$Catalogos_a_Facturar =  $this->Exportacion_Model->obtner_valores_detalle_GuiaMA($warehouse);
									if (!empty($Catalogos_a_Facturar)) {

										foreach ($Catalogos_a_Facturar as $catalogos_a_Facturar) {
											// obtener la descripcion del servicio por catalago de producto
											// catalogos a facturar  cargo basico, inspeccion no intrusiva, fuel, seguro


											//$cargo_corte_guia = $catalogos_a_Facturar->D15;

											if ($catalogos_a_Facturar->D15 > 0) {
												$precio_neto  = $catalogos_a_Facturar->D15;
												$venta_neta   = ($cantidad * $precio_neto);
												$idCatalogoProducto = '31';
												// se busca la informacion del producto
												$DatosCatalogoProducto =  $this->Importacion_Model->obtenerDatosCatalogoProductos($idCatalogoProducto);
												foreach ($DatosCatalogoProducto as $key => $datosCatalogoProducto) {
													$id                     = $datosCatalogoProducto->id;
													$descripcion            = $datosCatalogoProducto->descripcion;
													$id_clasificacion_inv   = $datosCatalogoProducto->id_clasificacion_inv;
													$id_tipo_venta          = $datosCatalogoProducto->id_tipo_venta;
													$id_tipo_mh             = $datosCatalogoProducto->id_tipo_mh;
													$id_presentacion        = $datosCatalogoProducto->id_presentacion;
													$id_tc_producto         = $datosCatalogoProducto->id_tc_producto;
												}
												$DatosKardex = array(
													'id_catalogo_producto'  => $id,
													'descripcion'           => $descripcion,
													'cantidad'              => $cantidad,
													'precio_neto'           => $precio_neto, //precio del servicio,
													'venta_neta'            => $venta_neta, // precioServicio  * cantidad
													'id_tipo_venta'         => $id_tipo_venta, //1.ventas propias, 2.ventas a terceros, 3.ventas_ajenas
													'id_tipo_mh'            => $id_tipo_mh, //afecto o gravado, excento, no sujeto
													'id_tipo_comprobante'   => $idTipoComprobante //1.CCF, 2.FCF, 3.NCR, 4.FEX

												);

												// var_dump($DatosKardex);
												// $this->Exportacion_Controller->guardar_detalle_venta($idEncaComprobante, $exento, $tasaCero, $percep1, $DatosKardex);
												$this->guardar_detalle_venta($idEncaComprobante, $exento, $tasaCero, $percep1, $DatosKardex);
											}


											//$argo_manejo_y_transp = $catalogos_a_Facturar->argo_manejo_y_transp;
											if ($catalogos_a_Facturar->N15 > 0) {
												$precio_neto  = $catalogos_a_Facturar->N15;
												$venta_neta   = ($cantidad * $precio_neto);
												$idCatalogoProducto = '32';
												// se busca la informacion del producto
												$DatosCatalogoProducto =  $this->Importacion_Model->obtenerDatosCatalogoProductos($idCatalogoProducto);
												foreach ($DatosCatalogoProducto as $key => $datosCatalogoProducto) {
													$id                     = $datosCatalogoProducto->id;
													$descripcion            = $datosCatalogoProducto->descripcion;
													$id_clasificacion_inv   = $datosCatalogoProducto->id_clasificacion_inv;
													$id_tipo_venta          = $datosCatalogoProducto->id_tipo_venta;
													$id_tipo_mh             = $datosCatalogoProducto->id_tipo_mh;
													$id_presentacion        = $datosCatalogoProducto->id_presentacion;
													$id_tc_producto         = $datosCatalogoProducto->id_tc_producto;
												}
												$DatosKardex = array(
													'id_catalogo_producto'  => $id,
													'descripcion'           => $descripcion,
													'cantidad'              => $cantidad,
													'precio_neto'           => $precio_neto, //precio del servicio,
													'venta_neta'            => $venta_neta, // precioServicio  * cantidad
													'id_tipo_venta'         => $id_tipo_venta, //1.ventas propias, 2.ventas a terceros, 3.ventas_ajenas
													'id_tipo_mh'            => $id_tipo_mh, //afecto o gravado, excento, no sujeto
													'id_tipo_comprobante'   => $idTipoComprobante //1.CCF, 2.FCF, 3.NCR, 4.FEX

												);

												// var_dump($DatosKardex);
												$this->guardar_detalle_venta($idEncaComprobante, $exento, $tasaCero, $percep1, $DatosKardex);
											}


											//$cargo_seguro = $catalogos_a_Facturar->cargo_seguro;
											if ($catalogos_a_Facturar->N48 > 0) {
												$precio_neto  = $catalogos_a_Facturar->N48;
												$venta_neta   = ($cantidad * $precio_neto);
												$idCatalogoProducto = '33';
												// se busca la informacion del producto
												$DatosCatalogoProducto =  $this->Importacion_Model->obtenerDatosCatalogoProductos($idCatalogoProducto);
												foreach ($DatosCatalogoProducto as $key => $datosCatalogoProducto) {
													$id                     = $datosCatalogoProducto->id;
													$descripcion            = $datosCatalogoProducto->descripcion;
													$id_clasificacion_inv   = $datosCatalogoProducto->id_clasificacion_inv;
													$id_tipo_venta          = $datosCatalogoProducto->id_tipo_venta;
													$id_tipo_mh             = $datosCatalogoProducto->id_tipo_mh;
													$id_presentacion        = $datosCatalogoProducto->id_presentacion;
													$id_tc_producto         = $datosCatalogoProducto->id_tc_producto;
												}
												$DatosKardex = array(
													'id_catalogo_producto'  => $id,
													'descripcion'           => $descripcion,
													'cantidad'              => $cantidad,
													'precio_neto'           => $precio_neto, //precio del servicio,
													'venta_neta'            => $venta_neta, // precioServicio  * cantidad
													'id_tipo_venta'         => $id_tipo_venta, //1.ventas propias, 2.ventas a terceros, 3.ventas_ajenas
													'id_tipo_mh'            => $id_tipo_mh, //afecto o gravado, excento, no sujeto
													'id_tipo_comprobante'   => $idTipoComprobante //1.CCF, 2.FCF, 3.NCR, 4.FEX

												);

												//var_dump($DatosKardex);
												$this->guardar_detalle_venta($idEncaComprobante, $exento, $tasaCero, $percep1, $DatosKardex);
											}


											//$cargo_entrega_local        = $catalogos_a_Facturar->insp_no_intrusiva;
											if ($catalogos_a_Facturar->cargo_seguro > 0) {
												$precio_neto  = $catalogos_a_Facturar->cargo_seguro;
												$venta_neta   = ($cantidad * $precio_neto);
												$idCatalogoProducto = '82';
												// se busca la informacion del producto
												$DatosCatalogoProducto =  $this->Importacion_Model->obtenerDatosCatalogoProductos($idCatalogoProducto);
												foreach ($DatosCatalogoProducto as $key => $datosCatalogoProducto) {
													$id                     = $datosCatalogoProducto->id;
													$descripcion            = $datosCatalogoProducto->descripcion;
													$id_clasificacion_inv   = $datosCatalogoProducto->id_clasificacion_inv;
													$id_tipo_venta          = $datosCatalogoProducto->id_tipo_venta;
													$id_tipo_mh             = $datosCatalogoProducto->id_tipo_mh;
													$id_presentacion        = $datosCatalogoProducto->id_presentacion;
													$id_tc_producto         = $datosCatalogoProducto->id_tc_producto;
												}
												$DatosKardex = array(
													'id_catalogo_producto'  => $id,
													'descripcion'           => $descripcion,
													'cantidad'              => $cantidad,
													'precio_neto'           => $precio_neto, //precio del servicio,
													'venta_neta'            => $venta_neta, // precioServicio  * cantidad
													'id_tipo_venta'         => $id_tipo_venta, //1.ventas propias, 2.ventas a terceros, 3.ventas_ajenas
													'id_tipo_mh'            => $id_tipo_mh, //afecto o gravado, excento, no sujeto
													'id_tipo_comprobante'   => $idTipoComprobante //1.CCF, 2.FCF, 3.NCR, 4.FEX

												);

												//var_dump($DatosKardex);
												$this->guardar_detalle_venta($idEncaComprobante, $exento, $tasaCero, $percep1, $DatosKardex);
											}

											// se ingresa a kardex  como  tramite de visado
											//$cargo_permiso = $catalogos_a_Facturar->insp_no_intrusiva;
											if ($catalogos_a_Facturar->insp_no_intrusiva > 0) {
												$precio_neto  = $catalogos_a_Facturar->insp_no_intrusiva;
												$venta_neta   = ($cantidad * $precio_neto);
												$idCatalogoProducto = '84';
												// se busca la informacion del producto
												$DatosCatalogoProducto =  $this->Importacion_Model->obtenerDatosCatalogoProductos($idCatalogoProducto);
												// var_dump('los datos de inspeccioj no intrusiva son #################');
												//var_dump($DatosCatalogoProducto);

												foreach ($DatosCatalogoProducto as $key => $datosCatalogoProducto) {
													$id                     = $datosCatalogoProducto->id;
													$descripcion            = $datosCatalogoProducto->descripcion;
													$id_clasificacion_inv   = $datosCatalogoProducto->id_clasificacion_inv;
													$id_tipo_venta          = $datosCatalogoProducto->id_tipo_venta;
													$id_tipo_mh             = $datosCatalogoProducto->id_tipo_mh;
													$id_presentacion        = $datosCatalogoProducto->id_presentacion;
													$id_tc_producto         = $datosCatalogoProducto->id_tc_producto;
												}
												$DatosKardex = array(
													'id_catalogo_producto'  => $id,
													'descripcion'           => $descripcion,
													'cantidad'              => $cantidad,
													'precio_neto'           => $precio_neto, //precio del servicio,
													'venta_neta'            => $venta_neta, // precioServicio  * cantidad
													'id_tipo_venta' 		=> $id_tipo_venta, //1.ventas propias, 2.ventas a terceros, 3.ventas_ajenas
													'id_tipo_mh' 			=> $id_tipo_mh, //afecto o gravado, excento, no sujeto
													'id_tipo_comprobante'   => $idTipoComprobante //1.CCF, 2.FCF, 3.NCR, 4.FEX

												);
												// var_dump('************************ guardando inspeccion no instrusica******************');
												//  var_dump($DatosKardex);
												//$this->guardar_detalle_venta($idEncaComprobante, $exento, $tasaCero, $percep1, $DatosKardex);
												$this->guardar_detalle_venta($idEncaComprobante, $exento, $tasaCero, $percep1, $DatosKardex);
											}



											// $cargo_tramite_aduanal = $catalogos_a_Facturar->fuel;
											if ($catalogos_a_Facturar->fuel > 0) {
												$precio_neto  = $catalogos_a_Facturar->fuel;
												$venta_neta   = ($cantidad * $precio_neto);
												$idCatalogoProducto = '123';
												// se busca la informacion del producto
												$DatosCatalogoProducto =  $this->Importacion_Model->obtenerDatosCatalogoProductos($idCatalogoProducto);
												foreach ($DatosCatalogoProducto as $key => $datosCatalogoProducto) {
													$id                     = $datosCatalogoProducto->id;
													$descripcion            = $datosCatalogoProducto->descripcion;
													$id_clasificacion_inv   = $datosCatalogoProducto->id_clasificacion_inv;
													$id_tipo_venta          = $datosCatalogoProducto->id_tipo_venta;
													$id_tipo_mh             = $datosCatalogoProducto->id_tipo_mh;
													$id_presentacion        = $datosCatalogoProducto->id_presentacion;
													$id_tc_producto         = $datosCatalogoProducto->id_tc_producto;
												}
												$DatosKardex = array(
													'id_catalogo_producto'  => $id,
													'descripcion'           => $descripcion,
													'cantidad'              => $cantidad,
													'precio_neto'           => $precio_neto, //precio del servicio,
													'venta_neta'            => $venta_neta, // precioServicio  * cantidad
													'id_tipo_venta'         => $id_tipo_venta, //1.ventas propias, 2.ventas a terceros, 3.ventas_ajenas
													'id_tipo_mh'            => $id_tipo_mh, //afecto o gravado, excento, no sujeto
													'id_tipo_comprobante'   => $idTipoComprobante //1.CCF, 2.FCF, 3.NCR, 4.FEX

												);
												// var_dump('Datos  a facturar en kardex');

												// var_dump($DatosKardex);
												$this->guardar_detalle_venta($idEncaComprobante, $exento, $tasaCero, $percep1, $DatosKardex);
											}


											//para  facturar el  nuevo servicio de cuenta  ajena;
											if ($catalogos_a_Facturar->iva_adn > 0) {
												$precio_neto  = $catalogos_a_Facturar->iva_adn;
												$venta_neta   = ($cantidad * $precio_neto);
												$idCatalogoProducto = '145';
												// se busca la informacion del producto
												$DatosCatalogoProducto =  $this->Importacion_Model->obtenerDatosCatalogoProductos($idCatalogoProducto);
												foreach ($DatosCatalogoProducto as $key => $datosCatalogoProducto) {
													$id                     = $datosCatalogoProducto->id;
													$descripcion            = $datosCatalogoProducto->descripcion;
													$id_clasificacion_inv   = $datosCatalogoProducto->id_clasificacion_inv;
													$id_tipo_venta          = $datosCatalogoProducto->id_tipo_venta;
													$id_tipo_mh             = $datosCatalogoProducto->id_tipo_mh;
													$id_presentacion        = $datosCatalogoProducto->id_presentacion;
													$id_tc_producto         = $datosCatalogoProducto->id_tc_producto;
												}
												$DatosKardex = array(
													'id_catalogo_producto'  => $id,
													'descripcion'           => $descripcion,
													'cantidad'              => $cantidad,
													'precio_neto'           => $precio_neto, //precio del servicio,
													'venta_neta'            => $venta_neta, // precioServicio  * cantidad
													'id_tipo_venta'         => $id_tipo_venta, //1.ventas propias, 2.ventas a terceros, 3.ventas_ajenas
													'id_tipo_mh'            => $id_tipo_mh, //afecto o gravado, excento, no sujeto
													'id_tipo_comprobante'   => $idTipoComprobante //1.CCF, 2.FCF, 3.NCR, 4.FEX

												);
												// var_dump('Datos  a facturar en kardex');

												// var_dump($DatosKardex);
												$this->guardar_detalle_venta($idEncaComprobante, $exento, $tasaCero, $percep1, $DatosKardex);
											}



											// Generando los montos totales de los encabezados del comprobante
											$DatossumKardex = $this->Ventas_Model->calcularTotalesventa($idEncaComprobante);
											// pasando valores a variables para realizar calculos
											$totalVentaNetaAjena        = $DatossumKardex[0]->totalKardexCuentaAjena;
											$totalKardexGravado         = $DatossumKardex[0]->totalKardexGravado;
											$truncar = 10 ** 2;
											// $totalKardexIvaGravado      =   intval(($DatossumKardex[0]->totalKardexGravado *0.13) * $truncar) / $truncar;
											$totalKardexIvaGravado      = $DatossumKardex[0]->totalKardexIvaGravado;
											// $totalKardexIvaGravado = parseFloat(data[0].totalKardexIvaGravado;
											$totalKardexExento          = $DatossumKardex[0]->totalKardexExento;
											$totalKardexExportacion     = $DatossumKardex[0]->totalKardexExportacion;
											$totalKardexNosujeto        = $DatossumKardex[0]->totalKardexNosujeto;
											//   almacena el   subtotal de la  venta
											$subTotal = round(($totalKardexGravado + $totalKardexIvaGravado + $totalKardexExento + $totalKardexExportacion + $totalKardexNosujeto), 2);
											//  calculando si para el  cliente se le hace retencion
											if ($percep1 == 'SI') {
												if ($totalKardexGravado >= 100) {
													$retencionUnoPorCiento = round(($totalKardexGravado * 0.01), 2);
												}
											} else {
												$retencionUnoPorCiento = 0.00;
											}
											$totalLiquidocobrar1PorCiento = ($subTotal - $retencionUnoPorCiento);
											//  almacena el   total  de l
											$totalKardexVentaTotal = number_format(($totalLiquidocobrar1PorCiento + $totalVentaNetaAjena), 2);
											$totalKardexVentaTotal =   str_replace(',', '', $totalKardexVentaTotal);
											$nombrecliente         = '';
											$documentopresentar    = '';
											// generando el array para actualizar el encabezado del  comprobante
											$data = array(
												'id_empresa'				=> $idEmpresa,
												'id_sucursal_empresa'		=> $idSucursal,
												'id_cliente'				=> $idCliente,
												'nombrecliente'            => $nombrecliente,
												'documentopresentar'        => $documentopresentar,
												'id_destinatario'			=> $id_destinatario,
												'id_tipo_comprobante'		=> $idTipoComprobante,
												'numero_comprobante'		=> $numero_comprobante,
												'comprobante_afecto'		=> $numero_comprobante,
												'id_comprob_afecto'         => $id_comprob_afecto,
												'serie_factura'		        => $serie_factura,
												'ot_numero'					=> $ot_numero,
												'fecha_comprobante'			=> $fecha_comprobante,
												'total_exento'				=> $totalKardexExento,
												'total_exportacion'			=> $totalKardexExportacion,
												'total_no_sujeto'			=> $totalKardexNosujeto,
												'total_gravado'				=> $totalKardexGravado,
												'total_iva_gravado'			=> $totalKardexIvaGravado,
												'sub_total_ventas'			=> $subTotal,
												'retencion_1_por_ciento'	=> $retencionUnoPorCiento,
												'sub_total_cuenta_ajena'	=> $totalVentaNetaAjena,
												'venta_total'				=> $totalKardexVentaTotal,
												'id_tipo_evento'            => $id_tipo_evento,
												'fact_manual'               => $fact_manual,
											);

											//var_dump('totales de factura............................................');
											if ($totalKardexVentaTotal > 0) {
												$datoupcabecera  =  $this->Ventas_Model->guardar_enca($data, $idEncaComprobante);
												if ($datoupcabecera !=  0) {
													// segmento para actualizar en  guias_import_ss  los warehouse que ya se procesaron
													//actualizar el control de correlativos
													$this->Importacion_Model->actualizar_correlativos($idEmpresa, $idSucursal, $idTipoComprobante, $nuevoCorrelativo);

													$procesado = 1; // variable que indicara que los warehouse ya fueron  procesados
													//  funcion  para  obtener las  guias a actualizar
													//  arrar  de datos  de facturacion
													$data =  array(
														'id_tipo_comprobante'    => $idTipoComprobante,
														'numero_comprobante'     => $numero_comprobante,
														'fecha_comprobante' 	 => $fecha_comprobante,
														'facturacion_pendiente'  => 1,
														'seleccionado'           => 0
													);
													// echo  'modificar los datos de las  guias' se  ponen como facturado;
													$datosdfacturacion = $this->Exportacion_Model->asigna_datosdfacturacion($idEncaComprobante, $idCliente, $warehouse, $data);
													//  obtenermos  las  guias  facturadas para  casos  MA
													if ($datosdfacturacion != 0) {
														$guias_g  =   $this->Exportacion_Model->guias_expfacturadasFE($idEncaComprobante);
														if (!empty($guias_g)) {
															foreach ($guias_g as $gma) {
																if ($gma->ma   == 1) {
																	$tracking_number = $gma->guia_no;
																	// echo 'actualizando para  MA' . '<br>';
																	$guiaMAupdate = $this->Exportacion_Model->update_facturadoUSPS($tracking_number);
																}
															}
														}
													}
													# generamos la partida de venta
													$codigopartida   = '008';
													//$generarpartida  = new Generarpartidas();
													//$generarpartida->generarpartida_venta($idTipoComprobante, $idEncaComprobante, $codigopartida, $fecha_comprobante);
													# fin de la generacion de partida de venta

													$id_tipo_pago    = 2;
													if ($guia_ncancelada = 0) { // indica que ya se cancelaron  todas las guias de la factura
														if ($id_tipo_pago == 1) { //  el cliente es de contado
															//  quitar la bandera de incluir cobro
															$incluir_cobro = 0; //  quitamos la marca de cobrado /
															$this->Ventas_Model->incluir_cobro($incluir_cobro, $idEncaComprobante);
														}
													}
													$valor_abono        = 0.00;
													$valor_cargo        = $totalKardexVentaTotal;
													$comprobante_afecto = $numero_comprobante;
													$tipo_transac       =  'c';                                    //}

													echo 'ingresando al  proceso de crear cuenta por cobrar!!!!!!!!!!';

													if ($id_tipo_pago ==   2) {
														echo 'ingresando al  proceso de crear cuenta por cobrar';
														$dataCxC = array(
															'id_cliente'             => $idCliente,
															'id_destinatario'        => $idCliente,
															'id_empresa'             => $idEmpresa,
															'id_comprob_venta'       => $idEncaComprobante,
															'num_comrpobante'        => $numero_comprobante,
															'id_tipo_comprobante'    => $idTipoComprobante,
															'id_comprob_afecto'      => $idEncaComprobante,
															'valor_cargo'            => $valor_cargo,
															'valor_abono'            => $valor_abono,
															'fecha_emision'          => $fecha_comprobante,
															'tipo_transac'          => $tipo_transac
														);
														//var_dump($dataCxC);
														//var_dump('proceso  de almacenado en  det_cxc');
														$idcxc = $this->Ventas_Model->buscarIdCxC($idEncaComprobante);
														// siempre se genera  ya que crea la cuenta por cobrar despues del  proceso  de facturacion
														$this->Ventas_Model->guardar_detalle_cxc($dataCxC, $idcxc->id);
													}
												}
											}
										}
									}
									# FIN DEL SEGMENTO  DE LA  GENERACION  DE LA  VENTA
								}
							}
							//  recorriendo  los datos del detalle de la guia para obtener a que cliente se le va a facturar
						}
						if ($guiaMAupdate != 0) {
							$id_venta = 0;
							$datos    = $this->DTES_Model->datguia_exp($warehouse);
							if (!empty($datos)) {
								$id_venta = $datos->id_venta;
								//   con ese  id  obtenemos los datos de la  venta
								if ($id_venta !=  0) {
									$datosv =    $this->DTES_Model->datventa($id_venta);
									if (!empty($datosv)) {
										// var_dump($datosv);
										$id = $datosv->id;
										$id_tipo_evento = $datosv->id_tipo_evento;
										$cod_generacion = $datosv->cod_generacion;
										$resultadoERR   = '';
										$id_cliente     =  12165;
										//echo   'eñ  codigo  de  generacion  es ' .  $cod_generacion;
										if ($cod_generacion ==   null || $cod_generacion ==   null  || $cod_generacion == '') {
											$proceso  = 1;
											$facturaelectronica  = new Facturaelectronica();
											$resulgeneracio      =   $facturaelectronica->generarfacturaelectronica($id_tipo_evento, $id);
											$resultadoERR =   $facturaelectronica->CtrlError($resulgeneracio, $id_cliente, $proceso, $id);
										} else {
											echo  'ya se  genero facura  electronica';
										}
									}
								}
							} else {
								echo   'Esta guia  no se encuentra descargada en  el  sistema de facturacion';
							}
						}
					} else {
						echo  'esta guia ya se  proceso!!!!!!!!!!!!!!!!!!!!';
					}
				}
			}
		}
		# FIN DEL SEGMENTO PARA LA FACTURACION  ELECTRONICA

		//  actualizar  estatus  de  manifiesto 
		//echo 'los  daos para  actualizar son  ' .  $id_estatus .  ' hw'. $warehouse; 

	}

	function token_ma($id_tokencliente, $token_cliente)
	{

		$header = array(
			'Host: tracking.mailamericas.com',
			'Content-Type: application/json',
			'Cache-Control: no-cache'
		);

		$data = array(
			"grant_type" => "client_credentials",
			"client_id" => $id_tokencliente, //42,
			"client_secret" => $token_cliente, //"1CFIlE3pXo2QdJgcnlocoRIoXsJ1Dr3veM8pWKj4",
			"scope" => "*"
		);
    	$url = "https://tracking.mlasalvador.com/oauth/token" ;



		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => json_encode(array(
				"grant_type" => "client_credentials",
				"client_id" => $id_tokencliente,
				"client_secret" => $token_cliente,
				"scope" => "*"
			)),
			CURLOPT_HTTPHEADER => array(
				'Cache-Control: no-cache',
				'Content-Type: application/json',
				'Host: tracking.mailamericas.com'
			),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		//   echo $response;
		$code = json_decode($response);
		// var_dump($code);
		$token = $code->access_token;
		return $token;
	}


	/* Espacio para metodos d valores, vienen desde c807xpress/servicios*/
	public function tikets_post()
	{
		$data = $this->post();

		//    var_dump($data);
		//echo $data['descripcion'];

		$datos = array(
			"empresa_id"     => $data['empresa_id'],
			"proceso_id"     => $data['proceso_id'],
			"sistema_id"     => $data['sistema_id'],
			"estado_id"      => $data['estado_id'],
			"colaborador_id" => $data['colaborador_id'],
			"categoria_id"   => $data['categoria_id'],
			"asunto"         => $data['asunto'],
			"descripcion"    => $data['descripcion'],
			"prioridad_id"   => $data['prioridad_id'],
			"fecha"          => $data['fecha'],
			"created_at"     => $data['created_at'],
			"updated_at"     => $data['updated_at'],
		);
		$rsl = $this->Manifiesto_model->guardar_ticket($datos);
		echo $rsl;
	}

	public function consulta_tikets_get()
	{
		$result = $this->Manifiesto_model->consulta_tikets();
		echo json_encode($result, JSON_PRETTY_PRINT);
	}

	public function getItemsForGma($tracking_number)
	{
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);
		$DrEtorno = 0;
		$fila  = $this->Exportacion_Model->getItemsFormaG($tracking_number);
		if ($fila != null) {   //  determina que el  json contenga valores para contiuar la operacion
			#region //  declaracion de variables
			foreach ($fila as $row) {
				$guia_no       = '';
				$guia_no       = $row->guia_no;
				// echo  'buscando la guia ' .  $guia_no   .  '<br>' ;
				$DatosNguia    = $this->Exportacion_Model->buscar_guia_tbguias($guia_no);
				//var_dump($DatosNguia);
				$id_empresa					 = 47;
				$id_prov_exp				 = 6; //  ups
				$id_cliente					 = 12165;
				$facturacion_pendiente		 = 0;
				$fecha					     = date("Y-m-d"); //  fecha de la guia
				$fecha_ingreso 			     = date("Y-m-d");
				$hora_ingreso                = null;
				$id_tipo_pago                = 5;
				$id_pais_destino             = 211;
				$id_region_destino           = 1;
				$id_servicio                 = 2;
				$largo_cms                   = 0;
				$alto_cms                    = 0;
				$ancho                       = 0;
				$ma                          = 1;
				$porcetajefuel               =  0;
				$unidad_m                    = 'lbs';
				$cargo_recargo_no_doc		 = 0.0;
				$aplicar_seguro				 = 0; // 0 indica que no aplica  1  aplica
				$id_tarifa_seguro			 = 0;
				$cargo_seguro				 = 0.0;
				$insp_no_intrusiva			 = 0.0;
				$cargo_adicional			 = 0.0;
				$concepto_cargo_adicional	 = '';
				$valor_monto_declarado       = 0;
				$no_piezas                   = 1;
				$seleccionado				 = 0;
				$id_usuario_ingreso          =  $_SESSION["user_id"];
				$ciudad_destino              =  '';
				$subtotal                    = 0;
				$total_descuentos            = 0;
				$fuel                        = 0;
				$id_servexp                  = 2;
				$id = 0;
				$cargo_basico                = $row->cargo_basico;
				$cargo_peso_extra            = $row->cargo_peso_extra;
				$iva						 = $row->iva;
				$iva_adn                     = $row->iva_adn;
				$total_guia                  = $row->total_guia;
				if ($DatosNguia == null) {
					// indica que no encontro la guia en base de datos
					//echo  'proceso de  insertar  datos' ;
					$guia_no					 = $row->guia_no;
					$guia_ibc_no				 = $row->guia_no;
					$descripcion                 = $row->descripcion;
					$peso_neto_kgs               = $row->peso_neto_kgs;
					$peso_neto_lbs               = $row->peso_neto_lbs;
					$peso_a_tomar_kgs            = $row->peso_neto_kgs;
					$peso_a_tomar_lbs            = $row->peso_neto_lbs;

					$data = array(
						'id_empresa' 						=> 47, //$_SESSION["empresa_id"]),
						'id_prov_exp' 						=> $id_prov_exp,
						'id_cliente' 						=> $id_cliente,
						'guia_no' 							=> $guia_no,
						'guia_ibc_no' 						=> $guia_ibc_no,
						'fecha' 							=> $fecha, //  fecha de la guia
						'fecha_ingreso' 					=> $fecha_ingreso, // fecha en  que el usuario ingresa la guia
						'hora_ingreso' 						=> $hora_ingreso,
						'id_usuario_ingreso' 				=> $id_usuario_ingreso,
						'id_tipo_pago'	 					=> $id_tipo_pago,
						'facturacion_pendiente'             => $facturacion_pendiente,
						'ciudad_destino' 					=> $ciudad_destino,
						'id_pais_destino' 					=> $id_pais_destino,
						'id_region_destino' 				=> $id_region_destino,
						'id_servicio' 						=> $id_servexp, // 1,2 documento  no  documento
						'descripcion'                       => $descripcion, // almacena que es lo que se envia
						'valor_monto_declarado' 			=> $valor_monto_declarado,
						'no_piezas' 						=> $no_piezas,
						'unidad_m' 						    => $unidad_m, //  lbs o kgs
						'peso_neto_kgs'	 				    => $peso_neto_kgs, // cambiar  peso_a_tomar_kgs
						'peso_neto_lbs'	 				    => number_format($peso_neto_lbs, 3), // cambiar  peso_a_tomar_kgs
						'peso_a_tomar_lbs' 					=> number_format($peso_a_tomar_lbs, 3),
						'peso_a_tomar_kgs'                  => number_format(floatval($peso_a_tomar_kgs), 2),
						'cargo_basico'	 					=> $cargo_basico,
						'cargo_peso_extra'	 				=> $cargo_peso_extra,
						'cargo_recargo_no_doc'	 			=> $cargo_recargo_no_doc,
						'aplicar_seguro'					=> $aplicar_seguro,
						'id_tarifa_seguro'	 				=> $id_tarifa_seguro,
						'cargo_seguro'	 					=> $cargo_seguro,
						'insp_no_intrusiva'	 				=> $insp_no_intrusiva,
						'subtotal'	 						=> $subtotal,
						'total_descuentos'	 				=> $total_descuentos,
						'fuel'	 							=> $fuel,
						'iva'								=> $iva,
						'total_guia' 						=> $total_guia,
						'seleccionado'                      => $seleccionado,
						'porc_fuel'                         => $porcetajefuel,
						'iva_adn'                           => $iva_adn,
						'ma'                                => $ma,
					);
					$DrEtorno = $this->Exportacion_Model->guardar_guiaexp($id, $data);
				} else {
					// echo  'actualizando paquetes';
					//funcion  de  actualizacion
					$dataGMA = array(
						'cargo_basico'	 					=> $cargo_basico,
						'cargo_peso_extra'	 				=> $cargo_peso_extra,
						'iva_adn'                           => $iva_adn,
						'iva'								=> $iva,
						'total_guia' 						=> $total_guia,
					);
					$DrEtorno = $this->Exportacion_Model->update_guiaMA($guia_no, $dataGMA);
				}
			}
		}
		return  $DrEtorno;
	}

	// funcion para  validad los  datos de las  guias
	public function seleccionar_guia($guia_no, $seleccionado, $ma)
	{
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);
		$nError = 0;
		if ($ma ==  1) {
			// echo    'ingresando    a validacion  ma  ' ;
			// lanzamos la  validacion  de los  datos de las  guia x
			$datos_gEXP  =  $this->Exportacion_Model->getdatosGuiaEXP($guia_no);
			//var_dump($datos_gEXP);
			//echo 'datos MAjghjg' . '<br>';

			if (!empty($datos_gEXP)) {
				$datos_gMA  = $this->Exportacion_Model->getdatosGuiaMA($guia_no);
				//echo 'datos MA' . '<br>';
				//var_dump($datos_gMA);
				if (!empty($datos_gMA)) {
					//  comparamos el cargo basico
					if ($datos_gEXP->cargo_basico != $datos_gMA->manejo) {
						$nError += 1;
					}
					//  comparamos el cargo basico
					if ($datos_gEXP->cargo_peso_extra != $datos_gMA->comision) {
						$nError += 1;
					}
					//  comparamos el cargo basico
					if ($datos_gEXP->iva_adn != $datos_gMA->total_imp) {
						$nError += 1;
					}

					if ($datos_gEXP->iva != $datos_gMA->total_ivag) {
						$nError += 1;
					}
					if ($datos_gEXP->total_guia != $datos_gMA->cobro_final) {
						$nError += 1;
					}
				}
			}

			echo  'errror  en  la  guia ' . $nError . ' error  de seleccion ';
			if ($nError > 0) {

				$this->Exportacion_Model->seleccionar_guia($guia_no, $seleccionado = 0);
			} else {
				$this->Exportacion_Model->seleccionar_guia($guia_no, $seleccionado);
			}
		} else {
			$this->Exportacion_Model->seleccionar_guia($guia_no, $seleccionado);
		}
		echo $nError;
	}

	//  FUNCION PARA  ALMACENAR EL DETALLE DE LAS  VENTAS
	public function guardar_detalle_venta($idVenta, $exento, $tasaCero, $percep1, $DatosKardex)
	{
		//  determinar si los productos vienen de ingreso manual o de  un llenado automatico -- observacion  oscar
		//  se necesita pasar la informacio del cliente si esta excento , tasa cero o si se le persive al cliente
		// generacion  de variables
		$kdx_exento 				= 0.00;
		$kdx_exportacion 			= 0.00;
		$kdx_no_sujeto 				= 0.00;
		$kdx_gravado 				= 0.00;
		$kdx_iva_gravado 			= 0.00;
		$kdx_exento_a_terceros 		= 0.00;
		$kdx_gravado_a_terceros 	= 0.00;
		$kdx_iva_gravado_a_terceros = 0.00;
		$kdx_cuenta_ajena 			= 0.00;
		$retencion_1_por_ciento 	= 0.00;
		$kdx_vta_total 				= 0.00;
		$id_encabezado_comprobante  = $idVenta;
		$id_detalle 				= '';
		$id_catalogo_producto 		= 0;
		$descripcion 				= '';
		$cantidad 					= 0.00;
		$precio_neto 				= 0.00;
		$venta_neta 				= 0.00;
		$tasaCero 					= $tasaCero;
		$exento 					= $exento;
		$perUnoPorCiento 			= $percep1;
		$id_tc_producto 			= '';
		$id_clasificacion_inv 		= '';
		$id_tipo_venta 				= '';
		$id_tipo_mh 				= '';
		$id_presentacion 			= '';
		$id_tipo_comprobante 		= '';

		if (isset($DatosKardex)) {
			// var_dump('se ha encontrados datos genereados de forma automatica');
			//  var_dump($DatosKardex);

			$id_catalogo_producto 		= $DatosKardex['id_catalogo_producto'];
			$descripcion 		        = $DatosKardex['descripcion'];
			$cantidad 					= $DatosKardex['cantidad'];
			$precio_neto 				= $DatosKardex['precio_neto'];
			$venta_neta 				= $DatosKardex['venta_neta'];
			$id_tipo_venta 			    = intval($DatosKardex['id_tipo_venta']); //1.ventas propias, 2.ventas a terceros, 3.ventas_ajenas
			$id_tipo_mh 				= $DatosKardex['id_tipo_mh']; //afecto o gravado, excento, no sujeto
			$id_tipo_comprobante 		= $DatosKardex['id_tipo_comprobante']; //1.CCF, 2.FCF, 3.NCR, 4.FEX
		} else {
			echo  'los datos del kardex no llegaron  ';
		}
		//echo  'el catalo del producto despues de asignar es ' . $id_catalogo_producto;




		// nueva codificacio
		if ($id_tipo_venta == 1) { //1.ventas propias
			if ($id_tipo_mh == 1) { //afecto o gravado
				if ($id_tipo_comprobante == 4) { // factura exportacion
					if ($tasaCero == 'NO' && $exento   = 'NO') { //si no es exento o tasa cero pasa a ser gravado por defecto
						var_dump("en exportacion es gravado!!!!!!!!!!!!!!!!!!");
						$kdx_gravado = $venta_neta;
						$kdx_iva_gravado = $venta_neta * 0.13;
						$kdx_vta_total   = round(($kdx_gravado + $kdx_iva_gravado), 2);
					} elseif ($tasaCero == 'NO' && $exento == 'SI') {
						$kdx_exento = $venta_neta;
						$kdx_vta_total = $kdx_exento;
					} elseif ($tasaCero == 'SI') {
						$kdx_exportacion = $venta_neta;
						$kdx_vta_total = $kdx_exportacion;
					}
				} elseif ($id_tipo_comprobante == 2) { // consumidor final
					if ($exento == 'SI') { //si cliente es exento
						$kdx_exento = $venta_neta;
						$kdx_vta_total = $kdx_exento;
					} else {
						$kdx_gravado = $venta_neta;
						$kdx_iva_gravado = $venta_neta * 0.13;
						$kdx_vta_total = round(($kdx_gravado + $kdx_iva_gravado), 2);
					}
				} else { // para credito fiscal

					if ($exento == 'SI') { //si cliente es exento
						$kdx_exento = $venta_neta;
						$kdx_vta_total = $kdx_exento;
					} else { //si no es exento o tasa cero pasa a ser gravado por defecto
						var_dump("gravado");
						$kdx_gravado = $venta_neta;
						$kdx_iva_gravado = $venta_neta * 0.13;
						$kdx_vta_total = round(($kdx_gravado + $kdx_iva_gravado), 2);
					}
				}
			} elseif ($id_tipo_mh == 2) { //excento tipo  de movimiento  del producto


				if ($id_tipo_comprobante == 4) { // factura exportacion
					$kdx_exportacion = $venta_neta;
					$kdx_vta_total = $kdx_exportacion;
				} else {
					$kdx_exento = $venta_neta;
					$kdx_vta_total = $venta_neta;
				}
			} else { //no sujeto

				$kdx_no_sujeto = $venta_neta;
				$kdx_vta_total = $venta_neta;
			}
		} elseif ($id_tipo_venta == 2) { //2.ventas a terceros
			if ($id_tipo_mh == 1) { //afecto o gravado
				if ($exento == 'SI') { //si cliente es exento
					$kdx_exento_a_terceros = $venta_neta;
					$kdx_vta_total = $kdx_exento;
				} else { //si no es exento o tasa cero pasa a ser gravado por defecto
					$kdx_gravado_a_terceros = $venta_neta;
					$kdx_iva_gravado_a_terceros = $venta_neta * 0.13;
					$kdx_vta_total = round(($kdx_gravado_a_terceros + $kdx_iva_gravado_a_terceros), 2);
				}
			} elseif ($id_tipo_mh == 2) { //excento
				$kdx_exento_a_terceros = $venta_neta;
				$kdx_vta_total = $venta_neta;
			} else { //no sujeto
				//$kdx_no_sujeto = $venta_neta;
			}
		} else { //3.ventas_ajenas
			$kdx_cuenta_ajena = $venta_neta;
			$kdx_vta_total = $venta_neta;
		}

		if ($perUnoPorCiento == 'SI' && $kdx_gravado > 100) {
			$retencion_1_por_ciento = ($kdx_gravado + $kdx_iva_gravado) * 0.01;
		}
		$id_detalle = null;


		$data = array(
			'id_encabezado_comprobante' => $id_encabezado_comprobante,
			'no_guia'                   => 0, //nwarehouse
			'id_catalogo_producto'      => $id_catalogo_producto,
			'descripcion'               => $descripcion,
			'cantidad'                  => $cantidad,
			'precio_neto'               => $precio_neto,
			'venta_neta'                => $venta_neta,
			'id_tipo_venta'             => $id_tipo_venta,
			'kdx_exento'                => $kdx_exento,
			'kdx_exportacion'           => $kdx_exportacion,
			'kdx_no_sujeto'             => $kdx_no_sujeto,
			'kdx_gravado'               => $kdx_gravado,
			'kdx_iva_gravado'           => $kdx_iva_gravado,
			'kdx_exento_a_terceros'     => $kdx_exento_a_terceros,
			'kdx_gravado_a_terceros'    => $kdx_gravado_a_terceros,
			'kdx_iva_gravado_a_terceros' => $kdx_iva_gravado_a_terceros,
			'kdx_cuenta_ajena'          => $kdx_cuenta_ajena,
			'kdx_vta_total'             => $kdx_vta_total

		);
		//var_dump('Antes de guardar en el kardex');

		// var_dump($data);
		$this->Ventas_Model->guardar_detalle_venta($data, $id_detalle);
	}

	//Recibe la información de cliente
	public function prealerta_post()
	{

		$data = $this->post();
		//var_dump($data);

		$datos = array(
			'nombre' => $data["nombre"],
			'tracking_number' => $data['tracking_number'],
			'correo' => $data['correo'],
			'telefono' => $data['telefono'],
			'dui' => $data['dui'],
			'commodity' => $data['commodity'],
			'direccion' => $data['direccion']
		);

		$dui = $this->Manifiesto_model->verificar_dui($data['dui']);
		// update, departamento, dui, municipio y agencia 

		if (count($dui) == 0) {
			$this->Manifiesto_model->prealerta($datos);
			$this->sendEmail($datos);
			echo "No existe el dui";
		} else {
			echo "Dui encontrado!";
		}
	}
	//recibe valores de dui y nombre de cliente
	public function identidad_personal_post()
	{
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);
		$data = $this->post();

		$datos = array(
			'nombre' => $data['nombre'],
			'dui' => $data['dui'],
		);

		$this->Manifiesto_model->identidad_personal($datos);

		$idDepartamento = 0;
		$idMunicipio =  0;
		$id_sucursal = 0;

		//si sucursal > 0 realizar una consulta a la tabla agencias el valor que traiga la sucursal
		if ($data["sucursal"] > 0) {
			$result = $this->Manifiesto_model->buscar_sucursal($data["sucursal"]);

			$idDepartamento =  $result->id_departamento;

			$idMunicipio =  $result->id_municipio;

			$id_sucursal = $data["sucursal"];
		} else {
			$idDepartamento = $data['departamento'];
			$idMunicipio = $data['municipio'];
		}


		$this->Manifiesto_model->update_prealerta($data['dui'], $idDepartamento, $idMunicipio, $id_sucursal, $data['tracking']);
	}




	public function sendEmail($datos)
	{

		$nombre = $datos["nombre"];
		$correo = $datos["correo"];
		$tracking_number = $datos["tracking_number"];
		$commodity = $datos["commodity"];
		$telefono = $datos["telefono"];
		$dui = $datos["dui"];
		$direccion = $datos["direccion"];


		$msj = '<!DOCTYPE html>
        
                                <html lang="en">
                                <head>
                                <meta charset="UTF-8">
                                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                                <title>Document</title>
                                </head>
                                <body>
                                <p>Estimado ' . $nombre . '</p>
                                <p>Te notificamos que hemos recibido un paquete de una compra procedente de AliExpress.</p>
                                <p style="margin: 0.2rem 0rem!important;"><span style="font-weight: bold;">Tracking number : ' . $tracking_number . '</span></p>
                                <p style="margin: 0.2rem 0rem!important;"><span style="font-weight: bold;">Descripcion de la compra :' . $commodity . ' </span></p>
                                <p>* Puedes restrear tu envio desde AliExpress o en el siguiente enlace <a href="https://mailamericas.com/ ">https://mailamericas.com/</a> usando tu número de tracking</p>
                                <p>Para completar el proceso de la liberación de tu(s) compra(s) necesitamos que nos ayudes llenado el <a href="">formulario</a> para efectos del tramite aduanal y poder aplicar al beneficio de la Ley de "Facilitación de Compras en Linea" que por compras personales menores a $300.00 no pagas DAI (Derecho Arancelario a la Importación) ni permisos especiales*. <br>
                                Una vez liberado, puedes recogerlo en cualquier de nuestras oficinas o para tu comodidad, enviártelo hasta tu domicilio o lugar de trabajo.
                                </p><p>NOTA IMPORTANTE:</p>
                                <p>- Por tu seguridad, el pago será únicamente al momento de la entrega de tu paquete y deberá ser en efectivo. Nunca te pediremos que deposites anticipadamente en una cuenta bancaria o que nos brindes datos de alguna tarjeta de crédito o débito.</p>
                                <br>
                                <br>
                                <p style="margin: 0.2rem 0rem!important;"><span style="font-weight: bold;">PBX:</span> (503) 2537-7200</p>
                                <p style="margin: 0.2rem 0rem!important;"><span style="font-weight: bold;">WhatsApp:</span> (503) 7150-9535 o al (503) 6034-6037</p>
                                <p style="margin: 0.2rem 0rem!important;"><span style="font-weight: bold;">Web:</span> <a href="https://mistarship.com/agencias/">mistarship.com</a></p>
                                <p style="margin: 0.2rem 0rem!important;"><span style="font-weight: bold;">Facebook:</span> /StarshipSV</p>
                                <br>
                                <a href="https://stargates.site/form/?tracking_get=' . $tracking_number . '&dui=' . $dui . '&correo=' . $correo . '&tel=' . $telefono . '&commodity=' . $commodity . '&nombre=' . $nombre . '&direccion=' . $direccion . '" style="text-decoration: dashed;background-color: #ff6a40;width: 260px;color: #ffffff !important;margin-top: 10px;border: 1px solid transparent;border-radius: 4px;cursor: pointer;display: inline-block;font-size: 16px;font-weight: 400;line-height: 1.5;padding: 6px 12px;position: relative;text-align: center;transition: background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;-webkit-user-select: none;-moz-user-select: none;user-select: none;vertical-align: middle;white-space: nowrap;">Llenar formulario acá</a>
                                </body>
                                </html>';

		error_reporting(E_ALL);
		ini_set('display_errors', '1');
		$this->load->library('email');
		$config = array(
			'protocol' => 'smtp',
			'smtp_host' => 'localhost',
			'smtp_port' => 25,
			'smtp_user' => 'adminserver@stargates.site',
			'smtp_pass' => 'adminserver2022',
			'mailtype' => 'html',
			'charset' => 'utf-8',
		);
		$this->email->initialize($config);
		$this->email->set_newline("\r\n");
		$this->email->from('servicioalcliente7@mistarship.com', 'StarShip');
		$this->email->to($correo);
		$this->email->subject('Notificacion de StarShip');
		$this->email->message($msj);
		if ($this->email->send()) {
			echo 'Your email was sent.';
		} else {
			echo $correo . " - NO ENVIADO<br>";
		}
		$this->email->clear(true);
	}

	public function upatetracking_post()
	{

		$data = $this->post(); //contiene los datos

		$data =  file_get_contents('php://input', true);
		//  echo $data ;
		$row = json_decode($data);
		$tracking_number = $row->tracking_number;


		$rsl = $this->Exportacion_Model->update_facturadoUSPS($tracking_number);
		echo $rsl;
	}


	public function consignee_get()
	{
		$id = $this->uri->segment(3);

		if (!isset($id)) {
			$respuesta = array(
				'err' => true,
				'mensaje' => 'Es necesario el número de warehouse'
			);
			$this->response($respuesta, REST_Controller::HTTP_BAD_REQUEST);
			return;
		}

		if ($id == " ") {
		} else {
			$id = str_replace("%20", " ", $id);
		}
		$warehouse = $this->Warehouse_model->get_tracking($id);

		if (($warehouse)) {
			$respuesta = array(
				'err' => false,
				'mensaje' => 'Registro cargado correctamente',
				'cliente' => $warehouse
			);
			$this->response($respuesta);
		} else {
			$respuesta = array(
				'err' => true,
				'mensaje' => 'El warehouse ' . $id . ', no existe ',
				'warehouse' => null
			);
			$this->response($respuesta, REST_Controller::HTTP_NOT_FOUND);
		}
	}


	public function comparar_post()
	{
		$data = $this->post();
		$tracking = $data['tracking'];

		$result =	$this->Exportacion_Model->comparar($tracking);
		echo json_encode($result);
	}


	public function estadosApgOLD_post()
	{
		ini_set('display_errors', '1');
		ini_set('display_startup_errors', '1');
		error_reporting(E_ALL);
		date_default_timezone_set("America/El_Salvador");
		$date = date('Y-m-d H:i:s');
		$fecha_tmp = DateTime::createFromFormat('Y-m-d H:i:s', $date);
		$fecha_iso = $fecha_tmp->format(DateTime::ATOM);

		$item = "";
		$fecha = "";
		$warehouse = "";
		$id_estatus = "";
		$casillero = "";
		$id_pais =  "";



		$data = $this->post(); //contiene los datos

		$data =  file_get_contents('php://input', true);
		//  echo $data ;
		$row = json_decode($data, true);

		//-----------------------------------------------------------------------------

		$codigo_recibido =  $row['codigo'];
		$tracking_recibido =  $row['guia'];

		$existe =	 $this->Manifiesto_model->verificar_pod($tracking_recibido, $codigo_recibido);



		if ($existe) {
			$respuesta = array(
				'err' => false,
				'mensaje' => 'Ya existe un registro con estatus 15 para ese tracking.'
			);

			$info = array(
				"tracking_number" => $tracking_recibido,
				"id_estatus" => $codigo_recibido,
				"fecha" => date("Y-m-d H:i:s")

			);
			echo json_encode($respuesta);
			$this->Manifiesto_model->guardar_alerta($info);
			exit;
		}

		$existe_ro =	 $this->Manifiesto_model->verificar_Ro($tracking_recibido, $codigo_recibido);



		if ($existe_ro) {
			$respuesta = array(
				'err' => false,
				'mensaje' => 'Ya existe un registro con estatus 311 para ese tracking.'
			);

			$info = array(
				"tracking_number" => $tracking_recibido,
				"id_estatus" => $codigo_recibido,
				"fecha" => date("Y-m-d H:i:s")

			);
			echo json_encode($respuesta);
			//	$this->Manifiesto_model->guardar_alerta($info);
			exit;
		}




		//echo "no encontrado";

		//-----------------------------------------------------------------------------


		$item = 1;
		$fecha = date('Y-m-d H:i:s');
		$warehouse =  $row['guia'];



		/** Recibe la guia de xpreess (internal_tracking) y luego busca la guia de Mailamericas(tracking_number) */
		$query =	 $this->Manifiesto_model->get_tracking($warehouse);


		$cliente = $this->Manifiesto_model->datos_empresa($query[0]->id_empresa);



		$id_tokencliente = $cliente[0]->id_tokencliente;
		$token_cliente =   $cliente[0]->token_cliente;


		$id_estatus = $row['codigo'];
		$nombre_estatus = $row['estatus'];

		$description = "";
		$problemaGestion = $row['observaciones'];


		if ($id_estatus == 16) {
			$description    =  $row['estatus'] . ": " .  $row['observaciones'];
		} else {
			//$description    =  $row['estatus'];
		}

		if ($id_estatus == 16) {
			$razon_codigo = $row['razon']['codigo'];
			$problemaGestion = $row['razon']['descripcion'];
		}

		$casillero = "100";
		$id_pais =  1;
		$pod_nombre = "";
		$pod_valor = "";
		$document_name = "";
		$ruta = "";


		$devolucion = false;

		if ($id_estatus == 15 &&  $row['devolucion'] == 1) {

			$id_estatus = 311;
			$devolucion = true;
		}

		$this->Manifiesto_model->update_estatus($warehouse, $id_estatus); //guardar estas en tablas locales

		$datos = array(

			'item' => $item,

			'fecha' => date('Y-m-d H:i:s'),

			'warehouse' => $warehouse,

			'id_estatus' => $id_estatus,

			'casillero' =>  $casillero,

			'id_pais' =>  $id_pais,

			'pod_nombre' =>  $pod_nombre,

			'pod_valor' => "",

			'json_string' => '{    "guia": ""}', //$data, data trae el json cpmpleto, utilizarlo para testing

			'ruta_foto' => $ruta,

			'fechaiso' => $fecha_iso,

			'problema_gestion' => $description

		);


		$this->Manifiesto_model->guardar_estatus($datos);





		//TODO: PARA RESPUESTA DE ESTATUS
		// $data = array(
		// 	"respuesta" => $resultStr,
		// 	"tracking_number" =>  $warehouse,
		// 	"descripcion" =>  $description, // m
		// 	"fecha" =>  $fecha,
		// 	"fechaiso" =>  $fecha_iso

		// );

		//	$this->Manifiesto_model->guardar_logma($data);
		$statusName = "";
		$this->DTES_Model->updatetracking_usps($id_estatus, $warehouse);

		//	if ($warehouse == 'T32679422220687E7B') {


		if ($id_estatus == 16 || $id_estatus == 19) {
			//buscamos el equivalente en la tabla, Llamar al método del modelo para buscar por descripción

			$resultado = $this->Manifiesto_model->buscarPorDescripcion($problemaGestion);

			// Verificar si se encontraron resultados
			if ($resultado) {
				$statusName = $resultado[0]['codigo_cliente'];
				echo $statusName;
			} else {
				echo "No se encontraron coincidencias.";
			}
		} else {

			$resultado = $this->Manifiesto_model->buscarPorCodigo($id_estatus);

			// Verificar si se encontraron resultados
			if ($resultado) {
				$statusName = $resultado[0]['codigo_cliente'];
				echo $statusName;
			} else {
				echo "No se encontraron coincidencias.";
			}
		}

		if ($devolucion == true) {
			$statusName = "RO";
		}

		//	exit;
		//bloque para api

		$this->load->library('TokenApg'); // No uses mayúsculas aquí

		$username = "St4r4P1Shp";
		$password = "@1?g=]kAG>Yb";

		$response = $this->tokenapg->login($username, $password);
		$token = "";
		if (isset($response['token']['token'])) {
			$token = $response['token']['token']; // Extraer el token
			//echo   $token;
		} else {
			echo "No se pudo obtener el token.";
		}


		$url = 'https://api.apg-global.com/api/account/save-status';
		//$statusName = "OK";
		$data = [
			'tracking' => $warehouse,
			'status' => [
				[
					'statusName' => $statusName,
					'location' => 'El Salvador',
					'statusDate' => date('Y-m-d H:i:s'),
					'pod' => null,
				],
			],
		];

		$curl = curl_init();

		curl_setopt_array($curl, [
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => json_encode($data),
			CURLOPT_HTTPHEADER => [
				'Authorization: Bearer ' . $token,
				'Content-Type: application/json',
			],
		]);

		$response = curl_exec($curl);

		if (curl_errno($curl)) {
			echo 'cURL error: ' . curl_error($curl);
		} else {
			echo $response;
		}

		curl_close($curl);
		//}else{
		//	echo "no es valido";
		//}
	}


	public function fechasApg_post()
	{

		exit;
		date_default_timezone_set("America/El_Salvador");
		$date = date('Y-m-d H:i:s');
		$fecha_tmp = DateTime::createFromFormat('Y-m-d H:i:s', $date);
		$fecha_iso = $fecha_tmp->format(DateTime::ATOM);

		$item = "";
		$fecha = "";
		$warehouse = "";
		$id_estatus = "";
		$casillero = "";
		$id_pais =  "";



		$data = $this->post(); //contiene los datos

		$data =  file_get_contents('php://input', true);
		//  echo $data ;
		$row = json_decode($data, true);



		$item = 1;
		//$fecha_real='2025-02-23 19:15:01'; //TODO: cambiar la fecha aqui
		$fecha_real = '2025-02-24 16:30:03'; //TODO: cambiar la fecha aqui

		$warehouse =  $row['guia'];

		/** Recibe la guia de xpreess (internal_tracking) y luego busca la guia de Mailamericas(tracking_number) */
		$query =	 $this->Manifiesto_model->get_tracking($warehouse);


		$cliente = $this->Manifiesto_model->datos_empresa($query[0]->id_empresa);



		$id_tokencliente = $cliente[0]->id_tokencliente;
		$token_cliente = $cliente[0]->token_cliente;


		$id_estatus = $row['codigo'];
		$nombre_estatus = $row['estatus'];

		$description = "";
		$problemaGestion = $row['observaciones'];
		if ($id_estatus == 16) {
			$description    =  $row['estatus'] . ": " .  $row['observaciones'];
		} else {
			//$description    =  $row['estatus'];
		}

		$casillero = "100";
		$id_pais =  1;
		$pod_nombre = "";
		$pod_valor = "";
		$document_name = "";
		$ruta = "";



		$this->Manifiesto_model->update_estatus($warehouse, $id_estatus); //guardar estas en tablas locales

		$datos = array(

			'item' => $item,

			'fecha' => $fecha_real,

			'warehouse' => $warehouse,

			'id_estatus' => $id_estatus,

			'casillero' =>  $casillero,

			'id_pais' =>  $id_pais,

			'pod_nombre' =>  $pod_nombre,

			'pod_valor' => "",

			'json_string' => '{    "guia": ""}', //$data, data trae el json cpmpleto, utilizarlo para testing

			'ruta_foto' => $ruta,

			'fechaiso' => $fecha_iso,

			'problema_gestion' => $description

		);


		$this->Manifiesto_model->guardar_estatus($datos);





		//TODO: PARA RESPUESTA DE ESTATUS
		// $data = array(
		// 	"respuesta" => $resultStr,
		// 	"tracking_number" =>  $warehouse,
		// 	"descripcion" =>  $description, // m
		// 	"fecha" =>  $fecha,
		// 	"fechaiso" =>  $fecha_iso

		// );

		//	$this->Manifiesto_model->guardar_logma($data);

		$this->DTES_Model->updatetracking_usps($id_estatus, $warehouse);

		//	if ($warehouse == 'T32679422220687E7B') {


		if ($id_estatus == 16) {
			//buscamos el equivalente en la tabla, Llamar al método del modelo para buscar por descripción

			$resultado = $this->Manifiesto_model->buscarPorDescripcion($problemaGestion);

			// Verificar si se encontraron resultados
			if ($resultado) {
				$statusName = $resultado[0]['codigo_cliente'];
				echo $statusName;
			} else {
				echo "No se encontraron coincidencias.";
			}
		} else {

			$resultado = $this->Manifiesto_model->buscarPorCodigo($id_estatus);

			// Verificar si se encontraron resultados
			if ($resultado) {
				$statusName = $resultado[0]['codigo_cliente'];
				echo $statusName;
			} else {
				echo "No se encontraron coincidencias.";
			}
		}

		//	exit;
		//bloque para api

		$this->load->library('TokenApg'); // No uses mayúsculas aquí

		$username = "St4r4P1Shp";
		$password = "@1?g=]kAG>Yb";

		$response = $this->tokenapg->login($username, $password);
		$token = "";
		if (isset($response['token']['token'])) {
			$token = $response['token']['token']; // Extraer el token
			//echo   $token;
		} else {
			echo "No se pudo obtener el token.";
		}


		$url = 'https://api.apg-global.com/api/account/save-status';
		//$statusName = "OK";
		$data = [
			'tracking' => $warehouse,
			'status' => [
				[
					'statusName' => $statusName,
					'location' => 'El Salvador',
					'statusDate' => $fecha_real, //date('Y-m-d H:i:s'),
					'pod' => null,
				],
			],
		];

		$curl = curl_init();

		curl_setopt_array($curl, [
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => json_encode($data),
			CURLOPT_HTTPHEADER => [
				'Authorization: Bearer ' . $token,
				'Content-Type: application/json',
			],
		]);

		$response = curl_exec($curl);

		if (curl_errno($curl)) {
			echo 'cURL error: ' . curl_error($curl);
		} else {
			echo $response;
		}

		curl_close($curl);
		//}else{
		//	echo "no es valido";
		//}
	}

	public function estadosApgbueno_post()
	{
		$this->load->model('Entrega_model');
		$this->load->model('Manifiesto_model');
		$this->load->model('DTES_Model');
		$this->load->library('TokenApg');

		try {
			ini_set('display_errors', '1');
			ini_set('display_startup_errors', '1');
			error_reporting(E_ALL);

			date_default_timezone_set("America/El_Salvador");
			$date = date('Y-m-d H:i:s');
			$fecha_tmp = DateTime::createFromFormat('Y-m-d H:i:s', $date);
			$fecha_iso = $fecha_tmp->format(DateTime::ATOM);

			header('Content-Type: application/json');

			$rawData = file_get_contents('php://input');
			$row = json_decode($rawData, true);

			if (!$row || !is_array($row)) {
				http_response_code(400);
				$this->_responderInmediatamente([
					'error' => true,
					'mensaje' => 'Formato JSON inválido.'
				]);
				return;
			}
			$entregado = 'NO';
			$codigo_recibido = $row['codigo'] ?? null;
			$tracking_recibido = $row['guia'] ?? null;
			$fecha_response = $row['fecha'] ?? null;   //fecha enviada por webhook

			$date = $fecha_response;
			$fecha_tmp = DateTime::createFromFormat('Y-m-d H:i:s', $date);
			$fecha_iso = $fecha_tmp->format(DateTime::ATOM);

			if (!$codigo_recibido || !$tracking_recibido) {
				http_response_code(400);
				$this->_responderInmediatamente([
					'error' => true,
					'mensaje' => 'Faltan datos requeridos: codigo o guia.'
				]);
				return;
			}

			$request_tracking = $this->Manifiesto_model->obtener_tracking($tracking_recibido);
			if (!$request_tracking) {
				http_response_code(404);
				$this->_responderInmediatamente([
					'error' => true,
					'mensaje' => "Tracking {$tracking_recibido} no encontrado."
				]);
				return;
			}

			//  RESPUESTA INMEDIATA AL PRIMER SISTEMA
			$this->_responderInmediatamente([
				'error' => false,
				'message' => 'Recibido correctamente.',
				'tracking' => $tracking_recibido
			]);

			// ---------------------------------------
			// CONTINÚA EL PROCESAMIENTO INTERNO
			// ---------------------------------------

			$existe = $this->Manifiesto_model->verificar_pod($tracking_recibido, $codigo_recibido);
			if ($existe) {
				$info = [
					"tracking_number" => $tracking_recibido,
					"id_estatus" => $codigo_recibido,
					"fecha" => date("Y-m-d H:i:s")
				];
				$this->Manifiesto_model->guardar_alerta($info);
				$this->_registrarEventoInterno(
					'Ya existe un registro con estatus 15 para ese tracking.',
					$tracking_recibido,
					'repetido',
					null,               // respuesta (en este caso no hay)
					$codigo_recibido,
					"",
					$entregado   // id_estatus
				);

				return;
			}

			$existe_ro = $this->Manifiesto_model->verificar_Ro($tracking_recibido, $codigo_recibido);
			if ($existe_ro) {
				$this->_registrarEventoInterno('Ya existe un registro con estatus 311 para ese tracking.', $tracking_recibido, 'Repetido', null, $codigo_recibido, "", $entregado);
				return;
			}

			// $query = $this->Manifiesto_model->obtener_tracking($tracking_recibido);
			// if (!$query || !isset($query[0]->id_empresa)) {
			// 	$this->_registrarEventoInterno('No se pudo obtener información del tracking.', $tracking_recibido, 'nodata');
			// 	return;
			// }
			$problemaGestion = "";
			$id_estatus = $row['codigo'];
			$description = "";
			if ($id_estatus == 16 || $id_estatus == 19) {
				$description =  ($row['observaciones'] ?? '');
				$problemaGestion = $row['razon']['descripcion'] ?? '';
			}

			$devolucion = false;
			if ($id_estatus == 15 && ($row['devolucion'] ?? 0) == 1) {
				$id_estatus = 311;
				$devolucion = true;
			}

			if ($id_estatus == 19) {
				$problemaGestion =  ($row['observaciones'] ?? '');
				//$problemaGestion = $row['razon']['descripcion'] ?? '';
			}
			$this->Manifiesto_model->update_estatus($tracking_recibido, $id_estatus);

			$datos = [
				'item' => 1,
				'fecha' => $fecha_response, //date('Y-m-d H:i:s'),
				'warehouse' => $tracking_recibido,
				'id_estatus' => $id_estatus,
				'casillero' => '100',
				'id_pais' => 1,
				'pod_nombre' => '',
				'pod_valor' => "",
				'json_string' => json_encode(['guia' => '']),
				'ruta_foto' => '',
				'fechaiso' => $fecha_iso,
				'problema_gestion' => $description
			];

			$this->Manifiesto_model->guardar_estatus($datos);

			$this->DTES_Model->updatetracking_usps($id_estatus, $tracking_recibido);

			$statusName = "";
			if ($id_estatus == 16 || $id_estatus == 19) {
				$resultado = $this->Manifiesto_model->buscarPorDescripcion($problemaGestion ?? '');
				$statusName = $resultado ? $resultado[0]['codigo_cliente'] : '';
			} else {
				$resultado = $this->Manifiesto_model->buscarPorCodigo($id_estatus);
				$statusName = $resultado ? $resultado[0]['codigo_cliente'] : '';
			}

			if ($devolucion) {
				$statusName = "RO";
			}

			$token = $this->tokenapg->getToken();

			if (!$token) {
				$this->_registrarEventoInterno(
					'No se pudo obtener el token de APG.',
					$tracking_recibido,
					'error',
					[], // puedes incluir respuesta si el método getToken() la retorna
					$codigo_recibido,
					"",
					$entregado
				);
			}

			// Enviar a APG
			//$url = 'https://api.apg-global.com/api/account/save-status';
			$url = 'https://apiprod.apg-global.com/api/account/save-status';
			$data = [
				'tracking' => $tracking_recibido,
				'status' => [
					[
						'statusName' => $statusName,
						'location' => 'El Salvador',
						'statusDate' => $fecha_response, //date('Y-m-d H:i:s'),
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

			if ($error) {
				$this->_registrarEventoInterno('Error al comunicarse con APG.', $tracking_recibido, 'fallido', $error, $id_estatus, $problemaGestion, $entregado);
				return;
			}

			$respuesta_apg = json_decode($response, true);


			$response_created = $data['objectResult']['status'][0]['response'];
			$valor = $respuesta_apg['objectResult']['response'] ?? null;
			if ($valor == "OK") {
				$entregado = "OK";
			}

			if ($response_created == "Created") {
				$entregado = "OK";
			}

			// Guarda el registro (y el JSON ya decodificado para auditar)
			$this->_registrarEventoInterno(
				'Respuesta de APG',
				$tracking_recibido,
				'entregado',
				$respuesta_apg ?? $response, // si falló decode, guarda crudo
				$id_estatus,
				$problemaGestion,
				$entregado
			);

			$this->_updateRespuesta($tracking_recibido, $respuesta_apg ?? $response, $id_estatus, $entregado, $problemaGestion,  json_encode($row, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
		} catch (Throwable $e) {
			error_log('Error inesperado en estadosApg01_post: ' . $e->getMessage());
			http_response_code(500);
			$this->_responderInmediatamente([
				'error' => true,
				'mensaje' => 'Error interno del servidor.',
				'detalle' => $e->getMessage()
			]);
		}
	}


	public function estadosApglast_post()
	{
		$this->load->model('Entrega_model');
		$this->load->model('Manifiesto_model');
		$this->load->model('DTES_Model');
		$this->load->library('TokenApg');
		$this->load->database(); // por si no está autoload

		try {
			ini_set('display_errors', '1');
			ini_set('display_startup_errors', '1');
			error_reporting(E_ALL);

			date_default_timezone_set("America/El_Salvador");
			header('Content-Type: application/json');

			$rawData = file_get_contents('php://input');
			$row     = json_decode($rawData, true);

			if (!$row || !is_array($row)) {
				http_response_code(400);
				$this->_responderInmediatamente([
					'error' => true,
					'mensaje' => 'Formato JSON inválido.'
				]);
				return;
			}

			$entregado         = 'NO';
			$codigo_recibido   = $row['codigo'] ?? null;
			$tracking_recibido = $row['guia']   ?? null;
			$fecha_response    = $row['fecha']  ?? null; // 'Y-m-d H:i:s' esperado

			if (!$codigo_recibido || !$tracking_recibido) {
				http_response_code(400);
				$this->_responderInmediatamente([
					'error' => true,
					'mensaje' => 'Faltan datos requeridos: codigo o guia.'
				]);
				return;
			}

			// Normaliza/valida fecha
			$dtBase = DateTime::createFromFormat('Y-m-d H:i:s', (string)$fecha_response);
			if (!$dtBase) {
				$dtBase = new DateTime((string)$fecha_response); // fallback ISO u otros
			}
			$fecha_iso = $dtBase->format(DateTime::ATOM);

			// Verifica tracking
			$request_tracking = $this->Manifiesto_model->obtener_tracking($tracking_recibido);
			if (!$request_tracking) {
				http_response_code(404);
				$this->_responderInmediatamente([
					'error' => true,
					'mensaje' => "Tracking {$tracking_recibido} no encontrado."
				]);
				return;
			}

			// Respuesta inmediata
			$this->_responderInmediatamente([
				'error' => false,
				'message' => 'Recibido correctamente.',
				'tracking' => $tracking_recibido
			]);

			// Anti-duplicados 15/311 (tu lógica)
			$existe = $this->Manifiesto_model->verificar_pod($tracking_recibido, $codigo_recibido);
			if ($existe) {
				$info = [
					"tracking_number" => $tracking_recibido,
					"id_estatus"      => $codigo_recibido,
					"fecha"           => date("Y-m-d H:i:s")
				];
				$this->Manifiesto_model->guardar_alerta($info);
				$this->_registrarEventoInterno(
					'Ya existe un registro con estatus 15 para ese tracking.',
					$tracking_recibido,
					'repetido',
					null,
					$codigo_recibido,
					"",
					$entregado
				);
				return;
			}

			$existe_ro = $this->Manifiesto_model->verificar_Ro($tracking_recibido, $codigo_recibido);
			if ($existe_ro) {
				$this->_registrarEventoInterno(
					'Ya existe un registro con estatus 311 para ese tracking.',
					$tracking_recibido,
					'Repetido',
					null,
					$codigo_recibido,
					"",
					$entregado
				);
				return;
			}

			// ======================
			// HELPERS (closures y utilitarios)
			// ======================

			// Inserta evento en detalle y actualiza encabezado + DTES
			$insertarEvento = function (int $id_estatus, DateTime $dt, string $problemaGestion = '', string $descripcion = '') use ($tracking_recibido) {
				$fechah  = $dt->format('Y-m-d H:i:s');
				$fechais = $dt->format(DateTime::ATOM);

				// Actualiza encabezado con el estatus actual
				$this->Manifiesto_model->update_estatus($tracking_recibido, $id_estatus);

				// Inserta detalle
				$datos = [
					'item'             => 1,
					'fecha'            => $fechah,
					'warehouse'        => $tracking_recibido,
					'id_estatus'       => $id_estatus,
					'casillero'        => '100',
					'id_pais'          => 1,
					'pod_nombre'       => '',
					'pod_valor'        => '',
					'json_string'      => json_encode(['guia' => '']),
					'ruta_foto'        => '',
					'fechaiso'         => $fechais,
					'problema_gestion' => ($descripcion !== '' ? $descripcion : $problemaGestion),
				];
				$this->Manifiesto_model->guardar_estatus($datos);

				// Sincroniza a tu otro sistema interno
				$this->DTES_Model->updatetracking_usps($id_estatus, $tracking_recibido);
			};

			// Resuelve statusName según tu regla (por código o por descripción)
			$resolverStatusName = function (int $id_estatus, string $problemaGestion = '') {
				if ($id_estatus === 16 || $id_estatus === 19) {
					$r = $this->Manifiesto_model->buscarPorDescripcion($problemaGestion ?? '');
					return $r ? $r[0]['codigo_cliente'] : '';
				} else {
					$r = $this->Manifiesto_model->buscarPorCodigo($id_estatus);
					return $r ? $r[0]['codigo_cliente'] : '';
				}
			};

			// ====== CLOSURE 1: Handle cURL reusado para APG ======
			$apgUrl = 'https://apiprod.apg-global.com/api/account/save-status';
			$crearHandleApg = function (string $token) use ($apgUrl) {
				$authHeader = (stripos($token, 'bearer ') === 0) ? $token : ('Bearer ' . $token);
				$ch = curl_init($apgUrl);
				curl_setopt_array($ch, [
					CURLOPT_RETURNTRANSFER    => true,
					CURLOPT_POST              => true,
					CURLOPT_HTTPHEADER        => [
						'Authorization: ' . $authHeader,
						'Content-Type: application/json',
						'Accept: application/json',
						'Expect:',
						'Connection: keep-alive',
						'Keep-Alive: timeout=5, max=1000',
					],
					CURLOPT_TIMEOUT           => 60,
					CURLOPT_CONNECTTIMEOUT    => 20,
					CURLOPT_IPRESOLVE         => CURL_IPRESOLVE_V4,
					CURLOPT_NOSIGNAL          => true,
					CURLOPT_HTTP_VERSION      => CURL_HTTP_VERSION_1_1,
					CURLOPT_DNS_CACHE_TIMEOUT => 300,
					CURLOPT_FORBID_REUSE      => false,
					CURLOPT_FRESH_CONNECT     => false,
				]);
				return $ch;
			};

			// ====== CLOSURE 2: Envío a APG con reintentos/backoff ======
			$enviarApg = function ($ch, int $id_estatus, string $tracking_recibido, string $statusName, string $fechaEnvio, string $problemaGestion) use (&$entregado) {
				$payload = [
					'tracking' => $tracking_recibido,
					'status'   => [[
						'statusName' => $statusName,
						'location'   => 'El Salvador',
						'statusDate' => $fechaEnvio, // 'Y-m-d H:i:s'
						'pod'        => null,
					]],
				];
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

				$maxAttempts   = 4;
				$attempt       = 0;
				$respuesta_apg = null;
				$ok            = false;


				$lastCurlNo    = null;
				$lastCurlErr   = null;
				$lastHttp      = null;
				$lastBody      = null;
				$finalRetryable = false; // ¿era un error reintetable (timeout/5xx/etc)? 


				$retryableCurl = [6, 7, 28, 52, 55, 56];
				$retryableHttp = [408, 429, 500, 502, 503, 504, 520];

				while ($attempt < $maxAttempts && !$ok) {
					$attempt++;

					$resp    = curl_exec($ch);
					$curlErr = curl_error($ch);
					$curlNo  = curl_errno($ch);
					$http    = curl_getinfo($ch, CURLINFO_HTTP_CODE);

					if ($curlErr) {
						$esRetry = in_array($curlNo, $retryableCurl, true);
						$this->_registrarEventoInterno(
							"APG cURL intento {$attempt}" . ($esRetry ? " (retry)" : ""),
							$tracking_recibido,
							$esRetry ? 'warning' : 'fallido',
							['curl_errno' => $curlNo, 'curl_error' => $curlErr],
							$id_estatus,
							$problemaGestion,
							$entregado
						);
						if ($esRetry && $attempt < $maxAttempts) {
							$ms = (int) (500 * pow(2, $attempt - 1) + random_int(0, 250));
							usleep($ms * 1000);
							continue;
						}
						break;
					}

					if ($http < 200 || $http >= 300) {
						$esRetry = in_array($http, $retryableHttp, true);
						$this->_registrarEventoInterno(
							"APG HTTP {$http} intento {$attempt}" . ($esRetry ? " (retry)" : ""),
							$tracking_recibido,
							$esRetry ? 'warning' : 'fallido',
							['http_code' => $http, 'body' => $resp],
							$id_estatus,
							$problemaGestion,
							$entregado
						);
						if ($esRetry && $attempt < $maxAttempts) {
							$ms = (int) (600 * pow(2, $attempt - 1) + random_int(0, 300));
							usleep($ms * 1000);
							continue;
						}
						break;
					}

					$respuesta_apg = json_decode($resp, true);
					if (json_last_error() !== JSON_ERROR_NONE) {
						$this->_registrarEventoInterno(
							"APG JSON inválido intento {$attempt}",
							$tracking_recibido,
							'fallido',
							['http_code' => $http, 'body' => $resp, 'json_error' => json_last_error_msg()],
							$id_estatus,
							$problemaGestion,
							$entregado
						);
						break;
					}

					$isSuccess       = $respuesta_apg['isSuccess'] ?? null;
					$respOK          = $respuesta_apg['objectResult']['response'] ?? null;
					$respStatusField = $respuesta_apg['objectResult']['status'][0]['response'] ?? null;

					if ($respOK === 'OK' || $respStatusField === 'Created' || $isSuccess === true) {
						$entregado = 'OK';
					}

					$this->_registrarEventoInterno(
						"APG OK intento {$attempt} (estatus {$id_estatus})",
						$tracking_recibido,
						'entregado',
						['http_code' => $http, 'resp' => $respuesta_apg],
						$id_estatus,
						$problemaGestion,
						$entregado
					);

					// Si quieres reactivar el guardado de respuesta en detalle, descomenta:
					// $this->_updateRespuesta($tracking_recibido, $respuesta_apg, $id_estatus, $entregado, $problemaGestion, null);

					$ok = true;
				}

				if (!$ok) {
					// Encola solo si el último error era reintetable; si no, puedes mandar a dead-letter/log
					$this->Entrega_model->enqueueOutbox(
						$tracking_recibido,
						$id_estatus,
						$statusName,
						$fechaEnvio,
						$payload,
						[
							'curl_errno' => $lastCurlNo,
							'curl_error' => $lastCurlErr,
							'http_code'  => $lastHttp,
							'body'       => $lastBody,
							'retryable'  => $finalRetryable
						]
					);
				}
				return $ok ? $respuesta_apg : null;
			};

			// ======================
			// LÓGICA PRINCIPAL
			// ======================
			$id_estatus_original = (int)$codigo_recibido;

			// *** VALIDACIÓN NUEVA ***
			// Si llega 19: revisa si YA existe algún 19 para este tracking.
			$yaTiene19 = false;
			if ($id_estatus_original === 19) {
				$yaTiene19 = $this->db
					->select('1')
					->from('detalle')
					->where('warehouse', $tracking_recibido)
					->where('id_estatus', 19)
					->limit(1)
					->get()
					->num_rows() > 0;
			}

			// FLUJO ESPECIAL para 19
			if ($id_estatus_original === 19) {

				if ($yaTiene19) {
					// ---- SOLO 19 (sin 219 ni 10) ----
					$descripcion_19     = $row['observaciones'] ?? '';
					$problemaGestion_19 = $descripcion_19;

					// Insertar evento 19 con fecha base (sin offset)
					$insertarEvento(19, $dtBase, $problemaGestion_19, $descripcion_19);

					// Token + envío APG SOLO 19
					$token = $this->tokenapg->getToken();
					if (!$token) {
						$this->_registrarEventoInterno(
							'No se pudo obtener el token de APG.',
							$tracking_recibido,
							'error',
							[],
							19,
							$problemaGestion_19,
							$entregado
						);
						return;
					}
					$ch   = $crearHandleApg($token);
					$sn19 = $resolverStatusName(19, $problemaGestion_19);
					$enviarApg($ch, 19, $tracking_recibido, $sn19, $dtBase->format('Y-m-d H:i:s'), $problemaGestion_19);
					curl_close($ch);
					return;
				}

				// ---- CADENA COMPLETA: 219 (t), 10 (t+2), 19 (t+4) ----
				$dt219 = clone $dtBase;                        // 219 fecha base
				$dt10  = (clone $dtBase)->modify('+2 minutes'); // 10  +2
				$dt19  = (clone $dtBase)->modify('+4 minutes'); // 19  +4

				// Inserciones locales
				$insertarEvento(219, $dt219);
				$insertarEvento(10,  $dt10);
				$descripcion_19     = $row['observaciones'] ?? '';
				$problemaGestion_19 = $descripcion_19;        // para 19 usas observaciones
				$insertarEvento(19,  $dt19, $problemaGestion_19, $descripcion_19);

				// Token y handle reusado
				$token = $this->tokenapg->getToken();
				if (!$token) {
					$this->_registrarEventoInterno(
						'No se pudo obtener el token de APG.',
						$tracking_recibido,
						'error',
						[],
						19,
						$problemaGestion_19,
						$entregado
					);
					return;
				}
				$ch = $crearHandleApg($token);

				// 219 -> statusName por código
				$sn219 = $resolverStatusName(219, '');
				$enviarApg($ch, 219, $tracking_recibido, $sn219, $dt219->format('Y-m-d H:i:s'), '');

				// 10  -> statusName por código
				$sn10 = $resolverStatusName(10, '');
				$enviarApg($ch, 10, $tracking_recibido, $sn10, $dt10->format('Y-m-d H:i:s'), '');

				// 19  -> statusName por descripción (observaciones)
				$sn19 = $resolverStatusName(19, $problemaGestion_19);
				$enviarApg($ch, 19, $tracking_recibido, $sn19, $dt19->format('Y-m-d H:i:s'), $problemaGestion_19);

				curl_close($ch);
				return;
			}

			// --------- FLUJO NORMAL (no es 19) ----------
			$problemaGestion = "";
			$id_estatus      = (int)$row['codigo'];
			$description     = "";

			if ($id_estatus == 16 || $id_estatus == 19) {
				$description     = ($row['observaciones'] ?? '');
				$problemaGestion = $row['razon']['descripcion'] ?? '';
			}

			$devolucion = false;
			if ($id_estatus == 15 && ($row['devolucion'] ?? 0) == 1) {
				$id_estatus = 311;
				$devolucion = true;
			}

			if ($id_estatus == 19) {
				$problemaGestion = ($row['observaciones'] ?? '');
			}

			// Encabezado + detalle
			$this->Manifiesto_model->update_estatus($tracking_recibido, $id_estatus);
			$datos = [
				'item'             => 1,
				'fecha'            => $dtBase->format('Y-m-d H:i:s'),
				'warehouse'        => $tracking_recibido,
				'id_estatus'       => $id_estatus,
				'casillero'        => '100',
				'id_pais'          => 1,
				'pod_nombre'       => '',
				'pod_valor'        => "",
				'json_string'      => json_encode(['guia' => '']),
				'ruta_foto'        => '',
				'fechaiso'         => $fecha_iso,
				'problema_gestion' => $description
			];
			$this->Manifiesto_model->guardar_estatus($datos);
			$this->DTES_Model->updatetracking_usps($id_estatus, $tracking_recibido);

			// statusName
			if ($id_estatus == 16 || $id_estatus == 19) {
				$resultado  = $this->Manifiesto_model->buscarPorDescripcion($problemaGestion ?? '');
				$statusName = $resultado ? $resultado[0]['codigo_cliente'] : '';
			} else {
				$resultado  = $this->Manifiesto_model->buscarPorCodigo($id_estatus);
				$statusName = $resultado ? $resultado[0]['codigo_cliente'] : '';
			}
			if ($devolucion) {
				$statusName = "RO";
			}

			// Enviar a APG (flujo normal) con cliente reusado
			$token = $this->tokenapg->getToken();
			if ($token) {
				$ch = $crearHandleApg($token);
				$enviarApg($ch, $id_estatus, $tracking_recibido, $statusName, $dtBase->format('Y-m-d H:i:s'), $problemaGestion);
				curl_close($ch);
			} else {
				$this->_registrarEventoInterno(
					'No se pudo obtener el token de APG.',
					$tracking_recibido,
					'error',
					[],
					$id_estatus,
					$problemaGestion,
					$entregado
				);
			}
		} catch (Throwable $e) {
			error_log('Error inesperado en estadosApg_post: ' . $e->getMessage());
			http_response_code(500);
			$this->_responderInmediatamente([
				'error'   => true,
				'mensaje' => 'Error interno del servidor.',
				'detalle' => $e->getMessage()
			]);
		}
	}


	public function estadosApg_post()
	{
		$this->load->model('Entrega_model');
		$this->load->model('Manifiesto_model');
		$this->load->model('DTES_Model');
		$this->load->library('TokenApg');

		try {
			ini_set('display_errors', '1');
			ini_set('display_startup_errors', '1');
			error_reporting(E_ALL);

			date_default_timezone_set("America/El_Salvador");
			header('Content-Type: application/json');

			$rawData = file_get_contents('php://input');
			$row     = json_decode($rawData, true);

			if (!$row || !is_array($row)) {
				http_response_code(400);
				$this->_responderInmediatamente([
					'error' => true,
					'mensaje' => 'Formato JSON inválido.'
				]);
				return;
			}

			$entregado         = 'NO';
			$codigo_recibido   = $row['codigo'] ?? null;
			$tracking_recibido = $row['guia']   ?? null;
			$fecha_response    = $row['fecha']  ?? null; // 'Y-m-d H:i:s' esperado

			if (!$codigo_recibido || !$tracking_recibido) {
				http_response_code(400);
				$this->_responderInmediatamente([
					'error' => true,
					'mensaje' => 'Faltan datos requeridos: codigo o guia.'
				]);
				return;
			}

			// Normaliza/valida fecha
			$dtBase = DateTime::createFromFormat('Y-m-d H:i:s', (string)$fecha_response);
			if (!$dtBase) {
				$dtBase = new DateTime((string)$fecha_response); // fallback ISO u otros
			}
			$fecha_iso = $dtBase->format(DateTime::ATOM);

			// Verifica tracking
			$request_tracking = $this->Manifiesto_model->obtener_tracking($tracking_recibido);
			if (!$request_tracking) {
				http_response_code(404);
				$this->_responderInmediatamente([
					'error' => true,
					'mensaje' => "Tracking {$tracking_recibido} no encontrado."
				]);
				return;
			}

			// Respuesta inmediata
			$this->_responderInmediatamente([
				'error' => false,
				'message' => 'Recibido correctamente.',
				'tracking' => $tracking_recibido
			]);

			// Anti-duplicados
			$existe = $this->Manifiesto_model->verificar_pod($tracking_recibido, $codigo_recibido);
			if ($existe) {
				$info = [
					"tracking_number" => $tracking_recibido,
					"id_estatus"      => $codigo_recibido,
					"fecha"           => date("Y-m-d H:i:s")
				];
				$this->Manifiesto_model->guardar_alerta($info);
				$this->_registrarEventoInterno(
					'Ya existe un registro con estatus 15 para ese tracking.',
					$tracking_recibido,
					'repetido',
					null,
					$codigo_recibido,
					"",
					$entregado
				);
				return;
			}

			$existe_ro = $this->Manifiesto_model->verificar_Ro($tracking_recibido, $codigo_recibido);
			if ($existe_ro) {
				$this->_registrarEventoInterno(
					'Ya existe un registro con estatus 311 para ese tracking.',
					$tracking_recibido,
					'Repetido',
					null,
					$codigo_recibido,
					"",
					$entregado
				);
				return;
			}

			// ======================
			// HELPERS (closures y utilitarios)
			// ======================

			// Inserta evento en detalle y actualiza encabezado + DTES
			$insertarEvento = function (int $id_estatus, DateTime $dt, string $problemaGestion = '', string $descripcion = '') use ($tracking_recibido) {
				$fechah  = $dt->format('Y-m-d H:i:s');
				$fechais = $dt->format(DateTime::ATOM);

				// Actualiza encabezado con el estatus actual
				$this->Manifiesto_model->update_estatus($tracking_recibido, $id_estatus);

				// Inserta detalle
				$datos = [
					'item'             => 1,
					'fecha'            => $fechah,
					'warehouse'        => $tracking_recibido,
					'id_estatus'       => $id_estatus,
					'casillero'        => '100',
					'id_pais'          => 1,
					'pod_nombre'       => '',
					'pod_valor'        => '',
					'json_string'      => json_encode(['guia' => '']),
					'ruta_foto'        => '',
					'fechaiso'         => $fechais,
					'problema_gestion' => ($descripcion !== '' ? $descripcion : $problemaGestion),
				];
				$this->Manifiesto_model->guardar_estatus($datos);

				// Sincroniza a tu otro sistema interno
				$this->DTES_Model->updatetracking_usps($id_estatus, $tracking_recibido);
			};

			// Resuelve statusName según tu regla (por código o por descripción)
			$resolverStatusName = function (int $id_estatus, string $problemaGestion = '') {
				if ($id_estatus === 16 || $id_estatus === 19) {
					$r = $this->Manifiesto_model->buscarPorDescripcion($problemaGestion ?? '');
					return $r ? $r[0]['codigo_cliente'] : '';
				} else {
					$r = $this->Manifiesto_model->buscarPorCodigo($id_estatus);
					return $r ? $r[0]['codigo_cliente'] : '';
				}
			};

			// ====== Helper: Encolar en APG Outbox cuando no se pudo enviar ======
			$enqueueOutbox = function (string $tracking, int $id_estatus, string $statusName, DateTime $dt, array $extra = []) {
				$fechaEnvio = $dt->format('Y-m-d H:i:s');
				$payload = [
					'tracking' => $tracking,
					'status'   => [[
						'statusName' => $statusName,
						'location'   => 'El Salvador',
						'statusDate' => $fechaEnvio,
						'pod'        => null,
					]],
				];
				$this->Entrega_model->enqueueOutbox(
					$tracking,
					$id_estatus,
					$statusName,
					$fechaEnvio,
					$payload,
					$extra
				);
			};

			// ====== CLOSURE 1: Handle cURL reusado para APG ======
			$apgUrl = 'https://apiprod.apg-global.com/api/account/save-status';
			$crearHandleApg = function (string $token) use ($apgUrl) {
				$authHeader = (stripos($token, 'bearer ') === 0) ? $token : ('Bearer ' . $token);
				$ch = curl_init($apgUrl);
				curl_setopt_array($ch, [
					CURLOPT_RETURNTRANSFER    => true,
					CURLOPT_POST              => true,
					CURLOPT_HTTPHEADER        => [
						'Authorization: ' . $authHeader,
						'Content-Type: application/json',
						'Accept: application/json',
						'Expect:',
						'Connection: keep-alive',
						'Keep-Alive: timeout=5, max=1000',
					],
					CURLOPT_TIMEOUT           => 60,   // total
					CURLOPT_CONNECTTIMEOUT    => 20,   // conexión
					CURLOPT_IPRESOLVE         => CURL_IPRESOLVE_V4,
					CURLOPT_NOSIGNAL          => true,
					CURLOPT_HTTP_VERSION      => CURL_HTTP_VERSION_1_1,
					CURLOPT_DNS_CACHE_TIMEOUT => 300,
					CURLOPT_FORBID_REUSE      => false,
					CURLOPT_FRESH_CONNECT     => false,
				]);
				return $ch;
			};

			// ====== CLOSURE 2: Envío a APG con reintentos/backoff + outbox si falla ======
			$enviarApg = function ($ch, int $id_estatus, string $tracking_recibido, string $statusName, string $fechaEnvio, string $problemaGestion) use (&$entregado) {
				$payload = [
					'tracking' => $tracking_recibido,
					'status'   => [[
						'statusName' => $statusName,
						'location'   => 'El Salvador',
						'statusDate' => $fechaEnvio, // 'Y-m-d H:i:s'
						'pod'        => null,
					]],
				];
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

				$maxAttempts     = 4; // 1 intento + 3 reintentos
				$attempt         = 0;
				$respuesta_apg   = null;
				$ok              = false;

				// Para auditar y encolar si todo falla
				$lastCurlNo      = null;
				$lastCurlErr     = null;
				$lastHttp        = null;
				$lastBody        = null;

				$retryableCurl = [6, 7, 28, 52, 55, 56];               // resolve/connect/timeout/got nothing/send/recv
				$retryableHttp = [408, 429, 500, 502, 503, 504, 520];   // timeouts, rate limit, 5xx

				while ($attempt < $maxAttempts && !$ok) {
					$attempt++;

					$resp        = curl_exec($ch);
					$lastCurlErr = curl_error($ch);
					$lastCurlNo  = curl_errno($ch);
					$lastHttp    = curl_getinfo($ch, CURLINFO_HTTP_CODE);
					$lastBody    = $resp;

					if ($lastCurlErr) {
						$esRetry = in_array($lastCurlNo, $retryableCurl, true);
						$this->_registrarEventoInterno(
							"APG cURL intento {$attempt}" . ($esRetry ? " (retry)" : ""),
							$tracking_recibido,
							$esRetry ? 'warning' : 'fallido',
							['curl_errno' => $lastCurlNo, 'curl_error' => $lastCurlErr],
							$id_estatus,
							$problemaGestion,
							$entregado
						);
						if ($esRetry && $attempt < $maxAttempts) {
							$ms = (int) (500 * pow(2, $attempt - 1) + random_int(0, 250));
							usleep($ms * 1000);
							continue;
						}
						break; // no retryable o agotado
					}

					if ($lastHttp < 200 || $lastHttp >= 300) {
						$esRetry = in_array($lastHttp, $retryableHttp, true);
						$this->_registrarEventoInterno(
							"APG HTTP {$lastHttp} intento {$attempt}" . ($esRetry ? " (retry)" : ""),
							$tracking_recibido,
							$esRetry ? 'warning' : 'fallido',
							['http_code' => $lastHttp, 'body' => $lastBody],
							$id_estatus,
							$problemaGestion,
							$entregado
						);
						if ($esRetry && $attempt < $maxAttempts) {
							$ms = (int) (600 * pow(2, $attempt - 1) + random_int(0, 300));
							usleep($ms * 1000);
							continue;
						}
						break;
					}

					// 2xx: parseamos
					$respuesta_apg = json_decode($resp, true);
					if (json_last_error() !== JSON_ERROR_NONE) {
						$this->_registrarEventoInterno(
							"APG JSON inválido intento {$attempt}",
							$tracking_recibido,
							'fallido',
							['http_code' => $lastHttp, 'body' => $lastBody, 'json_error' => json_last_error_msg()],
							$id_estatus,
							$problemaGestion,
							$entregado
						);
						break;
					}

					// Éxito lógico
					$isSuccess       = $respuesta_apg['isSuccess'] ?? null;
					$respOK          = $respuesta_apg['objectResult']['response'] ?? null;
					$respStatusField = $respuesta_apg['objectResult']['status'][0]['response'] ?? null;

					if ($respOK === 'OK' || $respStatusField === 'Created' || $isSuccess === true) {
						$entregado = 'OK';
					}

					// Auditoría del intento exitoso
					$this->_registrarEventoInterno(
						"APG OK intento {$attempt} (estatus {$id_estatus})",
						$tracking_recibido,
						'entregado',
						['http_code' => $lastHttp, 'resp' => $respuesta_apg],
						$id_estatus,
						$problemaGestion,
						$entregado
					);

					// Si deseas guardar la respuesta en detalle, descomenta:
					// $this->_updateRespuesta($tracking_recibido, $respuesta_apg, $id_estatus, $entregado, $problemaGestion, null);

					$ok = true;
				} // fin while

				// Si agotó intentos, encola para reintento por worker
				if (!$ok) {
					$this->Entrega_model->enqueueOutbox(
						$tracking_recibido,
						$id_estatus,
						$statusName,
						$fechaEnvio,
						$payload,
						[
							'curl_errno' => $lastCurlNo,
							'curl_error' => $lastCurlErr,
							'http_code'  => $lastHttp,
							'body'       => $lastBody
						]
					);
				}

				return $ok ? $respuesta_apg : null;
			};

			// ======================
			// LÓGICA PRINCIPAL
			// ======================
			$id_estatus_original = (int)$codigo_recibido;

			// *** VALIDACIÓN NUEVA ***
			// Si llega 19: revisa si YA existe algún 19 para este tracking.
			$yaTiene19 = false;
			if ($id_estatus_original === 19) {
				$yaTiene19 = $this->db
					->select('1')
					->from('detalle')
					->where('warehouse', $tracking_recibido)
					->where('id_estatus', 19)
					->limit(1)
					->get()
					->num_rows() > 0;
			}

			// FLUJO ESPECIAL para 19
			if ($id_estatus_original === 19) {

				if ($yaTiene19) {
					// ---- SOLO 19 (sin 219 ni 10) ----
					$descripcion_19     = $row['observaciones'] ?? '';
					$problemaGestion_19 = $descripcion_19;

					// Insertar evento 19 con fecha base (sin offset)
					$insertarEvento(19, $dtBase, $problemaGestion_19, $descripcion_19);

					// Token + envío APG SOLO 19 (si no hay token, encola)
					$sn19  = $resolverStatusName(19, $problemaGestion_19);
					$token = $this->tokenapg->getToken();
					if (!$token) {
						$enqueueOutbox($tracking_recibido, 19, $sn19, $dtBase, ['reason' => 'token_unavailable']);
						$this->_registrarEventoInterno(
							'Token APG no disponible: evento 19 encolado',
							$tracking_recibido,
							'warning',
							[],
							19,
							$problemaGestion_19,
							$entregado
						);
						return;
					}

					$ch = $crearHandleApg($token);
					$enviarApg($ch, 19, $tracking_recibido, $sn19, $dtBase->format('Y-m-d H:i:s'), $problemaGestion_19);
					curl_close($ch);
					return;
				}

				// ---- CADENA COMPLETA: 219 (t), 10 (t+2), 19 (t+4) ----
				$dt219 = clone $dtBase;                         // 219 fecha base
				$dt10  = (clone $dtBase)->modify('+2 minutes'); // 10  +2
				$dt19  = (clone $dtBase)->modify('+4 minutes'); // 19  +4

				// Inserciones locales
				$insertarEvento(219, $dt219);
				//$insertarEvento(10,  $dt10);
				$descripcion_19     = $row['observaciones'] ?? '';
				$problemaGestion_19 = $descripcion_19;          // para 19 usas observaciones
				$insertarEvento(19,  $dt19, $problemaGestion_19, $descripcion_19);

				// Resolver statusName ANTES del token
				$sn219 = $resolverStatusName(219, '');
				$sn10  = $resolverStatusName(10,  '');
				$sn19  = $resolverStatusName(19,  $problemaGestion_19);

				// Token y handle reusado (si no hay token, encola los tres)
				$token = $this->tokenapg->getToken();
				if (!$token) {
					$enqueueOutbox($tracking_recibido, 219, $sn219, $dt219, ['reason' => 'token_unavailable']);
					//$enqueueOutbox($tracking_recibido, 10,  $sn10,  $dt10,  ['reason' => 'token_unavailable']);
					$enqueueOutbox($tracking_recibido, 19,  $sn19,  $dt19,  ['reason' => 'token_unavailable']);

					$this->_registrarEventoInterno(
						'Token APG no disponible: cadena 219/10/19 encolada',
						$tracking_recibido,
						'warning',
						[],
						19,
						$problemaGestion_19,
						$entregado
					);
					return;
				}

				$ch = $crearHandleApg($token);

				// Envíos granulares
				$enviarApg($ch, 219, $tracking_recibido, $sn219, $dt219->format('Y-m-d H:i:s'), '');
				//$enviarApg($ch, 10,  $tracking_recibido, $sn10,  $dt10->format('Y-m-d H:i:s'),  '');
				$enviarApg($ch, 19,  $tracking_recibido, $sn19,  $dt19->format('Y-m-d H:i:s'),  $problemaGestion_19);

				curl_close($ch);
				return;
			}

			// --------- FLUJO NORMAL (no es 19) ----------
			$problemaGestion = "";
			$id_estatus      = (int)$row['codigo'];
			$description     = "";

			if ($id_estatus == 16 || $id_estatus == 19) {
				$description     = ($row['observaciones'] ?? '');
				$problemaGestion = $row['razon']['descripcion'] ?? '';
			}

			$devolucion = false;
			if ($id_estatus == 15 && ($row['devolucion'] ?? 0) == 1) {
				$id_estatus = 311;
				$devolucion = true;
			}

			if ($id_estatus == 19) {
				$problemaGestion = ($row['observaciones'] ?? '');
			}

			// Encabezado + detalle
			$this->Manifiesto_model->update_estatus($tracking_recibido, $id_estatus);
			$datos = [
				'item'             => 1,
				'fecha'            => $dtBase->format('Y-m-d H:i:s'),
				'warehouse'        => $tracking_recibido,
				'id_estatus'       => $id_estatus,
				'casillero'        => '100',
				'id_pais'          => 1,
				'pod_nombre'       => '',
				'pod_valor'        => "",
				'json_string'      => json_encode(['guia' => '']),
				'ruta_foto'        => '',
				'fechaiso'         => $fecha_iso,
				'problema_gestion' => $description
			];
			$this->Manifiesto_model->guardar_estatus($datos);
			$this->DTES_Model->updatetracking_usps($id_estatus, $tracking_recibido);

			// statusName
			if ($id_estatus == 16 || $id_estatus == 19) {
				$resultado  = $this->Manifiesto_model->buscarPorDescripcion($problemaGestion ?? '');
				$statusName = $resultado ? $resultado[0]['codigo_cliente'] : '';
			} else {
				$resultado  = $this->Manifiesto_model->buscarPorCodigo($id_estatus);
				$statusName = $resultado ? $resultado[0]['codigo_cliente'] : '';
			}
			if ($devolucion) {
				$statusName = "RO";
			}

			// Enviar a APG (flujo normal) con cliente reusado; si no hay token, encola
			$token = $this->tokenapg->getToken();
			if ($token) {
				$ch = $crearHandleApg($token);
				$enviarApg($ch, $id_estatus, $tracking_recibido, $statusName, $dtBase->format('Y-m-d H:i:s'), $problemaGestion);
				curl_close($ch);
			} else {
				$enqueueOutbox($tracking_recibido, $id_estatus, $statusName, $dtBase, ['reason' => 'token_unavailable']);
				$this->_registrarEventoInterno(
					'Token APG no disponible: evento encolado',
					$tracking_recibido,
					'warning',
					[],
					$id_estatus,
					$problemaGestion,
					$entregado
				);
			}
		} catch (Throwable $e) {
			error_log('Error inesperado en estadosApg_post: ' . $e->getMessage());
			http_response_code(500);
			$this->_responderInmediatamente([
				'error'   => true,
				'mensaje' => 'Error interno del servidor.',
				'detalle' => $e->getMessage()
			]);
		}
	}

	private function _registrarEventoInterno($descripcion, $tracking, $estado = 'info', $respuesta = null, $id_estatus = null, $problemaGestion, $entregado)
	{
		$this->Entrega_model->registrar([
			'tracking'         => $tracking,
			'id_estatus'       => $id_estatus,
			'estado'           => $estado,
			'mensaje'          => $descripcion,
			'respuesta'        => is_array($respuesta) ? json_encode($respuesta) : $respuesta,
			'problema_gestion' => $problemaGestion,
			'entregado'   => $entregado
		]);
	}


	private function _updateRespuesta($tracking, $respuesta = null, $id_estatus = null, $entregado = null, $mala_gestion = null, $req = null)
	{
		$this->Entrega_model->UpdateRespuesta([
			'tracking'      => $tracking,
			'id_estatus'    => $id_estatus,
			'respuesta'     => is_array($respuesta) ? json_encode($respuesta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : $respuesta,
			'entregado'     => $entregado,
			'mala_gestion'  => $mala_gestion,
			'req'           => $req, // ← nuevo
		]);
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


		ob_flush();
		flush();
		if (function_exists('fastcgi_finish_request')) {
			fastcgi_finish_request(); // ✨ para FPM
		}
	}


	public function registrar($data)
	{
		$data['fecha'] = date('Y-m-d H:i:s');
		$this->db->insert('log_entregas', $data);
	}

	public function facturafel_post($tracking, $id_estatus)
	{

		date_default_timezone_set("America/El_Salvador");

		$query = $this->Manifiesto_model->get_tracking($tracking);
		
		// Datos de la guía / empresa
		$guiaRow  = $query[0];
		$cliente  = $this->Manifiesto_model->datos_empresa($guiaRow->id_empresa);

		$codigoEmpresa   = $guiaRow->id_empresa;
		//$id_estatus = $guiaRow->id_estatus;

		if ($codigoEmpresa == 108 || $codigoEmpresa == 109 || $codigoEmpresa == 100) {
			//bloque para facturar MA-FEL
			if ($id_estatus == 15) {
				$guiaFel = $this->Manifiesto_model->get_guiaFel($tracking);
				$json_data = json_encode($guiaFel);

				$curl = curl_init();

				curl_setopt_array($curl, array(
					CURLOPT_URL => 'https://stargates.site/app_starship/ws/index.php/api/guiaaws',
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_POST => true,  // Forzar solicitud POST
					CURLOPT_POSTFIELDS => $json_data,  // Enviar el JSON como datos POST
					CURLOPT_HTTPHEADER => array(
						'X-API-KEY: 4a4da5bb348e648e51d84e24c8652d1284c31156',
						'Content-Type: application/json',
						'Content-Length: ' . strlen($json_data)  // Longitud del contenido
					),
				));

				$response = curl_exec($curl);
				$error_no = curl_errno($curl);
				$error_msg = curl_error($curl);

				curl_close($curl);

				// Manejo de errores
				if ($error_no) {
					echo "cURL Error ($error_no): " . $error_msg . PHP_EOL;
				} else {
					echo $response;
				}
			}
		}
	}
}
/* End of file Controllername.php */