<style>
    /* Importar fuente moderna si no está en tu layout principal */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

    html,
    body {
        height: 100%;
        margin: 0;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        background-color: #f4f6f9;
    }

    /* Navbar Superior de Alto Contraste */
    .main-header {
        position: fixed !important;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1030;
        background: #0B0F19 !important;
        /* Fondo ultra oscuro a juego con sidebar */
        border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
        padding: 0.6rem 1.5rem;
        height: 60px;
        display: flex;
        align-items: center;
    }

    /* Ajuste para que el contenido no quede oculto bajo la navbar */
    body:not(.sidebar-mini-md) .content-wrapper,
    body:not(.sidebar-mini-md) .main-sidebar {
        padding-top: 60px;
    }

    /* Sidebar Estilo SaaS Moderno */
    .main-sidebar {
        background-color: #0B0F19 !important;
        border-right: 1px solid rgba(255, 255, 255, 0.08) !important;
        box-shadow: 10px 0 30px rgba(0, 0, 0, 0.2) !important;
    }

    .sidebar {
        width: 260px;
        height: 100%;
        overflow-y: auto !important;
        overflow-x: hidden !important;
        padding: 1.2rem 0.8rem;
    }

    /* Área del Logo */
    .brand-link-container {
        padding: 0.5rem 0.5rem 1.5rem 0.5rem;
        display: flex;
        justify-content: center;
    }

    .brand-logo-img {
        max-width: 75%;
        filter: drop-shadow(0px 4px 10px rgba(0, 0, 0, 0.3));
        transition: transform 0.3s ease;
    }

    .brand-link-container:hover .brand-logo-img {
        transform: scale(1.03);
    }

    /* Panel de Usuario Estilo Tarjeta */
    .user-card-panel {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 12px;
        padding: 12px;
        margin-bottom: 1.5rem;
        transition: background 0.3s ease;
    }

    .user-card-panel:hover {
        background: rgba(255, 255, 255, 0.05);
    }

    .user-flag {
        width: 38px;
        height: 26px;
        object-fit: cover;
        border-radius: 6px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .user-name-text {
        font-size: 14px;
        font-weight: 500;
        color: #F3F4F6 !important;
        letter-spacing: 0.3px;
    }

    /* Elementos del Menú Principal */
    .nav-sidebar .nav-item {
        margin-bottom: 0.35rem;
    }

    .nav-sidebar .nav-item>.nav-link {
        background: transparent !important;
        display: flex;
        align-items: center;
        padding: 11px 16px;
        color: #9CA3AF !important;
        border-radius: 10px;
        font-weight: 500;
        font-size: 14px;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Iconos del Menú Principal */
    .nav-sidebar .nav-item>.nav-link .nav-icon {
        font-size: 18px;
        margin-right: 12px;
        color: #6B7280;
        transition: all 0.25s ease;
    }

    /* Estados Hover y Activo del Menú Principal */
    .nav-sidebar .nav-item>.nav-link:hover {
        background-color: rgba(254, 79, 0, 0.08) !important;
        /* Fondo naranja translúcido */
        color: #FE4F00 !important;
        /* Texto toma el color de acento */
    }

    .nav-sidebar .nav-item>.nav-link:hover .nav-icon {
        color: #FE4F00 !important;
        transform: translateX(2px);
    }

    /* Si AdminLTE añade la clase active */
    .nav-sidebar .nav-item.menu-open>.nav-link,
    .nav-sidebar .nav-item>.nav-link.active {
        background-color: #FE4F00 !important;
        /* Elemento activo full color */
        color: #fff !important;
        box-shadow: 0 4px 12px rgba(254, 79, 0, 0.3);
    }

    .nav-sidebar .nav-item.menu-open>.nav-link .nav-icon,
    .nav-sidebar .nav-item>.nav-link.active .nav-icon {
        color: #fff !important;
    }

    /* Contenedor del Submenú */
    .nav-treeview {
        background: rgba(0, 0, 0, 0.15);
        border-radius: 8px;
        margin-top: 2px;
        padding-left: 8px;
    }

    /* Enlaces del Submenú */
    .nav-sidebar .menu-open>.nav-treeview>li>a {
        display: flex;
        align-items: center;
        background: transparent !important;
        padding: 9px 16px;
        font-size: 13.5px;
        color: #9CA3AF !important;
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    .nav-sidebar .menu-open>.nav-treeview>li>a:hover {
        background-color: rgba(255, 255, 255, 0.05) !important;
        color: #F3F4F6 !important;
        padding-left: 20px;
        /* Sutil efecto de empuje */
    }

    .nav-sidebar .menu-open>.nav-treeview>li>a>.nav-icon {
        color: #4B5563 !important;
        font-size: 14px;
        margin-right: 12px;
        transition: color 0.2s ease;
    }

    .nav-sidebar .menu-open>.nav-treeview>li>a:hover>.nav-icon {
        color: #FE4F00 !important;
    }

    /* Botón Moderno de Cerrar Sesión */
    .btn-cerrar-sesion {
        font-size: 13.5px;
        font-weight: 500;
        color: #F3F4F6 !important;
        background-color: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.1);
        padding: 7px 16px;
        border-radius: 8px;
        text-decoration: none !important;
        transition: all 0.25s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-cerrar-sesion:hover {
        background-color: #FE4F00;
        color: #ffffff !important;
        border-color: #FE4F00;
        box-shadow: 0 4px 12px rgba(254, 79, 0, 0.25);
        transform: translateY(-1px);
    }

    /* Botón Hamburguesa de Menú */
    .burger-menu-btn {
        color: #9CA3AF !important;
        font-size: 18px;
        transition: color 0.2s ease;
    }

    .burger-menu-btn:hover {
        color: #FE4F00 !important;
    }

    /* Reset estructural obligatorio */
    .sidebar,
    .nav-sidebar,
    .nav-pills .nav-link,
    .nav-item>.nav-link {
        border: none !important;
        box-shadow: none !important;
    }

    /* Configuración global de FontAwesome */
    i {
        font-family: "Font Awesome 5 Free";
        font-weight: 900;
    }
</style>

<nav class="main-header navbar navbar-expand navbar-light">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link burger-menu-btn" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars"></i>
            </a>
        </li>
    </ul>

    <ul class="navbar-nav ml-auto"></ul>

    <a href="#" class="btn-cerrar-sesion" onclick="salir()">
        <i class="fas fa-power-off"></i> <span>Cerrar Sesión</span>
    </a>
</nav>

<aside class="main-sidebar sidebar-dark-primary">
    <div class="sidebar">

        <div class="brand-link-container">
            <img class="brand-logo-img d-block" src="<?php echo base_url(); ?>public/dist/img/AdminLTELogo.png" alt="AdminLTE Logo">
        </div>

        <div class="user-card-panel d-flex align-items-center">
            <div class="mr-3">
                <?php
                $paises = [1 => 'sv.png', 2 => 'gt.png', 3 => 'hn.png'];
                $flag = isset($paises[$_SESSION["pais"]]) ? $paises[$_SESSION["pais"]] : 'default.png';
                ?>
                <img src="<?php echo base_url(); ?>public/dist/img/<?php echo $flag; ?>" alt="Nación" class="user-flag">
            </div>
            <div class="overflow-hidden">
                <a href="#" class="user-name-text d-block text-truncate">
                    <?php echo htmlspecialchars($_SESSION["nombre"] ?? 'Usuario Activo'); ?>
                </a>
            </div>
        </div>

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <?php
                // Renderizado dinámico limpio de tus enlaces (.nav-item > .nav-link > .nav-icon + p)
                echo $menu;
                ?>
            </ul>
        </nav>
    </div>
</aside>