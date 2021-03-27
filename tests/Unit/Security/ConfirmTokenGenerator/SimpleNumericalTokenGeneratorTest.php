<?php

namespace App\Tests\Unit\Security\ConfirmTokenGenerator;

use App\Security\ConfirmTokenGenerator\SimpleNumericalTokenGenerator;
use Codeception\Test\Unit;

class SimpleNumericalTokenGeneratorTest extends Unit
{
    public function testGenerate()
    {
        $generator = new SimpleNumericalTokenGenerator();
        $result = $generator->generate();

        $this->assertIsNumeric($result);
    }

    public function testExpireAt()
    {
        $generator = new SimpleNumericalTokenGenerator();
        $date = new \DateTime();
        $result = $generator->expireAt();

        $this->assertGreaterThan($date, $result);
    }
}
