<x-mary-card class="flex flex-col relative shadow border border-base-300 group">
    <!-- Corpo do Card -->
    <div class="flex-grow">
        <div class="flex">
            <x-mary-icon class="h-6 w-6 mr-2" name="o-building-office" />
            <h3 class="text-xl font-bold text-base-content">{{__('messages.rent_show_property_title')}}</h3>
        </div>


        <div class="mt-4 space-y-3">
            <div class="flex-grow">
                <h3 class="text-lg font-bold text-base-content">{{ $imovel->titulo }}</h3>
                <p class="text-sm text-base-content/70">{{ $imovel->endereco->endereco . ', ' .  $imovel->endereco->bairro . ', ' . (App\Helpers\Cidades::getById($imovel->endereco->cidade)['nome']).' - '.  $imovel->endereco->estado}}</p>
            </div>
        </div>

    </div>

    <div class="mt-2">
        <x-mary-badge class='bg-base-300' :value="App\Enums\TiposImoveis::tryFrom($imovel->tipo)->label()" />
    </div>

    @if(empty($imovel->deleted_at))
    <div class="flex flex-1 mt-4 justify-center bg-primary/5 border border-primary/20 rounded-lg p-4 hover:bg-primary/70 hover:text-white">
        <a href="{{route('imoveis.show', $imovel->id)}}"> {{__('messages.rent_show_property_details')}}</a>
    </div>
    @endif

</x-mary-card>