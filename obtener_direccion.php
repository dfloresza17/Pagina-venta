<?php
include 'connection.php';

if (isset($_POST['idDireccion'])) {
    $idDireccion = $_POST['idDireccion'];

    $sql = "SELECT Direccion, Codigo_Postal, Colonia, Ciudad, Correo_cliente, Telefono_cliente FROM direccion WHERE idDireccion = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param('i', $idDireccion);
    $stmt->execute();
    $result = $stmt->get_result();
    $direccion = $result->fetch_assoc();

    echo json_encode($direccion);
}
?>
