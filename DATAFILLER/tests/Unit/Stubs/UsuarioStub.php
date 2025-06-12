<?php
namespace App\Models;

class Usuario {
    public function __construct($db) {}
    public function obtenerInfoUsuario($usuario_id) {
        return ['id' => 1, 'plan' => 'premium'];
    }
}