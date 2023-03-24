<?php

namespace App\Domain\Services;

class ComputeCartStatusService
{
    public function execute(CartDTO|string $input)
    {

    }

    private function sanitizeInput(CartDTO|string $input): string
    {
        return $input instanceof CartDTO ? $input->uuid : $input;
    }
}
