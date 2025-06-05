<?php
// Incluir archivo de conexión
include 'connection.php';

// Verificar si se ha enviado una consulta de búsqueda y si la consulta no está vacía
if (isset($_GET['query']) && !empty($_GET['query'])) {
    $query = $_GET['query'];

    // Preparar la consulta SQL para buscar productos que coincidan con la consulta
    $sql = "SELECT * FROM producto WHERE Nombre_producto LIKE '%$query%' ORDER BY Nombre_producto";

    // Ejecutar la consulta
    $result = $connection->query($sql);

    // Mostrar los resultados de la búsqueda
    if ($result->num_rows > 0) {
        echo "<h2>Resultados de la búsqueda:</h2>";
        echo '<div class="col-md-12">
                <div class="row">
                    <div class="products-tabs">
                        <!-- tab -->
                        <div id="tab1" class="tab-pane active">
                            <div class="products-slick" data-nav="#slick-nav-1">';
        while ($row = $result->fetch_assoc()) {
            echo '<div class="product">
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
                  </div>';
        }
        echo '          </div>
                        </div>
                    </div>
                  </div>';
    } else {
        echo "No se encontraron resultados para '$query'.";
    }
} 

?>
