<?php
require 'backend/db/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $day_id = isset($_POST['day_id']) ? intval($_POST['day_id']) : 0;

    if ($day_id > 0) {
        $updateQuery = "UPDATE Days SET status = 'produccion' WHERE id = $day_id";
        if (mysqli_query($enlace, $updateQuery)) {
            echo "<script>
                  alert('El día ha sido cambiado a producción.');
                  window.location.href = 'historial.php';
                  </script>";
        } else {
            echo "<script>
                  alert('Error al cambiar el estado del día.');
                  window.location.href = 'historial.php';
                  </script>";
        }
    } else {
        echo "<script>
              alert('ID de día inválido.');
              window.location.href = 'historial.php';
              </script>";
    }
} else {
    header('Location: historial.php');
    exit;
}
?>