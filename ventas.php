<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ventas</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

		<!-- Google font -->
		<link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,700" rel="stylesheet">

		<!-- Bootstrap -->
		<link type="text/css" rel="stylesheet" href="css/bootstrap.min.css"/>

		<!-- Slick -->
		<link type="text/css" rel="stylesheet" href="css/slick.css"/>
		<link type="text/css" rel="stylesheet" href="css/slick-theme.css"/>

		<!-- nouislider -->
		<link type="text/css" rel="stylesheet" href="css/nouislider.min.css"/>

		<!-- Font Awesome Icon -->
		<link rel="stylesheet" href="css/font-awesome.min.css">

		<!-- Custom stlylesheet -->
		<link type="text/css" rel="stylesheet" href="css/style.css"/>

		<link rel="stylesheet" href="style.css">
        
        <script src="https://kit.fontawesome.com/d438250857.js" crossorigin="anonymous"></script>
</head>

<body>

<header>
        <!-- TOP HEADER -->
        <!-- TOP HEADER -->
        <div id="top-header">
            <div class="container">
                <ul class="header-links pull-left">
                    <li><a href="ver_usuarios.php"><i class="fa-solid fa-user-pen"></i> Ver usuarios</a></li>
                    <li><a href="home_adm.php"><i class="fa-solid fa-computer"></i> Ver articulos</a></li>
                    <li><a href="Ver_solicutudes_eliminacion.php"><i class="fa-solid fa-user-xmark"></i>Solicitudes</a></li>
                    <li><a href="informes.php"><i class="fa-solid fa-file"></i>Informes</a></li>
                    <li><a href="#"><i class="fa-solid fa-money-check-dollar"></i>Ventas</a></li>

                </ul>
                <ul class="header-links pull-right">
                    <li><a href="#"><i class="fa fa-user-o"></i> Administrador</a></li>
                </ul>
            </div>
        </div>
        <!-- /TOP HEADER -->

        <!-- MAIN HEADER -->
        <div id="header">
            <div class="container">
                <div class="row">
                    <div class="col-md-3">
                        <div class="header-logo">
                            <a href="index.php" class="logo">
                                <img src="./img/GCCIMG.png" width="50%">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /MAIN HEADER -->
    </header>
    <!-- /HEADER -->

    <div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Informe de Ventas</h5>
                </div>
                <div class="card-body">
                    <form action="" method="post">
                        <div class="form-group">
                            <label for="fechaInicioVenta">Fecha de Inicio:</label>
                            <input type="date" class="form-control" id="fechaInicioVenta" name="fechaInicioVenta" required>
                        </div>
                        <div class="form-group">
                            <label for="fechaFinVenta">Fecha de Fin:</label>
                            <input type="date" class="form-control" id="fechaFinVenta" name="fechaFinVenta" required>
                        </div>
                        <button type="submit" class="btn btn-primary" name="btn-generar-informe-venta">Generar Informe</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php
    // Incluir el archivo de conexión a la base de datos
    include 'connection.php';

    // Variables para almacenar los datos de las gráficas
    $fechasVenta = [];
    $totalVentas = [];
    $articulosVendidos = [];
    $sumaTotalVendido = 0;

    if (isset($_POST['btn-generar-informe-venta'])) {
        // Obtener fechas del formulario de registro
        $fechaInicioVenta = $_POST['fechaInicioVenta'];
        $fechaFinVenta = $_POST['fechaFinVenta'];

        // Preparar y ejecutar la consulta SQL
        $stmt = $connection->prepare("SELECT DATE(fecha_compra) AS fecha, idProducto, SUM(cantidad) AS cantidad_vendida, SUM(cantidad * precio) AS total_vendido
                                       FROM compras
                                       WHERE fecha_compra BETWEEN ? AND ?
                                       GROUP BY DATE(fecha_compra), idProducto");
        $stmt->bind_param('ss', $fechaInicioVenta, $fechaFinVenta);
        $stmt->execute();
        $resultVenta = $stmt->get_result();

        if ($resultVenta->num_rows > 0) {
            while ($row = $resultVenta->fetch_assoc()) {
                $fechasVenta[] = $row['fecha'];
                $totalVentas[] = $row['total_vendido'];
                $articulosVendidos[] = [
                    'fecha' => $row['fecha'],
                    'idProducto' => $row['idProducto'],
                    'cantidad_vendida' => $row['cantidad_vendida'],
                    'total_vendido' => $row['total_vendido']
                ];
                $sumaTotalVendido += $row['total_vendido'];
            }

            echo '<div class="row mt-5">';
            echo '<div class="col-md-12">';
            echo '<h5>Artículos Vendidos:</h5>';
            echo '<table class="table">';
            echo '<thead>';
            echo '<tr>';
            echo '<th>Fecha</th>';
            echo '<th>ID Producto</th>';
            echo '<th>Cantidad Vendida</th>';
            echo '<th>Total Vendido</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            foreach ($articulosVendidos as $venta) {
                echo '<tr>';
                echo '<td>'.$venta['fecha'].'</td>';
                echo '<td>'.$venta['idProducto'].'</td>';
                echo '<td>'.$venta['cantidad_vendida'].'</td>';
                echo '<td>'.$venta['total_vendido'].'</td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
            echo '</div>';
            echo '</div>';

            // Mostrar el total vendido
            echo '<div class="row mt-3">';
            echo '<div class="col-md-12 text-right">';
            echo '<h5>Total Vendido: $' . number_format($sumaTotalVendido, 2) . '</h5>';
            echo '</div>';
            echo '</div>';

            // Imprimir contenedor para la gráfica
            echo '<div class="row mt-5">';
            echo '<div class="col-md-12">';
            echo '<h5>Gráfico de Ventas</h5>';
            echo '<canvas id="chartVentas"></canvas>';
            echo '</div>';
            echo '</div>';

            // Imprimir botón de descarga
            echo '<div class="row mt-2">';
            echo '<div class="col-md-12 text-center">';
            echo '<button id="downloadVentaChart" class="btn btn-success mt-2">Descargar Gráfica</button>';
            echo '</div>';
            echo '</div>';
        } else {
            echo '<div class="alert alert-warning mt-5">No se encontraron ventas en el periodo seleccionado.</div>';
        }

        $stmt->close();
    }
    $connection->close();
    ?>

    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            var ctxVenta = document.getElementById('chartVentas').getContext('2d');
            if (ctxVenta) {
                var chartVentas = new Chart(ctxVenta, {
                    type: 'bar',
                    data: {
                        labels: <?php echo json_encode($fechasVenta); ?>,
                        datasets: [{
                            label: 'Total Vendido',
                            data: <?php echo json_encode($totalVentas); ?>,
                            backgroundColor: 'rgba(108, 252, 54, 0.2)',
                            borderColor: 'rgba(108, 252, 54, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });

                // Agregar funcionalidad de descarga
                document.getElementById('downloadVentaChart').addEventListener('click', function() {
                    var a = document.createElement('a');
                    a.href = chartVentas.toBase64Image();
                    a.download = 'grafica_ventas.png';
                    a.click();
                });
            }
        });
    </script>


</body>
</html>