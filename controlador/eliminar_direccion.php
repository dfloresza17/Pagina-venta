<?php
    if(!empty($_GET["idDireccion"])){
        $idDireccion = $_GET["idDireccion"];

        $sql = $connection -> query("DELETE FROM direccion WHERE idDireccion = $idDireccion");

        if($sql == 1){
            echo "<div class='alert alert-success'>Direccion borrada correctamente</div>";
        }else{
            echo "<div class='alert alert-danger'>Error al eliminar</div>";
        }
    }
?>