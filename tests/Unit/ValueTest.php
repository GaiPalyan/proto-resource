<?php

declare(strict_types=1);

use Tests\Stubs\Resources\UserCallbackResource;
use Tests\Stubs\Resources\UserResource;

it('maps field with the same name', function () {
    $message = new UserResource(user(['id' => 'user-1']))->toProto();

    expect($message->getId())->toBe('user-1');
});

it('maps field with a different source key', function () {
    $message = new UserResource(user(['full_name' => 'John Doe']))->toProto();

    expect($message->getName())->toBe('John Doe');
});

it('maps field via callback', function () {
    $message = new UserCallbackResource(user(['full_name' => 'john doe']))->toProto();

    expect($message->getName())->toBe('JOHN DOE');
});

it('skips null value', function () {
    $message = new UserResource(user(['id' => null]))->toProto();

    expect($message->getId())->toBe('');
});
