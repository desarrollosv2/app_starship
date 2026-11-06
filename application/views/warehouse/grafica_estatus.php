<!-- NO incluir loader.js aquí -->

<!-- (Opcional) Tus CSS/JS de DataTables si se usan en esta vista -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.bootstrap4.min.css">
<script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>

<?php
$agrupados = [];
$nombres_estatus = [];

foreach ($lista as $row) {
    $id_estatus = $row->id_estatus;
    $nombre = $row->nombre;

    if (!isset($agrupados[$id_estatus])) {
        $agrupados[$id_estatus] = 0;
        $nombres_estatus[$id_estatus] = $nombre;
    }
    $agrupados[$id_estatus]++;
}

$ids_entregados = [15, 311];
$entregados = 0;
$pendientes = 0;

foreach ($agrupados as $id_estatus => $cantidad) {
    if (in_array((int)$id_estatus, $ids_entregados)) {
        $entregados += $cantidad;
    } else {
        $pendientes += $cantidad;
    }
}
?>

<div class="container">
    <div class="row">
        <div class="col-md-6 mb-3">
            <div id="graficas_estatus" class="border rounded p-2" style="height: 400px;"></div>
        </div>
        <div class="col-md-6 mb-3">
            <div id="grafica_resumen_entrega" class="border rounded p-2" style="height: 400px;"></div>
        </div>
    </div>
</div>

<script type="text/javascript">
    // === SOLO funciones de dibujo; nada de google.charts.load aquí ===

    function drawChart() {
        const data = google.visualization.arrayToDataTable([
            ['Estado', 'Cantidad'],
            <?php foreach ($agrupados as $id_estatus => $cantidad): ?>
                <?php $nombre = $nombres_estatus[$id_estatus]; ?>['<?= addslashes($nombre) ?> (<?= (int)$cantidad ?>)', <?= (int)$cantidad ?>],
            <?php endforeach; ?>
        ]);

        const options = {
            title: 'Estatus de las Guías',
            pieHole: 0.2,
            pieSliceText: 'value-and-percentage',
            sliceVisibilityThreshold: 0,
            legend: {
                position: 'right',
                alignment: 'center'
            },
            tooltip: {
                trigger: 'focus',
                text: 'both'
            },
            colors: ['#3366cc', '#dc3912', '#ff9900', '#109618', '#990099', '#3b3eac', '#0099c6', '#dd4477', '#66aa00', '#b82e2e']
        };

        const el = document.getElementById('graficas_estatus');
        if (!el) return;

        const chart = new google.visualization.PieChart(el);
        chart.draw(data, options);

        google.visualization.events.addListener(chart, 'select', function() {
            const sel = chart.getSelection()[0];
            if (sel) {
                const label = data.getValue(sel.row, 0);
                const nombreEstatus = label.split(' (')[0];
                if (typeof obtener_Detalles_Por_Estatus === 'function') {
                    obtener_Detalles_Por_Estatus(nombreEstatus);
                }
            }
        });
    }

    function drawChartResumen() {
        const entregados = <?= (int)$entregados ?>;
        const pendientes = <?= (int)$pendientes ?>;

        const data = google.visualization.arrayToDataTable([
            ['Estado', 'Cantidad'],
            ['Llegó a su destino (' + entregados + ')', entregados],
            ['Pendientes (' + pendientes + ')', pendientes]
        ]);

        const options = {
            title: 'Resumen de Entregas',
            pieHole: 0.3,
            pieSliceText: 'value-and-percentage',
            colors: ['#109618', '#dc3912'],
            legend: {
                position: 'right',
                alignment: 'center'
            }
        };

        const el = document.getElementById('grafica_resumen_entrega');
        if (!el) return;

        const chart = new google.visualization.PieChart(el);
        chart.draw(data, options);

        google.visualization.events.addListener(chart, 'select', function() {
            const sel = chart.getSelection()[0];
            if (sel) {
                const label = data.getValue(sel.row, 0);
                const estatus = label.includes('Llegó a su destino') ? 'entregados' : 'pendientes';
                const id = <?= json_encode($lista[0]->id_manifiesto ?? 0) ?>;
                if (typeof descargarExcelResumen === 'function') {
                    descargarExcelResumen(id, estatus);
                }
            }
        });
    }
</script>