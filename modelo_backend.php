<?php
require 'backend/db/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $nifco_numero = isset($_POST['nifco_numero']) ? $_POST['nifco_numero'] : '';
    $numero_parte = isset($_POST['numero_parte']) ? $_POST['numero_parte'] : '';

    try {
        if ($action === 'create') {
            // Verificar si el NIFCO Número o el Número de Parte ya existe
            $checkQuery = "SELECT * FROM Modelos WHERE nifco_numero = '$nifco_numero' OR numero_parte = '$numero_parte'";
            $checkResult = mysqli_query($enlace, $checkQuery);
            if (mysqli_num_rows($checkResult) > 0) {
                throw new Exception("Error: El NIFCO Número o el Número de Parte ya existe.");
            }

            $insertQuery = "INSERT INTO Modelos (nifco_numero, numero_parte) VALUES ('$nifco_numero', '$numero_parte')";
            if (mysqli_query($enlace, $insertQuery)) {
                $message = "Modelo agregado exitosamente.";
            } else {
                throw new Exception("Error al agregar el modelo: " . mysqli_error($enlace));
            }
        } elseif ($action === 'update' && $id > 0) {
            // Verificar si el NIFCO Número o el Número de Parte ya existe para otro registro
            $checkQuery = "SELECT * FROM Modelos WHERE (nifco_numero = '$nifco_numero' OR numero_parte = '$numero_parte') AND id != $id";
            $checkResult = mysqli_query($enlace, $checkQuery);
            if (mysqli_num_rows($checkResult) > 0) {
                throw new Exception("Error: El NIFCO Número o el Número de Parte ya existe.");
            }

            $updateQuery = "UPDATE Modelos SET nifco_numero = '$nifco_numero', numero_parte = '$numero_parte' WHERE id = $id";
            if (mysqli_query($enlace, $updateQuery)) {
                $message = "Modelo actualizado exitosamente.";
            } else {
                throw new Exception("Error al actualizar el modelo: " . mysqli_error($enlace));
            }
        } elseif ($action === 'delete' && $id > 0) {
            $deleteQuery = "DELETE FROM Modelos WHERE id = $id";
            if (mysqli_query($enlace, $deleteQuery)) {
                $message = "Modelo eliminado exitosamente.";
            } else {
                throw new Exception("Error al eliminar el modelo: " . mysqli_error($enlace));
            }
        } else {
            throw new Exception("Acción no válida.");
        }
    } catch (Exception $e) {
        $message = $e->getMessage();
    }

    header("Location: add_modelo.php?message=" . urlencode($message));
    exit;
} else {
    header('Location: add_modelo.php');
    exit;
}
?>