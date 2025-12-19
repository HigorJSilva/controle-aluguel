<?php

use App\Models\Locacao;
use App\Helpers\Formatacao;
use Livewire\Volt\Component;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

new class extends Component {
    public Locacao $locacao;

    public array $paymentHistory = [];

    public function mount()
    {
        $this->locacao->loadMissing([
            'inquilino' => function ($query) {
                $query->select(['id', 'user_id', 'nome', 'documento', 'email', 'telefone', 'deleted_at'])->withTrashed();
            },
            'imovel' => function ($query) {
                $query->select(['id', 'user_id', 'titulo', 'tipo', 'deleted_at'])->withTrashed();
            }
        ]);

        if (!$this->locacao->pertenceUsuario()) {
            throw new NotFoundHttpException();
        }
    }
}; ?>

<div>

    <x-pages.layout>

        <x-slot:actions>
            <x-mary-button :label="__('messages.back')" icon="o-arrow-left" :link="route('locacoes.index')" wire:navigate class="btn-ghost" />
            <x-mary-button :label="__('messages.edit')" icon="o-pencil" :link="route('locacoes.edit', $locacao->id)" wire:navigate class="btn-primary" />
        </x-slot:actions>

        <x-slot:content>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <x-mary-card class="shadow border border-base-300 relative">
                        <x-mary-badge :value="$this->locacao->status ? __('messages.rent_index_active_label') :  __('messages.rent_index_inactive_label')" @class([ 'absolute top-4 right-4' , match ($locacao->status) {
                            true => 'badge-success',
                            false => 'badge-error badge-dash',
                            default => 'badge-error'
                            }])/>
                            <h2 class="text-xl font-bold mb-4">{{__('messages.rent_show_title')}}</h2>
                            <div class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <x-mary-icon name="lucide.dollar-sign" class="w-4 h-4 text-base-content/70" />
                                        <label class="text-sm text-base-content/70">{{__('messages.input_property_rent_label')}}</label>
                                        <p class="text-xl font-semibold text-primary">{{'R$ '. Formatacao::dinheiro($locacao->valor)}}</p>

                                    </div>

                                    <div>
                                        <x-mary-icon name="lucide.calendar" class="w-4 h-4 text-base-content/70" />
                                        <label class="text-sm text-base-content/70">{{__('messages.input_rent_due_day')}}</label>
                                        <p class="text-base-content ml-4">{{$locacao->dia_vencimento}}</p>
                                    </div>

                                    <div>
                                        <x-mary-icon name="lucide.calendar" class="w-4 h-4 text-base-content/70" />
                                        <label class="text-sm text-base-content/70">{{__('messages.input_payment_start_label')}}</label>
                                        <p class="text-base-content">{{Formatacao::data($locacao->data_inicio)}}</p>
                                    </div>

                                    <div>
                                        <x-mary-icon name="lucide.calendar" class="w-4 h-4 text-base-content/70" />
                                        <label class="text-sm text-base-content/70">{{__('messages.input_payment_end_label')}}</label>
                                        <p class="text-base-content mt-2">{{empty($locacao->data_fim) ? __('messages.rent_index_indefinite_span_label') : Formatacao::data($locacao->data_fim)}}</p>
                                    </div>
                                </div>
                            </div>
                    </x-mary-card>

                    {{--//TODO: implementar componente reaproveitavel de historico de pagamentos --}}
                    <x-mary-card class="shadow border border-base-300">
                        <h2 class="text-xl font-bold mb-4">{{__('messages.property_show_payment_history_title')}}</h2>
                        <div class="space-y-3">
                            @forelse ($this->paymentHistory as $payment)
                            <div class="flex items-center justify-between p-4 bg-base-200/50 rounded-lg">
                                <div>
                                    <p class="font-medium text-base-content">{{ $payment['month'] }}</p>
                                    <p class="text-sm text-base-content/70">{{__('messages.property_show_paid_in_label')}} {{ $payment['date'] }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-base-content">{{ $payment['amount'] }}</p>
                                    <x-mary-badge value="Recebido" class="badge-success badge-outline" />
                                </div>
                            </div>
                            @empty
                            <p class="text-base-content/70">{{__('messages.property_show_empty_payment_history_title')}}</p>
                            @endforelse
                        </div>
                    </x-mary-card>
                </div>

                <div class="space-y-6">
                    <x-imoveis.imoveis-card-info :imovel="$this->locacao->imovel" />
                    <x-inquilinos.inquilinos-card-info :inquilino="$this->locacao->inquilino" />
                </div>
            </div>
        </x-slot:content>
    </x-pages.layout>
</div>