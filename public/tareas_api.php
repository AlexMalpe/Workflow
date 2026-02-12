<?php
session_start();
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'desarrollador'])) {
    http_response_code(403);
    echo json_encode([]);
    exit;
}
require_once '../config/db.php';
header('Content-Type: application/json');
$tareas = $conn->query('SELECT titulo, descripcion, fecha_limite, prioridad FROM tareas WHERE fecha_limite IS NOT NULL');
$eventos = [];
while ($t = $tareas->fetch_assoc()) {
    $color = '#3788d8';
    if ($t['prioridad'] === 'alta') $color = '#e74c3c';
    elseif ($t['prioridad'] === 'media') $color = '#f1c40f';
    elseif ($t['prioridad'] === 'baja') $color = '#2ecc71';
    $eventos[] = [
        'title' => $t['titulo'],
        'start' => $t['fecha_limite'],
        'descripcion' => $t['descripcion'],
        'color' => $color
    ];
}
echo json_encode($eventos);