<?php
if (isset($encabezado)) {
    $fecha_entrega      = $encabezado->fecha_entrega;
    $cajas_entrega      = $encabezado->numero_cajas;
    $paquetes_entrega   = $encabezado->numero_paquetes;
    $referencias_entrega = $encabezado->total_referencias;
    $placa              = $encabezado->placa;
    $marchamo           = $encabezado->marchamo;
    $piloto             = $encabezado->piloto;
    $manifiesto         = $encabezado->manifiesto;
    $proveedor          = $encabezado->proveedor;
    $direccion          = $encabezado->direccion;
} else {
    $fecha_entrega      = "";
    $cajas_entrega      = "";
    $paquetes_entrega   = "";
    $referencias_entrega = "";
    $placa              = "";
    $marchamo           = "";
    $piloto             = "";
    $manifiesto         = "";
    $proveedor          = "";
    $direccion          = "";
}
if (isset($total_paquetes)) {
    $total_paquetes_master = $total_paquetes;
} else {
    $total_paquetes_master = "";
}
if (isset($total_cajas)) {
    $total_cajas_master = $total_cajas;
} else {
    $total_cajas_master = "";
}


?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Nueva POD generada</title>
</head>

<body style="background-color:#f5f5f5; padding:20px; font-family:Arial,Helvetica, sans-serif;">

    <div style="max-width:900px; margin:0 auto;">

        <div style="background:#ffffff; border:1px solid #ddd; border-radius:6px; box-shadow:0 2px 4px rgba(0,0,0,.08);">

            <div style="background:#007bff; color:#ffffff; padding:15px; border-radius:6px 6px 0 0;">
                <h3 style="margin:0; font-size:18px;">Nueva POD generada</h3>
            </div>

            <div style="padding:20px;">

                <p style="margin-bottom:20px;">
                    Se ha generado una nueva <strong>POD</strong>.
                    A continuación se detallan los datos correspondientes:
                </p>

                <div style="border:1px solid #dee2e6; border-radius:4px; padding:15px; margin-bottom:20px;">
                    <h4 style="color:#007bff; margin-top:0; font-size:15px;">Datos del Manifiesto</h4>

                    <table width="100%" cellpadding="6" cellspacing="0">
                        <?php if (isset($referencias)) : ?>
                            <tr>
                                <td width="25%"><strong>Master:</strong><br><?= $manifiesto ?></td>
                                <td width="25%"><strong>Referencias Master:</strong><br><?= $referencias ?></td>
                                <td width="25%"><strong>Paquetes Master:</strong><br><?= $total_paquetes_master ?></td>
                                <td width="25%"><strong>Cajas Master:</strong><br><?= $total_cajas_master ?></td>
                            </tr>
                        <?php else : ?>
                            <tr>
                                <td width="33"><strong>Master:</strong><br><?= $manifiesto ?></td>
                                <td width="33%"><strong>Paquetes Master:</strong><br><?= $total_paquetes_master ?></td>
                                <td width="33%"><strong>Cajas Master:</strong><br><?= $total_cajas_master ?></td>
                            </tr>
                        <?php endif; ?>
                    </table>

                    <hr style="border:none; border-top:1px solid #ddd; margin:15px 0;">

                    <table width="100%" cellpadding="6" cellspacing="0">
                        <?php if (isset($referencias)) : ?>
                            <tr>
                                <td width="33%"><strong>Referencias a entregar:</strong><br><?= $referencias_entrega ?></td>
                                <td width="33%"><strong>Paquetes a entregar:</strong><br><?= $paquetes_entrega ?></td>
                                <td width="33%"><strong>Cajas a entregar:</strong><br><?= $cajas_entrega ?></td>
                            </tr>
                        <?php else : ?>
                            <tr>
                                <td width="50%"><strong>Paquetes a entregar:</strong><br><?= $paquetes_entrega ?></td>
                                <td width="50%"><strong>Cajas a entregar:</strong><br><?= $cajas_entrega ?></td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </div>

                <div style="border:1px solid #dee2e6; border-radius:4px; padding:15px; margin-bottom:20px;">
                    <h4 style="color:#007bff; margin-top:0; font-size:15px;">Datos de Entrega</h4>

                    <table width="100%" cellpadding="6" cellspacing="0">
                        <tr>
                            <td width="50%"><strong>Fecha de entrega:</strong><br><?= $fecha_entrega ?></td>
                            <td width="50%"><strong>Piloto:</strong><br><?= $piloto ?></td>
                        </tr>
                    </table>

                    <table width="100%" cellpadding="6" cellspacing="0">
                        <tr>
                            <td width="33%"><strong>Placa:</strong><br><?= $placa ?></td>
                            <td width="33%"><strong>Marchamo:</strong><br><?= $marchamo ?></td>
                            <td width="33%"><strong>Proveedor LMD:</strong><br><?= $proveedor ?></td>
                        </tr>
                    </table>

                    <table width="100%" cellpadding="6" cellspacing="0">
                        <tr>
                            <td>
                                <strong>Dirección de entrega:</strong><br>
                                <?= $direccion ?>
                            </td>
                        </tr>
                    </table>
                </div>

                <div style="background:#fff3cd; border:1px solid #ffeeba; padding:15px; border-radius:4px;">
                    <strong>Recordatorio importante:</strong><br>
                    Recuerda subir el <strong>comprobante de entrega</strong> en el sistema
                    en un lapso máximo de <strong>24 horas</strong>.
                </div>

                <p style="color:#6c757d; font-size:12px; margin-top:20px;">
                    Este correo fue generado automáticamente por el sistema.
                </p>

            </div>
        </div>
    </div>

</body>

</html>