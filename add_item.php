<?php
require 'backend/db/db.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $part_number = strtoupper(mysqli_real_escape_string($enlace, $_POST['part_number']));
    $serial_number = strtoupper(mysqli_real_escape_string($enlace, $_POST['serial_number']));
    $quantity = strtoupper(mysqli_real_escape_string($enlace, $_POST['quantity']));
    $pallet_id = mysqli_real_escape_string($enlace, $_POST['pallet_id']);
    $folio_id = mysqli_real_escape_string($enlace, $_POST['folio_id']);

    // Limpiar los valores escaneados
    $part_number = preg_replace('/^P/', '', $part_number);
    $serial_number = preg_replace('/^1S/', '', $serial_number);
    $quantity = preg_replace('/^Q/', '', $quantity);

    // Eliminar espacios y guiones
    $part_number = preg_replace('/[\s-]/', '', $part_number);
    $serial_number = preg_replace('/[\s-]/', '', $serial_number);
    $quantity = preg_replace('/[\s-]/', '', $quantity);

    // Buscar en tabla Modelos
    $search_model_query = "SELECT id, nifco_numero FROM Modelos WHERE 
        LOWER(numero_parte) = LOWER('$part_number') OR 
        LOWER(nifco_numero) = LOWER('$part_number')";
    $model_result = mysqli_query($enlace, $search_model_query);

    if (!$model_result) {
        $response['message'] = "Error en la consulta SQL: " . mysqli_error($enlace);
    } else if (mysqli_num_rows($model_result) > 0) {
        $model_info = mysqli_fetch_assoc($model_result);
        $model_id = $model_info['id'];
        $nifco_numero = $model_info['nifco_numero'];

        $quantity = preg_replace('/\D/', '', $quantity);

        if (!is_numeric($quantity) || (int)$quantity <= 0) {
            $response['message'] = "Cantidad inválida. Por favor, ingrese un número válido.";
        } else {
            $quantity = (int)$quantity;

            // Verificar si el número de serie ya existe en cualquier pallet
            $check_serial_query = "SELECT COUNT(*) as count FROM Cajas_scanned WHERE serial_number = '$serial_number'";
            $check_serial_result = mysqli_query($enlace, $check_serial_query);
            $serial_check = mysqli_fetch_assoc($check_serial_result);
            if ($serial_check['count'] > 0) {
                $response['message'] = "El número de serie ya existe en otro pallet. Por favor, utiliza un número de serie diferente.";
            } else {
                // Insertar el registro en la tabla Cajas_scanned
                $insert_query = "INSERT INTO Cajas_scanned (part_id, pallet_id, serial_number, quantity)
                                 VALUES ($model_id, $pallet_id, '$serial_number', $quantity)";
                if (mysqli_query($enlace, $insert_query)) {
                    $response['success'] = true;
                    $response['message'] = "Registro agregado exitosamente. NIFCO: $nifco_numero";
                } else {
                    $response['message'] = "Error al agregar el registro: " . mysqli_error($enlace);
                }
            }
        }
    } else {
        $response['message'] = "Número de parte no encontrado en la base de datos.";
    }
}

echo json_encode($response);
?>