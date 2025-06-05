<?php
    include ("connection.php");    
    $idDireccion = $_GET["idDireccion"];
    $sql = $connection -> query("SELECT *  FROM direccion WHERE idDireccion = $idDireccion");
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
        <h2 class = "text-center alert-secondary">Modificar mi direccion</h2>

        <input type="hidden" name="idDireccion" value = "<?= $_GET["idDireccion"]?>">

        <?php
            include ("controlador/cont_modificar_direccion.php");
            while($datos = $sql ->fetch_object()){?>
                <div class="mb-1">
                    <label class="form-label">Direccion</label>
                    <input class ="input_form" type="text" name="Direccion" id=""  value = "<?= $datos->Direccion ?>" > 
                </div>

                <div class="mb-1">
                    <label class="form-label">Colonia</label>
                    <input class ="input_form" type="text" name="Colonia" value = "<?= $datos->Colonia ?>" > 
                </div>

                <div class="mb-1">
                    <label for="exampleInputEmail" class="form-label">Ciudad</label>
                    <input class ="input_form" type="text" name="Ciudad" id=""value = "<?= $datos->Ciudad ?>" > 
                </div>

                <div class="mb-1">
                    <label for="exampleInputEmail" class="form-label">Codigo Postal</label>
                    <input class ="input_form" type="number" name="Codigo_Postal" id="" value = "<?= $datos->Codigo_Postal ?>"> 
                </div>

                <!--<input type="submit" name="btn-up" id="btn-registrar" class="btn-login" value="Modificar producto" >-->
                <button type="submit" class="btn btn-primary" name="btn-guardar" value="ok">Modificar direccion</button>
        <?php }
        ?>        
    </form>
</body>
</html>



