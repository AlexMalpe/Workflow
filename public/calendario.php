<?php
session_start();
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'desarrollador'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Calendario de Tareas - WorkFlow</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
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
            max-width: 950px;
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
        #calendar { max-width: 900px; margin: 40px auto; background: #fff1; border-radius: 12px; box-shadow: 0 2px 12px #0002; }
        .login-link {
            text-align: center;
            margin-top: 24px;
        }
        .login-link a {
            color: #6dd5fa;
            text-decoration: none;
            font-weight: 600;
        }
        .login-link a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="logo"><i class="fa-solid fa-diagram-project"></i> WorkFlow</div>
    </div>
    <div class="container">
        <h2>Calendario de Tareas</h2>
        <button onclick="location.href='index.php'" style="background:#2980b9;color:#fff;border:none;padding:10px 24px;border-radius:6px;font-weight:600;cursor:pointer;margin-bottom:18px;">Volver atrás</button>
        <div id='calendar'></div>
        <div class="login-link">
            <a href="login.php">Ir al login</a>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        fetch('tareas_api.php')
            .then(response => response.json())
            .then(events => {
                var calendarEl = document.getElementById('calendar');
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    locale: 'es',
                    events: events,
                    eventClick: function(info) {
                        alert('Tarea: ' + info.event.title + '\nFecha límite: ' + info.event.start + (info.event.extendedProps.descripcion ? '\nDescripción: ' + info.event.extendedProps.descripcion : ''));
                    }
                });
                calendar.render();
            });
    });
    </script>
</body>
</html>