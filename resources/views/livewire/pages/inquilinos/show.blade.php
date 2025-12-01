<?php

use App\Enums\TiposImoveis;
use App\Helpers\Formatacao;
use App\Models\Imovel;
use App\Models\Inquilino;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

new class extends Component {

    public Inquilino $inquilino;
    //TODO: implementar vínculo com aluguel
    public Imovel $imovel;

    public array $paymentHistory = [];


    public function mount(): void
    {
        if ($this->inquilino->user_id != Auth::user()->id) {
            throw new NotFoundHttpException();
        }


        //TODO: substiuir ao implementar pagamentos
        $this->paymentHistory = [
            ['month' => 'Novembro 2025', 'amount' => 'R$ 2.500', 'status' => 'received', 'date' => '05/11/2025'],
            ['month' => 'Outubro 2025', 'amount' => 'R$ 2.500', 'status' => 'received', 'date' => '05/10/2025'],
            ['month' => 'Setembro 2025', 'amount' => 'R$ 2.500', 'status' => 'received', 'date' => '05/09/2025'],
        ];
    }
}; ?>

<x-pages.layout>
    <x-slot:actions>
        <x-mary-button :label="__('messages.back')" icon="o-arrow-left" :link="route('inquilinos.index')" wire:navigate class="btn-ghost" />
        <x-mary-button :label="__('messages.edit')" icon="o-pencil" :link="route('inquilinos.edit', $inquilino->id)" wire:navigate class="btn-primary" />
    </x-slot:actions>

    <x-slot:content>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="lg:col-span-2 space-y-6">

                <x-mary-card class="shadow border border-base-300 relative">
                    <h2 class="text-xl font-bold mb-4">{{$inquilino->nome}}</h2>
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-mary-icon name="o-paper-airplane" class="w-4 h-4 text-base-content/70" />
                                <label class="text-sm text-base-content/70">{{__('messages.input_tenant_document_label')}}</label>
                                <p class="text-base-content">{{Formatacao::documento($inquilino->documento)}}</p>

                            </div>

                            <div>
                                <x-mary-icon name="lucide.mail" class="w-4 h-4 text-base-content/70" />
                                <label class="text-sm text-base-content/70">{{__('messages.input_tenant_email_label')}}</label>
                                <p class="text-base-content">{{$inquilino->email}}</p>
                            </div>

                            <div>
                                <x-mary-icon name="lucide.phone" class="w-4 h-4 text-base-content/70" />
                                <label class="text-sm text-base-content/70">{{__('messages.input_tenant_fone_label')}}</label>
                                <p class="text-base-content">{{Formatacao::telefone($inquilino->telefone)}}</p>
                            </div>
                        </div>

                        <div>
                            <x-mary-hr />
                        </div>

                        <div>
                            <label class="text-sm text-base-content/70">{{__('messages.input_tenant_obs_label')}}</label>
                            <p class="text-base-content mt-2">{{$inquilino->observacao}}</p>
                        </div>
                    </div>
                </x-mary-card>

                <x-mary-card class="shadow border border-base-300 mt-6">
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

                @if (!empty($this->aluguel))
                <x-mary-card class="shadow border border-base-300 relative">
                    <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
                        <x-mary-icon name="lucide.house" class="h-5 w-5" />
                        {{__('messages.property_show_current_property_label')}}
                    </h2>
                    <div class="space-y-4">
                        <div>
                            <p class="text-base-content font-medium mt-1">{{ $aluguel->imovel->titulo ?? 'N/A' }}</p>
                            <p class="text-sm text-base-content/70 mt-1">{{ (TiposImoveis::tryfrom($aluguel->imovel->tipo))->label() ?? 'N/A' }}</p>
                        </div>
                        <x-mary-hr />

                        <div class="flex flex-1 justify-between">
                            <label class="text-sm text-base-content/70 flex items-center gap-1">
                                {{__('messages.property_show_rent_label')}}
                            </label>
                            <p class="text-base-content font-medium mt-1">{{ Formatacao::dinheiro($aluguel->valor) ?? 'N/A' }}</p>
                        </div>

                        <div class="flex flex-1 text-sm">
                            <label class="text-base-content/70 flex items-center gap-1 mr-2">
                                <x-mary-icon name="o-calendar-days" class="h-3.5 w-3.5" />
                                {{__('messages.show_start_label') . ": "}}
                            </label>
                            <p class="text-base-content">{{ empty($aluguel->inquilino->data_inicio_contrato) 
                                ? __('messages.not_specified') 
                                : (Carbon\Carbon::parse($aluguel->inquilino->data_inicio_contrato))->format('d/m/Y') }}</p>
                        </div>

                        <div class="flex flex-1 text-sm">
                            <label class="text-base-content/70 flex items-center gap-1 mr-2">
                                <x-mary-icon name="o-calendar-days" class="h-3.5 w-3.5" />
                                {{__('messages.show_end_label') . ": "}}
                            </label>
                            <p class="text-base-content">{{ empty($imovel->inquilino->data_fim_contrato) 
                                ? __('messages.not_specified') 
                                : (Carbon\Carbon::parse($imovel->inquilino->data_fim_contrato))->format('d/m/Y') }}</p>
                        </div>

                        <x-mary-hr />

                        <div class="flex flex-1 justify-center bg-primary/5 border border-primary/20 rounded-lg p-4 hover:bg-primary/70 hover:text-white">
                            <a href="{{route('imoveis.show', $imovel->id)}}"> Ver detalhes do aluguel</a>
                        </div>
                    </div>
                </x-mary-card>
                @else

                <x-mary-card class="shadow border border-base-300 relative">
                    <h2 class="text-xl font-bold mb-2">{{__('messages.tenant_show_available_tenant_title')}}</h2>
                    <p class="text-base-content/70 mb-4">
                        {{__('messages.tenant_show_available_tenant_subtitle')}}.
                    </p>

                    <x-mary-button label="{{__('messages.new_rent_button')}}" class="btn-primary w-full" />
                </x-mary-card>
                @endif

            </div>

        </div>
    </x-slot:content>

</x-pages.layout>