<?php

namespace MotaWord\Active\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Http\Kernel;
use Illuminate\Support\Facades\Route;
use MotaWord\Active\ActiveServeMiddleware;
use MotaWord\Active\MotaWordActiveServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setupRoutes();
    }

    /**
     * @param Application $app
     */
    protected function getPackageProviders($app): array
    {
        return [
            MotaWordActiveServiceProvider::class,
        ];
    }

    /**
     * @param Application $app
     */
    protected function getEnvironmentSetUp($app): void
    {
        $app->make(Kernel::class)->prependMiddleware(ActiveServeMiddleware::class);

        // mock guzzle client
        $app->bind(Client::class, function () {
            $mock = new MockHandler([
                new Response(200, ['prerender.io-mock' => true]),
            ]);
            $stack = HandlerStack::create($mock);

            return new Client(['handler' => $stack]);
        });
    }

    protected function setupRoutes(): void
    {
        Route::get('test-middleware', function () {
            return 'GET - Success';
        });

        Route::post('test-middleware', function () {
            return 'Success';
        });
    }
}
