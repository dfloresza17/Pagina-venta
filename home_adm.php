<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Home Admin</title>
    
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
            var respuesta = confirm("¿Estas seguro de eliminar el producto?");
            return respuesta; 
        }
    </script>




    <!-- HEADER -->
    <header>
        <!-- TOP HEADER -->
        <!-- TOP HEADER -->
        <div id="top-header">
            <div class="container">
                <ul class="header-links pull-left">
                    <li><a href="ver_usuarios.php"><i class="fa-solid fa-user-pen"></i> Ver usuarios</a></li>
                    <li><a href="#"><i class="fa-solid fa-computer"></i> Ver articulos</a></li>
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

    <h1 class = "text-center p-3">Registro de articulos</h1>

    <div class="container-fluid row">
        <form class = "col-4" method = "POST" enctype="multipart/form-data">
            <?php
                include "connection.php";
                include "controlador/admin_registrar_articulos.php" ;
                include "controlador/eliminar_articulo.php";
            ?>
            <input class ="input_form" type="text" name="Nombre_producto" id="" placeholder="Nombre producto: " > 
            <input class ="input_form" type="text" name="Descripcion" id="" placeholder="Descipcion del Producto: " > 
            <input class ="input_form" type="text" pattern="[0-9]*" name="Cantidad" id="" placeholder="Cantidad: " > 
            <input class ="input_form" type="text" name="Precio" id="" placeholder="Precio: "  step="0.01">    
            <input class ="input_form" type="text" name="Marca" id="" placeholder="Marca: " > 
            <input class ="input_form" type="text" name="Modelo" id="" placeholder="Modelo: " > 
            <input class ="input_form" type="text" name="Categoria_producto" id="" placeholder="Categoria: " > <br>
            <div class="form-group">
                <label for="imagen">Imagen:</label>
                <input type="file" class="form-control" id="imagen" name="imagen" placeholder="Imagen">
                <label for="gif">Gif:</label>
                <input type="file" class="form-control" id="gif" name="gif" placeholder="Gif">
            </div>

            <input type="submit" name="btn-up" id="btn-registrar" class="btn-login" value="Registrar" >
        </form>

        <div class="col-8 p-4">
            <table class="table">
                <thead>
                    <tr>
                    <th scope="col">id</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Descripcion</th>
                    <th scope="col">Cantidad</th>
                    <th scope="col">Precio</th>
                    <th scope="col">Marca</th>
                    <th scope="col">Modelo</th>
                    <th scope="col">Categoria</th>
                    <th scope="col">Editar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        include ("connection.php");
                        $sql = $connection->query("SELECT * FROM producto");
                        while($datos = $sql->fetch_object()) { ?>
                            <tr>
                                <td ><?=$datos->idProducto ?></td>
                                <td><?=$datos->Nombre_producto ?></td>
                                <td><?=$datos->Descripcion ?></td>
                                <td><?=$datos->Cantidad ?></td>
                                <td><?=$datos->Precio ?></td>
                                <td><?=$datos->Marca ?></td>
                                <td><?=$datos->Modelo ?></td>
                                <td><?=$datos->Categoria_producto ?></td>
                                <td>
                                    <a href="modificar_producto.php?idProducto=<?=$datos->idProducto?>" class = "btn"><i class="fa-regular fa-pen-to-square"></i></a>                              
                                    <a onclick="return eliminar()" href="home_adm.php?idProducto=<?= $datos->idProducto?>" class = "btn btn-danger"><i class="fa-solid fa-trash-can "></i></a>
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
