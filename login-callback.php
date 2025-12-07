<?php
session_start();
require_once 'vendor/autoload.php';  // Incluye el autoload de Composer para las librerías de Google
include 'config.php';  // Conexión a tu base de datos

// Configuración del cliente de Google
$client = new Google\Client();
$client->setClientId('678074892690-tki3sme5mv8r55k92188622h5mep16er.apps.googleusercontent.com');  
$client->setClientSecret('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'); // Sustituye con tu Client Secret de Google
$client->setRedirectUri('http://localhost/eventosTickets/login-callback.php');  // Asegúrate de que esta URI esté correcta
$client->addScope("email");
$client->addScope("profile");

// Verifica si hay un código de autorización en la URL
if (isset($_GET['code'])) {
    try {
        // Muestra el código de autorización para depuración
        echo "Código de autorización recibido: " . $_GET['code'];
        echo "<br>";  // Salto de línea para mejor legibilidad
        
        // Obtén el token de acceso de Google usando el código de autorización
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
        
        // Verifica si se obtuvo el token
        if (!isset($token['access_token'])) {
            echo "Error: No se pudo obtener el token de acceso.";
            exit;
        }

        // Configura el cliente con el token de acceso
        $client->setAccessToken($token['access_token']);

        // Muestra el token para depuración
        echo "Token de acceso recibido: ";
        print_r($token);
        echo "<br>";
        
        // Obtén la información del usuario desde Google
        $oauth = new Google\Service\Oauth2($client);
        $userInfo = $oauth->userinfo->get();

        // Verifica si la información del usuario se obtiene correctamente
        if (!$userInfo) {
            echo "Error: No se pudo obtener la información del usuario.";
            exit;
        }

        // Datos del usuario
        $email = $userInfo->email;
        $name = $userInfo->name;
        $photo = $userInfo->picture;

        // Verifica si el usuario ya está registrado en tu base de datos
        $select = mysqli_query($conn, "SELECT * FROM `user_form` WHERE email = '$email'");
        if (mysqli_num_rows($select) > 0) {
            // Si el usuario existe, obtén los datos de la base de datos
            $row = mysqli_fetch_assoc($select);
            $_SESSION['user_id'] = $row['id'];  // Almacena el ID del usuario en la sesión
            header('Location: home.php');  // Redirige a la página principal
            exit;
        } else {
            // Si el usuario no existe, regístralo en la base de datos
            $insert = mysqli_query($conn, "INSERT INTO `user_form` (email, name, profile_picture) VALUES ('$email', '$name', '$photo')");
            
            if ($insert) {
                // Almacena el ID del nuevo usuario en la sesión
                $_SESSION['user_id'] = mysqli_insert_id($conn);
                header('Location: home.php');  // Redirige a la página principal
                exit;
            } else {
                // Si hubo un error al registrar el usuario
                echo "Error al registrar al usuario";
                exit;
            }
        }
    } catch (Exception $e) {
        // En caso de error durante la solicitud al token o la obtención de datos
        echo "Error: " . $e->getMessage();
        exit;
    }
} else {
    // Si no se ha recibido el código de autorización, muestra un error
    echo "Error: No se recibió el código de autorización.";
    exit;
}
?>
