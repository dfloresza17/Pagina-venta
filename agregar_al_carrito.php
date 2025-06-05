// agregar_al_carrito.php
<?php
include "connection.php"; // Incluir la conexión a la base de datos
session_start();

if (isset($_POST['agregar_al_carrito'])) {
    $idCliente = $_POST['idCliente'];
    $idProducto = $_POST['idProducto'];
    $cantidad = intval($_POST['cantidad']);

    // Verificar la cantidad en stock del producto
    $query = "SELECT Cantidad FROM producto WHERE idProducto = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param('i', $idProducto);
    $stmt->execute();
    $result = $stmt->get_result();
    $producto = $result->fetch_assoc();

    if (!$producto) {
        $_SESSION['message'] = "Producto no encontrado.";
        header("Location: producto.php?producto_id=$idProducto");
        exit();
    }

    $cantidadEnStock = intval($producto['Cantidad']);

    // Verificar si el producto ya está en el carrito
    $query = "SELECT Cantidad FROM carrito WHERE idCliente = ? AND idProducto = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param('ii', $idCliente, $idProducto);
    $stmt->execute();
    $result = $stmt->get_result();
    $carrito = $result->fetch_assoc();

    if ($carrito) {
        $cantidadEnCarrito = intval($carrito['Cantidad']);
        $nuevaCantidad = $cantidadEnCarrito + $cantidad;

        if ($nuevaCantidad > $cantidadEnStock) {
            $_SESSION['message'] = "No hay suficiente cantidad en stock.";
            header("Location: producto.php?producto_id=$idProducto");
            exit();
        }

        // Actualizar la cantidad en el carrito
        $query = "UPDATE carrito SET Cantidad = ? WHERE idCliente = ? AND idProducto = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param('iii', $nuevaCantidad, $idCliente, $idProducto);
        $stmt->execute();
    } else {
        if ($cantidad > $cantidadEnStock) {
            $_SESSION['message'] = "No hay suficiente cantidad en stock.";
            header("Location: producto.php?producto_id=$idProducto");
            exit();
        }

        // Insertar el producto en el carrito
        $query = "INSERT INTO carrito (idCliente, idProducto, Cantidad) VALUES (?, ?, ?)";
        $stmt = $connection->prepare($query);
        $stmt->bind_param('iii', $idCliente, $idProducto, $cantidad);
        $stmt->execute();
    }

    $_SESSION['message'] = "Producto agregado al carrito.";
    header("Location: producto.php?producto_id=$idProducto");
    exit();
} else {
    $_SESSION['message'] = "Acceso no autorizado.";
    header("Location: index.php");
    exit();
}
?>
