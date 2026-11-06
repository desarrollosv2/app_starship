<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <?php $this->load->view('hd'); ?>
    <?php  foreach ($info_cliente as $row) {
        echo $row->casillero;
    }?>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h5>Informacion de Cuenta </h5>
            </div>

            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="card-body">
                            <div class="form-group">
                                <label for=""> Casillero</label>
                                <input class="form-control col-sm-4" type="text" placeholder="Casillero"
                                    value="<?php echo $info_cliente->casillero?>" readonly>
                            </div>
                            <div class="form-group">
                                <label for="">Nombre</label>
                                <input class="form-control col-sm-12" type="text" placeholder="Nombre"
                                    value="<?php echo $info_cliente->nombre?>" readonly>
                            </div>
                            <div class="form-group">
                                <label for=""> Correo Electronico</label>
                                <input class="form-control col-sm-12" type="text" placeholder="Correo Electronico"
                                    value="<?php echo $info_cliente->correo?>" readonly>
                            </div>
                            <div class="form-group">
                                <label for=""> Sucursal de Entrega</label>

                                <input class="form-control col-sm-12" type="text" readonly id="sentrega"
                                    name="sentrega"  value="<?php echo $info_cliente->nombre_sucursal ?>" >

                            </div>

                            <div class="form-group">

                                <input type="hidden" id="id_sucursal" name="id_sucursal"
                                    value="<?php echo $info_cliente->id_sucursal?>">


                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group text-center">
                        <br>
                            <label for="" > Dirección Miami</label>
                            
                              <p>  7950 NW 77 ST STE 4, Suite SAL54010
                                Miami, FL 33195-2133</p>
                           
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
    $(document).ready(function() {
        var id_c = $("#id_sucursal").val();
        console.log(id_c);
    });
    </script>
</body>

</html>