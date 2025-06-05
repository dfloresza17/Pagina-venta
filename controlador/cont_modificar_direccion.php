<?php
    if(!empty($_POST["btn-guardar"])){
        if(!empty($_POST["Direccion"]) and !empty($_POST["Colonia"]) and !empty($_POST["Codigo_Postal"]) and !empty($_POST["Ciudad"])){
            $idDireccion = $_POST["idDireccion"];
            $Direccion = $_POST["Direccion"];
            $Colonia = $_POST["Colonia"];
            $Ciudad = $_POST["Ciudad"];
            $Codigo_Postal = $_POST["Codigo_Postal"];
            
            $sql = $connection->query("UPDATE direccion SET Direccion = '$Direccion', Ciudad = '$Ciudad', Colonia = '$Colonia', Codigo_Postal = $Codigo_Postal WHERE idDireccion = $idDireccion");

            if($sql == 1){
                header("location:direccion.php");

            }else{
                echo "<div class = 'alert alert-warning'>Error al actualizar</div>";
            }

        }else{
            echo "<div class = 'alert alert-warning'>Campos vacios</div>";
        }
    }
?>