<?php
    if(!empty($_POST["btn-registrar"])){
        if(!empty($_POST["Nombre_producto"]) and !empty($_POST["Descripcion"]) and !empty($_POST["Cantidad"]) and !empty($_POST["Precio"]) and !empty($_POST["Marca"]) and !empty($_POST["Modelo"]) and !empty($_POST["Categoria_producto"])){
            $idProducto = $_POST["idProducto"];
            $Nombre_producto = $_POST["Nombre_producto"];
            $Descripcion = $_POST["Descripcion"];
            $Cantidad = $_POST["Cantidad"];
            $Precio = $_POST["Precio"];
            $Marca = $_POST["Marca"];
            $Modelo = $_POST["Modelo"];
            $Categoria_producto = $_POST["Categoria_producto"];

            $sql = $connection->query("UPDATE producto SET Nombre_producto = '$Nombre_producto', Descripcion = '$Descripcion', Cantidad = '$Cantidad', Precio = '$Precio', Marca = '$Marca', Modelo = '$Modelo', Categoria_producto = '$Categoria_producto' WHERE idProducto = $idProducto");

            if($sql == 1){
                header("location:home_adm.php");
            }else{
                echo "<div class = 'alert alert-warning'>Error al actualizar</div>";
            }

        }else{
            echo "<div class = 'alert alert-warning'>Campos vacios</div>";
        }
    }
?>