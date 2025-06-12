<?php
namespace App\Models;

class Usuario {
    // Simula propiedad usada en varios tests
    public $id = 10;

    public function __construct($db) {}

    public function obtenerInfoUsuario($usuario_id) {
        return [
            'id' => $usuario_id,
            'nombre' => 'pepe',
            'apellido_paterno' => 'p',
            'email' => 'p@e',
            'plan' => 'premium',
            'consultas_diarias' => 5,
            'fecha_ultima_consulta' => '2025-06-11'
        ];
    }

    public function obtenerInfoCompleta($usuario_id) {
        return [
            'id' => $usuario_id,
            'nombre' => 'ana',
            'apellido_paterno' => 'x',
            'email' => 'x@e',
            'tipo_plan' => 'gratuito',
            'consultas_diarias' => 1,
            'fecha_ultima_consulta' => '2025-06-12'
        ];
    }

    public function buscarPorEmail($email) {
        return $email === 'a@e' ? ['id' => 7, 'nombre' => 'alex', 'apellido_paterno' => 'p', 'email' => 'a@e'] : false;
    }

    public function buscarPorNombre($nombre) {
        return $nombre === 'pep' ? ['id' => 5, 'nombre' => 'pep', 'apellido_paterno' => 'p', 'apellido_materno' => 'm', 'email' => 'p@e'] : false;
    }

    public function crear() {
        return $this->id === 10;
    }

    public function obtenerConsultasRestantes($usuario_id = null) {
        return 2;
    }

    public function puedeRealizarConsulta() { return true; }

    public function incrementarConsultas() { return true; }

    public function obtenerConsultasHoy() { return 2; }

    public function actualizarPlan() { return true; }

    public function existeUsuario() { return true; }

    public function limpiarTokensExpirados() { return true; }

    public function guardarTokenRecuperacion() { return true; }

    public function verificarTokenRecuperacion() { return true; }

    public function obtenerPlanUsuario() { return 'premium'; }

    public function obtenerEstadisticasUsuario() {
        return [
            'total_consultas' => 4,
            'total_registros_generados' => 100,
            'dias_activos' => 2,
            'ultima_actividad' => '2025-06-12'
        ];
    }

    public function calcularConsultasRestantes() { return 101; }

    public function validarLogin($nombre = null, $pass = null) {
        return ['usuario' => ['exito' => true]];
    }
}