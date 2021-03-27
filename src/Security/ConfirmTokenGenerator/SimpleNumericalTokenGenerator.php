<?php

namespace App\Security\ConfirmTokenGenerator;

use DateTime;
use DateTimeInterface;

class SimpleNumericalTokenGenerator implements ConfirmTokenGeneratorInterface
{
    private const EXPIRE_TIME = '6 hours';

    public function generate(): string
    {
        $randomInt = random_int(100000, 999999);
        return (string) $randomInt;
    }

    public function expireAt(): DateTimeInterface
    {
        return new DateTime(sprintf("+%s", self::EXPIRE_TIME));
    }
}
