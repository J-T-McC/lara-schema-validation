<?php

namespace Workbench\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Workbench\App\Models\Post;

/**
 * @template TModel of Post
 *
 * @extends Factory<TModel>
 */
class PostFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<TModel>
     */
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => $this->faker->uuid(),
            'title' => $this->faker->sentence(),
            'slug' => Str::slug($this->faker->sentence(), '-'),
            'status' => $this->faker->randomElement(['draft', 'published', 'archived']),
            'tags' => $this->faker->words(3),
            'meta' => [
                'views' => $this->faker->numberBetween(100, 1000),
                'likes' => $this->faker->numberBetween(10, 100),
                'comments' => $this->faker->numberBetween(0, 50),
            ],
            'published_at' => $this->faker->dateTimeThisYear(),
        ];
    }
}