<?php

declare(strict_types=1);

use Tests\Stubs\Resources\UserRawResource;

it('fills scalar fields via raw relation', function () {
    $message = new UserRawResource(user())->toProto();

    expect($message->getAddress()->getCity())->toBe('Moscow')
        ->and($message->getAddress()->getStreet())->toBe('Arbat');
});

it('skips null raw relation', function () {
    $message = new UserRawResource(user(['address' => null]))->toProto();

    expect($message->hasAddress())->toBeFalse();
});

it('fills repeated items via raw', function () {
    $source = user([
        'posts' => [
            (object) ['id' => 'post-1', 'title' => 'First'],
            (object) ['id' => 'post-2', 'title' => 'Second'],
        ],
    ]);

    $message = new UserRawResource($source)->toProto();

    expect($message->getPosts())->toHaveCount(2)
        ->and($message->getPosts()[0]->getId())->toBe('post-1')
        ->and($message->getPosts()[1]->getTitle())->toBe('Second');
});

it('skips empty repeated raw', function () {
    $message = new UserRawResource(user(['posts' => []]))->toProto();

    expect($message->getPosts())->toHaveCount(0);
});

it('fills raw relation from array', function () {
    $source = user(['address' => ['city' => 'SPb', 'street' => 'Nevsky']]);

    $message = new UserRawResource($source)->toProto();

    expect($message->getAddress()->getCity())->toBe('SPb')
        ->and($message->getAddress()->getStreet())->toBe('Nevsky');
});

it('fills nested object within raw relation', function () {
    $source = user([
        'address' => (object) [
            'city' => 'Moscow',
            'street' => 'Arbat',
            'district' => (object) ['name' => 'Central'],
        ],
    ]);

    $message = new UserRawResource($source)->toProto();
    expect($message->getAddress()->getCity())->toBe('Moscow')
        ->and($message->getAddress()->getDistrict()->getName())->toBe('Central');
});
