<?php
include 'connection.php';

if (isset($_POST['idTarjeta'])) {
    $idTarjeta = $_POST['idTarjeta'];

    $sql = "SELECT nombre_titular, numero_tarjeta, fecha_vencimiento, codigo_seguridad FROM tarjetas_credito WHERE idTarjeta = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param('i', $idTarjeta);
    $stmt->execute();
    $result = $stmt->get_result();
    $tarjeta = $result->fetch_assoc();

    echo json_encode($tarjeta);
}
?>
