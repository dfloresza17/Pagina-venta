<?php

if(!empty($_POST["btn-agregar"])){ //Si el boton esta presionado
    if(empty($_POST["direccion"]) or empty($_POST["cp"]) or empty($_POST["colonia"]) or empty($_POST["ciudad"]) or empty($_POST["email"]) or empty($_POST["telefono"])){ //Validar que el usuario ingrese los datos requeridos
        echo '<div class="alert">Uno de los campos esta vacios</div>';
    }else{
        $direccion = $_POST["direccion"];
        $cp = $_POST["cp"];
        $colonia = $_POST["colonia"];
        $ciudad = $_POST["ciudad"];
        $email = $_POST["email"];
        $telefono = $_POST["telefono"];

        //$pass_encript = password_hash($Password_cliente, PASSWORD_DEFAULT);

        $sql = $connection->query("INSERT INTO dirreccion (direccion, cp, colonia, ciudad, email, telefono) VALUES ('$direccion', '$cp', '$colonia', '$ciudad', '$email', '$telefono')");

        if($sql == 1){
            echo '<div class="success">Usuario registrado correctamente</div>';
            echo "<script>alert('Usuario registrado correctamente'); window.open('login.php', '_self');</script>";
        }else{
            echo '<div class="alert">El usuario NO se pudo registrar correctamente</div>';
        }
        
    }
}
?>