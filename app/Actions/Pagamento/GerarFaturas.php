<?php

declare(strict_types=1);

namespace App\Actions\Pagamento;

use App\Helpers\Datas;
use App\Models\Pagamento;
use Carbon\Carbon;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

final class GerarFaturas
{
    public static function run($locacao): void
    {

        try {
            $hoje = Carbon::today();
            DB::transaction(function () use ($locacao, $hoje): void {
                $dataVencimentoAtual = Carbon::parse($locacao->proxima_geracao_fatura);
                $dataVencimento = $dataVencimentoAtual->copy()->day((int) ($locacao->dia_vencimento));

                $incremento = $dataVencimento > $hoje ? '0' : '1';
                $dataVencimento = Datas::addMonths($dataVencimento, $incremento);

                Pagamento::create([
                    'locacao_id' => $locacao->id,
                    'data_vencimento' => $dataVencimento,
                    'data_referencia' => $dataVencimento->copy()->startOfMonth(),
                    'valor' => $locacao->valor,
                    'descricao' => 'Aluguel ref. ' . $dataVencimento->format('m/Y'),
                    'status' => 'pendente',
                ]);

                $proximaGeracao = $dataVencimento->copy()->subDays($locacao->dias_antecedencia_geracao);
                $proximaGeracao = $proximaGeracao < $dataVencimento ? Datas::addMonths($dataVencimento, '1')->format('Y-m-d') : $proximaGeracao->format('Y-m-d');

                $locacao->update([
                    'proxima_fatura' => $dataVencimento,
                    'proxima_geracao_fatura' => $proximaGeracao,
                ]);
            });
        } catch (Throwable $e) {
            if ($e instanceof DomainException) {
                throw $e;
            }

            Log::error('GerarFaturas error', ['Arquivo' => $e->getFile(), 'Linha' => $e->getLine(), 'Mensagem' => $e->getMessage(), 'Locação' => $locacao->id]);
        }
    }
}
