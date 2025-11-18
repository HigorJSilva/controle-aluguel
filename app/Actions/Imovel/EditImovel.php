<?php

declare(strict_types=1);

namespace App\Actions\Imovel;

use App\DTO\Imovel\EditImovelDTO;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Models\Endereco;
use App\Models\Imovel;
use DomainException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

final class EditImovel extends Controller
{
    public static function run(EditImovelDTO $imovelDto, Imovel $imovel, ?Endereco $endereco): ?Imovel
    {
        DB::beginTransaction();

        try {

            if (Auth::user()->status !== UserStatus::ACTIVE) {
                throw new DomainException('Usuário inativo. Consulte sua assinatura', 402);
            }

            $enderecoPayload = $imovelDto->toArray()['endereco'];
            $imovelPayload = $imovelDto->toArray();

            unset($imovelPayload['endereco']);

            $imovel->update($imovelPayload);
            $imovel->save();

            if ($endereco instanceof Endereco) {
                $endereco->update($enderecoPayload);
                $endereco->save();
            }

            DB::commit();

            return $imovel;
        } catch (Throwable $e) {
            if ($e instanceof DomainException) {
                throw $e;
            }

            DB::rollBack();
            Log::error('EditImovel error', ['Arquivo' => $e->getFile(), 'Linha' => $e->getLine(), 'Mensagem' => $e->getMessage(), 'Usuario' => $imovelDto->userId]);

            return null;
        }
    }
}
