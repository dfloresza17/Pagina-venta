<?php
    if(!empty($_POST["btn-login"])){
        if(empty($_POST["Correo_cliente"]) and empty($_POST["Password_cliente"])){
            echo '<div class="alert alert-danger">Los campos están vacíos</div>';
        } else {
            $Correo_cliente = $_POST["Correo_cliente"];
            $Password_cliente = $_POST["Password_cliente"];

            $sql = $connection->query("SELECT * FROM cliente WHERE Correo_cliente = '$Correo_cliente' AND Password_cliente = '$Password_cliente'");

            if($datos = $sql->fetch_object()){
                // Inicio de sesión exitoso, establecer variables de sesión con la información del usuario
                session_start();
                $_SESSION['idCliente'] = $datos->idCliente;
                $_SESSION['Nombres_cliente'] = $datos->Nombres_cliente;
                header("location:index.php");
                exit();
            } else {
                echo '<div class="alert alert-danger">Acceso denegado</div>';
            }
        }
    }
?>
