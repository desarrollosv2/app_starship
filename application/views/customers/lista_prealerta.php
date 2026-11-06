<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <!-- <link href="https://fonts.googleapis.com/css?family=Montserrat:100,200,300,400" rel="stylesheet"> -->
    <title></title>
  <style>
 
  </style>
</head>

<body>
   <?php $this->load->view('hd'); ?>
    <div class="container m-top">
        <div class="table-responsive">
            <table class="table   table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Id </th>
                        <th>Tracking</th>
                        <th>Courir</th>
                        <th>donde lo compraste</th>
                        <th>Valor</th>
                        <th>descripcion</th>
                        <th>Casillero</th>
                        <th colspan="2">Acciones</th>
                    </tr>
                </thead>
                <tbody id="contenidoLista">
                </tbody>
            </table>

        </div>
    </div>
    </div>

    <script>
    lista_prealerta()
    </script>
</body>

</html>