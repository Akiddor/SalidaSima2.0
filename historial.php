<?php
require 'backend/db/db.php';

// Obtener los días archivados ordenados de los más recientes a los más antiguos
$historialQuery = "SELECT * FROM Days WHERE status = 'archivado' ORDER BY day_date DESC";
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
    <script src="index.js" defer></script>
    <link rel="icon" href="simafa.png" type="image/sima">
</head>
<body>
    <header>
        <a href="index.php" class="home-icon"><i class="fas fa-home"></i></a>
        <a href="historial.php" class="history-icon"><i class="fas fa-history"></i> Historial</a>
    </header>
    <h1>Historial de Días Archivados</h1>

    <div class="accordion custom-accordion">
        <?php if ($historialResult && mysqli_num_rows($historialResult) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($historialResult)): ?>
                <div class="accordion-item custom-accordion-item">
                    <div class="accordion-header custom-accordion-header">
                        <h3><?php echo date("d/m/Y", strtotime($row['day_date'])); ?></h3>
                    </div>
                    <div class="accordion-body custom-accordion-body">
                        <!-- Sección de folios -->
                        <div class="accordion custom-accordion">
                            <?php
                            $foliosQuery = "SELECT * FROM Folios WHERE day_id = " . $row['id'] . " ORDER BY folio_number ASC";
                            $foliosResult = mysqli_query($enlace, $foliosQuery);

                            if ($foliosResult && mysqli_num_rows($foliosResult) > 0):
                                while ($folio = mysqli_fetch_assoc($foliosResult)):
                                    ?>
                                    <div class="accordion-item custom-accordion-item" id="folio-<?php echo $folio['id']; ?>"
                                        data-folio-id="<?php echo $folio['id']; ?>">
                                        <div class="accordion-header custom-accordion-header">
                                            <h4><?php echo htmlspecialchars($folio['folio_number']); ?></h4>
                                        </div>
                                        <div class="accordion-body custom-accordion-body">
                                            <!-- Sección de pallets -->
                                            <div class="pallets">
                                                <?php
                                                $palletsQuery = "SELECT * FROM Pallets WHERE folio_id = " . $folio['id'] . " ORDER BY CAST(SUBSTRING_INDEX(pallet_number, ' ', -1) AS UNSIGNED) ASC";
                                                $palletsResult = mysqli_query($enlace, $palletsQuery);

                                                if ($palletsResult && mysqli_num_rows($palletsResult) > 0):
                                                    while ($pallet = mysqli_fetch_assoc($palletsResult)):
                                                        ?>
                                                        <div class="pallet custom-pallet" data-pallet-id="<?php echo $pallet['id']; ?>">
                                                            <h5><?php echo htmlspecialchars($pallet['pallet_number']); ?></h5>
                                                            <div class="pallet-actions">
                                                                <button class="btn-select-pallet" data-pallet-id="<?php echo $pallet['id']; ?>">
                                                                    <i class="fas fa-check-square"></i> Seleccionar
                                                                </button>
                                                            </div>

                                                            <!-- Tabla de registros para el pallet -->
                                                            <table class="custom-table">
                                                                <thead>
                                                                    <tr>
                                                                        <th class="custom-th">Número de Parte</th>
                                                                        <th class="custom-th">NIFCO</th>
                                                                        <th class="custom-th">Serial</th>
                                                                        <th class="custom-th">Cantidad</th>
                                                                        <th class="custom-th">Acciones</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php
                                                                    $itemsQuery = "SELECT cs.*, m.numero_parte, m.nifco_numero FROM Cajas_scanned cs JOIN Modelos m ON cs.part_id = m.id WHERE cs.pallet_id = " . $pallet['id'];
                                                                    $itemsResult = mysqli_query($enlace, $itemsQuery);

                                                                    if ($itemsResult && mysqli_num_rows($itemsResult) > 0):
                                                                        while ($item = mysqli_fetch_assoc($itemsResult)):
                                                                            ?>
                                                                           <tr data-item-id="<?php echo $item['id']; ?>">
    <td class="custom-td">
        <?php echo htmlspecialchars($item['numero_parte']); ?>
    </td>
    <td class="custom-td">
        <?php echo htmlspecialchars($item['nifco_numero']); ?>
    </td>
    <td class="custom-td">
        <?php echo htmlspecialchars($item['serial_number']); ?>
    </td>
    <td class="custom-td">
        <?php echo htmlspecialchars($item['quantity']); ?>
    </td>
    <td class="custom-td">
        <button class="btn-edit-item" data-item-id="<?php echo $item['id']; ?>">Editar</button>
        <button class="btn-delete-item" data-item-id="<?php echo $item['id']; ?>">Eliminar</button>
    </td>
</tr>   
                                                                            <?php
                                                                        endwhile;
                                                                    else:
                                                                        ?>
                                                                        <tr>
                                                                            <td class="custom-td" colspan="5">No hay registros para este pallet.
                                                                            </td>
                                                                        </tr>
                                                                        <?php
                                                                    endif;
                                                                    ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <?php
                                                    endwhile;
                                                else:
                                                    ?>
                                                    <p>No hay pallets disponibles para este folio.</p>
                                                    <?php
                                                endif;
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                endwhile;
                            else:
                                ?>
                                <p>No hay folios disponibles para esta fecha.</p>
                                <?php
                            endif;
                            ?>
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