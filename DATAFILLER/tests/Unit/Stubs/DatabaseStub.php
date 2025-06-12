<?php
namespace App\Config;

use App\Tests\Unit\Stubs\PdoNowStub;

class Database
{
    public function getConnection(...$args)
    {
        return new PdoNowStub();
    }
}