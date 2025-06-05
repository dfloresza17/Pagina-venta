<?php
session_start();
include 'connection.php';

// Función para ocultar los dígitos de en medio de un número de tarjeta
function ocultarDigitosTarjeta($metodoPago) {
    // Verificar si el método de pago comienza con la palabra "Tarjeta:"
    if (strpos($metodoPago, 'Tarjeta:') === 0) {
        // Obtener el número de tarjeta eliminando "Tarjeta: " del inicio
        $numeroTarjeta = substr($metodoPago, 9);

        // Obtener la longitud del número de tarjeta
        $longitud = strlen($numeroTarjeta);

        // Mantener los primeros cuatro dígitos
        $primerosCuatro = substr($numeroTarjeta, 0, 4);

        // Mantener los últimos cuatro dígitos
        $ultimosCuatro = substr($numeroTarjeta, -4);

        // Reemplazar los dígitos del medio con asteriscos (*)
        $digitosMedioOcultos = str_repeat('*', max($longitud - 8, 0)); // Asegurar que times no sea negativo

        // Combinar los primeros cuatro dígitos, los dígitos ocultos y los últimos cuatro dígitos
        return 'Tarjeta: ' . $primerosCuatro . $digitosMedioOcultos . $ultimosCuatro;
    } else {
        // Devolver el método de pago sin cambios si no es una tarjeta
        return $metodoPago;
    }
}

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['idCliente'])) {
    header('Location: login.php'); // Redirige a la página de inicio de sesión si no hay un cliente logueado
    exit();
}

$idCliente = $_SESSION['idCliente'];

// Consulta para obtener las compras del usuario
$sql = "SELECT c.idCompra, c.idProducto, c.cantidad, c.precio, c.numero_tarjeta, c.direccion_envio, c.fecha_compra, c.metodo_pago, p.Nombre_producto, p.imagen
        FROM compras c
        JOIN producto p ON c.idProducto = p.idProducto
        WHERE c.idCliente = ? 
        ORDER BY c.fecha_compra DESC";
$stmt = $connection->prepare($sql);
if ($stmt === false) {
    die('Error en la preparación de la consulta: ' . $connection->error);
}
$stmt->bind_param('i', $idCliente);
$stmt->execute();
$result = $stmt->get_result();

$compras = [];
while ($row = $result->fetch_assoc()) {
    $fechaCompra = $row['fecha_compra'];
    $row['metodo_pago'] = ocultarDigitosTarjeta($row['metodo_pago']); // Oculta los dígitos medios de la tarjeta de crédito
    $compras[$fechaCompra][] = $row;
}
$stmt->close();

// Manejo de mensajes de sesión
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
                    <li class="active"><a href="compras.php">Pedidos</a></li>
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
        <div class="container">
        <div class="row">
    <h1 class="title">Compras realizadas</h1>
    <?php
    if (!empty($compras)) {
        foreach ($compras as $fechaCompra => $compra) {
            $fechaFormateada = date('H:i, d-m-Y', strtotime($fechaCompra));
            echo '<div class="col-md-12">';
            echo '<h3>Fecha de compra: ' . $fechaFormateada . '</h3>';
            echo '<div class="product-widget-container">';
            echo '<p><strong>Dirección de envío:</strong> ' . htmlspecialchars($compra[0]['direccion_envio']) . '</p>';
            echo '<p><strong>Método de pago:</strong> ' . htmlspecialchars($compra[0]['metodo_pago']) . '</p>';

            foreach ($compra as $item) {
                $rutaImagen = 'Imagenes_productos/' . $item['imagen'];
                echo '<div class="product-widget" data-id-producto="' . $item['idProducto'] . '" data-precio="' . $item['precio'] . '">';
                echo '    <div class="product-img">';
                echo '        <img src="' . $rutaImagen . '" alt="Imagen del producto">';
                echo '    </div>';
                echo '    <div class="product-body">';
                echo '        <p><strong>Producto:</strong> ' . htmlspecialchars($item['Nombre_producto']) . '</p>';
                echo '        <p><strong>Cantidad:</strong> ' . $item['cantidad'] . '</p>';
                echo '        <p><strong>Precio:</strong> $' . number_format($item['precio'], 2) . '</p>';
                echo '    </div>';
                echo '</div>';
            }
            echo '</div>';
            echo '</div>';
        }
    } else {
        echo '<p>No has realizado ninguna compra.</p>';
    }
    ?>
</div>



        </div>
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