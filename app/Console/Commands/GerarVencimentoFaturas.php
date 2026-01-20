<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\StatusPagamentos;
use App\Models\Pagamento;
use Carbon\Carbon;
use Illuminate\Console\Command;

final class GerarVencimentoFaturas extends Command
{
    protected $signature = 'locacoes:vencimento-faturas';

    protected $description = 'Verifica data de vencimento e altera status de faturas';

    public function handle(): void
    {
        $hoje = Carbon::today()->format('Y-m-d');
        $this->info("Iniciando vencimento de faturas para: {$hoje}");

        $pagamentos = Pagamento::query()
            ->whereNotIn('status', [StatusPagamentos::RECEBIDO, StatusPagamentos::RECEBIDO_PARCIALMENTE, StatusPagamentos::CANCELADO])
            ->where('data_vencimento', '<', $hoje)
            ->get();

        $count = 0;

        foreach ($pagamentos as $pagamento) {
            $pagamento->status = StatusPagamentos::ATRASADO;
            $pagamento->save();
            $count++;
        }

        $this->info("Processo finalizado. {$count} faturas geradas.");
    }
}
