<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Actions\Pagamento\GerarFaturas;
use App\Models\Locacao;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

final class GerarFaturasJob implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
        //
    }

    public function handle(): void
    {
        $hoje = Carbon::today();

        Log::channel('sentry_logs')->info("Iniciando geração de faturas para: {$hoje->format('d/m/Y')}");

        $locacoesParaProcessar = Locacao::query()
            ->where('status', true)
            ->where(function ($query) use ($hoje): void {
                $query->whereNull('proxima_geracao_fatura')
                    ->orWhereDate('proxima_geracao_fatura', '<', $hoje);
            })
            ->where(function ($query) use ($hoje): void {
                $query->whereNull('data_fim')
                    ->orWhereDate('data_fim', '>=', $hoje);
            })
            ->whereDate('data_inicio', '<=', $hoje)
            ->orderBy('id', 'asc')
            ->get();

        $count = 0;

        foreach ($locacoesParaProcessar as $locacao) {
            GerarFaturas::run($locacao);
            $count++;
        }

        Log::channel('sentry_logs')->info("Processo finalizado. {$count} faturas geradas.");
    }
}
