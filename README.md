# Lara Schema Validation

Validate Laravel JSON responses against [JSON Schemas](https://json-schema.org/specification).

This package leverages [Opis JSON Schema](https://opis.io/json-schema/) for schema validation and provides convenient methods for validating API responses in Laravel applications.

## Installation

Install the package via Composer:

```bash
composer require jtmcc/lara-schema-validation
```

## Usage

### PHPUnit Example

You can use the `SchemaValidator` class to validate JSON responses against a schema file:

```php
use JTMcC\LaraSchemaValidation\SchemaValidator;

public function testIndexValidation()
{
    // Act
    $response = $this->getJson(route('api.posts.index'));

    // Assert
    SchemaValidator::validateResponseCollection($response, 'post.json');
}

public function testShowValidation()
{
    // Act
    $response = $this->getJson(route('api.posts.show', 1));

    // Assert
    SchemaValidator::validateResponse($response, 'post.json');
}
```

### Pest Example

This package extends Pest with custom expectations for schema validation:

```php
test('it validates schema', function () {
    $response = $this->getJson(route('api.posts.show', 1));

    expect($response)->toMatchSchema('post.json');
});

test('it validates schema collection', function () {
    $response = $this->getJson(route('api.posts.index'));

    expect($response)->toMatchSchemaCollection('post.json');
});
```

### Schema Files

Place your JSON schema files in the `tests/schemas` directory (or any other directory you prefer). For example:

```json
{
  "$schema": "https://json-schema.org/draft/2020-12/schema",
  "type": "object",
  "title": "Post",
  "properties": {
    "id": {
      "type": "integer",
      "description": "Primary ID of the post"
    },
    "uuid": {
      "type": "string",
      "format": "uuid",
      "description": "Unique identifier for the post"
    },  
    "slug": {
      "type": "string",
      "description": "URL-friendly identifier for the post"
    },
    "status": {
      "type": "string",
      "enum": ["draft", "published", "archived"],
      "description": "Status of the post"
    },
    "tags": {
      "type": "array",
      "items": {
        "type": "string"
      },
      "description": "Tags associated with the post"
    },
    "published_at": {
      "type": "string",
      "format": "date-time",
      "description": "Publication timestamp"
    },
    "created_at": {
      "type": "string",
      "format": "date-time",
      "description": "Creation timestamp"
    },
    "updated_at": {
      "type": "string",
      "format": "date-time",
      "description": "Last update timestamp"
    },
    "comments" : {
      "type": "array",
      "items": {
        "$ref": "comment.json"
      },
      "description": "Comments relations associated with the post"
    }
  },
  "required": ["id", "uuid", "slug", "status", "tags", "published_at", "created_at", "updated_at"]
}
```

### Error Handling

When validation fails, an `AssertionFailedError` is thrown with detailed information about the validation errors.

Example error message:

```
JSON schema validation failed at /tests/Feature/SchemaValidatorTest.php:52

Schema: /tests/schemas/post.json

message: The properties must match schema: uuid, slug, status, tags, published_at, created_at, updated_at
keyword: properties
path: /
errors:
  0:
    message: The data must match the 'uuid' format
    keyword: format
    path: /uuid
  1:
    message: The data (null) must match the type: string
    keyword: type
    path: /slug
  2:
    message: The data should match one item from enum
    keyword: enum
    path: /status
  3:
    message: The data (string) must match the type: array
    keyword: type
    path: /tags
  4:
    message: The data must match the 'date-time' format
    keyword: format
    path: /published_at
  5:
    message: The data must match the 'date-time' format
    keyword: format
    path: /created_at
  6:
    message: The data must match the 'date-time' format
    keyword: format
    path: /updated_at
```

## Credits

- [Opis JSON Schema](https://opis.io/json-schema/) for the underlying validation library.

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).
