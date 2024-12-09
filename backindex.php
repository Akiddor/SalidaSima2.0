<?php

// Incluir el archivo de conexión a la base de datos
require 'backend/db/db.php';

// Inicializar variables para los mensajes
$message = '';
$messageType = '';

/**
 * Función para mover registros antiguos a historial
 *
 * @param $enlace Enlace a la base de datos
 */
function moveOldRecordsToHistory($enlace) {
    // Asegurarse de que el folio_id exista en la tabla Folios antes de mover
    $moveQuery = "INSERT INTO Historial (folio_id, pallet_id, created_at)
                  SELECT p.folio_id, p.id, p.created_at
                  FROM Pallets p
                  JOIN Folios f ON p.folio_id = f.id
                  WHERE p.created_at < NOW() - INTERVAL 5 DAY";
    mysqli_query($enlace, $moveQuery);

    // Eliminar los registros antiguos de la tabla Pallets
    $deleteQuery = "DELETE FROM Pallets WHERE created_at < NOW() - INTERVAL 5 DAY";
    mysqli_query($enlace, $deleteQuery);
}

// Crear día
if (isset($_POST['create_day'])) {
    $day_date = mysqli_real_escape_string($enlace, $_POST['day_date']);

    // Verificar si ya existe un registro para esta fecha
    $check_day_query = "SELECT * FROM Days WHERE day_date = '$day_date'";
    $check_day_result = mysqli_query($enlace, $check_day_query);

    if (mysqli_num_rows($check_day_result) > 0) {
        $message = "Ya existe un registro para esta fecha: " . $day_date;
        $messageType = 'error';
    } else {
        $create_day_query = "INSERT INTO Days (day_date) VALUES ('$day_date')";

        if (mysqli_query($enlace, $create_day_query)) {
            $message = "Día creado exitosamente: " . $day_date;
            $messageType = 'success';
        } else {
            $message = "Error al crear el día: " . mysqli_error($enlace);
            $messageType = 'error';
        }
    }

    // Redirigir al usuario a la misma página con el mensaje correspondiente
    header("Location: " . $_SERVER['PHP_SELF'] . "?message=" . urlencode($message) . "&messageType=" . urlencode($messageType));
    exit;
}

// Crear Folio
if (isset($_POST['create_folio'])) {
    $folio_date = mysqli_real_escape_string($enlace, $_POST['folio_date']);

    // Buscar el día correspondiente
    $find_day_query = "SELECT id FROM Days WHERE day_date = '$folio_date'";
    $find_day_result = mysqli_query($enlace, $find_day_query);

    if (mysqli_num_rows($find_day_result) > 0) {
        $day = mysqli_fetch_assoc($find_day_result);
        $day_id = $day['id'];

        // Generar número de folio
        $folio_number_query = "SELECT COALESCE(MAX(CAST(SUBSTRING(folio_number, 7) AS UNSIGNED)), 0) + 1 AS next_folio
                               FROM Folios
                               WHERE folio_number LIKE 'SIMA-%'";
        $folio_number_result = mysqli_query($enlace, $folio_number_query);
        $next_folio = mysqli_fetch_assoc($folio_number_result)['next_folio'];
        $folio_number = 'SIMA-' . str_pad($next_folio, 6, '0', STR_PAD_LEFT);

        // Insertar nuevo folio
        $create_folio_query = "INSERT INTO Folios (folio_number, day_id, departure_date)
                               VALUES ('$folio_number', $day_id, '$folio_date')";

        if (mysqli_query($enlace, $create_folio_query)) {
            $message = "Folio creado exitosamente: " . $folio_number;
            $messageType = 'success';
        } else {
            $message = "Error al crear el folio: " . mysqli_error($enlace);
            $messageType = 'error';
        }
    } else {
        $message = "No se encontró el día para crear el folio.";
        $messageType = 'error';
    }

    // Redirigir al usuario a la misma página con el mensaje correspondiente
    header("Location: " . $_SERVER['PHP_SELF'] . "?message=" . urlencode($message) . "&messageType=" . urlencode($messageType));
    exit;
}

// Crear Pallet
if (isset($_POST['create_pallet'])) {
    $pallet_date = mysqli_real_escape_string($enlace, $_POST['pallet_date']);
    $folio_id = mysqli_real_escape_string($enlace, $_POST['folio_id']);

    // Contar pallets existentes para este folio
    $count_pallets_query = "SELECT COUNT(*) as pallet_count FROM Pallets WHERE folio_id = $folio_id";
    $count_pallets_result = mysqli_query($enlace, $count_pallets_query);
    $pallet_count = mysqli_fetch_assoc($count_pallets_result)['pallet_count'];

    // Generar número de pallet
    $pallet_number = 'Pallet ' . ($pallet_count + 1);

    // Insertar nuevo pallet
    $create_pallet_query = "INSERT INTO Pallets (folio_id, pallet_number) VALUES ($folio_id, '$pallet_number')";

    if (mysqli_query($enlace, $create_pallet_query)) {
        // Actualizar total de pallets en folio
        $update_folio_query = "UPDATE Folios SET total_pallets = total_pallets + 1 WHERE id = $folio_id";
        mysqli_query($enlace, $update_folio_query);

        $message = "Pallet creado exitosamente: " . $pallet_number;
        $messageType = 'success';
    } else {
        $message = "Error al crear el pallet: " . mysqli_error($enlace);
        $messageType = 'error';
    }

    // Redirigir al usuario a la misma página con el mensaje correspondiente
    header("Location: " . $_SERVER['PHP_SELF'] . "?message=" . urlencode($message) . "&messageType=" . urlencode($messageType));
    exit;
}

// Obtener fechas únicas de la tabla Days
$dateQuery = "SELECT DISTINCT day_date as pallet_date FROM Days ORDER BY day_date DESC";
$dateResult = mysqli_query($enlace, $dateQuery);
?>