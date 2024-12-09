<?php

require 'backend/db/db.php';

$pallet_ids = isset($_GET['pallets']) ? explode(',', $_GET['pallets']) : [];
$folio = isset($_GET['folio']) ? $_GET['folio'] : '';

if (empty($pallet_ids)) {
    die('No se seleccionaron pallets para imprimir.');
}

// Verificar si el folio ya existe en la tabla pallets_impresos
$check_folio_query = "SELECT COUNT(*) FROM pallets_impresos WHERE folio = '$folio'";
$result = mysqli_query($enlace, $check_folio_query);
$count = mysqli_fetch_row($result)[0];

if ($count > 0) {
    echo "<script>
          alert('El folio " . addslashes(htmlspecialchars($folio)) . " ya existe. Por favor, ingrese un folio único.');
          window.location.href = 'index.php';
          </script>";
    exit;
}


// Insertar el folio en la tabla pallets_impresos
$insert_folio_query = "INSERT INTO pallets_impresos (folio) VALUES ('$folio')";
mysqli_query($enlace, $insert_folio_query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/pallets.css">
    <title>Imprimir Pallets</title>
    <style>
        @media print {
            .no-print {
                display: none;
            }
            body {
                font-family: Arial, sans-serif;
                font-size: 14px;
                margin: 0;
                padding: 20px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }
            th, td {
                border: 1px solid #000;
                padding: 8px;
                text-align: left;
            }
            th {
                background-color: #f2f2f2;
            }
            .pallet-info {
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="logo">
        <img src="Simaaa.png" alt="Sima Solutions">
    </div>
    <h2><p>Folio: <?php echo htmlspecialchars($folio); ?></p></h2>

    <?php
    foreach ($pallet_ids as $pallet_id) {
        $pallet_query = "SELECT * FROM pallets WHERE id = $pallet_id";
        $pallet_result = mysqli_query($enlace, $pallet_query);
        $pallet = mysqli_fetch_assoc($pallet_result);

        $items_query = "SELECT * FROM items_scanned WHERE pallet_id = $pallet_id";
        $items_result = mysqli_query($enlace, $items_query);
    ?>
        <div class="pallet-info">
            <h2>Pallet: <?php echo htmlspecialchars($pallet['pallet_name']); ?></h2>
            <p>Fecha de creación: <?php echo date('d/m/Y H:i:s', strtotime($pallet['created_at'])); ?></p>
            <table>
                <thead>
                    <tr>
                        <th>Número de Parte</th>
                        <th>NIFCO</th>
                        <th>Serial</th>
                        <th>Cantidad</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($item = mysqli_fetch_assoc($items_result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['part_number']); ?></td>
                        <td><?php echo htmlspecialchars($item['nifco_numero']); ?></td>
                        <td><?php echo htmlspecialchars($item['serial_number']); ?></td>
                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                        <td><?php echo htmlspecialchars($item['fecha_y_hora']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <hr>
    <?php } ?>

    <div class="buttons no-print">
        <button onclick="window.print()">Imprimir</button>
        <button onclick="window.close()">Cerrar</button>
    </div>
</body>
</html>


