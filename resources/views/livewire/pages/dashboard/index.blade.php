<?php

use App\Enums\StatusImoveis;
use App\Enums\StatusPagamentos;
use App\Helpers\Datas;
use App\Helpers\Formatacao;
use App\Models\Imovel;
use App\Models\Locacao;
use App\Models\Pagamento;
use App\Traits\ClearsFilters;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
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
    public function imoveis(): Collection
    {
        return Imovel::query()
            ->select(['id', 'titulo', 'status', 'valor_aluguel_sugerido'])
            ->where('user_id', Auth::user()->id)
            ->with([
                'endereco:imovel_id,endereco,cidade,estado,bairro',
                'locacaoAtiva:id,imovel_id,proxima_fatura'
            ])
            ->when($this->search, function (Builder $q) {
                $q->where('titulo', 'ilike', "%$this->search%")
                    ->orWhereHas('endereco', fn(Builder $eq) => $eq->where('endereco', 'ilike', "%$this->search%"));
            })
            ->when($this->status, fn(Builder $q) => $q->where('status', $this->status))
            ->orderBy('updated_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();
    }

    #[Computed(persist: true)]
    public function pagamentos(): Collection
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
            ->orderBy('data_referencia', 'desc')
            ->limit(10)
            ->get();
    }


    #[Computed(persist: true)]
    public function stats(): array
    {
        $pagamentosRecebidos = $this->pagamentosRecebidos();
        $ocupacaoImovel = $this->ocupacaoImovel();
        $proximosRecebimentos = $this->proximosRecebimentos();
        $receitaEsperada = $this->receitaEsperada();

        return [
            'pagamentosRecebidos' => $pagamentosRecebidos,
            'ocupacaoImovel' => $ocupacaoImovel,
            'proximosRecebimentos' => $proximosRecebimentos,
            'receitaEsperada' => $receitaEsperada
        ];
    }

    private function pagamentosRecebidos(): array
    {
        $inicioMes = Carbon::now()->startOfMonth();
        $fimMes    = Carbon::now()->endOfMonth();

        $inicioMesPassado = Carbon::now()->subMonth()->startOfMonth();
        $fimMesPassado    = Carbon::now()->subMonth()->endOfMonth();

        $receitaAtual = Pagamento::doUsuario()
            ->where('status', StatusPagamentos::RECEBIDO->value)
            ->whereBetween('data_pagamento', [$inicioMes, $fimMes])
            ->sum('valor');

        $receitaAnterior = Pagamento::doUsuario()
            ->where('status', StatusPagamentos::RECEBIDO->value)
            ->whereBetween('data_pagamento', [$inicioMesPassado, $fimMesPassado])
            ->sum('valor');

        $porcentagemReceita = 0;
        if ($receitaAnterior > 0) {
            $porcentagemReceita = (($receitaAtual - $receitaAnterior) / $receitaAnterior) * 100;
        } elseif ($receitaAtual > 0) {
            $porcentagemReceita = 100;
        }
        return [
            'receitaAtual' => Formatacao::dinheiro($receitaAtual),
            'porcentagem' => $porcentagemReceita
        ];
    }

    private function ocupacaoImovel(): array
    {
        $totalImoveis = Imovel::where('user_id', Auth::user()->id)->count();

        $imoveisAlugados = Imovel::where('user_id', Auth::user()->id)
            ->where('status', StatusImoveis::ALUGADO->value)
            ->count();

        $taxaOcupacao = $totalImoveis > 0 ? ($imoveisAlugados / $totalImoveis) * 100 : 0;

        return [
            'imoveisAlugados' => $imoveisAlugados,
            'taxaOcupacao' => number_format($taxaOcupacao, 2, ',', '.'),
            'totalImoveis' => $totalImoveis,
        ];
    }

    private function proximosRecebimentos(): array
    {
        $inicioMes = Carbon::now()->startOfMonth();
        $fimMes    = Carbon::now()->endOfMonth();

        $queryPendentes = Pagamento::doUsuario()
            ->where('status', StatusPagamentos::PENDENTE->value)
            ->whereBetween('data_vencimento', [$inicioMes, $fimMes]);

        $valorPendente = $queryPendentes->sum('valor');

        $qtdPendentes = $queryPendentes->count();
        return [
            'valor' => Formatacao::dinheiro($valorPendente),
            'quantidade' => $qtdPendentes
        ];
    }

    private function receitaEsperada(): array
    {
        $inicioMes = Carbon::now()->startOfMonth();
        $fimMes    = Carbon::now()->endOfMonth();

        $receitaEsperada = Pagamento::doUsuario()
            ->whereBetween('data_referencia', [$inicioMes, $fimMes])
            ->sum('valor');

        $receitaEsperadaAnterior = $this->receitaEsperadaProximoMes();

        $trendEsperada = 0;
        if ($receitaEsperadaAnterior > 0) {
            $trendEsperada = (($receitaEsperada - $receitaEsperadaAnterior) / $receitaEsperadaAnterior) * 100;
        }
        return [
            'valor' => Formatacao::dinheiro($receitaEsperada),
            'trend' => $trendEsperada
        ];
    }

    private function receitaEsperadaProximoMes()
    {
        $hoje = Carbon::today()->startOfMonth();

        $proximoMes = Datas::addMonths($hoje, 1)->endOfMonth();
        return Locacao::query()
            ->doUsuario()
            ->where('status', true)
            ->where(function ($query) use ($proximoMes): void {
                $query->whereNull('proxima_geracao_fatura')
                    ->orWhereDate('proxima_geracao_fatura', '<', $proximoMes);
            })
            ->where(function ($query) use ($proximoMes): void {
                $query->whereNull('data_fim')
                    ->orWhereDate('data_fim', '>=', $proximoMes);
            })
            ->whereDate('data_inicio', '<=', $proximoMes)
            ->orderBy('id', 'asc')
            ->sum('valor');
    }
}; ?>

<x-pages.layout :page-title="__('messages.property_dashboard_title')" :subtitle="__('messages.property_dashboard_subtitle')">
    <x-slot:search>
    </x-slot:search>

    <x-slot:actions>
        <x-mary-button :link="route('imoveis.create')" icon="o-plus" :label="__('messages.new_property_button')" class="btn-primary" />
    </x-slot:actions>

    <x-slot:content>
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-8">
            {{-- TODO: SUBSTIRUIR STRINGS POR MESSAGES.PHP --}}
            <x-dashboard.dashboard-card
                :title="__('messages.dashboard_month_income_title')"
                :value="'R$ ' . $this->stats['pagamentosRecebidos']['receitaAtual']"
                :description="__('messages.dashboard_month_income_subtitle') . Formatacao::dataAno('F')"
                icon="o-currency-dollar"
                :trend="$this->stats['pagamentosRecebidos']['porcentagem'] .  '% vs mês anterior'"
                :trend-positive="$this->stats['pagamentosRecebidos']['porcentagem'] > 0"
                variant="success" />
            <x-dashboard.dashboard-card
                :title="__('messages.dashboard_ocupation_rate_title')"
                :value="$this->stats['ocupacaoImovel']['taxaOcupacao'] . '%'"
                :description="$this->stats['ocupacaoImovel']['imoveisAlugados'] . __('messages.dashboard_ocupation_rate_subtitle1') . $this->stats['ocupacaoImovel']['totalImoveis'] . __('messages.dashboard_ocupation_rate_subtitle2')"
                icon="o-home"
                variant="primary" />

            <x-dashboard.dashboard-card
                :title="__('messages.dashboard_next_payments_title')"
                :value="'R$ ' . $this->stats['proximosRecebimentos']['valor']"
                :description="$this->stats['proximosRecebimentos']['quantidade'] . __('messages.dashboard_next_payments_subtitle')"
                icon="o-calendar"
                variant="default" />

            <x-dashboard.dashboard-card
                :title="__('messages.dashboard_incoming_income_title') . Formatacao::dataAno('F')"
                :value="'R$ ' .  $this->stats['receitaEsperada']['valor']"
                :description="__('messages.dashboard_incoming_income_subtitle')"
                icon="o-presentation-chart-line"
                :trend="$this->stats['receitaEsperada']['trend'] . '% estimado'"
                :trend-positive="$this->stats['receitaEsperada']['trend'] >= 0"
                variant="primary" />
        </div>

        <div>
            <div class="flex justify-between mt-6">
                <div>
                    <x-mary-header :title="__('messages.rent_index_title')" class="!mb-6" />
                </div>
                <div class="flex flex-row-reverse gap-2">
                    <x-mary-button
                        :label="__('messages.rent_index_title')"
                        icon="lucide.file-signature"
                        :link="route('locacoes.index')"
                        class="rounded-md"
                        wire:navigate />
                    <x-mary-button
                        :label="__('messages.property_index_title')"
                        icon="m-building-office-2"
                        :link="route('imoveis.index')"
                        wire:navigate />
                    <x-mary-button
                        :label="__('messages.tenant_index_title')"
                        icon="lucide.users-round"
                        :link="route('inquilinos.index')"
                        wire:navigate />

                </div>
            </div>
            @if($this->imoveis->count() !== 0)
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                @foreach ($this->imoveis as $imovel)
                <x-imoveis-card :imovel="$imovel" :actions="false" />
                @endforeach
            </div>
            @else
            <div class="text-center py-16">
                <x-mary-icon name="o-building-office" class="w-16 h-16 text-gray-300 mx-auto mb-4" />
                <h3 class="text-lg font-semibold text-gray-700 mb-2">
                    {{ __('messages.no_property_found_title') }}
                </h3>
                <x-mary-button :label="__('messages.new_property_button')" icon="o-plus" :link="route('imoveis.create')" class="btn-primary" />
            </div>
            @endif
        </div>

        <div class="flex flex-col mt-6">
            <div>
                <x-mary-header class="!mb-6" :title="__('messages.payment_index_title')" />
            </div>

            <div class="gap-1">
                @if($this->pagamentos->count() !== 0)
                <div class="flex flex-col gap-4">
                    @foreach ($this->pagamentos as $pagamento)
                    <x-pagamentos.pagamento-list :pagamento="$pagamento" :show-title="false" />
                    @endforeach
                </div>

                @else
                <div class="text-center py-16">
                    <x-mary-icon name="lucide.badge-dollar-sign" class="w-16 h-16 text-gray-300 mx-auto mb-4" />
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">
                        {{ __('messages.no_payment_found_title') }}
                    </h3>
                </div>
                @endif
            </div>
        </div>

    </x-slot:content>

</x-pages.layout>