<?php
$servidor = 'localhost';
$usuario = 'root';
$clave = '';
$baseDeDatos = 'salidadbsima';

// Conectar a la base de datos
$enlace = mysqli_connect($servidor, $usuario, $clave, $baseDeDatos);



// Verificar conexión
if (!$enlace) {
    die("Conexión fallida: " . mysqli_connect_error());
    
}
?>
    