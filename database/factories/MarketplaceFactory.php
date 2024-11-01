<?php

namespace Database\Factories;

use App\Models\Marketplace;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Marketplace>
 */
class MarketplaceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Marketplace::class;

    /**
     * The current password being used by the factory.
     */
    protected static ?string $password = null;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'national_id' => (string) fake()->numberBetween(1000000000, 9999999999),
            'mobile' => (string) fake()->numberBetween(1000000000, 9999999999),
            'is_active' => true,
            'password' => static::$password ??= Hash::make('password'),
            'latitude' => (string) fake()->latitude(),
            'longitude' => (string) fake()->longitude(),
            'description' => fake()->text(50),
            "rate_and_review" => null
        ];
    }

    public function forLocation(float $latitude, float $longitude): static
    {
        return $this->state(fn () => [
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);
    }

    public function setInactive(): static
    {
        return $this->state(fn () => [
            'is_active' => false,
        ]);
    }
}
