<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link rel="stylesheet" type="text/css" href="<?php echo base_url('public/css/newstyle.css') ?>"> -->
   <!-- Bootstrap CSS -->
   <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <title></title>
    <style>
        body {
            background-color: #f4f5f7;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

       
        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .header h1 {
            font-size: 2.5rem;
            color: #F8F9FC;
            margin-bottom: 10px;
        }

        .form-control,
        .btn {
            border-radius: 30px;
        }

        .btn-primary {
            background-color: #F8F9FC;
            border-color: #F8F9FC;
        }

        .btn-primary:hover {
            background-color: #375a7f;
            border-color: #375a7f;
        }

        .table {
            margin-top: 20px;
        }

        .table-title {
            font-size: 1.5rem;
            color: #F8F9FC;
            margin-bottom: 10px;
            text-align: center;
        }

        .table thead {
            background-color: #F8F9FC;
            color: #ffffff;
        }

        .table th,
        .table td {
            vertical-align: middle;
        }

        .btn-warning,
        .btn-danger {
            border-radius: 20px;
        }

        .btn-warning {
            background-color: #f6c23e;
            border-color: #f6c23e;
        }

        .btn-danger {
            background-color: #e74a3b;
            border-color: #e74a3b;
        }

        .text-muted {
            color: #858796;
        }

        .search-and-filter {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 5px;
            background-color: #ffffff;
            border: 1px solid #e3e6f0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .search-and-filter .form-inline {
            display: flex;
            align-items: center;
            width: 100%;
        }

        .search-and-filter .form-inline .date-filters {
            display: flex;
            align-items: center;
            margin-right: 15px;
        }

        .search-and-filter .form-inline .date-filters label {
            margin-right: 5px;
        }

        .search-and-filter .form-inline .date-filters input {
            margin-right: 10px;
        }

        .search-and-filter .form-inline .btn {
            margin-left: 10px;
        }

        .search-and-filter .form-inline .search-input {
            margin-left: auto;
            width: 300px;
        }

        .date-filters input {
            width: 120px;
        }

        .search-input input {
            width: 100%;
        }

        .h {
            display: none;
        }
    </style>
</head>

<body>
    <?php // $this->load->view('hd'); 
    ?>
<div class="container">
        <div class="header">
            <h1>Gestión de Empleados</h1>
            <p class="text-muted">Administra tus empleados de manera eficiente y elegante.</p>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addEmployeeModal">
                <i class="fas fa-plus"></i> Añadir Empleado
            </button>
        </div>
        
        <!-- Modal para añadir empleados -->
        <div class="modal fade" id="addEmployeeModal" tabindex="-1" aria-labelledby="addEmployeeModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addEmployeeModalLabel">Añadir Nuevo Empleado</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="employeeForm">
                            <div class="form-group">
                                <label for="name">Nombre</label>
                                <input type="text" class="form-control" id="name" placeholder="Nombre" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" placeholder="Email" required>
                            </div>
                            <div class="form-group">
                                <label for="position">Puesto</label>
                                <input type="text" class="form-control" id="position" placeholder="Puesto" required>
                            </div>
                            <div class="form-group">
                                <label for="salary">Salario</label>
                                <input type="number" class="form-control" id="salary" placeholder="Salario" required>
                            </div>
                            <div class="form-group">
                                <label for="gender">Género</label>
                                <select class="form-control" id="gender" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="Hombre">Hombre</option>
                                    <option value="Mujer">Mujer</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Guardar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros y barra de búsqueda -->
        <div class="search-and-filter">
            <form class="form-inline">
                <div class="date-filters">
                    <label for="startDate">Desde:</label>
                    <input type="date" class="form-control" id="startDate">
                    <label for="endDate">Hasta:</label>
                    <input type="date" class="form-control" id="endDate">
                </div>
                <button type="button" class="btn btn-secondary" onclick="filterEmployees()">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
                <div class="search-input">
                    <input type="text" class="form-control" id="searchInput" placeholder="Buscar empleado...">
                </div>
            </form>
        </div>

        <!-- Título de la tabla -->
        <div class="table-title">Lista de Empleados</div>
        
        <!-- Tabla de empleados -->
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Email</th>
                    <th scope="col">Puesto</th>
                    <th scope="col">Salario</th>
                    <th scope="col">Género</th>
                    <th scope="col">Acciones</th>
                </tr>
            </thead>
            <tbody id="employeeTableBody">
                <tr>
                    <td>1</td>
                    <td>Juan Pérez</td>
                    <td>juan.perez@empresa.com</td>
                    <td>Gerente</td>
                    <td>$5,000</td>
                    <td>Hombre</td>
                    <td>
                        <button class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Editar</button>
                        <button class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i> Eliminar</button>
                    </td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>María López</td>
                    <td>maria.lopez@empresa.com</td>
                    <td>Desarrolladora</td>
                    <td>$4,000</td>
                    <td>Mujer</td>
                    <td>
                        <button class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Editar</button>
                        <button class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i> Eliminar</button>
                    </td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>Carlos Ruiz</td>
                    <td>carlos.ruiz@empresa.com</td>
                    <td>Diseñador</td>
                    <td>$3,500</td>
                    <td>Hombre</td>
                    <td>
                        <button class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Editar</button>
                        <button class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i> Eliminar</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

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

    <!-- Div para cargar modal de Referencias -->
    <div id="referencia_update">

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
                            <label for="direccion">Direcci��n:</label>
                            <input type="text" class="form-control" id="direccion" placeholder="Introduzca dirección" name="direccion" required>
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
    <div class="container">
        <div class="alert alert-primary alert-dismissible fade show hide" id="msg" role="alert">
            <strong>La Carga se ha Realizado Correctamente!</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>

    <div class="modal fade" id="upfileModal" role="dialog" data-backdrop="static">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header bg-white">
                    <h5 class="modal-title" id="exampleModalLabel">Cargar Referencia</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="row-fluid" id="messagefile"></div>
                <form enctype="multipart/form-data" method="post" class="up_ref" id="up_ref" action="javascript:upload_file_referencia()">
                    <input type="hidden" id="idpreclasificacion" name="idpreclasificacion">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="row">
                                <input type="file" name="file" id="file" class="w-50" required accept=".xls" />
                            </div>
                        </div>
                    </div>

                    <br>

                    <div class="modal-footer">
                        <button type="submit" id="enviar" class="btn btn-primary">
                            <i class="fa fa-check" aria-hidden="true"> Aplicar</i></button>

                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fa fa-close" aria-hidden="true"> Cancelar</i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="alert alert-primary alert-dismissible fade show hide" id="msg" role="alert">
            <strong>La Carga se ha Realizado Correctamente!</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>

    <div class="modal fade" id="upfileSoModal" role="dialog" data-backdrop="static">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header bg-white">
                    <h5 class="modal-title" id="exampleModalLabel">Cargar Paquetes Sobrantes</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="row-fluid" id="messagefile"></div>
                <form enctype="multipart/form-data" method="post" class="up_sobras" id="up_sobras" action="javascript:upload_file_sobrante()">
                    <input type="text" id="idpreclasificacion" name="idpreclasificacion" hidden>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="row">
                                <input type="file" name="file" id="file" class="w-50" required accept=".xls" />
                            </div>
                        </div>
                    </div>

                    <br>

                    <div class="modal-footer">
                        <button type="submit" id="enviar" class="btn btn-primary">
                            <i class="fa fa-check" aria-hidden="true"> </i> Aplicar</button>

                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fa fa-close" aria-hidden="true"> </i> Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <script>
        $(document).ready(function() {
            $('#warehouse').DataTable({
                //para cambiar el lenguaje a español
                "language": {
                    "lengthMenu": "Mostrar _MENU_ registros",
                    "zeroRecords": "No se encontraron resultados",
                    "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                    "infoFiltered": "(filtrado de un total de _MAX_ registros)",
                    "sSearch": "Buscar:",
                    "oPaginate": {
                        "sFirst": "Primero",
                        "sLast": "Último",
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

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>

</html>