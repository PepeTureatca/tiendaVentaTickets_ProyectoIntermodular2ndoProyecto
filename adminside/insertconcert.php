<?php
include "config.php";

// Verificar si hay un ID de concierto en la URL para edición
$conid = isset($_GET['concert_id']) ? $_GET['concert_id'] : null;

if(isset($_POST['submit'])) {
    // Obtener datos del formulario
    $concert_name = mysqli_real_escape_string($conn, $_POST['concert_name']);
    $concert_date = $_POST['concert_date'];
    $concert_time = $_POST['concert_time'];
    $concert_artist = mysqli_real_escape_string($conn, $_POST['concert_artist']);
    $concert_desc = mysqli_real_escape_string($conn, $_POST['concert_desc']);
    $concert_genre = mysqli_real_escape_string($conn, $_POST['concert_genre']);
    $concert_venue = mysqli_real_escape_string($conn, $_POST['concert_venue']);
    $concert_ubprice = $_POST['concert_ubprice'];
    $concert_lbprice = $_POST['concert_lbprice'];
    $concert_vipprice = $_POST['concert_vipprice'];
    $concert_genadprice = $_POST['concert_genadprice'];
    $concert_contact = mysqli_real_escape_string($conn, $_POST['concert_contact']);

    // Validar si hay una imagen subida
    $image = $_FILES['image']['name'];
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = 'uploaded_img/' . $image;

    // Errores de validación
    $errors = [];

    // Verificar si los campos están vacíos
    if (empty($concert_name) || empty($concert_date) || empty($concert_time) || empty($concert_artist) || empty($concert_desc) || empty($concert_genre) || empty($concert_venue) || empty($concert_ubprice) || empty($concert_lbprice) || empty($concert_vipprice) || empty($concert_genadprice) || empty($concert_contact) || empty($image)) {
        $errors[] = "Todos los campos son obligatorios.";
    }

    // Verificar el tamaño de la imagen
    if ($image_size > 2000000) {
        $errors[] = "El tamaño de la imagen es demasiado grande. Por favor, selecciona una imagen menor a 2MB.";
    }

    // Validar si los precios son numéricos
    $numericFields = ['concert_ubprice', 'concert_lbprice', 'concert_vipprice', 'concert_genadprice'];
    foreach ($numericFields as $field) {
        if (!is_numeric($_POST[$field])) {
            $errors[] = ucfirst(str_replace('_', ' ', $field)) . " debe ser un número.";
        }
    }

    // Verificar si el género y el lugar de concierto contienen solo letras
    $letterFields = ['concert_genre', 'concert_venue'];
    foreach ($letterFields as $field) {
        if (!ctype_alpha(str_replace(' ', '', $_POST[$field]))) {
            $errors[] = ucfirst(str_replace('_', ' ', $field)) . " debe contener solo letras.";
        }
    }

    // Si no hay errores, insertar en la base de datos
    if (empty($errors)) {
        // Formatear la fecha y hora
        date_default_timezone_set('Europe/Madrid');
        $formatted_date = date("Y-m-d", strtotime($concert_date));
        $formatted_time = date("H:i", strtotime($concert_time));

        // Consulta para insertar el nuevo concierto
        $query = "INSERT INTO tblconcert (concert_name, concert_date, concert_time, concert_artist, concert_desc, concert_genre, concert_venue, ub_price, lb_price, vip_price, genad_price, concert_contact, image)
        VALUES ('$concert_name', '$formatted_date', '$formatted_time', '$concert_artist', '$concert_desc', '$concert_genre', '$concert_venue', '$concert_ubprice', '$concert_lbprice', '$concert_vipprice', '$concert_genadprice', '$concert_contact', '$image')";

        $result = mysqli_query($conn, $query);

        // Si la consulta se ejecuta correctamente, mover la imagen
        if ($result) {
            move_uploaded_file($image_tmp_name, $image_folder);
            header("Location: viewconcert.php?msg=El nuevo concierto ha sido añadido");
            exit();
        } else {
            echo "Error al insertar el concierto: " . mysqli_error($conn);
        }
    } else {
        // Mostrar los errores si los hay
        echo "<div style='background-color: red; color:white; text-align:center;'>";
        foreach ($errors as $error) {
            echo $error . "<br>";
        }
        echo "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Insertar Concierto</title>
</head>
<body>
    <nav class="navbar navbar-light justify-content-center fs-3 mb-5" style="background-color: #00ADB5;">
        Insertar Concierto
    </nav>

    <div class="container">
        <div class="text-center mb-4">
            <h3>Agregar Nuevo Concierto</h3>
            <p class="text-muted">Por favor, complete el formulario a continuación.</p>
        </div>

        <form action="" method="post" enctype="multipart/form-data">
            <div class="form-group mb-3">
                <label>Nombre del Concierto:</label>
                <input type="text" class="form-control" name="concert_name" required>
            </div>
            <div class="form-group mb-3">
                <label>Fecha:</label>
                <input type="date" class="form-control" name="concert_date" required>
            </div>
            <div class="form-group mb-3">
                <label>Hora:</label>
                <input type="time" class="form-control" name="concert_time" required>
            </div>
            <div class="form-group mb-3">
                <label>Artista:</label>
                <input type="text" class="form-control" name="concert_artist" required>
            </div>
            <div class="form-group mb-3">
                <label>Descripción:</label>
                <textarea class="form-control" name="concert_desc" rows="4" required></textarea>
            </div>
            <div class="form-group mb-3">
                <label>Género:</label>
                <input type="text" class="form-control" name="concert_genre" required>
            </div>
            <div class="form-group mb-3">
                <label>Lugar:</label>
                <input type="text" class="form-control" name="concert_venue" required>
            </div>
            <div class="form-group mb-3">
                <label>Precio UB:</label>
                <input type="number" class="form-control" name="concert_ubprice" required>
            </div>
            <div class="form-group mb-3">
                <label>Precio LB:</label>
                <input type="number" class="form-control" name="concert_lbprice" required>
            </div>
            <div class="form-group mb-3">
                <label>Precio VIP:</label>
                <input type="number" class="form-control" name="concert_vipprice" required>
            </div>
            <div class="form-group mb-3">
                <label>Precio GenAd:</label>
                <input type="number" class="form-control" name="concert_genadprice" required>
            </div>
            <div class="form-group mb-3">
                <label>Contacto:</label>
                <input type="text" class="form-control" maxlength=250 name="concert_contact" placeholder="Correo de contacto" required>
            </div>

            <div class="form-group mb-3">
                <label class="form-label" for="image">Imagen del Concierto:</label>
                <input type="file" id="image" name="image" class="form-control" accept="image/jpg, image/jpeg, image/png" required>
            </div>

            <div>
                <button type="submit" class="btn btn-success col" name="submit">Guardar</button>
                <a href="viewconcert.php" class="btn btn-danger col">Cancelar</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
