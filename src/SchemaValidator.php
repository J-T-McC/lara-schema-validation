<?php

namespace JTMcC\LaraSchemaValidation;

use Illuminate\Testing\Assert;
use Illuminate\Testing\TestResponse;
use JsonException;
use Opis\JsonSchema\CompliantValidator;
use Opis\JsonSchema\Errors\ErrorFormatter;
use PHPUnit\Framework\AssertionFailedError;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

class SchemaValidator
{
    private const string SCHEMA_CONFIG_KEY = 'lara-schema-validation.schemas';

    public static function validateResponseCollection(TestResponse $response, string $schema): void
    {
        self::validateResponseData($response, $schema, fn($data) => is_iterable($data) ? $data : [$data]);
    }

    public static function validateResponse(TestResponse $response, string $schemaFile): void
    {
        self::validateResponseData($response, $schemaFile, fn($data) => [$data]);
    }

    private static function getSchemaRoot(): string
    {
        return config(self::SCHEMA_CONFIG_KEY);
    }

    /**
     * @throws JsonException
     */
    private static function getResponseData(TestResponse $response): mixed
    {
        $data = json_decode($response->content(), false, 512, JSON_THROW_ON_ERROR);

        return is_object($data) && property_exists($data, 'data') ? $data->data : $data;
    }

    private static function formatErrors(array $errors, int $level = 0): string
    {
        return array_reduce(array_keys($errors), function ($formatted, $key) use ($errors, $level) {
            $indent = str_repeat('  ', $level);
            $value = $errors[$key];

            return $formatted . (is_array($value)
                    ? "{$indent}{$key}:\n" . self::formatErrors($value, $level + 1)
                    : "{$indent}{$key}: {$value}\n");
        }, '');
    }

    private static function getTestCallerLocation(): string
    {
        foreach (debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS) as $frame) {
            if (isset($frame['file'], $frame['line']) && str_ends_with($frame['file'], '.php')) {
                $pos = strpos($frame['file'], '/tests/');
                if ($pos !== false) {
                    return substr($frame['file'], $pos + 1) . ":{$frame['line']}";
                }
            }
        }

        return 'Caller location unknown';
    }

    private static function validateResponseData(
        TestResponse $response,
        string $schemaFile,
        callable $dataExtractor
    ): void {
        $file = self::getSchemaRoot() . $schemaFile;

        if (!file_exists($file)) {
            throw new FileNotFoundException($file);
        }

        $schema = json_decode(file_get_contents($file), false);
        $validator = new CompliantValidator;
        $validator->setMaxErrors(-1);
        $validator->setStopAtFirstError(false);

        $validator->resolver()->registerPrefix('schema://', self::getSchemaRoot());

        foreach ($dataExtractor(self::getResponseData($response)) as $data) {
            $result = $validator->validate($data, $schema);

            if ($result->hasError()) {
                $errors = (new ErrorFormatter)->formatNested($result->error());
                throw new AssertionFailedError(
                    sprintf(
                        "JSON schema validation failed at /%s\n\nSchema: %s\n\n%s",
                        self::getTestCallerLocation(),
                        $file,
                        self::formatErrors($errors)
                    )
                );
            }

            Assert::assertTrue($result->isValid());
        }
    }
}