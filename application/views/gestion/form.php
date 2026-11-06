<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Starship</title>
  
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/css/alertify.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #FE4F00 !important;
            --primary-light: rgba(254, 79, 0, 0.08) !important;
            --secondary: #D94200 !important;
            --success: #28A745 !important;
            --dark: #212529 !important;
            --light: #FFFFFF !important;
            --gray: #6C757D !important;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            
        }

       

        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header {
            background: linear-gradient(to right, var(--primary), var(--secondary));
            color: white;
            padding: 2rem;
            text-align: center;
            position: relative;
        }

        .header h1 {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }

        .header p {
            opacity: 0.9;
            font-size: 1rem;
        }

        .tracking-form {
            padding: 2rem;
            background: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .input-group {
            display: flex;
            max-width: 600px;
            margin: 0 auto;
            box-shadow: 0 5px 15px rgba(254, 79, 0, 0.15);
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s;
        }

        .input-group:focus-within {
            box-shadow: 0 5px 20px rgba(254, 79, 0, 0.25);
            transform: translateY(-2px);
        }

        #tracking-number {
            flex: 1;
            padding: 1rem 1.5rem;
            border: none;
            font-size: 1rem;
            outline: none;
            background: var(--light);
        }

        #buscar_bag_number {
            background: linear-gradient(to right, var(--primary), var(--secondary));
            color: white;
            border: none;
            padding: 0 2rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        #buscar_bag_number:hover {
            background: var(--secondary);
        }

        .tracking-results {
            display: none;
            padding: 2rem;
            animation: fadeIn 0.6s ease-out;
        }

        .status-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            padding: 1.5rem;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .status-badge {
            background: var(--primary-light);
            color: var(--primary);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .status-badge.delivered {
            background: #E8F5E9;
            color: #28A745;
        }

        .status-badge.in-transit {
            background: #FFF3E0;
            color: #FF6D00;
        }

        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 11px;
            top: 0;
            height: 100%;
            width: 2px;
            background: #e0e0e0;
        }

        .timeline-event {
            position: relative;
            padding-bottom: 2rem;
            padding-left: 2rem;
        }

        .timeline-event:last-child {
            padding-bottom: 0;
        }

        .event-dot {
            position: absolute;
            left: 0;
            top: 0;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: var(--gray);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 12px;
            z-index: 2;
        }

        .timeline-event.active .event-dot {
            background: var(--primary);
            box-shadow: 0 0 0 4px rgba(254, 79, 0, 0.2);
        }

        .timeline-event.delivered .event-dot {
            background: #28A745;
            box-shadow: 0 0 0 4px rgba(40, 167, 69, 0.2);
        }

        .event-content {
            background: var(--light);
            border-radius: 8px;
            padding: 1rem;
            transition: all 0.3s;
            border: 1px solid #f0f0f0;
        }

        .timeline-event.active .event-content {
            background: var(--primary-light);
            border-left: 3px solid var(--primary);
        }

        .timeline-event.delivered .event-content {
            background: #E8F5E9;
            border-left: 3px solid #28A745;
        }

        .event-date {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--gray);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .event-status {
            font-weight: 700;
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
        }

        .event-location {
            color: var(--gray);
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
        }

        .copy-tracking {
            background: none;
            border: none;
            color: var(--primary);
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.9rem;
            transition: all 0.3s;
        }

        .copy-tracking:hover {
            color: var(--secondary);
        }

        @media (max-width: 768px) {
            .container {
                border-radius: 12px;
                margin: 1rem;
                padding: 0;
            }

            .input-group {
                flex-direction: column;
                box-shadow: none;
            }

            #tracking-number {
                border-radius: 8px 8px 0 0;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            }

            #buscar_bag_number {
                border-radius: 0 0 8px 8px;
                padding: 1rem;
                justify-content: center;
            }

            .status-card {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        /* Animaciones */
        .pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(254, 79, 0, 0.4);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(254, 79, 0, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(254, 79, 0, 0);
            }
        }
    </style>
</head>

<body>
    <div class="container mt-3">
        
        <div class="tracking-form">
          
            <?php echo form_open('index.php/Gestion/buscar_bag_number', ['id' => 'frm_buscar_bag_number']); ?>
            <div class="input-group">
                <input type="hidden" id="master-id" name="master-id">
                <input
                    type="text"
                    id="tracking-number"
                    name="tracking_number"
                    placeholder="Ingresa o escanea tu número de Bolsa"
                    required>
                <button type="submit" id="buscar_bag_number">
                    <i class="fas fa-plus-circle"></i> Crear guías
                </button>
            </div>
            <?php echo form_close(); ?>
        </div>

        <div id="tbl_respuesta_busqueda"></div>

    </div>

  
    <script src="<?php echo base_url(); ?>public/js/gestion.js"></script>
</body>

</html>