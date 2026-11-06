<div class="container-fluid m-top ">
    <div class="table-responsive">
        <table class="table custom-table" id="tblcuerpoaws">
            <thead>
                <tr>
                    <th scope="col"> # </td>
                    <th scope="col">AWB</td>
                    <th scope="col">Fecha</td>
                    <th scope="col">Descripcion</td>
                    
                    <th scope="col" >Acciones</td>

                </tr>
            </thead>
            <tbody>
                <!--div id="listaawb"-->
                    <?php
                  //  var_dump($lista);
                    if (isset($lista)) {
                        $c = 1;
                        foreach ($lista as $row) { ?>
                            <tr class="rw">

                                <td scope="row" style="margin-left: 9px"><?php echo $c; ?></td>
                                <td scope="row"><?php echo $row->manifiesto; ?></td>
                                <td scope="row"><?php echo $row->fecha; ?></td>
                                <td scope="row"><?php echo $row->descripcion; ?></td>
                                <?php 
                                if($row->tipo=="TRA"){ ?>

                                <td>
                                   <a href="#" class="btn btn-default btn-sm"  onclick="clasifica(<?php echo $row->idpreclasificacion ;?>,<?php echo "'$row->manifiesto'" ;?>)" title="Verificar paquetes..." style="margin:0px;">
                                        <i class="fas fa-check text-primary"></i>
                                    </a>
                                </td>     
                                
                                <?php    
                                }else{?>
                                 
                                <td>
                                    <a href="#" class="btn btn-default btn-sm"  onclick="lista_warehouse_cla(<?php echo $row->idpreclasificacion ;?>,<?php echo "'$row->manifiesto'"; ?>, <?php echo 0; ?>)" title="Ver detalle..." style="margin:0px;">
                                        <i class="fas fa-search text-warning"></i></a>
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
                <!--/div -->
            </tbody>
        </table>
    </div>
    
</div>
<!--script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script-->
<!--script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script-->

<!--script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.3/css/dataTables.dataTables.css" /--> 
<script>
    $(document).ready(function(){
 
         $("#tblcuerpoaws").DataTable({
                "paging"       : true,
                "searching"    : true,
                "ordering"     : true,
                "info"         : true,
                "lengthChange" : true,
            });
    });
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