<?php

session_start();
include 'config.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    echo "El ID de usuario no está definido en la sesión. Por favor, inicie sesión.";
    exit; // Detener el código si no está logueado
}

// Obtener el ID del usuario
$user_id = $_SESSION['user_id'];

// Consulta para obtener los datos del usuario
$query = mysqli_query($conn, "SELECT * FROM `user_form` WHERE id = '$user_id'") or die('query failed');

// Verificar si la consulta ha devuelto resultados
if (mysqli_num_rows($query) > 0) {
    // Si hay resultados, obtener los datos
    $fetch = mysqli_fetch_assoc($query);
} else {
    // Si no se encuentra al usuario en la base de datos
    echo "No se encontraron datos para el usuario con ID: $user_id.";
    exit; // Detener el código si no se encontró al usuario
}


if (isset($_POST['update_profile'])) {
    date_default_timezone_set('Asia/Singapore');
    $firstmsg = 'Registros actualizados por última vez a las ' . date('h:i:s A');

    $update_name = mysqli_real_escape_string($conn, $_POST['update_name']);
    $update_email = mysqli_real_escape_string($conn, $_POST['update_email']);
    $update_number = mysqli_real_escape_string($conn, $_POST['update_number']);
    $update_add = mysqli_real_escape_string($conn, $_POST['update_add']);
    $update_fullname = mysqli_real_escape_string($conn, $_POST['update_fullname']);
    $update_dob = mysqli_real_escape_string($conn, $_POST['update_dob']);

    mysqli_query($conn, "UPDATE `user_form` SET username = '$update_name', email = '$update_email' WHERE id = '$user_id'") or die('query failed');

    $old_pass = $_POST['old_pass'];
    $update_pass = mysqli_real_escape_string($conn, md5($_POST['update_pass']));
    $new_pass = mysqli_real_escape_string($conn, md5($_POST['new_pass']));
    $confirm_pass = mysqli_real_escape_string($conn, md5($_POST['confirm_pass']));

    // Comprobar si la contraseña existe en la base de datos
    if (!empty($update_pass) || !empty($new_pass) || !empty($confirm_pass)) {
        if ($fetch['password'] === NULL) {
            $message[] = '¡La contraseña actual no está definida! No puedes cambiarla.';
        } elseif ($update_pass != $old_pass) {
            $message[] = '¡La contraseña antigua no coincide!';
        } elseif ($new_pass != $confirm_pass) {
            $message[] = '¡La confirmación de contraseña no coincide!';
        } elseif (strlen($_POST['new_pass']) < 8) {
            $message[] = '¡La nueva contraseña debe tener al menos 8 caracteres!';
        } else {
            mysqli_query($conn, "UPDATE `user_form` SET password = '$confirm_pass' WHERE id = '$user_id'") or die('Query failed');
            $message[] = '¡Contraseña actualizada correctamente!';
        }
    } else {
        $message[] = 'Nota: No se han completado los campos relacionados con la contraseña';
    }

    if (!strtotime($update_dob) || strtotime($update_dob) > time()) {
        $message[] = 'Fecha de nacimiento inválida o futura.';
    } else {
        mysqli_query($conn, "UPDATE `user_form` SET dob = '$update_dob' WHERE id = '$user_id'") or die('query failed');
    }

    if (!preg_match('/^\d{11}$/', $update_number)) {
        $message[] = 'Formato de número de teléfono inválido (se requieren 11 dígitos).';
    } else {
        mysqli_query($conn, "UPDATE `user_form` SET phonenum = '$update_number' WHERE id = '$user_id'") or die('query failed');
    }

    if (strlen($update_add) < 10 || strlen($update_add) > 100) {
        $message[] = 'Dirección inválida (debe tener entre 10 y 100 caracteres).';
    } else {
        mysqli_query($conn, "UPDATE `user_form` SET address = '$update_add' WHERE id = '$user_id'") or die('query failed');
    }

    if (!preg_match('/^[a-zA-Z ]+$/', $update_fullname)) {
        $message[] = "El nombre completo solo debe contener letras.";
    } else {
        mysqli_query($conn, "UPDATE `user_form` SET fullname = '$update_fullname' WHERE id = '$user_id'") or die('query failed');
    }

    if (!ctype_digit($update_number)) {
        $message[] = "El número de teléfono solo debe contener números.";
    } else {
        mysqli_query($conn, "UPDATE `user_form` SET phonenum = '$update_number' WHERE id = '$user_id'") or die('query failed');
    }

    // Manejo de la imagen de perfil
    $update_image = $_FILES['update_image']['name'];
    $update_image_size = $_FILES['update_image']['size'];
    $update_image_tmp_name = $_FILES['update_image']['tmp_name'];
    $update_image_folder = 'uploaded_img/' . $update_image;

    if (!empty($update_image)) {
        if ($update_image_size > 2000000) {
            $message[] = 'La imagen es demasiado grande.';
        } else {
            $image_update_query = mysqli_query($conn, "UPDATE `user_form` SET image = '$update_image' WHERE id = '$user_id'") or die('query failed');
            if ($image_update_query) {
                move_uploaded_file($update_image_tmp_name, $update_image_folder);
            }
            $message[] = '¡Imagen actualizada exitosamente!';
        }
    }
} else if (isset($_POST['proceed_payment'])) {
    header('location:paymenthome.php');
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/profile.css">
    <title>Actualizar Perfil</title>
    <link rel="icon" type="image/x-icon" href="css/images/logo.png">
</head>

<body>
    <div class="update-profile">
        <?php
        $select = mysqli_query($conn, "SELECT * FROM `user_form` WHERE id = '$user_id'") or die('query failed');
        if (mysqli_num_rows($select) > 0) {
            $fetch = mysqli_fetch_assoc($select);
        }
        ?>

        <form action="" method="post" enctype="multipart/form-data">

            <?php
            if ($fetch['image'] == '') {
                // Si la imagen está vacía, mostramos una imagen por defecto
                echo '<img src="css/images/avatar.png">';
            } else {
                // Si hay una imagen almacenada, la buscamos en la carpeta 'uploaded_img/'
                $image_path = 'uploaded_img/' . $fetch['image'];
                if (file_exists($image_path)) {
                    // Si la imagen existe en el servidor, la mostramos
                    echo '<img src="' . $image_path . '" alt="Foto de perfil" class="profile-img">';
                } else {
                    // Si la imagen no se encuentra, mostramos una imagen por defecto
                    echo '<img src="css/images/avatar.png">';
                }
            }



            if (isset($firstmsg)) {
                echo '<div class="firstmsg">' . $firstmsg . '</div>';
            }

            if (isset($message)) {
                foreach ($message as $message) {
                    echo '<div class="message">' . $message . '</div>';
                }
            }
            ?>

            <div class="flex">
                <div class="inputBox">
                    <span>Nombre de usuario:</span>
                    <input type="text" name="update_name" value="<?php echo $fetch['username']; ?>" class="box">

                    <span>Correo electrónico:</span>
                    <input type="email" name="update_email" value="<?php echo $fetch['email']; ?>" class="box">

                    <span>Número de teléfono:</span>
                    <input type="text" name="update_number" value="<?php echo $fetch['phonenum']; ?>" class="box">

                    <span>Dirección:</span>
                    <input type="text" name="update_add" value="<?php echo $fetch['address']; ?>" class="box">

                    <span>Nombre completo:</span>
                    <input type="text" name="update_fullname" value="<?php echo $fetch['fullname']; ?>" class="box">

                    <span>Foto de perfil:</span>
                    <input type="file" name="update_image" accept="image/jpg, image/jpeg, image/png" class="box">
                </div>

                <div class="inputBox">
                    <input type="hidden" name="old_pass" value="<?php echo $fetch['password']; ?>">

                    <span>Contraseña antigua:</span>
                    <input type="password" name="update_pass" placeholder="Ingresa tu contraseña anterior" class="box">

                    <span>Nueva contraseña:</span>
                    <input type="password" name="new_pass" placeholder="Ingresa nueva contraseña" class="box">

                    <span>Confirmar contraseña:</span>
                    <input type="password" name="confirm_pass" placeholder="Confirma la nueva contraseña" class="box">

                    <span>Fecha de nacimiento:</span>
                    <input type="date" name="update_dob" value="<?php echo $fetch['dob']; ?>" class="box">
                </div>
            </div>

            <input type="submit" value="Actualizar Perfil" name="update_profile" class="btn">
            <input type="submit" value="Proceder al Pago" name="proceed_payment" class="btn">
            <a href="home.php" class="delete-btn">Volver</a>
        </form>
    </div>
</body>

</html>