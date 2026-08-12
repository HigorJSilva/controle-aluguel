<?php

declare(strict_types=1);

namespace App\Actions\Servicos;

use App\DTO\Servico\CreateServicoDTO;
use App\Enums\UserStatus;
use App\Models\Servico;
use DomainException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

final class CreateServico
{
    public static function run(CreateServicoDTO $servicoDto): ?Servico
    {
        DB::beginTransaction();

        try {
            if (Auth::user()->status !== UserStatus::ACTIVE) {
                throw new DomainException('Usuário inativo. Consulte sua assinatura', 402);
            }

            $servico = new Servico($servicoDto->toArray());
            $servico->save();

            DB::commit();

            return $servico;
        } catch (Throwable $e) {
            if ($e instanceof DomainException) {
                throw $e;
            }

            DB::rollBack();
            Log::error('CreateServico error', ['Arquivo' => $e->getFile(), 'Linha' => $e->getLine(), 'Mensagem' => $e->getMessage(), 'Usuario' => $servicoDto->userId]);

            return null;
        }
    }
}
