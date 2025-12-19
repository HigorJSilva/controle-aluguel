<?php

use App\Actions\Locacao\CreateLocacao;
use App\DTO\Locacao\CreateLocacaoDTO;
use App\Enums\StatusImoveis;
use App\Models\Imovel;
use App\Models\Inquilino;
use App\Traits\ExceptionComponent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;
use Mary\Traits\Toast;

new class extends Component {
    use Toast, ExceptionComponent;

    public int $imovelId;
    public int $inquilinoId;
    public string $valor;
    public string $diaVencimento;
    public string $dataInicio;
    public ?string $dataFim;
    public bool $status = true;
    public int $diasAntecedenciaGeracao = 30;

    public array $dias = [];
    public array $datePickerConfig =  ['altFormat' => 'd/m/Y'];

    public function mount(): void
    {
        $this->dias = collect(range(1, 31))->map(fn($dia) => [
            'id' => $dia,
            'name' => 'Dia ' . $dia,
        ])->toArray();
    }

    #[Computed(persist: true)]
    public function imoveis(): Collection
    {
        return Imovel::query()
            ->select(['id', 'titulo', 'valor_aluguel_sugerido'])
            ->where(['user_id' => Auth::user()->id, 'status' => StatusImoveis::DISPONIVEL])
            ->orderBy('updated_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    #[Computed(persist: true)]
    public function inquilinos(): Collection
    {
        return Inquilino::query()
            ->select(['id', 'nome', 'documento'])
            ->where(['user_id' => Auth::user()->id])
            ->orderBy('updated_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function rules(): array
    {
        return [
            'imovelId' => ['required'],
            'inquilinoId' => ['required'],
            'valor' => ['required'],
            'diaVencimento' => ['required'],
            'dataInicio' => ['required'],
            'dataFim' => ['nullable'],
            'status' => ['boolean'],
            'diasAntecedenciaGeracao' => ['numeric'],
        ];
    }

    public function save(): void
    {
        $validateFields = $this->validate();
        $locacaoDto = new CreateLocacaoDTO(...$validateFields);

        $locacao = CreateLocacao::run($locacaoDto);

        if (!$locacao) {
            $this->error('messages.error_on_create', timeout: 5000);
            return;
        }

        $this->success(__("messages.created"), timeout: 5000);
        $this->redirect('/locacoes', navigate: true);
    }
}; ?>

<x-mary-card>
    <x-mary-hr />
    <x-mary-card>
        <a href="{{url()->previous()}}" wire:navigate class="inline-flex items-center text-base-content/70 hover:text-base-content text-sm mb-4">
            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            {{__('messages.back')}}
        </a>
        <form wire:submit.prevent="save" class="px-6 pb-6 mt-4">
            <x-mary-card class="shadow border border-base-300">

                <div class="mb-4">
                    <h2 class='text-xl font-semibold text-base-content'>{{ __("messages.rent_create_rent_title") }}</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <x-mary-select
                        :label="__('messages.input_rent_property_label')"
                        wire:model="imovelId"
                        :options="$this->imoveis"
                        option-value="id"
                        option-label="titulo"
                        :placeholder="__('messages.input_rent_property_placeholder')" />

                    <x-mary-select
                        :label="__('messages.input_rent_tenant_label')"
                        wire:model="inquilinoId"
                        :options="$this->inquilinos"
                        option-value="id"
                        option-label="nome"
                        :placeholder="__('messages.input_rent_tenant_placeholder')" />
                </div>

            </x-mary-card>

            <x-mary-card class="shadow border border-base-300 mt-6">
                <div class="mb-4">
                    <h2 class='text-xl font-semibold text-base-content'>{{ __("messages.rent_create_contract_title") }}</h2>
                </div>
                <div>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <x-mary-input
                            :label="__('messages.input_rent_amount_label')"
                            :placeholder="__('messages.input_rent_amount_placeholder')"
                            wire:model="valor"
                            prefix="R$"
                            locale="pt-BR"
                            money />

                        <x-mary-select
                            :label="__('messages.input_rent_due_day')"
                            :placeholder="__('messages.input_tenant_due_day_placeholder')"
                            wire:model="diaVencimento"
                            :options="$this->dias"
                            option-value="id"
                            option-label="name" />

                        <x-mary-datepicker
                            :label="__('messages.input_rent_start_date_label')"
                            :placeholder="__('messages.input_rent_start_date_placeholder')"
                            icon="o-calendar"
                            wire:model="dataInicio"
                            :config="$datePickerConfig" />

                        <x-mary-datepicker
                            :label="__('messages.input_rent_end_date_label')"
                            :placeholder="__('messages.input_tenant_end_date_placeholder')"
                            icon="o-calendar"
                            wire:model="dataFim"
                            :config="$datePickerConfig" />
                    </div>
                </div>
            </x-mary-card>

            <div class="mt-6 flex items-center justify-end gap-3">
                <a
                    href="{{ url()->previous() }}"
                    class="inline-flex items-center justify-center rounded-lg border border-base-300 px-4 py-2 text-base-content hover:bg-base-200">
                    {{ __("messages.cancel") }}
                </a>
                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-primary px-4 py-2 text-primary-content hover:bg-primary/90" spinner="save">
                    {{ __("messages.save") }}
                </button>
            </div>
        </form>
    </x-mary-card>

</x-mary-card>