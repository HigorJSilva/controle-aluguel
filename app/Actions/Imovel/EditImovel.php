<?php

declare(strict_types=1);

namespace App\Actions\Imovel;

use App\DTO\Imovel\EditImovelDTO;
use App\Http\Controllers\Controller;
use App\Models\Endereco;
use App\Models\Imovel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

final class EditImovel extends Controller
{
    public static function run(EditImovelDTO $imovelDto, Imovel $imovel, ?Endereco $endereco): ?Imovel
    {
        DB::beginTransaction();

        try {
            $enderecoPayload = $imovelDto->toArray()['endereco'];
            $imovelPayload = $imovelDto->toArray();

            unset($imovelPayload['endereco']);

            $imovel->update($imovelPayload);
            $imovel->save();

            if(!empty($endereco)){
                $endereco->update($enderecoPayload);
                $endereco->save();
            }

            DB::commit();

            return $imovel;
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('EditImovel error', ['Arquivo' => $e->getFile(), 'Linha' => $e->getLine(), 'Mensagem' => $e->getMessage(), 'Usuario' => $imovelDto->userId]);

            return null;
        }
    }
}
