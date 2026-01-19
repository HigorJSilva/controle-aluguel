<?php

use App\Enums\StatusPagamentos;
use App\Models\Pagamento;
use App\Helpers\Formatacao;
use Carbon\Carbon;
use Livewire\Volt\Component;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

new class extends Component {
    public Pagamento $pagamento;

    public array $paymentHistory = [];

    public function mount()
    {
        $this->pagamento->loadMissing([
            'locacao' => function ($query) {
                $query->withTrashed();
            },
            'locacao.inquilino' => function ($query) {
                $query->select(['id', 'user_id', 'nome', 'documento', 'email', 'telefone', 'deleted_at'])
                    ->withTrashed();
            },
            'locacao.imovel' => function ($query) {
                $query->select(['id', 'user_id', 'titulo', 'tipo', 'deleted_at'])
                    ->withTrashed();
            },
        ]);

        if (!$this->pagamento->locacao->pertenceUsuario()) {
            throw new NotFoundHttpException();
        }
    }

    public function marcarComoRecebido(): void
    {
        if (!$this->pagamento->locacao->pertenceUsuario()) {
            throw new NotFoundHttpException();
        }

        $this->pagamento->status = StatusPagamentos::RECEBIDO->value;
        $this->pagamento->data_pagamento = Carbon::now()->format('Y-m-d');
        $this->pagamento->save();
    }
}; ?>

<div>

    <x-pages.layout>

        <x-slot:actions>
            <x-mary-button :label="__('messages.back')" icon="o-arrow-left" :link="route('pagamentos.index')" wire:navigate class="btn-ghost" />
            <x-mary-button :label="__('messages.edit')" icon="o-pencil" :link="route('pagamentos.edit', $pagamento->id)" wire:navigate class="btn-primary" />
        </x-slot:actions>

        <x-slot:content>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <x-mary-card class="shadow border border-base-300 relative">

                        <h2 class="text-xl font-bold mb-4">{{__('messages.payment_show_title')}}</h2>
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-mary-icon name="lucide.dollar-sign" class="w-4 h-4 text-base-content/70" />
                                    <label class="text-sm text-base-content/70">{{__('messages.input_property_rent_label')}}</label>
                                    <p class="text-xl font-semibold text-primary">{{'R$ '. Formatacao::dinheiro($pagamento->valor)}}</p>

                                </div>

                                <div>
                                    <x-mary-icon name="lucide.badge-info" class="w-4 h-4 text-base-content/70" />
                                    <label class="text-sm text-base-content/70">{{__('messages.payment_index_status')}}</label>
                                    <x-mary-badge :value="App\Enums\StatusPagamentos::tryFrom($pagamento['status'])?->label()" @class([ 'flex mt-1' , App\Enums\StatusPagamentos::getCssClass($pagamento['status'])]) />
                                </div>

                                <div>
                                    <x-mary-icon name="lucide.calendar" class="w-4 h-4 text-base-content/70" />
                                    <label class="text-sm text-base-content/70">{{__('messages.payment_index_reference')}}</label>
                                    <p class="text-base-content ml-4">{{ Formatacao::dataMesAno($pagamento->data_referencia)}}</p>
                                </div>

                                <div>
                                    <x-mary-icon name="lucide.calendar" class="w-4 h-4 text-base-content/70" />
                                    <label class="text-sm text-base-content/70">{{__('messages.payment_index_due_date')}}</label>
                                    <p class="text-base-content">{{Formatacao::data($pagamento->data_vencimento)}}</p>
                                </div>

                                <div>
                                    <x-mary-icon name="lucide.calendar" class="w-4 h-4 text-base-content/70" />
                                    <label class="text-sm text-base-content/70">{{__('messages.payment_index_payment_date')}}</label>
                                    <p class="text-base-content mt-2">{{empty($pagamento->data_pagamento) ? '-' : Formatacao::data($pagamento->data_pagamento)}}</p>
                                </div>
                            </div>
                        </div>

                        <div class="w-full mt-4">
                            <x-mary-icon name="lucide.scroll-text" class="w-4 h-4 text-base-content/70" />
                            <label class="text-sm text-base-content/70">{{__('messages.payment_show_description')}}</label>
                            <p class="text-base-content mt-2">{{$pagamento->descricao}}</p>
                        </div>

                        @if($pagamento->status !== StatusPagamentos::RECEBIDO->value)
                        <x-slot:actions separator>
                            <x-mary-button label="Marcar como Pago" class="btn bg-green-700 w-full text-white rounded-xl" wire:click="marcarComoRecebido"/>
                        </x-slot:actions>
                        @endif
                    </x-mary-card>

                </div>

                <div class="space-y-6">
                    <x-imoveis.imoveis-card-info :imovel="$this->pagamento->locacao->imovel" />
                    <x-inquilinos.inquilinos-card-info :inquilino="$this->pagamento->locacao->inquilino" />
                    <x-locacoes.locacao-card-info :locacao="$this->pagamento->locacao" />
                </div>
            </div>
        </x-slot:content>
    </x-pages.layout>
</div>