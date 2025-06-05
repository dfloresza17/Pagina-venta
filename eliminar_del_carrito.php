<?php
session_start();
include 'connection.php';

if (isset($_SESSION['idCliente']) && isset($_POST['idProducto']) && isset($_POST['cantidad'])) {
    $idCliente = $_SESSION['idCliente'];
    $idProducto = $_POST['idProducto'];
    $cantidadEliminar = $_POST['cantidad'];

    // Comprobar la cantidad actual en el carrito
    $sql = "SELECT Cantidad FROM carrito WHERE idCliente = ? AND idProducto = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param('ii', $idCliente, $idProducto);
    $stmt->execute();
    $stmt->bind_result($cantidadActual);
    $stmt->fetch();
    $stmt->close();

    if ($cantidadActual !== null) {
        if ($cantidadEliminar >= $cantidadActual) {
            // Eliminar el producto del carrito
            $sql = "DELETE FROM carrito WHERE idCliente = ? AND idProducto = ?";
            $stmt = $connection->prepare($sql);
            $stmt->bind_param('ii', $idCliente, $idProducto);
        } else {
            // Reducir la cantidad del producto en el carrito
            $nuevaCantidad = $cantidadActual - $cantidadEliminar;
            $sql = "UPDATE carrito SET Cantidad = ? WHERE idCliente = ? AND idProducto = ?";
            $stmt = $connection->prepare($sql);
            $stmt->bind_param('iii', $nuevaCantidad, $idCliente, $idProducto);
        }

        if ($stmt->execute()) {
            echo "Producto eliminado/actualizado correctamente";
        } else {
            echo "Error al eliminar/actualizar el producto";
        }

        $stmt->close();
    } else {
        echo "Producto no encontrado en el carrito";
    }

    $connection->close();
} else {
    echo "Datos incompletos";
}
?>
