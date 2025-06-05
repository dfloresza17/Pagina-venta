<?php
// Incluir el archivo de conexión a la base de datos
include 'connection.php';

// Consultar la base de datos para obtener los productos en el carrito
$sql = "SELECT c.idCarrito, c.idCliente, c.idProducto, c.Cantidad, p.Precio 
        FROM carrito c 
        INNER JOIN producto p ON c.idProducto = p.idProducto";
$result = $connection->query($sql);

// Generar el HTML para mostrar los productos en el carrito
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Aquí generas el HTML para cada producto en el carrito
        echo '<div class="product-widget" data-id-producto="' . $row['idProducto'] . '" data-precio="' . $row['Precio'] . '">
                <!-- Código HTML para mostrar el producto en el carrito -->
              </div>';
    }
} else {
    // Si no hay productos en el carrito, mostrar un mensaje indicándolo
    echo '<p>No hay productos en el carrito.</p>';
}
?>
