<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <!-- <link href="https://fonts.googleapis.com/css?family=Montserrat:100,200,300,400" rel="stylesheet"> -->
    <title></title>
    <style>
        div.scroll_vertical {
            height: 800px;
            width: 100%;
            overflow: auto;

        }

        /*
    * {
  font-family: 'Montserrat', sans-serif;
}
.w100 {
  font-weight: 100;
}
.w200 {
  font-weight: 200;
}
.w300 {
  font-weight: 300;
}
.w400 {
  font-weight: 400;
} */

    </style>
</head>

<body>
    <?php $this->load->view('hd'); ?>
    <div class="container m-top scroll_vertical">
        <div class="table-responsive">
            <table class="table   table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Warehouse</th>
                        <th>Fecha ingreso</th>
                        <th>Fecha salida</th>
                        <th>Remitente</th>
                        <th>Descripcion</th>
                        <th>Bultos</th>
                        <th>Lbs</th>
                        <th>Tracking</th>
                        <th>Pre-Alertado</th>
                        <th>Estado</th>
                        <th>Total</th>
                        <th colspan="2">Acciones</th>
                    </tr>
                </thead>
                <tbody id="contenidoLista">
                </tbody>
            </table>

        </div>
    </div>





    <!--- modal para mostrar guias -->
    <div class="modal fade " id="ver_estatus_modal" role="dialog" data-backdrop="static">

        <div class="modal-dialog modal-dialog-centered mimodal " role="document">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header" style="background-color:white">
                    <h4 class="modal-title" style="color:#F15a29">Lista de Estatus</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="row-fluid">
                    <div class="messageup" id='messageup'></div>
                </div>

                <form enctype="multipart/form-data" class="listaestatus" id="listaestatus" action="javascript:consulta_estatus;">
                    <!-- Modal body -->
                    <div class="modal-body">
                        <div id="listado_estatus">

                            <?php $this->load->view('customers/lista_estatus');?>
                        </div>

                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!--- fin modal para mostrar guias -->




    <!--- fin modal para mostrar guias en Espress-->

    <script>
        listado_customer()

    </script>

    <script>
        jQuery(document).ready(function() {
            jQuery(".main-table").clone(true).appendTo('#tablaie').addClass('clone');
        });

    </script>


    <script>
        $(document).ready(function() {

            $("#myInput").on("keyup", function() {

                var value = $(this).val().toLowerCase();

                $("#contenidoLista tr").filter(function() {

                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)

                });

            });

        });

    </script>

</body>

</html>
