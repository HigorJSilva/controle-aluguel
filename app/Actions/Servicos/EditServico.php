<?php

declare(strict_types=1);

namespace App\Actions\Servicos;

use App\DTO\Servico\EditServicoDTO;
use App\Enums\UserStatus;
use App\Models\Servico;
use DomainException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

final class EditServico
{
    public static function run(EditServicoDTO $servicoDto, Servico $servico): ?Servico
    {
        DB::beginTransaction();

        try {
            if (Auth::user()->status !== UserStatus::ACTIVE) {
                throw new DomainException('Usuário inativo. Consulte sua assinatura', 402);
            }

            $servico->update($servicoDto->toArray());

            DB::commit();

            return $servico;
        } catch (Throwable $e) {
            if ($e instanceof DomainException) {
                throw $e;
            }

            DB::rollBack();
            Log::error('CreateServico error', ['Arquivo' => $e->getFile(), 'Linha' => $e->getLine(), 'Mensagem' => $e->getMessage(), 'Usuario' => $servico->userId]);

            return null;
        }
    }
}
