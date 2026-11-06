<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>php2</title>
</head>
<body>
	<style>
        	#someHtml{
        	}
			.containerj{
				display: flex;
			}
			.borderj{
				width: 20rem;
				border: 2px solid black;
			}
			
			p{
				font-family: Arial;
				font-size: 11px;
				padding: 0rem;
				height: 0.8rem;
			}
			
			.imgpdf{
			  width: 20rem;
			  height: 3rem;
			}
			.p_labe{
				font-size: 1rem;
    			margin: 0rem 1rem;
			}
			.my4{
				width: 100%;
    			margin: 0rem 0rem 0.7rem 0.7rem!important;
			}
			.my5{
				margin: 0.5rem 0rem;
				text-align: center;
				width: 100%;
			}
			.code{
				margin: 0rem 0rem;
			}
			.code img{
			   margin: 1px 0px 0px 2px;
               width: 311px!important;
               height: 3rem!important;
            }
			.rowinf{
				margin: 0rem 1rem 1rem 1rem;
			}
			.borde-l{
				border-left: 1px solid;
			}
			.my3{
				text-align: center;
    			width: 100%;
			}
			/*body{
				font-family: Arial;
				font-weight: bold;
				margin: 0px;
			}*/
    </style>
<?php
		var_dump($data);
		//var_dump($data[0]->consignee);

		//$this->datos['dconversacion'] = $this->WarehouseModel->consulta_conversacion($id);
		//var_dump(  $this->datos['guias']);
	//	$tracking_number = $data[0]->tracking_number;
	//	$url = "https://app.c807.com/guia.php/reporte/guias?guia=".$tracking_number;

	//	$curl = curl_init();
		//token
		//77bd02f856239cfc5d8de2a8415b4a5a7c0d8d2e
		//d3a22ca0cf04668a450a337a31ede0d9eeac64e3
	//	curl_setopt_array($curl, array(
	//	CURLOPT_URL => $url,
	//	CURLOPT_RETURNTRANSFER => true,
	//	CURLOPT_ENCODING => '',
	//	CURLOPT_MAXREDIRS => 10,
	//	CURLOPT_TIMEOUT => 0,
	//	CURLOPT_FOLLOWLOCATION => true,
	//	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	//	CURLOPT_CUSTOMREQUEST => 'GET',
	//	CURLOPT_HTTPHEADER => array(
	//		'Authorization: d3a22ca0cf04668a450a337a31ede0d9eeac64e3'
	//		),
	//	));

	//	$rsp = curl_exec($curl);
	//	curl_close($curl);
	//	$items = json_decode($rsp, true);
		
	//	$servicio = $items[0]['servicio'];
	//	$tipo_entrega = $items[0]['tipo_entrega'];
		
		$ur=base_url();
		

		$imag ='<img class="imgpdf" src="'.$ur.'Barcode/barcode.php?text='.$data[0]->tracking_number.'&sizefactor=2&size=100&codetype=Code128&print=false"/>';
?>
        <div id="contentToPrint">
	    	<div class="borderj">
			    <div class="containerj">
			        <div class="code">
			        <?php echo $imag; ?>
			        </div>
			    </div>
			    <div class="conatinerj">
			    	<div class="rowinf">
			    		<p>CODIGO CTF</p> 
			    		<p>Teléfono: </p>
			    		<p>DESTINO </p>
			    		<p>Nombre: <?php echo $data[0]->consignee;?></p>
			    		<p>Contactos: <?php echo $data[0]->consignee_email;?></p>
			    		<p>Dirección: <?php echo $data[0]->consignee_address1?></p>
			    		<p>Teléfono: <?php echo $data[0]->consignee_phone?></p>
			    		<p>Referencia: <?php echo $data[0]->referencia?></p>
			    	</div>
			    </div>
			    <div class="containerj">
			        <div class="my3">
			        	<p><?php echo $data[0]->tracking_number;?> 1 de <?php echo $data[0]->pieces?></p>
			        </div>
			    </div>
			    <div class="containerj">
			        <div class="my5">
			            <h1><?php echo  $data[0]->tipo_servicio ;?></h1>
			        </div>
			        <div class="my5 borde-l">
			            <h1><?php echo $data[0]->departamento_code; ?></h1>
			            <p><?php echo utf8_decode($data[0]->municipio_name); ?></p>
			        </div>
			    </div>
			    <div class="containerj">
			        <div class="my4">
			            <p>A cobrar: <?php echo $data[0]->cobro_final; ?></p>
			            <p>Peso: <?php echo $data[0]->gweight; ?> LB</p>
			            <p>Peso maximo: 0.00 LB</p>
			            <p>Especiales:</p>
			        </div>
			        <div class="my4">
			           <p>PAGO: Credito</p>
			           <p>Tipo de entrega: <?php echo  $data[0]->tipo_entrega;?></p>
			        </div>
			    </div>
	        </div>
        </div>
</body>
</html>