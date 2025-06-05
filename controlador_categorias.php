<?php
// Incluir archivo de conexión
include 'connection.php';

// Verificar si se ha enviado una consulta de búsqueda
if (isset($_GET['categoria']) && $_GET['categoria'] != '0') {
    $categoria = $_GET['categoria'];
    
    // Mapear la categoría principal a sus subcategorías correspondientes
    $categorias_map = [
        '1' => ['Laptop'],
        '2' => ['Gabinete', 'Memoria RAM', 'Memoria HDD', 'HDD', 'Memoria SSD', 'SSD', 'Tarjeta madre', 'Motherboard', 'Tarjeta gráfica', 'Procesador', 'Tarjeta wifi'],
        '3' => ['Teclado', 'Mouse', 'Audifonos', 'Pantalla']
    ];

    if (array_key_exists($categoria, $categorias_map)) {
        $subcategorias = $categorias_map[$categoria];
        // Crear una lista de subcategorías para la consulta SQL
        $subcategorias_list = "'" . implode("', '", $subcategorias) . "'";
        
        // Prepara la consulta SQL para buscar productos por subcategorías
        $sql = "SELECT * FROM producto WHERE Categoria_producto IN ($subcategorias_list) ORDER BY Nombre_producto";
        
        // Ejecuta la consulta
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
            echo "<h2>No se encontraron resultados para la categoría seleccionada.</h2>";
        }
    } else {
        echo "Categoría no válida.";
    }
}
?>