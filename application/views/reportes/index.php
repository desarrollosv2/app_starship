<?php $this->load->view('templates/filtros', array('clientes' => $clientes, 'tipos_producto' => $tipos_producto, 'accion' => 'cargar_reporte')); ?>

<div id="reporte" data-reporte="<?php echo html_escape($reporte); ?>">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div class="kpis" id="kpis"></div>
        <a href="javascript:void(0)" class="btn btn-outline-success" onclick="exportar_reporte()"><i class="fa-solid fa-file-excel"></i> Exportar a Excel (CSV)</a>
    </div>

    <div class="tabla-cards">
        <table class="table align-middle w-100">
            <thead id="rep-head"></thead>
            <tbody id="rep-body">
                <tr><td class="text-muted">Cargando...</td></tr>
            </tbody>
            <tfoot id="rep-foot"></tfoot>
        </table>
    </div>
</div>
