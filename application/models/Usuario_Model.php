<?php



class Usuario_model extends CI_Model

{

    function __construct() {

        parent::__construct();

    }

    

    function getLogin($datos) {

         

        $query=$this->db

        ->select("us.id, us.nombre, us.correo, us.clave, em.id as id_empresa, em.nombre as nombre_empresa, em.token, em.token_cliente, em.id_tokencliente, us.ver_doc_privado, us.casillero, us.interno, us.id_pais, us.client_code, us.id_estatus")

        ->from("usuarios as us")

        ->join("empresa as em","em.id = us.id_empresa","inner")

            //  ->join("pais_ec as pa","pa.id = em.id_pais","inner")

            //->where('id_estatus !=',0)

            //->orwhere('id_estatus IS NULL')

            //->where('id_estatus !=', 0)
            //->orWhereNull('id_estatus')

        ->where('us.correo' , $datos['correo'])

        ->where('us.clave'  , $datos['clave'])

        ->get();

        if ($query->num_rows()>0){

            return $query->row();

        }else{

            return false;

        }
      
    }


    public function get_Parent_Menu($datos)

        {

            $query = $this->db

                        ->select('me.id, me.title')

                        ->join('permisos as pe' , 'pe.id_usuario = us.id' , 'inner')

                        ->join('dyn_menu as me' , "me.id = pe.id_menu and me.is_parent = 1" , 'inner')

                        ->where('us.id', $datos)

                        ->order_by('me.position asc')

                        ->get('usuarios as us');

                        

            return $query->result();

            

           /*  var_dump($this->db->last_query()); die();

            die();



            var_dump($query->num_rows());

            die();

 */

           /*  if ($query->num_rows() > 1) {

            return $query->result();

            }else { return $query->row();} */

        }



    public function get_Hijos($datos, $id_parent)

        {

            $query = $this->db

                        ->select('pe.id, me.url , me.title')

                        ->join('permisos as pe' , 'pe.id_usuario = us.id' , 'inner')

                        ->join('dyn_menu as me' , "me.id = pe.id_menu and me.is_parent = 0 and me.parent_id = $id_parent" , 'inner')

                        ->where('us.id', $datos)

                        ->order_by('me.position', 'asc')

                        ->get('usuarios as us');

                        



            return $query->result();           

        }

    

    

}