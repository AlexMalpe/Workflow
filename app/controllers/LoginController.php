<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../models/functions.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        if ($username && $password) {
            $stmt = $conn->prepare('SELECT id, password, role FROM usuarios WHERE username = ?');
            if (!$stmt) {
                throw new Exception('Error en la preparación de la consulta: ' . $conn->error);
            }
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows === 1) {
                $stmt->bind_result($id, $hash, $role);
                $stmt->fetch();
                if (password_verify($password, $hash)) {
                    $_SESSION['user_id'] = $id;
                    $_SESSION['username'] = $username;
                    $_SESSION['role'] = $role;
                    setcookie('remember_user', $username, time() + 365*24*60*60, "/"); // Guardar usuario por 1 año
                    if ($role === 'admin') {
                        header('Location: admin.php');
                    } elseif ($role === 'desarrollador') {
                        header('Location: devpanel.php');
                    } else {
                        header('Location: panel.php');
                    }
                    exit;
                } else {
                    $error = 'Contraseña incorrecta.';
                }
            } else {
                $error = 'Usuario no encontrado.';
            }
            $stmt->close();
        } else {
            $error = 'Completa todos los campos.';
        }
    } catch (Exception $e) {
        $error = 'Error en el sistema: ' . $e->getMessage();
    }
}
