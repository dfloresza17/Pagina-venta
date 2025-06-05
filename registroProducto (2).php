<?php
include '../../../controller/conexion.php';

// Obtener los valores del formulario
$nombre = $_POST['nombre'];
$edad = $_POST['edad'];
$paisorigen = $_POST['paisorigen'];
$material = $_POST['material'];
$marca = $_POST['marca'];
$stock=$_POST['stock'];
$precio_compra=$_POST['precio_compra'];
$precio_venta=$_POST['precio_venta'];

// Procesar la imagen solo si se ha subido correctamente
if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    $imagen = $_FILES['imagen']['name'];
    $imagen_temp = $_FILES['imagen']['tmp_name'];
    $ruta_imagen = '/assets/products/' . $imagen;
    move_uploaded_file($imagen_temp, $ruta_imagen);
} else {
    // Manejar el error de carga de archivos aquí si es necesario
    switch ($_FILES['imagen']['error']) {
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            echo "El tamaño del archivo es demasiado grande";
            break;
        case UPLOAD_ERR_PARTIAL:
            echo "El archivo no se subió completamente";
            break;
        case UPLOAD_ERR_NO_FILE:
            echo "No se seleccionó ningún archivo para subir";
            break;
        case UPLOAD_ERR_NO_TMP_DIR:
            echo "Falta la carpeta temporal";
            break;
        case UPLOAD_ERR_CANT_WRITE:
            echo "Error al escribir el archivo en el disco";
            break;
        case UPLOAD_ERR_EXTENSION:
            echo "La subida de archivos fue detenida por una extensión de PHP";
            break;
        default:
            echo "Error al subir la imagen. Código de error: " . $_FILES['imagen']['error'];
    }
    exit();
}

    
    // Query para insertar los datos en la base de datos
    $query = "INSERT INTO productos (nombre_producto, fecha_fabricacion, material, precio_compra, precio_venta, edad_recomendada, pais_origen, stock, descuento, proveedores, sucursal, marcas, imagen) VALUES ('$nombre', 0 ,'$material', '$precio_compra', '$precio_venta', '$edad', '$paisorigen', '$stock', 0, NULL, NULL, '$marca', '$ruta_imagen')";
    
    
    $resultado=$conecta->query($query);
    if($resultado){
        echo "Datos registrados";
    }else{
        echo '<script language="javascript">alert("Contraseñas no coinciden");</script>';
    }

?>