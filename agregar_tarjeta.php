<?php
session_start();
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $idCliente = $_SESSION['idCliente'];
    $nombre_titular = $_POST['nombre'];
    $numero = $_POST['numero'];
    $fecha_vencimiento = $_POST['fecha_vencimiento'];
    $cvv = $_POST['cvv'];

    // Obtener el mes y el año de la fecha de vencimiento ingresada
    $fecha_vencimiento_array = explode('/', $_POST['fecha_vencimiento']);
    $mes = $fecha_vencimiento_array[0];
    $anio = $fecha_vencimiento_array[1];

    // Formatear la fecha de vencimiento en el formato YYYY-MM-DD
    $fecha_vencimiento = date("Y-m-t", strtotime("20$anio-$mes"));

    // Preparar la consulta SQL para insertar la tarjeta de crédito
    $sql = "INSERT INTO tarjetas_credito (idCliente, numero_tarjeta, fecha_vencimiento, nombre_titular, codigo_seguridad) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $connection->prepare($sql);

    // Verificar si la preparación de la consulta fue exitosa
    if ($stmt === false) {
        die("Error en la consulta: " . mysqli_error($connection));
    }

    // Vincular parámetros y ejecutar la consulta
    $stmt->bind_param('issss', $idCliente, $numero, $fecha_vencimiento, $nombre_titular, $cvv);
    if ($stmt->execute()) {
        $_SESSION['message'] = "Tarjeta de crédito registrada correctamente.";
    } else {
        $_SESSION['message'] = "Error al registrar la tarjeta de crédito: " . $stmt->error;
    }

    // Redirigir a la misma página para mostrar el mensaje en un alert
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Verificar si hay un mensaje en la sesión y mostrarlo en un alert
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
    echo "<script>
        alert('$message');
    </script>";
}

// Verificar si el usuario ha iniciado sesión
if (isset($_SESSION['idCliente'])) {
    $idCliente = $_SESSION['idCliente'];

    // Consulta SQL para obtener los productos en el carrito del cliente actual agrupados por idProducto
    $sql = "SELECT carrito.idProducto, producto.Nombre_producto, producto.Precio, producto.imagen, SUM(carrito.Cantidad) as Cantidad
            FROM carrito
            JOIN producto ON carrito.idProducto = producto.idProducto
            WHERE carrito.idCliente = ?
            GROUP BY carrito.idProducto";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param('i', $idCliente);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = null;
}

// Cerrar la consulta y la conexión
$stmt->close();
$connection->close();
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
    <script src="https://kit.fontawesome.com/d438250857.js" crossorigin="anonymous"></script>

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
                    <li><a href="cuenta_user.php"><i class="fa fa-user-o"></i>
                            <?php echo htmlspecialchars($_SESSION['Nombres_cliente']); ?></a></li>
                    <li><a href="controlador/cerrar_sesion.php"><i class="fa fa-sign-out-alt"></i> Cerrar sesión</a>
                    </li>
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
                    <li><a href="index.php">Inicio</a></li>
                    <li><a href="cuenta_user.php">Mi cuenta</a></li>
                    <li><a href="direccion.php">Direccion de envio</a></li>
                    <li><a href="compras.php">Pedidos</a></li>
                    <li class="active"><a href="metodos_pago.php">Metodos de pago</a></li>
                </ul>
                <!-- /NAV -->
            </div>
            <!-- /responsive-nav -->
        </div>
        <!-- /container -->
    </nav>
    <!-- /NAVIGATION -->

    <!-- container -->
    <div class="container">
        <!-- row -->
        <div class="row">
            <div class="col-md-12">
                <div class="newsletter">
                    <p>Agregar tarjeta de credito o debito</p>
                    <form action="#" method="POST">
                        <label for="nombre">Nombre en la Tarjeta:</label><br>
                        <input type="text" id="nombre" name="nombre" class="input_form" required><br><br>

                        <label for="numero">Número de Tarjeta:</label><br>
                        <input type="text" id="numero" name="numero" class="input_form" pattern="[0-9]{16}"
                            required><br><br>

                        <label for="fecha_vencimiento">Fecha de Vencimiento (MM/YY):</label><br>
                        <input type="text" id="fecha_vencimiento" name="fecha_vencimiento"
                            pattern="(0[1-9]|1[0-2])\/(2[5-9]|[3-9][0-9])" placeholder="MM/YY" class="input_form"
                            required><br><br>

                        <label for="cvv">CVV:</label><br>
                        <input type="text" id="cvv" name="cvv" pattern="[0-9]{3,4}" class="input_form" required><br><br>

                        <input type="submit" value="Enviar" class="btn-login">
                    </form>
                </div>
            </div>
        </div>
        <!-- /row -->
    </div>
    <!-- /container -->
    </div>
    <!-- /NEWSLETTER -->


    <!-- jQuery Plugins -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/slick.min.js"></script>
    <script src="js/nouislider.min.js"></script>
    <script src="js/jquery.zoom.min.js"></script>
    <script src="js/main.js"></script>

</body>

</html>