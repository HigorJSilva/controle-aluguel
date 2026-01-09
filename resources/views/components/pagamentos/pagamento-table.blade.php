<x-mary-card class="shadow-sm">
    <x-mary-table
        :headers="$headers"
        :rows="$pagamentos"
        link="/pagamentos/{id}"
        class="cursor-pointer"
        with-pagination>

        @scope('cell_imovel', $pagamento)
        <div class="flex items-center gap-2">
            <x-mary-icon name="o-home" class="w-4 h-4 text-gray-500" />
            <div>
                <p class="font-medium">{{ $pagamento["locacao"]["imovel"]["titulo"] }}</p>
                <p class="text-sm text-gray-500">{{ $pagamento["locacao"]["imovel"]["endereco"]["endereco"] }}</p>
            </div>
        </div>
        @endscope

        @scope('cell_inquilino', $pagamento)
        <div class="flex items-center gap-2">
            <x-mary-icon name="o-user" class="w-4 h-4 text-gray-500" />
            {{ $pagamento["locacao"]["inquilino"]['nome'] }}
        </div>
        @endscope

        @scope('cell_referencia', $pagamento)
        <div class="flex items-center gap-2">
            {{ App\Helpers\Formatacao::dataMesAno($pagamento['data_referencia'])}}
        </div>
        @endscope

        @scope('cell_vencimento', $pagamento)
        {{ App\Helpers\Formatacao::data($pagamento['data_vencimento'])}}
        @endscope

        @scope('cell_pagamento', $pagamento)
        {{ empty($pagamento['data_pagamento']) ? '-' : App\Helpers\Formatacao::data($pagamento['data_pagamento'])}}
        @endscope

        @scope('cell_valor', $pagamento)
        <span class="font-semibold">
            R$ {{ App\Helpers\Formatacao::dinheiro($pagamento['valor']) }}
        </span>
        @endscope

        @scope('cell_status', $pagamento)
        <x-mary-badge :value="App\Enums\StatusPagamentos::tryFrom($pagamento['status'])?->label()" @class([ 'top-4 right-4' , App\Enums\StatusPagamentos::getCssClass($pagamento['status'])]) />
        @endscope

    </x-mary-table>
</x-mary-card>