<?php

namespace App\Tests;

use App\Entity\Bannertop;
use PHPUnit\Framework\TestCase;

// php bin/phpunit .
class BannertopTest extends TestCase
{
    public function testBannertop(): void
    {
        
        $bannertop = new Bannertop();
        $bannertop->setText('-10%')
                  ->setIsActiv(true);

        $this->assertEquals('-10%', $bannertop->getText());
        $this->assertEquals(true, $bannertop->getIsActiv());
    }
}
