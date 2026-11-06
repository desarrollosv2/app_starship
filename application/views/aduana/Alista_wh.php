<?php
$id                        =    '';
$id_manifiesto             =    '';
$master                    =    '';
$tracking_number           =    '';
$fecha_creacion            =    '';
$consignee                 =    '';
$consignee_address1        =    '';
$consignee_address2        =    '';
$gweight                   =    '';
$commodity                 =    '';
$cobro_final               =    '';
$shipper_address1          =    '';
$shipper_address2          =    '';
$pieces                    =    '';
$cuenta                    =    '';
$origin                    =    '';
$destination               =    '';
$shipper_name              =    '';
$fecha                     =    '';
$iden_of                   =    '';
$pais_origen               =    '';
$pais_destino              =    '';
$bultos                    =    '';
$contenido                 =    '';
$flete                     =    '';
$consignee_account         =    '';
$natcod                    =    '';
$bol_type_code             =    '';



//var_dump($track);

if (isset($track)) {
    $id                        =    $track->id;
    $id_manifiesto             =    $track->id_manifiesto;
    $master                    =    $track->master;
    $tracking_number           =    $track->tracking_number;
    $fecha_creacion            =    $track->fecha_creacion;
    $consignee                 =    $track->consignee;
    $consignee_address1        =    $track->consignee_address1;
    $consignee_address2        =    $track->consignee_address2;
    $gweight                   =    $track->gweight;
    $commodity                 =    $track->commodity;
    $cobro_final               =    $track->cobro_final;
    $shipper_address1          =    $track->shipper_address1;
    $shipper_address2          =    $track->shipper_address2;
    $pieces                    =    $track->pieces;
    $cuenta                    =    $track->cuenta;
    $origin                    =    $track->origin;
    $destination               =    $track->destination;
    $shipper_name              =    $track->shipper_name;
    $consignee_account         =    $track->consignee_account;

    $fecha                     =    $track->fecha;
    $iden_of                   =    $track->iden_of;
    $pais_origen               =    $track->pais_origen;
    $pais_destino              =    $track->pais_destino;
    $bultos                    =    $track->bultos;
    $contenido                 =    $track->contenido;
    $flete                     =    $track->flete;

    $natcod                    =    $track->natcod;
    $bol_type_code             =    $track->bol_type_code;
}
?>

<body>
<?php $this->load->view("verifica");?>
    <div class="container-fluid">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <!-- jquery validation -->
                <div class="card card-danger mt-2">
                    <div class="card-header  bg-primary">

                        <h3 id="title"></h3>
                    </div>

                    <form enctype="multipart/form-data" class="add_wh" id="add_wh">
                        <div class="card-body">
                            <div>
                                <input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
                                <input type="text" name="id_manifiesto" id="id_manifiesto" value="<?php echo $id_manifiesto; ?>">
                                <input type="text" name="master" id="master" value="<?php echo $master; ?>">
                            </div>
                            <div class="input-group input-group-sm mb-1">
                                <label class="col-sm-2 control-label">Tracking</label>
                                <input type="text" class="form-control w-100" value="<?php echo $tracking_number; ?>" id="tracking_number" name="tracking_number">
                                <input type="checkbox" id="toggleTrack" onclick="miFuncion(event);"><label></label>
                                <label class="col-sm-2 control-label">Fecha</label>
                                <input type="date" class="form-control w-100" value="<?php echo $fecha; ?>" id="fecha" name="fecha">
                            </div>
                            <br>
                            <div class="input-group input-group-sm mb-1">
                                <label class="col-sm-2 control-label">Cuenta</label>
                                <input type="text" class="form-control w-100" value="<?php echo $consignee_account; ?>" id="consignee_account" name="consignee_account">

                                <label class="col-sm-2 control-label">Identificacion Oficial</label>
                                <input type="text" value="<?php echo $iden_of; ?>" class="form-control w-100" id="iden_of" name="iden_of">
                            </div>
                            <br>
                            <div class="input-group input-group-sm mb-1">
                                <label class="col-sm-2 control-label">Expedidor Nombre</label>
                                <input type="text" class="form-control w-100" value="<?php echo $shipper_name; ?>" id="shipper_name" name="shipper_name">

                                <label class="col-sm-2 control-label">Consignatario Nombre</label>
                                <input type="text" class="form-control w-100" value="<?php echo $consignee; ?>" id="consignee" name="consignee">
                            </div>
                            <br>
                            <div class="input-group input-group-sm mb-1">
                                <label class="col-sm-2 control-label">Exp. direccion 1</label>
                                <input type="text" class="form-control w-100" value="<?php echo $shipper_address1; ?>" id="shipper_address1" name="shipper_address1">

                                <label class="col-sm-2 control-label">Consig. direccion 1</label>
                                <input type="text" class="form-control w-100" value="<?php echo $consignee_address1; ?>" id="consignee_address1" name="consignee_address1">
                            </div>
                            <br>
                            <div class="input-group input-group-sm mb-1">
                                <label class="col-sm-2 control-label">Exp. direccion 2</label>
                                <input type="text" class="form-control w-100" value="<?php echo $shipper_address2; ?>" id="shipper_address2" name="shipper_address2">

                                <label class="col-sm-2 control-label">Consig. direccion 2</label>
                                <input type="text" class="form-control w-100" value="<?php echo $consignee_address2; ?>" id="consignee_address2" name="consignee_address2">
                            </div>
                            <br>
                            <div class="input-group input-group-sm mb-1">
                                <label class="col-sm-2 control-label">Pais Origen</label>

                                <select name="pais_origen" id="pais_origen"
                                    class="form-control chosen">
                                    <?php foreach ($paises as $row): ?>
                                        <option <?php echo ($pais_origen == $row->id) ? "selected" : ""; ?>
                                            value="<?php echo $row->id; ?>">
                                            <?php echo  $row->id . ' - ' . $row->nombre_espanol; ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>

                                <label class="col-sm-2 control-label">Pais Destino</label>
                                <select name="pais_destino" id="pais_destino" class="form-control chosen">
                                    <?php foreach ($paises as $row): ?>
                                        <option <?php echo ($pais_destino == $row->id) ? "selected" : ""; ?>
                                            value="<?php echo $row->id; ?>">
                                            <?php echo  $row->id . ' - ' . $row->nombre_espanol; ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                            <br>
                           
                            <div class="input-group input-group-sm mb-1">
                                <label class="col-sm-2 control-label">Tipo paquete</label>
                                <input type="text" class="form-control w-100" value="<?php echo $commodity; ?>" id="commodity" name="commodity">

                                <label class="col-sm-2 control-label">Bultos</label>
                                <input type="text" class="form-control w-100" value="<?php echo $bultos; ?>" id="bultos" name="bultos">
                            </div>
                            <br>
                            <div class="input-group input-group-sm mb-1">
                                <label class="col-sm-2 control-label">Peso (kg)</label>
                                <input type="text" class="form-control w-100" value="<?php echo $gweight; ?>" id="gweight" name="gweight">

                                <label class="col-sm-2 control-label">Contenido/Descrip</label>
                                <input type="text" value="<?php echo $contenido; ?>" class="form-control w-100" id="contenido" name="contenido">
                            </div>
                            <br>
                            <div class="input-group input-group-sm mb-1">
                                <label class="col-sm-2 control-label">Valor</label>
                                <input type="text" class="form-control w-100" value="<?php echo $cobro_final; ?>" id="cobro_final" name="cobro_final">
                                <label class="col-sm-2 control-label">Flete</label>
                                <input type="text" value="<?php echo $flete; ?>" class="form-control w-100" id="flete" name="flete">
                            </div>
                            <br>
                            <div class="input-group input-group-sm mb-1">
                                <label class="col-sm-2 control-label">NATCOD</label>
                                <input type="text" class="form-control w-100" value="<?php echo $natcod; ?>" id="natcod" name="natcod">
                                <label class="col-sm-2 control-label">Tipo de codigo</label>
                                <input type="text" value="<?php echo $bol_type_code; ?>" class="form-control w-100" id="bol_type_code" name="bol_type_code">
                            </div>
                            <br>
                            <div class="card-footer text-center">

                                <button type="button" class="btn btn-secondary" id="atras1" onClick="back()"> Atras</button>
                                <!-- <button id="atras2" type="button" onclick="Alista_warehouse_fast2()"  type="button" class="btn btn-dark btn-lg"> Atras jg</button> -->
                                <button type="button" class="btn btn-primary " onclick="guardar_wh_fast()"> Guardar</button>
                              
                            </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
    <script>
        function miFuncion(event) {

            if ($('#toggleTrack').is(":checked")) {
                $('#tracking_number').attr("readonly", false);

            } else {
                $('#tracking_number').attr("readonly", true);


            }
        } //end functi

        function back(){
            Alista_warehouse_fast( $("#id_manifiesto").val(),$("#master").val(), 0);
        }

    </script>
</body>

</html>