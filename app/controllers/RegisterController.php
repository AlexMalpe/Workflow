<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../models/functions.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    $profile_image_path = null;
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $debug = '';
        $debug .= 'Error code: ' . $_FILES['profile_image']['error'] . '<br>';
        $debug .= 'Nombre: ' . $_FILES['profile_image']['name'] . '<br>';
        $debug .= 'Tmp: ' . $_FILES['profile_image']['tmp_name'] . '<br>';
        $debug .= 'Tamaño: ' . $_FILES['profile_image']['size'] . '<br>';
        $debug .= 'is_writable uploads: ' . (is_writable(__DIR__ . '/../../public/uploads/') ? 'sí' : 'no') . '<br>';
        if ($_FILES['profile_image']['error'] !== UPLOAD_ERR_OK) {
            $error = 'Error al subir la imagen: código ' . $_FILES['profile_image']['error'] . '<br>' . $debug;
        } else {
            $ext = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array(strtolower($ext), $allowed)) {
                $filename = uniqid('userimg_') . '.' . $ext;
                $dest = __DIR__ . '/../../public/uploads/' . $filename;
                if (!is_writable(__DIR__ . '/../../public/uploads/')) {
                    $error = 'La carpeta uploads no tiene permisos de escritura.<br>' . $debug;
                } else {
                    if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $dest)) {
                        $profile_image_path = 'uploads/' . $filename;
                    } else {
                        $error = 'No se pudo mover la imagen al servidor.<br>' . $debug;
                    }
                }
            } else {
                $error = 'Formato de imagen no permitido.<br>' . $debug;
            }
        }
    }

    if ($username && $email && $password && in_array($role, ['desarrollador', 'admin'])) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'El correo no es válido.';
        } else {
            $stmt = $conn->prepare('SELECT id FROM usuarios WHERE username = ? OR email = ?');
            $stmt->bind_param('ss', $username, $email);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $error = 'El usuario o correo ya existe.';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare('INSERT INTO usuarios (username, email, password, role, profile_image) VALUES (?, ?, ?, ?, ?)');
                $stmt->bind_param('sssss', $username, $email, $hash, $role, $profile_image_path);
                if ($stmt->execute()) {
                    header('Location: login.php?registro=ok');
                    exit;
                } else {
                    $error = 'Error al registrar usuario.';
                }
            }
            $stmt->close();
        }
    } else {
        $error = 'Completa todos los campos correctamente.';
    }
}
