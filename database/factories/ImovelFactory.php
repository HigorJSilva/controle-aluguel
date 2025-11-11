<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
final class ImovelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'titulo' => fake()->words(3, true),
            'tipo' => fake()->numberBetween(1, 5),
            'user_id' => 3,
            'valor_aluguel_sugerido' => fake()->randomFloat(2, 400, 24000),
            'quartos' => fake()->randomNumber(2),
            'banheiros' => fake()->randomNumber(2),
            'area' => fake()->randomFloat(2, 1, 200),
            'status' => fake()->numberBetween(1, 5),
            'descricao' => fake()->realText(maxNbChars: 2000),
        ];
    }
}
