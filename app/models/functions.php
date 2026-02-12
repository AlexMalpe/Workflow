<?php
// Funciones auxiliares para el sistema de login/register
// Aquí se agregarán funciones como validación, registro y login de usuarios.

function es_email_valido($email) {
	return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}
