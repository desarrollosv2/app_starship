<?php
    $total = count($datos);
    $scan_global = 0;
    $entregado = 0;
    $reingreso = 0;
    $devolucion = 0;
    $sin_movimientos = 0;

    foreach($datos as $row)
    {
        $res_movimiento = false;

        if($row->scan_global == '1') {
            $scan_global++;
            $res_movimiento = true;
        }

        if($row->entregado == '1') {
            $entregado++;
            $res_movimiento = true;
        }

        if($row->reingreso == '1') {
            $reingreso++;
            $res_movimiento = true;
        }

        if($row->devolucion == '1') {
            $devolucion++;
            $res_movimiento = true;
        }

        if(!$res_movimiento) {
            $sin_movimientos++;
        }
    }

    function porcentaje($valor, $total)
    {
        return $total > 0 ? round(($valor / $total) * 100, 1) : 0;
    }

    $p_scan_global = porcentaje($scan_global, $total);
    $p_entregado = porcentaje($entregado, $total);
    $p_reingreso = porcentaje($reingreso, $total);
    $p_devolucion = porcentaje($devolucion, $total);
    $p_sin_movimientos = porcentaje($sin_movimientos, $total);
?>

<div id="grafica_imile_general" style="width: 100%; height: 400px;"></div>

<script type="text/javascript">
    google.charts.load("current", {packages:["corechart"]});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart()
    {
        const data = google.visualization.arrayToDataTable([
            ['Estado', 'Cantidad'],
            ['Escaneados Global (<?= $scan_global ?>)', <?= $scan_global ?>],
            ['Entregados (<?= $entregado ?>)', <?= $entregado ?>],
            ['Reingresos (<?= $reingreso ?>)', <?= $reingreso ?>],
            ['Devolución (<?= $devolucion ?>)', <?= $devolucion ?>],
            ['Sin Movimiento (<?= $sin_movimientos ?>)', <?= $sin_movimientos ?>]
        ]);

        const options = {
            title: 'Estado General de las Guías',
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
            colors: ['#4285F4', '#0F9D58', '#F4B400', '#DB4437', '#959595']
        };

        const chart = new google.visualization.PieChart(document.getElementById('grafica_imile_general'));
        chart.draw(data, options);
    }
</script>

