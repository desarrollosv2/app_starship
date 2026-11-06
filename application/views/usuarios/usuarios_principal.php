<div class="container-fluid">
    <?php
    //var_dump($_SESSION['id_empresa']);
    ?>
    <div>

        <div class="card shadow-sm mb-4">

            <div class="card-header bg-white text-black">
                <div class="row align-items-center">

                    <div class="col-md-6">
                        <h4 class="mb-0 font-weight-bold">
                            <i class="fa fa-users"></i>
                            Usuarios del Sistema
                        </h4>
                    </div>

                    <div class="col-md-6 text-md-right mt-3 mt-md-0">
                        <?php if (isset($acciones_usuario['c']) && $acciones_usuario['c'] == 1): ?>
                            <button type="button" class="btn text-white px-4" style="background-color:#f05a28;border-color:#f05a28;" onclick="agregar_nuevo_usuario_sistema();">
                                <i class="fa fa-plus"></i>
                                Agregar Usuarios
                            </button>
                        <?php endif; ?>
                    </div>

                </div>
            </div>

        </div>

    </div>
    <div id="div_dashboard_usuarios"></div>
    <div id="div_tbl_usuarios_sistema"></div>

</div>

<div class="modal fade" id="Modal_Usuario" tabindex="-1" role="dialog" aria-labelledby="Modal_UsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
            <div class="modal-header" style="background-color: #F15a29; border-radius: 12px 12px 0 0; border-bottom: 1px solid #eaedf1;">
                <h5 class="modal-title font-weight-bold" id="modalNuevoUsuarioLabel">
                    <i class="fa fa-users mr-2" style="color: #f8f9fa;"></i> <span id="titulo_modal_usuario_sistema"></span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="div_gestion_usuarios_sistema_form">

            </div>
            <div class="modal-footer" style="border-top:1px solid #eaedf1;background-color:#f8f9fa;border-radius:0 0 12px 12px;">
                <button type="button" class="btn btn-secondary px-4 border" data-dismiss="modal">
                    Cancelar
                </button>

                <button type="button" class="btn text-white px-4" style="background-color:#f05a28;border-color:#f05a28;" onclick="guardar_usuario_sistema()">
                    <i class="fa fa-save mr-1"></i>
                    <span>Guardar usuario</span>
                </button>
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