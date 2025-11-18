<?php

declare(strict_types=1);

use App\Models\User;

test('User deve mostrar as iniciais do usuário', function () {
    $user = new User(['name' => 'Initial Test']);

    expect($user->initials())->toBe('IT');
});
