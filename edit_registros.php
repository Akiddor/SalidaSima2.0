<?php
require 'backend/db/db.php';

$message = '';
$messageType = ''; // Variable para almacenar el tipo de mensaje (success o error)

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "SELECT * FROM items_scanned WHERE id = $id";
    $result = mysqli_query($enlace, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $item = mysqli_fetch_assoc($result);
    } else {
        die("Registro no encontrado.");
    }
} else {
    die("ID no proporcionado.");
}

$updateSuccess = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $part_number = $_POST['part_number'];
    $serial_number = $_POST['serial_number'];
    $quantity = $_POST['quantity'];
    $fecha_y_hora = $_POST['fecha_y_hora'];

    // Verificar si el número de serie ya existe en la base de datos para otro registro
    $check_serial_query = "SELECT COUNT(*) as count FROM items_scanned WHERE serial_number = '$serial_number' AND id != $id";
    $check_serial_result = mysqli_query($enlace, $check_serial_query);
    $serial_check = mysqli_fetch_assoc($check_serial_result);

    if ($serial_check['count'] > 0) {
        $message = "El número de serie ya existe. Por favor, utiliza un número de serie diferente.";
        $messageType = 'error';
    } else {
        $updateQuery = "UPDATE items_scanned SET part_number = '$part_number', serial_number = '$serial_number', quantity = '$quantity', fecha_y_hora = '$fecha_y_hora' WHERE id = $id";
        if (mysqli_query($enlace, $updateQuery)) {
            $updateSuccess = true;
            $message = "El registro se actualizó correctamente.";
            $messageType = 'success';
        } else {
            $message = "Error al actualizar el registro: " . mysqli_error($enlace);
            $messageType = 'error';
        }
    }

    // Redirigir a la página de index con el mensaje y el tipo de mensaje en la URL
    header("Location: index.php?message=" . urlencode($message) . "&messageType=" . urlencode($messageType));
    exit; // Terminar la ejecución del script después de la redirección
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Registro</title>
    <link rel="stylesheet" href="/css/edit.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" href="simafa.png" type="image/sima">
    
    </style>
    <script>
        function confirmUpdate() {
            return confirm('¿Estás seguro de que deseas actualizar este registro?');
        }

        // Mostrar notificación y ocultarla después de un tiempo ajustable
        document.addEventListener('DOMContentLoaded', function () {
            const notification = document.querySelector('.success-message, .error-message');
            if (notification) {
                notification.style.display = 'block';
                setTimeout(() => {
                    notification.style.display = 'none';
                }, 5000); // Cambia este valor para ajustar el tiempo (en milisegundos)
            }
        });
    </script>
</head>
<body>
<header>
        <a href="index.php" class="home-icon"><i class="fas fa-home"></i></a>
</header>
<h1>Editar Registro</h1>
<?php if (isset($_GET['message']) && isset($_GET['messageType'])): ?>
    <p class="<?php echo $_GET['messageType'] === 'success' ? 'success-message' : 'error-message'; ?>">
        <?php echo htmlspecialchars($_GET['message']); ?>
    </p>
<?php endif; ?>
<form method="POST" onsubmit="return confirmUpdate();">
    <label for="part_number">Número de Parte:</label>
    <input type="text" id="part_number" name="part_number" value="<?php echo isset($item) ? htmlspecialchars($item['part_number']) : ''; ?>" required>

    <label for="serial_number">Serial:</label>
    <input type="text" id="serial_number" name="serial_number" value="<?php echo isset($item) ? htmlspecialchars($item['serial_number']) : ''; ?>" required>

    <label for="quantity">Cantidad:</label>
    <input type="number" id="quantity" name="quantity" value="<?php echo isset($item) ? htmlspecialchars($item['quantity']) : ''; ?>" required>

    <label for="fecha_y_hora">Fecha:</label>
    <input type="date" id="fecha_y_hora" name="fecha_y_hora" value="<?php echo isset($item) ? htmlspecialchars(date('Y-m-d', strtotime($item['fecha_y_hora']))) : ''; ?>" required>

    <button type="submit">Actualizar</button>
</form>
</body>
</html>