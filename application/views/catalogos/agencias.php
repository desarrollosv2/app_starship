<?php
//

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://app.c807.com/guia.php/catalogo/agencia',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_SSL_VERIFYPEER => false, //TODO: quitar en produccion
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'Authorization: ' . $_SESSION['token'],

  ),
));

$response = curl_exec($curl);

curl_close($curl);
$agencia = json_decode($response, true);
//echo $response;
//
?>



<select name="agencia" id="agencia" class="form-control chosen">
  <option value="0">Seleccione agencia</option>
  <?php foreach ($agencia as $row) : ?>

    <option value="<?php echo $row['id']; ?>"><?php echo  $row['nombre']; ?></option>

  <?php endforeach ?>

</select>