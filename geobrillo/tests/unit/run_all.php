<?php
/**
 * Corre todos los tests unitarios del proyecto.
 * Ejecutar:  php tests/unit/run_all.php
 */

$tests = [
    __DIR__ . '/UsuarioControladorTest.php',
    __DIR__ . '/CamionControladorTest.php',
];

$falloAlguno = false;

foreach ($tests as $archivo) {
    $salida = [];
    $codigo = 0;
    exec("php " . escapeshellarg($archivo), $salida, $codigo);
    echo implode("\n", $salida) . "\n";
    if ($codigo !== 0) {
        $falloAlguno = true;
    }
}

if ($falloAlguno) {
    echo "❌ Al menos una suite de tests unitarios falló.\n";
    exit(1);
}

echo "✅ Todas las suites de tests unitarios pasaron.\n";
