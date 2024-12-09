<?php
require 'backend/db/db.php';

$response = ['success' => false, 'message' => ''];

if (isset($_GET['id'])) {
    $folio_id = intval($_GET['id']);
    $delete_query = "DELETE FROM Folios WHERE id = $folio_id";

    if (mysqli_query($enlace, $delete_query)) {
        $response['success'] = true;
        $response['message'] = 'Folio eliminado exitosamente.';
    } else {
        $response['message'] = 'Error al eliminar el folio: ' . mysqli_error($enlace);
    }
} else {
    $response['message'] = 'ID de folio no proporcionado.';
}

echo json_encode($response);
?>