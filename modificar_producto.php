<?php
    include ("connection.php");    
    $idProducto = $_GET["idProducto"];
    $sql = $connection -> query("SELECT *  FROM producto WHERE idProducto = $idProducto");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Articulos</title>

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


    <form class = "col-4 p-3 m-auto" method = "POST">
        <h2 class = "text-center alert-secondary">Modificar articulo</h2>

        <input type="hidden" name="idProducto" value = "<?= $_GET["idProducto"]?>">

        <?php
            include ("controlador/cont_modificar_producto.php");
            while($datos = $sql ->fetch_object()){?>
                <div class="mb-1">
                    <label for="exampleInputEmail" class="form-label">Nombre del producto</label>
                    <input class ="input_form" type="text" name="Nombre_producto" id=""  value = "<?= $datos->Nombre_producto ?>" > 
                </div>

                <div class="mb-1">
                    <label for="exampleInputEmail" class="form-label">Descripcion del producto</label>
                    <input class ="input_form" type="text" name="Descripcion" id="" value = "<?= $datos->Descripcion ?>" > 
                </div>

                <div class="mb-1">
                    <label for="exampleInputEmail" class="form-label">Cantidad del producto</label>
                    <input class ="input_form" type="text" name="Cantidad" id=""value = "<?= $datos->Cantidad ?>" > 
                </div>

                <div class="mb-1">
                    <label for="exampleInputEmail" class="form-label">Precio del producto</label>
                    <input class ="input_form" type="text" name="Precio" id="" value = "<?= $datos->Precio ?>"  step="0.01"> 
                </div>

                <div class="mb-1">
                    <label for="exampleInputEmail" class="form-label">Marca del producto</label>
                    <input class ="input_form" type="text" name="Marca" id="" value = "<?= $datos->Marca ?>" >  
                </div>

                <div class="mb-1">
                    <label for="exampleInputEmail" class="form-label">Modelo del producto</label>
                    <input class ="input_form" type="text" name="Modelo" id="" value = "<?= $datos->Modelo ?>" >  
                </div>

                <div class="mb-1">
                    <label for="exampleInputEmail" class="form-label">Categoria del producto</label>
                    <input class ="input_form" type="text" name="Categoria_producto" id="" value = "<?= $datos->Categoria_producto ?>" > 
                </div>

                <!--<input type="submit" name="btn-up" id="btn-registrar" class="btn-login" value="Modificar producto" >-->
                <button type="submit" class="btn btn-primary" name="btn-registrar" value="ok">Modificar producto</button>
        <?php }
        ?>        
    </form>
</body>
</html>



