<?php

namespace Database\Factories;

use App\Models\TravelOrder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TravelOrder>
 */
class TravelOrderFactory extends Factory
{
    protected $model = TravelOrder::class;

    public function definition(): array
    {
        $departureDate = $this->faker->dateTimeBetween('+1 month', '+6 months');
        $returnDate = clone $departureDate;
        $returnDate->modify('+' . $this->faker->numberBetween(3, 14) . ' days');

        return [
            'user_id' => User::factory(),
            'destination' => $this->faker->city() . ', ' . $this->faker->country(),
            'departure_date' => $departureDate,
            'return_date' => $returnDate,
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected', 'cancelled']),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }
}
