<?php

namespace App\Security\ConfirmTokenGenerator;

use DateTimeInterface;

interface ConfirmTokenGeneratorInterface
{
    public function generate(): string;

    public function expireAt(): DateTimeInterface;
}
