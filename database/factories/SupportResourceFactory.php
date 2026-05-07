<?php

namespace Database\Factories;

use App\Models\SupportResource;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<SupportResource>
 */
class SupportResourceFactory extends Factory
{
    protected $model = SupportResource::class;

    public function definition(): array
    {
        $title = $this->faker->sentence(3);

        return [
            'user_id' => User::factory(),
            'created_by' => null,
            'updated_by' => null,
            'title' => $title,
            'slug' => Str::slug($title).'-'.$this->faker->unique()->numberBetween(1, 9999),
            'description' => $this->faker->paragraph(),
            'resource_type' => 'document',
            'visibility' => 'public',
            'status' => 'published',
            'source_type' => 'link',
            'link_url' => 'https://example.com/resource',
            'download_count' => 0,
            'published_at' => now(),
        ];
    }
}
