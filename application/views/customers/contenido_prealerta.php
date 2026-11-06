
 <style>
.modal-header {
    background-color: white;
    color: #F15A29;
}
 </style>
 <nav class="navbar navbar-expand-lg navbar-light bg-light">
     <button type="button" onclick="modal_prealerta(0);" class="btn-modal" id="btn_prealert">
         <i class="fa fa-plus" aria-hidden="true"></i> Crear </button>
     </div>
 </nav>
 

 <div class="modal fade" id="prealerta_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true" data-backdrop="static">
     <div class="modal-dialog modal-lg" role="document">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title" id="exampleModalLabel">CREAR PRE-ALERTA</h5>
                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true">&times;</span>
                 </button>
             </div>
             <div class="modal-body">
                 <form enctype="multipart/form-data" class="form_prealerta" id="form_prealerta" name="form_prealerta"
                     method="post">

                     <div class="container">
                         <div class="row">
                             <div class="col-md-6">
                                 <div class="form-group">
                                     <label for="numero_tracking" class="col-form-label">Numero de tracking</label>

                                     <input type="text" class="form-control" id="numero_tracking"
                                         name="numero_tracking">
                                 </div>
                                 <div class="form-group">
                                     <label for="tienda_compra" class="col-form-label">Tienda donde compraste</label>
                                     <input type="text" class="form-control" id="tienda_compra" name="tienda_compra">
                                 </div>
                                 <div class="form-group">
                                     <main id="uploadfile_modal_style" class="main_full">
                                         <div class="container">
                                             <div class="panel">
                                                 <div class="button_outer">
                                                     <div class="btn_upload">
                                                         <input type="file" id="upload_file" name="upload_file">
                                                         Upload Image
                                                     </div>
                                                     <div class="processing_bar"></div>
                                                     <div class="success_box"></div>
                                                 </div>
                                             </div>
                                             <div class="error_msg"></div>
                                             <div class="uploaded_file_view" id="uploaded_view">
                                                 <span class="file_remove">X</span>
                                             </div>
                                         </div>
                                     </main>
                                 </div>
                             </div>
                             <div class="col-md-6">
                                 <div class="form-group">
                                     <label for="comp_courier" class="col-form-label">Compania courier</label>
                                     <?php $this->load->view("catalogos/courier"); ?>
                                 </div>

                                 <div class="form-group">
                                     <label for="valor_paquete" class="col-form-label">Valor del paquete
                                         (USD)</label>
                                     <input type="text" class="form-control" id="valor_paquete" name="valor_paquete">
                                 </div>

                                 <div class="form-group">
                                     <label for="comment">Describe tu paquete:</label>
                                     <textarea class="form-control" rows="3" id="comment" name="comment"></textarea>
                                 </div>
                             </div>

                         </div>
                     </div>
                     <input type="hidden" id="id_prealerts" name="id_prealerts">
                 </form>
             </div>
             <div class="modal-footer">
                 <button type="button" class="btn-modal" data-dismiss="modal"> <i class="fa fa-times"
                         aria-hidden="true"></i> Cancelar</button>
                 <button type="button" class="btn-modal" id="pre_alerta" onclick="guardar_prealerta()"><i
                         class="fa fa-check" aria-hidden="true"></i>
                     Aceptar</button>

             </div>
         </div>
     </div>
 </div>
 <script type="text/javascript">
$(document).ready(function() {
    var btnUpload = $("#upload_file"),
        btnOuter = $(".button_outer");
    btnUpload.on("change", function(e) {
        var ext = btnUpload.val().split('.').pop().toLowerCase();
        if ($.inArray(ext, ['gif', 'png', 'jpg', 'jpeg']) == -1) {
            

            cargar(ext);

            

        } else {
            $(".error_msg").text("");
            btnOuter.addClass("file_uploading");
            setTimeout(function() {
                btnOuter.addClass("file_uploaded");
            }, 3000);
            var uploadedFile = URL.createObjectURL(e.target.files[0]);
            setTimeout(function() {
                $("#uploaded_view").append('<img src="' + uploadedFile + '" />').addClass(
                    "show");
            }, 3500);
        }
    });

    $(".file_remove").on("click", function(e) {
        $("#uploaded_view").removeClass("show");
        $("#uploaded_view").find("img").remove();
        btnOuter.removeClass("file_uploading");
        btnOuter.removeClass("file_uploaded");
    });

    function cargar(ext) {

        var URLdomain = window.location.host;

        if (ext == 'docx') {
            $(".error_msg").text("");
            btnOuter.addClass("file_uploading");
            setTimeout(function() {
                btnOuter.addClass("file_uploaded");
            }, 3000);
            setTimeout(function() {
                $("#uploaded_view").append(
                    "show");
            }, 3500);

        } else if (ext == 'pdf') {
            $(".error_msg").text("");
            btnOuter.addClass("file_uploading");
            setTimeout(function() {
                btnOuter.addClass("file_uploaded");
            }, 3000);
            setTimeout(function() {
                $("#uploaded_view").append(
                    '<img src="https://'+URLdomain+'/app_starship/public/img/pdf.png" alt="" width="150" height="150">').addClass(
                    "show");
            }, 3500);

        } else if ($.inArray(ext, ['xlsx', 'xls']) == 1)    {
            $(".error_msg").text("");
            btnOuter.addClass("file_uploading");
            setTimeout(function() {
                btnOuter.addClass("file_uploaded");
            }, 3000);
            setTimeout(function() {
                $("#uploaded_view").append(
                    '<img src="https://'+URLdomain+'/app_starship/public/img/excel.png" alt="" width="150" height="150">').addClass(
                    "show");
            }, 3500);

        }else{
            $(".error_msg").text("Not an Image...");
        }

    }
});
 </script>