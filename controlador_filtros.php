<?php
// Incluir archivo de conexión
include 'connection.php';

// Inicializar variables para la consulta
$filtros = [];
$categoria = isset($_GET['categoria']) ? $_GET['categoria'] : '0';
$precio = isset($_GET['precio']) ? $_GET['precio'] : '0';
$marca = isset($_GET['marca']) ? $_GET['marca'] : '0';

// Mapear categorías
$categorias_map = [
    '1' => ['Laptop'],
    '2' => ['Gabinete', 'Memoria RAM', 'Memoria HDD', 'HDD', 'Memoria SSD', 'SSD', 'Tarjeta madre', 'Motherboard', 'Tarjeta gráfica', 'Procesador', 'Tarjeta wifi'],
    '3' => ['Teclado', 'Mouse', 'Audifonos', 'Pantalla']
];

// Mapear marcas
$marcas_map = [
    '1' => 'ASUS',
    '2' => 'HP',
    '3' => 'ADATA',
    '4' => 'OCELOT',
    '5' => 'KINGSTON',
    '6' => 'YEYIAN'
];

// Mapear precios
$precios_map = [
    '1' => [300, 3999],
    '2' => [4000, 7999],
    '3' => [8000, PHP_INT_MAX]
];

// Construir consulta SQL
$sql = "SELECT * FROM producto WHERE 1=1";

// Filtrar por categoría
if ($categoria != '0' && array_key_exists($categoria, $categorias_map)) {
    $subcategorias = $categorias_map[$categoria];
    $subcategorias_list = "'" . implode("', '", $subcategorias) . "'";
    $sql .= " AND Categoria_producto IN ($subcategorias_list)";
}

// Filtrar por marca
if ($marca != '0' && array_key_exists($marca, $marcas_map)) {
    $marca_nombre = $marcas_map[$marca];
    $sql .= " AND Marca = '$marca_nombre'";
}

// Filtrar por precio
if ($precio != '0' && array_key_exists($precio, $precios_map)) {
    $precio_rango = $precios_map[$precio];
    $sql .= " AND Precio BETWEEN " . $precio_rango[0] . " AND " . $precio_rango[1];
}

// Ejecutar la consulta solo si al menos un filtro está aplicado
if ($categoria != '0' || $precio != '0' || $marca != '0') {
    $sql .= " ORDER BY Nombre_producto";

    // Ejecutar la consulta
    $result = $connection->query($sql);

    // Mostrar los resultados de la búsqueda
    if ($result->num_rows > 0) {
        echo "<h2>Resultados de la búsqueda:</h2>";
        echo '<div class="col-md-12">
                <div class="row">';
        while ($row = $result->fetch_assoc()) {
            echo '<div class="col-md-3 col-sm-6">
                    <div class="product">
                        <div class="product-img d-flex justify-content-center align-items-center" style="width: 200px; height: 200px; overflow: hidden;">
                            <img src="Imagenes_productos/' . $row['imagen'] . '" alt="Imagen del Producto" class="img-fluid">
                            <div class="product-label position-absolute top-0 start-0">
                                <span class="new badge bg-primary">NEW</span>
                            </div>
                        </div>
                        <div class="product-body">
                            <p class="product-category">' . $row["Categoria_producto"] . '</p>
                            <h3 class="product-name"><a href="detalles_producto.php?producto_id=' . $row["idProducto"] . '">' . $row["Nombre_producto"] . '</a></h3>
                            <h4 class="product-price">$' . $row["Precio"] . '</h4>
                            <div class="product-btns">
                                <button class="quick-view" onclick="window.location.href=\'producto.php?producto_id=' . $row["idProducto"] . '\'">
                                    <i class="fa fa-eye"></i><span class="tooltipp">Ver</span>
                                </button>
                            </div>
                        </div>
                        <div class="add-to-cart">
                            <button class="add-to-cart-btn"><i class="fa fa-shopping-cart"></i> Agregar al carrito</button>
                        </div>
                    </div>
                  </div>';
        }
        echo '      </div>
                  </div>';
    } else {
        echo "<h2>No se encontraron resultados para los filtros seleccionados.</h2>";
    }
} else {
    echo "";
}
?>
