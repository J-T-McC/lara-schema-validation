<?php

namespace JTMcC\LaraSchemaValidation;

use Illuminate\Support\ServiceProvider;
use Pest\Expectation;

class LaraSchemaValidationServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/lara-schema-validation.php',
            'lara-schema-validation'
        );

        $this->publishes([
            __DIR__ . '/../config/lara-schema-validation.php' => config_path('lara-schema-validation.php'),
        ], 'lara-schema-validation-config');
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot(): void
    {
        if (class_exists(Expectation::class)) {
            expect()->extend('toMatchSchema', function (string $schemaFile) {
                /** @var Expectation $this */
                SchemaValidator::validateResponse($this->value, $schemaFile);

                return $this;
            });

            expect()->extend('toMatchSchemaCollection', function (string $schemaFile) {
                /** @var Expectation $this */
                SchemaValidator::validateResponseCollection($this->value, $schemaFile);

                return $this;
            });
        }
    }
}