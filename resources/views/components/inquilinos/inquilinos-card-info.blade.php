<x-mary-card class="flex flex-col relative shadow border border-base-300 group">

    <div class="flex">
        <x-mary-icon class="h-6 w-6 mr-2" name="o-user" />
        <h3 class="text-xl font-bold text-base-content">{{__('messages.rent_index_tenant_label')}}</h3>
    </div>

    <div class="flex-grow mt-4">
        <h3 class="text-lg font-bold text-base-content">{{ $inquilino->nome }}</h3>
    </div>

    <div class="mt-2 space-y-3">
        <div class="flex items-center gap-2">
            <x-mary-icon name="o-paper-airplane" class="w-4 h-4 text-base-content/70" />
            <span class="text-xs text-base-content/70">
                {{ strlen($inquilino->documento) === 11 ? 'CPF:' : 'CNPJ:' }}
            </span>
            <span class="text-sm font-semibold text-base-content">
                {{ App\Helpers\Formatacao::documento($inquilino->documento) }}
            </span>
        </div>
        <div class="flex items-center gap-2">
            <x-mary-icon name="lucide.mail" class="w-4 h-4 text-base-content/70" />
            <span class="text-sm text-base-content/70">
                {{ empty($inquilino->email) ? __('messages.not_specified') : $inquilino->email}}
            </span>
        </div>
        <div class="flex items-center gap-2">
            <x-mary-icon name="lucide.phone" class="w-4 h-4 text-base-content/70" />
            <span class="text-sm text-base-content/70">
                {{ empty($inquilino->telefone) ? __('messages.not_specified') : App\Helpers\Formatacao::telefone($inquilino->telefone) }}
            </span>
        </div>

        @if(empty($inquilino->deleted_at))
        <div class="flex flex-1 mt-4 justify-center bg-primary/5 border border-primary/20 rounded-lg p-4 hover:bg-primary/70 hover:text-white">
            <a href="{{route('inquilinos.show', $inquilino->id)}}"> {{__('messages.rent_show_tenant_details')}}</a>
        </div>
        @endif
    </div>

</x-mary-card>