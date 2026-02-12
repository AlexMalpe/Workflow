# Workflow - Gestión de Tareas PHP

Aplicación web para la gestión de tareas con roles, subida de archivos y comentarios.

## Instalación

1. Clona el repositorio:
   ```
   git clone https://github.com/AlexMalpe/Workflow.git
   ```
2. Copia la carpeta en tu directorio de XAMPP, por ejemplo: `c:/xampp/htdocs/trabajophp`
3. Crea una base de datos llamada `trabajophp` e importa el archivo `trabajophp.sql` desde la carpeta `/sql` o `/basededatos`.
4. Edita `config/db.php` con tus datos de conexión locales.
5. Inicia Apache y MySQL en XAMPP.
6. Accede a `http://localhost/trabajophp/public/` desde tu navegador.

## Uso

- Registro y login de usuarios.
- Gestión de tareas y comentarios.
- Subida y descarga de archivos asociados a tareas.
- Panel de administración y roles (admin, desarrollador, invitado).

## Tests

Puedes ejecutar los tests unitarios básicos con:
```
php tests/FuncionesTest.php
```

## Créditos

- Autor: Alex Malpelo
- Fecha: Febrero 2026

---


