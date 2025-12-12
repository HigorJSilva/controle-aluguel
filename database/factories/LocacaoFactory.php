<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Locacao>
 */
class LocacaoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $imoveisIds = DB::table('imoveis')->pluck('id');
        $inquilinosIds = DB::table('inquilinos')->pluck('id');
        $dataInicio = Carbon::createFromFormat('Y-m-d',fake()->date());
        $dataInicio = fake()->dateTimeBetween('-2 years', 'now');

        return [
            'imovel_id'  => fake()->randomElement($imoveisIds),
            'inquilino_id'  => fake()->randomElement($inquilinosIds),
            'valor' => fake()->randomFloat(2, 400, 24000),
            'dia_vencimento'=> fake()->numberBetween(1, 31),
            'data_inicio' => $dataInicio->format('Y-m-d'),
            'data_fim' => fake()->boolean() ? fake()->dateTimeBetween('now', '+ 3 years')->format('Y-m-d') : null,
            'status' =>  fake()->boolean(),
            'dias_antecedencia_geracao' => fake()->numberBetween(1, 31),
            'proxima_geracao_fatura' =>  Carbon::createFromDate($dataInicio)->addDays(5)->format('Y-m-d'),
            'proxima_fatura' => Carbon::createFromDate($dataInicio)->addMonth()->format('Y-m-d'),
        ];
    }
}