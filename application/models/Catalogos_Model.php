<?php



class Catalogos_Model extends CI_Model

{

    function __construct() {

        parent::__construct();

    }


    function companyByCountry($idCountry)
    {
        return $this->db
            ->select('id, nombre')
            ->where('id_pais', $idCountry)
            ->get('empresa')
            ->result();
 
    }


    public function mantenimiento_usuario($datos)

    {



        if (isset($datos['id']) && $datos['id'] > 0)

        {   

            $this->db->where('id', $datos['id']);

            $this->db->update('usuarios' ,$datos);

        }else{

            $this->db->insert('usuarios', $datos);

        }



        return false;

    }





    public function buscar_usuarios($id = null)

    {



        if (isset($id)){

            $this->db->where('us.id', $id);

        }



        return $this->db 

               ->select('us.id, us.nombre, us.correo, em.nombre as empresa, ver_doc_privado as privado')

               ->join("empresa as em","em.id = us.id_empresa","inner")

               ->get('usuarios as us' )

               ->result();

    }



    public function buscar_usuarios_email($id = null, $id_usuario = null)

    {



        return $this->db 

               ->select('id, nombre, correo')

               ->where('correo', $id)

               ->where('id <>' , $id_usuario)

               ->get('usuarios')

               ->result();

    }



    function grabar_empresa($datos) 

    {         

        $this->db->insert('empresa',$datos);

        if ($this->db->affected_rows() === 1 )

        {

            return $this->db->insert_id();

        }

        return false;      

    }



    function buscar_empresa($id = null) 

    {         

        if (isset($id) && $id > 0  ){

            $this->db->where('id', $id);

        }


        return $this->db 

               ->select('id, nombre')

               ->get('empresa')

               ->result();

    }    

    

    function eliminar_empresa($id){

  

        $this->db->where('id', $id);

        if ($this->db->delete('empresa')) {

            return TRUE;

        } else {

            return FALSE;

        }



    }



    public function mantenimiento_permiso($datos, $accion)

    {

        if (isset($accion) && $accion == "grabar")

        {

            $this->db->insert('permisos', $datos);

            

        }

        if (isset($accion) && $accion == "eliminar")

        {

            $this->db->where('id',$datos);

            $this->db->delete('permisos');      

        }

        return false;

    }

    

    public function buscar_permisos_asignado($datos)

    {

        $query = $this->db

                    ->select('pe.id as id , mn.title as titulo')

                    ->join('dyn_menu as mn','mn.id = pe.id_menu','inner')

                    ->where('pe.id_usuario' , $datos)

                    ->get('permisos as pe')

                    ->result();



        //echo $this->db->last_query();exit(); 



        return $query;

    }



    public function buscar_permisos_no_asignado($datos)

    {

        $query =  $this->db

                    ->select('mn.id as id , mn.title as titulo')

                    ->join('permisos as pe', "mn.id = pe.id_menu and pe.id_usuario = $datos ",'left')

                    ->where('pe.id is null')

                    ->order_by('mn.id', 'asc')

                    ->get('dyn_menu as mn')

                    ->result();



        //echo $this->db->last_query();exit();  



        return $query;

     }

    



    function buscar_proceso($id = null) 

    {         



        if (isset($id)){

            $this->db->where('id', $id);

        }



        return $this->db 

               ->select('id, proceso')

               ->get('proceso')

               ->result();

    } 



    public function mantenimiento_proceso($datos)

    {



        if (isset($datos['id']) && $datos['id'] > 0)

        {   

            $this->db->where('id', $datos['id']);

            $this->db->update('proceso' ,$datos);

        }else{

            $this->db->insert('proceso', $datos);

        }



        return false;

    }

    



    public function mantenimiento_empleados($datos)

    {



        if (isset($datos['id']) && $datos['id'] > 0)

        {   

            $this->db->where('id', $datos['id']);

            $this->db->update('empleados' ,$datos);

        }else{

            $this->db->insert('empleados', $datos);

        }



        return false;

    }



    public function buscar_empleados($datos = null)

    {



        if (isset($datos['id']) && $datos['id'] > 0  ){

            $this->db->where('em.id', $datos['id']);

        }



        if (isset($datos['celular'])  ){

            

            $this->db->where('em.numero_celular', $datos['celular']);

        }

 
        
        $query = $this->db 
              
               ->select('em.id, em.nombre, em.numero_celular, pr.proceso, emp.id as id_empresa, emp.nombre as nombre_empresa, em.id_proceso')

               ->join('proceso as pr', 'pr.id = em.id_proceso', 'inner')

               ->join('empresa as emp', 'emp.id = em.id_empresa', 'inner')

               ->order_by('em.numero_celular')

               ->get('empleados as em')

               ->result();



        return $query;

    }



    function consulta_celular($datos = null)

    {

 

        $query = $this->db 

               ->select('id, nombre, numero_celular')

               ->where('numero_celular', $datos['celular'])

               ->where('id <>', $datos['id'])

               ->get('empleados')

               ->result();



        return $query;

    }



    function buscar_proveedor($id = null) 

    {         



        if (isset($id)){

            $this->db->where('id', $id);

        }



        return $this->db 

               ->select('id, nombre')

               ->get('proveedor')

               ->result();

    } 



    public function mantenimiento_proveedor($datos)

    {



        if (isset($datos['id']) && $datos['id'] > 0)

        {   

            $this->db->where('id', $datos['id']);

            $this->db->update('proveedor' ,$datos);

        }else{

            $this->db->insert('proveedor', $datos);

        }



        return false;

    }

    

    public function mantenimiento_copiadoras($datos)

    {



        if (isset($datos['id']) && $datos['id'] > 0)

        {   

            $this->db->where('id', $datos['id']);

            $this->db->update('copiadoras' ,$datos);

        }else{

            $this->db->insert('copiadoras', $datos);

        }



        return false;

    }



    public function buscar_copiadoras($datos = null)

    {



        if (isset($datos['id']) && $datos['id'] > 0  ){

            $this->db->where('co.id', $datos['id']);

        }



 

        $query = $this->db 

               ->select('co.id, co.ubicacion, co.plan_copia, co.costo_plan, co.costo_exceso , pr.nombre, emp.nombre as nombre_empresa')

               ->join('proveedor as pr', 'pr.id = co.id_proveedor', 'inner')

               ->join('empresa as emp', 'emp.id = co.id_empresa', 'inner')

               ->get('copiadoras as co')

               ->result();



        return $query;

    }





    public function mantenimiento_cliente($datos)

    {



        if (isset($datos['id']) && $datos['id'] > 0)

        {   

            $this->db->where('id', $datos['id']);

            $this->db->update('clientes' ,$datos);

        }else{

            $this->db->insert('clientes', $datos);

        }



        return false;

    }



    public function buscar_cliente($datos)

    {


        if (isset($datos['id']) && $datos['id'] > 0  ){

         //   $this->db->where('cl.id', $datos['id']);

        }
/*
        if (isset($datos['codigo_cliente']) && $datos['codigo_cliente'] > 0  ){

            $this->db->where('cl.codigo_cliente', $datos['codigo_cliente']);

        } 



        if (isset($datos['nombre_cliente']) && strlen($datos['nombre_cliente']) > 0   ){

            $this->db->like('cl.nombre_cliente', $datos['nombre_cliente'], 'both');

        }



        if (isset($datos['correo']) && strlen($datos['correo']) > 0  ){

            $this->db->like('cl.correo', $datos['correo'], 'both');

        }



        if (isset($datos['id_empresa']) && $datos['id_empresa'] > 0  ){

            $this->db->where('cl.id_empresa', $datos['id_empresa']);

        }

*/

 

        $query = $this->db 

               ->select('cl.id, cl.codigo_cliente, cl.nombre_cliente, cl.correo, emp.nombre as nombre_empresa,  cl.id_empresa')

               ->join('empresa as emp', 'emp.id = cl.id_empresa', 'inner')

               ->where('cl.id_empresa', $_SESSION["id_empresa"])

               ->get('clientes as cl')

               ->result();



        //var_dump($query);exit();

        //echo $this->db->last_query();exit();  



        return $query;

    }





    public function mantenimiento_empleados_tfijos($datos)

    {



        if (isset($datos['id']) && $datos['id'] > 0)

        {   

            $this->db->where('id', $datos['id']);

            $this->db->update('tfijos' ,$datos);

        }else{

            $this->db->insert('tfijos', $datos);

        }



        return false;

    }



    public function buscar_tfijos($datos = null)

    {



        if (isset($datos['id']) && $datos['id'] > 0  ){

            $this->db->where('em.id', $datos['id']);

        }



        if (isset($datos['numero_tel'])  ){

            

            $this->db->where('em.numero_tel', $datos['numero_tel']);

        }

 

        $query = $this->db 

               ->select('em.id, em.nombre, em.numero_tel, pr.proceso, emp.nombre as nombre_empresa')

               ->join('proceso as pr', 'pr.id = em.id_proceso', 'inner')

               ->join('empresa as emp', 'emp.id = em.id_empresa', 'inner')

               ->order_by('emp.nombre asc , pr.proceso asc')

               ->get('tfijos as em')

               ->result();



        return $query;

    }





    public function buscar_empleados_tfijos($datos = null)

    {



        if (isset($datos['id']) && $datos['id'] > 0  ){

            $this->db->where('em.id', $datos['id']);

        }



        if (isset($datos['numero_tel'])  ){

            

            $this->db->where('em.numero_tel', $datos['numero_tel']);

        }

 

        $query = $this->db 

               ->select('em.id, em.nombre, em.numero_tel, em.corto, pr.proceso,emp.id as id_empresa, emp.nombre as nombre_empresa,em.id_proceso')

               ->join('proceso as pr', 'pr.id = em.id_proceso', 'inner')

               ->join('empresa as emp', 'emp.id = em.id_empresa', 'inner')

               ->order_by('em.numero_tel')

               ->get('tfijos as em')

               ->result();



        return $query;

    }

    function get_empresas_dropdown()
    {
        $this->db->from($this->empresa);
        $this->db->order_by('id');
        $result = $this->db->get();
        $return = array();
        if($result->num_rows() > 0){
                $return[''] = 'please select';
            foreach($result->result_array() as $row){
                $return[$row['id']] = $row['nombre'];
            }
        }
        return $return;
    }

    public function mantenimiento_tipo_documento($datos)

    {



        if (isset($datos['id']) && $datos['id'] > 0)

        {   

            $this->db->where('id', $datos['id']);

            $this->db->update('tipo_doc' ,$datos);

        }else{

            $this->db->insert('tipo_doc', $datos);

        }



        return false;

    }





    public function buscar_tipo_documento($id = null, $id_empresa = null)

    {



        if (isset($id) && $id <> null ){

            $this->db->where('td.id', $id);

        }



        return $this->db 

               ->select("td.id, td.nombre,  em.nombre as empresa, td.privado   ,

               case when td.privado = 1 then 'privado' when td.privado = 0 then 'publico' end as tipodoc ")

               ->join("empresa as em","em.id = td.id_empresa","inner")

               ->where('td.id_empresa', $id_empresa)

               ->get('tipo_doc as td' )

               ->result();

    }



    public function mantenimiento_tipo_doc_cliente($datos)

    {



        if (isset($datos['id']) && $datos['id'] > 0)

        {   

            $this->db->where('id', $datos['id']);

            $this->db->update('tipo_doc_cliente' ,$datos);

        }else{

            $this->db->insert('tipo_doc_cliente', $datos);

        }



        return false;

    }





    public function buscar_tipo_doc_cliente($id = null, $id_empresa = null)

    {



        if (isset($id) && $id <> null ){

            $this->db->where('td.id', $id);

        }



        return $this->db 

               ->select("td.id, td.nombre,  em.nombre as empresa, td.privado   ,

               case when td.privado = 1 then 'privado' when td.privado = 0 then 'publico' end as tipodoc ")

               ->join("empresa as em","em.id = td.id_empresa","inner")

               ->where('td.id_empresa', $id_empresa)

               ->get('tipo_doc_cliente as td' )

               ->result();

    }

}