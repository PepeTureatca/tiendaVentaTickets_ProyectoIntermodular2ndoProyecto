<?php
include 'config.php';
$sql = "SELECT * FROM tblconcert order by concert_date";
$all_concert = $conn->query($sql);


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/index.css">
    <title>Musiverse</title>
    <link rel="icon" type="image/x-icon" href="css/images/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

</head>

<body>
    <div class="hero">
        <video autoplay loop muted plays-inline class="back-video">
            <source src="css/images/concertopener.mp4" type="video/mp4">
        </video>

        <nav>
            <img src="css/images/logo.png" class="logo" width=90px>
            <ul>
                <li><a href="register.php"><i class="fas fa-user-plus"></i> REGISTRARSE</a></li>
                <li><a href="login.php"><i class="fas fa-user"></i> INICIAR SESION</a></li>

            </ul>
        </nav>
        <div class="content">
            <h1>MUSIVERSE</h1>
            <p>
                <i class="fas fa-music"></i> En musiverse encontrarás los mejores eventos musicales!
                <br>
                <i class="fas fa-map-marker-alt"></i> c/ Escalante 11, IES EL GRAO
            </p>
        </div>
    </div>

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