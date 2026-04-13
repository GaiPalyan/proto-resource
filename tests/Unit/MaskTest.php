<?php

declare(strict_types=1);

use Tests\Stubs\Resources\UserResource;

it('includes only fields listed in mask', function () {
    $message = new UserResource(user(), mask(['id']))->toGrpc();

    expect($message->getId())->toBe('user-1')
        ->and($message->getName())->toBe('');
});

it('includes all fields when no mask is given', function () {
    $message = new UserResource(user())->toGrpc();

    expect($message->getId())->toBe('user-1')
        ->and($message->getName())->toBe('John Doe');
});

it('supports nested field mask', function () {
    $message = new UserResource(user(), mask(['address.city']))->toGrpc();

    expect($message->getAddress()->getCity())->toBe('Moscow')
        ->and($message->getAddress()->getStreet())->toBe('');
});

it('excludes relation when not in mask', function () {
    $message = new UserResource(user(), mask(['id', 'name']))->toGrpc();

    expect($message->hasAddress())->toBeFalse();
});
