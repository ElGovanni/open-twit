<?php

namespace App\OpenApi;

interface RequestInterface
{
    public function getRequestClass(): string;
}
