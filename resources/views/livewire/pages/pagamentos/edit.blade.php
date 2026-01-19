<?php

declare(strict_types=1);

use App\Actions\Pagamento\EditPagamento;
use App\DTO\Pagamento\EditPagamentoDTO;
use App\Enums\StatusPagamentos;
use App\Helpers\Formatacao;
use App\Models\Pagamento;
use App\Traits\ExceptionComponent;
use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

new class extends Component {
    use Toast, ExceptionComponent;

    public Pagamento $pagamento;

    public ?string $dataPagamento;
    public string $dataVencimento;
    public string $dataReferencia;
    public string $valor;
    public ?string $descricao;
    public string $status;

    public array $statusPagamentos;
    public array $datePickerConfig;
    public array $datePickerMonthConfig;


    public function mount(): void
    {
        if (!$this->pagamento->doUsuario()) {
            throw new NotFoundHttpException();
        }
        $this->statusPagamentos = StatusPagamentos::all();

        $this->datePickerConfig = ['altFormat' => 'd/m/Y', 'locale' => app()->getLocale()];

        $this->datePickerMonthConfig = [
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

        $this->preencheFormulario();
    }

    public function preencheFormulario()
    {
        $this->dataPagamento = $this->pagamento->data_pagamento;
        $this->dataVencimento = $this->pagamento->data_vencimento;
        $this->dataReferencia = $this->pagamento->data_referencia;
        $this->valor = $this->pagamento->valor;
        $this->descricao = $this->pagamento->descricao;
        $this->status = $this->pagamento->status;
    }

    public function rules(): array
    {
        return [
            'dataPagamento' => ['nullable'],
            'dataVencimento' => ['required'],
            'dataReferencia' => ['required'],
            'valor' => ['required'],
            'descricao' => ['nullable', 'max:5000'],
            'status' => ['required'],
        ];
    }

    public function save(): void
    {
        $validateFields = $this->validate();
        $pagamentoDto = new EditPagamentoDTO(...$validateFields);

        $pagamento = EditPagamento::run($pagamentoDto, $this->pagamento);

        if (!$pagamento) {
            $this->error('messages.error_on_edit', timeout: 5000);
            return;
        }

        $this->success(__("messages.editd"), timeout: 5000);
        $this->redirect('/pagamentos', navigate: true);
    }
}; ?>

<x-mary-card>
    <x-mary-card>
        <a href="{{ route('pagamentos.index') }}" wire:navigate class="inline-flex items-center text-base-content/70 hover:text-base-content text-sm mb-4">
            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            {{__('messages.back')}}
        </a>
        <form wire:submit.prevent="save" class="px-6 pb-6 mt-4">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 space-y-6">
                <div class="lg:col-span-3 space-y-6">
                    <x-mary-card class="shadow border border-base-300 relative">

                        <h2 class="text-xl font-bold mb-4">{{__('messages.payment_show_title')}}</h2>
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                                <x-mary-input
                                    :label="__('messages.input_rent_amount_label')"
                                    :placeholder="__('messages.input_rent_amount_placeholder')"
                                    wire:model="valor"
                                    prefix="R$"
                                    locale="pt-BR"
                                    money />

                                <x-mary-select
                                    :label="__('messages.payment_index_status')"
                                    :placeholder="__('messages.select_payment_status')"
                                    wire:model="status"
                                    :options="$this->statusPagamentos"
                                    option-value="id"
                                    option-label="name" />

                                <x-mary-datepicker
                                    :label="__('messages.input_payment_reference_label')"
                                    :placeholder="__('messages.input_payment_reference_placeholder')"
                                    icon="o-calendar"
                                    wire:model.live="dataReferencia"
                                    :config="$datePickerMonthConfig"
                                    disabled />

                                <x-mary-datepicker
                                    :label="__('messages.payment_index_due_date')"
                                    :placeholder="__('messages.payment_index_due_date')"
                                    icon="o-calendar"
                                    wire:model="dataVencimento"
                                    :config="$datePickerConfig" />

                                <x-mary-datepicker
                                    :label="__('messages.payment_index_payment_date')"
                                    :placeholder="__('messages.payment_index_payment_date')"
                                    icon="o-calendar"
                                    wire:model="dataPagamento"
                                    :config="$datePickerConfig" />
                            </div>
                        </div>

                        <div class="md:col-span-2 mt-4">
                            <x-mary-textarea
                                :label="__('messages.payment_show_description')"
                                :placeholder="__('messages.payment_show_description')"
                                wire:model="descricao"
                                rows:6 />
                        </div>
                    </x-mary-card>
                </div>
            </div>

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