<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   
    <style>
        .tooltip-inner 
        {
            background-color: #0F4C5C !important;
            color: #0F4C5C;
        }

        .bs-tooltip-top .arrow::before,
        .bs-tooltip-auto[x-placement^="top"] .arrow::before 
        {
            border-top-color: #0F4C5C !important;
        }

        .bs-tooltip-right .arrow::before,
        .bs-tooltip-auto[x-placement^="right"] .arrow::before 
        {
            border-right-color: #0F4C5C !important;
        }


        .bs-tooltip-bottom .arrow::before,
        .bs-tooltip-auto[x-placement^="bottom"] .arrow::before 
        {
            border-bottom-color: #0F4C5C !important;
        }

        .bs-tooltip-left .arrow::before,
        .bs-tooltip-auto[x-placement^="left"] .arrow::before 
        {
            border-left-color: #0F4C5C !important;
        }

        .modal-text-header 
        {
            color: #F15A29;
        }

        .dropdown-options 
        {
            display: none;
            position: relative;
            overflow: auto;
        }

        .dropdown:hover .dropdown-options 
        {
            display: block;
        }


        .accordion-single  {
            border-bottom: 1px solid #efefef;
            margin-top: 10px;
        }

        .accordion-single-title {
            box-shadow: 0 1px 0px rgb(0 0 0 / 5%), 0 1px 5px rgb(0 0 0 / 12%) !important;
            border-radius: 9px;
            background-color: #f0f0f082;
            padding: 20px;
            cursor: pointer;
            position: relative;
            font-size: 20px;
        }

        .accordion-single-title::after{
            content: "";
            position: absolute;
            right: 25px;
            top: 50%;
            transition: all 0.2s ease-in-out;
            display: block;
            width: 8px;
            height: 8px;
            border-top: solid 2px #999;
            border-right: solid 2px #999;
            transform: translateY(-50%) rotate(135deg);
        }

        .accordion-single-content {
            margin: 1rem 0rem;
            max-height: 0;
            overflow: hidden;
            transition: max-height .3s ease-in-out;
        }

        .accordion-single-content p {
            padding: 20px;
        }

        .accordion-single-item.is-open .accordion-single-content  {
            max-height: 600px;
        }

        .accordion-single-item.is-open .accordion-single-title::after  {
            transform: translateY(-50%) rotate(315deg);
        }
    </style>

</head>

<body>

    <div class="container-fluid" id="enca">
        <div class="row mt-2 ml-5 ">
            <div class="form-group mt-3 col-md-3 my-auto">
                <h2 id="master-number" name="master-number"></h2>
                <input type="hidden" id="id-master">
                <input type="hidden" id="name_master">
            </div>
            <div class="form-group md-2 mt-3 col-md-5" >
                <div class="row">
                    <button type="button" class="btn btn-primary btn-lg " onclick="editar_wh_fast(<?php echo $row->id; ?>)" title="Editar Guia" >
                        <i class="fa fa-plus"></i>
                    </button>
                    <button  class="btn btn-success btn-lg "
                    title="Manisfiesto Fast" onclick="lista_warehouseexport_cla()">
                        <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-lg " data-toggle="tooltip" data-placement="bottom" title="Imprimir facturas referencia" onclick="imprimir_referencia()">
                        <i class="fa fa-file-pdf-o"></i>
                    </button>
                    <button type="button" class="btn btn-secondary btn-lg  " data-toggle="tooltip" data-placement="bottom" title="Imprimi" >
                        <i class="fa fa-cog "></i>
                    </button>
                    <div class="col-md my-auto pb-2" >
                        <select name="refer" id="refer" class="form-control chosen" >
                                <option> seleccion refencia </option>    
                                <?php 
                                if(isset($ref)){
                                    $sel = $ref;
                                }else{
                                    $sel = 0;
                                }
                                echo $sel;

                                if (isset($lst_referencia)) { 
                                foreach ($lst_referencia as $row) { ?>
                                        <option value="<?php echo $row->referencia; ?>"   <?php if($sel === $row->referencia){echo 'selected';}else {echo '';} ?> >
                                                <?php echo $row->referencia; ?>
                                        </option>
                                <?php }} ?>                      
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group md-2 mt-3 col-md-3 my-auto">
                <div class="row float-right">
                    <button type="button" class="btn btn-primary btn-lg m-0 float-right"  data-toggle="tooltip" data-placement="bottom" title="Transmitir" onclick="tramite_aduana_cla()">
                        <i class="fa fa-share-alt-square"></i>
                    </button>
                </div>
            </div>
        </div>

        <hr class="borde-orange mt-0">

        <div class="container-fluid m-top" id="contenido-individual">
            <div class="row">
                <div class="col-sm-3"></div>
                <div class="col-sm-3"></div>
            </div>
        </div>
    </div>
    <!--?php var_dump($lst_referencia); ?-->
    <div class="container-fluid">
        <div class="contents">
                <!-- acc single -->
                <div class="accordion-single js-acc-single">
                  <div class="accordion-single-item js-acc-item">
                    <h2 class="accordion-single-title js-acc-single-trigger">Información de guia master</h2>
                    <div class="accordion-single-content">
                        <div class="container-fluid">
                            <form class="update_wh_fast">
                                <div class="form-row">
                                    <div>
                                        <input type="hidden" id="id_precla" name="id_precla" 
                                        value="<?php echo $preclasificacion[0]->idpreclasificacion;?>">
                                    </div>
                                    <div class="form-group col-md-3">
                                      <label for="inputEmail4">Declarante</label>
                                      <!--input type="text" class="form-control" id="declarante" 
                                      name="declarante" value="<?php echo $preclasificacion[0]->declarante;?>"-->
                                      <select name="declarante" id="declarante" 
                                            class="form-control chosen">
                                                <option <?php echo ($preclasificacion[0]->declarante=='Global Cargo de El Salvador S.A. de C.V.')?"selected":"";?>
                                                    value="Global Cargo de El Salvador S.A. de C.V.">Global Cargo de El Salvador S.A. de C.V.
                                                </option>
                                            </select>
                                    </div>
                                    <div class="form-group col-md-3">
                                      <label for="inputPassword4">Transporte</label>
                                      <!--input type="text" class="form-control" value="<?php echo $preclasificacion[0]->id_tipo_transporte; ?>" 
                                      id="id_tipo_transporte" name="id_tipo_transporte"-->
                                      <select name="tipo_transporte" id="tipo_transporte" 
                                            class="form-control chosen" >
                                                <option <?php echo ($preclasificacion[0]->tipo_transporte=='Aereo')?"selected":"";?>
                                                    value="Aereo">Aereo
                                                       
                                                </option>
                                                <option <?php echo ($preclasificacion[0]->tipo_transporte=='Terrestre')?"selected":"";?>
                                                    value="Terrestre">Terrestre
                                                </option>
                                                <option <?php echo ($preclasificacion[0]->tipo_transporte=='Maritimo')?"selected":"";?>
                                                    value="Maritimo">Maritimo
                                                </option>
                                      </select>
                                    </div>
                                    <div class="form-group col-md-3">
                                            <label for="inputEmail4">Transportista Master</label>
                                            <select name="id_transportista" id="id_transportista" 
                                            class="form-control chosen" >
                                                <?php foreach ($transportista as $row): ?>
                                                    <option <?php echo ($preclasificacion[0]->id_transportista==$row->id)?"selected":"";?>
                                                    value="<?php echo $row->id; ?>">
                                                        <?php echo  $row->id.' - '.$row->nombre_tra; ?>
                                                    </option>
                                                <?php endforeach ?>
                                            </select>
                                    </div>
                                    <div class="form-group col-md-3">
                                            <label for="inputPassword4">Documento Master</label>
                                            <input type="text" class="form-control" id="manifiesto" name="manifiesto" 
                                            value="<?php echo $preclasificacion[0]->manifiesto; ?>">
                                    </div>
                                </div>
                                <div class="form-row">
                                        <div class="form-group col-md-3">
                                            <label for="inputEmail4">Agente Expedidor</label>
                                            <!--input type="text" class="form-control" id="agent_exp" 
                                            name="agent_exp" value="<?php echo $preclasificacion[0]->agent_exp;?>"-->
                                            <select name="agent_exp" id="agent_exp" 
                                            class="form-control chosen">
                                                <option <?php echo ($preclasificacion[0]->agent_exp=='PAK YA')?"selected":"";?>
                                                    value="PAK YA">PAK YA
                                                </option>
                                            </select>
                                        </div>

                                        <div class="form-group col-md-3">
                                            <label for="inputPassword4">Puerto Salida</label>
                                            <!--input type="text" class="form-control" id="puerto_salida" name="puerto_salida"
                                            value="<?php echo $preclasificacion[0]->puerto_salida; ?>"-->
                                            <select name="puerto_salida" id="puerto_salida" 
                                            class="form-control chosen">
                                                <option <?php echo ($preclasificacion[0]->puerto_salida=='MIAMI, FL., US (USMIA)')?"selected":"";?>
                                                    value="MIAMI, FL., US (USMIA)">MIAMI, FL., US (USMIA)
                                                </option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="inputEmail4">Agente Consignatario</label>
                                            <!--input type="text" class="form-control" id="agent_cong" name="agent_cong"
                                            value="<?php echo $preclasificacion[0]->agent_cong; ?>"-->
                                            <select name="agent_cong" id="agent_cong" 
                                            class="form-control chosen">
                                                <option <?php echo ($preclasificacion[0]->agent_cong=='Global Cargo de El Salvador S.A. de C.V.')?"selected":"";?>
                                                    value="Global Cargo de El Salvador S.A. de C.V.">Global Cargo de El Salvador S.A. de C.V.
                                                </option>
                                            </select>
                                        </div>

                                        <div class="form-group col-md-3">
                                            <label for="inputPassword4">Puerto Salida</label>
                                            <!--input type="text" class="form-control" id="puerto_salida2" name="puerto_salida2"
                                            value="<?php echo $preclasificacion[0]->puerto_salida2; ?>"-->
                                            <select name="puerto_salida2" id="puerto_salida2" 
                                            class="form-control chosen">
                                                <option <?php echo ($preclasificacion[0]->puerto_salida2=='COMALAPA, S.V. (SVCOM)')?"selected":"";?>
                                                    value="COMALAPA, S.V. (SVCOM)">COMALAPA, S.V. (SVCOM)
                                                </option>
                                            </select>
                                        </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label for="inputEmail4">Fecha Salida</label>
                                        <input type="date" class="form-control" id="fechasalida" name="fechasalida"
                                        value="<?php echo $preclasificacion[0]->fechasalida; ?>">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="inputPassword4">Aduana</label>
                                        <!--input type="text" class="form-control" id="aduana" name="aduana"
                                        value="<?php echo $preclasificacion[0]->aduana; ?>"-->
                                        <select name="aduana" id="aduana" 
                                            class="form-control chosen">
                                                <option <?php echo ($preclasificacion[0]->aduana=='AEREA DE COMALAPA')?"selected":"";?>
                                                    value="AEREA DE COMALAPA">AEREA DE COMALAPA
                                                </option>
                                            </select>
                                    </div>
                                    <div class="form-group col-md-3">
                                            <label for="inputEmail4">Viaje</label>
                                            <input type="text" class="form-control" value="<?php echo $preclasificacion[0]->viaje; ?>" 
                                            id="viaje" name="viaje">
                                    </div>
                                    <div class="form-group col-md-3">
                                            <label for="inputPassword4">Línea del máster</label>
                                            <input type="text" class="form-control" value="<?php echo $preclasificacion[0]->linea_master; ?>" 
                                            id="linea_master" name="linea_master">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="inputEmail4">Núm. reg. adu</label>
                                        <input type="text" class="form-control" value="<?php echo $preclasificacion[0]->num_reg; ?>"
                                        id="num_reg" name="num_reg">
                                    </div>
                                    <div class="form-group col-md-6 my-auto">
                                        <button type="button" class="btn btn-primary float-md-right" onclick="update_wh_fast()">Aceptar</button>
                                        
                                        <button type="button" class="btn btn-secondary float-md-right" data-dismiss="modal">Cerrar</button>
                                    </div>      
                                </div>
                            </form>
                        </div>
                    </div>
                  </div>
                </div><!-- accordion-single -->
        </div>
    </div>

    <input type="hidden" id="opc-clasif">
    <div class="container" id="detalle-partida" style="display:none">

        <div class="row">
            <div class="col-sm-2"></div>
            <div class="col-sm-8">
                <div class="form-group">
                    <div class="text-center"></div>
                </div>
            </div>

            <div class="col-sm-2"></div>
        </div>
    </div>

    <div class="container-fluid m-top" id="contenido-principal">
        <div class="table-responsive">
            <div class="row"></div>

            <table class="table custom-table" id="tbl_detalle">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Tracking Number</th>
                        <th scope="col">Referencia</th>
                        <th scope="col">Expendidor</th>
                        <th scope="col">Consignatario</th>
                        <th scope="col">Bultos</th>
                        <th scope="col">Peso kg</th>
                        <th scope="col">Contenido</th>
                        <th scope="col">Valor</th>
                        <th scope="col">Declaracion</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>

                <tbody id="awb_detalle">
                    <?php
                    if(isset($lista)) 
                    {
                        $c = 1;
                        foreach ($lista as $row) 
                        { 
                    ?>
                            <tr class="rw ">
                                <td style="margin-left: 9px ; "><?php echo $c; ?></td>
                                <td scope="row"><?php echo $row->tracking_number; ?></td>
                                <td scope="row"><?php echo $row->referencia; ?></td>
                                <td scope="row"><?php echo $row->shipper_name; ?></td>
                                <td scope="row"><?php echo utf8_decode($row->consignee); ?></td>
                                <td scope="row"><?php echo $row->pieces; ?></td>
                                <td scope="row"><?php echo $row->gweight.' kg';  ?></td>
                                <td scope="row" style="width:100px"><?php echo $row->commodity; ?></td>
                                <td scope="row"><?php echo '$'.$row->cobro_final; ?></td>
                                <td scope="row"><?php echo ''; ?></td>
                                <td>
                                    <div class="row">
                                        <button class="col col-md-3 mx-1 btn btn-light" onclick="editar_wh_fast(<?php echo $row->id; ?>)" title="Editar Guia" style = "margin:0px;">
                                        <i class="fa fa-pencil" aria-hidden="true"></i>
                                    </button>
                                    <button  class="col col-md-3 mx-1 btn btn-light" onclick="open_modal_multiplicar(<?php echo "'$row->id'"; ?>)" title="Multiplicar" style="margin:0px;">
                                        <i class="fa fa-cut"></i>
                                    </button>
                                    <button  class="col col-md-3 mx-1 btn btn-light" onclick="eliminar_wh_fast(<?php echo $row->id; ?>)" title="Eliminar Guia" style = "margin:0px;">
                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                    </button>
                                    </div>
                                </td>
                            </tr>
                            
                            <?php
                                    $c = $c + 1;
                                }
                            }
                              ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php
        if(isset($info_master)) 
        {
            $c = 1;
            foreach ($info_master as $row) 
            { 
                $master = $row->manifiesto;
            }
        }
    ?>
    <!-- Modal Para Reemplazar Numero de Guias -->
    <div class="modal fade bd-example-modal-lg" id="modal_info_fast" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background-color:white">
                    <h4 class="modal-title" style="color:#F15a29">Editar Manifiesto</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="conatainer">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="inputEmail4">Declarante</label>
                                <input type="text" class="form-control" id="decalarante" name="decalarante" value="Global Cargo de El Salvador S.A. de C.V. - TC263">
                            </div>

                            <div class="form-group col-md-6">
                                <label for="inputPassword4">Transporte</label>
                                <input type="text" class="form-control" id="inputPassword4" value="Aéreo">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="inputEmail4">Transportista Master</label>
                                <select name="Atransportista_fast" id="Atransportista_fast" class="form-control chosen">
	                                <?php foreach ($transportista_fast as $row): ?>
	                        	        <option value="<?php echo $row->id; ?>">
	                        	            <?php echo  $row->id.' - '.$row->nombre_tra; ?>
	                        	        </option>
	                                <?php endforeach ?>
                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="inputPassword4">Documento Master</label>
                                <input type="text" class="form-control" id="inputPassword4" value="<?php echo $master; ?>"><?php echo $master; ?>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="inputEmail4">Agente Expedidor</label>
                                <input type="text" class="form-control" id="decalarante" name="decalarante" value="PAK YA">
                            </div>

                            <div class="form-group col-md-6">
                                <label for="inputPassword4">Puerto Salida</label>
                                <input type="text" class="form-control" id="inputPassword4" value="MIAMI, FL., US. (USMIA)">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="inputEmail4">Agente Consignatario</label>
                                <input type="text" class="form-control" id="inputEmail4" value="Global Cargo de El Salvador S.A. de C.V.">
                            </div>

                            <div class="form-group col-md-6">
                                <label for="inputPassword4">Puerto Salida</label>
                                <input type="text" class="form-control" id="inputPassword4" value="COMALAPA, SV. (SVCOM)">
                            </div>
                        </div>
                    </div>       

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="inputEmail4">Fecha Salida</label>
                            <input type="date" class="form-control" id="declarante" name="declarante">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="inputPassword4">Aduana</label>
                            <input type="text" class="form-control" id="inputPassword4">
                        </div>
                    </div>
                    
                    
                    <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="inputEmail4">Viaje</label>
                                <input type="text" class="form-control" id="decalarante" name="decalarante">
                            </div>

                            <div class="form-group col-md-6">
                                <label for="inputPassword4">Línea del máster</label>
                                <input type="text" class="form-control" id="inputPassword4">
                            </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="inputEmail4">Núm. reg. adu</label>
                            <input type="text" class="form-control" id="decalarante" name="decalarante">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-primary float-md-right" onclick="reemplazar_tracking(<?php echo $row->id ?>)">Aceptar</button>
                    <button type="button" class="btn btn-secondary float-md-right" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- modal  para multiplicar  paquetes-->
    <div class="modal fade" id="Modal_multiplicar" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-white modal-text-header ">
                    <h5 class="modal-title" id="" style="color: #F15A29">Multiplicar</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form enctype="multipart/form-data" class="add_multi" id="add_multi" action="javascript:guardar_item_multi()">

                        <input type="text" class="form-control" name="id-item" id="id-item">
                        <div class="form-group">
                            <label for="sacos">Cantidad</label>
                            <input type="number" class="form-control" min="1" step="1" placeholder="Introduzca Cantidad" id="cant" name="cant">
                        </div>

                        <!--button type="button" class="btn btn-secondary float-md-right" onclick="cerrar_modal()">Cerrar
                        </button-->
                        <button class="btn btn-primary float-md-right ">Guardar</button>

                    </form>


                </div>

            </div>
        </div>
    </div>

    <!-- Print referencia -->
    <div class="modal fade" id="modal_print_referencia" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color:white">
                    <h4 class="modal-title" style="color:#F15a29">Imprimir referencia </h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="conatainer">


                        <div class="row">
                            <div class="col">
                                <input type="text" class="form-control" id="ref_number" placeholder="Referencia">
                            </div>
                        </div>
                    </div>


                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-primary float-md-right" onclick="lista_warehouse1()">Aceptar</button>
                    <button type="button" class="btn btn-secondary float-md-right" data-dismiss="modal">Cerrar
                    </button>
                </div>
            </div>


        </div>
    </div>
    
     <!-- modal para tramite-->
    <div class="modal fade" id="modal_tramite_cla" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-white modal-text-header ">
                    <h5 class="modal-title" id="" style="color: #F15A29">Transmitir</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form enctype="multipart/form-data" class="add_multi" id="add_multi" action="javascript:guardar_item_multi()">

                        <div class="form-group">
                            <label for="sacos">Si está seguro de continuar, click en el botón enviar</label>
                        </div>

                        <!--button type="button" class="btn btn-secondary float-md-right" onclick="cerrar_modal()">Cerrar
                        </button-->
                        <div style="float: inline-end;">
                        <button class="btn btn-secondary float-md-left ">Cancelar</button>
                        <button class="btn btn-primary float-md-left ">Enviar</button>
                        </div>
                        

                    </form>


                </div>

            </div>
        </div>
    </div>

    <script src="<?php echo base_url(); ?>assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/plugins/jszip/jszip.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/plugins/pdfmake/pdfmake.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/plugins/pdfmake/vfs_fonts.js"></script>
    <script src="<?php echo base_url(); ?>assets/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.16/dist/sweetalert2.all.min.js"></script>


    <script>
        $(document).ready(function(){
           
                $("#tbl_detalle").DataTable({
                    "responsive"   : true,
                    "lengthChange" : true,
                    "paging"       : true,
                    "searching"    : true,
                    "ordering"     : true,
                    "info"         : true,
                    "lengthMenu" : [[3,5, 10, 20, -1], [3,5, 10, 20, "Todos"]],
                    "dom": "RlBfrtip",
                    "buttons": [
                        'excel'
                    ]
                    
                });
        });
        $(document).ready(function() {
            const accSingleTriggers = document.querySelectorAll('.js-acc-single-trigger');

            accSingleTriggers.forEach(trigger => trigger.addEventListener('click', toggleAccordion));

            function toggleAccordion() {
              const items = document.querySelectorAll('.js-acc-item');
              const thisItem = this.parentNode;

              items.forEach(item => {
                if (thisItem == item) {
                  thisItem.classList.toggle('is-open');
                  return;
                }
                item.classList.remove('is-open');
              });
            }
            $('.js-check-all').on('click', function() 
            {

                if ($(this).prop('checked')) 
                {
                    $('th input[type="checkbox"]').each(function() 
                    {
                        $(this).prop('checked', true);
                        $(this).closest('tr').addClass('active');
                    })
                } 
                else 
                {
                    $('th input[type="checkbox"]').each(function() 
                    {
                        $(this).prop('checked', false);
                        $(this).closest('tr').removeClass('active');
                    })
                }
            });

            $('th[scope="row"] input[type="checkbox"]').on('click', function() 
            {
                if ($(this).closest('tr').hasClass('active')) 
                {
                    $(this).closest('tr').removeClass('active');
                } 
                else 
                {
                    $(this).closest('tr').addClass('active');
                }
            });

            $(document).ready(function() 
            {
                $("#buscar").on("keyup", function() 
                {
                    var value = $(this).val().toLowerCase();
                    $("#awb_detalle tr").filter(function() 
                    {
                        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                    });
                });
            });
        });


        /**Fin */
        function verificar_id(id) 
        {
            nombre = "chk" + id;
            opc = 0;
            if ($('#' + nombre).is(':checked')) 
            {
                $('#valor').val(1);
                //  alert(nombre+' esta Seleccionado');
                opc = 1;
            } 
            else 
            {
                $('#valor').val(0);
                //  alert(nombre+' esta off');
                opc = 0;
            }

            var url = base_url("index.php/WhController/seleccionar_item/" + id + "/" + opc);
            $.get(url, function(data) 
            {

            });
        }
    </script>

    <script>
        $(document).ready(function() 
        {
            
        });

        $('#catalogo_ref').on('change', function() 
        {
            id = $("#id-master").val();
            master = $("#name_master").val();
            ref = this.value;

            if (ref == 'Todo') 
            {
                Alista_warehouse_fast(id, master, 0)
            } 
            else 
            {
                if (ref == 'Pendiente') 
                {
                    Alista_referencia(id, master, ref)
                } 
                else 
                {
                    Alista_referencia(id, master, ref)
                }
            }
        });

        $("#refer").change(function() 
        {   id = $("#id-master").val();
            master = $("#name_master").val();
            ref = this.value;

            console.log(id, master,ref);

            lista_manifiesto_refer(id, master, ref);
            
        });


        $("#agencia").change(function() 
        {
            if ($("#agencia").val() == 0) 
            {
                $("#tipo_entrega").val("NRML");
                $("#tipo_servicio").val("CCE");
            } 
            else 
            {
                var texto = $(this).find('option:selected').text();
                $("textarea#direccion").text(texto);
                $("#tipo_entrega").val("ECON");
                $("#tipo_servicio").val("CCE");
            }
        });


        function refreshForm() 
        {
            Alista_warehouse_fast($('#id-master').val(), $('#master-number').val());
        }

        function ver_individual() {
            $("#contenido-principal").hide();
            $("#enca").hide();
            $("#contenido-individual").show();
        }

        function ocultar_individual() 
        {
            $("#contenido-principal").show();
            $("#enca").show();
            $("#contenido-individual").hide();
        }

        $("#numero-guia").blur(function() 
        {
            Alista_warehouse_fast($("#id-master").val(), $("#name_master").val(), $("#numero-guia").val());
            //  $("#tracking01").val( $("#numero-guia").val());
        });
        
        function tramite_aduana_cla(){
            $("#modal_tramite_cla").modal("show");
        }
       

        // $("#numero-guia").val($("#master-number").val());
    </script>
    <style>
        #tblcuerpo_length,#tbl_detalle_length{
            display: inline-block;
            margin: 0px 25px 0px 25px!important;
        }
        .dt-buttons{
            float: left;
            display: inline-block;
            /*padding: 0rem 2rem;*/
        }
        .dt-buttons .btn{
            margin: 0px 25px 0px 25px!important;
        }
        #tblcuerpo_filter,#tbl_detalle_filter{
           display: inline-block;
           float: right;
           margin: 0px 25px 0px 25px!important; 
        }
    </style>

</body>

</html>