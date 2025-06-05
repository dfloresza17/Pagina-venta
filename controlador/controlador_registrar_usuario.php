<?php
// Incluir el archivo de conexión a la base de datos
include 'connection.php';

if (!empty($_POST["btn-registrar"])) { // Si el botón está presionado
    // Validar que todos los campos estén llenos
    if (empty($_POST["Nombres_cliente"]) || empty($_POST["Apellido_paterno"]) || empty($_POST["Apellido_materno"]) || empty($_POST["Telefono_cliente"]) || empty($_POST["Correo_cliente"]) || empty($_POST["Password_cliente"]) || empty($_POST["Confirmar_Password_cliente"])) {
        echo '<div class="alert alert-danger">Uno de los campos está vacío</div>';
    } else {
        // Asignar variables
        $Nombres_cliente = $_POST["Nombres_cliente"];
        $Apellido_paterno = $_POST["Apellido_paterno"];
        $Apellido_materno = $_POST["Apellido_materno"];
        $Telefono_cliente = $_POST["Telefono_cliente"];
        $Correo_cliente = $_POST["Correo_cliente"];
        $Password_cliente = $_POST["Password_cliente"];
        $Confirmar_Password_cliente = $_POST["Confirmar_Password_cliente"];
        
        // Verificar que las contraseñas coincidan
        if ($Password_cliente !== $Confirmar_Password_cliente) {
            echo '<div class="alert alert-danger">Las contraseñas no coinciden</div>';
        } else {
            // Verificar que el correo electrónico no esté ya registrado
            $sql_correo = $connection->query("SELECT * FROM cliente WHERE Correo_cliente='$Correo_cliente'");
            if ($sql_correo->num_rows > 0) {
                echo '<div class="alert alert-danger">El correo electrónico ya está registrado</div>';
            } else {
                // Insertar datos en la base de datos
                $sql = $connection->query("INSERT INTO cliente (Nombres_cliente, Apellido_paterno, Apellido_materno, Telefono_cliente, Correo_cliente, Password_cliente) VALUES ('$Nombres_cliente', '$Apellido_paterno', '$Apellido_materno', '$Telefono_cliente', '$Correo_cliente', '$Password_cliente')");

                if ($sql == 1) {
                    echo '<div class="alert alert-success">Usuario registrado correctamente</div>';
                    echo "<script>alert('Usuario $Nombres_cliente registrado correctamente'); window.open('login.php', '_self');</script>";
                } else {
                    echo '<div class="alert alert-danger">El usuario NO se pudo registrar correctamente</div>';
                }
            }
        }
    }
}
?>
