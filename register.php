<?php
include 'config.php';

if(isset($_POST['submit'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = mysqli_real_escape_string($conn, $_POST['password']);
    $cpass = mysqli_real_escape_string($conn, $_POST['cpassword']);
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $phone = mysqli_real_escape_string($conn, $_POST['phonenum']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    
    // Validar si se subió una imagen
    $image = $_FILES['image']['name'];
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = 'uploaded_img/'.$image;

    // Función para validar la contraseña
    function isPasswordStrong($pass) {
        return strlen($pass) >= 8;  // Solo requiere al menos 8 caracteres
    }

    if(!isPasswordStrong($pass)) {
        $message[] = 'La contraseña debe tener al menos 8 caracteres.';
    } else if(!preg_match('/^[a-zA-Z ]+$/', $fullname)) {
        $message[] = "El nombre completo debe contener solo caracteres alfabéticos.";
    } else if(!ctype_digit($phone) || strlen($phone) != 9) {
        $message[] = "El número de teléfono debe tener exactamente 9 dígitos.";
    } else {
        $select = mysqli_query($conn, "SELECT email FROM `user_form` WHERE email = '$email'");
        if(mysqli_num_rows($select) > 0) {
            $message[] = 'El usuario ya existe.';
        } else {
            if(!strtotime($dob) || strtotime($dob) > time()) {
                $message[] = 'Fecha de nacimiento inválida o no se permiten fechas futuras.';
            } elseif(strlen($address) < 10 || strlen($address) > 100) {
                $message[] = 'La dirección debe tener entre 10 y 100 caracteres.';
            } else {
                if($pass != $cpass) {
                    $message[] = 'Las contraseñas no coinciden.';
                } elseif($image_size > 2000000) {
                    $message[] = 'El tamaño de la imagen es demasiado grande.';
                } else {
                    $accdate = date('Y-m-d H:i:s');
                    $hashed_password = md5($_POST['password']);

                    // Si no se sube una imagen, se establece NULL
                    $image_insert = $image ? "'$image'" : "NULL";

                    $insert = mysqli_query($conn, "INSERT INTO `user_form` (username, email, password, fullname, dob, phonenum, address, accdate, image) 
                        VALUES ('$username', '$email', '$hashed_password', '$fullname', '$dob', '$phone', '$address', '$accdate', $image_insert)") or die('Error en la consulta');

                    if($insert) {
                        session_start();
                        if($image) {
                            move_uploaded_file($image_tmp_name, $image_folder);
                        }
                        $message[] = 'Registro exitoso';
                        header('location: login.php');
                    } else {
                        $message[] = 'Error al registrar el usuario.';
                    }
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse</title>
    <link rel="icon" type="image/x-icon" href="css/images/logo.png">
    <link rel="stylesheet" href="css/profile.css">
</head>

<body>
    <div class="form-container">
        <form action="" method="post" enctype="multipart/form-data">
            <h3 style="text-decoration:underline; text-decoration-thickness: 5px; text-decoration-color: #0D7377;">
                Regístrate ahora</h3> <br>
            <?php
            if(!empty($message)) {
                foreach($message as $msg) {
                    echo '<div class="message">'.$msg.'</div>';
                }
            }
            ?>
            <div class="input-group">
                <label for="username" class="lbl">Nombre de usuario:</label>
                <input type="text" id="username" name="username" placeholder="Ingresa tu nombre de usuario" class="box" required maxlength="100">
            </div>

            <div class="input-group">
                <label for="email" class="lbl">Correo electrónico:</label>
                <input type="email" id="email" name="email" placeholder="Ingresa tu correo electrónico" class="box" required maxlength="100">
            </div>

            <div class="input-group">
                <label for="fullname" class="lbl">Nombre completo:</label>
                <input type="text" id="fullname" name="fullname" placeholder="Ingresa tu nombre completo. Ej. Juan Pérez"
                    class="box" required maxlength="100">
            </div>

            <div class="input-group">
                <label for="address" class="lbl">Dirección:</label>
                <input type="text" id="address" name="address" placeholder="Ingresa tu dirección" class="box" required
                    maxlength="100">
            </div>

            <div class="input-group">
                <label for="dob" class="lbl">Fecha de nacimiento:</label>
                <input type="date" id="dob" name="dob" class="box" required maxlength="100">
            </div>

            <div class="input-group">
                <label for="phonenum" class="lbl">Número de teléfono:</label>
                <input type="number" id="phonenum" name="phonenum" placeholder="Ingresa tu número de teléfono. Ej. 096734111"
                    class="box" required maxlength="9">
            </div>

            <div class="input-group">
                <label for="password" class="lbl">Contraseña:</label>
                <input type="password" id="password" name="password" placeholder="Ingresa tu contraseña" class="box" required
                    maxlength="100">
            </div>

            <div class="input-group">
                <label for="cpassword" class="lbl">Confirmar contraseña:</label>
                <input type="password" id="cpassword" name="cpassword" placeholder="Confirma tu contraseña" class="box"
                    required maxlength="100">
            </div>

            <div class="input-group">
                <label for="image" class="lbl">Foto de perfil (Opcional):</label>
                <input type="file" id="image" name="image" class="box" accept="image/jpg, image/jpeg, image/png">
            </div>

            <input type="submit" name="submit" value="Registrarse ahora" class="btn" id="register">
            <p>¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a></p>
        </form>
    </div>

    <style>
        /* Aquí va el estilo */
        .form-container {
            min-height: 140vh;
            background-color: var(--light-bg);
            display: flex;
            justify-content: center;
            padding: 20px;
            background-color: #3c4242;
            background-image: url(css/images/bg.jpg);
            background-repeat: no-repeat;
            background-blend-mode: soft-light;
            background-size: cover;
            background-position: center;
            border: 6px solid #0D7377;
            box-sizing: border-box;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.8);
            width: 100%;
        }
        body {
            background: url("images/bgwave2.jpg");
            background-color: #333;
            background-blend-mode: soft-light;
            background-repeat: no-repeat;
            background-size: 100%;
        }
        .lbl {
            width: 100%;
            display: inline-block;
            text-align: left;
            font-size: 15px;
            color: var(--white);
            font-weight: bold;
        }
    </style>
</body>

</html>
