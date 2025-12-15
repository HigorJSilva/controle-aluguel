<?php

use App\Models\Locacao;
use App\Traits\ClearsFilters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

new class extends Component {
    use Toast;
    use WithPagination;
    use ClearsFilters;

    public string $search = '';
    public ?string $status = null;
    public bool $modal = false;

    public array $statusOptions = [];

    #[Computed(persist: true)]
    public function locacoes(): LengthAwarePaginator
    {
        $locacao = Locacao::query()
            ->select(['id', 'inquilino_id', 'imovel_id', 'valor', 'data_fim', 'data_inicio', 'status'])
            ->doUsuario()
            ->with([
                'inquilino' => function ($query) {
                    return $query->select(['id', 'nome'])->withTrashed();
                },
                'imovel' => function ($query) {
                    return $query->select(['id', 'titulo'])->with(['endereco:imovel_id,endereco'])->withTrashed();
                }
            ])
            ->when($this->status, fn(Builder $q) => $q->where('status', boolval($this->status % 2)))
            ->orderBy('updated_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return $locacao;
    }

    public function mount()
    {
        $this->statusOptions = [
            ['id' => '1', 'name' => __('messages.rent_index_active_label')],
            ['id' => '2', 'name' => __('messages.rent_index_inactive_label')]
        ];
    }

    public function clear(): void
    {
        unset($this->locacoes);
        $this->reset('search');
        $this->success(__('messages.cleared_filters'));
    }

    public function updatedPaginators()
    {
        unset($this->locacoes);
    }
}; ?>

<x-pages.layout :page-title="__('messages.rent_index_title')" :subtitle="__('messages.rent_index_subtitle')">
    <x-slot:search>
    </x-slot:search>

    <x-slot:actions>
        <x-mary-button :link="route('locacoes.create')" icon="lucide.file-signature" :label="__('messages.new_rent_button')" class="btn-primary" />
    </x-slot:actions>

    <x-slot:content>
        <div class="flex flex-col md:flex-row gap-4 mb-6">
            <div class="flex-1">
                <x-mary-input
                    :placeholder="__('messages.input_rent_search')"
                    wire:model.live.debounce="search"
                    icon="o-magnifying-glass"
                    clearable />
            </div>
            <x-mary-select
                :placeholder="__('messages.select_property_status')"
                :options="$statusOptions"
                wire:model.live="status"
                allow-empty
                class="w-full md:w-[200px]" />
        </div>
    </x-slot:content>

    @if($this->locacoes->count() > 0)

    <div class="m-2">
        {{$this->locacoes->links()}}
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2  lg:grid-cols-3 gap-6">
        @foreach($this->locacoes as $locacao)
        <x-locacoes.locacao-card :locacao="$locacao" />
        @endforeach

    </div>
     <div class="mt-2">
            {{ $this->locacoes->links() }}
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

</x-pages.layout>