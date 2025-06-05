<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- registrar_productos.php -->
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

        

</head>
<body>
    <div class="container-register_products">
        <br><h2>Registrar Producto</h2> <br><br>
        <form method="POST" action="home_admin.php">

            <?php
                include("connection.php");
            ?>
            <!--<input class ="input_form" type="text" name="idProducto" id="" placeholder="Id del Producto: " required> -->
            <input class ="input_form" type="text" name="Nombre_producto" id="" placeholder="Nombre producto: " required> 
            <input class ="input_form" type="text" name="Descripcion" id="" placeholder="Descipcion del Producto: " required> 
            <input class ="input_form" type="number" name="Cantidad" id="" placeholder="Cantidad: " required> 
            <input class ="input_form" type="text" name="Precio" id="" placeholder="Precio: " required step="0.01">    
            <input class ="input_form" type="text" name="Marca" id="" placeholder="Marca: " required> 
            <input class ="input_form" type="text" name="Modelo" id="" placeholder="Modelo: " required> 
            <input class ="input_form" type="number" name="Categoria_producto" id="" placeholder="Categoria: " required> 

            
            <!--<input type="submit" class="btn btn-primary" name="btn-registrar" id="btn-registrar">Registrar Producto</input>-->
            <input type="submit" name="btn-up" id="btn-registrar" class="btn-login" value="Registrar" >

        </form>
    </div>

    
    
</body>
</html>