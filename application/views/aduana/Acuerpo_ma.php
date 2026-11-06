<style>
    #tblcuerpo_length{
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
    #tblcuerpo_filter{
       display: inline-block;
       float: right;
       margin: 0px 25px 0px 25px!important; 
    }
</style>
<?php $this->load->view("verifica");?>
<div class="container-fluid m-top">
    <div class="table-responsive">
        <table class="table custom-table" id="tblcuerpo">
            <thead>
                <tr>
                    <th scope="col"> # </th>
                    <th scope="col">Transportista</th>
                    <th scope="col">Referencia</th>
                    <th scope="col">Documento Transporte</th>
                    <th scope="col">Puerto Salida ccc</th>
                    <th scope="col">Puerto Llegada</th>
                    <th scope="col">Fecha Salida</th>
                    <th scope="col">Viaje</th>
                    <th scope="col">Linea Master</th>
                    <th scope="col">Cantidad Tracking</th>
                    <th scope="col">Tracking Declaraciones</th>
                    <th scope="col">Num Reg Aduana</th>
                    <th scope="col">Fecha Registro</th>
                    <th scope="col">Usuario</th>
                    <th scope="col">Acciones</th>
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
                            <td scope="row"><?php echo $c; ?></td>
                            <td scope="row"><?php echo $row->nombre_tra; ?></td>
                            <td scope="row"><?php echo $row->referencia; ?></td>
                            <td scope="row"><?php echo $row->manifiesto; ?></td>
                            <td scope="row"><?php echo $row->puerto_salida; ?></td>
                            <td scope="row"><?php echo $row->puerto_salida2; ?></td>
                            <td scope="row"><?php echo $row->fechasalida; ?></td>
                            <td scope="row"><?php echo $row->viaje; ?></td>
                            <td scope="row"><?php echo $row->linea_master; ?></td>
                            <td scope="row"><?php echo $row->n_guias; ?></td>
                            <td scope="row"><?php echo $row->declarante; ?></td>
                            <td scope="row"><?php echo $row->num_reg; ?></td>
                            <td scope="row"><?php echo $row->fecha; ?></td>
                            <td scope="row"><?php echo $row->nombre; ?></td>
                            <td scope="row">
                                <div class="row">
                                    <button class="col-5 mx-1 btn btn-secondary" onclick="Alista_warehouse_fast(<?php echo $row->idpreclasificacion; ?>,<?php echo "'$row->manifiesto'"; ?>, <?php echo 0; ?>)" title="Ver detalle..." style="margin:0px;">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button class="col-5 mx-1 btn btn-primary" onclick="trans_wh_fast(<?php echo $row->idpreclasificacion; ?>)" title="Transportista" style="margin:0px;">
                                        <i class="fa fa-plane"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                <?php
                        $c = $c + 1;
                    }
                } ?>
                <!--/div-->
            </tbody>
        </table>
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
<script>
    $(document).ready(function() {
        $("#tblcuerpo").DataTable({
            "responsive": true,
            "lengthChange": true,
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "lengthMenu": [
                [3, 5, 10, 20, -1],
                [3, 5, 10, 20, "Todos"]
            ],
            "dom": "RlBfrtip",
            "buttons": [
                'excel'
            ]
        });
    });
    $(document).ready(function() {
        /*$("#tbl-cuerpo").DataTable({
                "paging"       : true,
                "searching"    : true,
                "ordering"     : true,
                "info"         : true,
                "language"     : 
                {
                    "lengthMenu"   : "Mostrar _MENU_ registros por página",
                    "zeroRecords"  : "No se encontraron datos que mostrar",
                    "info"         : "Mostrando Página _PAGE_ de _PAGES_",
                    "infoEmpty"    : "registros no disponibles",
                    "infoFiltered" : "(filtered from _MAX_ total records)",
                    "sSearch"      : "Buscar:",
                    "oPaginate"    : 
                    {
                        "sFirst"       : "Primero",
                        "sLast"        : "Último",
                        "sNext"        : "Siguiente",
                        "sPrevious"    : "Anterior",
                    },
                },
                "lengthChange" : true,
            }).buttons().container().appendTo('#tbl-cuerpo_wrapper .col-md-6:eq(0)');
        });*/

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
    });
</script>