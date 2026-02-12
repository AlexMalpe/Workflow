<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: login.php');
    exit;
}
require_once '../config/db.php';

// Acciones: añadir, editar, borrar tarea/usuario
$accion = $_GET['accion'] ?? '';
$id = $_GET['id'] ?? null;

// Procesar formularios de tareas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tarea_form'])) {
    $titulo = trim($_POST['titulo'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $prioridad = $_POST['prioridad'] ?? 'media';
    $fecha_limite = $_POST['fecha_limite'] ?? null;
    $estado = $_POST['estado'] ?? 'pendiente';
    $usuario_id = $_POST['usuario_id'] ?? null;
        // Validar archivo obligatorio
        if (!isset($_FILES['tarea_zip']) || $_FILES['tarea_zip']['error'] === UPLOAD_ERR_NO_FILE) {
            echo '<div style="color:red">El archivo es obligatorio.</div>';
            exit;
        }
        $file = $_FILES['tarea_zip'];
        $fileName = $file['name'];
        $fileTmp = $file['tmp_name'];
        $fileDest = 'uploads/tarea_' . time() . '_' . preg_replace('/[^a-zA-Z0-9_.-]/', '', $fileName);
        if (!move_uploaded_file($fileTmp, __DIR__ . '/' . $fileDest)) {
            echo '<div style="color:red">Error al guardar el archivo.</div>';
            exit;
        }
    if ($accion === 'editar_tarea' && $id) {
        $stmt = $conn->prepare('UPDATE tareas SET titulo=?, descripcion=?, prioridad=?, fecha_limite=?, estado=?, usuario_id=?, tarea_zip=? WHERE id=?');
            $stmt->bind_param('sssssssi', $titulo, $descripcion, $prioridad, $fecha_limite, $estado, $usuario_id, $fileDest, $id);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare('INSERT INTO tareas (titulo, descripcion, prioridad, fecha_limite, estado, usuario_id, tarea_zip) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $stmt->bind_param('sssssis', $titulo, $descripcion, $prioridad, $fecha_limite, $estado, $usuario_id, $fileDest);
        $stmt->execute();
    }
    header('Location: admin.php');
    exit;
}
if ($accion === 'borrar_tarea' && $id) {
    $stmt = $conn->prepare('DELETE FROM tareas WHERE id=?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    header('Location: admin.php');
    exit;
}
// Procesar formularios de usuarios
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['usuario_form'])) {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'invitado';
    if ($accion === 'editar_usuario' && $id) {
        $stmt = $conn->prepare('UPDATE usuarios SET username=?, email=?, role=? WHERE id=?');
        $stmt->bind_param('sssi', $username, $email, $role, $id);
        $stmt->execute();
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare('INSERT INTO usuarios (username, email, password, role) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('ssss', $username, $email, $hash, $role);
        $stmt->execute();
    }
    header('Location: admin.php');
    exit;
}
if ($accion === 'borrar_usuario' && $id) {
    $stmt = $conn->prepare('DELETE FROM usuarios WHERE id=?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    header('Location: admin.php');
    exit;
}
// Obtener tareas y usuarios con filtros
$usuarios = $conn->query('SELECT * FROM usuarios ORDER BY created_at DESC');
$where = [];
if (!empty($_GET['f_usuario_id'])) {
    $uid = intval($_GET['f_usuario_id']);
    $where[] = "t.usuario_id = $uid";
}
if (!empty($_GET['f_estado'])) {
    $est = $conn->real_escape_string($_GET['f_estado']);
    $where[] = "t.estado = '$est'";
}
$where_sql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
$tareas = $conn->query("SELECT t.*, u.username as asignado, u.profile_image as asignado_img FROM tareas t LEFT JOIN usuarios u ON t.usuario_id = u.id $where_sql ORDER BY t.fecha_creacion DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración - WorkFlow</title>
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
            <a href="calendario.php">Calendario</a>
            <a href="logout.php">Cerrar sesión</a>
        </div>
    </div>
    <div class="container">
        <h2>Panel de Administración</h2>
        <h3>Tareas</h3>
    <form method="GET" style="margin-bottom:10px;">
        <label>Filtrar por usuario:</label>
        <select name="f_usuario_id">
            <option value="">Todos</option>
            <?php foreach ($usuarios as $u): ?>
                <option value="<?= $u['id'] ?>" <?= (isset($_GET['f_usuario_id']) && $_GET['f_usuario_id'] == $u['id']) ? 'selected' : '' ?>><?= htmlspecialchars($u['username']) ?></option>
            <?php endforeach; ?>
        </select>
        <label>Estado:</label>
        <select name="f_estado">
            <option value="">Todos</option>
            <option value="pendiente" <?= (isset($_GET['f_estado']) && $_GET['f_estado'] == 'pendiente') ? 'selected' : '' ?>>Pendiente</option>
            <option value="en_progreso" <?= (isset($_GET['f_estado']) && $_GET['f_estado'] == 'en_progreso') ? 'selected' : '' ?>>En progreso</option>
            <option value="completada" <?= (isset($_GET['f_estado']) && $_GET['f_estado'] == 'completada') ? 'selected' : '' ?>>Completada</option>
        </select>
        <button type="submit">Filtrar</button>
    </form>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="tarea_form" value="1">
        <input type="text" name="titulo" placeholder="Título" required>
        <input type="text" name="descripcion" placeholder="Descripción">
        <select name="prioridad">
            <option value="alta">Alta</option>
            <option value="media" selected>Media</option>
            <option value="baja">Baja</option>
        </select>
        <input type="date" name="fecha_limite" placeholder="Fecha límite">
        <select name="estado">
            <option value="pendiente">Pendiente</option>
            <option value="en_progreso">En progreso</option>
            <option value="completada">Completada</option>
        </select>
        <select name="usuario_id">
            <option value="">Sin asignar</option>
            <?php foreach ($usuarios as $u): ?>
                <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['username']) ?></option>
            <?php endforeach; ?>
        </select>
        <input type="file" name="tarea_zip" accept=".zip">
        <button type="submit">Añadir tarea</button>
    </form>
    <table>
        <tr><th>ID</th><th>Título</th><th>Descripción</th><th>Prioridad</th><th>Fecha límite</th><th>Estado</th><th>Asignado a</th><th>Acciones</th></tr>
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
                    <img src="../public/<?= htmlspecialchars($t['asignado_img']) ?>" alt="img" style="width:40px;height:40px;border-radius:50%;object-fit:cover;vertical-align:middle;margin-right:6px;">
                <?php endif; ?>
                <?= htmlspecialchars($t['asignado'] ?? 'Sin asignar') ?>
            </td>
            <td>
                <?php if (!empty($t['tarea_zip'])): ?>
                    <a href="../public/<?= htmlspecialchars($t['tarea_zip']) ?>" target="_blank" class="btn" style="background:#3498db;color:#fff;padding:6px 16px;border-radius:6px;font-weight:600;margin-bottom:8px;display:inline-block;margin-right:12px;">Descargar archivo</a>
                <?php endif; ?>
                <a href="admin.php?accion=borrar_tarea&id=<?= $t['id'] ?>" onclick="return confirm('¿Borrar tarea?')" class="btn" style="background:#e74c3c;color:#fff;padding:6px 16px;border-radius:6px;font-weight:600;display:inline-block;">Borrar</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <h3>Usuarios</h3>
    <form method="POST">
        <input type="hidden" name="usuario_form" value="1">
        <input type="text" name="username" placeholder="Usuario" required>
        <input type="email" name="email" placeholder="Correo" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <select name="role">
            <option value="desarrollador">Desarrollador</option>
            <option value="admin">Admin</option>
        </select>
        <button type="submit">Añadir usuario</button>
    </form>
    <table>
        <tr><th>ID</th><th>Usuario</th><th>Correo</th><th>Rol</th><th>Acciones</th></tr>
        <?php foreach ($usuarios as $u): ?>
        <tr>
            <td><?= $u['id'] ?></td>
            <td><?= htmlspecialchars($u['username']) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td><?= $u['role'] ?></td>
            <td>
                <?php if ($u['id'] != $_SESSION['user_id']): ?>
                <a href="admin.php?accion=borrar_usuario&id=<?= $u['id'] ?>" onclick="return confirm('¿Borrar usuario?')" class="btn" style="background:#e74c3c;color:#fff;padding:6px 16px;border-radius:6px;font-weight:600;">Borrar</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
