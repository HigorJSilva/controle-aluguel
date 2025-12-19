<?php

declare(strict_types=1);

use App\Enums\StatusImoveis;
use App\Enums\TiposImoveis;
use App\Models\Endereco;
use App\Models\Imovel;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

beforeEach(function () {
    $this->user = User::factory()->active()->create();
    $this->actingAs($this->user);
});

it('tela de detalhes de imóvel pode ser vista', function () {
    $imovel = Imovel::factory()->create(['user_id' => Auth::user()->id]);
    Endereco::factory()->create(['imovel_id' => $imovel->id]);

    $response = $this->get("/imoveis/$imovel->id");
    $response->assertStatus(200);
    $response->assertSee(__('messages.property_show_title'));
});

it('componente de imovel pode ser visto', function () {
    $imovel = Imovel::factory()->create(['user_id' => Auth::user()->id]);
    Endereco::factory()->create(['imovel_id' => $imovel->id]);

    $response = $this->get('/imoveis/' . $imovel->id);

    $response->assertStatus(200);
    $response->assertSee($imovel->titulo);
    $response->assertSee(TiposImoveis::tryFrom((string) ($imovel->tipo))->label());
    $response->assertSee($imovel->user_id);
    $response->assertSee(number_format($imovel->valor_aluguel_sugerido ?? 0, 2, ',', '.'));
    $response->assertSee($imovel->quartos);
    $response->assertSee($imovel->banheiros);
    $response->assertSee($imovel->area . 'm²');
    $response->assertSee(StatusImoveis::tryFrom((string) ($imovel->status))->label());
    $response->assertSee($imovel->descricao);
});

it('componente de lista de pagamentos pode ser vista', function () {
    $imovel = Imovel::factory()->create(['user_id' => Auth::user()->id]);
    Endereco::factory()->create(['imovel_id' => $imovel->id]);

    $response = $this->get("/imoveis/$imovel->id");

    $response->assertSee(__('messages.property_show_payment_history_title'));
});

it('componente de inquilino vazio pode ser vista', function () {
    $imovel = Imovel::factory()->create(['user_id' => Auth::user()->id]);
    Endereco::factory()->create(['imovel_id' => $imovel->id]);

    $response = $this->get("/imoveis/$imovel->id");

    $response->assertSee(__('messages.property_show_available_property_title'));
    $response->assertSee(__('messages.property_show_available_property_subtitle'));
    $response->assertSee(__('messages.new_rent_button'));
});

it('tela de detalhes de imóvel não pode ser vista por outro usuario', function () {
    $imovel = Imovel::factory()->create(['user_id' => Auth::user()->id]);
    Endereco::factory()->create(['imovel_id' => $imovel->id]);

    $this->user = User::factory()->active()->create();
    $this->actingAs($this->user);

    $response = $this->get("/imoveis/$imovel->id");

    $response->assertStatus(404);
});

it('tela de detalhes de imóvel não pode ser vista caso seja deletado', function () {
    $imovel = Imovel::factory()->create(['user_id' => Auth::user()->id]);
    Endereco::factory()->create(['imovel_id' => $imovel->id]);

    $imovel->delete();

    $response = $this->get("/imoveis/$imovel->id");

    $response->assertStatus(404);
});
