<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title></title>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <button type="button" id="enviar" class="btn btn-primary my-2 my-sm-0" onclick="prealertas()"> Pre alertar</button>

               
        <form class="form-inline my-2 my-lg-0" id="form">
            <input class="form-control mr-sm-0" type="search" placeholder="Buscar..." aria-label="Search" id="myInput"
                style="margin-left:20px;">
           
        </form>
        </div>
    </nav>
    
    <script>
    $('#form').on('keyup keypress', function(e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            e.preventDefault();
            return false;
        }
    });
    </script>
<body>
</html>