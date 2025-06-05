<?php
    if(!empty($_POST["btn-login"])){
        if(empty($_POST["Correo_admin"]) and empty($_POST["Password_admin"])){
            echo '<div class = "alert alert-danger"> Los campos estan vacios </div>';
        }else{
            $Correo_admin = $_POST["Correo_admin"];
            $Password_admin = $_POST["Password_admin"];

            $sql = $connection -> query("SELECT * FROM administrador WHERE Correo_admin =  '$Correo_admin' AND Password_admin = '$Password_admin'");

            if($datos = $sql -> fetch_object()){
                header("location:home_adm.php");
            }else{
                echo '<div class = "alert alert-danger"> Acceso denegado </div>';
            }

        }
    }
?>