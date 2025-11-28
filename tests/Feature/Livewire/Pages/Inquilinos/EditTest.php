<?php

declare(strict_types=1);

use App\Actions\Inquilino\EditInquilino;
use App\DTO\Inquilino\EditInquilinoDTO;
use App\Enums\UserStatus;
use App\Models\Inquilino;
use App\Models\User;
use App\Services\Documentos\BuscaDocumentoStrategy;
use Illuminate\Support\Facades\Http;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->user = User::factory()->active()->create();
    $this->actingAs($this->user);

    $this->inquilino = Inquilino::factory()->create(['user_id' => $this->user->id]);

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

test('tela de editar inquilino pode ser vista', function () {
    $id = $this->inquilino->id;
    $response = $this->get("/inquilinos/$id/edit");

    $response->assertStatus(200);
    $response->assertSeeText($this->inquilino->titulo);
    $response->assertSeeText(__('messages.tenant_edit_title'));
});

test('inquilino não pode ser editado sem dados obrigatórios', function (string $field, string $rule) {
    livewire('pages.inquilinos.edit', ['user' => $this->user, 'inquilino' => $this->inquilino])
        ->set($field, '')
        ->call('save')
        ->assertHasErrors([$field => $rule]);
})->with([
    'nome' => ['nome', 'required'],
    'documento' => ['documento', 'required'],
]);

test('inquilino pode ser editado', function () {

    $nome = fake()->name();
    $documento = '43226679456385';
    $email = 'exemplo@email.com';
    $telefone = '62983232323';
    $observacao = null;

    livewire('pages.inquilinos.edit', ['inquilino' => $this->inquilino])
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

test('retornar null se a Action de edição de inquilino falhar', function () {
    $data = [
        'nome' => fake()->name(),
        'documento' => '432266794563851111111111',
        'email' => 'exemplo@email.com',
        'telefone' => '62983232323',
        'observacao' => null,
    ];

    $dto = new EditInquilinoDTO(...$data);

    $inquilino = EditInquilino::run($dto, $this->inquilino);

    expect($inquilino)->toBeNull();
});

test('retornar exception se usuário no ativo tentar atualizar inqquilino', function () {
    $this->user = User::factory()->active()->create(['status' => UserStatus::SUSPENDED->value]);
    $this->actingAs($this->user);

    $this->inquilino = Inquilino::factory()->create(['user_id' => $this->user->id]);

    $data = [
        'nome' => fake()->name(),
        'documento' => '43226679456385',
        'email' => 'exemplo@email.com',
        'telefone' => '62983232323',
        'observacao' => null,
    ];

    $dto = new EditInquilinoDTO(...$data);

    $inquilino = EditInquilino::run($dto, $this->inquilino);

    expect($inquilino)->toBeNull();
})->throws(DomainException::class);

test('tela de editar inquilino não pode ser vista se inquilino pertercer a outro usuário', function () {
    $this->user = User::factory()->active()->create();
    $this->actingAs($this->user);

    $id = $this->inquilino->id;
    $response = $this->get("/inquilinos/$id/edit");

    $response->assertStatus(404);
});
