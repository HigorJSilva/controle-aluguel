<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\StatusImoveis;
use App\Enums\StatusPagamentos;
use App\Enums\TiposImoveis;
use App\Models\Endereco;
use App\Models\Imovel;
use App\Models\Inquilino;
use App\Models\Locacao;
use App\Models\Pagamento;
use App\Models\User;
use Carbon\Carbon;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

final class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::factory(2)->create();
        Imovel::factory(30)->create();

        $imoveisIds = DB::table('imoveis')->pluck('id');
        foreach ($imoveisIds as $imovelId) {
            Endereco::factory()->create([
                'imovel_id' => $imovelId,
            ]);
        }

        Endereco::factory(30)->create();
        Inquilino::factory(30)->create();
        Locacao::factory(30)->create();

        $this->prepararDemonstracao();
    }

    public function criarPagamentosAteHoje($locacao): void
    {
        $inicio = Carbon::parse($locacao->data_inicio)->startOfDay();
        $hoje = Carbon::today();

        $data = $inicio->copy();

        while ($data <= $hoje) {
            Pagamento::firstOrCreate([
                'locacao_id' => $locacao->id,
                'data_referencia' => $data->format('Y-m-d'),
            ], [
                'data_vencimento' => $data->copy()->day($locacao->dia_vencimento)->format('Y-m-d'),
                'valor' => $locacao->valor,
                'status' => StatusPagamentos::PENDENTE,
            ]);

            $data->addMonth();
        }
    }

    private function prepararDemonstracao(): void
    {
        $user = User::factory()->create(['name' => 'Demonstração', 'email' => 'demonstracao@aluguefacil.com']);

        Imovel::insert([
            [
                'titulo' => 'Apartamento Vista Mar',
                'tipo' => TiposImoveis::APARTAMENTO->value,
                'user_id' => $user->id,
                'valor_aluguel_sugerido' => fake()->randomFloat(2, 400, 24000),
                'status' => StatusImoveis::ALUGADO->value,
            ],
            [
                'titulo' => 'Casa Holanda',
                'tipo' => TiposImoveis::CASA->value,
                'user_id' => $user->id,
                'valor_aluguel_sugerido' => fake()->randomFloat(2, 400, 24000),
                'status' => StatusImoveis::DISPONIVEL->value,
            ],
            [
                'titulo' => 'Sala Comercial Ed. Monte Verde',
                'tipo' => TiposImoveis::SALA_COMERCIAL->value,
                'user_id' => $user->id,
                'valor_aluguel_sugerido' => fake()->randomFloat(2, 400, 24000),
                'status' => StatusImoveis::ALUGADO->value,
            ],
            [
                'titulo' => 'Apartamento Belo Monte',
                'tipo' => TiposImoveis::SALA_COMERCIAL->value,
                'user_id' => $user->id,
                'valor_aluguel_sugerido' => fake()->randomFloat(2, 400, 24000),
                'status' => StatusImoveis::ALUGADO->value,
            ],
        ]);

        $imoveisIds = DB::table('imoveis')->where('user_id', $user->id)->pluck('id');

        foreach ($imoveisIds as $imovelId) {
            Endereco::factory()->create([
                'imovel_id' => $imovelId,
            ]);
        }

        Inquilino::insert([
            [
                'user_id' => $user->id,
                'nome' => 'Gustavo Miguel Galhardo Jr.',
                'documento' => '14474560922',
                'email' => 'gustavo.miguel@gmail.com',
                'telefone' => null,
                'observacao' => null,
            ],
            [
                'user_id' => $user->id,
                'nome' => 'Claro telecomunicações S/A',
                'documento' => '40432544000147',
                'email' => 'contato.claro@gmail.com',
                'telefone' => '11111111111',
                'observacao' => 'Contatar pelo setor de cobrança e falar com Carlos',
            ],
            [
                'user_id' => $user->id,
                'nome' => 'Ana Maria Lopes',
                'documento' => '71324063700',
                'email' => 'ana.maria@gmail.com',
                'telefone' => null,
                'observacao' => null,
            ],
            [
                'user_id' => $user->id,
                'nome' => 'Pedro Paulo ',
                'documento' => '64605269827',
                'email' => 'pedro.paulo@gmail.com',
                'telefone' => '11111121212',
                'observacao' => null,
            ],
        ]);

        $inquilinosIds = DB::table('inquilinos')->where('user_id', $user->id)->pluck('id');
        $imoveisAlugadosIds = DB::table('imoveis')->where(['user_id' => $user->id, 'status' => StatusImoveis::ALUGADO->value])->pluck('id');

        $datasInicio = ['2025-06-01', '2025-12-01', '2026-01-01'];

        foreach ($imoveisAlugadosIds as $key => $imovelId) {
            $locacao = Locacao::create(
                [
                    'imovel_id' => $imovelId,
                    'inquilino_id' => $inquilinosIds[$key],
                    'dia_vencimento' => 10,
                    'data_inicio' => $datasInicio[$key],
                    'valor' => fake()->randomFloat(2, 400, 24000),
                    'data_fim' => null,
                    'status' => 1,
                    'dias_antecedencia_geracao' => 30,
                    'proxima_geracao_fatura' => null,
                    'proxima_fatura' => null,
                ]
            );
            $this->criarPagamentosAteHoje($locacao);
        }
    }
}
