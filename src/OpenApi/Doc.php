<?php

namespace App\OpenApi;

use Attribute;
use Symfony\Component\HttpFoundation\Response;

#[Attribute(Attribute::TARGET_CLASS)]
class Doc
{
    public function __construct(
        public string $tag,
        public string $url,
        public int $responseCode = Response::HTTP_OK,
        public string $contentType = 'application/json',
        public string $method = 'get',
    )
    {}
}
