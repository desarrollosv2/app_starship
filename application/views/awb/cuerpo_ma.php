<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <!-- Estos primero -->
    <!--<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    
    
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">

    
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script> -->

    <style>
        #awbTable td,
        #awbTable th {
            padding-top: 6px !important;
            padding-bottom: 6px !important;
            vertical-align: middle !important;
            font-size: 0.9rem;
            line-height: 1.2 !important;
        }

        #awbTable .dropdown-toggle.btn {
            padding: 4px 10px !important;
            font-size: 0.85rem;
            line-height: 1 !important;
            height: auto !important;
            border-radius: 15px !important;
        }

        /* Botón */
        .btn-outline-primary.btn-sm.dropdown-toggle {
            border-radius: 12px;
            transition: all .2s ease;
        }

        .btn-outline-primary.btn-sm.dropdown-toggle:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, .08);
        }

        /* Menú */
        .nice-dropdown {
            border: 0 !important;
            border-radius: 14px;
            box-shadow: 0 20px 50px rgba(18, 38, 63, .18);
            padding: .5rem;
            min-width: 16rem;
            animation: dd-fade .14s ease-out;
        }

        /* Ítems */
        .nice-item {
            border-radius: 10px;
            padding: .55rem .75rem;
            transition: background .15s ease, transform .05s ease;
        }

        .nice-item:hover {
            background: rgba(13, 110, 253, .08);
            /* azul suave */
        }

        .nice-item:active {
            transform: scale(.99);
        }

        /* Divider */
        .dropdown-divider {
            margin: .4rem 0;
            border-top: 1px dashed rgba(0, 0, 0, .12);
        }

        /* Animación sutil */
        @keyframes dd-fade {
            from {
                opacity: 0;
                transform: translateY(-4px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Modo oscuro opcional */
        @media (prefers-color-scheme: dark) {
            .nice-dropdown {
                background: #1f2330;
                box-shadow: 0 20px 50px rgba(0, 0, 0, .45);
            }

            .nice-item {
                color: #e7e9ee;
            }

            .nice-item:hover {
                background: rgba(255, 255, 255, .08);
            }

            .dropdown-divider {
                border-top-color: rgba(255, 255, 255, .15);
            }
        }
    </style>
</head>

<body>

    <div class="container-fluid m-top ">
        <div class="table-responsive" style="min-height: 80vh;">
            <table class="table" id="awbTable">
                <thead style="border:none;">

                    <tr>
                        <th scope="col"> # </td>
                        <th scope="col">AWB (MIA-SAL)</td>
                        <th scope="col">AWB (CN-MIA)</td>
                        <th scope="col">Carga de documentos</td>
                        <th scope="col">Fecha</td>
                        <th scope="col">Recibido Miami</td>
                        <th scope="col">Descripcion</td>
                        <th scope="col">Proveedor Ultima Milla</td>
                        <th scope="col">Número Guias</td>
                        <th scope="col">Acciones</th>


                    </tr>
                </thead>
                <tbody class="mt-3">
                    <?php
                    //  var_dump($lista);
                    if (isset($lista)) {
                        $c = 1;
                        foreach ($lista as $row) { ?>
                            <tr>
                                <td><?php echo $row->idpreclasificacion; ?></td>
                                <td><?php echo $row->manifiesto; ?></td>
                                <td><?php echo $row->manifiesto_tempo; ?></td>
                                <td scope="row">

                                    <a href="#" class="btn btn-default btn-sm" Onclick="detalle_documentos(<?php echo $row->idpreclasificacion; ?>)" title="Ver detalle documentos cargados" style="margin:0px;">
                                        <progress id="file" value="<?= $row->porcentaje_docs ?>" max="100"> <?= $row->porcentaje_docs ?>% </progress> <?= $row->porcentaje_docs ?>%
                                    </a>


                                </td>
                                <td><?php echo $row->fecha; ?></td>
                                <td><?php echo $row->fecha_arribo_miami; ?></td>
                                <td><?php echo $row->descripcion; ?></td>
                                <td><?php echo $row->nombre; ?></td>
                                <td><?php echo $row->n_guias; ?>
                                <td>



                                    <div class="dropdown">
                                        <button class="btn btn-outline-primary btn-sm dropdown-toggle shadow-sm px-3"
                                            type="button" id="accionesDropdown"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-cogs mr-2"></i> Acciones
                                        </button>

                                        <div class="dropdown-menu dropdown-menu-right nice-dropdown" aria-labelledby="accionesDropdown">
                                            <?php if ($_SESSION["client_code"] == 107) { ?>
                                                <a class="dropdown-item d-flex align-items-center nice-item"
                                                    href="#"
                                                    onclick="lista_warehouse(<?php echo $row->idpreclasificacion; ?>, '<?php echo $row->manifiesto; ?>', 0)">
                                                    <i class="fas fa-search text-warning mr-2"></i> Ver Detalle
                                                </a>
                                            <?php } else { ?>
                                                <a class="dropdown-item d-flex align-items-center nice-item" href="#" onclick="cargar_archivo2(<?php echo $row->idpreclasificacion; ?>)">
                                                    <i class="fas fa-id-card text-secondary mr-2"></i> Actualizar Dui
                                                </a>
                                                <a class="dropdown-item d-flex align-items-center nice-item" href="#" onclick="cargar_archivo1(<?php echo $row->idpreclasificacion; ?>)">
                                                    <i class="fas fa-upload text-secondary mr-2"></i> Cargar Datos
                                                </a>

                                                <a class="dropdown-item d-flex align-items-center nice-item" href="#" onclick="lista_warehouse(<?php echo $row->idpreclasificacion; ?>, '<?php echo $row->manifiesto; ?>', 0)">
                                                    <i class="fas fa-search text-warning mr-2"></i> Ver Detalle
                                                </a>

                                                <div class="dropdown-divider"></div>

                                                <a class="dropdown-item d-flex align-items-center nice-item" href="#" onclick="uploadCostosDuca(<?php echo $row->idpreclasificacion; ?>)">
                                                    <i class="fas fa-dollar-sign text-success mr-2"></i> Carga duca y asignación costos
                                                </a>
                                                <a class="dropdown-item d-flex align-items-center nice-item" href="#" onclick="cargarDocumentosManifiesto(<?php echo $row->idpreclasificacion; ?>)">
                                                    <i class="fa fa-cloud-upload text-secondary mr-2"></i> Cargar documentos manifiesto
                                                </a>
                                                <a class="dropdown-item d-flex align-items-center nice-item" href="#" onclick="cargar_referencia(<?php echo $row->idpreclasificacion; ?>)">
                                                    <i class="fas fa-folder-open text-secondary mr-2"></i> Cargar Referencia
                                                </a>
                                                <a class="dropdown-item d-flex align-items-center nice-item" href="#" onclick="reporte_manifiesto(<?php echo $row->idpreclasificacion; ?>)">
                                                    <i class="fas fa-file text-secondary mr-2"></i> Reporte de Guías
                                                </a>
                                                <a class="dropdown-item d-flex align-items-center nice-item" href="#" onclick="clasifica(<?php echo $row->idpreclasificacion; ?>, '<?php echo $row->manifiesto; ?>')">
                                                    <i class="fas fa-check text-primary mr-2"></i> Verificar Paquetes
                                                </a>
                                                <a class="dropdown-item d-flex align-items-center nice-item" href="#" onclick="traspaso_automatico(<?php echo $row->idpreclasificacion; ?>)">
                                                    <i class="fas fa-share-alt text-primary mr-2"></i> Crear Manifiesto
                                                </a>
                                                <a class="dropdown-item d-flex align-items-center nice-item" href="#" onclick="lista_sobrantes(<?php echo $row->idpreclasificacion; ?>)">
                                                    <i class="fas fa-angle-double-up text-success mr-2"></i> Paquetes Sobrantes
                                                </a>
                                                <a class="dropdown-item d-flex align-items-center nice-item" href="#" onclick="agregar_manifiestoxref(<?php echo $row->idpreclasificacion; ?>)">
                                                    <i class="fas fa-plus text-secondary mr-2"></i> Asignar Referencia
                                                </a>
                                                <a class="dropdown-item d-flex align-items-center nice-item" href="#" onclick="agregar_costosxref(<?php echo $row->idpreclasificacion; ?>)">
                                                    <i class="fas fa-dollar-sign text-success mr-2"></i> Asignar Costos
                                                </a>

                                                <a class="dropdown-item d-flex align-items-center nice-item" href="#" onclick="bag_guides(<?php echo $row->idpreclasificacion; ?>)">
                                                    <i class="fas fa-cog text-dark mr-2"></i> Crear Guías
                                                </a>
                                                <a class="dropdown-item d-flex align-items-center nice-item" href="#" onclick="TraslateMaster(<?php echo $row->idpreclasificacion; ?>)">
                                                    <i class="fas fa-globe text-dark mr-2"></i> Cambiar Idioma
                                                </a>

                                                <a class="dropdown-item d-flex align-items-center nice-item" href="#" onclick="modificar_manifiesto(<?php echo $row->idpreclasificacion; ?>)">
                                                    <i class="fas fa-pencil-alt text-dark mr-2"></i> Modificar campos manifiesto
                                                </a>
                                                <a class="dropdown-item d-flex align-items-center nice-item" href="#" onclick="arribo_modal(<?php echo $row->idpreclasificacion; ?>)">
                                                    <i class="fas fa-pencil-alt text-dark mr-2"></i> Agregar fecha de arribo a Miami
                                                </a>

                                                <a class="dropdown-item d-flex align-items-center nice-item" href="#" onclick="openChangeCountry(<?php echo $row->idpreclasificacion; ?>)">
                                                    <i class="fa fa-pencil-alt text-secondary mr-2"></i> Cambiar pais manifiesto
                                                </a>
                                                <a class="dropdown-item d-flex align-items-center nice-item" href="#" onclick="set_proveedorlm(<?php echo $row->idpreclasificacion; ?>)">
                                                    <i class="fa fa-pencil-alt text-secondary mr-2"></i> Asignar proveedor ultima milla
                                                </a>

                                                <?php if ($_SESSION["user_id"] == 11200 || $_SESSION["user_id"] == 33540 || $_SESSION["user_id"] == 39102 || $_SESSION["user_id"] == 2) { ?>

                                                    <a class="dropdown-item d-flex align-items-center nice-item" href="#" onclick="set_master_temu(<?php echo $row->idpreclasificacion; ?>)">
                                                        <i class="fa fa-pencil-alt text-secondary mr-2"></i> Asignar master TEMU
                                                    </a>

                                                <?php } ?>

                                                <a class="dropdown-item d-flex align-items-center nice-item" href="#" onclick="modalCargarClient(<?php echo $row->idpreclasificacion; ?>)">
                                                    <i class="fa fa-pencil-alt text-secondary mr-2"></i> Modificar cliente
                                                </a>


                                            <?php } ?>
                                        </div>
                                    </div>


                                </td>
                            </tr>
                    <?php
                            $c++;
                        }
                    }
                    ?>
                </tbody>

            </table>
        </div>

    </div>
    <script>
        $(document).ready(function() {
            if ($.fn.DataTable.isDataTable("#awbTable")) {
                $("#awbTable").DataTable().destroy();
            }

            $("#awbTable").DataTable({
                paging: true,
                pageLength: 10,
                searching: true,
                ordering: false,
                autoWidth: false,
                language: {
                    lengthMenu: "Mostrar _MENU_ registros",
                    zeroRecords: "No se encontraron resultados",
                    info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                    infoEmpty: "No hay registros disponibles",
                    infoFiltered: "(filtrado de _MAX_ registros totales)",
                    search: "Buscar:",
                    paginate: {
                        next: ">>",
                        previous: "<<",
                    },
                }
            });
        });
    </script>

</body>

</html>