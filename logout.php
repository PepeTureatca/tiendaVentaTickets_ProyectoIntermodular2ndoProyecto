<?php
session_start();
require_once 'vendor/autoload.php';  // Incluye el autoload de Composer para las librerías de Google

// Verificar si el usuario está logueado
if (isset($_SESSION['user_id'])) {
    // Establece el cliente de Google
    $client = new Google\Client();
    $client->setClientId('678074892690-tki3sme5mv8r55k92188622h5mep16er.apps.googleusercontent.com');
    $client->setClientSecret('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');
    
    // Si el token de Google existe en la sesión, lo revocas
    if (isset($_SESSION['access_token'])) {
        $client->revokeToken($_SESSION['access_token']);  // Esto elimina el token de Google
    }
}

// Eliminar todas las variables de sesión
session_unset();  // Elimina todas las variables de sesión
session_destroy();  // Destruye la sesión

// Eliminar cookies de sesión PHP
setcookie(session_name(), '', time() - 3600, '/');  // Eliminar la cookie PHPSESSID

// Eliminar cookies de Google si están presentes
setcookie('G_AUTHUSER_H', '', time() - 3600, '/');  // G_AUTHUSER_H
setcookie('G_AUTH', '', time() - 3600, '/');         // G_AUTH
setcookie('G_AUTHUSER_ID', '', time() - 3600, '/');  // G_AUTHUSER_ID

// Redirigir al login
header('Location: login.php');
exit();
?>
