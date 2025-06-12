<?php
namespace App\Config;

use PDO;

class Database
{
    /**
     * Devuelve siempre un PDO SQLite en memoria para los tests,
     * evitando cualquier conexión real.
     */
    public function getConnection(...$args): PDO
    {
        return new PDO('sqlite::memory:');
    }
}