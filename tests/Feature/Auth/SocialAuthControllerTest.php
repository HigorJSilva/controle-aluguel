<?php

declare(strict_types=1);

use App\Enums\SocialiteProviders;
use App\Models\SocialAccount;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->provider = SocialiteProviders::GOOGLE;
});

test('redirect route exists and is accessible', function () {
    $response = $this->get("/oauth/{$this->provider->value}/redirect");

    $response->assertRedirect();
});

test('callback route redirects on success', function () {

    $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
    $abstractUser
        ->shouldReceive('getId')
        ->andReturn(rand())
        ->shouldReceive('getName')
        ->andReturn(Str::random(10))
        ->shouldReceive('getEmail')
        ->andReturn(Str::random(10) . '@gmail.com')
        ->shouldReceive('getAvatar')
        ->andReturn('https://en.gravatar.com/userimage');
        Socialite::shouldReceive('driver->user')->andReturn($abstractUser);
        $response = $this->get("/oauth/{$this->provider->value}/callback");

    $response->assertRedirect();
});

test('callback route redirects on login success', function () {
    $user = User::factory()->create();

    $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
    $abstractUser
        ->shouldReceive('getId')
        ->andReturn(rand())
        ->shouldReceive('getName')
        ->andReturn(Str::random(10))
        ->shouldReceive('getEmail')
        ->andReturn(Str::random(10) . '@gmail.com')
        ->shouldReceive('getAvatar')
        ->andReturn('https://en.gravatar.com/userimage');
        Socialite::shouldReceive('driver->user')->andReturn($abstractUser);
        
    SocialAccount::create(['provider_id' => $abstractUser->getId(),
        'provider_name' => $this->provider->value,
        'user_id' => $user->id,
        'avatar' => $abstractUser->getAvatar()]);
        
    $response = $this->get("/oauth/{$this->provider->value}/callback");
    $response->assertRedirect();
});


test('callback route handles errors gracefully', function () {
    $response = $this->get("/oauth/{$this->provider->value}/callback");

    $response->assertRedirect(route('login'));
    $response->assertSessionHas('error');
});

test('invalid provider redirects to login', function () {
    $response = $this->get('/oauth/invalid-provider/redirect');

    $response->assertNotFound();
});

test('invalid provider callback redirects to login', function () {
    $response = $this->get('/oauth/invalid-provider/callback');

    $response->assertNotFound();
});
