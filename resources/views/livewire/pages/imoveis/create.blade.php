<?php

use App\Actions\Imovel\CreateImovel;
use App\DTO\Imovel\CreateImovelDTO;
use App\Enums\StatusImoveis;
use App\Enums\TiposImoveis;
use App\Helpers\Cidades;
use App\Services\CEP\BuscaCepStrategy;
use Illuminate\Support\Collection;
use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Auth;

new class extends Component
{
    use Toast;

    public string $titulo = '';

    public string $tipo = TiposImoveis::CASA->value;

    public string $endereco = '';

    public string $bairro = '';

    public string $cidade = '';

    public string $estado = '';

    public string $cep = '';

    public string $valorAluguelSugerido = '';

    public string $status = StatusImoveis::DISPONIVEL->value;

    public ?string $quartos = null;

    public ?string $banheiros = null;

    public ?string $area = null;

    public ?string $descricao = null;

    /** Select options */
    public array $tipos;

    public Collection $cidades;

    public array $statuses = [];

    public function rules(): array
    {
        return [
            'titulo' => ['required', 'string', 'min:3', 'max:255'],
            'tipo' => ['required', 'string'],
            'endereco' => ['required', 'string', 'min:5', 'max:255'],
            'bairro' => ['required', 'string', 'min:2', 'max:100'],
            'cidade' => ['required', 'string'],
            'estado' => ['string', 'min:2', 'max:2'],
            'cep' => ['required', 'string', 'size:9'],
            'valorAluguelSugerido' => ['required', 'string'],
            'status' => ['required', 'string'],
            'quartos' => ['nullable', 'integer', 'min:0'],
            'banheiros' => ['nullable', 'integer', 'min:0'],
            'area' => ['nullable', 'numeric', 'min:0'],
            'descricao' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function mount(): void
    {
        $this->tipos = TiposImoveis::all();
        $this->statuses = StatusImoveis::all();
        $this->searchCidades();
    }

    public function save(): void
    {
        $validateFields = $this->validate();
        $validateFields['userId'] = Auth::user()->id;
        $validateFields['cep'] = preg_replace('/\D/', '', $validateFields['cep']);
        

        $imovelDto = new CreateImovelDTO(...$validateFields);

        $imovel = CreateImovel::run($imovelDto);

        if ($imovel) {
            $this->success(__("messages.created"), timeout: 5000, redirectTo: route('imoveis.index'));
            $this->redirect('/imoveis', navigate: true);
            return;
        }

        $this->error('messages.error_on_create', timeout: 5000);

    }

    public function searchCidades(string $value = '')
    {
        $cidades = collect(Cidades::list());
        $selectedCidade = $cidades->firstWhere('id', $this->cidade);

        $retorno = $cidades
            ->filter(function ($cidade) use ($value) {
                return mb_stripos($cidade['nome'], $value) !== false;
            })
            ->take(15)
            ->values();

        if ($selectedCidade && ! $retorno->contains('id', $selectedCidade['id'])) {
            $retorno->prepend($selectedCidade);
        }

        $this->cidades = $retorno;
    }

    public function updatedCidade()
    {
        if (empty($this->cidade)) {
            $this->estado = '';

            return;
        }

        $cidade = Cidades::getById($this->cidade);
        $this->estado = $cidade ? $cidade['uf'] : '';
    }

    public function updatedCep(string $value, BuscaCepStrategy $buscaCepService)
    {
        $value = preg_replace('/\D/', '', $value);
        if (mb_strlen($value) !== 8) {
            return;
        }

        $endereco = $buscaCepService->buscar($value);

        if (empty($endereco)) {
            return;
        }

        $this->fillEndereco($endereco);
    }

    private function fillEndereco($endereco): void
    {
        $this->estado = $this->cidade = $this->bairro = $this->endereco = '';

        $logradouro = $endereco['logradouro'] ?? '';
        $complemento = $endereco['complemento'] ?? '';

        $this->bairro = $endereco['bairro'] ?? '';
        $this->cidade = $endereco['ibge'] ?? '';
        $this->endereco = $logradouro . $complemento;
        $this->searchCidades();
        $this->updatedCidade();
    }
}; ?>


<x-mary-card>
    <x-mary-card>
        <a href="{{ url()->previous() }}" wire:navigate class="inline-flex items-center text-base-content/70 hover:text-base-content text-sm mb-4">
            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            {{ __("messages.back") }}
        </a>

        <x-mary-card>
            <div class="px-6 pt-6">
                <h2 class="text-xl font-semibold text-base-content">{{ __("messages.property_create_title") }}</h2>
                <p class="text-sm text-base-content/70 mt-1">{{ __("messages.property_create_description") }}</p>
            </div>

            @if (session("success"))
            <div class="mx-6 mt-4 rounded-md bg-success/10 text-success px-4 py-2 text-sm">
                {{ session("success") }}
            </div>
            @endif

            <form wire:submit.prevent="save" class="px-6 pb-6 mt-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-mary-input :label="__('messages.input_property_name_label')" :placeholder="__('messages.input_property_name_placeholder')" wire:model="titulo" />

                    <x-mary-select
                        :label="__('messages.input_property_type_label')"
                        :placeholder="__('messages.input_property_type_placeholder')"
                        :options="$tipos"
                        option-value="id"
                        option-label="name"
                        wire:model="tipo" />

                    <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <x-mary-input
                            :label="__('messages.input_property_zip_label')"
                            :placeholder="__('messages.input_property_zip_placeholder')"
                            wire:model.blur="cep"
                            x-mask="99999-999" />
                        <x-mary-choices
                            :label="__('messages.input_property_city_label')"
                            :placeholder="__('messages.input_property_city_placeholder')"
                            wire:model.live="cidade"
                            :options="$cidades"
                            option-value="id"
                            search-function="searchCidades"
                            option-label="name"
                            no-result-text="Procure um valor..."
                            debounce="500ms"
                            clearable
                            searchable
                            single
                            required>
                            @scope("item", $cidade)
                            <x-mary-list-item :item="$cidade" sub-value="uf" value="nome" />
                            @endscope

                            @scope("selection", $cidade)
                            {{ $cidade["nome"] }}
                            @endscope
                        </x-mary-choices>
                        <x-mary-input
                            :label="__('messages.input_property_state_label')"
                            :placeholder="__('messages.input_property_state_placeholder')"
                            wire:model="estado"
                            disabled />
                    </div>

                    <div class="md:col-span-2">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="md:col-span-3">
                                <x-mary-input
                                    :label="__('messages.input_property_address_label')"
                                    :placeholder="__('messages.input_property_address_placeholder')"
                                    wire:model.lazy="endereco" />
                            </div>
                            <div class="md:col-span-1">
                                <x-mary-input
                                    :label="__('messages.input_property_neighborhood_label')"
                                    :placeholder="__('messages.input_property_neighborhood_placeholder')"
                                    wire:model="bairro" />
                            </div>
                        </div>
                    </div>

                    <div class="md:col-span-2 flex flex-col md:flex-row gap-4">
                        <div class="md:w-1/5">
                            <x-mary-input
                                :label="__('messages.input_property_rent_label')"
                                :placeholder="__('messages.input_property_rent_placeholder')"
                                wire:model="valorAluguelSugerido"
                                required
                                prefix="R$"
                                locale="pt-BR"
                                money />
                        </div>
                        <div class="md:w-1/5">
                            <x-mary-input
                                :label="__('messages.input_property_bedrooms_label')"
                                :placeholder="__('messages.input_property_bedrooms_placeholder')"
                                wire:model="quartos" />
                        </div>
                        <div class="md:w-1/5">
                            <x-mary-input
                                :label="__('messages.input_property_bathrooms_label')"
                                :placeholder="__('messages.input_property_bathrooms_placeholder')"
                                wire:model="banheiros" />
                        </div>
                        <div class="md:w-1/5">
                            <x-mary-input
                                :label="__('messages.input_property_area_label')"
                                :placeholder="__('messages.input_property_area_placeholder')"
                                wire:model="area"
                                Suffix="m²" />
                        </div>
                        <div class="md:w-1/5">
                            <x-mary-select
                                :label="__('messages.input_property_status_label')"
                                :placeholder="__('messages.input_property_status_placeholder')"
                                :options="$statuses"
                                option-value="id"
                                option-label="name"
                                wire:model="status" />
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <x-mary-textarea
                            :label="__('messages.input_property_description_label')"
                            :placeholder="__('messages.input_property_description_placeholder')"
                            wire:model="descricao"
                            rows:6 />
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <a
                        href="{{ url()->previous() }}"
                        class="inline-flex items-center justify-center rounded-lg border border-base-300 px-4 py-2 text-base-content hover:bg-base-200">
                        {{ __("messages.cancel") }}
                    </a>
                    <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-primary px-4 py-2 text-primary-content hover:bg-primary/90">
                        {{ __("messages.save") }}
                    </button>
                </div>
            </form>
        </x-mary-card>
    </x-mary-card>
</x-mary-card>