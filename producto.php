<?php
// Iniciar la sesión para poder acceder a $_SESSION
include "connection.php"; // Aquí incluyes tu archivo de conexión si es necesario

session_start();


// Inicializar variables para evitar errores de 'Undefined variable'
$idCliente = "";
$Nombres_cliente = "";
$Apellido_paterno = "";
$Apellido_materno = "";
$Correo_cliente = "";
$Password_cliente = "";
$Telefono_cliente = "";

// Verificar si la sesión está definida
if (isset($_SESSION['idCliente'])) {
    // Si la sesión está definida, obtener el nombre de usuario del usuario
    $idCliente = $_SESSION['idCliente'];

    // Realizar la consulta para obtener los datos del usuario desde la base de datos
    $query = "SELECT idCliente, Nombres_cliente, Apellido_paterno, Apellido_materno, Correo_cliente, Password_cliente, Telefono_cliente FROM cliente WHERE idCliente = '$idCliente'";
    $result = mysqli_query($connection, $query);

    // Verificar si se encontró el usuario en la base de datos
    if (mysqli_num_rows($result) > 0) {
        // Obtener los datos del usuario de la consulta
        $row = mysqli_fetch_assoc($result);
        $idCliente = $row['idCliente'];
        $Nombre_usuario = $row['Nombres_cliente'];
        $Apellido_paterno = $row['Apellido_paterno'];
        $Apellido_materno = $row['Apellido_materno'];
        $Correo_cliente = $row['Correo_cliente'];
        $Password_cliente = $row['Password_cliente'];
        $Telefono_cliente = $row['Telefono_cliente'];
    } else {
        // Si no se encontró el usuario, redirigirlo a la página de inicio de sesión
        header("Location: login.php");
        exit(); // Asegura que el script no continúe después de la redirección
    }
} else {
    // Si el usuario no ha iniciado sesión, redirigirlo a la página de inicio de sesión
    header("Location: login.php");
    exit(); // Asegura que el script no continúe después de la redirección
}
// Incluir el archivo de conexión a la base de datos
include 'connection.php';
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
                    <div class="col-md-9">
                        <div class="header-logo">
                            <a href="index.php" class="logo" target="_self">
                                <img src="./img/GCCIMG.png" width="50%">
                            </a>
                        </div>
                    </div>
                    <!-- /LOGO -->

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
                    <li class="active"><a href="index.php">Inicio</a></li>
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

    <div class="section">
        <div class="container">
            <div class="row">
                <div class="col-md-5 col-md-push-2">
                    <div id="product-main-img">
                        <?php
    if (isset($_GET['producto_id'])) {
        $producto_id = $_GET['producto_id'];
        include 'connection.php';
        $sql = "SELECT * FROM producto WHERE idProducto = $producto_id";
        $result = $connection->query($sql);
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            ?>
                        <div class="product-preview">
                            <img src="Imagenes_productos/<?php echo htmlspecialchars($row['imagen'], ENT_QUOTES, 'UTF-8'); ?>"
                                alt="Imagen del Producto" class="img-fluid">
                        </div>
                        <?php if (!empty($row['gif'])): ?>
                        <div class="product-preview">
                            <img src="gif/<?php echo htmlspecialchars($row['gif'], ENT_QUOTES, 'UTF-8'); ?>"
                                alt="Gif del Producto" class="img-fluid">
                        </div>
                        <?php endif; ?>
                        <?php
        } else {
            echo "Producto no encontrado.";
        }
    } else {
        echo "ID de producto no proporcionado.";
    }
    ?>
                    </div>

                </div>

                <div class="col-md-2 col-md-pull-5">
                    <div id="product-imgs">
                        <?php if (isset($row)): ?>
                        <div class="product-preview">
                            <img src="Imagenes_productos/<?php echo htmlspecialchars($row['imagen'], ENT_QUOTES, 'UTF-8'); ?>"
                                alt="Imagen del Producto" class="img-fluid">
                        </div>
                        <?php if (!empty($row['gif'])): ?>
                        <div class="product-preview">
                            <img src="gif/<?php echo htmlspecialchars($row['gif'], ENT_QUOTES, 'UTF-8'); ?>"
                                alt="Gif del Producto" class="img-fluid">
                        </div>
                        <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>


                <div class="col-md-5">
                    <div class="product-details">
                        <?php
                            if (isset($_GET['producto_id'])) {
                                $producto_id = $_GET['producto_id'];
                                include 'connection.php';
                                $sql = "SELECT * FROM producto WHERE idProducto = $producto_id";
                                $result = $connection->query($sql);
                                if ($result && $result->num_rows > 0) {
                                    $row = $result->fetch_assoc();
                                    ?>
                        <h2 class="product-name"><?php echo $row['Nombre_producto']; ?></h2>
                        <div>
                            <h3 class="product-price">$<?php echo $row['Precio']; ?></h3>
                            <span class="product-available"><?php echo $row['Cantidad']; ?> En Stock</span>
                        </div>
                        <p><?php echo $row['Descripcion']; ?></p>
                        <p><?php echo $row['Marca']; ?></p>
                        <p><?php echo $row['Modelo']; ?></p>

                        <div class="add-to-cart">
                            <form action="agregar_al_carrito.php" method="POST" class="add-to-cart-form">
                                <input type="hidden" name="idCliente" value="<?php echo $_SESSION['idCliente']; ?>">
                                <input type="hidden" name="idProducto" value="<?php echo $row['idProducto']; ?>">
                                <div class="form-group">
                                    <label for="cantidad">Cantidad:</label>
                                    <input type="number" id="cantidad" name="cantidad" value="1" min="1"
                                        class="input_form" required>
                                </div>
                                <button type="submit" name="agregar_al_carrito" class="add-to-cart-btn">
                                    <i class="fa fa-shopping-cart"></i> Agregar al carrito
                                </button>
                            </form>
                        </div>

                        <?php
                                } else {
                                    echo "El producto no se encontró.";
                                }
                                $connection->close();
                            } else {
                                echo "ID del producto no proporcionado.";
                            }
                            ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                    if (result.includes('Producto eliminado') || result.includes(
                            'Cantidad actualizada')) {
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