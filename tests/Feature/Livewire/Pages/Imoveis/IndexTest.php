<?php

declare(strict_types=1);

use App\Models\Endereco;
use App\Models\Imovel;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

beforeEach(function () {
    $this->user = User::factory()->active()->create();
    $this->actingAs($this->user);
});

it('tela de listagem de imóvel pode ser vista', function () {
    $response = $this->get('/imoveis');

    $response->assertStatus(200);
    $response->assertSee(__('messages.property_index_title'));
});

it('componente de estatísticas pode ser visto', function () {
    $response = $this->get('/imoveis');

    $response->assertStatus(200);
    $response->assertSee(__('messages.property_index_title'));
});

it('componente de lista de imóveis pode ser vista', function () {
    $imovel = Imovel::factory()->create(['user_id' => Auth::user()->id]);
    Endereco::factory()->create(['imovel_id' => $imovel->id]);
    $response = $this->get('/imoveis');

    $response->assertSee($imovel->titulo);
});

it('componente de lista de imóveis vazia pode ser vista', function () {
    $response = $this->get('/imoveis');

    $response->assertSee(__('messages.no_property_found_title'));
});

it('componente de lista de imóveis vazia não pode ser vista', function () {
    $imovel = Imovel::factory()->create(['user_id' => Auth::user()->id]);
    Endereco::factory()->create(['imovel_id' => $imovel->id]);
    $response = $this->get('/imoveis');

    $response->assertDontSee(__('messages.no_property_found_title'));
});

it('Imovel tem um relacionamento de endereco', function () {
    $imovel = Imovel::factory()->create(['user_id' => Auth::user()->id]);
    $endereco = Endereco::factory()->create(['imovel_id' => $imovel->id]);

    expect(method_exists($imovel, 'endereco'))->toBeTrue();

    expect($imovel->endereco)->toBeInstanceOf(Endereco::class);

    expect($imovel->endereco->id)->toBe($endereco->id);
    expect($imovel->endereco->imovel_id)->toBe($imovel->id);
});

it('Endereco tem um relacionamento de imovel', function () {
    $imovel = Imovel::factory()->create(['user_id' => Auth::user()->id]);
    $endereco = Endereco::factory()->create(['imovel_id' => $imovel->id]);

    expect(method_exists($imovel, 'endereco'))->toBeTrue();

    expect($endereco->imovel)->toBeInstanceOf(Imovel::class);

    expect($endereco->imovel->id)->toBe($imovel->id);
    expect($endereco->id)->toBe($endereco->id);
});
