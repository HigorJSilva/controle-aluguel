<?php

declare(strict_types=1);

namespace App\Actions\Locacao;

use App\Actions\Pagamento\GerarFaturas;
use App\DTO\Locacao\CreateLocacaoDTO;
use App\Enums\StatusImoveis;
use App\Enums\UserStatus;
use App\Models\Imovel;
use App\Models\Locacao;
use DomainException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

final class CreateLocacao
{
    public static function run(CreateLocacaoDTO $locacaoDto): ?Locacao
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

            $locacao = new Locacao($locacaoDto->toArray());
            $imovel->status = StatusImoveis::ALUGADO->value;

            $locacao->save();
            $imovel->save();

            GerarFaturas::run($locacao);
            DB::commit();

            return $locacao;
        } catch (Throwable $e) {
            if ($e instanceof DomainException) {
                throw $e;
            }

            DB::rollBack();
            Log::error('CreateLocacao error', ['Arquivo' => $e->getFile(), 'Linha' => $e->getLine(), 'Mensagem' => $e->getMessage()]);

            return null;
        }
    }
}
