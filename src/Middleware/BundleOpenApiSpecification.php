<?php

declare(strict_types=1);

namespace JustSteveKing\Laravel\LaravelRedoc\Middleware;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use JustSteveKing\Laravel\LaravelRedoc\Actions\DownloadOpenAPIBundle;
use JustSteveKing\Laravel\LaravelRedoc\Services\BundlerContract;
use Closure;

final class BundleOpenApiSpecification
{
    public function __construct(
        private BundlerContract $bundler,
    ) {}

    /**
     * Provides Redoc with route to download newest bundled OpenAPI specification file.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Config::get('redoc.openapi.bundle.bundler') === null) {
            return $next($request);
        }

        $specification = Config::get('redoc.openapi.bundle.rootFile');

        abort_unless(\file_exists($specification), 404, 'Specification file does not exist');
        abort_unless($this->bundler->isAvailable(), 500, $this->bundler::class.' is not available');

        $bundle = $this->bundler->bundle($specification);

        Config::set('redoc.openapi.path', action(DownloadOpenAPIBundle::class, ['file' => $bundle]));

        return $next($request);
    }
}
