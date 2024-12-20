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
                        <form action="cambiar_estado.php" method="post" class="inline-form">
                            <input type="hidden" name="day_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" class="btn btn-primary">Mandar a Producción</button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No hay días archivados.</p>
        <?php endif; ?>
    </div>
</body>
</html>