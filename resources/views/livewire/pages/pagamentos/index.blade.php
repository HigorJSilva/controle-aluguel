<?php

use App\Enums\StatusImoveis;
use App\Enums\StatusPagamentos;
use App\Helpers\Formatacao;
use App\Models\Imovel;
use App\Models\Pagamento;
use App\Traits\ClearsFilters;
use Carbon\Carbon;
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

    public ?string $status = null;
    public ?string $dataReferencia = null;

    public bool $modal = false;
    public array $sortBy = ['column' => 'titulo', 'direction' => 'asc'];

    public mixed $targetDelete = null;

    public array $datePickerConfig =  [];
    public array $headers = [
        ['key' => 'imovel', 'label' => 'Imóvel'],
        ['key' => 'inquilino', 'label' => 'tenant'],
        ['key' => 'referencia', 'label' => 'reference'],
        ['key' => 'vencimento', 'label' => 'dueDate'],
        ['key' => 'pagamento', 'label' => 'paymentDate'],
        ['key' => 'valor', 'label' => 'amount'],
        ['key' => 'status', 'label' => 'status'],
    ];

    public function mount()
    {
        $this->dataReferencia = Carbon::now()->startOfMonth();
        $this->datePickerConfig = [
            'locale' => app()->getLocale(),
            'altFormat' => 'm-Y',
            'plugins' => [
                [
                    'monthSelectPlugin' => [
                        'locale' => app()->getLocale(),
                        'dateFormat' => 'Y-m-d',
                        'altFormat' => 'F Y',
                    ]
                ]
            ]
        ];

        $this->headers = [
            ['key' => 'imovel', 'label' => __('messages.payment_index_property')],
            ['key' => 'inquilino', 'label' => __('messages.payment_index_tenant'), 'class' => 'hidden md:table-cell'],
            ['key' => 'referencia', 'label' => __('messages.payment_index_reference')],
            ['key' => 'vencimento', 'label' => __('messages.payment_index_due_date'), 'class' => 'hidden md:table-cell'],
            ['key' => 'pagamento', 'label' => __('messages.payment_index_payment_date'), 'class' => 'hidden lg:table-cell'],
            ['key' => 'valor', 'label' => __('messages.payment_index_amount'), 'class' => 'hidden lg:table-cell'],
            ['key' => 'status', 'label' => __('messages.payment_index_status')],
        ];
    }

    #[Computed(persist: true)]
    public function pagamentos(): LengthAwarePaginator
    {

        return Pagamento::query()
            ->select(['id', 'locacao_id', 'data_pagamento', 'data_vencimento', 'data_referencia', 'valor', 'status'])
            ->doUsuario()
            ->with([
                'locacao' => function ($query) {
                    $query->select(['id', 'imovel_id', 'inquilino_id'])->withTrashed()
                        ->with([
                            'inquilino' => function ($query) {
                                $query->select(['id', 'user_id', 'nome'])
                                    ->withTrashed();
                            },
                            'imovel' => function ($query) {
                                $query->select(['id', 'user_id', 'titulo'])
                                    ->with([
                                        'endereco:imovel_id,endereco'
                                    ])->withTrashed();
                            },
                        ]);
                },
            ])
            ->when($this->search, function ($query) {
                $query->whereHas('locacao', function ($q) {
                    $q->whereHas('inquilino', fn($sq) => $sq->where('nome', 'ilike', "%{$this->search}%"))
                        ->orWhereHas('imovel', fn($sq) => $sq->where('titulo', 'ilike', "%{$this->search}%"));
                });
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->where('data_referencia', $this->dataReferencia)
            ->orderBy('data_pagamento', 'desc')
            ->paginate(10);
    }

    #[Computed(persist: true, cache: true)]
    public function stats(): array
    {
        $totaisPorStatus = Pagamento::query()
            ->doUsuario()
            ->where('data_referencia', $this->dataReferencia)

            ->selectRaw('status, SUM(valor) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $totaisPorStatus['recebido'] = isset($totaisPorStatus['recebido']) ? $totaisPorStatus['recebido'] : 0;
        $totaisPorStatus['pendente'] = isset($totaisPorStatus['pendente']) ? $totaisPorStatus['pendente'] : 0;
        $totaisPorStatus['atrasado'] = isset($totaisPorStatus['atrasado']) ? $totaisPorStatus['atrasado'] : 0;

        return $totaisPorStatus;
    }

    #[Computed(persist: true, cache: true)]
    public function statusGroup(): array
    {
        return StatusPagamentos::all();
    }

    /**
     * Limpa os filtros de busca e status
     */
    public function clear(): void
    {
        unset($this->pagamentos);
        $this->reset('search', 'status');
        $this->success(__('messages.cleared_filters'));
    }

    public function updatedPaginators()
    {
        unset($this->pagamentos);
    }


    public function delete(int $id): void
    {
        try {
            Imovel::where(['id' => $id, 'user_id' => Auth::user()->id])->first()->delete();
            $this->success(__('messages.deleted'));

            unset($this->pagamentos);
            unset($this->stats);
        } catch (\Exception $e) {
            $this->error(__('messages.error_on_delete'));
        }
        $this->modal = false;
    }

    public function updatedStatus(): void
    {
        unset($this->pagamentos);
    }

    public function updatedDataReferencia(): void
    {
        unset($this->stats);
    }

    public function updatedSearch(): void
    {
        unset($this->pagamentos);
    }
}; ?>


<x-pages.layout :page-title="__('messages.payment_index_title')" :subtitle="__('messages.payment_index_subtitle')">
    <x-slot:search>
    </x-slot:search>

    <x-slot:actions>
        {{-- <x-mary-button :link="route('pagamentos.create')" icon="o-plus" :label="__('messages.new_payment_button')" class="btn-primary" /> --}}
    </x-slot:actions>

    <x-slot:content>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">

            <x-mary-stat
                :value="'R$ ' . App\Helpers\Formatacao::dinheiro($this->stats['recebido'])"
                :title="__('messages.payment_index_paid_label')"
                icon="lucide.dollar-sign"
                class="bg-base-100 shadow rounded-lg border border-base-300 px-6 py-6 alert alert-outline alert-success font-bold" />
            <x-mary-stat
                :value="'R$ ' . App\Helpers\Formatacao::dinheiro($this->stats['pendente'])"
                :title="__('messages.payment_index_pending_label')"
                icon="lucide.calendar-clock"
                class="bg-base-100 shadow rounded-lg border border-base-300 px-6 py-6 alert alert-outline alert-warning font-bold" />
            <x-mary-stat
                :value="'R$ ' .  App\Helpers\Formatacao::dinheiro($this->stats['atrasado'])"
                :title="__('messages.payment_index_late_label')"
                icon="lucide.calendar-x-2"
                class="bg-base-100 shadow rounded-lg border border-base-300 px-6 py-6 alert alert-outline alert-error font-bold" />

        </div>

        <!-- Filtros -->
        <div class="flex flex-col md:flex-row gap-4 mb-6">
            <div class="flex-1">
                <x-mary-input
                    :placeholder="__('messages.input_payment_search')"
                    wire:model.live.debounce="search"
                    icon="o-magnifying-glass"
                    clearable />
            </div>
            <x-mary-datepicker
                :label="__('messages.input_payment_reference_label')"
                :placeholder="__('messages.input_payment_reference_placeholder')"
                icon="o-calendar"
                wire:model.live="dataReferencia"
                :config="$datePickerConfig"
                inline />
            <x-mary-select
                :label="__('messages.payment_index_status')"
                :placeholder="__('messages.select_payment_status')"
                :options="$this->statusGroup"
                wire:model.live="status"
                allow-empty
                class="w-full md:w-[200px]"
                inline />
        </div>

        @if ($this->pagamentos->count() > 0)
        <x-pagamentos.pagamento-table :headers="$this->headers" :pagamentos="$this->pagamentos" />

        @else
        <!-- Estado Vazio -->
        <div class="text-center py-16">
            <x-mary-icon name="lucide.badge-dollar-sign" class="w-16 h-16 text-gray-300 mx-auto mb-4" />
            <h3 class="text-lg font-semibold text-gray-700 mb-2">
                {{ __('messages.no_payment_found_title') }}
            </h3>
            <p class="text-gray-500 mb-6">
                {{ __('messages.no_payment_found_subtitle') }}
            </p>
            {{-- <x-mary-button :label="__('messages.new_payment_button')" icon="o-plus" :link="route('pagamentos.create')" class="btn-primary" /> --}}
        </div>
        @endif
    </x-slot:content>

    <x-mary-modal wire:model="modal" :title="__('messages.delete_payment_modal_title')" :subtitle="__('messages.delete_payment_modal_subtitle')" class="backdrop-blur">
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
        $wire.targetDelete = event.pagamento;
    });
</script>
@endscript