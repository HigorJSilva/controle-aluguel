<?php

use App\Models\Inquilino;
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

    #[Computed(persist: true)]
    public function inquilinos(): LengthAwarePaginator
    {
        $ok = Inquilino::query()
            ->select(['id', 'nome', 'documento', 'email', 'telefone', 'observacao'])
            ->where(['user_id' => Auth::user()->id])
            ->when($this->search, function (Builder $query) {
                $query->where('nome', 'ilike', "%$this->search%")
                    ->orWhere('documento', 'ilike', "%$this->search%");
            })
            ->orderBy('updated_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return $ok;
    }

    public function clear(): void
    {
        unset($this->inquilinos);
        $this->reset('search');
        $this->success(__('messages.cleared_filters'));
    }

    public function updatedPaginators()
    {
        unset($this->inquilinos);
    }
}; ?>

<x-pages.layout :page-title="__('messages.tenant_index_title')" :subtitle="__('messages.tenant_index_subtitle')">

    <x-slot:search>
    </x-slot:search>

    <x-slot:actions>
        <x-mary-button :link="route('inquilinos.create')" icon="o-plus" :label="__('messages.new_tenant_button')" class="btn-primary" />
    </x-slot:actions>

    <x-slot:content>
        <div class="flex flex-col md:flex-row gap-4 mb-6">
            <div class="flex-1">
                <x-mary-input
                    :placeholder="__('messages.input_tenant_search')"
                    wire:model.live.debounce="search"
                    icon="o-magnifying-glass"
                    clearable />
            </div>
        </div>

        @if($this->inquilinos->count() > 0)
        <div>
            {{$this->inquilinos->links()}}
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($this->inquilinos as $inquilino)
            <x-inquilinos.inquilinos-card :inquilino="$inquilino" />
            @endforeach
        </div>

        <div class="mt-8">
            {{ $this->inquilinos->links() }}
        </div>
        @else
        <div class="text-center py-16">
            <x-mary-icon name="o-building-office" class="w-16 h-16 text-gray-300 mx-auto mb-4" />
            <h3 class="text-lg font-semibold text-gray-700 mb-2">
                {{ __('messages.no_tenant_found_title') }}
            </h3>
            <p class="text-gray-500 mb-6">
                {{ __('messages.no_tenant_found_subtitle') }}
            </p>
            <x-mary-button :label="__('messages.new_tenant_button')" icon="o-plus" :link="route('inquilinos.create')" class="btn-primary" />
        </div>
        @endif
    </x-slot:content>
    <x-mary-modal wire:model="modal" :title="__('messages.delete_tenant_modal_title')" :subtitle="__('messages.delete_tenant_modal_subtitle')" class="backdrop-blur">
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
        $wire.targetDelete = event.imoveis;
    });
</script>
@endscript