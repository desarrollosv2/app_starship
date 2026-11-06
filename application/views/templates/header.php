<?php
// Menu lateral: grupos con sus opciones. La clave de cada opcion coincide con $menu del controlador.
$grupos = array(
    array('titulo' => 'Pallets', 'icono' => 'fa-pallet', 'items' => array(
        'PalletController/index' => array('Armar pallet', 'fa-barcode', site_url('PalletController')),
        'PalletController/listado' => array('Listado de pallets', 'fa-list', site_url('PalletController/listado')),
    )),
    array('titulo' => 'Reportes', 'icono' => 'fa-chart-column', 'items' => array(
        'ReportesController/cajas-tipo' => array('Cajas por tipo', 'fa-box', site_url('ReportesController/ver/cajas-tipo')),
        'ReportesController/cliente-tipo' => array('Cliente y tipo', 'fa-table-cells', site_url('ReportesController/ver/cliente-tipo')),
        'ReportesController/pallets-tipo' => array('Pallets por tipo', 'fa-tags', site_url('ReportesController/ver/pallets-tipo')),
        'ReportesController/pallets-cliente' => array('Pallets por cliente', 'fa-users', site_url('ReportesController/ver/pallets-cliente')),
    )),
);
?><!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-name" content="<?php echo html_escape($this->security->get_csrf_token_name()); ?>">
    <meta name="csrf-hash" content="<?php echo html_escape($this->security->get_csrf_hash()); ?>">
    <title><?php echo html_escape($titulo ? $titulo . ' · ' : ''); ?>Logística Inversa</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/css/alertify.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/css/themes/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo base_url('assets/css/app.css'); ?>" rel="stylesheet">
</head>
<body>
<aside class="sidebar" id="sidebar">
    <a class="brand" href="<?php echo site_url('PalletController'); ?>">
        <span class="brand-icon"><i class="fa-solid fa-boxes-stacked"></i></span>
        <span class="brand-text">Logística<small>inversa</small></span>
    </a>

    <nav class="menu">
        <?php foreach ($grupos as $i => $g): ?>
            <?php $abierto = array_key_exists($menu, $g['items']); ?>
            <a class="menu-grupo<?php echo $abierto ? '' : ' collapsed'; ?>" data-bs-toggle="collapse" href="#grupo-<?php echo $i; ?>"
               role="button" aria-expanded="<?php echo $abierto ? 'true' : 'false'; ?>">
                <i class="fa-solid <?php echo $g['icono']; ?> fa-fw"></i>
                <span><?php echo $g['titulo']; ?></span>
                <i class="fa-solid fa-chevron-down menu-chevron"></i>
            </a>
            <div class="collapse<?php echo $abierto ? ' show' : ''; ?>" id="grupo-<?php echo $i; ?>">
                <?php foreach ($g['items'] as $clave => $item): ?>
                    <a class="menu-item<?php echo $menu === $clave ? ' active' : ''; ?>" href="<?php echo $item[2]; ?>">
                        <i class="fa-solid <?php echo $item[1]; ?> fa-fw"></i><span><?php echo $item[0]; ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </nav>
</aside>
<div class="sidebar-backdrop" onclick="toggle_sidebar()"></div>

<div class="main">
    <header class="topbar">
        <button type="button" class="topbar-toggle" onclick="toggle_sidebar()" aria-label="Mostrar u ocultar menú">
            <i class="fa-solid fa-bars"></i>
        </button>
        <span class="topbar-titulo"><?php echo html_escape($titulo); ?></span>
        <span class="topbar-fecha"><i class="fa-regular fa-calendar"></i> <?php echo date('d/m/Y'); ?></span>
    </header>

    <main class="content">
