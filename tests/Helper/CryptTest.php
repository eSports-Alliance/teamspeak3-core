<?php

namespace ESportsAlliance\TeamSpeakCore\Tests\Helper;

use PHPUnit\Framework\TestCase;
use \PHPUnit\Framework\Constraint\IsType as PHPUnit_IsType;
use ESportsAlliance\TeamSpeakCore\Helper\Crypt;

class CryptTest extends TestCase
{
    public function testEncrypt()
    {
        $crypto = new Crypt('My Secret Key');
        $this->assertEquals('b45xr3dIAI4=', $crypto->encrypt('password'));
        $this->assertEquals('password', $crypto->decrypt('b45xr3dIAI4='));
    }
}
