<?php
require 'backend/db/db.php';

// Lógica para manejar la actualización del registro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = mysqli_real_escape_string($enlace, $_POST['id']);
    $part_number = mysqli_real_escape_string($enlace, $_POST['part_number']);
    $serial_number = mysqli_real_escape_string($enlace, $_POST['serial_number']);
    $quantity = mysqli_real_escape_string($enlace, $_POST['quantity']);

    // Obtener el part_id basado en el número de parte
    $part_query = "SELECT id FROM Modelos WHERE numero_parte = '$part_number'";
    $part_result = mysqli_query($enlace, $part_query);
    if ($part_result && mysqli_num_rows($part_result) > 0) {
        $part_info = mysqli_fetch_assoc($part_result);
        $part_id = $part_info['id'];

        $update_query = "UPDATE Cajas_scanned SET part_id = '$part_id', serial_number = '$serial_number', quantity = $quantity WHERE id = $id";
        if (mysqli_query($enlace, $update_query)) {
            $message = "Registro actualizado exitosamente.";
            $messageType = 'success';
            // Redirigir al usuario a index.php con el mensaje y el tipo de mensaje
            header("Location: index.php?message=" . urlencode($message) . "&messageType=" . urlencode($messageType));
            exit; // Terminar la ejecución del script después de la redirección
        } else {
            $message = "Error al actualizar el registro: " . mysqli_error($enlace);
            $messageType = 'error';
        }
    } else {
        $message = "Número de parte no encontrado.";
        $messageType = 'error';
    }

    // Redirigir al usuario con el mensaje y el tipo de mensaje
    header("Location: edit_item.php?id=$id&message=" . urlencode($message) . "&messageType=" . urlencode($messageType));
    exit; // Terminar la ejecución del script después de la redirección
}

// Obtener los datos del registro a editar
if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($enlace, $_GET['id']);
    $query = "SELECT cs.*, m.numero_parte FROM Cajas_scanned cs JOIN Modelos m ON cs.part_id = m.id WHERE cs.id = $id";
    $result = mysqli_query($enlace, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $registro = mysqli_fetch_assoc($result);
    } else {
        die("Registro no encontrado.");
    }
} else {
    die("ID no proporcionado.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Item</title>
    <link rel="stylesheet" href="/css/edit.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" href="simafa.png" type="image/sima">
    <script>
        function confirmUpdate() {
            return confirm('¿Estás seguro de que deseas actualizar este registro?');
        }

        // Mostrar notificación y ocultarla después de un tiempo ajustable
        document.addEventListener('DOMContentLoaded', function () {
            const notification = document.querySelector('.notification');
            if (notification) {
                notification.classList.add('show');
                setTimeout(() => {
                    notification.classList.remove('show');
                }, 5000); // Cambia este valor para ajustar el tiempo (en milisegundos)
            }
        });
    </script>
</head>
<body>
<header>
    <!-- Enlaces a la página de inicio y al historial -->
    <a href="index.php" class="home-icon"><i class="fas fa-home"></i></a>
    <a href="historial.php" class="history-icon"><i class="fas fa-history"></i> Historial</a>
</header>
<div class="container">
    <h1>Editar Item</h1>
    <?php if (isset($_GET['message']) && isset($_GET['messageType'])): ?>
        <div class="notification <?php echo htmlspecialchars($_GET['messageType']); ?> show">
            <?php echo htmlspecialchars($_GET['message']); ?>
        </div>
    <?php endif; ?>
    <form method="POST" onsubmit="return confirmUpdate();">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($registro['id']); ?>">
        <label for="part_number">Número de Parte:</label>
        <input type="text" id="part_number" name="part_number" value="<?php echo htmlspecialchars($registro['numero_parte']); ?>" required>

        <label for="serial_number">Serial:</label>
        <input type="text" id="serial_number" name="serial_number" value="<?php echo htmlspecialchars($registro['serial_number']); ?>" required>

        <label for="quantity">Cantidad:</label>
        <input type="number" id="quantity" name="quantity" value="<?php echo htmlspecialchars($registro['quantity']); ?>" required>

        <button type="submit">Actualizar</button>
    </form>
</div>
</body>
</html>