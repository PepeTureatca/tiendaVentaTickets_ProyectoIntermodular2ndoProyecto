<?php
include "config.php";
session_start();
$admin_id = $_SESSION['admin_id'];

$select = mysqli_query($conn, "SELECT * FROM `admin` WHERE admin_id = '$admin_id'") or die('query failed');
if (mysqli_num_rows($select) > 0) {
    $fetch = mysqli_fetch_assoc($select);
} else {
    $fetch = null;
}
?>
<!DOCTYPE html>
<html lang="es" title="Diseño de código">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Panel de Control</title>
    <link rel="icon" type="image/x-icon" href="images/logo.png">

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        integrity="sha512-Avb2QiuDEEvB4bZJYdft2mNjVShBftLdPG8FJ0V7irTLQ8Uo0qcPxh4Plq7G5tGm0rU+1SPhVotteLpBERwTkw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous">
    </script>
</head>

<div class="sidebar">
    <img src="images/logo.png" alt="Logo">
    <div class="sidebar-nav">
        <a href="#">
            <i class="fas fa-home"></i> Panel de Control
        </a>
        <a href="viewbuyer.php">
            <i class="fas fa-ticket-alt"></i> Compradores
        </a>
        <a href="viewconcert.php">
            <i class="fas fa-music"></i> Conciertos
        </a>
        <a href="viewusers.php">
            <i class="fas fa-users"></i> Usuarios
        </a>
        <a href="../login.php">
            <i class="fas fa-sign-out-alt"></i> Salir
        </a>
    </div>
</div>

<body>
    <main class="table">
        <div class="scrollcon">
            <section class="table__header">
                <h1> <i class="fas fa-user"></i> Bienvenido de nuevo,
                    <?php echo $fetch['admin_name'] ?>
                </h1>
            </section>
            <section class="table__body">
                <table>
                    <thead>
                        <tr>
                            <th scope="col">Información del Administrador:</th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody class="admincon">
                        <tr>
                            <td>
                                <div class="avatar-container">
                                    <img src="uploaded_img/eriel.jpg" alt="Avatar" class="avatar">
                                </div>
                            </td>
                            <td>
                                <p><i class="fas fa-user icon"></i> Nombre del Administrador:
                                    <?php echo $fetch['admin_name'] ?>
                                </p>
                                <br><br>
                                <p><i class="fas fa-calendar-alt icon"></i> Fecha de Ingreso:
                                    <?php echo date('F j, Y', strtotime($fetch['date_hired'])); ?>
                                </p>
                                <br><br>
                                <p><i class="fas fa-home icon "></i> Vive en
                                    <?php echo $fetch['admin_lived'] ?>
                                </p>

                            </td>
                            <td>
                                <p><i class="far fa-envelope icon"></i> Correo Electrónico:</p>
                                <p>
                                    <?php echo $fetch['admin_email'] ?>
                                </p>
                                <br><br>
                                <p><i class="far fa-clock icon"></i> Hora:</p>
                                <div id="clock"></div>

                                <script>
                                    function updateClock() {
                                        const madridTime = new Date().toLocaleString("es-ES", {
                                            timeZone: "Europe/Madrid"
                                        });
                                        const timeString = madridTime.split(',')[1].trim();
                                        document.getElementById('clock').innerText = timeString;
                                    }

                                    setInterval(updateClock, 1000);

                                    updateClock();
                                </script>

                            </td>
                            <td class="hidden-cell">
                                <p>Correo Electrónico:</p>
                                <p>
                                    <?php echo $fetch['admin_email'] ?>
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </section>
            <section class="table__body">
                <table>
                    <thead>
                        <tr>
                            <th scope="col">Estadísticas del Sistema de Conciertos:</th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="dashboard-container">
                                    <i class="fas fa-users"></i>
                                    <h2>Número de Usuarios</h2>
                                    <p>
                                        <?php
                                        $sql = "SELECT COUNT(*) AS NumberOfUsers FROM user_form";
                                        $result = $conn->query($sql);

                                        if ($result === false) {
                                            echo $conn->error;
                                        } else {
                                            $row = $result->fetch_assoc();
                                            if ($row !== null && isset($row['NumberOfUsers'])) {
                                                $count = $row['NumberOfUsers'];
                                                echo $count;
                                            } else {
                                                echo "No hay usuarios disponibles.";
                                            }
                                        }
                                        ?>
                                    </p>
                                </div>
                            </td>
                            <td>
                                <div class="dashboard-container">
                                    <i class="fas fa-ticket-alt"></i>
                                    <h2>Número de Transacciones de Boletos</h2>
                                    <p>
                                        <?php
                                        $sql = "SELECT COUNT(*) AS tickets FROM tblbuyer where status= 'Paid'";
                                        $result = $conn->query($sql);

                                        if ($result === false) {
                                            echo $conn->error;
                                        } else {
                                            $row = $result->fetch_assoc();
                                            if ($row !== null && isset($row['tickets'])) {
                                                $count = $row['tickets'];
                                                echo $count;
                                            } else {
                                                echo "No hay compradores disponibles.";
                                            }
                                        }
                                        ?>
                                    </p>
                                </div>
                            </td>
                            <td>
                                <div class="dashboard-container">
                                    <i class="fas fa-music"></i>
                                    <h2>Número de Conciertos Disponibles</h2>
                                    <p>
                                        <?php
                                        $sql = "SELECT COUNT(*) AS concert FROM tblconcert ";
                                        $result = $conn->query($sql);

                                        if ($result === false) {
                                            echo $conn->error;
                                        } else {
                                            $row = $result->fetch_assoc();
                                            if ($row !== null && isset($row['concert'])) {
                                                $count = $row['concert'];
                                                echo $count;
                                            } else {
                                                echo "No hay conciertos disponibles.";
                                            }
                                        }
                                        ?>
                                    </p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </section>

            <section class="table__body">
                <table>
                    <thead>
                        <tr>
                            <th scope="col">Estadísticas de Ingresos de Conciertos:</th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="dashboard-container">
                                    <i class="fa-solid fa-money-bill"></i>
                                    <h2>Ingresos del Día</h2>
                                    <p>
                                        <?php
                                        date_default_timezone_set('Europe/Madrid');
                                        $today = date("Y-m-d");
                                        $sql = "SELECT SUM(payment_price) AS today_revenue FROM tblbuyer WHERE payment_date = '$today' AND status ='Paid'";
                                        $result = $conn->query($sql);

                                        if ($result === false) {
                                            echo "Error: " . $conn->error;
                                        } else {
                                            $row = $result->fetch_assoc();

                                            if ($row !== null && isset($row['today_revenue'])) {
                                                $todayRevenue = $row['today_revenue'];
                                                echo "Ingresos de hoy: ₱" . number_format($todayRevenue, 2);
                                            } else {
                                                echo "No hay ingresos disponibles para hoy.";
                                            }
                                        }
                                        ?>
                                    </p>
                                </div>
                            </td>
                            <td>
                                <div class="dashboard-container">
                                    <i class="fa-solid fa-money-bill-trend-up"></i>
                                    <h2>Ingresos del Mes</h2>
                                    <p>
                                        <?php
                                        date_default_timezone_set('Europe/Madrid');

                                        $firstDayOfMonth = date("Y-m-01");
                                        $lastDayOfMonth = date("Y-m-t");

                                        $sql = "SELECT SUM(payment_price) AS monthly_revenue FROM tblbuyer WHERE payment_date BETWEEN '$firstDayOfMonth' AND '$lastDayOfMonth' AND status ='Paid'";
                                        $result = $conn->query($sql);

                                        if ($result === false) {
                                            echo "Error: " . $conn->error;
                                        } else {
                                            $row = $result->fetch_assoc();

                                            if ($row !== null && isset($row['monthly_revenue'])) {
                                                $monthlyRevenue = $row['monthly_revenue'];
                                                echo "Ingresos del mes: ₱" . number_format($monthlyRevenue, 2);
                                            } else {
                                                echo "No hay ingresos disponibles para este mes.";
                                            }
                                        }
                                        ?>
                                    </p>
                                </div>
                            </td>
                            <td>
                                <div class="dashboard-container">
                                    <i class="fa-solid fa-icons"></i>
                                    <h2>Concierto Más Vendido</h2>
                                    <p>
                                        <?php
                                        $sql = "SELECT concert_name, COUNT(*) as frequency FROM tblbuyer GROUP BY concert_name ORDER BY frequency DESC LIMIT 1";
                                        $result = $conn->query($sql);

                                        if ($result === false) {
                                            echo "Error: " . $conn->error;
                                        } else {
                                            $row = $result->fetch_assoc();

                                            if ($row !== null && isset($row['concert_name'])) {
                                                $topSellingConcert = $row['concert_name'];
                                                echo $topSellingConcert;
                                            } else {
                                                echo "No hay datos disponibles.";
                                            }
                                        }
                                        $conn->close();
                                        ?>
                                    </p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </section>
        </div>
    </main>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</body>

</html>