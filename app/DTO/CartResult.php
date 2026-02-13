<?php

namespace App\DTO;

final readonly class CartResult
{
    public function __construct(
        public bool $success,
        public string $message = '',
        public int $status = 200,
        public array $data = [],
    ) {}
}
