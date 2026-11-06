<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Cajas por pallet antes de pedir consentimiento al usuario para seguir agregando
$config['max_cajas_default'] = 12;

// Formato de la etiqueta PDF: 'A4' (horizontal, para pegar en el pallet) o 'ETIQUETA' (100x150 mm, impresora termica)
$config['pdf_formato'] = 'A4';
