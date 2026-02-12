<?php
require_once '../app/controllers/LoginController.php';
$remember_user = $_COOKIE['remember_user'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión - WorkFlow</title>
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
        .container {
            max-width: 400px;
            margin: 60px auto 0 auto;
            background: rgba(255,255,255,0.07);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(44,62,80,0.15);
            padding: 40px 30px;
        }
        h2 {
            color: #6dd5fa;
            font-size: 2em;
            margin-bottom: 1em;
            text-align: center;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }
        label {
            font-weight: 600;
            margin-bottom: 4px;
        }
        input[type="text"], input[type="password"] {
            border-radius: 6px;
            border: none;
            padding: 10px 12px;
            font-size: 1em;
            margin-bottom: 8px;
        }
        input[type="submit"] {
            background: #2980b9;
            color: #fff;
            font-weight: 600;
            border: none;
            border-radius: 6px;
            padding: 12px 0;
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
        }
        input[type="submit"]:hover {
            background: #6dd5fa;
            color: #232526;
        }
        .register-link {
            text-align: center;
            margin-top: 18px;
        }
        .register-link a {
            color: #6dd5fa;
            text-decoration: none;
            font-weight: 600;
        }
        .register-link a:hover { text-decoration: underline; }
        .error {
            color: #ff6b6b;
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="logo"><i class="fa-solid fa-diagram-project"></i> WorkFlow</div>
    </div>
    <div class="container">
        <h2>Iniciar Sesión</h2>
        <?php if (!empty($error)): ?>
            <div class="error"> <?= htmlspecialchars($error) ?> </div>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <label for="username">Usuario:</label>
            <input type="text" id="username" name="username" required value="<?= htmlspecialchars($remember_user) ?>">
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>
            <input type="submit" value="Entrar">
        </form>
        <div class="register-link">
            ¿No tienes cuenta? <a href="register.php">Regístrate aquí</a>
        </div>
    </div>
</body>
</html>