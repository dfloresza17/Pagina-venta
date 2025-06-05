<?php
session_start();
include 'connection.php';

$mensaje = null; // Inicializamos el mensaje a null

if (isset($_POST['idTarjeta']) && isset($_SESSION['idCliente'])) {
    $idTarjeta = $_POST['idTarjeta'];
    $idCliente = $_SESSION['idCliente'];

    // Verificar que la tarjeta pertenece al cliente y eliminarla
    $sql = "DELETE FROM tarjetas_credito WHERE idTarjeta = ? AND idCliente = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param('ii', $idTarjeta, $idCliente);

    if ($stmt->execute()) {
        $mensaje = "Tarjeta eliminada correctamente";
    } else {
        $mensaje = "Error al eliminar la tarjeta";
    }

    $stmt->close();
}

$stmt = null; // Inicializamos $stmt fuera de las condiciones para asegurarnos de que esté definido

if (isset($_SESSION['idCliente'])) {
    $idCliente = $_SESSION['idCliente'];

    // Consulta para obtener las tarjetas de crédito del cliente
    $sql = "SELECT idTarjeta, numero_tarjeta, fecha_vencimiento FROM tarjetas_credito WHERE idCliente = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param('i', $idCliente);
    $stmt->execute();
    $result = $stmt->get_result();

    $tarjetas = [];
    while ($row = $result->fetch_assoc()) {
        $tarjetas[] = $row;
    }

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

// Verificamos si $stmt está definido antes de cerrarlo
if ($stmt !== null) {
    $stmt->close();
}


// Eliminar el correo de PayPal seleccionado
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['idEmail'])) {
    $idEmail = intval($_POST['idEmail']);

    $sql = "DELETE FROM paypal_emails WHERE idEmail = ? AND idCliente = ?";
    $stmt = $connection->prepare($sql);
    if ($stmt === false) {
        die('Error en la preparación de la consulta: ' . $connection->error);
    }
    $stmt->bind_param('ii', $idEmail, $idCliente);
    if ($stmt->execute()) {
        $mensaje = "Correo de PayPal eliminado exitosamente.";
    } else {
        $mensaje = "Error al eliminar el correo de PayPal: " . $stmt->error;
    }
}

// Obtener los correos de PayPal del cliente actual
$sql = "SELECT idEmail, email FROM paypal_emails WHERE idCliente = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param('i', $idCliente);
$stmt->execute();
$result = $stmt->get_result();
$paypalEmails = [];
while ($row = $result->fetch_assoc()) {
    $paypalEmails[] = $row;
}


// Consulta para obtener los productos en el carrito
if (isset($_SESSION['idCliente'])) {
    $idCliente = $_SESSION['idCliente'];

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
$connection->close();


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Inicio</title>

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
    <link type="text/css" href="css/stylepers.css" />
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
                    <p>Método de pago</p>
                    <?php if ($mensaje): ?>
                    <div class="alert alert-info">
                        <?php echo htmlspecialchars($mensaje); ?>
                    </div>
                    <?php endif; ?>
                    <form id="form-eliminar-tarjeta" method="post" action="">
                        <label>
                            <ul class="footer-payments">
                                <li><a href="#"><i class="fa fa-credit-card"></i></a></li>
                                <select id="select-tarjeta" name="idTarjeta" class="input-select">
                                    <option value="0">Pago con Tarjeta (Débito o Crédito)</option>
                                    <?php
                foreach ($tarjetas as $tarjeta) {
                    $numero_tarjeta = $tarjeta['numero_tarjeta'];
                    $numero_oculto = substr($numero_tarjeta, 0, 4) . ' XXXX XXXX ' . substr($numero_tarjeta, -4);
                    $fecha_vencimiento = date("m/y", strtotime($tarjeta['fecha_vencimiento']));
                    echo "<option value='{$tarjeta['idTarjeta']}'>{$numero_oculto} {$fecha_vencimiento}</option>";
                }
                ?>
                                </select>
                                <button type="submit" class="btn btn-danger"><i
                                        class="fa-solid fa-trash-can"></i></button>
                            </ul>
                        </label>
                    </form>
                    <a href="agregar_tarjeta.php">
                        <button class="btn-login">Agregar nueva tarjeta</button><br>
                    </a>


                    <form id="" method="post" action="">
                        <label>
                            <ul class="footer-payments">
                                <li><a href="#"><i class="fa fa-cc-paypal"></i></a></li>
                                <select id="" name="idEmail" class="input-select">
                                    <option value="0">Seleccione un correo de PayPal</option>
                                    <?php foreach ($paypalEmails as $email): ?>
                                    <option value="<?php echo htmlspecialchars($email['idEmail']); ?>">
                                        <?php echo htmlspecialchars($email['email']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="btn btn-danger"><i
                                        class="fa-solid fa-trash-can"></i></button>
                            </ul>
                        </label>
                    </form>

                    <a href="agregar_paypal.php">
                        <button class="btn-login">Agregar cuenta Paypal</button><br>
                    </a>





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
    <!-- JavaScript -->
    <script>
    function agregarAlCarrito(idProducto, idCliente) {
        const formData = new FormData();
        formData.append('idProducto', idProducto);
        formData.append('idCliente', idCliente);
        formData.append('cantidad', 1);
        formData.append('agregar_al_carrito', true);

        fetch('agregar_al_carrito.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(result => {
                alert(result); // Mostrar mensaje del servidor
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }

    document.addEventListener('DOMContentLoaded', function() {
        function eliminarDelCarrito(idProducto) {
            const formData = new FormData();
            formData.append('idProducto', idProducto);

            fetch('eliminar_del_carrito.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(result => {
                    if (result.trim() === "Producto eliminado") {
                        // Eliminar el artículo del DOM
                        document.querySelector(`.product-widget[data-id-producto='${idProducto}']`)
                            .remove();

                        // Actualizar los totales del carrito
                        actualizarCarrito();
                    } else {
                        console.error(result);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        function actualizarCarrito() {
            // Recalcular el total de artículos y el subtotal
            let totalItems = 0;
            let subtotal = 0.0;

            document.querySelectorAll('.product-widget').forEach(widget => {
                const qtyElement = widget.querySelector('.qty');
                if (qtyElement) {
                    const qty = parseInt(qtyElement.textContent.replace('x', ''));
                    const price = parseFloat(widget.getAttribute('data-precio'));
                    totalItems += qty;
                    subtotal += qty * price;
                }
            });

            const qtyElement = document.querySelector('.qty');
            if (qtyElement) {
                qtyElement.textContent = totalItems;
            }
            const itemCountElement = document.querySelector('#cart-item-count');
            if (itemCountElement) {
                itemCountElement.textContent = totalItems;
            }
            const subtotalElement = document.querySelector('#cart-subtotal');
            if (subtotalElement) {
                subtotalElement.textContent = subtotal.toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }
        }

        window.eliminarDelCarrito = eliminarDelCarrito;
    });
    document.addEventListener('DOMContentLoaded', function() {
        function eliminarDelCarrito(idProducto, cantidad) {
            const formData = new FormData();
            formData.append('idProducto', idProducto);
            formData.append('cantidad', cantidad);

            fetch('eliminar_del_carrito.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(result => {
                    // Mostrar el mensaje del servidor
                    alert(result);

                    // Si el producto fue eliminado o la cantidad fue actualizada
                    if (result.includes('Producto eliminado') || result.includes('Cantidad actualizada')) {
                        // Actualizar la cantidad o eliminar el artículo del DOM
                        const productWidget = document.querySelector(
                            `.product-widget[data-id-producto='${idProducto}']`);
                        if (productWidget) {
                            const qtyElement = productWidget.querySelector('.qty');
                            const totalCantidadElement = productWidget.querySelector('.total-cantidad');
                            const currentQty = parseInt(qtyElement.textContent.replace('x', ''));
                            const cantidadEliminar = parseInt(cantidad);
                            if (currentQty > cantidadEliminar) {
                                const nuevaCantidad = currentQty - cantidadEliminar;
                                qtyElement.textContent = nuevaCantidad + 'x';
                                totalCantidadElement.value = nuevaCantidad;
                                totalCantidadElement.max = nuevaCantidad;
                            } else {
                                productWidget.remove();
                            }
                        }

                        // Actualizar los totales del carrito
                        actualizarCarrito();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        function actualizarCarrito() {
            // Recalcular el total de artículos y el subtotal
            let totalItems = 0;
            let subtotal = 0.0;

            document.querySelectorAll('.product-widget').forEach(widget => {
                const qtyElement = widget.querySelector('.qty');
                if (qtyElement) {
                    const qty = parseInt(qtyElement.textContent.replace('x', ''));
                    const price = parseFloat(widget.getAttribute('data-precio'));
                    totalItems += qty;
                    subtotal += qty * price;
                }
            });

            const qtyElement = document.querySelector('.qty');
            if (qtyElement) {
                qtyElement.textContent = totalItems;
            }
            const itemCountElement = document.querySelector('#cart-item-count');
            if (itemCountElement) {
                itemCountElement.textContent = totalItems;
            }
            const subtotalElement = document.querySelector('#cart-subtotal');
            if (subtotalElement) {
                subtotalElement.textContent = subtotal.toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }
        }

        window.eliminarDelCarrito = eliminarDelCarrito;
    });

    function agregarAlCarrito(idProducto, idCliente) {
        const formData = new FormData();
        formData.append('idProducto', idProducto);
        formData.append('idCliente', idCliente);
        formData.append('cantidad', 1);
        formData.append('agregar_al_carrito', true);

        fetch('agregar_al_carrito.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(result => {
                alert(result); // Mostrar mensaje del servidor
                location.reload(); // Recargar la página
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }
    </script>
</body>

</html>