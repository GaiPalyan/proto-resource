<?php

declare(strict_types=1);

use Tests\Stubs\Resources\UserResource;

it('maps nested relation via resource', function () {
    $message = new UserResource(user())->toGrpc();

    expect($message->getAddress()->getCity())->toBe('Moscow')
        ->and($message->getAddress()->getStreet())->toBe('Arbat');
});

it('skips null relation', function () {
    $message = new UserResource(user(['address' => null]))->toGrpc();

    expect($message->hasAddress())->toBeFalse();
});
