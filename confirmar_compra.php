<?php
session_start();
include 'connection.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['idCliente'])) {
    header('Location: login.php');
    exit();
}

$idCliente = $_SESSION['idCliente'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idDireccionSeleccionada = isset($_POST['direccion']) ? $_POST['direccion'] : null;
    $idTarjetaSeleccionada = isset($_POST['tarjeta']) ? $_POST['tarjeta'] : null;
    $idPaypalSeleccionada = isset($_POST['paypal']) ? $_POST['paypal'] : null;

    // Verificar los valores recibidos
    if ($idDireccionSeleccionada == 0 || $idDireccionSeleccionada === null) {
        die('Error: Debes seleccionar una dirección válida.');
    }

    if (($idTarjetaSeleccionada == 0 && $idPaypalSeleccionada == 0) || ($idTarjetaSeleccionada === null && $idPaypalSeleccionada === null)) {
        die('Error: Debes seleccionar un método de pago válido.');
    }

    // Obtener la dirección de envío seleccionada
    $sql = "SELECT Direccion, Codigo_Postal, Colonia, Ciudad FROM direccion WHERE idCliente = ? AND idDireccion = ?";
    $stmt = $connection->prepare($sql);
    if ($stmt === false) {
        die('Error en la preparación de la consulta: ' . $connection->error);
    }
    $stmt->bind_param('ii', $idCliente, $idDireccionSeleccionada);
    $stmt->execute();
    $result = $stmt->get_result();
    $direccion = $result->fetch_assoc();
    $stmt->close();

    if (!$direccion) {
        die('Error: Dirección no encontrada.');
    }

    // Determinar el método de pago seleccionado
    if ($idTarjetaSeleccionada) {
        $sql = "SELECT numero_tarjeta FROM tarjetas_credito WHERE idCliente = ? AND idTarjeta = ?";
        $stmt = $connection->prepare($sql);
        if ($stmt === false) {
            die('Error en la preparación de la consulta: ' . $connection->error);
        }
        $stmt->bind_param('ii', $idCliente, $idTarjetaSeleccionada);
        $stmt->execute();
        $result = $stmt->get_result();
        $tarjeta = $result->fetch_assoc();
        $stmt->close();

        if (!$tarjeta) {
            die('Error: Tarjeta no encontrada.');
        }

        $metodo_pago = 'Tarjeta: ' . $tarjeta['numero_tarjeta'];
    } else {
        $sql = "SELECT email FROM paypal_emails WHERE idCliente = ? AND idEmail = ?";
        $stmt = $connection->prepare($sql);
        if ($stmt === false) {
            die('Error en la preparación de la consulta: ' . $connection->error);
        }
        $stmt->bind_param('ii', $idCliente, $idPaypalSeleccionada);
        $stmt->execute();
        $result = $stmt->get_result();
        $paypal = $result->fetch_assoc();
        $stmt->close();

        if (!$paypal) {
            die('Error: Cuenta PayPal no encontrada.');
        }

        $metodo_pago = 'PayPal: ' . $paypal['email'];
    }

    // Obtener los productos en el carrito del cliente
    $sql = "SELECT carrito.idCarrito, carrito.idProducto, producto.Precio, carrito.Cantidad 
            FROM carrito
            JOIN producto ON carrito.idProducto = producto.idProducto
            WHERE carrito.idCliente = ?";
    $stmt = $connection->prepare($sql);
    if ($stmt === false) {
        die('Error en la preparación de la consulta: ' . $connection->error);
    }
    $stmt->bind_param('i', $idCliente);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $idCarrito = $row['idCarrito'];
        $idProducto = $row['idProducto'];
        $precio = $row['Precio'];
        $cantidad = $row['Cantidad'];
        $direccion_envio = $direccion['Direccion'] . ", " . $direccion['Colonia'] . ", " . $direccion['Ciudad'] . ", CP: " . $direccion['Codigo_Postal'];

        $sqlInsert = "INSERT INTO compras (idCliente, idProducto, cantidad, precio, metodo_pago, direccion_envio, idCarrito) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmtInsert = $connection->prepare($sqlInsert);
        if ($stmtInsert === false) {
            die('Error en la preparación de la consulta: ' . $connection->error);
        }
        $stmtInsert->bind_param('iiidssi', $idCliente, $idProducto, $cantidad, $precio, $metodo_pago, $direccion_envio, $idCarrito);
        if (!$stmtInsert->execute()) {
            die('Error al insertar compra: ' . $stmtInsert->error);
        }
        $stmtInsert->close();

        // Restar la cantidad de artículos de la tabla de productos
        $sqlUpdate = "UPDATE producto SET Cantidad = Cantidad - ? WHERE idProducto = ?";
        $stmtUpdate = $connection->prepare($sqlUpdate);
        if ($stmtUpdate === false) {
            die('Error en la preparación de la consulta: ' . $connection->error);
        }
        $stmtUpdate->bind_param('ii', $cantidad, $idProducto);
        if (!$stmtUpdate->execute()) {
            die('Error al actualizar cantidad de producto: ' . $stmtUpdate->error);
        }
        $stmtUpdate->close();
    }

    // Vaciar el carrito del cliente
    $sql = "DELETE FROM carrito WHERE idCliente = ?";
    $stmt = $connection->prepare($sql);
    if ($stmt === false) {
        die('Error en la preparación de la consulta: ' . $connection->error);
    }
    $stmt->bind_param('i', $idCliente);
    if (!$stmt->execute()) {
        die('Error al vaciar el carrito: ' . $stmt->error);
    }
    $stmt->close();

    $_SESSION['message'] = '¡Gracias por tu compra!';
    header('Location: confirmar_compra.php');
    exit();
}

// Obtener los productos en el carrito del cliente para mostrar en la página
$sql = "SELECT carrito.idProducto, producto.Nombre_producto, producto.Precio, producto.imagen, SUM(carrito.Cantidad) as Cantidad
        FROM carrito
        JOIN producto ON carrito.idProducto = producto.idProducto
        WHERE carrito.idCliente = ?
        GROUP BY carrito.idProducto";
$stmt = $connection->prepare($sql);
$stmt->bind_param('i', $idCliente);
$stmt->execute();
$result = $stmt->get_result();

// Obtener el mensaje de sesión si existe
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
if ($message) {
    unset($_SESSION['message']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Confirmación de Compra</title>
    <!-- Google font -->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,700" rel="stylesheet">
    <!-- Bootstrap -->
    <link type="text/css" rel="stylesheet" href="css/bootstrap.min.css" />
    <!-- Slick -->
    <link type="text/css" rel="stylesheet" href="css/slick.css" />
    <link type="text/css" rel="stylesheet" href="css/slick-theme.css" />
    <!-- nouislider -->
    <link type="text/css" rel="stylesheet" href="css/nouislider.min.css" />
    <!-- Font Awesome Icon -->
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <!-- Custom stlylesheet -->
    <link type="text/css" rel="stylesheet" href="css/style.css" />
</head>
<body>
    <!-- HEADER -->
    <header>
        <!-- TOP HEADER -->
        <div id="top-header">
            <div class="container">
                <ul class="header-links pull-left">
                    <li><a href="#"><i class="fa fa-phone"></i> +021-95-51-84</a></li>
                    <li><a href="#"><i class="fa fa-envelope-o"></i> email@email.com</a></li>
                    <li><a href="#"><i class="fa fa-map-marker"></i> FCBIyT</a></li>
                </ul>
                <ul class="header-links pull-right">
                    <?php if (isset($_SESSION['idCliente'])): ?>
                        <li><a href="cuenta_user.php"><i class="fa fa-user-o"></i> <?php echo htmlspecialchars($_SESSION['Nombres_cliente']); ?></a></li>
                        <li><a href="controlador/cerrar_sesion.php"><i class="fa fa-sign-out-alt"></i> Cerrar sesión</a></li>
                    <?php else: ?>
                        <li><a href="./login.php"><i class="fa fa-user-o"></i> Mi cuenta</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        <!-- /TOP HEADER -->

        <!-- MAIN HEADER -->
        <div id="header">
            <div class="container">
                <div class="row">
                    <!-- LOGO -->
                    <div class="col-md-4">
                        <div class="header-logo">
                            <a href="index.php" class="logo" target="_self">
                                <img src="./img/GCCIMG.png" width="50%">
                            </a>
                        </div>
                    </div>
                    <!-- /LOGO -->

                    <div class="col-md-5">
                        <div class="header-search">
                            <form>
                                <input class="input" placeholder="Busca aquí" name="query">
                                <button class="search-btn">Buscar</button>
                            </form>
                        </div>
                    </div>

                    <div class="col-md-3 clearfix">
                        <div class="header-ctn">
                            <!-- Cart -->
                            <div class="dropdown">
                                <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                                    <i class="fa fa-shopping-cart"></i>
                                    <span>Carrito</span>
                                    <?php
            $total_items = 0;
            $subtotal = 0.0;
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $total_items += $row['Cantidad'];
                    $subtotal += $row['Cantidad'] * $row['Precio'];
                }
            }
            ?>
                                    <div class="qty"><?php echo $total_items; ?></div>
                                </a>
                                <div class="cart-dropdown">
                                    <div class="cart-list" id="cart-list">
                                        <?php
                if ($result && $result->num_rows > 0) {
                    mysqli_data_seek($result, 0); // Reset the result pointer
                    while ($row = $result->fetch_assoc()) {
                        $rutaImagen = 'Imagenes_productos/' . $row['imagen'];
                        echo '<div class="product-widget" data-id-producto="' . $row['idProducto'] . '" data-precio="' . $row['Precio'] . '">
                            <div class="product-img">
                                <img src="' . $rutaImagen . '" alt="">
                            </div>
                            <div class="product-body">
                                <h3 class="product-name"><a href="#">' . $row['Nombre_producto'] . '</a></h3>
                                <h4 class="product-price">
                                    <span class="qty">' . $row['Cantidad'] . 'x</span>
                                    $' . number_format($row['Precio'], 2, '.', ',') . '
                                </h4>
                                <div>
                                    <span>Total cantidad: </span>
                                    <input type="text" value="' . $row['Cantidad'] . '" disabled class="total-cantidad">
                                </div>
                                <div>
                                    <span>Eliminar cantidad: </span>
                                    <input type="number" min="1" max="' . $row['Cantidad'] . '" value="1" class="cantidad-eliminar">
                                </div>
                            </div>
                            <button class="delete" onclick="eliminarDelCarrito(' . $row['idProducto'] . ', this.previousElementSibling.querySelector(\'.cantidad-eliminar\').value)">
                                <i class="fa fa-close"></i>
                            </button>
                        </div>';    
                    }
                } else {
                    echo '<p>No hay productos en el carrito.</p>';
                }
                ?>
                                    </div>

                                    <div class="cart-summary">
                                        <small><span id="cart-item-count"><?php echo $total_items; ?></span> Item(s)
                                            seleccionados</small>
                                        <h5>SUBTOTAL: $<span
                                                id="cart-subtotal"><?php echo number_format($subtotal, 2, '.', ','); ?></span>
                                        </h5>
                                    </div>

                                    <div class="cart-btns">
                                        <a href="carrito.php">Ver carrito</a>
                                        <a href="carrito.php">Orden de pago <i class="fa fa-arrow-circle-right"></i></a>
                                    </div>
                                </div>
                            </div>
                            <!-- /Cart -->

                            <!-- Menu Toogle -->
                            <div class="menu-toggle">
                                <a href="#">
                                    <i class="fa fa-bars"></i>
                                    <span>Menu</span>
                                </a>
                            </div>
                            <!-- /Menu Toogle -->
                        </div>
                    </div>
                    <!-- /ACCOUNT -->
                </div>
                <!-- row -->
            </div>
            <!-- container -->
        </div>
        <!-- /MAIN HEADER -->
    </header>
    <!-- /HEADER -->

    <!-- NAVIGATION -->
    <nav id="navigation">
        <!-- container -->
        <div class="container">
            <!-- responsive-nav -->
            <div id="responsive-nav">
                <!-- NAV -->
                <ul class="main-nav nav navbar-nav">
                    <li class="active"><a href="#">Inicio</a></li>
                    <li><a href="cuenta_user.php">Mi cuenta</a></li>
                    <li><a href="direccion.php">Direccion de envio</a></li>
                    <li><a href="compras.php">Pedido</a></li>
                    <li><a href="metodos_pago.php">Metodos de pago</a></li>
                </ul>
                <!-- /NAV -->
            </div>
            <!-- /responsive-nav -->
        </div>
        <!-- /container -->
    </nav>
    <!-- /NAVIGATION -->

    <!-- SECTION -->
    <div class="section">
        <!-- container -->
        <div class="container">
            <!-- row -->
            <div class="row">
                <div class="col-md-12">
                    <?php if ($message): ?>
                        <h1 class="text-center"><?php echo $message; ?></h1>
                        <p class="text-center">Tu pedido ha sido realizado con éxito.</p>
                    <?php else: ?>
                        <h1 class="text-center">Confirmación de Compra</h1>
                        <p class="text-center">Revisa los detalles de tu compra a continuación.</p>
                    <?php endif; ?>
                </div>
                <!-- /row -->
            </div>
            <!-- /container -->
        </div>
        <!-- /SECTION -->

        <!-- jQuery Plugins -->
        <script src="js/jquery.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/slick.min.js"></script>
        <script src="js/nouislider.min.js"></script>
        <script src="js/jquery.zoom.min.js"></script>
        <script src="js/main.js"></script>
        <script>
        setTimeout(function() {
            window.location.href = 'index.php'; // Reemplaza con la URL a la que deseas redirigir
        }, 5000); // 5000 milisegundos = 5 segundos
        </script>
</body>
</html>
