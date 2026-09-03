<?php
/**
 * TestRunner: un mini framework de aserciones, para no depender de
 * instalar PHPUnit/composer. Cada archivo de test crea un TestRunner,
 * llama a assertTrue/assertFalse/assertEquals y al final llama a
 * resumen(), que imprime cuántos casos pasaron y termina con código
 * de salida distinto de 0 si algo falló (útil para CI).
 */
class TestRunner {
    private $nombreSuite;
    private $total = 0;
    private $ok = 0;

    public function __construct($nombreSuite) {
        $this->nombreSuite = $nombreSuite;
        echo "== Tests unitarios: {$nombreSuite} ==\n";
    }

    public function assertTrue($condicion, $descripcion) {
        $this->total++;
        if ($condicion) {
            $this->ok++;
            echo "  ✅ {$descripcion}\n";
        } else {
            echo "  ❌ {$descripcion}\n";
        }
    }

    public function assertFalse($condicion, $descripcion) {
        $this->assertTrue(!$condicion, $descripcion);
    }

    public function assertEquals($esperado, $real, $descripcion) {
        $this->assertTrue($esperado === $real, "{$descripcion} (esperado: " . var_export($esperado, true) . ", obtenido: " . var_export($real, true) . ")");
    }

    public function resumen() {
        echo "-- {$this->nombreSuite}: {$this->ok}/{$this->total} casos correctos --\n\n";
        if ($this->ok !== $this->total) {
            exit(1);
        }
    }
}
