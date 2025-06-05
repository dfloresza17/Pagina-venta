<?php
    if(!empty($_GET["idProducto"])){
        $idProducto = $_GET["idProducto"];

        $sql = $connection -> query("DELETE FROM producto WHERE idProducto = $idProducto");

        if($sql == 1){
            echo "<div class='alert alert-success'>Producto borrado correctamente</div>";
        }else{
            echo "<div class='alert alert-danger'>Error al eliminar</div>";
        }
    }
?>