<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('public/css/newstyle.css') ?>">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css">
    <title></title>
    <style>
        .h {
            display: none;
        }
    </style>
</head>

<body>
    <?php // $this->load->view('hd'); 
    ?>

    <div class="container-fluid">

        <div class="row mt-2 ml-5 ">
            <div class="form-group mt-4">
                <h2>AWB</h2>

            </div>
            <div class="form-group ml-5 mt-4">
                <select name="opciones" id="opciones" class="form-control " data-placeholder="Seleccione estatus" required>
                    <option value="0">Seleccione...</option>
                    <option value="1"> AWB </option>
                    <option value="2"> Referencia </option>
                    <option value="3"> Pendientes </option>
                    <option value="4"> Procesadas </option>
                    <option value="5"> Todo </option>
                </select>
            </div>

            <div class="form-group ml-2 mt-4 h" id="f-ini">
                <input type="date" id="fecha-inicio" name="fecha-inicio" class="form-control">
            </div>
            <div class="form-group ml-2 mt-4 h" id="f-fin">
                <input type="date" id="fecha-fin" name="fecha-fin" class="form-control">
            </div>

            <div class="form-group ml-2 mt-3 h" id="b-buscar">
                <button type="button" class="btn btn-primary btn-circle btn-xl mt-0" id="btn-buscar" onclick="consulta_awb()" style="margin-right: 0px;"><i class="fas fa-search"></i></button>
            </div>


            <div class="form-group ml-2 mt-3 h" id="buscar">
                <div class="search-box">
                    <button class="btn-search"><i class="fas fa-search" onclick="consulta_awb()"></i></button>
                    <input type="text" class="input-search" placeholder="buscar..." id="buscar-manifiesto">
                </div>
            </div>
        </div>
        <hr class="borde-orange mt-0">

    </div>
    <div class="container-fluid">
        <div id="awb_list">

        </div>
    </div>

     <!-- Div para  cargar la modal  de cargar Archivos 02-10-2023-->
    <div id="awb_update">
        
    </div>
    
    <!-- Div para  cargar la modal  de cargar Archivos 12-10-2023-->
    <div id="dui_update">
        
    </div>
    
    <!--- modal para mostrar guias en Express-->
    <div class="modal fade" id="guia_express_modal" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color:white">
                    <h4 class="modal-title" style="color:#F15a29">Crear Guia C807Express </h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form enctype="multipart/form-data" class="guia_ex" id="guia_ex" action="javascript:crear_guia_express();">
                    <div class="modal-body">

                        <input type="text" id="order_number" name="order_number">
                        <input type="text" id="ndepto" name="ndepto">
                        <input type="text" id="nmunic" name="nmunic">
                        <input type="text" id="nservi" name="nservi">

                        <div class="form-group">
                            <label for="">Servicio:</label>
                            <div id="listado_servicios">

                            </div>

                            <label for="">Departamento :</label>
                            <div id="listado_departamentos">

                            </div>

                            <label for="" id="lmunicipio" name="lmunicipio">Municipio:</label>
                            <div id="listado_municipios">

                            </div>

                        </div>

                        <div class="form-group">
                            <label for="direccion">Dirección:</label>
                            <input type="text" class="form-control" id="direccion" placeholder="Introduzca direcci贸n" name="direccion" required>
                            <div class="valid-feedback">Valid.</div>
                            <div class="invalid-feedback">Please fill out this field.</div>
                        </div>


                        <button type="button" class="btn btn-secondary float-md-right" data-dismiss="modal">Cerrar
                        </button>
                        <button type="submit" class="btn btn-primary float-md-right">Guardar</button>

                    </div>
                </form>

            </div>
        </div>
    </div>


    <!--- RETORNO INFO guias en Express-->
    <div class="modal fade" id="info_modal_express" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color:white">
                    <h4 class="modal-title" style="color:#F15a29">Crear Guia C807Express </h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>


                <form action="">
                    <div class="container">
                        <div class="row">
                            <div class="col">
                                <div class="form-group tcenter">
                                    <h4 id="mensaje" name="mensaje"></h4>


                                </div>
                                <div class="form-group tcenter">

                                    <h4 id="recolecta"></h4>

                                </div>
                            </div>
                        </div>
                    </div>

                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary float-md-right" data-dismiss="modal">Cerrar
                    </button>
                </div>
            </div>


        </div>
    </div>

    </div>


    <script>
        $(document).ready(function() {
            $('#warehouse').DataTable({
                //para cambiar el lenguaje a espa帽ol
                "language": {
                    "lengthMenu": "Mostrar _MENU_ registros",
                    "zeroRecords": "No se encontraron resultados",
                    "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                    "infoFiltered": "(filtrado de un total de _MAX_ registros)",
                    "sSearch": "Buscar:",
                    "oPaginate": {
                        "sFirst": "Primero",
                        "sLast": "脷ltimo",
                        "sNext": ">>",
                        "sPrevious": "<<"
                    },
                    "sProcessing": "Procesando...",
                }
            });

            $('.js-check-all').on('click', function() {

                if ($(this).prop('checked')) {
                    $('th input[type="checkbox"]').each(function() {
                        $(this).prop('checked', true);
                        $(this).closest('tr').addClass('active');
                    })
                } else {
                    $('th input[type="checkbox"]').each(function() {
                        $(this).prop('checked', false);
                        $(this).closest('tr').removeClass('active');
                    })
                }

            });

            $('th[scope="row"] input[type="checkbox"]').on('click', function() {
                if ($(this).closest('tr').hasClass('active')) {
                    $(this).closest('tr').removeClass('active');
                } else {
                    $(this).closest('tr').addClass('active');
                }
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

        });

        function hola() {
            alert("here i am");
        }
    </script>


</body>

</html>