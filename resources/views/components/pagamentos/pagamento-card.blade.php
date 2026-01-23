<x-mary-card class="shadow border border-base-300">

    <h2 class="text-xl font-bold mb-4">{{__('messages.property_show_payment_history_title')}}</h2>

    <div class="space-y-3">
        @forelse ($pagamentos as $pagamento)
        <div class="flex items-center justify-between p-4 bg-base-200/50 rounded-lg">
            <div>
                <p class="font-medium text-base-content m-2">{{ App\Helpers\Formatacao::dataMesAno($pagamento['data_referencia'])}}</p>

                <div class="flex flex-row gap-6 m-2">
                    <p class="text-sm text-base-content/70">{{__('messages.property_show_due_label')}} {{ App\Helpers\Formatacao::data($pagamento['data_vencimento']) }}</p>

                    @if(!empty($pagamento['data_pagamento']))
                    <p class="text-sm text-base-content/70">
                        {{__('messages.property_show_paid_in_label')}} {{ App\Helpers\Formatacao::data($pagamento['data_pagamento']) }}
                    </p>
                    @endif
                </div>

            </div>
            <div class="flex justify-end gap-4">
                <p class="font-semibold text-base-content">R$ {{ App\Helpers\Formatacao::dinheiro($pagamento['valor']) }}</p>
                <x-mary-badge :value="App\Enums\StatusPagamentos::tryFrom($pagamento['status'])?->label()" @class([ 'top-4 right-4' , App\Enums\StatusPagamentos::getCssClass($pagamento['status'])]) />
            </div>
        </div>
        @empty
        <p class="text-base-content/70">{{__('messages.property_show_empty_payment_history_title')}}</p>
        @endforelse
    </div>
</x-mary-card>