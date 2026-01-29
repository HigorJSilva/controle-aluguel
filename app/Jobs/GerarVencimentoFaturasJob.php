<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\StatusPagamentos;
use App\Models\Pagamento;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

final class GerarVencimentoFaturasJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $hoje = Carbon::today()->format('Y-m-d');
        echo "Iniciando vencimento de faturas para: {$hoje}";

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

        echo "Processo finalizado. {$count} faturas geradas.";
    }
}
