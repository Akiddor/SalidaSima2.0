<?php
require 'backend/db/db.php';

$response = ['exists' => false];

if (isset($_GET['folio'])) {
    $folio = mysqli_real_escape_string($enlace, $_GET['folio']);
    $query = "SELECT id FROM Folios WHERE folio_number = '$folio'";
    $result = mysqli_query($enlace, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $response['exists'] = true;
    }
}

echo json_encode($response);
?>