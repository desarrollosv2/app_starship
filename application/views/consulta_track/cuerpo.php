<?php
define("UNWANTED_CHARS", ["\n", "\r", "#", "-", "'", ".", "\\", "//", "!", "\(", "\)"]);
$this->load->view("verifica");
if (isset($tracking_numb)) {
    $c = 1;
    foreach ($tracking_numb as $row) {
        $suma_manejo += $row->manejo + $row->total_ivag + $row->comision;
?>
        <tr class="rw ">
            <td scope="row" style="display:none"><?php echo $row->id; ?></td>
            <td style="margin-left: 9px ; "><?php echo $c; ?></td>


            <td scope="row"><?php echo $row->awb; ?></td>
            <td scope="row"><?php echo $row->referencia; ?></td>
            <td scope="row"><?php echo $row->tracking_number; ?></td>
            <td scope="row"><?php echo $row->tracking_replace; ?></td>
            <td scope="row"><?php echo $row->gweight . ' lb';  ?></td>
            <td scope="row"><?php echo $row->value; ?></td>
            <td scope="row"><?php echo $row->items; ?></td>
            <td scope="row" style="width:100px"><?php echo $row->commodity; ?></td>
            <td scope="row"><?php echo $row->id_partida; ?></td>
            <td scope="row"><?php echo $row->consignee_account; ?></td>
            <td scope="row"><?php echo utf8_decode($row->consignee); ?></td>
            <td scope="row"><?php echo utf8_decode($row->consignee_address1); ?></td>
            <td scope="row"><?php echo utf8_decode($row->consignee_city); ?></td>
            <td scope="row"><?php echo utf8_decode($row->consignee_state); ?></td>
            <td scope="row"><?php echo $row->consignee_phone; ?></td>
            <td scope="row"><?php echo $row->consignee_email; ?></td>
            <td scope="row"><?php echo $row->hts; ?></td>
            <td scope="row"><?php echo $row->pieces; ?></td>
            <td scope="row" class="font-weight-bold text-danger"><?php echo $row->numero_partida; ?></td>
            <td scope="row" class="font-weight-bold text-danger"><?php echo $row->descripcion_producto; ?></td>
            <td scope="row" class="font-weight-bold text-primary"><?php echo $row->departamento; ?></td>
            <td scope="row" class="font-weight-bold text-primary"><?php echo $row->municipio; ?></td>
            <td scope="row"><?php echo $row->tipo_entrega; ?></td>
            <td scope="row"><?php echo $row->tipo_servicio; ?></td>
            <td scope="row"><?php echo  number_format($row->iva, 2); ?></td>
            <td scope="row"><?php echo  number_format($row->dai, 2); ?></td>
            <td scope="row"><?php echo  number_format($suma_manejo, 2); ?></td>
            <td scope="row"><?php echo  number_format($row->almacenaje, 2); ?></td>
            <td scope="row"><?php echo  number_format($row->cobro_final, 2); ?></td>
            <td scope="row"><?php echo $row->pickup_number; ?></td>
            <td scope="row"><?php echo $row->internal_tracking; ?></td>
            <?php
            if ($row->id_estatus == "15") {
                echo '<td scope="row" class="font-weight-bold text-success">SI</td>';
            } else {
                echo '<td scope="row" class="font-weight-bold text-danger">NO</td>';
            }
            ?>

            <?php
            if ($row->facturado == "1") {
                echo '<td scope="row" class="font-weight-bold text-success">FACTURADO</td>';
            } else {
                echo '<td scope="row" class="font-weight-bold text-danger">PENDIENTE</td>';
            }
            ?>

            <?php
            if ($row->sobrante == 1) {
                echo '<td scope="row" class="font-weight-bold text-success">SI</td>';
            } else {
                echo '<td scope="row" class="font-weight-bold text-danger">NO</td>';
            }
            ?>

            <td scope="row"><?php echo $row->bag_number; ?></td>
            <?php
            $cleandAddress = cleanString($row->consignee_address1);
            // $cleandName = removeSpecialCharacters($row->consignee);
            ?>
            <td>

                <a href="#" class="btn btn-secundary btn-md mt-0" Onclick="ver_agencias_consulta(<?php echo "'$row->id'"; ?>, <?php echo "'$row->consignee_account'"; ?>,  <?php echo "'$row->consignee_email'"; ?>,  <?php echo "'$row->consignee_phone'"; ?>, <?php echo str_replace('"', '', "'$cleandAddress'"); ?>)" title="Asignar agencia" style="margin:0px;">
                    <i class="fas fa-home  text-secondary"></i>
                </a>
            </td>

            <td>
                <a href="#" class="btn btn-secundary btn-md mt-0" Onclick="lista_servicios_express2(<?php echo "'$row->id'"; ?>)" title="Departamento / Municipio" style="margin:0px;">
                    <i class="fa fa-file-text text-secondary"></i></a>
            </td>

            <?php
            $string = preg_replace("[\n|\r|\n\r]", "", trim($row->consignee_address1));
            $string = str_replace('"', '', $string);
            $string = str_replace('"', '', $string);
            $string = str_replace(",", '', $string);
            $string = str_replace("#", '', $string);
            $string = str_replace("-", '', $string);
            $string = str_replace("'", '', $string);
            $string = str_replace(".", '', $string);
            $string = str_replace("'", '', $string);
            $string = str_replace('\\', '', $string);
            $string = str_replace('//', '', $string);

            $cleandAddress = cleanString($row->consignee_address1);
            $cleandName = removeSpecialCharacters($row->consignee);

            ?>

            <td>
                <a href="#" class="btn btn-secundary btn-md mt-0 " id="createGuide" Onclick="crear_guia_xpress(<?php echo "'$row->id'"; ?>,
                                    <?php echo "'$row->tracking_number'"; ?>,
                                    <?php echo trim("'$cleandName'"); ?>,
                                    <?php echo "'$cleandAddress'";         ?>,
                                    <?php echo $row->departamento_id; ?>,
                                    <?php echo $row->municipio_id; ?>,
                                    <?php echo $row->cobro_final; ?>,
                                    <?php echo "'$row->comentario'"; ?>)" title="Crear guia xpress " style="margin:0px;">
                    <i class="fa fa-cog text-primary"></i></a>
            </td>

            <td>
                <a href="#" class="btn btn-secundary btn-md mt-0" Onclick="pdf_ticket(<?php echo "'$row->id'"; ?>,<?php echo "'$row->tracking_number'"; ?>,<?php echo "'$row->internal_tracking'"; ?>)" title="Generar QR" style="margin:0px;">
                    <i class="fa fa-qrcode text-warning" aria-hidden="true"></i>
                </a>
            </td>
            <td>
                <a href="#" class="btn btn-secundary btn-md mt-0" Onclick="pdf_guia_xpress(<?php echo "'$row->id'"; ?>,<?php echo "'$row->tracking_number'"; ?>,<?php echo "'$row->internal_tracking'"; ?>)" title="Guia Xpress" style="margin:0px;">
                    <i class="fa fa-qrcode text-success" aria-hidden="true"></i>
                </a>
            </td>

            <td>
                <a href="#" class="btn btn-default btn-md" Onclick="reemplazar(<?php echo "'$row->tracking_number'"; ?>)" title="Reemplazar" style="margin:0px;">
                    <i class="fa fa-retweet" aria-hidden="true"></i></a>
            </td>

            <td>
                <div class="dropdown">
                    <button class="btn btn-primary  btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">

                        <i class="fa fa-list" aria-hidden="true"></i>Opciones
                    </button>
                    <div class="dropdown-options">
                        <a href="#" class="btn btn-default btn-md" Onclick="historial(<?php echo "'$row->tracking_number'"; ?>)" title="Historial" style="margin:0px;">
                            <i class="fa fa-history" aria-hidden="true"></i> Historial </a>
                        <a href="#" class="btn btn-default btn-md" Onclick="reemplazar23(<?php echo "'$row->tracking_number'"; ?>)" title="Asignar Referencia" style="margin:0px;">
                            <i class="fa fa-refresh" aria-hidden="true"></i> Asignar Referencia</a>
                    </div>
                </div>

            </td>
        </tr>
<?php
        $c = $c + 1;
    }
}
?>
<?php
// Función para limpiar el string para direccion
function cleanString($input)
{
    // Reemplazar "/" con un espacio en blanco
    $cleaned = str_replace('/', ' ', trim($input));
    // Reemplazar caracteres no deseados
    $cleaned = str_replace(UNWANTED_CHARS, '', $cleaned);
    // Eliminar repeticiones de comas
    $cleaned = preg_replace('/,+/', ',', $cleaned);
    // Reemplazar múltiples espacios por uno solo
    $cleaned = preg_replace('/\s+/', ' ', $cleaned);
    return $cleaned;
}

//para campo nombre consignatario
function removeSpecialCharacters($input)
{
    return preg_replace('/[^a-zA-Z0-9\\s\\.]/', '', $input);
}
?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>