<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title>WorkFlow - Gestión de Tareas en Equipo</title>
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
			font-size: 2.2em;
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
		.hero {
			max-width: 700px;
			margin: 60px auto 0 auto;
			text-align: center;
		}
		.hero-title {
			font-size: 3em;
			font-weight: 700;
			margin-bottom: 0.2em;
			color: #6dd5fa;
		}
		.hero-desc {
			font-size: 1.3em;
			color: #e0e0e0;
			margin-bottom: 2em;
		}
		.features {
			display: flex;
			flex-wrap: wrap;
			justify-content: center;
			gap: 32px;
			margin: 3em 0 2em 0;
		}
		.feature {
			background: rgba(255,255,255,0.07);
			border-radius: 14px;
			padding: 32px 28px;
			min-width: 220px;
			max-width: 260px;
			box-shadow: 0 4px 24px rgba(44,62,80,0.12);
			text-align: center;
		}
		.feature i {
			font-size: 2.2em;
			color: #6dd5fa;
			margin-bottom: 0.5em;
		}
		.feature-title {
			font-size: 1.2em;
			font-weight: 600;
			margin-bottom: 0.5em;
		}
		.feature-desc {
			color: #cfd8dc;
			font-size: 1em;
		}
		@media (max-width: 700px) {
			.hero { margin: 30px 10px 0 10px; }
			.features { flex-direction: column; gap: 18px; }
		}
	</style>
</head>
<body>
	<script>
	// Contador de visitas por usuario
	function getCookie(name) {
		let match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
		return match ? decodeURIComponent(match[2]) : null;
	}
	let visitas = getCookie('visitas_web');
	if (visitas) {
		visitas = parseInt(visitas) + 1;
	} else {
		visitas = 1;
	}
	document.cookie = 'visitas_web=' + visitas + ';path=/';
	document.addEventListener('DOMContentLoaded', function() {
		var div = document.createElement('div');
		div.style = 'position:fixed;bottom:0;right:0;background:#2980b9;color:#fff;padding:8px 18px;border-radius:12px 0 0 0;font-weight:600;z-index:9999;margin:16px;';
		div.innerHTML = 'Has visitado esta web ' + visitas + ' veces.';
		document.body.appendChild(div);
	});
	<script>
	// Mostrar nombre de usuario guardado
	function getCookie(name) {
		let match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
		return match ? decodeURIComponent(match[2]) : null;
	}
	var usuario = getCookie('remember_user');
	if (usuario) {
		document.addEventListener('DOMContentLoaded', function() {
			var div = document.createElement('div');
			div.style = 'position:fixed;top:0;left:0;background:#232526;color:#fff;padding:8px 18px;border-radius:0 0 12px 0;font-weight:600;z-index:9999;margin:16px;';
			div.innerHTML = 'Bienvenido, ' + usuario + '!';
			document.body.appendChild(div);
		});
	}
	</script>
	<script>
	// Mostrar aviso de cookies si no está aceptado
	if (!document.cookie.includes('cookies_aceptadas=true')) {
		document.addEventListener('DOMContentLoaded', function() {
			var aviso = document.createElement('div');
			aviso.id = 'cookie-aviso';
			aviso.style = 'position:fixed;bottom:0;left:0;width:100%;background:#232526;color:#fff;padding:18px 0;text-align:center;z-index:9999;font-size:1.1em;box-shadow:0 -2px 12px #0002;';
			aviso.innerHTML = 'Este sitio utiliza cookies para mejorar la experiencia. <button style="background:#2980b9;color:#fff;border:none;padding:8px 18px;border-radius:6px;font-weight:600;margin-left:12px;cursor:pointer;" onclick="document.cookie=\'cookies_aceptadas=true;path=/\';document.getElementById(\'cookie-aviso\').remove();">Aceptar</button>';
			document.body.appendChild(aviso);
		});
	}

	// Guardar nombre de usuario en cookie si se ingresa
	function guardarUsuario() {
		var nombre = prompt('Introduce tu nombre de usuario para recordarlo:');
		if (nombre) {
			document.cookie = 'usuario_nombre=' + encodeURIComponent(nombre) + ';path=/';
			alert('Nombre guardado.');
		}
	}
	</script>
	<div class="navbar">
		<div class="logo"><i class="fa-solid fa-diagram-project"></i> WorkFlow</div>
		<div class="nav-btns">
			<a href="login.php">Iniciar sesión</a>
			<a href="register.php">Registrarse</a>
			<a href="calendario.php">Calendario</a>
			<a href="#" onclick="guardarUsuario()">Recordar usuario</a>
		</div>
	</div>
	<div class="hero">
		<div class="hero-title">Organiza el trabajo de tu equipo</div>
		<div class="hero-desc">WorkFlow te ayuda a gestionar tareas, asignar responsables, establecer prioridades y visualizar el avance de tu equipo de desarrollo de forma moderna y sencilla.</div>
		<div class="features">
			<div class="feature">
				<i class="fa-solid fa-users"></i>
				<div class="feature-title">Gestión de equipos</div>
				<div class="feature-desc">Crea usuarios, asigna roles y mantén el control de tu equipo.</div>
			</div>
			<div class="feature">
				<i class="fa-solid fa-list-check"></i>
				<div class="feature-title">Tareas y prioridades</div>
				<div class="feature-desc">Asigna tareas, establece prioridades y fechas límite fácilmente.</div>
			</div>
			<div class="feature">
				<i class="fa-solid fa-calendar-days"></i>
				<div class="feature-title">Calendario visual</div>
				<div class="feature-desc">Visualiza todas las tareas y deadlines en un calendario interactivo.</div>
			</div>
			<div class="feature">
				<i class="fa-solid fa-chart-pie"></i>
				<div class="feature-title">Estadísticas y filtros</div>
				<div class="feature-desc">Filtra tareas, revisa el progreso y obtén métricas del equipo.</div>
			</div>
			<div class="feature">
				<i class="fa-solid fa-comments"></i>
				<div class="feature-title">Colaboración</div>
				<div class="feature-desc">Comenta en tareas y mantén la comunicación fluida.</div>
			</div>
		</div>
	</div>
</body>
</html>
