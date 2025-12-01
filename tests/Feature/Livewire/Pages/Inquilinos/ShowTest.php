<?php

declare(strict_types=1);

use App\Helpers\Formatacao;
use App\Models\Inquilino;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

beforeEach(function () {
    $this->user = User::factory()->active()->create();
    $this->actingAs($this->user);
    $this->inquilino = Inquilino::factory()->create(['user_id' => Auth::user()->id]);
});

it('tela de detalhes de inquilino pode ser vista', function () {
    $inquilino = $this->inquilino;
    $response = $this->get("/inquilinos/$inquilino->id");
    $response->assertStatus(200);
});

it('componente de inquilino pode ser visto', function () {
    $inquilino = $this->inquilino;
    $response = $this->get('/inquilinos/' . $inquilino->id);

    $response->assertStatus(200);
    $response->assertSee($this->inquilino->nome);
    $response->assertSee(Formatacao::documento($this->inquilino->documento));
    $response->assertSee($this->inquilino->email);
    $response->assertSee(Formatacao::telefone($this->inquilino->telefone));
});

it('componente de lista de pagamentos pode ser vista', function () {
    $inquilino = $this->inquilino;
    $response = $this->get("/inquilinos/$inquilino->id");
    $response->assertSee(__('messages.property_show_payment_history_title'));
});

it('componente de inquilino vazio pode ser vista', function () {
    $inquilino = $this->inquilino;
    $response = $this->get("/inquilinos/$inquilino->id");

    $response->assertSee(__('messages.tenant_show_available_tenant_title'));
    $response->assertSee(__('messages.tenant_show_available_tenant_subtitle'));
    $response->assertSee(__('messages.new_rent_button'));
});

it('tela de detalhes de inquilino não pode ser vista por outro usuario', function () {
    $this->user = User::factory()->active()->create();
    $this->actingAs($this->user);

    $inquilino = $this->inquilino;
    $response = $this->get("/inquilinos/$inquilino->id");

    $response->assertStatus(404);
});

it('tela de detalhes de inquilino não pode ser vista caso seja deletado', function () {
    $inquilino = $this->inquilino;

    $inquilino->delete();

    $response = $this->get("/inquilinos/$inquilino->id");

    $response->assertStatus(404);
});
