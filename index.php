<?php
    // Incluir el archivo de conexión a la base de datos
    include 'connection.php';

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
                    <li class="active"><a href="#">Inicio</a></li>
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

    <!-- /NAVIGATION -->
    <?php
        include ("connection.php");
        include ("controlador_busqueda.php");
        include ("controlador_filtros.php");
    ?>



    <!-- SECTION -->
    <div class="section">
    <!-- container -->
    <div class="container">
        <!-- row -->
        <div class="row">
            <!-- Products tab & slick -->
            <div class="col-md-12">
                <div class="row">
                    <div class="products-tabs">
                        <!-- tab -->
                        <div id="tab1" class="tab-pane active">
                            <div class="products-slick" data-nav="#slick-nav-1">
                                <?php
                                // Establecer la conexión a la base de datos
                                $servername = "localhost";
                                $username = "root";
                                $password = "";
                                $dbname = "integradora";

                                $conn = new mysqli($servername, $username, $password, $dbname);

                                // Verificar la conexión
                                if ($conn->connect_error) {
                                    die("Conexión fallida: " . $conn->connect_error);
                                }

                                // Consultar la base de datos, solo productos con cantidad >= 1
                                $sql = "SELECT idProducto, nombre_producto, categoria_producto, precio, imagen, Cantidad FROM producto WHERE Cantidad >= 1";
                                $result = $conn->query($sql);

                                // Mostrar los resultados
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        ?>
                                        <!-- product -->
                                        <div class="product">
                                            <div class="product-img d-flex justify-content-center align-items-center"
                                                style="width: 265px; height: 200px; overflow: hidden;">
                                                <img src="Imagenes_productos/<?php echo $row['imagen']; ?>"
                                                    alt="Imagen del Producto" class="img-fluid">
                                                <div class="product-label position-absolute top-0 start-0">
                                                    <span class="new badge bg-primary">NEW</span>
                                                </div>
                                            </div>
                                            <div class="product-body">
                                                <p class="product-category"><?php echo $row["categoria_producto"]; ?></p>
                                                <h3 class="product-name"><a
                                                        href="producto.php?producto_id=<?php echo $row["idProducto"]; ?>"><?php echo $row["nombre_producto"]; ?></a>
                                                </h3>
                                                <h4 class="product-price">$<?php echo number_format($row["precio"], 2); ?>
                                                </h4>
                                                <div class="product-btns">
                                                    <button class="quick-view"
                                                        onclick="window.location.href='producto.php?producto_id=<?php echo $row["idProducto"]; ?>'">
                                                        <i class="fa fa-eye"></i><span class="tooltipp">Ver</span>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="add-to-cart">
                                                <form action="agregar_al_carrito2.php" method="POST"
                                                    class="add-to-cart-form">
                                                    <input type="hidden" name="idCliente"
                                                        value="<?php echo $_SESSION["idCliente"]; ?>">
                                                    <input type="hidden" name="idProducto"
                                                        value="<?php echo $row["idProducto"]; ?>">
                                                    <input type="hidden" name="cantidad" value="1">
                                                    <button type="submit" name="agregar_al_carrito" class="add-to-cart-btn">
                                                        <i class="fa fa-shopping-cart"></i> Agregar al carrito
                                                    </button>
                                                </form>
                                            </div>

                                        </div>
                                        <!-- /product -->
                                        <?php
                                    }
                                } else {
                                    echo "0 resultados";
                                }
                                $conn->close();
                                ?>
                            </div>
                            <div id="slick-nav-1" class="products-slick-nav"></div>
                        </div>
                        <!-- /tab -->
                    </div>
                </div>
            </div>
            <!-- Products tab & slick -->
        </div>
        <!-- /row -->
    </div>
    <!-- /container -->
</div>


<!-- /SECTION -->
<div class="section">
    <!-- container -->
    <div class="container">
        <!-- row -->
        <div class="row">
            <!-- Products tab & slick -->
            <div class="col-md-12">
                <div class="row">
                    <div class="products-tabs">
                        <!-- tab -->
                        <div id="tab1" class="tab-pane active">
                            <div class="products-slick" data-nav="#slick-nav-2">
                                <?php
                                // Establecer la conexión a la base de datos
                                $servername = "localhost";
                                $username = "root";
                                $password = "";
                                $dbname = "integradora";

                                $conn = new mysqli($servername, $username, $password, $dbname);

                                // Verificar la conexión
                                if ($conn->connect_error) {
                                    die("Conexión fallida: " . $conn->connect_error);
                                }

                                // Consultar la base de datos, solo productos con cantidad >= 1
                                $sql = "SELECT idProducto, nombre_producto, categoria_producto, precio, imagen, Cantidad FROM producto WHERE Cantidad >= 1";
                                $result = $conn->query($sql);

                                // Mostrar los resultados
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        ?>
                                        <!-- product -->
                                        <div class="product">
                                            <div class="product-img d-flex justify-content-center align-items-center"
                                                style="width: 265px; height: 200px; overflow: hidden;">
                                                <img src="Imagenes_productos/<?php echo $row['imagen']; ?>"
                                                    alt="Imagen del Producto" class="img-fluid">
                                                <div class="product-label position-absolute top-0 start-0">
                                                    <span class="new badge bg-primary">NEW</span>
                                                </div>
                                            </div>
                                            <div class="product-body">
                                                <p class="product-category"><?php echo $row["categoria_producto"]; ?></p>
                                                <h3 class="product-name"><a
                                                        href="producto.php?producto_id=<?php echo $row["idProducto"]; ?>"><?php echo $row["nombre_producto"]; ?></a>
                                                </h3>
                                                <h4 class="product-price">$<?php echo number_format($row["precio"], 2); ?>
                                                </h4>
                                                <div class="product-btns">
                                                    <button class="quick-view"
                                                        onclick="window.location.href='producto.php?producto_id=<?php echo $row["idProducto"]; ?>'">
                                                        <i class="fa fa-eye"></i><span class="tooltipp">Ver</span>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="add-to-cart">
                                                <form action="agregar_al_carrito2.php" method="POST"
                                                    class="add-to-cart-form">
                                                    <input type="hidden" name="idCliente"
                                                        value="<?php echo $_SESSION["idCliente"]; ?>">
                                                    <input type="hidden" name="idProducto"
                                                        value="<?php echo $row["idProducto"]; ?>">
                                                    <input type="hidden" name="cantidad" value="1">
                                                    <button type="submit" name="agregar_al_carrito" class="add-to-cart-btn">
                                                        <i class="fa fa-shopping-cart"></i> Agregar al carrito
                                                    </button>
                                                </form>
                                            </div>

                                        </div>
                                        <!-- /product -->
                                        <?php
                                    }
                                } else {
                                    echo "0 resultados";
                                }
                                $conn->close();
                                ?>
                            </div>
                            <div id="slick-nav-2" class="products-slick-nav"></div>
                        </div>
                        <!-- /tab -->
                    </div>
                </div>
            </div>
            <!-- Products tab & slick -->
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

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
                    alert(result);
                    location.reload();
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }

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
                    alert(result);

                    if (result.includes('Producto eliminado') || result.includes('Cantidad actualizada')) {
                        const productWidget = document.querySelector(`.product-widget[data-id-producto='${idProducto}']`);
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

                        actualizarCarrito();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }


            function actualizarCarrito() {
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
            window.actualizarCantidad = actualizarCantidad;
        });
    </script>
</body>

</html>