<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include 'config.php';

$user_id = $_SESSION['user_id'];
$concertId = $_SESSION['concert_id'];

// Obtener información de pago
$select = mysqli_query($conn, "SELECT * FROM `tblpayment` WHERE userid = '$user_id'") or die('query failed');
$select2 = mysqli_query($conn, "SELECT * FROM `user_form` WHERE id = '$user_id'") or die('query failed');

// Obtener los datos de los asientos
$select3 = mysqli_query($conn, "SELECT * FROM `seats` WHERE concertid = '$concertId'") or die('query failed');

// Obtener información del concierto
$select4 = mysqli_query($conn, "SELECT * FROM `tblconcert` WHERE concert_id = '$concertId'") or die('query failed');

if (mysqli_num_rows($select) > 0) {
    $fetch = mysqli_fetch_assoc($select);
} else {
    $fetch = null;
}

if (mysqli_num_rows($select2) > 0) {
    $fetchuser = mysqli_fetch_assoc($select2);
} else {
    $fetchuser = null;
}

if (mysqli_num_rows($select4) > 0) {
    $fetchcon = mysqli_fetch_assoc($select4);
} else {
    $fetchcon = null;
}

// Asegurarse de que las variables de precios no estén indefinidas
$VIP = isset($fetchcon['vip_price']) ? $fetchcon['vip_price'] : 0;
$UB = isset($fetchcon['ub_price']) ? $fetchcon['ub_price'] : 0;
$LB = isset($fetchcon['lb_price']) ? $fetchcon['lb_price'] : 0;
$GA = isset($fetchcon['genad_price']) ? $fetchcon['genad_price'] : 0;

// Función para generar número de transacción
function generateTransactionNumber() {
    $prefix = 'TXN';
    $randomPart = rand(100000, 999999); // Genera un número aleatorio de 6 dígitos
    return $prefix.$randomPart;
}

$selectedSeats = "No se seleccionaron asientos"; // Por defecto
if (isset($_GET['encrypted_seats'])) {
    $encryptedSeats = urldecode($_GET['encrypted_seats']);
    $encryptionKey = 'RevsjvQoul';
    $decodedData = base64_decode($encryptedSeats);
    $decryptedSeats = xorEncryptDecrypt($decodedData, $encryptionKey);
    $selectedSeatsArray = json_decode($decryptedSeats, true);
    $selectedSeats = implode(', ', $selectedSeatsArray);
}

function xorEncryptDecrypt($input, $key) {
    $output = '';
    for ($i = 0; $i < strlen($input); $i++) {
        $output .= chr(ord($input[$i]) ^ ord($key[$i % strlen($key)]));
    }
    return $output;
}

// Precios y nombres de asientos seleccionados
$seatNames = array_map('trim', explode(',', $selectedSeats));
$totalPrice = 0;
$selectedSeatIds = [];
$selectedSeatNames = [];

foreach ($seatNames as $seatName) {
    $seatParts = array_map('trim', explode('-', $seatName));

    if (isset($seatParts[0], $seatParts[1])) {
        $seat = $seatParts[0];
        $section = $seatParts[1];

        $query = "SELECT price, seatid, seatname, section, status FROM seats WHERE concertid = '$concertId' AND section = '$section' AND seatname = '$seat'";
        $result = mysqli_query($conn, $query);

        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $price = $row['price'];
            $seatId = $row['seatid'];
            $chosenseatnames = $row['seatname'].' - '.$row['section'];
            $selectedSeatIds[] = $seatId;
            $selectedSeatNames[] = $chosenseatnames;
            $totalPrice += $price;
        } else {
            echo "Error ejecutando consulta: ".mysqli_error($conn);
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
    <title>Pago de Entradas</title>
    <link rel="stylesheet" href="css/seats.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="css/images/logo.png">
</head>
<body>

<div class="form-container">
    <form action="" method="post">
        <a href="home.php" class="cta-button"> <i class="fas fa-home"></i> Volver al menú principal</a>
        <h1><br>MAPA DEL CONCIERTO: </h1><br>
        <p>ID del Concierto: <?php echo $concertId; ?></p>
        <p>Nombre del Concierto: <?php echo $fetchcon['concert_name']; ?></p><br>
        <label for="text1" class="labeltext">Nombre del Cliente: <?php echo $fetchuser['fullname']; ?></label>

        <!-- Aquí se está restaurando el mapa de asientos -->
        <div class="seat-map-container">
            <img src="css/images/concertmap.png" alt="Mapa del Concierto" id="seatimage" usemap="#concertmap">
            <map name="concertmap">
                <?php
                // Generamos los enlaces para los asientos, con su respectivo estado (disponible/ocupado)
                $seats = mysqli_query($conn, "SELECT * FROM seats WHERE concertid = '$concertId'");
                while ($row = mysqli_fetch_assoc($seats)) {
                    $seatId = $row['seatid'];
                    $seatName = $row['seatname'];
                    $section = $row['section'];
                    $status = $row['status'];

                    // Determinamos el color de fondo del asiento dependiendo de su estado
                    $seatColor = ($status == 'Taken') ? 'red' : 'green';
                ?>
                    <area shape="rect" coords="x1,y1,x2,y2" href="pickseatscredit.php?seat=<?php echo $seatId; ?>" alt="<?php echo $seatName; ?>" title="<?php echo $seatName . ' (' . $status . ')'; ?>" style="background-color: <?php echo $seatColor; ?>;">
                <?php } ?>
            </map>
        </div>

        <br>

        <a href="pickseatscredit.php" class="cta-button">Elegir un asiento</a>

        <label for="text1" class="labeltext" id="seatprice">Precio de la zona de asientos:</label>
        <label for="text1" class="labeltext" id="paymentmode">Modo de pago: <?php echo $selectedPayment ?? 'Tarjeta de Crédito'; ?></label>

        <label for="text1" class="labeltext" id="labeltext">Asientos seleccionados: <?php echo $selectedSeats; ?></label>

        <label for="text1" class="labeltext">Fecha: <?php echo date("Y/m/d"); ?></label>
        
        <div class="note-container" id="note">
            <p><strong>Nota:</strong> Antes de proceder, por favor vincula tu tarjeta de crédito.</p>
        </div>

        <a href="paymentseats.php" id="cardlink" class="credit-btn">
            Tarjeta de Crédito: <?php echo isset($fetch['status']) ? $fetch['status'] : 'No vinculada'; ?>
        </a>

        <p id="total">Monto Total: <span id="result"><?php echo number_format($totalPrice, 2); ?></span></p>

    </form>
</div>

<script>
    var pinCorrect = true; // No más pin, asumiendo que no es necesario en este caso

    function handlePaymentSelection(paymentMethod) {
        document.getElementById('paymentmode').textContent = 'Modo de pago: ' + paymentMethod;
    }
</script>

</body>
</html>

