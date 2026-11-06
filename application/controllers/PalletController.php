<?php
defined('BASEPATH') or exit('No direct script access allowed');

class PalletController extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('CatalogoModel');
        $this->load->model('PalletModel');
        $this->load->model('PalletCajaModel');
        $this->load->model('PalletFotoModel');
        $this->load->helper('pallet');
    }

    /**
     * Pantalla de armado: paso 1 (cliente + tipo) y paso 2 (escaneo). Acepta ?pallet=ID para retomar.
     */
    public function index()
    {
        $data['titulo']         = 'Armar pallet';
        $data['menu']           = 'PalletController/index';
        $data['scripts']        = array('pallet.js');
        $data['clientes']       = $this->CatalogoModel->listar_clientes();
        $data['tipos_producto'] = $this->CatalogoModel->listar_tipos_producto();
        $data['abiertos']       = $this->PalletModel->listar(array('estado' => 'abierto'), 50);
        $data['retomar_id']     = (int) $this->input->get('pallet');

        $this->load->view('templates/header', $data);
        $this->load->view('pallet/index', $data);
        $this->load->view('templates/footer', $data);
    }

    public function crear()
    {

        ini_set("display_errors", 1);
        $cliente_id = (int) $this->input->post('cliente_id');
        $tipo_id    = (int) $this->input->post('tipo_producto_id');

        if (!$this->CatalogoModel->cliente_valido($cliente_id) || !$this->CatalogoModel->tipo_producto_valido($tipo_id)) {
            return responder_json(array('success' => FALSE, 'message' => 'Seleccione un cliente y un tipo de producto válidos.'));
        }

        $res = $this->PalletModel->crear($cliente_id, $tipo_id);
        if (!$res) {
            return responder_json(array('success' => FALSE, 'message' => 'No se pudo crear el pallet.'));
        }

        if (!$res['existente']) {
            $this->registrar_log($res['pallet']->id . '-crear', array(
                'pallet'           => codigo_pallet($res['pallet']),
                'cliente_id'       => $cliente_id,
                'tipo_producto_id' => $tipo_id,
            ));
        }

        return responder_json(array(
            'success'   => TRUE,
            'existente' => $res['existente'],
            'pallet'    => $this->payload($res['pallet']),
        ));
    }

    /**
     * Datos del pallet y sus cajas (para retomar un pallet abierto).
     */
    public function ver()
    {
        $pallet = $this->PalletModel->get_with_relations((int) $this->input->post('pallet_id'));
        if (!$pallet) {
            return responder_json(array('success' => FALSE, 'message' => 'Pallet no encontrado.'));
        }
        if ($pallet->estado === 'eliminado') {
            return responder_json(array('success' => FALSE, 'message' => 'El pallet N° ' . codigo_pallet($pallet) . ' fue eliminado.'));
        }
        if ($pallet->estado !== 'abierto') {
            return responder_json(array('success' => FALSE, 'message' => 'El pallet N° ' . codigo_pallet($pallet) . ' está cerrado.'));
        }

        return responder_json(array('success' => TRUE, 'pallet' => $this->payload($pallet)));
    }

    /**
     * Retoma un pallet escaneando el codigo de barras de su etiqueta PDF (prefijo del tipo + numero, ej. CN-000013).
     * Tambien acepta etiquetas impresas antes del correlativo por tipo (solo el numero) si no hay ambiguedad.
     * Solo se permite si el pallet esta abierto.
     */
    public function retomar_por_etiqueta()
    {
        $codigo = strtoupper(trim((string) $this->input->post('codigo_pallet')));

        if (preg_match('/^([A-Z]{1,5})-?(\d{1,10})$/', $codigo, $m)) {
            $pallet = $this->PalletModel->get_por_codigo($m[1], (int) $m[2]);
            if (!$pallet) {
                return responder_json(array('success' => FALSE, 'message' => 'No existe el pallet N° ' . $m[1] . '-' . numero_pallet_fmt($m[2]) . '.'));
            }
        } elseif (preg_match('/^\d{1,10}$/', $codigo)) {
            $pallet = $this->pallet_por_etiqueta_antigua((int) $codigo);
            if (is_string($pallet)) {
                return responder_json(array('success' => FALSE, 'message' => $pallet));
            }
        } else {
            return responder_json(array('success' => FALSE, 'message' => 'El código ' . $codigo . ' no corresponde a una etiqueta de pallet.'));
        }
        if ($pallet->estado === 'eliminado') {
            return responder_json(array('success' => FALSE, 'message' => 'El pallet N° ' . codigo_pallet($pallet) . ' fue eliminado y no se puede retomar.'));
        }
        if ($pallet->estado !== 'abierto') {
            return responder_json(array('success' => FALSE, 'message' => 'El pallet N° ' . codigo_pallet($pallet) . ' está cerrado y no se puede retomar.'));
        }

        return responder_json(array('success' => TRUE, 'pallet' => $this->payload($pallet)));
    }

    public function escanear()
    {
        $pallet_id = (int) $this->input->post('pallet_id');
        $codigo    = trim((string) $this->input->post('codigo_barras'));

        if ($pallet_id <= 0 || $codigo === '') {
            return responder_json(array('success' => FALSE, 'message' => 'Datos incompletos.'));
        }
        if (strlen($codigo) > 128 || !preg_match('/^[A-Za-z0-9\-_.\/]+$/', $codigo)) {
            return responder_json(array('success' => FALSE, 'message' => 'Código de barras inválido: ' . $codigo));
        }

        // Transaccion con bloqueo del pallet para que dos lecturas simultaneas no superen el limite
        $this->db->trans_begin();

        $pallet = $this->PalletModel->bloquear($pallet_id);
        if (!$pallet || $pallet->estado !== 'abierto') {
            $this->db->trans_rollback();
            return responder_json(array('success' => FALSE, 'message' => 'Pallet no encontrado o cerrado.'));
        }

        // El codigo de caja es unico en todo el sistema
        $existe = $this->PalletCajaModel->buscar_por_codigo($codigo);
        if ($existe) {
            $this->db->trans_rollback();
            $mensaje = ((int) $existe->pallet_id === $pallet_id)
                ? 'La caja ' . $codigo . ' ya fue escaneada en este pallet.'
                : 'La caja ' . $codigo . ' ya pertenece al pallet N° ' . codigo_pallet($existe) . ' (' . $existe->estado . ').';
            return responder_json(array('success' => FALSE, 'message' => $mensaje));
        }

        $total = $this->PalletModel->contar_cajas($pallet_id);
        if ($total >= (int) $pallet->max_cajas && !(int) $pallet->excede_limite) {
            $this->db->trans_rollback();
            return responder_json(array(
                'success'                 => FALSE,
                'requiere_consentimiento' => TRUE,
                'message'                 => 'El pallet ya tiene ' . $total . ' cajas.',
                'codigo_barras'           => $codigo,
            ));
        }

        $resultado = $this->PalletCajaModel->agregar($pallet_id, $codigo);
        if ($resultado !== 'ok') {
            $this->db->trans_rollback();
            return responder_json(array(
                'success' => FALSE,
                'message' => $resultado === 'duplicado' ? 'La caja ' . $codigo . ' ya fue registrada.' : 'No se pudo registrar la caja.',
            ));
        }
        $this->db->trans_commit();

        $payload = $this->payload($this->PalletModel->get_with_relations($pallet_id));

        return responder_json(array(
            'success'          => TRUE,
            'codigo_barras'    => $codigo,
            'limite_alcanzado' => $payload['total_cajas'] >= $payload['max_cajas'] && !$payload['excede_limite'],
            'pallet'           => $payload,
        ));
    }

    /**
     * El usuario acepta seguir agregando cajas por sobre el limite.
     */
    public function consentir()
    {
        $pallet = $this->PalletModel->get_with_relations((int) $this->input->post('pallet_id'));
        if (!$pallet || $pallet->estado !== 'abierto') {
            return responder_json(array('success' => FALSE, 'message' => 'Pallet no encontrado o cerrado.'));
        }

        $total = $this->PalletModel->contar_cajas($pallet->id);
        if ($total < (int) $pallet->max_cajas) {
            return responder_json(array('success' => FALSE, 'message' => 'El pallet aún no llega al límite de ' . $pallet->max_cajas . ' cajas.'));
        }

        if (!(int) $pallet->excede_limite) {
            $this->PalletModel->registrar_consentimiento($pallet->id, $this->input->ip_address());
            $this->registrar_log($pallet->id . '-consentimiento', array(
                'pallet'        => codigo_pallet($pallet),
                'total_cajas'   => $total,
                'max_cajas'     => (int) $pallet->max_cajas,
                'ip'            => $this->input->ip_address(),
            ));
        }

        return responder_json(array('success' => TRUE, 'pallet' => $this->payload($this->PalletModel->get_with_relations($pallet->id))));
    }

    public function quitar_caja()
    {
        $pallet_id = (int) $this->input->post('pallet_id');
        $caja_id   = (int) $this->input->post('caja_id');

        $pallet = $this->PalletModel->get_with_relations($pallet_id);
        if (!$pallet || $pallet->estado !== 'abierto') {
            return responder_json(array('success' => FALSE, 'message' => 'Solo se pueden quitar cajas de un pallet abierto.'));
        }

        $caja = $this->PalletCajaModel->obtener($pallet_id, $caja_id);
        if (!$caja || !$this->PalletCajaModel->quitar($pallet_id, $caja_id)) {
            return responder_json(array('success' => FALSE, 'message' => 'La caja no existe en este pallet.'));
        }

        $this->registrar_log($pallet_id . '-quitar-caja', array(
            'pallet'        => codigo_pallet($pallet),
            'codigo_barras' => $caja->codigo_barras,
        ));

        return responder_json(array('success' => TRUE, 'pallet' => $this->payload($pallet)));
    }

    public function cerrar()
    {
        $pallet_id = (int) $this->input->post('pallet_id');

        $pallet = $this->PalletModel->get_with_relations($pallet_id);
        if (!$pallet || $pallet->estado !== 'abierto') {
            return responder_json(array('success' => FALSE, 'message' => 'Pallet no encontrado o ya cerrado.'));
        }

        $total = $this->PalletModel->contar_cajas($pallet_id);
        if ($total === 0) {
            return responder_json(array('success' => FALSE, 'message' => 'No se puede cerrar un pallet sin cajas.'));
        }

        // Las 2 fotografias son obligatorias para cerrar
        if ($this->PalletFotoModel->contar($pallet_id) < 2) {
            return responder_json(array(
                'success'        => FALSE,
                'requiere_fotos' => TRUE,
                'message'        => 'Debe cargar las 2 fotografías antes de cerrar el pallet.',
                'pallet'         => $this->payload($pallet),
            ));
        }

        $this->PalletModel->cerrar($pallet_id);
        $this->registrar_log($pallet_id . '-cerrar', array(
            'pallet'        => codigo_pallet($pallet),
            'total_cajas'   => $total,
        ));

        return responder_json(array('success' => TRUE, 'pallet' => $this->payload($this->PalletModel->get_with_relations($pallet_id))));
    }

    /**
     * Etiqueta PDF del pallet, generada al vuelo. ?formato=ETIQUETA para 100x150 mm.
     */
    public function pdf($id = 0)
    {
        $pallet = $this->PalletModel->get_with_relations((int) $id);
        if (!$pallet) {
            show_404();
        }

        $formato = $this->input->get('formato') === 'ETIQUETA' ? 'ETIQUETA' : $this->config->item('pdf_formato');

        $this->load->library('Pallet_pdf');
        $bytes = $this->pallet_pdf->generar_etiqueta($pallet, $formato);

        $this->output->set_content_type('application/pdf')
            ->set_header('Content-Disposition: inline; filename="pallet_' . codigo_pallet($pallet) . '.pdf"')
            ->set_output($bytes);
    }

    /**
     * Listado de pallets con filtros, desde donde se reimprime el PDF o se retoma un pallet.
     */
    public function listado()
    {
        $data['titulo']         = 'Listado de pallets';
        $data['menu']           = 'PalletController/listado';
        $data['scripts']        = array('listado.js');
        $data['clientes']       = $this->CatalogoModel->listar_clientes();
        $data['tipos_producto'] = $this->CatalogoModel->listar_tipos_producto();

        $this->load->view('templates/header', $data);
        $this->load->view('pallet/listado', $data);
        $this->load->view('templates/footer', $data);
    }

    public function listado_datos()
    {
        $filas = $this->PalletModel->listar(filtros_busqueda());
        foreach ($filas as $r) {
            $r->numero_fmt  = codigo_pallet($r);
            $r->total_cajas = (int) $r->total_cajas;
            $r->total_fotos = (int) $r->total_fotos;
            $r->editable_hoy = (bool) (int) $r->editable_hoy;
            $r->pdf_url     = site_url('PalletController/pdf/' . $r->id);
        }
        return responder_json(array('success' => TRUE, 'pallets' => $filas));
    }

    /**
     * Sube (o reemplaza) la fotografia 1 o 2 de un pallet abierto. Archivo en el campo "foto".
     */
    public function subir_foto()
    {
        $pallet_id = (int) $this->input->post('pallet_id');
        $posicion  = (int) $this->input->post('posicion');

        if (!in_array($posicion, array(1, 2), TRUE)) {
            return responder_json(array('success' => FALSE, 'message' => 'Posición de fotografía inválida.'));
        }

        $pallet = $this->PalletModel->get_with_relations($pallet_id);
        if (!$pallet || $pallet->estado !== 'abierto') {
            return responder_json(array('success' => FALSE, 'message' => 'Solo se pueden cargar fotografías a un pallet abierto.'));
        }

        $carpeta = $this->carpeta_fotos($pallet_id);
        if (!is_dir($carpeta) && !mkdir($carpeta, 0755, TRUE)) {
            return responder_json(array('success' => FALSE, 'message' => 'No se pudo crear la carpeta de fotografías.'));
        }

        $this->load->library('upload', array(
            'upload_path'   => $carpeta,
            'allowed_types' => 'jpg|jpeg|png',
            'max_size'      => 15360,
            'file_name'     => 'foto' . $posicion . '_' . time(),
            'file_ext_tolower' => TRUE,
        ));

        if (!$this->upload->do_upload('foto')) {
            return responder_json(array('success' => FALSE, 'message' => 'La fotografía debe ser una imagen JPG o PNG de hasta 15 MB.'));
        }

        $archivo  = $this->upload->data('file_name');
        $anterior = $this->PalletFotoModel->guardar($pallet_id, $posicion, $archivo);
        if ($anterior) {
            $this->borrar_archivo_foto($pallet_id, $anterior);
        }

        $this->registrar_log($pallet_id . '-foto' . $posicion, array(
            'pallet'        => codigo_pallet($pallet),
            'archivo'       => $archivo,
            'reemplaza'     => $anterior,
        ));

        return responder_json(array('success' => TRUE, 'pallet' => $this->payload($pallet)));
    }

    /**
     * Muestra la fotografia 1 o 2 del pallet (las fotos no son accesibles directamente desde uploads/).
     */
    public function foto($pallet_id = 0, $posicion = 0)
    {
        $foto = $this->PalletFotoModel->obtener((int) $pallet_id, (int) $posicion);
        $ruta = $foto ? $this->carpeta_fotos($pallet_id) . basename($foto->archivo) : '';

        if (!$foto || !is_file($ruta)) {
            show_404();
        }

        $this->load->helper('file');
        $this->output->set_content_type(get_mime_by_extension($ruta) ?: 'image/jpeg')
            ->set_header('Cache-Control: private, max-age=86400')
            ->set_output(file_get_contents($ruta));
    }

    /**
     * Deja el pallet vacio (sin cajas ni fotos) y abierto, con el mismo numero. Solo el mismo dia de su creacion.
     */
    public function reiniciar()
    {
        $pallet = $this->pallet_editable_hoy((int) $this->input->post('pallet_id'), 'reiniciar');
        if (is_string($pallet)) {
            return responder_json(array('success' => FALSE, 'message' => $pallet));
        }

        // Un pallet cerrado vuelve a quedar abierto: se mantiene la regla de un solo abierto por cliente + tipo
        if ($pallet->estado === 'cerrado') {
            $otro = $this->PalletModel->buscar_abierto($pallet->cliente_id, $pallet->tipo_producto_id);
            if ($otro && (int) $otro->id !== (int) $pallet->id) {
                return responder_json(array('success' => FALSE, 'message' => 'Ya existe el pallet abierto N° '
                    . codigo_pallet($otro) . ' para ' . $pallet->cliente_nombre . ' / ' . $pallet->tipo_legible
                    . '. Ciérrelo o elimínelo antes de reiniciar este pallet.'));
            }
        }

        $codigos = $this->PalletModel->codigos_cajas($pallet->id);
        if (!$this->PalletModel->reiniciar($pallet->id)) {
            return responder_json(array('success' => FALSE, 'message' => 'No se pudo reiniciar el pallet.'));
        }

        $fotos = $this->PalletFotoModel->eliminar_por_pallet($pallet->id);
        foreach ($fotos as $archivo) {
            $this->borrar_archivo_foto($pallet->id, $archivo);
        }

        $this->registrar_log($pallet->id . '-reiniciar', array(
            'pallet'          => codigo_pallet($pallet),
            'estado_anterior' => $pallet->estado,
            'cajas_quitadas'  => $codigos,
            'fotos_borradas'  => $fotos,
        ));

        return responder_json(array('success' => TRUE, 'pallet' => $this->payload($this->PalletModel->get_with_relations($pallet->id))));
    }

    /**
     * Borrado logico del pallet (queda como 'eliminado'). Solo el mismo dia de su creacion.
     */
    public function eliminar()
    {
        $pallet = $this->pallet_editable_hoy((int) $this->input->post('pallet_id'), 'eliminar');
        if (is_string($pallet)) {
            return responder_json(array('success' => FALSE, 'message' => $pallet));
        }

        $codigos = $this->PalletModel->codigos_cajas($pallet->id);
        if (!$this->PalletModel->eliminar($pallet->id)) {
            return responder_json(array('success' => FALSE, 'message' => 'No se pudo eliminar el pallet.'));
        }

        $this->registrar_log($pallet->id . '-eliminar', array(
            'pallet'          => codigo_pallet($pallet),
            'estado_anterior' => $pallet->estado,
            'cajas_liberadas' => $codigos,
        ));

        return responder_json(array('success' => TRUE, 'numero_fmt' => codigo_pallet($pallet)));
    }

    /**
     * Etiqueta impresa antes del correlativo por tipo: el codigo de barras solo trae el numero.
     * Se acepta solo si ese numero existe en un unico pallet; si ya se repite entre tipos no se adivina
     * (podria ser un pallet nuevo con el mismo numero) y se pide reimprimir la etiqueta.
     */
    private function pallet_por_etiqueta_antigua($numero)
    {
        $pallets = $this->PalletModel->listar_por_numero($numero);
        if (!$pallets) {
            return 'No existe el pallet N° ' . numero_pallet_fmt($numero) . '.';
        }
        if (count($pallets) === 1) {
            return $pallets[0];
        }

        return 'La etiqueta N° ' . numero_pallet_fmt($numero) . ' es anterior al correlativo por tipo y corresponde a varios pallets ('
            . implode(', ', array_map('codigo_pallet', $pallets)) . '). Reimprima la etiqueta del pallet.';
    }

    /**
     * Pallet que se puede reiniciar/eliminar (existe, no esta eliminado y fue creado hoy), o el mensaje de error.
     */
    private function pallet_editable_hoy($pallet_id, $accion)
    {
        $pallet = $this->PalletModel->get_with_relations($pallet_id);
        if (!$pallet || $pallet->estado === 'eliminado') {
            return 'Pallet no encontrado.';
        }
        if (!$this->PalletModel->es_del_dia($pallet->id)) {
            return 'Solo se puede ' . $accion . ' un pallet el mismo día en que se creó (N° '
                . codigo_pallet($pallet) . ' se creó el ' . date('d/m/Y', strtotime($pallet->created_at)) . ').';
        }
        return $pallet;
    }

    private function carpeta_fotos($pallet_id)
    {
        return FCPATH . 'uploads/pallets/fotos/' . (int) $pallet_id . '/';
    }

    private function borrar_archivo_foto($pallet_id, $archivo)
    {
        $ruta = $this->carpeta_fotos($pallet_id) . basename($archivo);
        if (is_file($ruta)) {
            @unlink($ruta);
        }
    }

    /**
     * URLs de las 2 fotografias (NULL si falta). El ?v= evita que el navegador muestre una foto reemplazada.
     */
    private function urls_fotos($pallet_id)
    {
        $fotos = $this->PalletFotoModel->listar_por_pallet($pallet_id);
        $urls = array();
        foreach (array(1, 2) as $posicion) {
            $urls[$posicion] = isset($fotos[$posicion])
                ? site_url('PalletController/foto/' . (int) $pallet_id . '/' . $posicion) . '?v=' . strtotime($fotos[$posicion]->created_at)
                : NULL;
        }
        return $urls;
    }

    private function payload($pallet)
    {
        return array(
            'id'            => (int) $pallet->id,
            'numero_pallet' => (int) $pallet->numero_pallet,
            'numero_fmt'    => codigo_pallet($pallet),
            'cliente'       => $pallet->cliente_nombre,
            'tipo_producto' => $pallet->tipo_legible,
            'estado'        => $pallet->estado,
            'max_cajas'     => (int) $pallet->max_cajas,
            'excede_limite' => (bool) (int) $pallet->excede_limite,
            'total_cajas'   => $this->PalletModel->contar_cajas($pallet->id),
            'cajas'         => $this->PalletCajaModel->listar_por_pallet($pallet->id),
            'pdf_url'       => site_url('PalletController/pdf/' . $pallet->id),
            'fotos'         => $this->urls_fotos($pallet->id),
            'editable_hoy'  => $this->PalletModel->es_del_dia($pallet->id),
        );
    }

    private function registrar_log($solicitud, $respuesta)
    {
        $data = array(
            'id_solicitante' => isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL,
            'fecha'          => date('Y-m-d H:i:s'),
            'solicitud'      => $solicitud,
            'respuesta'      => json_encode($respuesta),
            'pagina'         => 'pallets',
        );

        $this->PalletModel->insert_log($data);
    }
}
