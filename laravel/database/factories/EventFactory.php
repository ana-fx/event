<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->sentence(3);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'category' => $this->faker->randomElement(['Music', 'Sports', 'Conference', 'Workshop', 'Festival']),
            'status' => $this->faker->randomElement(['active', 'draft']),
            'banner_path' => 'events/banners/dummy.jpg',
            'thumbnail_path' => 'events/thumbnails/dummy.jpg',
            'start_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'end_date' => $this->faker->dateTimeBetween('+1 month', '+2 months'),
            'description' => $this->faker->paragraphs(3, true),
            'terms' => $this->faker->paragraph(),
            'location' => $this->faker->address(),
            'province' => $this->faker->state(),
            'city' => $this->faker->city(),
            'zip' => $this->faker->postcode(),
            'google_map_embed' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.521260322283!2d106.8195613507864!3d-6.194741395493371!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f5390917b759%3A0x1daecad1344e1a8d!2sMonumen%20Nasional!5e0!3m2!1sid!2sid!4v1652614986701!5m2!1sid!2sid" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>',
            'seo_title' => $name,
            'seo_description' => $this->faker->sentence(),
            'organizer_name' => $this->faker->company(),
        ];
    }
}
