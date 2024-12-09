    <?php
    require 'backend/db/db.php';

    $pallet_id = isset($_GET['pallet_id']) ? intval($_GET['pallet_id']) : 0;

    if ($pallet_id <= 0) {
        die('Invalid pallet ID');
    }

    $pallet_query = "SELECT * FROM pallets WHERE id = $pallet_id";
    $pallet_result = mysqli_query($enlace, $pallet_query);
    $pallet = mysqli_fetch_assoc($pallet_result);

    $items_query = "SELECT * FROM items_scanned WHERE pallet_id = $pallet_id";
    $items_result = mysqli_query($enlace, $items_query);

    // Obtener el último número de folio
    $last_folio_query = "SELECT MAX(CAST(folio AS UNSIGNED)) AS last_folio FROM pallets_impresos";
    $last_folio_result = mysqli_query($enlace, $last_folio_query);
    $last_folio = mysqli_fetch_assoc($last_folio_result)['last_folio'] ?? 0;
    $next_folio = $last_folio + 1;
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Print Pallet <?php echo htmlspecialchars($pallet['pallet_name']); ?></title>
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
        <div class="pallet-info">
            <h1>Pallet: <?php echo htmlspecialchars($pallet['pallet_name']); ?></h1>
            <p>Fecha de creación: <?php echo date('d/m/Y H:i:s', strtotime($pallet['created_at'])); ?></p>
            <label for="folio">Folio:</label>
            <input type="text" id="folio" name="folio" value="<?php echo $next_folio; ?>" required>
        </div>

        <?php
        if ($items_result && mysqli_num_rows($items_result) > 0) {
        ?>
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
        <?php
        } else {
            echo "No hay registros de productos en este pallet.";
        }
        ?>

        <div class="buttons no-print">
            <button onclick="window.print()">Imprimir</button>
            <button onclick="window.close()">Cerrar</button>
        </div>
    </body>
    </html>
