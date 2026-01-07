<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Pagamento\GerarFaturas;
use App\Models\Locacao;
use Carbon\Carbon;
use Illuminate\Console\Command;

final class GerarFaturasCommand extends Command
{
    protected $signature = 'locacoes:gerar-faturas';

    protected $description = 'Gera faturas de aluguel baseadas na data de corte programada';

    public function handle(): void
    {
        $hoje = Carbon::today();

        $this->info("Iniciando geração de faturas para: {$hoje->format('d/m/Y')}");

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

        $this->info("Processo finalizado. {$count} faturas geradas.");
    }
}
