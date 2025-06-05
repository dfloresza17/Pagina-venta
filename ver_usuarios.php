<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ver usuarios</title>
    
        <!-- Bootstrap -->
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

		<!-- Google font -->
		<link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,700" rel="stylesheet">

		<!-- Bootstrap -->
		<link type="text/css" rel="stylesheet" href="css/bootstrap.min.css"/>

		<!-- Slick -->
		<link type="text/css" rel="stylesheet" href="css/slick.css"/>
		<link type="text/css" rel="stylesheet" href="css/slick-theme.css"/>

		<!-- nouislider -->
		<link type="text/css" rel="stylesheet" href="css/nouislider.min.css"/>

		<!-- Font Awesome Icon -->
		<link rel="stylesheet" href="css/font-awesome.min.css">

		<!-- Custom stlylesheet -->
		<link type="text/css" rel="stylesheet" href="css/style.css"/>

		<link rel="stylesheet" href="style.css">
        
        <script src="https://kit.fontawesome.com/d438250857.js" crossorigin="anonymous"></script>


</head>
<body>

    <script>
        function eliminar(){
            var respuesta = confirm("¿Estas seguro de eliminar el usuario?");
            return respuesta; 
        }
    </script>




    <!-- HEADER -->
    <header>
        <!-- TOP HEADER -->
        <div id="top-header">
            <div class="container">
                <ul class="header-links pull-left">
                    <li><a href="#"><i class="fa-solid fa-user-pen"></i> Ver usuarios</a></li>
                    <li><a href="home_adm.php"><i class="fa-solid fa-computer"></i> Ver articulos</a></li>
                    <li><a href="Ver_solicutudes_eliminacion.php"><i class="fa-solid fa-user-xmark"></i>Solicitudes</a></li>
                    <li><a href="informes.php"><i class="fa-solid fa-file"></i>Informes</a></li>

                </ul>
                <ul class="header-links pull-right">
                    <li><a href="#"><i class="fa fa-user-o"></i> Administrador</a></li>
                </ul>
            </div>
        </div>
        <!-- /TOP HEADER -->

        <!-- MAIN HEADER -->
        <div id="header">
            <div class="container">
                <div class="row">
                    <div class="col-md-3">
                        <div class="header-logo">
                            <a href="index.php" class="logo">
                                <img src="./img/GCCIMG.png" width="50%">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /MAIN HEADER -->
    </header>
    <!-- /HEADER -->

    <h1 class = "text-center p-3">Ver usuarios</h1>

    <div >
        <div >
            <table class="table">
                <thead>
                    <tr>
                    <th scope="col">id</th>
                    <th scope="col">Nombres</th>
                    <th scope="col">Apellido Paterno</th>
                    <th scope="col">Apellido Materno</th>
                    <th scope="col">Correo</th>
                    <th scope="col">Editar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        include ("connection.php");
                        include ("controlador/eliminar_usuario.php");
                        $sql = $connection->query("SELECT * FROM cliente");
                        while($datos = $sql->fetch_object()) { ?>
                            <tr>
                                <td ><?=$datos->idCliente ?></td>
                                <td><?=$datos->Nombres_cliente ?></td>
                                <td><?=$datos->Apellido_paterno ?></td>
                                <td><?=$datos->Apellido_materno ?></td>
                                <td><?=$datos->Correo_cliente ?></td>
                                <td>
                                    <a onclick="return eliminar()" href="ver_usuarios.php?idCliente=<?= $datos->idCliente?>" class = "btn btn-danger"><i class="fa-solid fa-trash-can "></i></a>
                                </td>
                            </tr>
                        <?php }
                    ?>

                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
