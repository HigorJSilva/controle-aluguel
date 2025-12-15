<x-mary-card class="flex flex-col relative shadow border border-base-300 group hover:shadow-lg hover:scale-[1.02] hover:-translate-y-1
        hover:border-base-300 transition-transform duration-200">
    <a href="{{ route('locacoes.show', $locacao->id)}}" wire:navigate>
        <x-mary-badge :value="$locacao->status ? __('messages.rent_index_active_label') : __('messages.rent_index_inactive_label')" @class([ 'absolute top-4 right-4' ,
            match ($locacao->status) {
            true => 'badge-success',
            false => 'badge-error badge-dash',
            default => 'badge-error'
            }])/>

            <div class="flex-grow">
                <h3 class="text-lg font-bold text-base-content">{{ $locacao->imovel?->titulo }}</h3>
                <h3 class="text-sm text-base-content/70">{{ $locacao->imovel?->endereco?->endereco }}</h3>
            </div>

            <div class="mt-6 space-y-3">
                <div class="flex items-center gap-2">
                    <x-mary-icon name="lucide.users-round" class="w-4 h-4 text-base-content/70" />
                    <span class="text-xs text-base-content/70">
                        {{ __('messages.rent_index_tenant_label') .':'  }}
                    </span>
                    <span class="text-sm font-semibold text-base-content">
                        {{ $locacao->inquilino->nome }}
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <x-mary-icon name="lucide.dollar-sign" class="w-4 h-4 text-base-content/70" />
                    <span class="text-xs text-base-content/70">
                        {{ __('messages.rent_index_rent_label') .':'  }}
                    </span>

                    <span class="text-sm font-semibold text-primary">
                        {{ 'R$ '. App\Helpers\Formatacao::dinheiro($locacao->valor) }}
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <x-mary-icon name="lucide.calendar" class="w-4 h-4 text-base-content/70" />
                    <span class="text-xs text-base-content/70">
                        {{ __('messages.rent_index_span_label') .':'  }}
                    </span>
                    <span class="text-sm font-semibold text-base-content">
                        {{ App\Helpers\Formatacao::data($locacao->data_inicio) .' - '. (empty($locacao->data_fim) ? __('messages.rent_index_indefinite_span_label') : App\Helpers\Formatacao::data($locacao->data_fim)) }}
                    </span>
                </div>
            </div>
    </a>

</x-mary-card>