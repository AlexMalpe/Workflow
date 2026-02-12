<?php
// Archivo para la conexión a la base de datos (movido desde includes)
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'trabajophp';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die('Error de conexión: ' . $conn->connect_error);
}
