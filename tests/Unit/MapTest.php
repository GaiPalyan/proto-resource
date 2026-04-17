<?php

declare(strict_types=1);

use Tests\Stubs\Resources\UserResource;

it('maps scalar map field', function () {
    $message = new UserResource(user(['metadata' => ['key' => 'value', 'env' => 'prod']]))->toProto();

    expect($message->getMetadata()['key'])->toBe('value')
        ->and($message->getMetadata()['env'])->toBe('prod');
});

it('skips empty map field', function () {
    $message = new UserResource(user(['metadata' => []]))->toProto();

    expect($message->getMetadata())->toHaveCount(0);
});
