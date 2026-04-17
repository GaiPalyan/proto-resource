<?php

declare(strict_types=1);

use Tests\Stubs\Resources\UserResource;

it('includes only fields listed in mask', function () {
    $message = new UserResource(user(), mask(['id']))->toProto();

    expect($message->getId())->toBe('user-1')
        ->and($message->getName())->toBe('');
});

it('includes all fields when no mask is given', function () {
    $message = new UserResource(user())->toProto();

    expect($message->getId())->toBe('user-1')
        ->and($message->getName())->toBe('John Doe');
});

it('supports nested field mask', function () {
    $message = new UserResource(user(), mask(['address.city']))->toProto();

    expect($message->getAddress()->getCity())->toBe('Moscow')
        ->and($message->getAddress()->getStreet())->toBe('');
});

it('excludes relation when not in mask', function () {
    $message = new UserResource(user(), mask(['id', 'name']))->toProto();

    expect($message->hasAddress())->toBeFalse();
});

it('accepts array instead of FieldMask', function () {
    $message = new UserResource(user(), ['id'])->toProto();

    expect($message->getId())->toBe('user-1')
        ->and($message->getName())->toBe('');
});

it('supports nested field mask passed as array', function () {
    $message = new UserResource(user(), ['address.city'])->toProto();

    expect($message->getAddress()->getCity())->toBe('Moscow')
        ->and($message->getAddress()->getStreet())->toBe('');
});

it('excludes relation when not in array mask', function () {
    $message = new UserResource(user(), ['id', 'name'])->toProto();

    expect($message->hasAddress())->toBeFalse();
});
