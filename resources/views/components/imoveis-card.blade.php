<x-mary-card class="flex flex-col relative shadow border border-base-300">

    <x-mary-badge :value="App\Enums\StatusImoveis::tryFrom($imovel->status)?->label() ?? $imovel->status" @class([ 'absolute top-4 right-4' , 'badge-success'=> $imovel->status == App\Enums\StatusImoveis::ALUGADO->value,
        'badge-success badge-dash' => in_array($imovel->status,[
        App\Enums\StatusImoveis::INDISPONIVEL->value,
        App\Enums\StatusImoveis::AGUARDANDO_LOCACAO->value,
        App\Enums\StatusImoveis::EM_MANUTENCAO->value
        ]),
        'badge-warning' => $imovel->status == App\Enums\StatusImoveis::DISPONIVEL->value,
        ]) />

        <!-- Corpo do Card -->
        <div class="flex-grow">
            <div class="flex items-start gap-3">
                <!-- Ícone -->
                <div class="h-10 w-10 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                    <x-mary-icon name="o-building-office" class="h-6 w-6 text-primary" />
                </div>
                <!-- Textos Título/Subtítulo -->
                <div class="flex-grow">
                    <h3 class="text-lg font-bold text-base-content">{{ $imovel->titulo }}</h3>
                    <p class="text-sm text-base-content/70">{{ $imovel->endereco->endereco . ', ' .  $imovel->endereco->bairro . ', ' . (App\Helpers\Cidades::getById($imovel->endereco->cidade)['nome']).' - '.  $imovel->endereco->estado}}</p>
                </div>
            </div>

            <div class="mt-6 space-y-3">
                <div class="flex items-center gap-2">
                    <x-mary-icon name="o-currency-dollar" class="w-4 h-4 text-base-content/70" />
                    <span class="text-sm font-semibold text-base-content">
                        R$ {{ number_format($imovel->valor_aluguel_sugerido ?? 0, 2, ',', '.') }}
                    </span>
                    <span class="text-sm text-base-content/70">/ mês</span>
                </div>
                <div class="flex items-center gap-2">
                    <x-mary-icon name="o-user" class="w-4 h-4 text-base-content/70" />
                    <span class="text-sm text-base-content/70">
                        {{ $imovel->inquilino->nome ?? __('messages.vacant_property_title') }}
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <x-mary-icon name="o-calendar-days" class="w-4 h-4 text-base-content/70" />
                    <span class="text-sm text-base-content/70">
                        Próx. Pgto: {{ $imovel->proximo_pagamento ?? __('messages.next_payment_property_title') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Ações do Card -->
        <x-slot:actions class="mt-4 pt-4 border-t border-base-200">
            <x-mary-button :label="__('messages.edit')" icon="o-pencil" :link="route('imoveis.edit', $imovel->id)" class="btn-sm btn-ghost" />
            <x-mary-button
                :label="__('messages.remove')"
                icon="o-trash"
                @click="$dispatch('target-delete', { imoveis: {{ $imovel->id }} })"
                class="btn-sm btn-ghost text-error"
                spinner />
        </x-slot:actions>
</x-mary-card>