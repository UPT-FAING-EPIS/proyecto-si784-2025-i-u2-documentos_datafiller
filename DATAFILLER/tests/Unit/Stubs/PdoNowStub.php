<?php
namespace App\Tests\Unit\Stubs;

use PDO;

class PdoNowStub extends PDO
{
    public function __construct()
    {
        parent::__construct('sqlite::memory:');
    }

    public function prepare($statement, $options = null)
    {
        // Reemplaza NOW() por CURRENT_TIMESTAMP para compatibilidad con SQLite
        $statement = str_replace('NOW()', 'CURRENT_TIMESTAMP', $statement);
        return parent::prepare($statement, $options ?? []);
    }
}