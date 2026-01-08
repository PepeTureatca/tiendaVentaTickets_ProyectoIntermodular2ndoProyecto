<?php
include "config.php";
$sql = "SELECT * FROM tblbuyer";
$all_buyer = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es" title="Diseño de Código">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Ver Compradores</title>
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
        <a href="dashboard.php">
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
        <section class="table__header">
            <h1>Información de Tickets de Compradores</h1>
            <div class="input-group">
                <input type="text" name="search" class="searchinput" placeholder="Buscar datos...">
                <img src="images/search.png" alt="">
            </div>
        </section>
        <section class="table__body">
            <table>
                <thead>
                    <tr>
                        <th scope="col">ID del Comprador</th>
                        <th scope="col">Nombre del Comprador</th>
                        <th scope="col">Asientos Elegidos</th>
                        <th scope="col">Modo de Pago</th>
                        <th scope="col">Teléfono</th>
                        <th scope="col">Nombre del Concierto</th>
                        <th scope="col">Fecha del Concierto</th>
                        <th scope="col">Cantidad de Tickets</th>
                        <th scope="col">Fecha de Pago</th>
                        <th scope="col">Número de Transacción</th>
                        <th scope="col">Precio Pagado</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while($row = $all_buyer->fetch_assoc()) {
                        ?>
                        <tr>
                            <td><?php echo $row['buyer_id'] ?></td>
                            <td><?php echo $row['buyer_name'] ?></td>
                            <td><?php echo $row['buyer_chosenseats'] ?></td>
                            <td><?php echo $row['payment_mode'] ?></td>
                            <td><?php echo $row['buyer_phonenum'] ?></td>
                            <td><?php echo $row['concert_name'] ?></td>
                            <td><?php echo $row['concert_date'] ?></td>
                            <td><?php echo $row['tickets_qty'] ?></td>
                            <td><?php echo $row['payment_date'] ?></td>
                            <td><?php echo $row['transaction_no'] ?></td>
                            <td><?php echo '&#8369;'.number_format($row['payment_price'], 2) ?></td>
                            <td><?php echo $row['status'] ?></td>
                            <td>
                                <a href="editbuyer.php?id=<?php echo $row['buyer_id']; ?>&concert_id=<?php echo $row['concert_id']; ?>&transaction_no=<?php echo $row['transaction_no']; ?>"
                                    class="link-dark"><i class="fa-solid fa-pen-to-square fs- me-1"></i></a>

                                <a href="#" class="link-dark"
                                    onclick="confirmDelete(<?php echo $row['buyer_id'] ?>, '<?php echo $row['transaction_no'] ?>')">
                                    <i class="fa-solid fa-trash fs-5 "></i>
                                </a>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

                    <script>
                        function confirmDelete(buyerId, transaction_no) {
                            var confirmation = confirm("¿Está seguro de que desea cancelar este pago?");
                            if (confirmation) {
                                window.location.href = 'deletebuyer.php?id=' + buyerId + '&transaction_no=' + transaction_no;
                            }
                        }
                        $(document).ready(function () {
                            $('.searchinput').keyup(function () {
                                var query = $(this).val();
                                $.ajax({
                                    type: 'POST',
                                    url: 'searchbuyer.php',
                                    data: { query: query },
                                    dataType: 'json',
                                    success: function (response) {
                                        updateTable(response);
                                    },
                                    error: function (xhr, status, error) {
                                        console.error(xhr.responseText);
                                    }
                                });
                            });

                            function updateTable(data) {
                                var tableBody = $('tbody');
                                tableBody.empty();

                                if (data.length > 0) {
                                    $.each(data, function (index, row) {
                                        var formattedPrice = new Intl.NumberFormat('es-PH', {
                                            style: 'currency',
                                            currency: 'PHP'
                                        }).format(row.payment_price);

                                        var newRow = '<tr>';
                                        newRow += '<td>' + row.buyer_id + '</td>';
                                        newRow += '<td>' + row.buyer_name + '</td>';
                                        newRow += '<td>' + row.buyer_chosenseats + '</td>';
                                        newRow += '<td>' + row.payment_mode + '</td>';
                                        newRow += '<td>' + row.buyer_phonenum + '</td>';
                                        newRow += '<td>' + row.concert_name + '</td>';
                                        newRow += '<td>' + row.concert_date + '</td>';
                                        newRow += '<td>' + row.tickets_qty + '</td>';
                                        newRow += '<td>' + row.payment_date + '</td>';
                                        newRow += '<td>' + row.transaction_no + '</td>';
                                        newRow += '<td>' + formattedPrice + '</td>';
                                        newRow += '<td>' + row.status + '</td>';
                                        newRow += '<td>';
                                        newRow += '<a href="editbuyer.php?id=' + encodeURIComponent(row.buyer_id) +
                                            '&concert_id=' + encodeURIComponent(row.concert_id) +
                                            '&transaction_no=' + encodeURIComponent(row.transaction_no) +
                                            '" class="link-dark"><i class="fa-solid fa-pen-to-square fs- me-1"></i></a>';

                                        newRow += '<a href="#" onclick="confirmDelete(' + row.buyer_id + ', \'' + row.transaction_no + '\')" class="link-dark"><i class="fa-solid fa-trash fs-5"></i></a>';
                                        newRow += '</td>';
                                        newRow += '</tr>';
                                        tableBody.append(newRow);
                                    });
                                } else {
                                    var noResultsRow = '<tr><td colspan="13">No se encontraron resultados</td></tr>';
                                    tableBody.append(noResultsRow);
                                }
                            }
                        });
                    </script>
                </tbody>
            </table>
        </section>
    </main>
</body>

</html>
