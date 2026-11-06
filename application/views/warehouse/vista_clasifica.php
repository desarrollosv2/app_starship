<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/css/alertify.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('public/css/estilos.css') ?>">


    
    <style>
        :root {
            --primary: #FE4F00 !important;
            --primary-light: rgba(254, 79, 0, 0.08) !important;
            --secondary: #D94200 !important;
            --success: #28A745 !important;
            --dark: #212529 !important;
            --light: #FFFFFF !important;
            --gray: #6C757D !important;
        }

       

        .cerrar_div {
            width: 400px;
            height: 100px;
            background: lightblue;
            display: block
        }

        .boton {
            float: right;
            background: #ef7f1b;
            color: white;
            width: 30px;
            height: 25px;
            border-radius: 100%;
            border: 1px solid white;
        }

        .boton:hover {
            border: 1px solid #ef7f1b;
            color: yellow;
        }

        /* Nuevos estilos para el input de búsqueda */
        .input-group-custom {
            display: flex;
            max-width: 600px;
            margin: 0 auto;
            box-shadow: 0 5px 15px rgba(254, 79, 0, 0.15);
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s;
        }

        .input-group-custom:focus-within {
            box-shadow: 0 5px 20px rgba(254, 79, 0, 0.25);
            transform: translateY(-2px);
        }

        .search-input-custom {
            flex: 1;
            padding: 1rem 1.5rem;
            border: none;
            font-size: 1rem;
            outline: none;
            background: var(--light);
        }

        .search-button-custom {
            background: linear-gradient(to right, var(--primary), var(--secondary));
            color: white;
            border: none;
            padding: 0 2rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .search-button-custom:hover {
            background: var(--secondary);
        }

        @media (max-width: 768px) {
            .input-group-custom {
                flex-direction: column;
                box-shadow: none;
            }

            .search-input-custom {
                border-radius: 8px 8px 0 0;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            }

            .search-button-custom {
                border-radius: 0 0 8px 8px;
                padding: 1rem;
                justify-content: center;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card text-center">
            <div class="card-header">
                <h4 class="text-muted">Verificar Paquetes</h4>
            </div>
            <div class="card-body">
                <input type="hidden" id="id-master-awb">
                <input type="hidden" id="manifiesto-number" class="form-control" />
                <div class="row p-4 m-3" id="master" name="master" style="background-color:#F15a29;display:none">
                    <div class="col-md-2">
                    </div>
                    <div class="col-md-4" style="float:left;margin:5px">
                        <input type="text" id="guia_master" name="guia_master" placeholder="Buscar..." class="form-control" />
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn btn-secondary pdf" onclick="pdf_clasificado()">Generar PDF</button>
                    </div>
                    <div class="col-md-1">
                    </div>
                    <span class="boton text-center" onclick="cerrar_div()" style="display:none;cursor: pointer">X</span>
                </div>
                <br>
                <form enctype="multipart/form-data" class="qry_warehouse forminline" id="qry_warehouse">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="input-group-custom">
                                <input type="search" id="search" name="search" placeholder="Buscar..." class="search-input-custom" />
                                <button type="button" class="search-button-custom" onclick="consulta_warehouse()">
                                    <i class="fa fa-search"></i> Buscar
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="row pt-3">
                        <div class="col-md-12">
                            <div class="box">
                               
                                <div class="container-1">
                                    <input type="text" id="referencia" name="referencia" class="form-control" style="font-weight: bold; font-size:75px; text-align:left" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="casillero" name="casillero" class="form-control" />
                </form>
                <br>
                <div id="lista_guia_master"></div>
            </div>
            <div class="card-footer text-muted"></div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $("#search").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("#myTable tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
            
            //alert("Elmer here");
            
            $("#search").on('keyup', function(e) {
                var keycode = e.keyCode || e.which;
                var warehouse = $("#search").val();
                var guiamaster = $("#guia_master").val();
                var st = $("#estatus").val();
                if (keycode == 13) {
                    //alert("init");
                    if ($("#search").val().length <= 0) {
                       // alert("search");
                        alertify.set("notifier", "position", "top-right");
                        alertify.warning("!Es necesario ingresar número de guía para poder continuar!");
                    } else {
                       // alert("update");
                         update_warehouse(warehouse, st);

                    }
                }
            });
        });
    </script>

    <script>
        verifica();
    </script>
</body>
</html>

