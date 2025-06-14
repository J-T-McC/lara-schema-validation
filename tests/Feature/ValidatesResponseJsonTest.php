<?php

use JTMcC\LaraSchemaValidation\SchemaValidator;
use PHPUnit\Framework\AssertionFailedError;
use Workbench\Database\Factories\CommentFactory;
use Workbench\Database\Factories\PostFactory;

test('it can validate index', function (string $type) {
    // Collect
    PostFactory::new()
        ->has(CommentFactory::new()->count(2))
        ->count(2)
        ->createQuietly();

    // Act
    $results = $this->getJson(route('api.posts.index'), [
        'type' => $type
    ]);

    // Assert
    SchemaValidator::validateResponseCollection($results, 'post.json');

})->with(['paginate', 'simplePaginate', 'all']);

test('it can validate show', function () {
    // Collect
    $post = PostFactory::new()
        ->has(CommentFactory::new()->count(2))
        ->createQuietly();

    // Act
    $results = $this->getJson(route('api.posts.show', $post->id));

    // Assert
    SchemaValidator::validateResponse($results, 'post.json');
});

test('it fails test on schema failure', function () {
    // Act
    $results = $this->getJson(route('api.posts.empty'));

    // Assert
    $this->expectException(AssertionFailedError::class);

    SchemaValidator::validateResponse($results, 'post.json');
});

test('it fails on invalid results and formats the error', function () {
    $results = $this->getJson(route('api.posts.empty'));

    try {
        SchemaValidator::validateResponse($results, 'post.json');
        $this->fail('Expected schema validation to fail, but it passed.');
    } catch (AssertionFailedError $e) {
        $message = $e->getMessage();

        // Top-level structure
        expect($message)
            // Context
            ->toContain('JSON schema validation failed at /tests/Feature/ValidatesResponseJsonTest.php:')
            ->toContain('tests/schemas/post.json')

            // Root-level schema error
            ->toContain('message: The properties must match schema: uuid, slug, status, tags, meta, published_at, created_at, updated_at')
            ->toContain('keyword: properties')
            ->toContain('path: /')
            ->toContain('errors:')

            // Error 0 — uuid
            ->toContain('0:')
            ->toContain('message: The data must match the \'uuid\' format')
            ->toContain('keyword: format')
            ->toContain('path: /uuid')

            // Error 1 — slug
            ->toContain('1:')
            ->toContain('message: The data (null) must match the type: string')
            ->toContain('keyword: type')
            ->toContain('path: /slug')

            // Error 2 — status
            ->toContain('2:')
            ->toContain('message: The data should match one item from enum')
            ->toContain('keyword: enum')
            ->toContain('path: /status')

            // Error 3 — tags
            ->toContain('3:')
            ->toContain('message: The data (string) must match the type: array')
            ->toContain('keyword: type')
            ->toContain('path: /tags')

            // Error 4 — meta
            ->toContain('4:')
            ->toContain('message: The data (string) must match the type: object')
            ->toContain('keyword: type')
            ->toContain('path: /meta')

            // Error 5 — published_at
            ->toContain('5:')
            ->toContain('message: The data must match the \'date-time\' format')
            ->toContain('keyword: format')
            ->toContain('path: /published_at')

            // Error 6 — created_at
            ->toContain('6:')
            ->toContain('message: The data must match the \'date-time\' format')
            ->toContain('keyword: format')
            ->toContain('path: /created_at')

            // Error 7 — updated_at
            ->toContain('7:')
            ->toContain('message: The data must match the \'date-time\' format')
            ->toContain('keyword: format')
            ->toContain('path: /updated_at');
    }
});

test('it extends pest and validates schema', function () {
    // Collect
    $post = PostFactory::new()
        ->has(CommentFactory::new()->count(2))
        ->createQuietly();

    // Act
    $results = $this->getJson(route('api.posts.show', $post->id));

    // Assert
    expect($results)->toMatchSchema('post.json');
});

test('it extends pest and validates schema collection', function () {
    // Collect
    $post = PostFactory::new()
        ->has(CommentFactory::new()->count(2))
        ->count(2)
        ->createQuietly();

    // Act
    $results = $this->getJson(route('api.posts.index'));

    // Assert
    expect($results)->toMatchSchemaCollection('post.json');
});