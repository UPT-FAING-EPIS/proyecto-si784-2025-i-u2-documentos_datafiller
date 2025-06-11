<?php
namespace App\Controllers;

class ClearResultsController
{
    /**
     * Limpia las variables de sesión relacionadas con resultados generados.
     * @return array Estado de la operación y mensaje.
     */
    public function clearResults(): array
    {
        if(session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        unset($_SESSION['datos_generados']);
        unset($_SESSION['estadisticas_generacion']);
        unset($_SESSION['estructura_analizada']);
        unset($_SESSION['db_type']);

        return [
            'success' => true,
            'message' => 'Resultados limpiados exitosamente'
        ];
    }

}
