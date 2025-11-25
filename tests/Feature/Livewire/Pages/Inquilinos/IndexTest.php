<?php

declare(strict_types=1);

use App\Helpers\Formatacao;
use App\Models\Inquilino;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

beforeEach(function () {
    $this->user = User::factory()->active()->create();
    $this->actingAs($this->user);
});

it('tela de listagem de inquilino pode ser vista', function () {
    $response = $this->get('/inquilinos');

    $response->assertStatus(200);
    $response->assertSeeText(__('messages.tenant_index_title'));
});

it('lista de inquilinos pode ser vista', function () {
    $inquilino = Inquilino::factory()->create(['user_id' => Auth::user()->id]);

    $response = $this->get('/inquilinos');

    $response->assertStatus(200);
    $response->assertSeeText($inquilino->nome);
    $response->assertSeeText(Formatacao::documento($inquilino->documento));
    $response->assertSeeText(Formatacao::telefone($inquilino->telefone));
});

it('lista de inquilinos mostra texto ao ter propriedades não preenchidas', function () {
    Inquilino::factory()->create(['user_id' => Auth::user()->id, 'telefone' => null, 'email' => null]);

    $response = $this->get('/inquilinos');

    $response->assertStatus(200);
    $response->assertSee(__('messages.not_specified'));
});

it('lista de inquilinos mostra opção para cadastrar caso lista esteja vazia', function () {

    $response = $this->get('/inquilinos');

    $response->assertStatus(200);
    $response->assertSeeText(__('messages.no_tenant_found_title'));
    $response->assertSeeText(__('messages.no_tenant_found_subtitle'));
    $response->assertSeeText(__('messages.new_tenant_button'));
});

it('lista de inquilinos não mostra inquilinos de outros usuarios', function () {
    $inquilino = Inquilino::factory()->create(['user_id' => Auth::user()->id]);

    $this->user = User::factory()->active()->create();
    $this->actingAs($this->user);

    $response = $this->get('/inquilinos');

    $response->assertStatus(200);
    $response->assertDontSee($inquilino->nome);
});
