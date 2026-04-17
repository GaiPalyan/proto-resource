<?php

declare(strict_types=1);

use Tests\Stubs\Resources\UserResource;

it('maps repeated field via resource', function () {
    $source = user([
        'posts' => [
            (object) ['id' => 'post-1', 'title' => 'First'],
            (object) ['id' => 'post-2', 'title' => 'Second'],
        ],
    ]);

    $message = new UserResource($source)->toProto();

    expect($message->getPosts())->toHaveCount(2)
        ->and($message->getPosts()[0]->getTitle())->toBe('First')
        ->and($message->getPosts()[1]->getTitle())->toBe('Second');
});

it('skips empty repeated field', function () {
    $message = new UserResource(user(['posts' => []]))->toProto();

    expect($message->getPosts())->toHaveCount(0);
});
