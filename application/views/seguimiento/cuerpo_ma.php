<div class="container-fluid m-top ">
    <div class="table-responsive">
        <table class="table custom-table">
            <thead>
                <tr>
                    <th scope="col"> # </td>
                    <th scope="col">AWB</td>
                    <th scope="col">Fecha</td>
                    <th scope="col">Descripción</td>
                    
                    <th scope="col" colspan="5">Acciones</td>

                </tr>
            </thead>
            <tbody>
                <div id="listaawb">
                    <?php
                  //  var_dump($lista);
                    if (isset($lista)) {
                        $c = 1;
                        foreach ($lista as $row) { ?>
                            <tr>

                                <td scope="row" style="margin-left: 9px"><?php echo $c; ?></td>
                                <td scope="row"><?php echo $row->manifiesto; ?></td>
                                <td scope="row"><?php echo $row->fecha; ?></td>
                                <td scope="row"><?php echo $row->descripcion; ?></td>
                                <?php 
                                if($row->tipo=="TRA"){ ?>
                                     <td></td>
                                    
                                    <td></td>
                                    
                                    <td></td>
                                    
                                <td>
                                   <a href="#" class="btn btn-default btn-sm"  Onclick="clasifica(<?php echo $row->idpreclasificacion ;?>,<?php echo "'$row->manifiesto'" ;?>)" title="Verificar paquetes..." style="margin:0px;">
                                        <i class="fas fa-check text-primary"></i></a>
                                </td>     
                                
                                </td> <td>
                                    
                                
                                <?php    
                                }else{?>
                                 <td>
                                    <a href="#" class="btn btn-default btn-sm"  Onclick="cargar_archivo2(<?php  echo $row->idpreclasificacion;  ?>)" title="Actualizar Dui" style="margin:0px;">
                                        <i class="fa fa-id-card" aria-hidden="true"></i>

                                </td>
                                
                                <td>
                                    <a href="#" class="btn btn-default btn-sm"  Onclick="cargar_archivo1(<?php  echo $row->idpreclasificacion;  ?>)" title="Cargar Datos" style="margin:0px;">
                                        <i class="fa fa-upload" aria-hidden="true"></i>

                                </td>
                                
                                <td>
                                    <a href="#" class="btn btn-default btn-sm"  Onclick="lista_warehouse_confirma(<?php echo $row->idpreclasificacion ;?>,<?php echo "'$row->manifiesto'"; ?>)" title="Ver detalle..." style="margin:0px;">
                                        <i class="fas fa-search text-warning"></i></a>
                                </td>
                                
                                <td>
                                    <a href="#" class="btn btn-default btn-sm"  Onclick="clasifica(<?php echo $row->idpreclasificacion ;?>,<?php echo "'$row->manifiesto'" ;?>)" title="Verificar paquetes..." style="margin:0px;">
                                        <i class="fas fa-check text-primary"></i></a>
                                </td>
                                   
                                <td>
                                    <a href="#" class="btn btn-default btn-sm"  Onclick="traspaso_automatico(<?php echo $row->idpreclasificacion ;?>)" title="Crear manifiesto (Traspao)" style="margin:0px;">
                                        <i class="fas fa-share-alt text-primary"></i></a>
                                </td>
                                <?php    
                                }
                                
                                ?>
                               

                            </tr>
                    <?php
                            $c = $c + 1;
                        }
                    }
                    ?>
                </div>
            </tbody>
        </table>
    </div>
</div>
<script>
     $('#opciones').on('change', function() {
                opcion = this.value;
                if (opcion == 3 || opcion == 4 || opcion == 5) {
                    $("#f-ini").show();
                    $("#f-fin").show();
                    $("#b-buscar").show();
                    $("#buscar").hide();
                } else {
                    $("#f-ini").hide();
                    $("#f-fin").hide();
                    $("#b-buscar").hide();
                    $("#buscar").show();


                }
            });
</script>