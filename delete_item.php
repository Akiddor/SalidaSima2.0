<?php
require 'backend/db/db.php';

$response = ['success' => false, 'message' => ''];

if (isset($_GET['id'])) {
    $item_id = mysqli_real_escape_string($enlace, $_GET['id']);

    // Eliminar el item
    $delete_query = "DELETE FROM Cajas_scanned WHERE id = $item_id";
    if (mysqli_query($enlace, $delete_query)) {
        $response['success'] = true;
        $response['message'] = 'Registro eliminado exitosamente.';
    } else {
        $response['message'] = 'Error al eliminar el registro: ' . mysqli_error($enlace);
    }
} else {
    $response['message'] = 'ID del registro no proporcionado.';
}

echo json_encode($response);
?>