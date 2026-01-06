<?php
session_start();
require_once 'vendor/autoload.php';  // Asegúrate de que esto esté incluido en home.php
include 'config.php';  // Conexión a la base de datos

// Verifica si el usuario está logueado
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = null;
}

// Verifica si el usuario ha solicitado cerrar sesión
if (isset($_GET['logout'])) {
    // Cierra la sesión local
    session_destroy();
    
    // Borra la cookie de Google si existe
    if (isset($_SESSION['access_token'])) {
        // Aquí eliminamos el token de acceso de Google
        $client = new Google\Client();
        $client->setClientId('TU_CLIENT_ID');
        $client->setClientSecret('TU_CLIENT_SECRET');
        
        // Revoca el token de acceso
        $client->revokeToken($_SESSION['access_token']);
        
        // Borra la cookie que guarda el token
        unset($_SESSION['access_token']);
    }
    
    // Redirige a la página de login
    header("Location: login.php");
    exit();
}

$sql = "SELECT * FROM tblconcert";
$all_concert = $conn->query($sql);

$select = mysqli_query($conn, "SELECT * FROM `user_form` WHERE id = '$user_id'") or die('query failed');
if(mysqli_num_rows($select) > 0) {
    $fetch = mysqli_fetch_assoc($select);
} else {
    $fetch = null;
}

$itemsPerPage = 3;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $itemsPerPage;
$sql = "SELECT * FROM tblconcert LIMIT $offset, $itemsPerPage";
$all_concert = $conn->query($sql);

$totalConcerts = $conn->query("SELECT COUNT(*) FROM tblconcert")->fetch_row()[0];
$totalPages = ceil($totalConcerts / $itemsPerPage);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Principal</title>
    <link rel="icon" type="image/x-icon" href="css/images/logo.png">
    <link rel="stylesheet" href="css/homepage.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        /* Sección de los conciertos */
        #concerts .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-evenly;  /* Distribuye las tarjetas de forma equilibrada */
            gap: 20px;  /* Espacio entre las tarjetas */
        }

        /* Estilo de las tarjetas */
        #concerts .card {
            width: 250px;  /* Tamaño adecuado para las tarjetas */
            height: auto;  /* Ajusta la altura automáticamente */
            margin: 15px;
            padding: 15px;
            background-color: #fff;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);  /* Sombra más suave */
            border-radius: 8px;
            transition: transform 0.3s ease-in-out;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Efecto hover más sutil */
        #concerts .card:hover {
            transform: translateY(-5px);
        }

        /* Ajuste de las imágenes dentro de las tarjetas */
        #concerts .card .imgBx {
            width: 100%;
            height: 150px;  /* Ajuste de la altura de la imagen */
            overflow: hidden;
            margin-bottom: 15px;
        }

        #concerts .card .imgBx img {
            width: 100%;
            height: 100%;
            object-fit: cover;  /* Asegura que la imagen cubra el área sin distorsionarse */
        }

        /* Título de la tarjeta */
        #concerts .card h2 {
            font-size: 18px;  /* Aumentar un poco el tamaño del título */
            text-align: center;
            margin-bottom: 10px;
        }

        /* Descripción dentro de la tarjeta */
        #concerts .card .desc p {
            font-size: 14px;  /* Tamaño más pequeño para la descripción */
            color: #555;
            line-height: 1.5;
            text-align: center;
            margin-bottom: 15px;
        }

        /* Botón dentro de la tarjeta */
        #concerts .card .button-container a {
            font-size: 14px;  /* Ajuste del tamaño del texto del botón */
            padding: 8px 15px;
            background-color: #00ADB5;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        #concerts .card .button-container a:hover {
            background-color: #007c80;
        }

        /* Ajustes para la paginación */
        .pagination {
            margin-top: 20px;
            text-align: center;
        }

        .pagination a {
            padding: 10px 15px;
            margin: 0 5px;
            border-radius: 5px;
            background-color: #00ADB5;
            color: white;
            text-decoration: none;
        }

        .pagination a.active {
            background-color: #007c80;
        }

    </style>
</head>

<body>

    <header class="header" style="box-shadow:0 10px 10px rgba(0,0,0,.2);">
        <a href="home.php" class="logo">
            <i class="fas fa-music" style="color: #00ADB5;"></i>
            <span style="color:#00ADB5;">Musi</span>Verse</a>

        <nav class="navbar">
            <a href="#home">Inicio</a>
            <a href="#concerts">Conciertos</a>
            <a href="#about">Sobre Nosotros</a>
            <a href="#contact">Contáctanos</a>
            <a href="ticketshistory.php">Mis Entradas</a>
            <a href="update_profile.php">Perfil</a>
            <a href="home.php?logout=1" class="logout">Cerrar Sesión</a>
        </nav>

        <div id="menu-bars" class="fas fa-bars"></div>
    </header>

    <script src="js/navbar.js"></script>

    <section class="home" id="home">
        <div class="wrapper">
            <div class="box">
                <div></div><div></div><div></div><div></div><div></div>
                <div></div><div></div><div></div><div></div>
            </div>
        </div>

        <div class="content">
            <img src="css/images/bghome.png" class="musilogo">

            <h3>Tu Universo de Entradas Musicales <span>/ MUSIVERSE.PH</span></h3>
            <a href="ticketshistory.php" class="btn">Mis Entradas</a>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

        <section class="con" id="concerts">
            <div class="content2">
                <h3>Próximos <span>Conciertos</span></h3>
                <p class="intro">
                    ¡Prepárate para un viaje musical inolvidable! Estamos emocionados de presentarte nuestros próximos conciertos,
                    donde el escenario cobrará vida con actuaciones sensacionales de algunos de los artistas más reconocidos.
                </p>
            </div>

            <!-- Paginación -->
            <?php
            $startPage = max(1, $page - 2);
            $endPage = min($totalPages, $startPage + 4);

            if($endPage - $startPage + 1 < 5) {
                $startPage = max(1, $endPage - 4);
            }
            ?>

            <div class="pagination">
                <?php if($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>">&lt;</a>
                <?php endif; ?>

                <?php for($i = $startPage; $i <= $endPage; $i++): ?>
                    <a href="?page=<?php echo $i; ?>" <?php if($i == $page) echo 'class="active"'; ?>>
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if($page < $totalPages): ?>
                    <a href="?page=<?php echo $page + 1; ?>">&gt;</a>
                <?php endif; ?>
            </div>

            <br>

            <!-- Mostrar conciertos -->
            <?php
            while($row = $all_concert->fetch_assoc()) {
                $concertId = $row["concert_id"];
                $url = 'concert.php?concert_id='.urlencode($concertId);
            ?>
                <div class="container">
                    <div class="card">
                        <div class="imgBx">
                            <a href="#">
                                <?php echo '<img src="adminside/uploaded_img/'.$row["image"].'">'; ?>
                            </a>
                        </div>
                        <h2><?php echo $row["concert_name"]; ?></h2>
                        <div class="desc">
                            <p>
                                Artista: <?php echo $row["concert_artist"]; ?><br>
                                Fecha del Concierto: <?php echo date('F j, Y', strtotime($row['concert_date'])); ?><br>
                                ID del Concierto: <?php echo $row["concert_id"]; ?><br>
                                Género: <?php echo $row["concert_genre"]; ?><br>
                                Lugar: <?php echo $row["concert_venue"]; ?>
                            </p>
                        </div>
                        <div class="button-container">
                            <a href="<?php echo $url; ?>" class="button">Ver Más</a>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </section>
    </section>
</body>
<footer>

    <div class="row secondary">
        <div>
            <p><i class="fas fa-phone-alt"></i></p>
            <p>+34 674 20 69 89</p>
        </div>
        <div>
            <p><i class="fas fa-envelope"></i></p>
            <p>casubin@gmail.com</p>
        </div>
        <div>
            <p><i class="fas fa-map-marker-alt"></i></p>
            <p>c/ Escalante 11</p>
        </div>
    </div>

    <div class="row copyright">
        <p>Copyright &copy; 2025 Musiverse | Todos los derechos reservados</p>
    </div>
</footer>


</html>