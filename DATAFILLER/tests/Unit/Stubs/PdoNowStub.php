<?php
namespace App\Tests\Unit\Stubs;

use PDO;
use PDOStatement;

/**
 * Extiende PDO para reemplazar NOW() por CURRENT_TIMESTAMP en queries.
 */
class PdoNowStub extends PDO
{
    public function __construct()
    {
        parent::__construct('sqlite::memory:');
    }

    public function prepare($statement, $options = null)
    {
        // Reemplaza NOW() por CURRENT_TIMESTAMP antes de preparar
        $statement = str_replace('NOW()', 'CURRENT_TIMESTAMP', $statement);
        if ($options === null) {
            return parent::prepare($statement);
        }
        return parent::prepare($statement, $options);
    }
}