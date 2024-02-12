<?php

declare(strict_types=1);

namespace JustSteveKing\Laravel\LaravelRedoc\Actions;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class DownloadOpenAPIBundle extends BaseController
{
    public function __construct(
        private string $storagePath,
    ) {}

    public function __invoke(string $file): StreamedResponse
    {
        $specification = $this->storagePath.'/'.$file;

        if (! Storage::exists($specification)) {
            abort(404, 'Bundle not found');
        }

        return Storage::response($specification);
    }
}
