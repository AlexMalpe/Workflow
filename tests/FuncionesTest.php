<?php
// tests/FuncionesTest.php
require_once __DIR__ . '/../app/models/functions.php';

function test_es_email_valido() {
    $ok = es_email_valido('usuario@dominio.com');
    $fail = es_email_valido('noesunemail');
    if ($ok && !$fail) {
        echo "test_es_email_valido OK\n";
    } else {
        echo "test_es_email_valido FAIL\n";
    }
}

test_es_email_valido();
