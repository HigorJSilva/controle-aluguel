<?php

declare(strict_types=1);

namespace App\Actions\Imovel;

use App\DTO\Imovel\CreateImovelDTO;
use App\Http\Controllers\Controller;
use App\Models\Endereco;
use App\Models\Imovel;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

final class CreateImovel extends Controller
{

    public static function run(CreateImovelDTO $imovelDto): ?Imovel
    {
        DB::beginTransaction();

        try {
            $enderecoPayload = $imovelDto->toArray()['endereco'];
            $imovelPayload = $imovelDto->toArray();

            unset($imovelPayload['endereco']);

            $imovel = new Imovel($imovelPayload);
            $imovel->save();

            $endereco = new Endereco(array_merge($enderecoPayload, ['imovel_id' => $imovel->id]));
            $endereco->save();

            DB::commit();

            return $imovel;
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('CreateImovel error', ['Arquivo' => $e->getFile(), 'Linha' => $e->getLine(), 'Mensagem' => $e->getMessage(), 'Usuario' => $imovelDto->userId]);

            return null;
        }
    }
}
