<?php

declare(strict_types=1);

namespace App\Actions\Inquilino;

use App\DTO\Inquilino\EditInquilinoDTO;
use App\Enums\UserStatus;
use App\Models\Inquilino;
use DomainException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

final class EditInquilino
{
    public static function run(EditInquilinoDTO $inquilinoDto, Inquilino $inquilino): ?Inquilino
    {
        DB::beginTransaction();

        try {
            if (Auth::user()->status !== UserStatus::ACTIVE) {
                throw new DomainException('Usuário inativo. Consulte sua assinatura', 402);
            }

            $inquilino->update($inquilinoDto->toArray());

            DB::commit();

            return $inquilino;
        } catch (Throwable $e) {
            if ($e instanceof DomainException) {
                throw $e;
            }

            DB::rollBack();
            Log::error('CreateInquilino error', ['Arquivo' => $e->getFile(), 'Linha' => $e->getLine(), 'Mensagem' => $e->getMessage(), 'Usuario' => $inquilino->userId]);

            return null;
        }
    }
}
