<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Informe de Registro de Usuarios</title>
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
                    <li><a href="#"><i class="fa-solid fa-file"></i>Informes</a></li>

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
          <h5 class="card-title">Informe de Registro de Usuarios</h5>
        </div>
        <div class="card-body">
          <form action="" method="post">
            <div class="form-group">
              <label for="fechaInicioReg">Fecha de Inicio:</label>
              <input type="date" class="form-control" id="fechaInicioReg" name="fechaInicioReg" required>
            </div>
            <div class="form-group">
              <label for="fechaFinReg">Fecha de Fin:</label>
              <input type="date" class="form-control" id="fechaFinReg" name="fechaFinReg" required>
            </div>
            <button type="submit" class="btn btn-primary" name="btn-generar-informe-reg">Generar Informe</button>
          </form>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card">
        <div class="card-header">
          <h5 class="card-title">Informe de Eliminación de Usuarios</h5>
        </div>
        <div class="card-body">
          <form action="" method="post">
            <div class="form-group">
              <label for="fechaInicioElim">Fecha de Inicio:</label>
              <input type="date" class="form-control" id="fechaInicioElim" name="fechaInicioElim" required>
            </div>
            <div class="form-group">
              <label for="fechaFinElim">Fecha de Fin:</label>
              <input type="date" class="form-control" id="fechaFinElim" name="fechaFinElim" required>
            </div>
            <button type="submit" class="btn btn-primary" name="btn-generar-informe-elim">Generar Informe</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <?php
  // Incluir el archivo de conexión a la base de datos
  include 'connection.php';

  // Variables para almacenar los datos de las gráficas
  $fechasReg = [];
  $totalRegistrados = [];
  $fechasElim = [];
  $totalEliminados = [];

  if(isset($_POST['btn-generar-informe-reg'])) {
      // Obtener fechas del formulario de registro
      $fechaInicioReg = $_POST['fechaInicioReg'];
      $fechaFinReg = $_POST['fechaFinReg'];

      // Consulta SQL para obtener los usuarios registrados en el periodo proporcionado
      $sqlReg = "SELECT DATE(fecha_registro) AS fecha, COUNT(*) AS total_registrados
                 FROM cliente
                 WHERE fecha_registro BETWEEN '$fechaInicioReg' AND '$fechaFinReg'
                 GROUP BY DATE(fecha_registro)";

      $resultReg = $connection->query($sqlReg);

      if ($resultReg->num_rows > 0) {
          while ($row = $resultReg->fetch_assoc()) {
              $fechasReg[] = $row['fecha'];
              $totalRegistrados[] = $row['total_registrados'];
          }

          echo '<div class="row mt-5">';
          echo '<div class="col-md-12">';
          echo '<h5>Usuarios Registrados:</h5>';
          echo '<table class="table">';
          echo '<thead>';
          echo '<tr>';
          echo '<th>Fecha</th>';
          echo '<th>Total Registrados</th>';
          echo '</tr>';
          echo '</thead>';
          echo '<tbody>';
          foreach ($fechasReg as $key => $fecha) {
              echo '<tr>';
              echo '<td>'.$fecha.'</td>';
              echo '<td>'.$totalRegistrados[$key].'</td>';
          }
          echo '</tbody>';
          echo '</table>';
          echo '</div>';
          echo '</div>';

          // Imprimir contenedor para la gráfica
          echo '<div class="row mt-5">';
          echo '<div class="col-md-12">';
          echo '<h5>Gráfico de Usuarios Registrados</h5>';
          echo '<canvas id="chartUsuariosRegistrados"></canvas>';
          echo '</div>';
          echo '</div>';
      } else {
          echo '<div class="alert alert-warning mt-5">No se encontraron usuarios registrados en el periodo seleccionado.</div>';
      }
  }

  if(isset($_POST['btn-generar-informe-elim'])) {
      // Obtener fechas del formulario de eliminación
      $fechaInicioElim = $_POST['fechaInicioElim'];
      $fechaFinElim = $_POST['fechaFinElim'];

      // Consulta SQL para obtener los usuarios eliminados en el periodo proporcionado
      $sqlElim = "SELECT DATE(fecha_eliminacion) AS fecha, COUNT(*) AS total_eliminados
                  FROM registro_eliminaciones
                  WHERE fecha_eliminacion BETWEEN '$fechaInicioElim' AND '$fechaFinElim'
                  GROUP BY DATE(fecha_eliminacion)";

      $resultElim = $connection->query($sqlElim);

      if ($resultElim->num_rows > 0) {
          while ($row = $resultElim->fetch_assoc()) {
              $fechasElim[] = $row['fecha'];
              $totalEliminados[] = $row['total_eliminados'];
          }

          echo '<div class="row mt-5">';
          echo '<div class="col-md-12">';
          echo '<h5>Usuarios Eliminados:</h5>';
          echo '<table class="table">';
          echo '<thead>';
          echo '<tr>';
          echo '<th>Fecha</th>';
          echo '<th>Total Eliminados</th>';
          echo '</tr>';
          echo '</thead>';
          echo '<tbody>';
          foreach ($fechasElim as $key => $fecha) {
              echo '<tr>';
              echo '<td>'.$fecha.'</td>';
              echo '<td>'.$totalEliminados[$key].'</td>';
          }
          echo '</tbody>';
          echo '</table>';
          echo '</div>';
          echo '</div>';

          // Imprimir contenedor para la gráfica
          echo '<div class="row mt-5">';
          echo '<div class="col-md-12">';
          echo '<h5>Gráfico de Usuarios Eliminados</h5>';
          echo '<canvas id="chartUsuariosEliminados"></canvas>';
          echo '</div>';
          echo '</div>';
      } else {
          echo '<div class="alert alert-warning mt-5">No se encontraron usuarios eliminados en el periodo seleccionado.</div>';
      }
  }

  // Cerrar la conexión a la base de datos
  $connection->close();
  ?>

</div>

<script>
    // Gráfica de Usuarios Registrados
    var ctxReg = document.getElementById('chartUsuariosRegistrados').getContext('2d');
    var chartUsuariosRegistrados = new Chart(ctxReg, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($fechasReg); ?>,
            datasets: [{
                label: 'Usuarios Registrados',
                data: <?php echo json_encode($totalRegistrados); ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
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

    // Gráfica de Usuarios Eliminados
    var ctxElim = document.getElementById('chartUsuariosEliminados').getContext('2d');
    var chartUsuariosEliminados = new Chart(ctxElim, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($fechasElim); ?>,
            datasets: [{
                label: 'Usuarios Eliminados',
                data: <?php echo json_encode($totalEliminados); ?>,
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
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
</script>

</body>
</html>
