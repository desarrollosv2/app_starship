<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Reportes de pallets y cajas. Cada reporte se define una vez (columnas + filas + totales)
 * y se usa tanto para la pantalla (JSON) como para la exportacion CSV.
 */
class ReportesController extends CI_Controller
{

    private $reportes = array(
        'cajas-tipo'      => 'Cajas por tipo de producto',
        'cliente-tipo'    => 'Cajas y pallets por cliente y tipo de producto',
        'pallets-tipo'    => 'Pallets por tipo de producto',
        'pallets-cliente' => 'Pallets por cliente',
    );

    public function __construct()
    {

        ini_set("display_errors",1);
        parent::__construct();
        $this->load->model('CatalogoModel');
        $this->load->model('ReporteModel');
        $this->load->helper('pallet');
    }

    public function index()
    {
        redirect('ReportesController/ver/cajas-tipo');
    }

    public function ver($reporte = '')
    {
        if (!isset($this->reportes[$reporte])) {
            show_404();
        }

        $data['titulo']         = $this->reportes[$reporte];
        $data['menu']           = 'ReportesController/' . $reporte;
        $data['scripts']        = array('reportes.js');
        $data['reporte']        = $reporte;
        $data['clientes']       = $this->CatalogoModel->listar_clientes();
        $data['tipos_producto'] = $this->CatalogoModel->listar_tipos_producto();

        $this->load->view('templates/header', $data);
        $this->load->view('reportes/index', $data);
        $this->load->view('templates/footer', $data);
    }

    public function datos($reporte = '')
    {
        if (!isset($this->reportes[$reporte])) {
            return responder_json(array('success' => FALSE, 'message' => 'Reporte no encontrado.'));
        }
        return responder_json(array_merge(array('success' => TRUE), $this->construir($reporte, filtros_busqueda())));
    }

    /**
     * Exporta el reporte a CSV (separador ';' y BOM para que Excel en espanol lo abra bien).
     */
    public function csv($reporte = '')
    {
        if (!isset($this->reportes[$reporte])) {
            show_404();
        }
        $r = $this->construir($reporte, filtros_busqueda());

        $fh = fopen('php://temp', 'r+');
        fputcsv($fh, array_column($r['columnas'], 'titulo'), ';');
        foreach (array_merge($r['filas'], array($r['total'])) as $fila) {
            $linea = array();
            foreach ($r['columnas'] as $c) {
                $v = isset($fila[$c['clave']]) ? $fila[$c['clave']] : '';
                $linea[] = ($c['tipo'] === 'pct' || $c['tipo'] === 'dec') ? number_format((float) $v, 1, ',', '') : $v;
            }
            fputcsv($fh, $linea, ';');
        }
        rewind($fh);
        $csv = "\xEF\xBB\xBF" . stream_get_contents($fh);
        fclose($fh);

        $nombre = 'reporte_' . str_replace('-', '_', $reporte) . '_' . date('Ymd_His') . '.csv';
        $this->output->set_content_type('text/csv', 'utf-8')
            ->set_header('Content-Disposition: attachment; filename="' . $nombre . '"')
            ->set_output($csv);
    }

    /**
     * @return array columnas: [{clave, titulo, tipo: texto|num|pct|dec}], filas: [assoc] (con 'fila' = subtotal
     *               para las filas de subtotal), total: assoc
     */
    private function construir($reporte, $f)
    {
        $conteo = array(
            array('clave' => 'pallets', 'titulo' => 'Pallets', 'tipo' => 'num'),
            array('clave' => 'pallets_abiertos', 'titulo' => 'Abiertos', 'tipo' => 'num'),
            array('clave' => 'pallets_cerrados', 'titulo' => 'Cerrados', 'tipo' => 'num'),
            array('clave' => 'cajas', 'titulo' => 'Cajas', 'tipo' => 'num'),
        );
        $claves = array('pallets', 'pallets_abiertos', 'pallets_cerrados', 'cajas');

        switch ($reporte) {
            case 'cajas-tipo':
                $datos = $this->ReporteModel->resumen('tipo', $f);
                $total_cajas = array_sum(array_column($datos, 'cajas'));
                $total_pallets = array_sum(array_column($datos, 'pallets'));

                $filas = array();
                foreach ($datos as $d) {
                    $filas[] = array(
                        'nombre'     => $d->nombre,
                        'cajas'      => $d->cajas,
                        'porcentaje' => $total_cajas ? round($d->cajas * 100 / $total_cajas, 1) : 0,
                        'pallets'    => $d->pallets,
                        'promedio'   => $d->pallets ? round($d->cajas / $d->pallets, 1) : 0,
                    );
                }
                return array(
                    'columnas' => array(
                        array('clave' => 'nombre', 'titulo' => 'Tipo de producto', 'tipo' => 'texto'),
                        array('clave' => 'cajas', 'titulo' => 'Cajas', 'tipo' => 'num'),
                        array('clave' => 'porcentaje', 'titulo' => '% del total', 'tipo' => 'pct'),
                        array('clave' => 'pallets', 'titulo' => 'Pallets', 'tipo' => 'num'),
                        array('clave' => 'promedio', 'titulo' => 'Cajas por pallet', 'tipo' => 'dec'),
                    ),
                    'filas' => $filas,
                    'total' => array(
                        'nombre'     => 'Total',
                        'cajas'      => $total_cajas,
                        'porcentaje' => $total_cajas ? 100 : 0,
                        'pallets'    => $total_pallets,
                        'promedio'   => $total_pallets ? round($total_cajas / $total_pallets, 1) : 0,
                    ),
                );

            case 'cliente-tipo':
                $filas = array();
                $sub = NULL;
                $total = array_fill_keys($claves, 0);

                foreach ($this->ReporteModel->cliente_tipo($f) as $d) {
                    // Subtotal al cambiar de cliente
                    if ($sub && $sub['cliente_id'] !== $d->cliente_id) {
                        $filas[] = $sub['fila'];
                        $sub = NULL;
                    }
                    if (!$sub) {
                        $sub = array('cliente_id' => $d->cliente_id, 'fila' => array_merge(
                            array('cliente' => 'Subtotal ' . $d->cliente, 'tipo' => '', 'fila' => 'subtotal'),
                            array_fill_keys($claves, 0)
                        ));
                    }

                    $fila = array('cliente' => $d->cliente, 'tipo' => $d->tipo);
                    foreach ($claves as $k) {
                        $fila[$k] = $d->$k;
                        $sub['fila'][$k] += $d->$k;
                        $total[$k] += $d->$k;
                    }
                    $filas[] = $fila;
                }
                if ($sub) {
                    $filas[] = $sub['fila'];
                }

                return array(
                    'columnas' => array_merge(array(
                        array('clave' => 'cliente', 'titulo' => 'Cliente', 'tipo' => 'texto'),
                        array('clave' => 'tipo', 'titulo' => 'Tipo de producto', 'tipo' => 'texto'),
                    ), $conteo),
                    'filas' => $filas,
                    'total' => array_merge(array('cliente' => 'Total', 'tipo' => ''), $total),
                );

            default: // pallets-tipo | pallets-cliente
                $agrupar = $reporte === 'pallets-cliente' ? 'cliente' : 'tipo';
                $filas = array();
                $total = array_fill_keys($claves, 0);
                foreach ($this->ReporteModel->resumen($agrupar, $f) as $d) {
                    $fila = array('nombre' => $d->nombre);
                    foreach ($claves as $k) {
                        $fila[$k] = $d->$k;
                        $total[$k] += $d->$k;
                    }
                    $filas[] = $fila;
                }
                return array(
                    'columnas' => array_merge(array(
                        array('clave' => 'nombre', 'titulo' => $agrupar === 'cliente' ? 'Cliente' : 'Tipo de producto', 'tipo' => 'texto'),
                    ), $conteo),
                    'filas' => $filas,
                    'total' => array_merge(array('nombre' => 'Total'), $total),
                );
        }
    }
}
