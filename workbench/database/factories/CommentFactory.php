<?php

namespace Workbench\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workbench\App\Models\Comment;

/**
 * @template TModel of Comment
 *
 * @extends Factory<TModel>
 */
class CommentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<TModel>
     */
    protected $model = Comment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'post_id' => PostFactory::new(),
            'content' => $this->faker->sentence(),
            'is_approved' => $this->faker->boolean(),
            'metadata' => [
                'ip_address' => $this->faker->ipv4,
                'user_agent' => $this->faker->userAgent,
            ],
        ];
    }
}