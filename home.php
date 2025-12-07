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

<!-- Aquí va todo tu código HTML y la estructura de la página -->

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
                                <h2><br><?php echo $row["concert_name"]; ?></h2>
                                <div class="desc">
                                    <p><br>
                                        Artista: <?php echo $row["concert_artist"]; ?><br>
                                        Fecha del Concierto: <?php echo date('F j, Y', strtotime($row['concert_date'])); ?><br>
                                        ID del Concierto: <?php echo $row["concert_id"]; ?><br>
                                        Género: <?php echo $row["concert_genre"]; ?><br>
                                        Lugar: <?php echo $row["concert_venue"]; ?>
                                    </p>
                                </div>
                                <div class="button-container">
                                    <a class="button" href="<?php echo $url; ?>">Ver Más</a>
                                </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </section>
    </section>

<section class="about" id="about">
    <br><br><br>
    <h3>Sobre Nosotros</h3>
    <p style="margin:50px;">
        En Musiverse, te llevamos a un universo lleno de experiencias musicales inolvidables.
        Disfruta de conciertos con artistas reconocidos y una mezcla vibrante de géneros musicales.
    </p>

    <div class="card-aboutus">

        <div class="cardcon">
            <img src="css/images/aboutus1.png">
            <div class="card-aboutcon">
                <h1>Selección de Conciertos Inigualable</h1>
                <p>
                    Ofrecemos una variedad diversa de conciertos para todos los gustos musicales:
                    pop, jazz, rock y mucho más.
                </p>
                <a href="#contact" class="card-button">Leer Más</a>
            </div>
        </div>

        <div class="cardcon">
            <img src="css/images/aboutus2.png">
            <div class="card-aboutcon">
                <h1>Compra de Entradas Fácil</h1>
                <p>
                    Explora eventos, elige tus asientos y paga fácilmente. Métodos de pago seguros
                    y confirmación instantánea.
                </p>
                <a href="#contact" class="card-button">Leer Más</a>
            </div>
        </div>

        <div class="cardcon">
            <img src="css/images/aboutus3.png">
            <div class="card-aboutcon">
                <h1>Comprometidos con tu Experiencia</h1>
                <p>
                    Un concierto es más que un evento: es una experiencia. Te brindamos información
                    útil sobre recintos, artistas y recomendaciones.
                </p>
                <a href="#contact" class="card-button">Leer Más</a>
            </div>
        </div>

        <div class="cardcon">
            <img src="css/images/aboutus4.png">
            <div class="card-aboutcon">
                <h1>Cartel de Artistas Increíble</h1>
                <p>
                    Desde estrellas globales hasta nuevos talentos, nuestra plataforma reúne lo mejor
                    del entretenimiento en vivo.
                </p>
                <a href="#contact" class="card-button">Leer Más</a>
            </div>
        </div>

    </div>
</section>

<section class="service">
    <div class="services">

        <div class="slide-container active">
            <div class="slide">
                <div class="servicecon">
                    <h3>¿Qué métodos de pago aceptan?</h3>
                    <p>
                        Aceptamos tarjetas de crédito, débito y pagos en efectivo.
                    </p>
                    <a href="faq.html" class="btn">Saber Más</a>
                </div>
                <video src="css/images/concert1.mp4" muted autoplay loop></video>
            </div>
        </div>

        <div class="slide-container">
            <div class="slide">
                <div class="servicecon">
                    <h3>¿Cómo puedo enterarme de los próximos conciertos?</h3>
                    <p>Suscríbete a nuestro boletín o síguenos en redes sociales.</p>
                    <a href="faq.html" class="btn">Saber Más</a>
                </div>
                <video src="css/images/concert2.mp4" muted autoplay loop></video>
            </div>
        </div>

        <div class="slide-container">
            <div class="slide">
                <div class="servicecon">
                    <h3>¿Hay tarifas adicionales al comprar entradas?</h3>
                    <p>No, no cobramos tarifas adicionales. Tu satisfacción es nuestra prioridad.</p>
                    <a href="faq.html" class="btn">Saber Más</a>
                </div>
                <video src="css/images/concert3.mp4" muted autoplay loop></video>
            </div>
        </div>

        <div id="next" onclick="next()">></div>
        <div id="prev" onclick="prev()"><</div>

    </div>
</section>

<section class="contact" id="contact">
    <h1 class="heading"><span>CONTÁCTANOS</span></h1>

    <p>
        Valoramos tus comentarios, preguntas o sugerencias.
        Completa el formulario y nuestro equipo te responderá lo antes posible.
    </p>

    <div class="contactcon">
        <form action="https://formspree.io/f/mwkdgerp" method="POST">

            <div class="inputBox">
                <input type="text" placeholder="Nombre" name="Name" value="<?php echo $fetch['fullname'] ?>">
                <input type="email" value="<?php echo $fetch['email'] ?>" placeholder="Correo" name="Email" readonly>
            </div>

            <div class="inputBox">
                <input type="number" placeholder="Número" name="Phonenumber" value="<?php echo $fetch['phonenum'] ?>" readonly>

                <select name="subject" id="subjectSelect">
                    <option value="" disabled selected>Selecciona un asunto</option>
                    <option value="Unlinking a Credit Card">Desvincular tarjeta</option>
                    <option value="Reporting a Bug">Reportar un error</option>
                    <option value="Other">Otro</option>
                </select>
            </div>

            <textarea name="Message" placeholder="Tu mensaje" cols="30" rows="10"></textarea>
            <input type="submit" value="Enviar Mensaje" class="btn">

        </form>

        <image src="css/images/contact.png" class="contactimg">
    </div>
</section>

<footer>
    <div class="row primary">

        <div class="columncomp">
            <h3>Musiverse.Corp</h3>
            <p>
                Bienvenido a Musiverse, tu puerta a un mundo de experiencias musicales en vivo.
                Creemos en el poder de la música para inspirar y conectar.
            </p>
        </div>

        <div class="column links">
            <h3>Enlaces Rápidos</h3>
            <ul>
                <li><a href="faq.html">Preguntas Frecuentes</a></li>
                <li><a href="ticketshistory.php">Entradas</a></li>
                <li><a href="#Meet">Nuestro Equipo</a></li>
                <li><a href="#concerts">Próximos Conciertos</a></li>
            </ul>
        </div>

        <div class="column subscribe">
            <form action="https://formspree.io/f/mwkdgerp" method="POST">
                <h3>Suscribirse</h3>
                <div>
                    <input type="email" name="email" placeholder="Tu correo electrónico" />
                    <button>Suscribirse</button>
                </div>
            </form>

            <div class="social">
                <a href="https://www.facebook.com" target="_blank"><i class="fab fa-facebook-square"></i></a>
                <a href="https://www.instagram.com" target="_blank"><i class="fab fa-instagram-square"></i></a>
                <a href="https://twitter.com" target="_blank"><i class="fab fa-twitter-square"></i></a>
            </div>
        </div>

    </div>

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

<script src="js/home.js"></script>
</body>
</html>
