<x-mary-card class="flex flex-col relative shadow border border-base-300 group hover:shadow-lg hover:scale-[1.02] hover:-translate-y-1
        hover:border-base-300 transition-transform duration-200">
    <a href="{{ route('inquilinos.show', $inquilino->id)}}" wire:navigate>

        <div class="flex-grow">
            <h3 class="text-lg font-bold text-base-content">{{ $inquilino->nome }}</h3>
        </div>

        <div class="mt-6 space-y-3">
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
            @if(!empty($inquilino->observacao))
            <div class="flex items-center gap-2">
                <span class="text-sm text-base-content/70">
                    {{ Str::limit($inquilino->observacao, 50)}}
                </span>
            </div>
            @endif
        </div>
    </a>

    <x-slot:actions class="mt-4 pt-4 border-t border-base-200">
        <x-mary-button :label="__('messages.edit')" icon="o-pencil" :link="route('inquilinos.edit', $inquilino->id)" class="btn-sm btn-ghost" />
        <x-mary-button
            :label="__('messages.remove')"
            icon="o-trash"
            @click="$dispatch('target-delete', { inquilinos: {{ $inquilino->id }} })"
            class="btn-sm btn-ghost text-error"
            spinner />
    </x-slot:actions>
</x-mary-card>