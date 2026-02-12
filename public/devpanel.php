<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'desarrollador') {
    header('Location: login.php');
    exit;
}
require_once '../config/db.php';
$user_id = $_SESSION['user_id'];

// Cambiar estado de tarea a completada
if (isset($_GET['completar']) && is_numeric($_GET['completar'])) {
    $tid = (int)$_GET['completar'];
    $stmt = $conn->prepare('UPDATE tareas SET estado="completada" WHERE id=? AND usuario_id=?');
    $stmt->bind_param('ii', $tid, $user_id);
    $stmt->execute();
    header('Location: devpanel.php');
    exit;
}

// Procesar entrega de archivo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['entregar_tarea']) && isset($_POST['tarea_id']) && is_numeric($_POST['tarea_id'])) {
    $tarea_id = (int)$_POST['tarea_id'];
    if (!isset($_FILES['tarea_file']) || $_FILES['tarea_file']['error'] === UPLOAD_ERR_NO_FILE) {
        echo '<div style="color:red">El archivo es obligatorio.</div>';
        exit;
    }
    $file = $_FILES['tarea_file'];
    $fileName = $file['name'];
    $fileTmp = $file['tmp_name'];
    $fileDest = 'uploads/tarea_' . time() . '_' . preg_replace('/[^a-zA-Z0-9_.-]/', '', $fileName);
    if (!move_uploaded_file($fileTmp, __DIR__ . '/../public/' . $fileDest)) {
        echo '<div style="color:red">Error al guardar el archivo.</div>';
        exit;
    }
    $stmt = $conn->prepare('UPDATE tareas SET tarea_zip=? WHERE id=? AND usuario_id=?');
    $stmt->bind_param('sii', $fileDest, $tarea_id, $user_id);
    $stmt->execute();
    header('Location: devpanel.php');
    exit;
}
// Añadir comentario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comentario']) && is_numeric($_POST['tarea_id'])) {
    $comentario = trim($_POST['comentario']);
    $tarea_id = (int)$_POST['tarea_id'];
    if ($comentario) {
        $stmt = $conn->prepare('INSERT INTO comentarios (tarea_id, usuario_id, comentario) VALUES (?, ?, ?)');
        $stmt->bind_param('iis', $tarea_id, $user_id, $comentario);
        $stmt->execute();
    }
    header('Location: devpanel.php');
    exit;
}
// Obtener tareas asignadas
$tareas = $conn->query("SELECT t.*, u.profile_image as asignado_img, u.username as asignado_nombre FROM tareas t LEFT JOIN usuarios u ON t.usuario_id = u.id WHERE t.usuario_id = $user_id ORDER BY t.fecha_creacion DESC");
// Obtener comentarios por tarea
$comentarios = [];
$res = $conn->query("SELECT * FROM comentarios WHERE usuario_id = $user_id");
while ($row = $res->fetch_assoc()) {
    $comentarios[$row['tarea_id']][] = $row;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Desarrollador - WorkFlow</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #232526 0%, #414345 100%);
            font-family: 'Montserrat', Arial, sans-serif;
            color: #fff;
        }
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 24px 40px 0 40px;
        }
        .navbar .logo {
            font-size: 2em;
            font-weight: 700;
            color: #6dd5fa;
            letter-spacing: 2px;
        }
        .navbar .nav-btns a {
            color: #fff;
            background: #2980b9;
            border-radius: 6px;
            padding: 10px 24px;
            margin-left: 12px;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.2s, color 0.2s;
        }
        .navbar .nav-btns a:hover {
            background: #6dd5fa;
            color: #232526;
        }
        .container {
            max-width: 1100px;
            margin: 40px auto 0 auto;
            background: rgba(255,255,255,0.07);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(44,62,80,0.15);
            padding: 40px 30px;
        }
        h2 {
            color: #6dd5fa;
            font-size: 2.2em;
            margin-bottom: 0.5em;
            text-align: center;
        }
        h3 {
            color: #fff;
            font-size: 1.3em;
            margin-top: 2em;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 1.5em 0;
            background: rgba(255,255,255,0.04);
            border-radius: 10px;
            overflow: hidden;
        }
        th, td {
            border: 1px solid #6dd5fa33;
            padding: 10px 8px;
            text-align: center;
        }
        th {
            background: #2980b9;
            color: #fff;
            font-weight: 700;
        }
        tr:nth-child(even) { background: rgba(255,255,255,0.03); }
        tr:hover { background: #6dd5fa22; }
        form {
            margin-bottom: 1.5em;
        }
        input, select, button {
            font-family: inherit;
            font-size: 1em;
            border-radius: 6px;
            border: none;
            padding: 8px 12px;
            margin: 0 6px 10px 0;
        }
        input[type="date"] { min-width: 120px; }
        button, .btn {
            background: #2980b9;
            color: #fff;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
        }
        button:hover, .btn:hover {
            background: #6dd5fa;
            color: #232526;
        }
        .actions a { margin: 0 4px; }
        @media (max-width: 900px) {
            .container { padding: 20px 5px; }
            .navbar { flex-direction: column; gap: 10px; padding: 18px 10px 0 10px; }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="logo"><i class="fa-solid fa-diagram-project"></i> WorkFlow</div>
        <div class="nav-btns">
            <a href="calendario.php" target="_blank">Calendario</a>
            <a href="logout.php">Cerrar sesión</a>
        </div>
    </div>
    <div class="container">
        <h2>Panel de Desarrollador</h2>
        <h3>Mis tareas</h3>
        <!-- Formulario de creación de tarea eliminado -->
        <table>
            <tr><th>ID</th><th>Título</th><th>Descripción</th><th>Prioridad</th><th>Fecha límite</th><th>Estado</th><th>Asignado</th><th>Acciones</th><th>Archivo</th></tr>
        <?php foreach ($tareas as $t): ?>
        <tr>
            <td><?= $t['id'] ?></td>
            <td><?= htmlspecialchars($t['titulo']) ?></td>
            <td><?= htmlspecialchars($t['descripcion']) ?></td>
            <td><?= ucfirst($t['prioridad']) ?></td>
            <td><?= $t['fecha_limite'] ? htmlspecialchars($t['fecha_limite']) : '-' ?></td>
            <td><?= $t['estado'] ?></td>
            <td>
                <?php if (!empty($t['asignado_img'])): ?>
                    <img src="/trabajophp/public/<?= htmlspecialchars($t['asignado_img']) ?>" alt="img" style="width:40px;height:40px;border-radius:50%;object-fit:cover;vertical-align:middle;margin-right:6px;">
                <?php else: ?>
                    <img src="https://via.placeholder.com/40" alt="img" style="width:40px;height:40px;border-radius:50%;object-fit:cover;vertical-align:middle;margin-right:6px;">
                <?php endif; ?>
                <?= htmlspecialchars($t['asignado_nombre'] ?? 'Tú') ?>
            </td>
            <td>
                <?php if ($t['estado'] !== 'completada'): ?>
                    <form method="POST" enctype="multipart/form-data" style="margin-bottom:8px;">
                        <input type="hidden" name="entregar_tarea" value="1">
                        <input type="hidden" name="tarea_id" value="<?= $t['id'] ?>">
                        <input type="file" name="tarea_file" required>
                        <button type="submit">Entregar archivo</button>
                    </form>
                    <a href="devpanel.php?completar=<?= $t['id'] ?>" class="btn" style="background:#2ecc71;color:#fff;padding:6px 16px;border-radius:6px;font-weight:600;">Marcar como completada</a>
                <?php else: ?>
                    <span style="background:#2ecc71;color:#fff;padding:6px 16px;border-radius:6px;font-weight:600;">Completada</span>
                <?php endif; ?>
            </td>
            <td>
                <?php if (!empty($t['tarea_zip'])): ?>
                    <a href="/trabajophp/public/<?= htmlspecialchars($t['tarea_zip']) ?>" target="_blank" class="btn" style="background:#3498db;color:#fff;padding:6px 16px;border-radius:6px;font-weight:600;margin-bottom:8px;display:inline-block;">Descargar archivo</a>
                <?php else: ?>
                    <span style="color:#ccc;">Sin archivo</span>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td colspan="5">
                <form method="POST" style="margin:0;">
                    <input type="hidden" name="tarea_id" value="<?= $t['id'] ?>">
                    <input type="text" name="comentario" placeholder="Añadir comentario" required>
                    <button type="submit">Comentar</button>
                </form>
                <b>Comentarios:</b><br>
                <?php if (!empty($comentarios[$t['id']])): ?>
                    <ul>
                    <?php foreach ($comentarios[$t['id']] as $c): ?>
                        <li><?= htmlspecialchars($c['comentario']) ?> <small>(<?= $c['fecha'] ?>)</small></li>
                    <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <i>Sin comentarios</i>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
