<?php
require 'backend/db/db.php';

$response = ['success' => false];

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    $query = "DELETE FROM items_scanned WHERE id = ?";
    $stmt = $enlace->prepare($query);

    if ($stmt === false) {
        $response['message'] = "Error en la preparación de la consulta: " . $enlace->error;
    } else {
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $response['success'] = true;
        } else {
            $response['message'] = "Error al eliminar el registro.";
        }
        $stmt->close();
    }
} else {
    $response['message'] = "ID no proporcionado.";
}

$enlace->close();

header('Content-Type: application/json');
echo json_encode($response);