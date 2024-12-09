<?php
require 'backend/db/db.php';

$response = ['success' => false, 'message' => ''];

if (isset($_GET['id'])) {
    $pallet_id = intval($_GET['id']);
    $delete_query = "DELETE FROM Pallets WHERE id = $pallet_id";

    if (mysqli_query($enlace, $delete_query)) {
        $response['success'] = true;
        $response['message'] = 'Pallet eliminado exitosamente.';
    } else {
        $response['message'] = 'Error al eliminar el pallet: ' . mysqli_error($enlace);
    }
} else {
    $response['message'] = 'ID de pallet no proporcionado.';
}

echo json_encode($response);
?>