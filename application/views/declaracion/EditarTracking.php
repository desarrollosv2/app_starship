 <style>
     .modal-xl {
         max-width: 90% !important;
         /* Ajusta el ancho según lo necesites */
     }
 </style>
 <?php
    $master                = 0;
    $idManif               = 0;
    $tracking_number       = 0;
    $contenido_descripcion = '';
    $unidades              = 0;
    $bultos                = 0;
    $pesos                 = 0;
    $valor                 = 0;
    $seguro                = 0;
    $flete                 = 0;
    $consignatario         = "";
    $id_consignatario      = 0;
    $documento_transporte  = 0;
    $tipo_id               = 0;
    $regimen               = 0;
    $pais_origen           = 0;
    $idItem                = 0;
    $num_reg               = 0;
    $partida               = 0;

    $objeto = $detTraking[0];

    if (isset($detTraking)) {


        $master                = $objeto->manifiesto;
        $documento_transporte  = $objeto->DocumentoTransporte;
        $factura_codigo        = $objeto->FacturaCodigo;
        $contenido_descripcion = $objeto->ContenidoDescripcion;
        $unidades              = $objeto->Unidades;
        $bultos                = $objeto->Bultos;
        $peso                  = $objeto->PesoKg;
        $valor                 = $objeto->Valor;
        $flete                 = $objeto->Flete;
        $seguro                = $objeto->Seguro;
        $consignatario         = $objeto->Consignatario;
        $id_consignatario      = $objeto->Consignatario;

        $pais_origenId         = $objeto->pais_origen;
        $otros                 = $objeto->otros;
        $num_reg               = $objeto->num_reg;
        $fecha_factura         = $objeto->FacturaFecha;
        $id_consignatario      = $objeto->IdConsignatario;
        $tipo_id               = $objeto->TipoId;
        $regimen               = $objeto->Regimen;
        $pais_origen           = $objeto->PaisOrigen;
        $idItem                = $objeto->idItem;
        $codigoArancelario     = $objeto->HSC3 . $objeto->TAR_PR1;
    }

    ?>
 <div class="modal fade bd-example-modal-xl" id="modal_editTrack" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
     <div class="modal-dialog modal-dialog-scrollable modal-xl modal-dialog-centered ">
         <div class="modal-content">
             <div class="modal-header" style="background-color:white">
                 <h4 class="modal-title text-muted">Editar item</h4>
                 <button type="button" class="close" data-dismiss="modal">&times;</button>
             </div>

             <div class="modal-body overflow-auto">
                 <form enctype="multipart/form-data" class="edit_declara" id="edit_declara">
                     <div class="card-body">
                         <div class="form-row">
                             <div class="col-md-4">
                                 <input type="hidden" id="id_item" name="id_item" value="<?php echo  $idItem ?>">
                                 <input type="hidden" id="id_precla" name="id_precla" value="">
                                 <input type="hidden" id="tipo_transporte_id" name="tipo_transporte_id" value="">
                                 <input type="hidden" id="transportista_id" name="transportista_id" value="">
                                 <input type="hidden" id="puertosalida_id" name="puertosalida_id" value="">
                                 <input type="hidden" id="puertosalida2_id" name="puertosalida2_id" value="">
                                 <input type="hidden" id="declarante_id" name="declarante_id" value="">
                                 <input type="hidden" id="agent_exp_id" name="agent_exp_id" value="">
                                 <input type="hidden" id="agent_cong_id" name="agent_cong_id" value="">
                             </div>
                         </div>

                         <div class="form-row">

                             <div class="col-md-4">
                                 <div class="form-group row">
                                     <label for="master" class="col-sm-4 text-muted ">Master</label>
                                     <div class="col-sm-8">
                                         <input type="text" class="form-control input-sm" value="<?php echo $num_reg ?>" id="master" name="master">
                                     </div>
                                 </div>
                                 <div class="form-group row">
                                     <label for="contenido_descripcion" class="col-sm-4 text-muted">Contenido</label>
                                     <div class="col-sm-8">
                                         <textarea name="contenido_descripcion" id="contenido_descripcion" cols="20" class="form-control"><?php echo $contenido_descripcion; ?></textarea>
                                     </div>
                                 </div>
                                 <div class="form-group row">
                                     <label for="valor" class="col-sm-4 text-muted">Valor</label>
                                     <div class="col-sm-8">
                                         <input type="number" class="form-control input-sm" value="<?php echo $valor ?>" id="valor" name="valor" step="any">
                                     </div>
                                 </div>
                                 <div class="form-group row">
                                     <label for="flete" class="col-sm-4 text-muted">Flete</label>
                                     <div class="col-sm-8">
                                         <input type="number" class="form-control input-sm" value="<?php echo $flete ?>" id="flete" name="flete" step="any" readonly>
                                     </div>
                                 </div>
                                 <div class="form-group row">
                                     <label for="factura_codigo" class="col-sm-4 text-muted">No. Factura</label>
                                     <div class="col-sm-8">
                                         <input type="text" class="form-control input-sm" value="<?php echo $factura_codigo; ?>" id="factura_codigo" name="factura_codigo">
                                     </div>
                                 </div>
                                 <div class="form-group row">
                                     <label for="consignee" class="col-sm-4 text-muted">Nombre consignatario</label>
                                     <div class="col-sm-8">
                                         <input type="text" class="form-control input-sm" value="<?php echo $consignatario; ?>" id="consignee" name="consignatario">
                                     </div>
                                 </div>
                             </div>
                             <div class="col-md-4">
                                 <div class="form-group row">
                                     <label for="documento_transporte" class="col-sm-4 text-muted">Tracking</label>
                                     <div class="col-sm-8">
                                         <input type="text" class="form-control input-sm" value="<?php echo $documento_transporte ?>" id="documento_transporte" name="documento_transporte">
                                     </div>
                                 </div>
                                 <div class="form-group row">
                                     <label for="unidades" class="col-sm-4 text-muted">Unidades</label>
                                     <div class="col-sm-8">
                                         <input type="number" class="form-control input-sm" value="<?php echo $unidades; ?>" id="unidades" name="unidades" step="any">
                                     </div>
                                 </div>
                                 <div class="form-group row">
                                     <label for="seguro" class="col-sm-4 text-muted">Peso</label>
                                     <div class="col-sm-8">
                                         <input type="number" class="form-control input-sm" value="<?php echo $peso ?>" id="peso" name="peso" step="any">
                                     </div>
                                 </div>
                                 <div class="form-group row">
                                     <label for="seguro" class="col-sm-4 text-muted">Seguro</label>
                                     <div class="col-sm-8">
                                         <input type="number" class="form-control input-sm" value="<?php echo number_format($seguro, 2) ?>" id="seguro" name="seguro" step="any" readonly>
                                     </div>
                                 </div>
                                 <div class="form-group row">
                                     <label for="otros" class="col-sm-4 text-muted">Otros</label>
                                     <div class="col-sm-8">
                                         <input type="number" class="form-control input-sm" value="" id="otros" name="otros" step="any">
                                     </div>
                                 </div>
                                 <div class="form-group row">
                                     <label for="fecha_factura" class="col-sm-4 text-muted">Factura fecha</label>
                                     <div class="col-sm-8">
                                         <input type="date" class="form-control input-sm" value="<?php echo $fecha_factura; ?>" id="fecha_factura" name="fecha_factura">
                                     </div>
                                 </div>

                             </div>
                             <div class="col-md-4">
                                 <div class="form-group row">
                                     <label for="bultos" class="col-sm-4 text-muted">Bultos</label>
                                     <div class="col-sm-8">
                                         <input type="number" class="form-control input-sm" value="<?php echo $bultos ?>" id="bultos" name="bultos" readonly>
                                     </div>
                                 </div>
                                 <div class="form-group row">
                                     <label for="pais_origen" class="col-sm-4 text-muted">Pais Origen</label>
                                     <div class="col-sm-8">
                                         <input type="text" class="form-control input-sm" value="<?php echo $pais_origen ?>" id="pais_origen" name="pais_origen">
                                     </div>
                                 </div>
                                 <div class="form-group row">
                                     <label for="tipoDocumento" class="col-sm-4 text-muted">Regimen</label>
                                     <div class="col-sm-8">
                                         <select class="form-control" id="regimen" name="regimen">
                                             <option value="000"><?php echo $regimen ?></option>
                                         </select>
                                     </div>
                                 </div>
                                 <div class="form-group row">
                                     <label for="tipoDocumento" class="col-sm-4 text-muted">Tipo ID</label>
                                     <div class="col-sm-8">
                                         <select class="form-control" id="tipoDocumento" name="tipoDocumento">
                                             <option value="NIT"><?php echo $tipo_id ?></option>
                                         </select>
                                     </div>
                                 </div>
                                 <div class="form-group row">
                                     <label for="id_consignatario" class="col-sm-4 text-muted">ID consignatario</label>
                                     <div class="col-sm-8">
                                         <input type="text" class="form-control input-sm" value="<?php echo $id_consignatario; ?>" id="id_consignatario" name="id_consignatario">
                                     </div>
                                 </div>
                             </div>

                         </div>
                         <!--bloque para partidas arancelarias  -->
                         <div class="modal-body">
                             <div class="card shadow-sm border-1 rounded-lg">
                                 <div class="card-header">
                                     <h5 class="text-muted">Código Arancelario</h5>
                                 </div>
                                 <div class="card-body">

                                     <div class="container-fluid">
                                         <div class="row">
                                             <div class="col-sm-3">
                                                 <label for="codigoArancelario" class=" text-muted">Código</label>
                                             </div>
                                             <div class="col-sm-3">
                                                 <input type="text" class="form-control form-control-sm"" id=" codigoArancelario" name="codigoArancelario" placeholder=""
                                                     value="<?php echo $codigoArancelario ?>">
                                             </div>
                                             <div class="col-sm-6">
                                                 <p></p>
                                             </div>
                                         </div>
                                         <div class="row">
                                             <div class="col-sm-3">
                                                 <label for="capitulo" class="text-muted">Capitulo</label>
                                             </div>
                                             <div class="col-sm-3">
                                                 <input type="text" class="form-control form-control-sm"" id=" capitulo" name="capitulo" placeholder="">
                                             </div>
                                             <div class="col-sm-6">
                                                 <p></p>
                                             </div>
                                         </div>


                                         <div class="row">
                                             <div class="col-sm-3">
                                                 <label for="subcapitulo" class="text-muted">Subcapítulo</label>
                                             </div>
                                             <div class="col-sm-3">
                                                 <input type="text" class="form-control form-control-sm"" id=" subcapitulo" name="subcapitulo" placeholder="">
                                             </div>
                                             <div class="col-sm-6">
                                                 <p></p>
                                             </div>
                                         </div>

                                         <div class="row">
                                             <div class="col-sm-3">
                                                 <label for="codigoArmonizado" class="text-muted">Código Armonizado</label>
                                             </div>
                                             <div class="col-sm-3">
                                                 <input type="text" class="form-control form-control-sm" id="codigoArmonizado" name="codigoArmonizado" placeholder="">
                                             </div>
                                             <div class="col-sm-6">
                                                 <p></p>
                                             </div>
                                         </div>

                                         <div class="row">
                                             <div class="col-sm-3">
                                                 <label for="dai" class="text-muted">DAI</label>
                                             </div>
                                             <div class="col-sm-3">
                                                 <input type="text" class="form-control form-control-sm"" id=" dai" name="dai" placeholder="">
                                             </div>
                                             <div class="col-sm-6">
                                                 <p></p>
                                             </div>
                                         </div>

                                         <div class="row">
                                             <div class="col-sm-3">
                                                 <label for="codigoArancelario" class="text-muted">IVA</label>
                                             </div>
                                             <div class="col-sm-3">
                                                 <input type="text" class="form-control form-control-sm"" id=" iva" name="iva" placeholder="">
                                             </div>
                                             <div class="col-sm-6">
                                                 <p></p>
                                             </div>
                                         </div>


                                         <div class="row">
                                             <div class="col-sm-3">
                                                 <label for="codigoArancelario" class="text-muted">Código Arancelario</label>
                                             </div>
                                             <div class="col-sm-3">
                                                 <input type="text" class="form-control form-control-sm"" id=" codigoArancelarioRef" name="codigoArancelarioRef" placeholder="">
                                             </div>
                                             <div class="col-sm-6">
                                                 <p></p>
                                             </div>
                                         </div>



                                     </div>

                                 </div>

                             </div>
                         </div>
                     </div>

                 </form>
             </div>

             <div class="modal-footer">
                 <div>
                     <!--<button type="button" class="btn btn-secondary" id="atras1" onClick="back()"> Atras</button> -->
                     <button class=" btn btn-primary btn-sm m-0 float-right" type=" button" id="btnprocesar_GuiasAdd" name="btnprocesar_GuiasAdd" onclick="updateInfoTrack()">Guardar Cambios</button>
                 </div>

             </div>
         </div>
     </div>
 </div>