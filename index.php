<?php include 'backindex.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SimaSolution</title>
    <!-- Estilos de Font Awesome y fuentes de Google Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Poppins:wght@400;600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="/css/index.css">
    <link rel="icon" href="simafa.png" type="image/sima">
</head>

<body>
    <header>
        <!-- Enlaces a la página de inicio y al historial -->
        <a href="index.php" class="home-icon"><i class="fas fa-home"></i></a>
        <a href="historial.php" class="history-icon"><i class="fas fa-history"></i> Historial</a>
    </header>
    <h1>Registro Salidas SimaSolution</h1>

    <!-- Mostrar mensaje de éxito o error si está presente -->
    <?php if (isset($_GET['message']) && isset($_GET['messageType'])): ?>
        <div id="notification" class="notification <?php echo htmlspecialchars($_GET['messageType']); ?> show">
            <?php echo htmlspecialchars($_GET['message']); ?>
        </div>
    <?php endif; ?>

    <!-- Formulario para crear un nuevo día -->
    <h2>Crear Nuevo Día</h2>
    <form id="createDayForm" name="createDayForm" method="POST" class="center-form">
        <label for="day_date">Fecha del Día:</label>
        <input type="date" id="day_date" name="day_date" required>
        <button type="submit" name="create_day">
            <span>Crear Día</span>
        </button>
    </form>

    <!-- Sección de folios y pallets -->
    <div class="accordion custom-accordion">
        <?php if (isset($dateResult) && mysqli_num_rows($dateResult) > 0): ?>
            <?php while ($dateRow = mysqli_fetch_assoc($dateResult)): ?>
                <div class="accordion-item custom-accordion-item">
                    <div class="accordion-header custom-accordion-header">
                        <h3><?php echo date("d/m/Y", strtotime($dateRow['pallet_date'])); ?></h3>
                    </div>
                    <div class="accordion-body custom-accordion-body">
                        <!-- Formulario para crear un nuevo folio -->
                        <form id="createFolioForm-<?php echo $dateRow['pallet_date']; ?>" name="createFolioForm" method="POST"
                            class="center-form">
                            <input type="hidden" name="folio_date" value="<?php echo $dateRow['pallet_date']; ?>">
                            <button type="submit" name="create_folio">
                                <span>Crear Folio</span>
                            </button>
                        </form>

                        <!-- Sección de folios -->
                        <div class="accordion custom-accordion">
                            <?php
                            $foliosQuery = "SELECT * FROM Folios WHERE DATE(departure_date) = '" . $dateRow['pallet_date'] . "' ORDER BY folio_number ASC";
                            $foliosResult = mysqli_query($enlace, $foliosQuery);

                            if ($foliosResult && mysqli_num_rows($foliosResult) > 0):
                                while ($folio = mysqli_fetch_assoc($foliosResult)):
                                    ?>
                                    <div class="accordion-item custom-accordion-item" id="folio-<?php echo $folio['id']; ?>"
                                        data-folio-id="<?php echo $folio['id']; ?>">
                                        <div class="accordion-header custom-accordion-header">
                                            <h4><?php echo htmlspecialchars($folio['folio_number']); ?></h4>
                                            <button class="btn-delete-folio" data-folio-id="<?php echo $folio['id']; ?>">
                                                <i class="fas fa-trash"></i> Eliminar Folio
                                            </button>
                                        </div>
                                        <div class="accordion-body custom-accordion-body">
                                            <!-- Formulario para crear un nuevo pallet -->
                                            <form id="createPalletForm-<?php echo $folio['id']; ?>" name="createPalletForm"
                                                method="POST" class="center-form">
                                                <input type="hidden" name="pallet_date" value="<?php echo $dateRow['pallet_date']; ?>">
                                                <input type="hidden" name="folio_id" value="<?php echo $folio['id']; ?>">
                                                <button type="submit" name="create_pallet">
                                                    <span>Crear Pallet</span>
                                                </button>
                                            </form>

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
                                                                <!-- Botones para seleccionar, agregar y eliminar pallets -->
                                                                <button class="btn-select-pallet" data-pallet-id="<?php echo $pallet['id']; ?>">
                                                                    <i class="fas fa-check-square"></i> Seleccionar
                                                                </button>
                                                                <button class="btn-show-add-item" data-pallet-id="<?php echo $pallet['id']; ?>"
                                                                    data-folio-id="<?php echo $folio['id']; ?>">
                                                                    <i class="fas fa-plus"></i> Agregar Item
                                                                </button>
                                                                <button class="btn-delete-pallet" data-pallet-id="<?php echo $pallet['id']; ?>">
                                                                    <i class="fas fa-trash"></i> Eliminar Pallet
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
                                                                            <tr>
                                                                                <td class="custom-td">
                                                                                    <?php echo htmlspecialchars($item['numero_parte']); ?>
                                                                                </td>
                                                                                <td class="custom-td">
                                                                                    <?php echo htmlspecialchars($item['nifco_numero']); ?>
                                                                                </td>
                                                                                <td class="custom-td">
                                                                                    <?php echo htmlspecialchars($item['serial_number']); ?>
                                                                                </td>
                                                                                <td class="custom-td"><?php echo htmlspecialchars($item['quantity']); ?>
                                                                                </td>
                                                                                <td class="custom-td">
                                                                                <button class="btn-edit-item" data-item-id="<?php echo $item['id']; ?>">Editar</button>                                                                                    <button class="btn-delete-item"
                                                                                        data-item-id="<?php echo $item['id']; ?>">Eliminar</button>
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
            <p class="no-dates-message">No hay registros de fechas disponibles.</p>
        <?php endif; ?>
    </div>
    <!-- Sección para imprimir pallets seleccionados -->
    <div id="print-selected-container" style="display: none;">
        <button id="print-selected-pallets" class="btn-print-selected">
            <i class="fas fa-print"></i> Imprimir Pallets Seleccionados
        </button>
    </div>
    <script src="index.js"></script>
</body>

</html>