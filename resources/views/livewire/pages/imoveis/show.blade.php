<?php

use App\Models\Imovel;
use App\Helpers\Cidades;
use App\Enums\TiposImoveis;
use App\Enums\StatusImoveis;
use App\Models\Locacao;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

new class extends Component {

    public Imovel $imovel;

    public ?Locacao $locacao;


    public array $paymentHistory = [];

    public function mount(Imovel $imovel): void
    {
        if ($this->imovel->user_id != Auth::user()->id) {
            throw new NotFoundHttpException();
        }

        $this->locacao = $this->imovel->locacaoAtiva;

        if (!empty($this->locacao)) {
            $this->locacao->load(['inquilino' => function ($query) {
                $query->select(['id', 'nome', 'documento', 'email', 'telefone']);
            }]);
        }

        $this->imovel = $imovel->loadMissing(['endereco']);

        // Mock data para o histórico de pagamentos
        $this->paymentHistory = [
            ['month' => 'Novembro 2025', 'amount' => 'R$ 2.500', 'status' => 'received', 'date' => '05/11/2025'],
            ['month' => 'Outubro 2025', 'amount' => 'R$ 2.500', 'status' => 'received', 'date' => '05/10/2025'],
            ['month' => 'Setembro 2025', 'amount' => 'R$ 2.500', 'status' => 'received', 'date' => '05/09/2025'],
        ];
    }
}; ?>

<x-pages.layout :page-title="$imovel->titulo">

    {{-- Botões de Ação no Header --}}
    <x-slot:actions>
        <x-mary-button :label="__('messages.back')" icon="o-arrow-left" :link="route('imoveis.index')" wire:navigate class="btn-ghost" />
        <x-mary-button :label="__('messages.edit')" icon="o-pencil" :link="route('imoveis.edit', $imovel->id)" wire:navigate class="btn-primary" />
    </x-slot:actions>

    {{-- Conteúdo Principal da Página --}}
    <x-slot:content>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Coluna Principal (Esquerda) --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- 1. Card de Informações da Propriedade --}}
                <x-mary-card class="shadow border border-base-300 relative">
                    <x-mary-badge :value="App\Enums\StatusImoveis::tryFrom($imovel->status)?->label() ?? $imovel->status" @class([ 'absolute top-4 right-4' , StatusImoveis::getCssClass($imovel->status)]) />

                        <h2 class="text-xl font-bold mb-4">{{__('messages.property_show_title')}}</h2>

                        <div class="space-y-4">
                            {{-- Endereço e Detalhes --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm text-base-content/70">{{__('messages.input_property_address_label')}}</label>
                                    <div class="flex items-center gap-2 mt-1">
                                        <x-mary-icon name="o-map-pin" class="h-4 w-4 text-primary" />
                                        <p class="text-base-content">{{ $imovel->endereco->endereco ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div>
                                    <label class="text-sm text-base-content/70">{{__('messages.input_property_city_label') . '/' . __('messages.input_property_state_label')}}</label>
                                    <p class="text-base-content mt-1">{{ (Cidades::getByid($imovel->endereco->cidade))['nome']  ?? 'N/A' }}, {{ $imovel->endereco->estado ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <label class="text-sm text-base-content/70">{{__('messages.input_property_zip_label')}}</label>
                                    <p class="text-base-content mt-1">{{ substr_replace($imovel->endereco->cep, '-', 5, 0)  ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <label class="text-sm text-base-content/70">{{__('messages.input_property_type_label')}}</label>
                                    <p class="text-base-content mt-1">{{ TiposImoveis::tryfrom($imovel->tipo)->label() ?? 'N/A' }}</p>
                                </div>
                            </div>

                            <x-mary-hr />

                            {{-- Quartos, Banheiros, Área --}}
                            <div class="grid grid-cols-3 gap-4">
                                <div class="flex items-center gap-2">
                                    <x-mary-icon name="lucide.bed-double" class="h-5 w-5 text-primary" />
                                    <div>
                                        <p class="text-sm text-base-content/70">{{__('messages.input_property_bedrooms_label')}}</p>
                                        <p class="font-semibold text-base-content">{{ $imovel->quartos ?? 0 }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <x-mary-icon name="lucide.bath" class="h-5 w-5 text-primary" />
                                    <div>
                                        <p class="text-sm text-base-content/70">{{__('messages.input_property_bathrooms_label')}}</p>
                                        <p class="font-semibold text-base-content">{{ $imovel->banheiros ?? 0 }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <x-mary-icon name="o-arrows-pointing-out" class="h-5 w-5 text-primary" />
                                    <div>
                                        <p class="text-sm text-base-content/70">{{__('messages.input_property_area_label')}}</p>
                                        <p class="font-semibold text-base-content">{{ $imovel->area ?? 0 }}m²</p>
                                    </div>
                                </div>
                            </div>

                            <x-mary-hr />

                            {{-- Descrição --}}
                            <div>
                                <label class="text-sm text-base-content/70">{{__('messages.input_property_description_label')}}</label>
                                <p class="text-base-content mt-1">{{ $imovel->descricao ?? '' }}</p>
                            </div>

                            <x-mary-hr />

                            {{-- Valor do Aluguel --}}
                            <div class="flex items-center gap-2">
                                <x-mary-icon name="o-currency-dollar" class="h-6 w-6 text-primary" />
                                <div>
                                    <label class="text-sm text-base-content/70">{{__('messages.input_property_rent_label')}}</label>
                                    <p class="text-2xl font-bold text-base-content">R$ {{ number_format($imovel->valor_aluguel_sugerido ?? 0, 2, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                </x-mary-card>

                {{-- 2. Card de Histórico de Pagamentos --}}
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

            {{-- Coluna da Sidebar (Direita) --}}
            <div class="space-y-6">

                {{-- 3. Card do Inquilino (se estiver ocupado) --}}
                @if ($imovel->status == StatusImoveis::ALUGADO->value && !empty($this->locacao->inquilino))
                <x-mary-card class="shadow border border-base-300 relative">
                    <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
                        <x-mary-icon name="o-user" class="h-5 w-5" />
                        {{__('messages.property_show_current_tenant_label')}}
                    </h2>
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm text-base-content/70">{{__('messages.input_tenant_name_label')}}</label>
                            <p class="text-base-content font-medium mt-1">{{ $this->locacao->inquilino->nome ?? __('messages.not_specified') }}</p>
                        </div>

                        <div>
                            <label class="text-sm text-base-content/70">{{__('messages.input_tenant_document_label')}}</label>
                            <p class="text-base-content font-medium mt-1">{{ App\Helpers\Formatacao::documento($this->locacao->inquilino->documento) }}</p>
                        </div>

                        <div>
                            <label class="text-sm text-base-content/70">{{__('messages.input_tenant_email_label')}}</label>
                            <p class="text-base-content mt-1">{{ empty($this->locacao->inquilino->email)  ? __('messages.not_specified') : $this->locacao->inquilino->email}}</p>
                        </div>
                        <div>
                            <label class="text-sm text-base-content/70">{{__('messages.input_tenant_fone_label')}}</label>
                            <p class="text-base-content mt-1">{{ empty($this->locacao->inquilino->telefone) ? __('messages.not_specified') : App\Helpers\Formatacao::telefone($this->locacao->inquilino->telefone)}}</p>
                        </div>

                        <x-mary-hr />

                        <div>
                            <label class="text-sm text-base-content/70 flex items-center gap-1">
                                <x-mary-icon name="o-calendar-days" class="h-3.5 w-3.5" />
                                {{__('messages.input_payment_start_label')}}
                            </label>
                            <p class="text-base-content font-medium mt-1">{{ App\Helpers\Formatacao::data($this->locacao->data_inicio)}}</p>
                        </div>

                        <div>
                            <label class="text-sm text-base-content/70 flex items-center gap-1">
                                <x-mary-icon name="o-calendar-days" class="h-3.5 w-3.5" />
                                {{__('messages.input_payment_end_label')}}
                            </label>
                            <p class="text-base-content font-medium mt-1">{{ empty($this->locacao->data_fim) ? __('messages.rent_index_indefinite_span_label') : App\Helpers\Formatacao::data( $this->locacao->data_fim) }}</p>
                        </div>

                        <x-mary-hr />

                        <div class="bg-primary/5 border border-primary/20 rounded-lg p-4">
                            <label class="text-sm text-base-content/70"> {{__('messages.input_payment_next_label')}}</label>
                            <p class="text-lg font-bold text-primary mt-1">{{ empty($this->locacao->proxima_fatura) ? __('messages.not_specified') : App\Helpers\Formatacao::data($this->locacao->proxima_fatura)}}</p>
                        </div>
                    </div>
                </x-mary-card>

                {{-- 4. Card de Propriedade Vaga (se não estiver ocupado) --}}
                @else
                <x-mary-card class="shadow border border-base-300 relative">
                    <h2 class="text-xl font-bold mb-2">{{__('messages.property_show_available_property_title')}}</h2>
                    <p class="text-base-content/70 mb-4">
                        {{__('messages.property_show_available_property_subtitle')}}.
                    </p>
                    {{-- Este link/botão vai levar para uma página de "novo contrato" --}}

                    <x-mary-button label="{{__('messages.new_rent_button')}}" class="btn-primary w-full" :link="route('locacoes.create')" />
                </x-mary-card>
                @endif
            </div>

        </div>
    </x-slot:content>
</x-pages.layout>