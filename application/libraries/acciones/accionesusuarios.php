<?php  
class accionesusr{
     // funcion para obtener las acciones de los usuarios 
     public function  obtacciones_usuarios($id_menu){
         $CI =  & get_instance();
         $CI ->load->model('Usuarios_Model');
         $accionesurs =$CI->Usuarios_Model->obtaccion_usuario($id_menu);
        // var_dump($accionesurs); 
         $datosuser  = array('c' =>$accionesurs->c,
                              'r' =>$accionesurs->r,
                              'u' =>$accionesurs->u,
                              'd' =>$accionesurs->d,);

         //var_dump($datosuser);
          return $datosuser;


     }
}