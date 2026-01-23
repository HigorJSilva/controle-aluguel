<x-mary-card class=" shadow border border-base-300 w-full">
    <a href="{{ route('pagamentos.show', $pagamento['id'])}}" wire:navigate>
        <div class="flex flex-row justify-between">
            <div class="flex flex-row gap-4 mr-6">
                <div>
                    <div class="flex flex-row items-center gap-2 mr-6">
                        <x-mary-icon name="o-home" class="w-4 h-4 text-gray-500" />
                        <div>
                            <p class="font-medium">{{ $pagamento["locacao"]["imovel"]["titulo"] }}</p>
                            <p class="text-sm text-gray-500">{{ $pagamento["locacao"]["imovel"]["endereco"]["endereco"] }}</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2">

                    <div class="flex items-center gap-2">
                        <x-mary-icon name="o-user" class="w-4 h-4 text-gray-500" />
                        {{ $pagamento["locacao"]["inquilino"]['nome'] }}
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between gap-6">
                {{ App\Helpers\Formatacao::data($pagamento['data_vencimento'])}}

                <span class="font-semibold">
                    R$ {{ App\Helpers\Formatacao::dinheiro($pagamento['valor']) }}
                </span>

                <x-mary-badge :value="App\Enums\StatusPagamentos::tryFrom($pagamento['status'])?->label()" @class([ 'top-4 right-4' , App\Enums\StatusPagamentos::getCssClass($pagamento['status'])]) />
            </div>
        </div>
    </a>
</x-mary-card>