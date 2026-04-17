<?php

declare(strict_types=1);

use Tests\Stubs\Resources\UserResource;

it('maps collection of sources to grpc messages', function () {
    $messages = UserResource::collection([
        user(['id' => 'user-1', 'full_name' => 'Alice']),
        user(['id' => 'user-2', 'full_name' => 'Bob']),
    ])->toProto();

    expect($messages)->toHaveCount(2)
        ->and($messages[0]->getId())->toBe('user-1')
        ->and($messages[1]->getId())->toBe('user-2');
});

it('collection is iterable', function () {
    $ids = [];

    foreach (UserResource::collection([user(['id' => 'user-1']), user(['id' => 'user-2'])]) as $resource) {
        $ids[] = $resource->toProto()->getId();
    }

    expect($ids)->toBe(['user-1', 'user-2']);
});

it('applies mask to all items in collection', function () {
    $messages = UserResource::collection(
        [user(['id' => 'user-1']), user(['id' => 'user-2'])],
        mask(['id'])
    )->toProto();

    expect($messages[0]->getName())->toBe('')
        ->and($messages[1]->getName())->toBe('');
});
