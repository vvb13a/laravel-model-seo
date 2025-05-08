<?php

namespace Vvb13a\LaravelModelSeo\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Vvb13a\LaravelModelSeo\Models\SeoData;

class SeoDataFactory extends Factory
{
    protected $model = SeoData::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(rand(3, 7)),
            'description' => $this->faker->paragraph(rand(1, 2)),
            'keywords' => implode(', ', $this->faker->words(rand(3, 7))),
            'canonical_url' => $this->faker->unique()->url(),
            'robots' => $this->faker->randomElement([
                'index,follow', 'noindex,nofollow', 'noindex,follow', 'index,nofollow', null,
            ]),
        ];
    }

    public function empty(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'title' => null,
                'description' => null,
                'keywords' => null,
                'canonical_url' => null,
                'robots' => null,
            ];
        });
    }

    public function robots(string $directive): Factory
    {
        return $this->state(['robots' => $directive]);
    }
}
