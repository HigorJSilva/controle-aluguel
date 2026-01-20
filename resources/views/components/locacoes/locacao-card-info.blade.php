<x-mary-card class="flex flex-col relative shadow border border-base-300 group">

    <div class="flex">
        <x-mary-icon class="h-6 w-6 mr-2" name="lucide.scroll-text" />
        <h3 class="text-xl font-bold text-base-content">{{__('messages.rent_show_rent_title')}}</h3>
    </div>

    <div class="mt-2 space-y-3">

        <div class="flex items-center gap-2">
            <x-mary-icon name="o-paper-airplane" class="w-4 h-4 text-base-content/70" />
            <span class="text-xs text-base-content/70">
                {{ __('messages.input_rent_due_day'). ':'  }}
            </span>
            <span class="text-sm font-semibold text-base-content">
                {{ ($locacao->dia_vencimento) }}
            </span>
        </div>

        <div class="flex items-center gap-2">
            <x-mary-icon name="lucide.calendar" class="w-4 h-4 text-base-content/70" />
            <span class="text-xs text-base-content/70">
                {{ __('messages.input_rent_start_date_label'). ':'  }}
            </span>
            <span class="text-sm text-base-content/70">
                {{ App\Helpers\Formatacao::data($locacao->data_inicio) }}
            </span>
        </div>


        <div class="flex items-center gap-2">
            <x-mary-icon name="lucide.calendar" class="w-4 h-4 text-base-content/70" />
            <span class="text-xs text-base-content/70">
                {{ __('messages.input_rent_end_date_label'). ':'  }}
            </span>
            <span class="text-sm text-base-content/70">
                {{ empty($locacao->data_fim) ? __('messages.not_specified') : App\Helpers\Formatacao::data($locacao->data_fim) }}
            </span>

        </div>

        @if(empty($locacao->deleted_at))
        <div class="flex flex-1 mt-4 justify-center bg-primary/5 border border-primary/20 rounded-lg p-4 hover:bg-primary/70 hover:text-white">
            <a href="{{route('locacoes.show', $locacao->id)}}"> {{__('messages.rent_show_rent_details')}}</a>
        </div>
        @endif
    </div>

</x-mary-card>