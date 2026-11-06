<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Arancel_Model  extends CI_Model
{

  // funcion para ver todas las partidas creadas 
  public function partidas_creadas()
  {

    
    $query = $this->db
      ->select('ar.*')
      ->get('partida_arancel ar')
      ->result();
    return $query;
  }

  //Funcion para guardar los datos de la partida
  public function insertar_partidas($data, $id)
  {

    if ($id) {
      $this->db
        ->set('numero_partida',       $data['numero_partida'])
        ->set('descripcion',          $data['descripcion'])
        ->set('id_origen',            $data['id_origen'])
        ->set('marca',                $data['marca'])
        ->set('modelo',               $data['modelo'])
        ->set('id_estado',            $data['id_estado'])
        ->set('descripcion_generica', $data['descripcion_generica'])
        ->set('porcentaje',           $data['porcentaje'])

        ->where('id', $id)
        ->update('partida_arancel');

      return ($this->db->affected_rows() > 0);
    } else {
      return $this->db->insert("partida_arancel", $data);
    }
  }


  //Funcion para editar Partidas
  public function editar_partidas($ID)
  {
    $query = $this->db
      ->select('*')
      ->where("id", $ID)
      ->get('partida_arancel')
      ->row();
    return $query;
  }


  //Funcion para guardar las partidas editadas
  public function guardar_partidasbk($id, $data)
  {
    if ($id == null) {
      return $this->db->insert("partida_arancel", $data);
    } else {

      $this->db
        ->set('numero_partida',     $data['numeroPartida'])
        ->set('descripcion',        $data['descripcion'])
        ->set('id_origen',             $data['id_origen'])
        ->set('marca',             $data['marca'])
        ->set('modelo',             $data['modelo'])
        ->set('estado',             $data['estado'])
        ->set('descripcion_generica',             $data['descripcion_generica'])
        ->set('porcentaje',             $data['porcentaje'])
        ->set('anulado',            $data['anulado'])
        ->where('id', $id)
        // ->where('pais.id' , $data['id_origen'])
        ->update('partida_arancel');

      return ($this->db->affected_rows() > 0);
    }
  }

  // funcion para eliminar las partidas
  public function eliminar_partida($ID)
  {
    $this->db->where("id", $ID);
    $this->db->delete("partida_arancel");
  }

  public function listado_permisos($id)
  {

    return $this->db->select('p.descripcion, d.id, d.idpermiso,d.partida')
      ->join('detalle_permiso d', 'd.idpermiso = p.idpermiso')
      ->where('d.partida', $id)
      ->get('permiso p')
      ->result();
  }


  public function agregar_permiso($data)
  {
    $this->db->insert('detalle_permiso', $data);
    return ($this->db->affected_rows() > 0);
  }

  public function eliminar_permiso($id)
  {
    $this->db->where('id', $id);
    $this->db->delete('detalle_permiso');
  }

  function verificar_permiso($permiso, $id)
  {
    return $this->db
      ->where('idpermiso', $permiso)
      ->where('partida', $id)
      ->get('detalle_permiso')
      ->row();
  }

  public function obtener_productos_sin_codigo($id)
  {
    return $this->db->select('id, id_manifiesto,commodity')
      ->where('id_manifiesto', $id)
      ->get('manifiesto')
      ->result();
  }

  public function actualizar_codigo_arancelario($id, $codigo_arancelario) {
    $this->db->where('id', $id);
    $this->db->update('manifiesto', ['arancel' => $codigo_arancelario]);
}

}
