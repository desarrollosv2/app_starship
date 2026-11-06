<style>
.modal-header {
    background-color: white;
    color: #F15A29;
}
</style>

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
                    <form>
                        <div class="container">
                            <div class="row">
                                <div class="col-md-6">

                                    <div class="form-group">
                                        <label for="numero_tracking" class="col-form-label">Numero de tracking</label>
                                        <input type="text" class="form-control" id="numero_tracking">
                                    </div>

                                    <div class="form-group">
                                        <label for="tienda_compra" class="col-form-label">Tienda donde compraste</label>
                                        <input type="text" class="form-control" id="tienda_compra">
                                    </div>
                                    <div class="form-group">
                                        <label for="consignatario" class="col-form-label">Consignatario</label>
                                        <select class="form-control" id="consignatario">
                                            <option value="+47">Consignartio uno</option>
                                            <option value="+46">Consignatario dos</option>
                                            <option value="+45">Consignatario tres)</option>
                                        </select>
                                    </div>


                                </div>

                                <div class="col-md-6">

                                    

                                    <div class="form-group">
                                        <label for="valor_paquete" class="col-form-label">Valor del paquete
                                            (USD)</label>
                                        <input type="text" class="form-control" id="valor_paquete">
                                    </div>

                                    <div class="form-group">
                                        <label for="comment">Describe tu paquete:</label>
                                        <textarea class="form-control" rows="3" id="comment"></textarea>
                                    </div>

                                </div>
                                <div class="file-upload-wrapper">
                                    <input type="file" id="input-file-now" class="file-upload" />
                                </div>

                            </div>
                        </div>



                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-modal" data-dismiss="modal"> <i class="fa fa-times"
                            aria-hidden="true"></i> Cancelar</button>
                    <button type="button" class="btn-modal"><i class="fa fa-check" aria-hidden="true"></i>
                        Aceptar</button>
                </div>
            </div>
        </div>
    </div>