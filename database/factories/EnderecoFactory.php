<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
final class EnderecoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $adress = fake()->address();

        return [
            'cep' => preg_replace('/\D/', '', explode(',', $adress)[0]),
            'endereco' => explode(',', $adress)[1],
            'bairro' => fake()->city(),
            'cidade' => '5201108',
            'estado' => mb_strtoupper(mb_substr('testers', -2)),
        ];
    }
}
