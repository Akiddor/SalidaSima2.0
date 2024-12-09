<?php
require 'backend/db/db.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registro'])) {
    $part_number = mysqli_real_escape_string($enlace, $_POST['part_number']);
    $serial_number = mysqli_real_escape_string($enlace, $_POST['serial_number']);
    $quantity = mysqli_real_escape_string($enlace, $_POST['quantity']);
    $pallet_id = mysqli_real_escape_string($enlace, $_POST['pallet_id']);
    $folio_id = mysqli_real_escape_string($enlace, $_POST['folio_id']);

    // Buscar en tabla Modelos
    $search_model_query = "SELECT id, nifco_numero FROM Modelos WHERE 
        LOWER(numero_parte) = LOWER('$part_number') OR 
        LOWER(nifco_numero) = LOWER('$part_number')";
    $model_result = mysqli_query($enlace, $search_model_query);

    if (!$model_result) {
        $message = "Error en la consulta SQL: " . mysqli_error($enlace);
        $messageType = 'error';
    } else if (mysqli_num_rows($model_result) > 0) {
        $model_info = mysqli_fetch_assoc($model_result);
        $model_id = $model_info['id'];
        $nifco_numero = $model_info['nifco_numero'];

        $quantity = preg_replace('/\D/', '', $quantity);

        if (!is_numeric($quantity) || (int)$quantity <= 0) {
            $message = "Cantidad inválida. Por favor, ingrese un número válido.";
            $messageType = 'error';
        } else {
            $quantity = (int)$quantity;

            // Verificar si el pallet pertenece al folio correcto
            $check_pallet_query = "SELECT * FROM Pallets WHERE id = $pallet_id AND folio_id = $folio_id";
            $check_pallet_result = mysqli_query($enlace, $check_pallet_query);

            if (mysqli_num_rows($check_pallet_result) == 0) {
                $message = "El pallet no pertenece al folio especificado.";
                $messageType = 'error';
            } else {
                // Verificar si el número de serie ya existe en el pallet
                $check_serial_query = "SELECT COUNT(*) as count FROM Cajas_scanned WHERE serial_number = '$serial_number' AND pallet_id = $pallet_id";
                $check_serial_result = mysqli_query($enlace, $check_serial_query);
                $serial_check = mysqli_fetch_assoc($check_serial_result);
                if ($serial_check['count'] > 0) {
                    $message = "El número de serie ya existe en este pallet. Por favor, utiliza un número de serie diferente.";
                    $messageType = 'error';
                } else {
                    // Insertar el registro en la tabla Cajas_scanned
                    $insert_query = "INSERT INTO Cajas_scanned (part_id, pallet_id, serial_number, quantity)
                                     VALUES ($model_id, $pallet_id, '$serial_number', $quantity)";
                    if (mysqli_query($enlace, $insert_query)) {
                        $message = "Registro agregado exitosamente. NIFCO: $nifco_numero";
                        $messageType = 'success';
                    } else {
                        $message = "Error al agregar el registro: " . mysqli_error($enlace);
                        $messageType = 'error';
                    }
                }
            }
        }
    } else {
        $message = "Número de parte no encontrado en la base de datos.";
        $messageType = 'error';
    }

    // Redirigir al usuario con el mensaje y el tipo de mensaje
    header("Location: " . strtok($_SERVER['HTTP_REFERER'], '?') . "?message=" . urlencode($message) . "&messageType=" . urlencode($messageType));
    exit;
}
?>