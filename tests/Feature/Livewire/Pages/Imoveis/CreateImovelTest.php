<?php

declare(strict_types=1);

use App\Actions\Imovel\CreateImovel;
use App\DTO\Imovel\CreateImovelDTO;
use App\Enums\StatusImoveis;
use App\Enums\TiposImoveis;
use App\Models\User;
use App\Services\CEP\ViaCepStrategy;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->user = User::factory()->active()->create();
    $this->actingAs($this->user);

    Http::fake([
        'viacep.com.br/ws/75020040/json/' => Http::response([
            'cep' => '75020-040',
            'logradouro' => 'Rua Desembargador Jaime',
            'complemento' => '',
            'unidade' => '',
            'bairro' => 'Centro',
            'localidade' => 'Anápolis',
            'uf' => 'GO',
            'estado' => 'Goiás',
            'regiao' => 'Centro-Oeste',
            'ibge' => '5201108',
            'gia' => '',
            'ddd' => '62',
            'siafi' => '9221'
        ], 200),
    ]);
});

it('tela de criar imóvel pode ser vista', function () {
    $response = $this->get('/imoveis/create');

    $response->assertStatus(200);
    $response->assertSee(__("messages.property_create_title"));
});

test('imóvel pode ser cadastrado sem dado obrigatório', function (string $field, string $rule) {
    livewire('pages.imoveis.create', ['user' => $this->user])
        ->set($field, '')
        ->call('save')
        ->assertHasErrors([$field => $rule]);
})->with('required_fields');

test('imóvel pode ser cadastrado', function () {
    livewire('pages.imoveis.create')
        ->set('titulo',  'Novo apartamento')
        ->set('tipo',  TiposImoveis::APARTAMENTO->value)
        ->set('endereco',  'Rua Desembargador Jaime')
        ->set('bairro',  'Centro')
        ->set('cidade',  '5201108')
        ->set('cep',  '75020-040')
        ->set('valorAluguelSugerido',  '1000.00')
        ->set('status', StatusImoveis::DISPONIVEL->value)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('imoveis.index'));

    $this->assertDatabaseHas('imoveis', [
        'titulo' => 'Novo apartamento',
        'tipo' => TiposImoveis::APARTAMENTO->value,
        'valor_aluguel_sugerido' => '1000.00',
        'status' => StatusImoveis::DISPONIVEL->value,
    ]);
    $this->assertDatabaseHas('enderecos', [
        'endereco' => 'Rua Desembargador Jaime',
        'bairro' => 'Centro',
        'cidade' => '5201108',
        'cep' => '75020040',
    ]);
});

test('retornar null ao falhar ao buscar CEP', function () {
    Http::fake([
        'viacep.com.br/ws/99999999/json/' => function () {
            throw new ConnectionException();
        },
    ]);

    $service = new ViaCepStrategy();
    $result = $service->buscar('99999999');

    expect($result)->toBeNull();
});


it('retornar null se a Action de criação de imovel falhar', function () {
    $data = [
        'titulo'   => 'Novo apartamento',
        'tipo'  => '1',
        'userId'  => 1,
        'valorAluguelSugerido'  => '1000.00',
        'quartos'  => 2,
        'banheiros'  => 2,
        'area'  => '80',
        'status'  => '1',
        'descricao' => '',
        'cep'  => '75020040',
        'endereco'  => 'Rua Desembargador Jaime',
        'bairro'  => 'Centro',
        'cidade'  => '5201108',
        'estado'  => 'GO',
    ];
    $dto = new CreateImovelDTO(...$data);

    $imovel = CreateImovel::run($dto);

    expect($imovel)->toBeNull();
});
