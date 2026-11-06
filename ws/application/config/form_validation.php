<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');


$config = array(

	'password_post' => array(
		array('field' => 'clave', 'label' => 'clave', 'rules' => 'trim|required|max_length[15]'),
		array('field' => 'casillero', 'label' => 'casillero', 'rules' => 'trim|required|max_length[15]'),
	),
	'mail_post' => array(
		array('field' => 'correo', 'label' => 'correo', 'rules' => 'trim|required|valid_email'),
		array('field' => 'casillero', 'label' => 'casillero', 'rules' => 'trim|required|max_length[15]'),

	),

	'sucursal_post' => array(
		array('field' => 'id_sucursal', 'label' => 'id_sucursal', 'rules' => 'trim|required|numeric'),
		array('field' => 'casillero', 'label' => 'casillero', 'rules' => 'trim|required|max_length[15]'),

	),

	'registro_put' => array(
		array('field' => 'nombre', 'label' => 'nombre', 'rules' => 'trim|required|max_length[120]'),
		array('field' => 'correo', 'label' => 'correo', 'rules' => 'trim|required|valid_email'),
		array('field' => 'telefono', 'label' => 'telefono', 'rules' => 'trim|required|max_length[15]'),

	),

	'addmanifiesto_post' => array(
	//	array('field' => 'tracking_number', 'label' => 'tracking_number', 'rules' => 'trim|required|max_length[25]'),
	//	array('field' => 'buyer_city', 'label' => 'buyer_city', 'rules' => 'trim|required'),
		array('field' => 'manifiesto', 'label' => 'manifiesto', 'rules' => 'trim|required'),
	),

);
