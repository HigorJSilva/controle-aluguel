<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Auth;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Inquilino>
 */
final class InquilinoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => Auth::user()?->id ?? User::first()->id,
            'nome' => fake()->name(),
            'documento' => random_bytes(1) !== '' && random_bytes(1) !== '0' ? fake()->numerify('##############') : fake()->numerify('###########'),
            'email' => fake()->email(),
            'telefone' => preg_replace('/\D/', '', fake()->phoneNumber()),
            'observacao' => fake()->realText(maxNbChars: 200),
        ];
    }
}
