<?php

declare(strict_types=1);

namespace JustSteveKing\Laravel\LaravelRedoc\Services;

use Illuminate\Support\Facades\{Cache, Process, Storage};
use JustSteveKing\Laravel\LaravelRedoc\Services\BundlerContract;

final class RedocBundler implements BundlerContract
{
    public function __construct(
        private string $storagePath,
    ) {}

    public function bundle(string $specificationPath): string
    {
        if (! \file_exists($specificationPath)) {
            throw new \InvalidArgumentException('Specification file does not exist');
        }

        $hash = \hash_file('sha256', $specificationPath);
        $bundleName = $hash.'.yml';
        $storedHash = Cache::get('openapi');

        if ($hash === $storedHash) {
            return $bundleName;
        }

        $previousBundle = $this->storagePath.'/'.$storedHash.'.yml';
        Storage::delete($previousBundle);

        Process::path($this->storage())
            ->run("npx @redocly/cli@latest bundle {$specificationPath} > {$bundleName}")
            ->throw();

        Cache::put('openapi', $hash);

        return $bundleName;
    }

    public function storage(): string
    {
        if (! Storage::exists($this->storagePath)) {
            Storage::makeDirectory($this->storagePath);
        }

        return Storage::path($this->storagePath);
    }

    public function isAvailable(): bool
    {
        return Process::run('npm -v')->successful();
    }
}
