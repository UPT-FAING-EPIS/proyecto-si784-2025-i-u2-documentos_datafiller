<?php
namespace App\Config;

use PDO;
use RuntimeException;

class Database
{
    /**
     * Devuelve siempre un PDO SQLite en memoria para los tests,
     * evitando cualquier conexión real.
     */
    public function getConnection(string $dbType): PDO
    {
        return new PDO('sqlite::memory:');
    }
}