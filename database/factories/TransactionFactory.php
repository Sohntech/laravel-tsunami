<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Type;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    protected $model = Transaction::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'montant' => $this->faker->randomFloat(2, 1, 1000),
            'destinataire' => User::factory(),
            'agent' => User::factory(),
            'exp' => User::factory(),
            'type_id' => Type::factory(),
        ];
    }
}
