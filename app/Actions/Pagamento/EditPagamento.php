<?php

declare(strict_types=1);

namespace App\Actions\Pagamento;

use App\DTO\Pagamento\EditPagamentoDTO;
use App\Enums\StatusPagamentos;
use App\Enums\UserStatus;

use App\Models\Pagamento;
use Carbon\Carbon;
use DomainException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

final class EditPagamento
{
    public static function run(EditPagamentoDTO $pagamentoDto, Pagamento $pagamento): ?Pagamento
    {
        DB::beginTransaction();

        try {
            if (Auth::user()->status !== UserStatus::ACTIVE) {
                throw new DomainException(__('messages.inactive_user'), 402);
            }

            $pagamento = $pagamento->loadMissing([
                'locacao' => function ($query) {
                    $query->select(['id', 'imovel_id', 'inquilino_id'])->withTrashed();
                },
                'locacao.inquilino' => function ($query) {
                    $query->select(['id', 'user_id'])
                        ->withTrashed();
                },
                'locacao.imovel' => function ($query) {
                    $query->select(['id', 'user_id'])
                        ->withTrashed();
                },
            ]);

            if ($pagamento->locacao->imovel->user_id !== Auth::user()->id || $pagamento->locacao->inquilino->user_id !== Auth::user()->id) {
                throw new DomainException(__('messages.unauthorized_user'), 404);
            }

            $payload = $pagamentoDto->toArray();

            if ($pagamento->status !== StatusPagamentos::RECEBIDO && $pagamentoDto->status === StatusPagamentos::RECEBIDO) {
                $payload['dataPagamento'] =  empty($pagamentoDto->dataPagamento) ?
                    Carbon::now()->format('Y-m-d')
                    : $pagamentoDto->dataPagamento;
            }

            if($pagamentoDto->status !== StatusPagamentos::RECEBIDO && !empty($pagamento->data_pagamento)){
                $payload['data_pagamento'] = null;
            }

            $pagamento->update($payload);
            $pagamento->save();

            DB::commit();

            return $pagamento;
        } catch (Throwable $e) {
            if ($e instanceof DomainException) {
                throw $e;
            }

            DB::rollBack();
            Log::error('EditPagamento error', ['Arquivo' => $e->getFile(), 'Linha' => $e->getLine(), 'Mensagem' => $e->getMessage()]);

            return null;
        }
    }
}
