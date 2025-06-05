<div class="section">
			<!-- container -->
			<div class="container">
				<!-- row -->
				<div class="row">

					<!-- section title -->
					<div class="col-md-12">
						<div class="section-title">
							<h3 class="title">Nuevos productos</h3>
							<div class="section-nav">
								<ul class="section-tab-nav tab-nav">
									<li class="active"><a data-toggle="tab" href="#tab1">Laptops</a></li>
									<li><a data-toggle="tab" href="#tab1">Smartphones</a></li>
									<li><a data-toggle="tab" href="#tab1">Camaras</a></li>
									<li><a data-toggle="tab" href="#tab1">Accesorios</a></li>
								</ul>
							</div>
						</div>
					</div>
					<!-- /section title -->

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

            // Consultar la base de datos
            $sql = "SELECT nombre_producto, categoria_producto, precio FROM producto";
            $result = $conn->query($sql);

            // Mostrar los resultados
            if ($result->num_rows > 0) {
               

                while($row = $result->fetch_assoc()) {
                   
                    ?>
                    
                    <!-- product -->
                    <div class="product">
                        <div class="product-img">
                            <img src="./img/product01.png" alt="">
                            <div class="product-label">
                                <span class="new">NEW</span>
                            </div>
                        </div>
                        <div class="product-body">
                            <p class="product-category"><?php echo $row["categoria_producto"]; ?></p>
                            <h3 class="product-name"><a href="#"><?php echo $row["nombre_producto"]; ?></a></h3>
                            <h4 class="product-price">$<?php echo $row["precio"]; ?></h4>
                            <div class="product-rating">
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star-o"></i>
                            </div>
                            <div class="product-btns">
                                <button class="add-to-wishlist"><i class="fa fa-heart-o"></i><span class="tooltipp">guardar</span></button>
                                <button class="add-to-compare"><i class="fa fa-exchange"></i><span class="tooltipp">agregar al carrito</span></button>
                                <button class="quick-view"><i class="fa fa-eye"></i><span class="tooltipp">ver</span></button>
                            </div>
                        </div>
                        <div class="add-to-cart">
                            <button class="add-to-cart-btn"><i class="fa fa-shopping-cart"></i> Agregar al carrito</button>
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