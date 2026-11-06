<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Manifiesto_model extends CI_Model
{
	public function guardar_manifiesto($data)
	{
		$this->db->insert('preclasificacion', $data);
		return $this->db->insert_id();
	}

	public function guardar_manifiesto_detalle($data)
	{
		$this->db->insert('manifiesto', $data);
		return $this->db->insert_id();
	}

	public function guardar_estatus($data)
	{
		$this->db->insert('detalle', $data);
		return $this->db->insert_id();
	}

	public function guardar_ios($data)
	{
		$this->db->insert('manifiesto', $data);
		return $this->db->insert_id();
	}


	public function tarifas()
	{
		$query = $this->db->select('*')
			->where('id_pais', 2)
			->get('tarifas')
			->result();
		return $query;
	}

	//log Mail Americas
	public function guardar_logma($data)
	{

		$this->db->insert('log_ma', $data);
		return $this->db->insert_id();
	}

	public function guardar_json($datos)
	{
		$this->db->insert('j_son', $datos);
		return $this->db->insert_id();
	}

	/* Bloque para valores, vienen desde c807xpress/servicios*/
	//log Mail Americas
	public function guardar_ticket($data)
	{

		$this->db->insert('gestiontickets.detalles', $data);
		return $this->db->insert_id();
	}

	public function consulta_tikets()
	{
		//obtiene los tikets de la empresa 3=c807xpress
		$rsl = $this->db
			->select('de.*,de.fecha,em.nombre nombre_empresa,
              pr.nombre nombre_proceso,
              si.nombre nombre_sistema,
              es.nombre nombre_estado,
              co.nombre nombre_colaborador,
              ca.nombre nombre_categoria,
              pi.nombre nombre_prioridad')
			->join('gestiontickets.empresas em', 'em.id = de.empresa_id')
			->join('gestiontickets.procesos pr', 'pr.id = de.proceso_id')
			->join('gestiontickets.sistemas si', 'si.id = de.sistema_id')
			->join('gestiontickets.estados es', 'es.id = de.estado_id')
			->join('gestiontickets.colaboradores co', 'co.id = de.colaborador_id')
			->join('gestiontickets.categorias ca', 'ca.id = de.categoria_id')
			->join('gestiontickets.prioridades pi', 'pi.id = de.prioridad_id')
			->where('de.empresa_id', 3)
			->where('es.id <> ', 7)
			->get('gestiontickets.detalles de')
			->result();
		return $rsl;
	}

	public function guia_alternativa($guia)
	{
		$query = $this->db->select('*')
			->where('tracking_replace', $guia)
			->get('reemplazo')
			->result();
		return $query;
	}

	public function get_tracking($guia)
	{
		$query = $this->db->select('tracking_number, internal_tracking, metodo, id_empresa')
			->where('tracking_number', $guia)
			->get('manifiesto')
			->result();
		return $query;
	}
	public function obtener_tracking($guia)
	{
		$query = $this->db->select('tracking_number, id_pais, id_manifiesto')
			->where('tracking_number', $guia)
			->get('manifiesto')
			->row();
		return $query;
	}

	public function obtener_id_pais($guia)
	{
		$query = $this->db->select('id_pais')
			->where('idpreclasificacion', $guia)
			->get('preclasificacion')
			->row();

		return $query ? $query->id_pais : null;
	}

	public function datos_empresa($id)
	{
		$query = $this->db->select('id, token_cliente, id_tokencliente')
			->where('id', $id)
			->get('empresa')
			->result();
		return $query;
	}

	public function update_estatus($wh, $estatus)
	{
		$this->db
			->set('id_estatus', $estatus)
			->where('tracking_number', $wh)
			->update('manifiesto');
		return ($this->db->affected_rows() > 0);
	}


	//Recibe la información de cliente
	public function  prealerta($data)
	{

		$this->db->insert('prealerta', $data);
		return ($this->db->affected_rows() > 0);
	}

	public function verificar_dui($dui)
	{
		$query = $this->db
			->select("*")
			->where('DUI', $dui)
			->get('clientes_dui')
			->result();
		return $query;
	}

	public function identidad_personal($data)
	{
		$this->db->insert('identidad_personal', $data);
		return ($this->db->affected_rows() > 0);
	}

	public function update_prealerta($dui, $departamento, $municipio, $sucursal, $tracking_number)
	{
		$this->db
			->set('dui', $dui)
			->set('departamento', $departamento)
			->set('municipio', $municipio)
			->set('id_sucursal', $sucursal)
			->where('tracking_number', $tracking_number)
			->update('prealerta');
		return ($this->db->affected_rows() > 0);
	}

	public function buscar_sucursal($idSucursal)
	{
		$query = $this->db->select('*')
			->where('id', $idSucursal)
			->get('agencias')
			->row();
		return $query;
	}


	public function get_guiaFel($guia)
	{
		$query = $this->db->select('*')
			->where('tracking_number', $guia)
			->get('manifiesto')
			->row();
		return $query;
	}

	public function buscarPorDescripcion($descripcion)
	{
		// Usar el Query Builder de CodeIgniter para hacer la consulta
		$this->db->where('descripcion', $descripcion);
		$query = $this->db->get('estatusapg');

		// Si hay resultados, devolverlos como un array
		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return null;
		}
	}


	public function buscarPorDescripcionImile($descripcion)
	{
		// Usar el Query Builder de CodeIgniter para hacer la consulta
		$this->db->where('descripcion', $descripcion);
		$query = $this->db->get('estatusimile');

		// Si hay resultados, devolverlos como un array
		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return null;
		}
	}


	public function buscarPorCodigo($codigo)
	{
		// Usar el Query Builder de CodeIgniter para hacer la consulta
		$this->db->where('codigo_interno', $codigo);
		$query = $this->db->get('estatusapg');

		// Si hay resultados, devolverlos como un array
		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return null;
		}
	}

	public function buscarPorCodigoImile($codigo)
	{
		// Usar el Query Builder de CodeIgniter para hacer la consulta
		$this->db->where('codigo_interno', $codigo);
		$query = $this->db->get('estatusimile');

		// Si hay resultados, devolverlos como un array
		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return null;
		}
	}
	//para validar si ya existe una estatus 15 aplicado a una guia
	public function verificar_pod($tracking_recibido, $codigo_recibido)
	{
		$query = $this->db->select('warehouse, id_estatus')
			->where('warehouse', $tracking_recibido)
			->where('id_estatus', 15)
			->get('detalle');

		if ($query->num_rows() > 0) {
			return true; // Ya existe un registro con ese estatus
		} else {
			return false; // No existe
		}
	}

	public function guardar_alerta($data)
	{
		$this->db->insert('alertas', $data);
		return $this->db->insert_id();
	}

	//para validar si ya existe una estatus 15 aplicado a una guia
	public function verificar_retornos($tracking_recibido, $codigo_recibido)
	{
		$query = $this->db->select('warehouse, id_estatus')
			->where('warehouse', $tracking_recibido)
			->where('id_estatus', 311)
			->get('detalle');

		if ($query->num_rows() > 0) {
			return true; // Ya existe un registro con ese estatus
		} else {
			return false; // No existe
		}
	}
	//fin

	//para validar si ya existe una estatus 15 aplicado a una guia
	public function verificar_Ro($tracking_recibido, $codigo_recibido)
	{
		$query = $this->db->select('warehouse, id_estatus')
			->where('warehouse', $tracking_recibido)
			->where('id_estatus', 311)
			->get('detalle');

		if ($query->num_rows() > 0) {
			return true; // Ya existe un registro con ese estatus
		} else {
			return false; // No existe
		}
	}


	public function update_empresaDetalle($empresa, $master)
	{
		$this->db
			->set('id_empresa', $empresa)
			->where('id_manifiesto', $master)
			->update('manifiesto');
	}

	public function update_empresaEnca($empresa, $master, $plataforma)
	{
		$this->db
			->set('client_id', $empresa)
			->set('userid', $empresa)
			->set('descripcion', $plataforma)
			->where('idpreclasificacion', $master)
			->update('preclasificacion');
	}

	public function buscarEstatus($campo, $valor, $id_empresa)
	{
		// Validar que el campo sea uno permitido para evitar inyección SQL
		$campos_permitidos = ['descripcion', 'codigo_interno'];
		if (!in_array($campo, $campos_permitidos)) {
			return null;
		}

		// Usar Query Builder con las condiciones
		$this->db->where($campo, $valor);
		$this->db->where('id_empresa', $id_empresa);
		$query = $this->db->get('eventos');

		// Devolver resultados
		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return null;
		}
	}
}


/* End of file ModelName.php */
