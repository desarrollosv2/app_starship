<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Tracking_DTE_Model extends CI_Model
{
    public function buscar_tracking($id)
    {
        $DBMA   = $this->load->database('contabilidad', TRUE);
        $query  = $DBMA->select("enca.id,
                                 exp.guia_no,
                                 enca.venta_total,
                                 enca.pdf_path")
                         ->join('contabilidad.guia_exp_gc exp', 'exp.id_venta = enca.id', 'inner')
                        // ->join('usps.manifiesto man', 'man.tracking_number = exp.guia_no', 'inner')
                         ->where('enca.id_empresa = 47')
                         ->where('exp.guia_no', $id)
                         ->GROUP_BY('exp.guia_no')
                         ->get('contabilidad.encabezados_comprob enca')
                         ->result();
        return $query;
    }
}