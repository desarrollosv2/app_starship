<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0" />



    <style type="text/css">
        ::selection {
            background-color: #E13300;
            color: white;
        }

        ::-moz-selection {
            background-color: #E13300;
            color: white;
        }

        body {

            margin: 40px;
            font: 13px/20px normal Helvetica, Arial, sans-serif;
            color: #4F5155;

        }

        a {
            color: #003399;
            background-color: transparent;
            font-weight: normal;
            text-decoration: none;
        }

        a:hover {
            color: #97310e;
        }

        h1 {
            color: #444;
            background-color: transparent;
            border-bottom: 1px solid #D0D0D0;
            font-size: 19px;
            font-weight: normal;
            margin: 0 0 14px 0;
            padding: 14px 15px 10px 15px;
        }

        code {
            font-family: Consolas, Monaco, Courier New, Courier, monospace;
            font-size: 12px;
            background-color: #f9f9f9;
            border: 1px solid #D0D0D0;
            color: #002166;
            display: block;
            margin: 14px 0 14px 0;
            padding: 12px 10px 12px 10px;
        }

        #body {
            margin: 0 15px 0 15px;
            min-height: 96px;
        }

        p {
            margin: 0 0 10px;
            padding: 0;
        }

        p.footer {
            text-align: right;
            font-size: 11px;
            border-top: 1px solid #D0D0D0;
            line-height: 32px;
            padding: 0 10px 0 10px;
            margin: 20px 0 0 0;
        }


        .form-floating {
            height: 40vh;
        }

        .slide-result {
            height: 75vh;
        }

        .container {
            background: white;
            border-radius: 10px;
            height: 100vh;
        }


        .accordion-container {
            width: 100%;

        }

        .card-aco {
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 10px;
            overflow: hidden;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
        }

        .card-aco-header {
            background: white;
            color: white;
            padding: 12px 20px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-aco-header h3 {
            margin: 0;
            font-size: 18px;
        }

        .card-aco-header b{
            color: black;
        }

        .icon {
            display: inline-block;
            font-weight: bold;
            transition: transform 0.3s ease;
            background: black;
            border-radius: 100%;
            width: 30px;
            height: 30px;
            font-size: 16px;
            font-weight: bold;
            line-height: 30px;
            text-align: center;
        }

        .card-aco-body {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            padding: 0 20px;
            background: #f9f9f9;
        }

        .card-aco-body p {
            margin: 10px 0;
        }

        @media only screen and (max-width: 600px) {

            .container {
                background: white;
                border-radius: 10px;
                height: fit-content;
            }

            .accordion-header .accordion-button {
                justify-content: flex-start !important;
                flex-wrap: wrap;
            }

            .slide-result {
                height: auto;
            }


        }
    </style>

    <!--<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">-->
</head>

<body class="p-4" style="background-color: #F15A29;">

    <div id="container" class="container">

        

        <div class="row slide-result">

            <div class="p-3  col-xs-12 col-sm-12 col-md-3 col-lg-3">


                <h1>Guías</h1>

                <form id="frmTrack">


                    <div class="mb-3">

                        <div class="form-floating">
                            <textarea class="form-control" id="txtListWarehouse" name="txtListWarehouse" style="height: 40vh"></textarea>

                        </div>
                    </div>
                    <div class="mb-3">
                        <button class="btn btn-dark">Track <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-truck" viewBox="0 0 16 16">
                                <path d="M0 3.5A1.5 1.5 0 0 1 1.5 2h9A1.5 1.5 0 0 1 12 3.5V5h1.02a1.5 1.5 0 0 1 1.17.563l1.481 1.85a1.5 1.5 0 0 1 .329.938V10.5a1.5 1.5 0 0 1-1.5 1.5H14a2 2 0 1 1-4 0H5a2 2 0 1 1-3.998-.085A1.5 1.5 0 0 1 0 10.5zm1.294 7.456A2 2 0 0 1 4.732 11h5.536a2 2 0 0 1 .732-.732V3.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5v7a.5.5 0 0 0 .294.456M12 10a2 2 0 0 1 1.732 1h.768a.5.5 0 0 0 .5-.5V8.35a.5.5 0 0 0-.11-.312l-1.48-1.85A.5.5 0 0 0 13.02 6H12zm-9 1a1 1 0 1 0 0 2 1 1 0 0 0 0-2m9 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2" />
                            </svg></button>




                        <button class="btn btn-outline-danger" type="button" onclick="clean()">Limpiar</button>


                    </div>
                </form>

            </div>
            <div class="p-3 col-xs-12 col-sm-12 col-md-9 col-lg-9" style="height: 100%;">

                <h1>Guías encontradas</h1>

                <div id="result" style="height: 100%; overflow: scroll;"></div>

            </div>

        </div>


    </div>

    <!--<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    -->
    <script type="text/javascript">
        function toggleCard(header) {
            const body = header.nextElementSibling;
            const icon = header.querySelector(".icon");

            if (body.style.maxHeight) {
                body.style.maxHeight = null; // cerrar
                icon.textContent = "+"; // cambiar icono a +
            } else {
                body.style.maxHeight = body.scrollHeight + "px"; // abrir
                icon.textContent = "-"; // cambiar icono a -
            }
        }



        $("#frmTrack").submit(function(e) {

            e.preventDefault();

            var frm = $(this).serialize();


            $.get("<?= base_url() ?>index.php/Gestion/list_warehouse", frm, function(result) {

                $("#result").html(result);
            })


        });

        function getStatusByWarehouse(warehouse, number, header) {
           


            $.get("<?= base_url() ?>index.php/Gestion/status_warehouse/" + warehouse, function(result) {
                console.log(result)
                $("#status_" + number).html(result);

                toggleCard(header)


            })
        }

        function clean() {

            $("#txtListWarehouse").val("");
            $("#frmTrack").submit();

        }
    </script>


</body>

</html>