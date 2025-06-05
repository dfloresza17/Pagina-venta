<?php
    if(!empty($_GET["idCliente"])){
        $idCliente = $_GET["idCliente"];

        $sql = $connection -> query("DELETE FROM solicitud_borrar_datos WHERE idCliente = $idCliente");

        $sql = $connection->query("INSERT INTO registro_eliminaciones (idCliente) VALUES ('$idCliente')");


        if($sql == 1){
            echo "<div class='alert alert-success'>Cliente borrado de solicitu correctamente</div>";
        }else{
            echo "<div class='alert alert-danger'>Error al eliminar</div>";
        }
    }
?>