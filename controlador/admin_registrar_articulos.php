<?php
if (!empty($_POST["btn-up"])) { // Si el botón está presionado
    if (
        !empty($_POST["Nombre_producto"]) &&
        !empty($_POST["Descripcion"]) &&
        !empty($_POST["Cantidad"]) &&
        !empty($_POST["Precio"]) &&
        !empty($_POST["Marca"]) &&
        !empty($_POST["Modelo"]) &&
        !empty($_POST["Categoria_producto"])
    ) { // Validar que el usuario ingrese los datos requeridos
        $Nombre_producto = $_POST["Nombre_producto"];
        $Descripcion = $_POST["Descripcion"];
        $Cantidad = $_POST["Cantidad"];
        $Precio = $_POST["Precio"];
        $Marca = $_POST["Marca"];
        $Modelo = $_POST["Modelo"];
        $Categoria_producto = $_POST["Categoria_producto"];
        $imagen = $_FILES["imagen"]["name"];
        $gif = $_FILES["gif"]["name"];

        // Ruta de destino para guardar la imagen
        $ruta_destino = "Imagenes_productos/" . $imagen;
        // Ruta de destino para guardar el gif
        $ruta_destino_gif = "gif/" . $gif;

        // Procesar la imagen solo si se ha subido correctamente
        if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] === UPLOAD_ERR_OK) {
            $imagen_temp = $_FILES["imagen"]["tmp_name"];
            // Mover la imagen al directorio de destino
            if (move_uploaded_file($imagen_temp, $ruta_destino)) {
                // Inicializar consulta SQL básica
                $sql_query = "INSERT INTO producto (Nombre_producto, Descripcion, Cantidad, Precio, Marca, Modelo, Categoria_producto, imagen";

                // Añadir campo gif si se ha subido un archivo GIF
                if (isset($_FILES["gif"]) && $_FILES["gif"]["error"] === UPLOAD_ERR_OK) {
                    $gif_temp = $_FILES["gif"]["tmp_name"];
                    if (move_uploaded_file($gif_temp, $ruta_destino_gif)) {
                        $sql_query .= ", gif) VALUES ('$Nombre_producto', '$Descripcion', '$Cantidad', '$Precio', '$Marca', '$Modelo', '$Categoria_producto', '$imagen', '$gif')";
                    } else {
                        echo '<div class="alert">Error al mover el GIF al directorio de destino</div>';
                        exit;
                    }
                } else {
                    // Completar la consulta SQL sin el campo gif
                    $sql_query .= ") VALUES ('$Nombre_producto', '$Descripcion', '$Cantidad', '$Precio', '$Marca', '$Modelo', '$Categoria_producto', '$imagen')";
                }

                // Ejecutar la consulta SQL
                $sql = $connection->query($sql_query);
                if ($sql == 1) {
                    echo '<div class="success">Articulo registrado correctamente</div>';
                } else {
                    echo '<div class="alert">El producto no se pudo registrar correctamente</div>';
                }
            } else {
                echo '<div class="alert">Error al mover la imagen al directorio de destino</div>';
            }
        } else {
            // Manejar el error de carga de archivos imagen aquí si es necesario
            switch ($_FILES["imagen"]["error"]) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    echo "El tamaño del archivo de imagen es demasiado grande";
                    break;
                case UPLOAD_ERR_PARTIAL:
                    echo "El archivo de imagen no se subió completamente";
                    break;
                case UPLOAD_ERR_NO_FILE:
                    echo "No se seleccionó ningún archivo de imagen para subir";
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    echo "Falta la carpeta temporal para el archivo de imagen";
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    echo "Error al escribir el archivo de imagen en el disco";
                    break;
                case UPLOAD_ERR_EXTENSION:
                    echo "La subida de archivos de imagen fue detenida por una extensión de PHP";
                    break;
                default:
                    echo "Error al subir la imagen. Código de error: " . $_FILES["imagen"]["error"];
            }
        }
    } else {
        echo '<div class="alert alert-danger">Los campos están vacíos</div>';
    }
}
?>
