<?php

namespace Database\Factories;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'payment_id' => $this->faker->uuid(),
            'project_id' => $this->faker->numberBetween(1, 10),
            'details' => $this->faker->sentence(),
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'currency' => $this->faker->randomElement(['USD', 'KZT', 'RUB']),
            'status' => $this->faker->randomElement(['Оплачен', 'Не оплачен']),
        ];
    }
}
