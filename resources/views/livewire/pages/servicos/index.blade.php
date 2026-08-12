<?php

use App\Enums\TiposServico;
use App\Models\Servico;
use App\Traits\ClearsFilters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

new class extends Component {
    use Toast;
    use WithPagination;
    use ClearsFilters;

    public string $search = '';
    public bool $modal = false;
    public mixed $targetDelete = null;
    public ?string $tipoServico = null;

    #[Computed(persist: true)]
    public function servicos(): LengthAwarePaginator
    {
        $servicos = Servico::query()
            ->select(['id', 'nome', 'documento', 'tipoServico', 'endereco', 'telefone', 'observacao'])
            ->where(['user_id' => Auth::user()->id])
            ->when($this->search, function (Builder $query) {
                $query->where('nome', 'ilike', "%$this->search%")
                    ->orWhere('documento', 'ilike', "%$this->search%");
            })
            ->when($this->tipoServico, function ($query) {
                $query->where('tipoServico', $this->tipoServico);
            })
            ->orderBy('updated_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return $servicos;
    }

     #[Computed(persist: true, cache: true)]
    public function tiposServicoGroup(): array
    {
        return TiposServico::all();
    }

    public function clear(): void
    {
        unset($this->servicos);
        $this->reset('search');
        $this->success(__('messages.cleared_filters'));
    }

    public function updatedPaginators()
    {
        unset($this->servicos);
    }

    public function delete(int $id): void
    {
        try {
            Servico::where(['id' => $id, 'user_id' => Auth::user()->id])->first()->delete();
            $this->success(__('messages.deleted'));

            unset($this->Servicos);
        } catch (\Exception $e) {
            $this->error(__('messages.error_on_delete'));
        }
        $this->modal = false;
    }
}; ?>

<x-pages.layout :page-title="__('messages.services_index_title')" :subtitle="__('messages.services_index_subtitle')">

    <x-slot:search>
    </x-slot:search>

    <x-slot:actions>
        <x-mary-button :link="route('servicos.create')" icon="o-plus" :label="__('messages.new_services_button')" class="btn-primary" />
    </x-slot:actions>

    <x-slot:content>
       <!-- Filtros -->
        <div class="flex flex-col md:flex-row gap-4 mb-6">
            <div class="flex-1">
                <x-mary-input
                    :placeholder="__('messages.input_services_search')"
                    wire:model.live.debounce="search"
                    icon="o-magnifying-glass"
                    clearable />
            </div>
            <x-mary-select
                :label="__('messages.services_index_tipo_servico')"
                :placeholder="__('messages.select_services_tipo_servico')"
                :options="$this->tiposServicoGroup"
                wire:model.live="tipoServico"
                allow-empty
                class="w-full md:w-[200px]"
                inline />
        </div>

        @if($this->servicos->count() > 0)
        <div class="m-2">
            {{$this->servicos->links()}}
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($this->servicos as $servico)
            <x-servicos.servicos-card :servico="$servico" />
            @endforeach
        </div>

        <div class="mt-8">
            {{ $this->servicos->links() }}
        </div>
        @else
        <div class="text-center py-16">
            <x-mary-icon name="o-building-office" class="w-16 h-16 text-gray-300 mx-auto mb-4" />
            <h3 class="text-lg font-semibold text-gray-700 mb-2">
                {{ __('messages.no_services_found_title') }}
            </h3>
            <p class="text-gray-500 mb-6">
                {{ __('messages.no_services_found_subtitle') }}
            </p>
            <x-mary-button :label="__('messages.new_services_button')" icon="o-plus" :link="route('servicos.create')" class="btn-primary" />
        </div>
        @endif
    </x-slot:content>
    <x-mary-modal wire:model="modal" :title="__('messages.delete_services_modal_title')" :subtitle="__('messages.delete_services_modal_subtitle')" class="backdrop-blur">
        <x-slot:actions>
            <x-mary-button :label="__('messages.cancel')" class="btn-soft" @click="$wire.modal = false" />
            <x-mary-button :label="__('messages.delete')" class="btn-error" wire:click="delete($wire.targetDelete)" spinner="delete" />
        </x-slot:actions>
    </x-mary-modal>
</x-pages.layout>

@script
<script>
    $wire.on('target-delete', (event) => {
        $wire.modal = true;
        $wire.targetDelete = event.servicos;
    });
</script>
@endscript