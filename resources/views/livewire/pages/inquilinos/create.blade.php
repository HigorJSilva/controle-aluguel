<?php

use App\Actions\Inquilino\CreateInquilino;
use App\DTO\Inquilino\CreateInquilinoDTO;
use App\Helpers\Formatacao;
use App\Rules\CnpjCpfRule;
use App\Services\Documentos\BuscaDocumentoStrategy;
use App\Traits\ExceptionComponent;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use Mary\Traits\Toast;

new class extends Component
{
    use Toast, ExceptionComponent;

    public string $nome = '';
    public string $documento = '';
    public ?string $email = null;
    public ?string $telefone = null;
    public ?string $observacao = null;

    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'min:3', 'max:255'],
            'documento' => ['required', 'string', new CnpjCpfRule],
            'telefone' => ['nullable', 'string'],
            'email' => ['nullable', 'email'],
            'observacao' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function save(): void
    {
        $validateFields = $this->validate();

        $ok = Formatacao::retornarDigitos(['documento' => $validateFields['documento'], 'telefone' => $validateFields['telefone']]);

        $payload = array_merge($validateFields, $ok);
        $payload['userId'] = Auth::user()->id;

        $inquilinoDto = new CreateInquilinoDTO(...$payload);

        $inquilino = CreateInquilino::run($inquilinoDto);

        if (!$inquilino) {
            $this->error('messages.error_on_create', timeout: 5000);
            return;
        }

        $this->success(__("messages.created"), timeout: 5000);
        $this->redirect('/inquilinos', navigate: true);
    }

    public function updatedDocumento(string $documento, BuscaDocumentoStrategy $buscaDocumento): void
    {
        $documento = Formatacao::retornarDigitos($documento);

        $response = $buscaDocumento->buscar($documento);

        if (empty($response)) {
            return;
        }

        $this->telefone = $response["estabelecimento"]["ddd1"] . $response["estabelecimento"]["telefone1"];
        $this->email = $response["estabelecimento"]["email"];
    }
};

?>

<x-mary-card>
    <x-mary-hr />
    <x-mary-card>
        <a href="{{url()->previous()}}" wire:navigate class="inline-flex items-center text-base-content/70 hover:text-base-content text-sm mb-4">
            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            {{__('messages.back')}}
        </a>

        <x-mary-card class="shadow border border-base-300">
            <div class="px-6 pt-6">
                <h2 class='text-xl font-semibold text-base-content'>{{ __("messages.tenant_create_title") }}</h2>
            </div>

            <form wire:submit.prevent="save" class="px-6 pb-6 mt-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-mary-input :label="__('messages.input_tenant_name_label')" :placeholder="__('messages.input_tenant_name_placeholder')" wire:model="nome" />
                    <x-mary-input :label="__('messages.input_tenant_document_label')" :placeholder="__('messages.input_tenant_document_placeholder')" wire:model.blur="documento" x-mask:dynamic="
                                                        $input.replace(/\D/, '').length !== 13
                                                        ? '99.999.999/9999-99'
                                                        : '999.999.999-99'" />
                    <x-mary-input :label="__('messages.input_tenant_email_label')" :placeholder="__('messages.input_tenant_email_placeholder')" wire:model="email" />
                    <x-mary-input :label="__('messages.input_tenant_fone_label')" :placeholder="__('messages.input_tenant_fone_placeholder')" wire:model.fill="telefone"
                        x-mask:dynamic="
                            $input[5] == 9
                                ? '(99) 99999-9999' 
                                : '(99) 9999-9999'" />

                    <div class=" md:col-span-2">
                        <x-mary-textarea :label="__('messages.input_tenant_obs_label')" :placeholder="__('messages.input_tenant_obs_placeholder')" wire:model="observacao" :rows=6 />
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
</x-mary-card>