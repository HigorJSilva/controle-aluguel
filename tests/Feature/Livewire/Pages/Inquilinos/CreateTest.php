<?php

declare(strict_types=1);

use App\Actions\Inquilino\CreateInquilino;
use App\DTO\Inquilino\CreateInquilinoDTO;
use App\Enums\UserStatus;
use App\Models\User;
use App\Services\Documentos\BuscaDocumentoStrategy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->user = User::factory()->active()->create();
    $this->actingAs($this->user);

    $service = app()->make(BuscaDocumentoStrategy::class);
    $baseUrl = $service->baseUrl;

    Http::fake([
        $baseUrl . '43226679456385' => Http::response([
            'estabelecimento' => [
                'telefone1' => '32144316',
                'email' => 'acgtescnpj@correios.com.br',
            ],
        ], 200),
    ]);
});

it('tela de criar inquilino pode ser vista', function () {
    $response = $this->get('/inquilinos/create');

    $response->assertStatus(200);
    $response->assertSee(__('messages.tenant_create_title'));
});

test('inquilino não pode ser cadastrado sem dados obrigatórios', function (string $field, string $rule) {
    livewire('pages.inquilinos.create', ['user' => $this->user])
        ->set($field, '')
        ->call('save')
        ->assertHasErrors([$field => $rule]);
})->with([
    'nome' => ['nome', 'required'],
    'documento' => ['documento', 'required'],
]);

test('inquilino pode ser cadastrado', function () {
    $nome = fake()->name();
    $documento = '43226679456385';
    $email = 'exemplo@email.com';
    $telefone = '62983232323';
    $observacao = null;

    livewire('pages.inquilinos.create')
        ->set('nome', $nome)
        ->set('documento', $documento)
        ->set('email', $email)
        ->set('telefone', $telefone)
        ->set('observacao', $observacao)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('inquilinos.index'));

    $this->assertDatabaseHas('inquilinos', [
        'nome' => $nome,
        'documento' => $documento,
        'email' => $email,
        'telefone' => $telefone,
        'observacao' => $observacao,
    ]);
});

it('retornar null se a Action de criação de inquilino falhar', function () {
    $data = [
        'nome' => fake()->name(),
        'documento' => '43226679456385',
        'email' => 'exemplo@email.com',
        'telefone' => '62983232323',
        'observacao' => null,
        'userId' => 121212121212121,
    ];

    $dto = new CreateInquilinoDTO(...$data);

    $inquilino = CreateInquilino::run($dto);

    expect($inquilino)->toBeNull();
});

it('deve ser propriedade do usuário logado', function () {
    $data = [
        'nome' => fake()->name(),
        'documento' => '43226679456385',
        'email' => 'exemplo@email.com',
        'telefone' => '62983232323',
        'observacao' => null,
        'userId' => Auth::user()->id,
    ];

    $dto = new CreateInquilinoDTO(...$data);

    $inquilino = CreateInquilino::run($dto);

    expect($inquilino->usuario->id)->toBe(Auth::user()->id);
});

it('deve retornar mensagem caso usuário esteja inativo', function () {
    $this->user = User::factory()->active()->create(['status' => UserStatus::INACTIVE->value]);
    $this->actingAs($this->user);

    $data = [
        'nome' => fake()->name(),
        'documento' => '43226679456385',
        'email' => 'exemplo@email.com',
        'telefone' => '62983232323',
        'observacao' => null,
        'userId' => Auth::user()->id,
    ];

    $dto = new CreateInquilinoDTO(...$data);

    $inquilino = CreateInquilino::run($dto);

    expect($inquilino)->toBeNull();
})->throws(DomainException::class);
