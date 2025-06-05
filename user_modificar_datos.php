<?php
    // Iniciar la sesión para poder acceder a $_SESSION
	include "connection.php"; // Aquí incluyes tu archivo de conexión si es necesario
	session_start();

	// Inicializar variables para evitar errores de 'Undefined variable'
	$nombre_usuario = "";
	$apellido_paterno = "";
	$apellido_materno = "";
	$correo_cliente = "";

	// Verificar si la sesión está definida
	if(isset($_SESSION['nombre_usuario'])) {
		// Si la sesión está definida, obtener el nombre de usuario del usuario
		$nombre_usuario = $_SESSION['nombre_usuario'];

		// Realizar la consulta para obtener los datos del usuario desde la base de datos
		$query = "SELECT Nombres_cliente, Apellido_paterno, Apellido_materno, Correo_cliente FROM cliente WHERE Nombres_cliente = '$nombre_usuario'";
		$result = mysqli_query($connection, $query);

		// Verificar si se encontró el usuario en la base de datos
		if(mysqli_num_rows($result) > 0) {
			// Obtener los datos del usuario de la consulta
			$row = mysqli_fetch_assoc($result);
			$nombre_usuario = $row['Nombres_cliente'];
			$apellido_paterno = $row['Apellido_paterno'];
			$apellido_materno = $row['Apellido_materno'];
			$correo_cliente = $row['Correo_cliente'];
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


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar datos</title>

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

    <form class = "col-4 p-3 m-auto" method = "POST">
        <h2 class = "text-center alert-secondary">Modificar datos</h2>

        <form>
			<?php include("modificar_datos_user.php") ?>
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" class="input_form" id="nombre" value="<?= $nombre_usuario ?>" >
            </div>
            <div class="form-group">
                <label for="apellidoPaterno">Apellido Paterno:</label>
                <input type="text" class="input_form" id="apellidoPaterno" value="<?= $apellido_paterno ?>">
            </div>
            <div class="form-group">
                <label for="apellidoMaterno">Apellido Materno:</label>
                <input type="text" class="input_form" id="apellidoMaterno" value="<?= $apellido_materno ?>" >
            </div>

			<button type="submit" class="btn btn-primary" name="btn-registrar" value="ok">Modificar datos</button>

            
            <!-- Puedes agregar más campos según sea necesario -->
        </form>

      
    </form>
</body>
</html>



