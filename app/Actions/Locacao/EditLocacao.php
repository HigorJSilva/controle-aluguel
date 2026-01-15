<?php

declare(strict_types=1);

namespace App\Actions\Locacao;

use App\DTO\Locacao\EditLocacaoDTO;
use App\Enums\StatusImoveis;
use App\Enums\StatusPagamentos;
use App\Enums\UserStatus;
use App\Models\Imovel;
use App\Models\Locacao;
use DomainException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

final class EditLocacao
{
    public static function run(EditLocacaoDTO $locacaoDto, Locacao $locacao): ?Locacao
    {
        DB::beginTransaction();

        try {
            if (Auth::user()->status !== UserStatus::ACTIVE) {
                throw new DomainException(__('messages.inactive_user'), 402);
            }

            $imovel = Imovel::select(['id', 'user_id', 'status'])->where('id', $locacaoDto->imovelId)->first();
            $inquilino = Imovel::select(['id', 'user_id'])->where('id', $locacaoDto->inquilinoId)->first();

            if ($imovel->user_id !== Auth::user()->id || $inquilino->user_id !== Auth::user()->id) {
                throw new DomainException(__('messages.unauthorized_user'), 404);
            }

            $locacao->update($locacaoDto->toArray());

            $imovel->status = $locacaoDto->status ? StatusImoveis::ALUGADO->value : StatusImoveis::DISPONIVEL->value;

            $imovel->save();

            if (!$locacaoDto->status) {
                $locacao->loadMissing(['pagamentos' => function ($query) {
                    $query->select(['locacao_id', 'status'])->whereIn('status', [StatusPagamentos::PENDENTE->value, StatusPagamentos::ATRASADO->value])
                        ->orderBy('data_referencia', 'desc');
                }]);

                $locacao->pagamentos()->update(['status' => StatusPagamentos::CANCELADO]);
            }

            DB::commit();

            return $locacao;
        } catch (Throwable $e) {
            if ($e instanceof DomainException) {
                throw $e;
            }

            DB::rollBack();
            Log::error('EditLocacao error', ['Arquivo' => $e->getFile(), 'Linha' => $e->getLine(), 'Mensagem' => $e->getMessage()]);

            return null;
        }
    }
}
