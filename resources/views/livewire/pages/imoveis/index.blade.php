<?php

use App\Enums\StatusImoveis;
use App\Models\Imovel;
use App\Traits\ClearsFilters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;
use Livewire\Attributes\Computed;

new class extends Component {
    use Toast;
    use WithPagination;
    use ClearsFilters;

    public string $search = '';

    public ?int $status = null;

    public bool $modal = false;

    public mixed $targetDelete = null;

    #[Computed(persist: true)]
    public function imoveis(): LengthAwarePaginator
    {
        return Imovel::query()
            ->select(['id', 'titulo', 'status', 'valor_aluguel_sugerido'])
            ->where('user_id', Auth::user()->id)
            ->with([
                'endereco:imovel_id,endereco,cidade,estado,bairro',
                // 'inquilino:id,nome,imovel_id',
            ])
            ->when($this->search, function (Builder $q) {
                $q->where('titulo', 'ilike', "%$this->search%")
                    ->orWhereHas('endereco', fn(Builder $eq) => $eq->where('endereco', 'ilike', "%$this->search%"));
            })
            ->when($this->status, fn(Builder $q) => $q->where('status', $this->status))
            ->orderBy('updated_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(12);
    }

    /**
     * Calcula as estatísticas para os cards
     */
    #[Computed(persist: true)]
    public function stats(): array
    {
        $counts = Imovel::query()
            ->where('user_id', Auth::user()->id)
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('COUNT(CASE WHEN status = ? THEN 1 END) as occupied', [StatusImoveis::ALUGADO->value])
            ->selectRaw('COUNT(CASE WHEN status = ? THEN 1 END) as vacant', [StatusImoveis::DISPONIVEL->value])
            ->first();

        $total = $counts->total ?? 0;
        $occupied = $counts->occupied ?? 0;
        $vacant = $counts->vacant ?? 0;

        return [
            'total' => $total,
            'occupied' => $occupied,
            'vacant' => $vacant,
            'occupationRate' => $total > 0 ? ($occupied / $total) * 100 : 0,
        ];
    }

    #[Computed(persist: true, cache: true)]
    public function statusGroup(): array
    {
        return StatusImoveis::all();
    }

    /**
     * Limpa os filtros de busca e status
     */
    public function clear(): void
    {
        unset($this->imoveis);
        $this->reset('search', 'status');
        $this->success(__('messages.cleared_filters'));
    }

    public function updatedPaginators()
    {
        unset($this->imoveis);
    }

    /**
     * Exclui o imóvel selecionado
     */
    public function delete(int $id): void
    {
        try {
            Imovel::findOrFail($id)->delete();
            $this->success(__('messages.deleted'));

            unset($this->imoveis);
            unset($this->stats);
        } catch (\Exception $e) {
            $this->error(__('messages.error_on_delete'));
        }
        $this->modal = false;
    }

    public function updatedStatus(): void
    {
        unset($this->imoveis);
    }

    public function updatedSearch(): void
    {
        unset($this->imoveis);
    }
}; ?>


<x-pages.layout :page-title="__('messages.property_index_title')" :subtitle="__('messages.property_index_subtitle')">
    <x-slot:search>
    </x-slot:search>

    <x-slot:actions>
        <x-mary-button :link="route('imoveis.create')" icon="o-plus" :label="__('messages.new_property_button')" class="btn-primary" />
    </x-slot:actions>

    <x-slot:content>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <x-mary-stat
                :value="$this->stats['total']"
                :title="__('messages.total_property_stat_tile')"
                :description="__('messages.total_property_stat_subtile')"
                icon="o-building-office"
                class="bg-base-100 shadow rounded-lg border border-base-300 px-6 py-6" />
            <x-mary-stat
                :value="$this->stats['occupied']"
                :title="__('messages.occupied_property_stat_tile')"
                :description="__(number_format($this->stats['occupationRate'], 0) .' '. __('messages.occupied_property_stat_subtile'))"
                icon="o-lock-closed"
                class="bg-base-100 shadow rounded-lg border border-base-300 text-success px-6 py-6" />
            <x-mary-stat
                :value="$this->stats['vacant']"
                :title="__('messages.vacant_property_stat_tile')"
                :description="__('messages.vacant_property_stat_subtile')"
                icon="o-lock-open"
                class="bg-base-100 shadow rounded-lg border border-base-300 px-6 py-6" />
        </div>

        <!-- Filtros -->
        <div class="flex flex-col md:flex-row gap-4 mb-6">
            <div class="flex-1">
                <x-mary-input
                    :placeholder="__('messages.input_property_search')"
                    wire:model.live.debounce="search"
                    icon="o-magnifying-glass"
                    clearable />
            </div>
            <x-mary-select
                :placeholder="__('messages.select_property_status')"
                :options="$this->statusGroup"
                wire:model.live="status"
                allow-empty
                class="w-full md:w-[200px]" />
        </div>

        <!-- Grid de Propriedades-->
        @if ($this->imoveis->count() > 0)
        <div class="mb-8">
            {{ $this->imoveis->links() }}
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($this->imoveis as $imovel)
            <x-imoveis-card :imovel="$imovel" />
            @endforeach
        </div>

        <!-- Paginação -->
        <div class="mt-8">
            {{ $this->imoveis->links() }}
        </div>

        @else
        <!-- Estado Vazio -->
        <div class="text-center py-16">
            <x-mary-icon name="o-building-office" class="w-16 h-16 text-gray-300 mx-auto mb-4" />
            <h3 class="text-lg font-semibold text-gray-700 mb-2">
                {{ __('messages.no_property_found_title') }}
            </h3>
            <p class="text-gray-500 mb-6">
                {{ __('messages.no_property_found_subtitle') }}
            </p>
            <x-mary-button :label="__('messages.new_property_button')" icon="o-plus" :link="route('imoveis.create')" class="btn-primary" />
        </div>
        @endif
    </x-slot:content>

    <x-mary-modal wire:model="modal" :title="__('messages.delete_property_modal_title')" :subtitle="__('messages.delete_property_modal_subtitle')" class="backdrop-blur">
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