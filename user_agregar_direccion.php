<?php
    if(!empty($_POST['btn-guardar'])){
        if(!empty($_POST['Direccion']) && !empty($_POST['Codigo_Postal']) && !empty($_POST['Colonia']) && !empty($_POST['Ciudad'])){
            $idCliente = $_POST["idCliente"];
            $Direccion = $_POST["Direccion"];
            $Codigo_Postal = $_POST["Codigo_Postal"];
            $Colonia = $_POST["Colonia"];
            $Ciudad = $_POST["Ciudad"];
            $Correo_cliente = $_POST["Correo_cliente"];
            $Telefono_cliente = $_POST["Telefono_cliente"];

            $sql = $connection->query("INSERT INTO direccion (Direccion, Codigo_Postal, Colonia, Ciudad, Correo_cliente, Telefono_cliente, idCliente) VALUES ('$Direccion', '$Codigo_Postal', '$Colonia', '$Ciudad', '$Correo_cliente', $Telefono_cliente, '$idCliente')");

            if ($sql == 1) {
                echo '<div class="success">Direccion agregada correctamente</div>';
            } else {
                echo '<div class="alert">La direccion no se pudo agregar</div>';
            }
        }else{
            echo '<div class="alert alert-danger">Los campos están vacíos</div>';
        }
    }
?>