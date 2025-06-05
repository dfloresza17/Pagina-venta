<?php
    // Incluir el archivo de conexión a la base de datos
    include "connection.php";

    // Verificar si se ha enviado el formulario de modificación
    if(isset($_POST['btn-modificar-datos'])) {
        // Recuperar los datos del formulario
        $Correo_cliente = $_POST['Correo_cliente'];
        $Nombre_usuario = $_POST['Nombres_cliente'];
        $Apellido_paterno = $_POST['Apellido_paterno'];
        $Apellido_materno = $_POST['Apellido_materno'];
        
        // Consulta SQL para actualizar los datos del usuario en la base de datos
        $sql = "UPDATE cliente SET Nombres_cliente='$Nombre_usuario', Apellido_paterno='$Apellido_paterno', Apellido_materno='$Apellido_materno' WHERE Correo_cliente='$Correo_cliente'";

        // Ejecutar la consulta
        if(mysqli_query($connection, $sql)) {
            // Los datos se han actualizado correctamente
            echo "<script>alert('Los datos se han actualizado correctamente.'); /script>";

            
        } else {
            // Si hay un error en la consulta
            echo '<div class="alert alert-danger">Error al actualizar los datos: ' . mysqli_error($connection) . '</div>';
        }
        echo "<script>alert('Datos modificados correctamente.');</script>";

        header("Location:cuenta_user.php");
    }



    // Verificar si se ha enviado el formulario para eliminar datos
    if (isset($_POST['btn-eliminar-datos'])) {
        $idCliente = $_POST["idCliente"];
        $Nombres_cliente = $_POST["Nombres_cliente"];
        $Apellido_paterno = $_POST["Apellido_paterno"];
        $Apellido_materno = $_POST["Apellido_materno"];
        $Telefono_cliente = $_POST["Telefono_cliente"];
        $Correo_cliente = $_POST["Correo_cliente"];
        $Password_cliente = $_POST["Password_cliente"];
        

        // Verificar si ya existe una solicitud de eliminación para este cliente
        $check_sql = $connection->prepare("SELECT * FROM solicitud_borrar_datos WHERE idCliente = ?");
        $check_sql->bind_param("i", $idCliente);
        $check_sql->execute();
        $check_sql->store_result();

        if ($check_sql->num_rows > 0) {
            echo "<script>alert('Ya existe una solicitud de eliminación para el usuario $Nombres_cliente'); window.open('cuenta_user.php', '_self');</script>";
        } else {
            // Insertar la solicitud de eliminación en la base de datos
            $insert_sql = $connection->prepare("INSERT INTO solicitud_borrar_datos (idCliente, Nombres_cliente, Apellido_paterno, Apellido_materno, Telefono_cliente, Correo_cliente, Password_cliente) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $insert_sql->bind_param("issssss", $idCliente, $Nombres_cliente, $Apellido_paterno, $Apellido_materno, $Telefono_cliente, $Correo_cliente, $Password_cliente);

            if ($insert_sql->execute()) {
                echo "<script>alert('$Nombres_cliente serás eliminado (a) posteriormente'); window.open('cuenta_user.php', '_self');</script>";
            } else {
                echo '<div class="alert">La solicitud NO se pudo registrar correctamente</div>';
            }

            $insert_sql->close();
        }

        $check_sql->close();
        $connection->close();
    }
?>


