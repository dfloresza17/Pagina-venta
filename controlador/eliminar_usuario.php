<?php
if (!empty($_GET["idCliente"])) {
    $idCliente = $_GET["idCliente"];
    
    // Verificar que $idCliente sea un número para evitar inyecciones SQL
    if (is_numeric($idCliente)) {
        $sql = "
            DELETE FROM carrito WHERE idCliente = $idCliente;
            DELETE FROM compras WHERE idCliente = $idCliente;
            DELETE FROM direccion WHERE idCliente = $idCliente;
            DELETE FROM paypal_emails WHERE idCliente = $idCliente;
            DELETE FROM cliente WHERE idCliente = $idCliente;
        ";
        
        if ($connection->multi_query($sql)) {
            do {
                /* store first result set */
                if ($result = $connection->store_result()) {
                    $result->free();
                }
            } while ($connection->more_results() && $connection->next_result());

            echo "<div class='alert alert-success'>Cliente borrado de compras correctamente</div>";
        } else {
            echo "<div class='alert alert-danger'>Error al eliminar cliente: " . $connection->error . "</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>ID de cliente no válido</div>";
    }
}
?>
