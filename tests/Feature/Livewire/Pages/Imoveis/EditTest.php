<?php

declare(strict_types=1);

use App\Actions\Imovel\EditImovel;
use App\DTO\Imovel\EditImovelDTO;
use App\Enums\StatusImoveis;
use App\Enums\TiposImoveis;
use App\Models\Endereco;
use App\Models\Imovel;
use App\Models\User;
use Illuminate\Support\Facades\Http;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->user = User::factory()->active()->create();
    $this->actingAs($this->user);

    $this->imovel = Imovel::factory()->create(['user_id' => $this->user->id]);
    Endereco::factory()->create(['imovel_id' => $this->imovel->id]);

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
            'siafi' => '9221',
        ], 200),
    ]);
});

it('tela de editar imóvel pode ser vista', function () {
    $id = $this->imovel->id;
    $response = $this->get("/imoveis/$id");

    $response->assertStatus(200);
    $response->assertSeeText($this->imovel->titulo);
});

test('imóvel não pode ser editado sem dados obrigatórios', function (string $field, string $rule) {
    livewire('pages.imoveis.edit', ['user' => $this->user, 'imovel' => $this->imovel])
        ->set($field, '')
        ->call('save')
        ->assertHasErrors([$field => $rule]);
})->with('required_fields');

test('imóvel pode ser editado', function () {
    livewire('pages.imoveis.edit', ['imovel' => $this->imovel])
        ->set('titulo', 'Novo apartamento')
        ->set('tipo', TiposImoveis::APARTAMENTO->value)
        ->set('endereco', 'Rua Desembargador Jaime')
        ->set('bairro', 'Centro')
        ->set('cidade', '5201108')
        ->set('cep', '75020-040')
        ->set('valorAluguelSugerido', '1000.00')
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

it('retornar null se a Action de edição de imovel falhar', function () {
    $data = [
        'titulo' => 'Novo apartamento',
        'tipo' => '1',
        'userId' => 1,
        'valorAluguelSugerido' => '1000.00',
        'quartos' => 2,
        'banheiros' => 2,
        'area' => '80',
        'status' => '1',
        'descricao' => '',
        'cep' => '75020040',
        'endereco' => 'Rua Desembargador Jaime',
        'bairro' => 'Centro',
        'cidade' => '5201108',
        'estado' => 'GO',
    ];
    $dto = new EditImovelDTO(...$data);

    $imovel = EditImovel::run($dto, $this->imovel, null);

    expect($imovel)->toBeNull();
});

it('tela de editar imóvel não pode ser vista se imóvel pertercer a outro usuário', function () {
    $this->user = User::factory()->active()->create();
    $this->actingAs($this->user);

    $id = $this->imovel->id;
    $response = $this->get("/imoveis/$id");

    $response->assertStatus(404);
});
