<?php

namespace Tests;

use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Workbench\App\Http\Controllers\Api\CommentController;
use Workbench\App\Http\Controllers\Api\PostController;

use function Orchestra\Testbench\workbench_path;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    use WithWorkbench;

    public const string SCHEMA_ROOT = __DIR__.'/schemas/';

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('lara-schema-validation.schemas', __DIR__.'/schemas/');
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(
            workbench_path('database/migrations')
        );
    }

    /**
     * Define routes setup.
     *
     * @param  Router  $router
     */
    protected function defineRoutes($router): void
    {
        Route::name('api.')->group(function () {
            Route::get('posts/empty', [PostController::class, 'empty'])
                ->name('posts.empty');

            Route::apiResource('posts', PostController::class)
                ->only(['index', 'show'])
                ->middleware(SubstituteBindings::class);

            Route::apiResource('comments', CommentController::class)
                ->only(['index', 'show'])
                ->middleware(
                    SubstituteBindings::class
                );
        });
    }
}
