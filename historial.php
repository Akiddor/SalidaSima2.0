<?php
require 'backend/db/db.php';

// Fetch records older than 5 days
$historialQuery = "SELECT * FROM Pallets WHERE created_at < NOW() - INTERVAL 5 DAY ORDER BY created_at DESC";
$historialResult = mysqli_query($enlace, $historialQuery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial - SimaSolution</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/index.css">
    <script src="script.js" defer></script>
    <link rel="icon" href="simafa.png" type="image/sima">
</head>
<body>
    <header>
        <a href="index.php" class="home-icon"><i class="fas fa-home"></i></a>
        <a href="historial.php" class="history-icon"><i class="fas fa-history"></i> Historial</a>
    </header>
    <h1>Historial de Pallets</h1>

    <div class="accordion custom-accordion">
        <?php if ($historialResult && mysqli_num_rows($historialResult) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($historialResult)): ?>
                <div class="accordion-item custom-accordion-item">
                    <div class="accordion-header custom-accordion-header">
                        <h3><?php echo date("d/m/Y", strtotime($row['created_at'])); ?></h3>
                    </div>
                    <div class="accordion-body custom-accordion-body">
                        <div class="pallet custom-pallet">
                            <h4><?php echo htmlspecialchars($row['pallet_number']); ?></h4>
                            <div class="pallet-actions">
                                <button class="btn-select-pallet" data-pallet-id="<?php echo $row['id']; ?>">
                                    <i class="fas fa-check-square"></i> Seleccionar
                                </button>
                            </div>
                            <table class="custom-table">
                                <thead>
                                    <tr>
                                        <th class="custom-th">Número de Parte</th>
                                        <th class="custom-th">NIFCO</th>
                                        <th class="custom-th">Serial</th>
                                        <th class="custom-th">Cantidad</th>
                                        <th class="custom-th">Fecha</th>
                                        <th class="custom-th">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $recordsQuery = "SELECT cs.*, p.numero_parte, p.nifco_numero 
                                                   FROM Cajas_scanned cs 
                                                   JOIN Partes p ON cs.part_id = p.id 
                                                   WHERE cs.pallet_id = " . $row['id'];
                                    $recordsResult = mysqli_query($enlace, $recordsQuery);
                                    while ($record = mysqli_fetch_assoc($recordsResult)): ?>
                                        <tr class="custom-tr" data-id="<?php echo $record['id']; ?>">
                                            <td class="custom-td"><?php echo htmlspecialchars($record['numero_parte']); ?></td>
                                            <td class="custom-td"><?php echo htmlspecialchars($record['nifco_numero']); ?></td>
                                            <td class="custom-td"><?php echo htmlspecialchars($record['serial_number']); ?></td>
                                            <td class="custom-td"><?php echo htmlspecialchars($record['quantity']); ?></td>
                                            <td class="custom-td"><?php echo htmlspecialchars($record['scan_timestamp']); ?></td>
                                            <td class="custom-td">
                                                <div style="display: flex; gap: 10px;">
                                                    <button onclick="editRecord(<?php echo $record['id']; ?>)">
                                                        <i class="fas fa-edit"></i> Editar
                                                    </button>
                                                    <button class="btneliminar" data-id="<?php echo $record['id']; ?>">
                                                        <i class="fas fa-trash"></i> Borrar
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="no-pallets-message">No hay registros en el historial.</p>
        <?php endif; ?>
    </div>
</body>
</html>