<?php

declare(strict_types=1);

namespace JustSteveKing\Laravel\LaravelRedoc\Services;

interface BundlerContract
{
    public function bundle(string $specificationPath): string;

    public function storage(): string;

    public function isAvailable(): bool;
}
