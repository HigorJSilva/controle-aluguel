<?php

declare(strict_types=1);

use App\Enums\UserStatus;

test('UserStatus enum deve listar todos', function () {

    $array = [
        ['id' => UserStatus::ACTIVE, 'name' => 'Active'],
        ['id' => UserStatus::INACTIVE, 'name' => 'Inactive'],
        ['id' => UserStatus::SUSPENDED, 'name' => 'Suspended'],
    ];

    expect($array)->toBe(UserStatus::all());
});
