<?php
namespace App\Controllers;

use App\Config\Database;
use App\Models\Usuario;

class UsuarioController {
    private $usuarioModel;
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->usuarioModel = new Usuario($this->db);
    }

    public function obtenerDatosParaHeader($usuario_id) {
        $info_usuario = $this->usuarioModel->obtenerInfoCompleta($usuario_id);

        if(!$info_usuario) {
            return [
                'plan_usuario' => 'gratuito',
                'consultas_restantes' => 0
            ];
        }

        $consultas_restantes = $this->usuarioModel->obtenerConsultasRestantes($usuario_id);

        return [
            'plan_usuario' => $info_usuario['tipo_plan'],
            'consultas_restantes' => $consultas_restantes
        ];
    }

    public function obtenerInfoUsuario($usuario_id) {
        return $this->usuarioModel->obtenerInfoCompleta($usuario_id);
    }

    public function obtenerConsultasRestantes($usuario_id) {
        return $this->usuarioModel->obtenerConsultasRestantes($usuario_id);
    }
}