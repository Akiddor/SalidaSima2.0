<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Item</title>
    <link rel="stylesheet" href="path/to/your/css/styles.css">
</head>
<body>
    <h1>Agregar Item</h1>

    <!-- Mostrar mensaje de éxito o error si está presente -->
    <?php if (isset($_GET['message']) && isset($_GET['messageType'])): ?>
        <div id="notification" class="notification <?php echo htmlspecialchars($_GET['messageType']); ?> show">
            <?php echo htmlspecialchars($_GET['message']); ?>
        </div>
    <?php endif; ?>

</body>
</html>