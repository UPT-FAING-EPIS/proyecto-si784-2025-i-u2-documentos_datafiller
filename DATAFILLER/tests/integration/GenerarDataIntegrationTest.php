<?php

use PHPUnit\Framework\TestCase;

class LoginAndDataFlowTest extends TestCase
{
    protected function setUp(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }

        $_SESSION = [];
        $_POST = [];
        $_FILES = [];
        $_GET = [];

        if (!headers_sent()) {
            header_remove();
        }

        ob_start();
        session_start();
        ob_end_clean();
    }

    public function testLoginAnalyzeAndGenerate()
    {
        // Paso 1: Login simulado
        $_POST['nombre'] = 'mafer';
        $_POST['password'] = '123456';

        ob_start();
        include __DIR__ . '/../../controllers/LoginController.php';
        $loginOutput = ob_get_clean();

        $this->assertArrayHasKey('usuario', $_SESSION, '❌ Login falló: No se creó la sesión');

        // Paso 2: Simulación de análisis de script
        $_POST['script'] = <<<SQL
CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100),
    email VARCHAR(100),
    fecha_registro DATE
);
SQL;

        $_POST['dbType'] = 'sql';

        ob_start();
        include __DIR__ . '/../../controllers/SqlAnalyzerController.php';
        $analyzerOutput = ob_get_clean();

        $this->assertArrayHasKey('estructura_analizada', $_SESSION, '❌ No se detectó estructura analizada');
        $this->assertNotEmpty($_SESSION['estructura_analizada'], '❌ La estructura analizada está vacía');

        // Paso 3: Simulación de generación de datos
        $_POST['formato_salida'] = 'sql';
        $_POST['idioma_datos'] = 'es_ES';

        foreach ($_SESSION['estructura_analizada'] as $index => $tabla) {
            $_POST["tabla_nombre"][$index] = $tabla['nombre'];
            $_POST["tabla_script"][$index] = $tabla['script_original'];
            $_POST["cantidad"][$index] = 10;

            foreach ($tabla['columnas'] as $colIndex => $columna) {
                $_POST["columna_info"][$index][$colIndex] = json_encode($columna);
                $_POST["tipo_generacion"][$index][$colIndex] = 'texto_aleatorio';
            }
        }

        ob_start();
        include __DIR__ . '/../../controllers/DataGeneratorController.php';
        $generatorOutput = ob_get_clean();

        $this->assertArrayHasKey('datos_generados', $_SESSION, '❌ No se generaron datos en sesión');
        $this->assertNotEmpty($_SESSION['datos_generados'], '❌ La variable datos_generados está vacía');

        // (Opcional) Muestra algo del resultado si estás debuggeando
        // echo $generatorOutput;
    }
}
