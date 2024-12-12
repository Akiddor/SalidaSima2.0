<?php
require 'backend/db/db.php';

$pallet_ids = isset($_GET['pallets']) ? explode(',', $_GET['pallets']) : [];
$folio = isset($_GET['folio']) ? $_GET['folio'] : '';

if (empty($pallet_ids)) {
    die('No se seleccionaron pallets para imprimir.');
}

// Verificar si el folio ya existe en la tabla folios_impresos
$check_folio_query = "SELECT COUNT(*) FROM folios_impresos WHERE folio_id = (SELECT id FROM Folios WHERE folio_number = '$folio')";
$result = mysqli_query($enlace, $check_folio_query);
$count = mysqli_fetch_row($result)[0];

if ($count > 0) {
    echo "<script>
          alert('El folio " . addslashes(htmlspecialchars($folio)) . " ya existe. Por favor, ingrese un folio único.');
          window.location.href = 'index.php';
          </script>";
    exit;
}

// Obtener el ID del folio
$folio_query = "SELECT id FROM Folios WHERE folio_number = '$folio'";
$folio_result = mysqli_query($enlace, $folio_query);
if ($folio_result && mysqli_num_rows($folio_result) > 0) {
    $folio_row = mysqli_fetch_assoc($folio_result);
    $folio_id = $folio_row['id'];

    // Insertar los pallets en la tabla folios_impresos
    foreach ($pallet_ids as $pallet_id) {
        $insert_query = "INSERT INTO folios_impresos (pallet_id, folio_id) VALUES ($pallet_id, $folio_id)";
        mysqli_query($enlace, $insert_query);
    }
} else {
    echo "<script>
          alert('El número de folio ingresado no existe.');
          window.location.href = 'index.php';
          </script>";
    exit;
}

// Inicializar la variable $pallets como un array vacío
$pallets = [];

// Obtener los detalles de los pallets seleccionados
foreach ($pallet_ids as $pallet_id) {
    $query = "SELECT cs.*, m.numero_parte, m.nifco_numero FROM Cajas_scanned cs JOIN Modelos m ON cs.part_id = m.id WHERE cs.pallet_id = $pallet_id";
    $result = mysqli_query($enlace, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        $pallets[$pallet_id][] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Packing Slip</title>
    <link rel="stylesheet" href="css/print_pallet.css">
    <style>
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="/img/Simaaa.png " alt="Logo">
            <h1>PACKING LIST</h1>
        </div>

        <div class="company-info">
            <p>Servicios Para La Industria Maquiladora</p>
            <p>Calle José Gutiérrez #407</p>
            <p>Col. Deportistas C.P. 31124</p>
            <p>Chihuahua, Chihuahua México</p>
        </div>

        <div class="details">
            <div>
                <p><strong>Date:</strong> ____________</p>
                <p><strong>Folio:</strong> ____________</p>
                <p><strong>Hora:</strong> ____________</p>
            </div>
            <div>
                <p><strong>Bill To:</strong></p>
                <p>NIFCO</p>
                <p>Nicolás Gogol #11301</p>
                <p>Complejo Industrial</p>
                <p>CP 31109 Chihuahua, Chih. México</p>
            </div>
        </div>

        <div class="details">
            <div>
                <p><strong>Ship To:</strong></p>
                <p>NIFCO</p>
                <p>Nicolás Gogol #11301</p>
                <p>Complejo Industrial</p>
                <p>CP 31109 Chihuahua, Chih. México</p>
            </div>
        </div>

        <div class="table-container">
            <?php $pallet_counter = 1; ?>
            <?php foreach ($pallets as $pallet_id => $items): ?>
                <h2>Pallet <?php echo $pallet_counter++; ?></h2>
                <table>
                    <thead>
                        <tr>
                            <th>Part Number</th>
                            <th>Nifco</th>
                            <th>Serial</th>
                            <th>Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $pallet): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($pallet['numero_parte']); ?></td>
                                <td><?php echo htmlspecialchars($pallet['nifco_numero'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($pallet['serial_number']); ?></td>
                                <td><?php echo htmlspecialchars($pallet['quantity']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endforeach; ?>
        </div>

        <div class="no-print">
            <button onclick="window.print()">Imprimir</button>
        </div>
    </div>
</body>

</html>