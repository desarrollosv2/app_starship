<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Progreso de Documentos - Manifiesto <?php echo $id_manifiesto; ?></title>
    <style>
        body {
            font-family: sans-serif;
            background-color: #f4f7f6;
            padding: 20px;
        }

        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #2c3e50;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* Badges de Estado */
        .badge {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.85em;
            font-weight: bold;
        }

        .badge-success {
            background-color: #d4edda;
            color: #155724;
        }

        .badge-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .ref-header {
            background-color: #e9ecef !important;
            font-weight: bold;
            color: #495057;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2>Estado de Carga: Manifiesto #<?php echo $id_manifiesto; ?></h2>

        <table id="tbResumenCarga">
            <thead>
                <tr>
                    <th>Referencia</th>
                    <th>Tipo de Documento</th>
                    <th>Cant. Cargada</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $current_ref = "";
                foreach ($documentos as $row):
                    // Detectar cambio de referencia para separar visualmente
                    $is_new_ref = ($current_ref != $row->referencia);
                    if ($is_new_ref) $current_ref = $row->referencia;
                ?>
                    <tr>
                        <td class="<?php echo $is_new_ref ? '' : ''; ?>">
                            <?= $row->referencia ?>
                        </td>
                        <td><?php echo $row->tipo_nombre; ?></td>
                        <td style="text-align: center;"><?php echo $row->cantidad; ?></td>
                        <td>
                            <?php if ($row->cantidad > 0): ?>
                                <span class="badge badge-success">✓ CARGADO</span>
                            <?php else: ?>
                                <span class="badge badge-danger">✗ PENDIENTE</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script type="text/javascript">
        $("#tbResumenCarga").DataTable({
            "buttons": [
                'excel'
            ]
        });
    </script>

</body>

</html>