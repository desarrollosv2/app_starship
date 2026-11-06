<style>
    /* ==========================================
    CARDS TEMU
    ========================================== */

    .card-temu {
        position: relative;

        display: flex;
        align-items: center;

        min-height: 145px;

        background: #fff;

        border: 1px solid #e9ecef;
        border-radius: 10px;

        padding: 20px;

        box-shadow: 0 2px 6px rgba(0, 0, 0, .05);

        transition: all .2s ease;
    }

    .card-temu:hover {
        transform: translateY(-2px);

        box-shadow: 0 5px 15px rgba(0, 0, 0, .08);
    }


    /* ==========================================
    ICONO
    ========================================== */

    .card-temu-icon {
        width: 58px;
        height: 58px;

        min-width: 58px;

        display: flex;
        align-items: center;
        justify-content: center;

        border-radius: 50%;

        font-size: 25px;

        margin-right: 16px;
    }


    /* ==========================================
    CONTENIDO
    ========================================== */

    .card-temu-content {
        min-width: 0;
    }

    .card-temu-titulo {
        font-size: 14px;
        font-weight: 600;

        color: #6c757d;

        margin-bottom: 2px;
    }


    /* Cantidad de paquetes */

    .card-temu-cantidad {
        display: inline-block;

        font-size: 28px;
        font-weight: 700;

        line-height: 1.1;
    }

    .card-temu-label {
        display: inline-block;

        font-size: 13px;

        color: #868e96;

        margin-left: 4px;
    }


    /* Monto */

    .card-temu-monto {
        font-size: 20px;
        font-weight: 700;

        margin-top: 5px;
        margin-bottom: 2px;
    }


    /* Descripción */

    .card-temu-descripcion {
        font-size: 11px;

        color: #868e96;
    }


    /* ==========================================
    PAGADOS
    ========================================== */

    .card-temu-pagados {
        border-left: 4px solid #28a745;
    }

    .card-temu-pagados .card-temu-icon {
        background: rgba(40, 167, 69, .10);
        color: #28a745;
    }

    .card-temu-pagados .card-temu-cantidad,
    .card-temu-pagados .card-temu-monto {
        color: #28a745;
    }


    /* ==========================================
    PENDIENTES
    ========================================== */

    .card-temu-pendientes {
        border-left: 4px solid #ffc107;
    }

    .card-temu-pendientes .card-temu-icon {
        background: rgba(255, 193, 7, .12);
        color: #d39e00;
    }

    .card-temu-pendientes .card-temu-cantidad,
    .card-temu-pendientes .card-temu-monto {
        color: #d39e00;
    }


    /* ==========================================
    TOTAL
    ========================================== */

    .card-temu-total {
        border-left: 4px solid #007bff;
    }

    .card-temu-total .card-temu-icon {
        background: rgba(0, 123, 255, .10);
        color: #007bff;
    }

    .card-temu-total .card-temu-cantidad,
    .card-temu-total .card-temu-monto {
        color: #007bff;
    }


    /* ==========================================
    RESPONSIVE
    ========================================== */

    @media (max-width: 767px) {

        .card-temu {
            min-height: 125px;

            padding: 15px;
        }

        .card-temu-icon {
            width: 48px;
            height: 48px;

            min-width: 48px;

            font-size: 20px;

            margin-right: 12px;
        }

        .card-temu-cantidad {
            font-size: 24px;
        }

        .card-temu-monto {
            font-size: 18px;
        }

    }
</style>

<div class="row mt-3">

    <!-- TOTAL -->
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="card-temu card-temu-total">

            <div class="card-temu-icon">
                <i class="fa fa-calculator"></i>
            </div>

            <div class="card-temu-content">

                <div class="card-temu-titulo">
                    Paquetes declarados
                </div>

                <div class="card-temu-cantidad" id="total_paquetes">
                    <?= $dash->total_paquetes_declarados ?>
                </div>

                <div class="card-temu-label">
                    paquetes
                </div>

                <div class="card-temu-monto" id="monto_total_declarado">
                    $ <?= number_format($dash->total_impuestos_declarados, 2) ?>
                </div>

                <div class="card-temu-descripcion">
                    Total de impuestos declarados
                </div>

            </div>

        </div>
    </div>


    <!-- PAQUETES PAGADOS -->
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="card-temu card-temu-pagados">

            <div class="card-temu-icon">
                <i class="fa fa-check-circle"></i>
            </div>

            <div class="card-temu-content">

                <div class="card-temu-titulo">
                    Paquetes pagados
                </div>

                <div class="card-temu-cantidad" id="total_paquetes_pagados">
                    <?= $dash->total_paquetes_pagados ?>
                </div>

                <div class="card-temu-label">
                    paquetes
                </div>

                <div class="card-temu-monto" id="monto_total_pagado">
                    $ <?= number_format($dash->total_impuestos_pagados , 2) ?>
                </div>

                <div class="card-temu-descripcion">
                    Total de impuestos pagados por TEMU
                </div>

            </div>

        </div>
    </div>


    <!-- PAQUETES PENDIENTES -->
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="card-temu card-temu-pendientes">

            <div class="card-temu-icon">
                <i class="fa fa-clock-o"></i>
            </div>

            <div class="card-temu-content">

                <div class="card-temu-titulo">
                    Paquetes pendientes
                </div>

                <div class="card-temu-cantidad" id="total_paquetes_pendientes">
                    <?= $dash->total_paquetes_pendientes ?>
                </div>

                <div class="card-temu-label">
                    paquetes
                </div>

                <div class="card-temu-monto" id="monto_total_pendiente">
                    $ <?= number_format($dash->total_impuestos_pendientes, 2) ?>
                </div>

                <div class="card-temu-descripcion">
                    Total de impuestos pendientes de pago
                </div>

            </div>

        </div>
    </div>



</div>