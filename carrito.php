<?php
// Iniciar la sesión
session_start();
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
    echo "<script>
        alert('$message');
        setTimeout(function() {
            location.reload();
        });
    </script>";
}

// Incluir el archivo de conexión a la base de datos
include 'connection.php';

// Verificar si el usuario ha iniciado sesión
if (isset($_SESSION['idCliente'])) {
    $idCliente = $_SESSION['idCliente'];

    // Consulta para obtener la tarjeta del cliente
    $sqltarjeta = "SELECT idTarjeta, nombre_titular, numero_tarjeta, fecha_vencimiento, codigo_seguridad FROM tarjetas_credito WHERE idCliente = ?";
    $stmt = $connection->prepare($sqltarjeta);
    if ($stmt === false) {
        die("Error al preparar la consulta para tarjetas: " . $connection->error);
    }
    $stmt->bind_param('i', $idCliente);
    if (!$stmt->execute()) {
        die("Error al ejecutar la consulta para tarjetas: " . $stmt->error);
    }
    $result = $stmt->get_result();
    $tarjetas = [];
    while ($row = $result->fetch_assoc()) {
        $tarjetas[] = $row;
    }
    $stmt->close();

    // Consulta para obtener la dirección de envío del cliente
    $sqldireccion = "SELECT idDireccion, Direccion, Codigo_Postal, Colonia, Ciudad, Correo_cliente, Telefono_cliente FROM direccion WHERE idCliente = ?";
    $stmt = $connection->prepare($sqldireccion);
    if ($stmt === false) {
        die("Error al preparar la consulta para dirección: " . $connection->error);
    }
    $stmt->bind_param('i', $idCliente);
    if (!$stmt->execute()) {
        die("Error al ejecutar la consulta para dirección: " . $stmt->error);
    }
    $result = $stmt->get_result();
    $direcciones = [];
    while ($row = $result->fetch_assoc()) {
        $direcciones[] = $row;
    }
    $stmt->close();

    // Consulta para obtener las cuentas de PayPal del cliente
    $sqlPayPal = "SELECT idEmail, email FROM paypal_emails WHERE idCliente = ?";
    $stmt = $connection->prepare($sqlPayPal);
    if ($stmt === false) {
        die("Error al preparar la consulta para PayPal: " . $connection->error);
    }
    $stmt->bind_param('i', $idCliente);
    if (!$stmt->execute()) {
        die("Error al ejecutar la consulta para PayPal: " . $stmt->error);
    }
    $result = $stmt->get_result();
    $paypalAccounts = [];
    while ($row = $result->fetch_assoc()) {
        $paypalAccounts[] = $row;
    }
    $stmt->close();

    // Consulta SQL para obtener los productos en el carrito del cliente actual agrupados por idProducto
    $sql = "SELECT carrito.idProducto, producto.Nombre_producto, producto.Precio, producto.imagen, SUM(carrito.Cantidad) as Cantidad
            FROM carrito
            JOIN producto ON carrito.idProducto = producto.idProducto
            WHERE carrito.idCliente = ?
            GROUP BY carrito.idProducto";
    $stmt = $connection->prepare($sql);
    if ($stmt === false) {
        die("Error al preparar la consulta para carrito: " . $connection->error);
    }
    $stmt->bind_param('i', $idCliente);
    if (!$stmt->execute()) {
        die("Error al ejecutar la consulta para carrito: " . $stmt->error);
    }
    $result = $stmt->get_result();
} else {
    $result = null;
}

$total_items = 0;
$subtotal = 0.0;
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $total_items += $row['Cantidad'];
        $subtotal += $row['Cantidad'] * $row['Precio'];
    }
}
?>


<!DOCTYPE html>
<script>
var tarjetas = <?php echo json_encode($tarjetas); ?>;
var direcciones = <?php echo json_encode($direcciones); ?>;
</script>
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
                                <select class="input-select" name="precio">
                                    <option value="0">Precio</option>
                                    <option value="1">300 a 3999</option>
                                    <option value="2">4000 a 7999</option>
                                    <option value="3">8000 +</option>
                                </select>
                                <select class="input-select" name="marca">
                                    <option value="0">Marcas</option>
                                    <option value="1">ASUS</option>
                                    <option value="2">HP</option>
                                    <option value="3">ADATA</option>
                                    <option value="4">OCELOT</option>
                                    <option value="5">KINGSTON</option>
                                    <option value="6">YEYIAN</option>
                                </select>
                                <select class="input-select" name="categoria">
                                    <option value="0">Categorias</option>
                                    <option value="1">Laptops</option>
                                    <option value="2">Hardware</option>
                                    <option value="3">Perifericos</option>
                                </select>
                                <input class="input" placeholder="Buca aquí" name="query">
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
                    <li><a href="metodos_pago.php">Metodos de pago</a></li>
                </ul>
                <!-- /NAV -->
            </div>
            <!-- /responsive-nav -->
        </div>
        <!-- /container -->
    </nav>
    <!-- /NAVIGATION -->

    <!-- BREADCRUMB -->
    <div id="breadcrumb" class="section">
        <!-- container -->
        <div class="container">
            <!-- row -->
            <div class="row">
                <div class="col-md-12">
                    <h3 class="breadcrumb-header">Orden de Pago</h3>
                </div>
            </div>
            <!-- /row -->
        </div>
        <!-- /container -->
    </div>
    <!-- /BREADCRUMB -->


    <?php
include 'connection.php';

if (isset($_SESSION['idCliente'])) {
    $idCliente = $_SESSION['idCliente'];

    // Consulta SQL para obtener los productos en el carrito del cliente actual
    $sql = "SELECT carrito.idProducto, producto.Nombre_producto, producto.Precio, carrito.Cantidad 
            FROM carrito
            JOIN producto ON carrito.idProducto = producto.idProducto
            WHERE carrito.idCliente = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param('i', $idCliente);
    $stmt->execute();
    $result = $stmt->get_result();

    // Inicializar variables para el total
    $total = 0;
    $orderProductsHtml = '';

    while ($row = $result->fetch_assoc()) {
        $productName = $row['Nombre_producto'];
        $productPrice = $row['Precio'];
        $productQuantity = $row['Cantidad'];
        $productTotal = $productPrice * $productQuantity;
        $total += $productTotal;

        // Generar HTML para cada producto
        $orderProductsHtml .= "<div class='order-col'>
                                    <div>{$productQuantity}x {$productName}</div>
                                    <div>\$ {$productTotal}</div>
                               </div>";
    }

    $stmt->close();
    $connection->close();
} else {
    $orderProductsHtml = "<div class='order-col'>
                            <div>No hay productos en el carrito.</div>
                          </div>";
    $total = 0;
}
?>
    <div class="container">
        <div class="col-md-12 order-details">
            <div class="section-title text-center">
                <h3 class="title">Tu orden de compra</h3>
            </div>
            <div class="order-summary">
                <div class="order-col">
                    <div><strong>PRODUCTO</strong></div>
                    <div><strong>TOTAL</strong></div>
                </div>
                <div class="order-products">
                    <?php echo $orderProductsHtml; ?>
                </div>
                <div class="order-col">
                    <div>Envío</div>
                    <div><strong>GRATIS</strong></div>
                </div>
                <div class="order-col">
                    <div><strong>TOTAL</strong></div>
                    <div><strong class="order-total">$<?php echo number_format($total, 2); ?></strong></div>
                </div>
            </div>
            <div class="payment-method">
                <div class="input-radio">
                    <input type="radio" name="payment" id="payment-1">
                    <label for="payment-1">
                        <span></span>
                        Pago Tarjeta Credito o Tarjeta de Debito
                    </label>
                    <div class="caption">
                        <form action="confirmar_compra.php" method="post">
                        <input class ="input_form" type="hidden" name="idCliente" value="<?php echo $idCliente; ?>" readonly >				
                            <h3>Selecciona la dirección de envío</h3>
                            <select class="input-select" id="direccion-select" name="direccion">
                                <option value="0">Selecciona una dirección</option>
                                <?php foreach ($direcciones as $direccion): ?>
                                <option value="<?= $direccion['idDireccion'] ?>">
                                    <?= "{$direccion['Direccion']}, {$direccion['Colonia']}, {$direccion['Ciudad']}, CP: {$direccion['Codigo_Postal']}" ?>
                                </option>
                                <?php endforeach; ?>
                            </select><br><br>

                            <h3>Selecciona la tarjeta de crédito</h3>
                            <select class="input-select" id="tarjeta-select" name="tarjeta">
                                <option value="0">Pago con Tarjeta (Débito o Crédito)</option>
                                <?php foreach ($tarjetas as $tarjeta): 
                    $numero_oculto = substr($tarjeta['numero_tarjeta'], 0, 4) . ' XXXX XXXX ' . substr($tarjeta['numero_tarjeta'], -4);
                    $fecha_vencimiento = date("m/y", strtotime($tarjeta['fecha_vencimiento'])); ?>
                                <option value="<?= $tarjeta['idTarjeta'] ?>">
                                    <?= "{$numero_oculto} {$fecha_vencimiento}" ?></option>
                                <?php endforeach; ?>
                            </select><br><br>
                            <button type="submit" class="primary-btn order-submit">Realizar Pago</button>
                        </form>
                    </div>
                </div>

            </div>




            <div class="input-radio">
                <input type="radio" name="payment" id="payment-2">
                <label for="payment-2">
                    <span></span>
                    Paypal
                </label>
                <div class="caption">
                    <form action="confirmar_compra.php" method="post">
                    <input class ="input_form" type="hidden" name="idCliente" value="<?php echo $idCliente; ?>" readonly >				
                        <h3>Selecciona la dirección de envío</h3>
                        <select class="input-select" id="direccion-select" name="direccion">
                            <option value="0">Selecciona una dirección</option>
                            <?php foreach ($direcciones as $direccion): ?>
                            <option value="<?= htmlspecialchars($direccion['idDireccion'], ENT_QUOTES, 'UTF-8') ?>">
                                <?= htmlspecialchars("{$direccion['Direccion']}, {$direccion['Colonia']}, {$direccion['Ciudad']}, CP: {$direccion['Codigo_Postal']}", ENT_QUOTES, 'UTF-8') ?>
                            </option>
                            <?php endforeach; ?>
                        </select><br><br>

                        <h3>Selecciona cuenta de Paypal</h3>
                        <select class="input-select" id="paypal-select" name="paypal">
                            <option value="0">Pago con Paypal</option>
                            <?php foreach ($paypalAccounts as $paypal): ?>
                            <option value="<?= htmlspecialchars($paypal['idEmail'], ENT_QUOTES, 'UTF-8') ?>">
                                <?= htmlspecialchars($paypal['email'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                            <?php endforeach; ?>
                        </select><br><br>

                        <button type="submit" class="primary-btn order-submit">Realizar Pago</button>
                    </form>
                </div>

            </div>

        </div>
    </div>
    <!-- /Order Details -->
    </div>
    <!-- /row -->
    </div>
    <!-- /container -->
    </div>
    <!-- /SECTION -->
    </div>

    <!-- jQuery Plugins -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/slick.min.js"></script>
    <script src="js/nouislider.min.js"></script>
    <script src="js/jquery.zoom.min.js"></script>
    <script src="js/main.js"></script>
    <!-- JavaScript -->
    <script>
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
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }

    document.getElementById('realizar-pago-btn').addEventListener('click', function(e) {
        e.preventDefault();

        // Obtener la dirección y tarjeta seleccionadas
        var direccionIndex = document.getElementById('direccion-select').value;
        var tarjetaIndex = document.getElementById('tarjeta-select').value;

        if (direccionIndex == 0 || tarjetaIndex == 0) {
            alert("Por favor, selecciona una dirección y una tarjeta.");
            return;
        }

        // Enviar la información al servidor
        var formData = new FormData();
        formData.append('direccionIndex', direccionIndex);
        formData.append('tarjetaIndex', tarjetaIndex);

        fetch('confirmar_compra.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    window.location.href = 'confirmar_compra.php';
                } else {
                    alert(result.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    });
    </script>
</body>

</html>