<?php
session_start();
include 'config.php';
$user_id = $_SESSION['user_id'];

// Obtener la tarjeta actual del usuario
$select = mysqli_query($conn, "SELECT * FROM `tblpayment` WHERE userid = '$user_id'") 
    or die('La consulta falló');

if (mysqli_num_rows($select) > 0) {
    $fetch = mysqli_fetch_assoc($select);
} else {
    $fetch = null;
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vincular Tarjeta</title>
    <link rel="stylesheet" href="css/payment.css">
    <link rel="icon" type="image/x-icon" href="css/images/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
<div class="container">
    <br>
    <div class="card-container">
        <div class="front">
            <div class="image">
                <img src="css/paymentimg/chip.png" alt="">
                <img src="css/paymentimg/unknown.png" alt="" id="cardimage">
            </div>
            <div class="card-number-box">
                <?php
                if (isset($fetch['cardnum']) && !empty($fetch['cardnum'])) {
                    $cardNumber = $fetch['cardnum'];
                    $splitCardNumber = implode('-', str_split($cardNumber, 4));
                    echo $splitCardNumber;
                } else {
                    echo '4444-5555-2222-4444';
                }
                ?>
            </div>
            <div class="flexbox">
                <div class="box">
                    <span>Titular</span>
                    <div class="card-holder-name">
                        <?php echo (!empty($fetch['cardholder'])) ? $fetch['cardholder'] : 'NOMBRE COMPLETO'; ?>
                    </div>
                </div>
                <div class="box">
                    <span>Expiración</span>
                    <div class="expiration">
                        <span class="exp-month"><?php echo (!empty($fetch['monthexp'])) ? $fetch['monthexp'] : 'MM'; ?></span>
                        <span class="exp-year"><?php echo (!empty($fetch['yearexp'])) ? $fetch['yearexp'] : 'YY'; ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="back">
            <div class="stripe"></div>
            <div class="box">
                <span><?php echo (!empty($fetch['cvv'])) ? $fetch['cvv'] : '1234'; ?></span>
                <div class="cvv-box"></div>
                <img src="css/paymentimg/unknown.png" id="cvvimg" alt="">
            </div>
        </div>
    </div>

    <form action="" method="post" enctype="multipart/form-data">
        <span class="note">Estado de la tarjeta:
            <?php echo isset($fetch['status']) ? $fetch['status'] : 'Sin vincular'; ?>
        </span>

        <div class="radio-inputs">
            <label class="radio">
                <input type="radio" required name="radio" onclick="detectCardType('css/paymentimg/mastercard.png')"
                    <?php echo (isset($fetch['cardtype']) && $fetch['cardtype'] === 'Mastercard') ? 'checked' : ''; ?> value="Mastercard">
                <span class="name">MasterCard</span>
            </label>

            <label class="radio">
                <input type="radio" required name="radio" onclick="detectCardType('css/paymentimg/visa.png')"
                    <?php echo (isset($fetch['cardtype']) && $fetch['cardtype'] === 'Visa') ? 'checked' : ''; ?> value="Visa">
                <span class="name">Visa</span>
            </label>
        </div>

        <div class="inputBox">
            <span>Número de tarjeta</span>
            <input type="text" required maxlength="19" class="card-number-input" id="cardnumber" name="cardnumber"
                   placeholder="1234-5678-9012-3456"
                   value="<?php echo isset($fetch['cardnum']) ? $fetch['cardnum'] : ''; ?>"
                   oninput="formatCardNumberInput(this)">
        </div>

        <div class="inputBox">
            <span>Titular</span>
            <input type="text" required class="card-holder-input" placeholder="Ej. Juan Pérez" name="cardname"
                   value="<?php echo isset($fetch['cardholder']) ? $fetch['cardholder'] : ''; ?>">
        </div>

        <div class="flexbox">
            <div class="inputBox">
                <span>Mes (MM)</span>
                <select name="expmonth" required class="month-input">
                    <option value="month" selected disabled>Mes</option>
                    <?php
                    $selectedMonth = $fetch['monthexp'] ?? '';
                    for ($i = 1; $i <= 12; $i++) {
                        $formatted = sprintf("%02d", $i);
                        $isSel = ($selectedMonth == $formatted) ? 'selected' : '';
                        echo "<option value=\"$formatted\" $isSel>$formatted</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="inputBox">
                <span>Año (YYYY)</span>
                <select name="expyear" required class="year-input">
                    <?php
                    $selectedYear = $fetch['yearexp'] ?? '';
                    $year = date("Y");
                    for ($i = $year; $i <= $year + 10; $i++) {
                        $isSel = ($selectedYear == $i) ? 'selected' : '';
                        echo "<option value=\"$i\" $isSel>$i</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="inputBox">
                <span>CVV</span>
                <input type="number" maxlength="4" class="cvv-input" placeholder="1234" name="cvv"
                       value="<?php echo isset($fetch['cvv']) ? $fetch['cvv'] : ''; ?>">
            </div>
        </div>

        <input type="submit" name="link" value="Vincular Tarjeta" class="submit-btn">

        <br><br>
        <a href="home.php" id="cardlink" class="back-btn">
            <i class="fas fa-arrow-left"></i> VOLVER
        </a>
    </form>
</div>

<?php
if (isset($_POST['link'])) {

    $cardNumber = str_replace("-", "", $_POST['cardnumber']);
    $cardHolder = mysqli_real_escape_string($conn, $_POST['cardname']);
    $monthExp = $_POST['expmonth'];
    $yearExp = $_POST['expyear'];
    $cvv = $_POST['cvv'];
    $cardType = mysqli_real_escape_string($conn, $_POST['radio']);
    $user_id = $_SESSION['user_id'];

    $pin = random_int(10000000, 99999999);
    $pinAsString = strval($pin);

    $errors = [];
    $currentYear = date("Y");
    $currentMonth = date("m");

    // Validaciones
    if ($yearExp < $currentYear || ($yearExp == $currentYear && $monthExp < $currentMonth)) {
        $errors[] = "La tarjeta ya está expirada.";
    }
    if (!preg_match("/^\d{16}$/", $cardNumber)) {
        $errors[] = "Número de tarjeta inválido (16 dígitos).";
    }
    if (empty($cardHolder) || !preg_match("/^[a-zA-Z\s]+$/u", $cardHolder)) {
        $errors[] = "Nombre del titular inválido.";
    }
    if (!preg_match("/^\d{3,4}$/", $cvv)) {
        $errors[] = "CVV inválido (3 o 4 dígitos).";
    }

    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p style='background-color:red;color:white;'>$error</p>";
        }
    } else {
        // Verificar si el usuario ya tiene una tarjeta vinculada
        $check = mysqli_query($conn, "SELECT * FROM `tblpayment` WHERE userid='$user_id'");
        if (mysqli_num_rows($check) > 0) {
            // Actualizar tarjeta existente
            $update = mysqli_query($conn,
                "UPDATE `tblpayment`
                 SET cardnum='$cardNumber', cardholder='$cardHolder', monthexp='$monthExp',
                     yearexp='$yearExp', cvv='$cvv', cardtype='$cardType', pin='$pinAsString',
                     status='Vinculada'
                 WHERE userid='$user_id'"
            );
        } else {
            // Insertar nueva tarjeta
            $update = mysqli_query($conn,
                "INSERT INTO `tblpayment` (userid, cardnum, cardholder, monthexp, yearexp, cvv, cardtype, pin, status)
                 VALUES ('$user_id','$cardNumber','$cardHolder','$monthExp','$yearExp','$cvv','$cardType','$pinAsString','Vinculada')"
            );
        }

        if ($update) {
            echo "<p style='background-color:green;color:white;'>¡Tarjeta vinculada exitosamente!</p>";
            echo "<script>setTimeout(()=>{ window.location.reload(); },1500);</script>";
        } else {
            echo "<p style='background-color:red;color:white;'>Error al vincular la tarjeta. Intente de nuevo.</p>";
        }
    }
}
?>

<script>
function formatCardNumberInput(input) {
    var n = input.value.replace(/\D/g, '');
    var formatted = n.replace(/(\d{4})(?=\d)/g, '$1-');
    input.value = formatted;
}

window.onload = function() {
    var cardNumberInput = document.getElementById('cardnumber');
    formatCardNumberInput(cardNumberInput);
};

function detectCardType(imageSrc) {
    document.getElementById('cardimage').src = imageSrc;
    document.getElementById('cvvimg').src = imageSrc;
}

document.querySelector('.card-number-input').oninput = () => {
    document.querySelector('.card-number-box').innerText =
        document.querySelector('.card-number-input').value;
}

document.querySelector('.card-holder-input').oninput = () => {
    document.querySelector('.card-holder-name').innerText =
        document.querySelector('.card-holder-input').value;
}

document.querySelector('.month-input').oninput = () => {
    document.querySelector('.exp-month').innerText =
        document.querySelector('.month-input').value;
}

document.querySelector('.year-input').oninput = () => {
    document.querySelector('.exp-year').innerText =
        document.querySelector('.year-input').value;
}

document.querySelector('.cvv-input').onmouseenter = () => {
    document.querySelector('.front').style.transform = 'perspective(1000px) rotateY(-180deg)';
    document.querySelector('.back').style.transform = 'perspective(1000px) rotateY(0deg)';
}

document.querySelector('.cvv-input').onmouseleave = () => {
    document.querySelector('.front').style.transform = 'rotateY(0deg)';
    document.querySelector('.back').style.transform = 'rotateY(180deg)';
}

document.querySelector('.cvv-input').oninput = () => {
    document.querySelector('.cvv-box').innerHTML =
        document.querySelector('.cvv-input').value;
}
</script>

</body>
</html>

