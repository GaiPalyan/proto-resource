<?php

declare(strict_types=1);

namespace Tests\Stubs\Resources;

use ProtoResource\Attributes\ProtoMessage;
use ProtoResource\Resources\Resource;
use ProtoResource\Types\Relation;
use ProtoResource\Types\Repeated;
use ProtoResource\Types\Value;
use Tests\Stubs\Messages\AddressMessage;
use Tests\Stubs\Messages\PostMessage;
use Tests\Stubs\Messages\UserMessage;

#[ProtoMessage(UserMessage::class)]
class UserRawResource extends Resource
{
    public static function fields(): array
    {
        return [
            new Value('id'),
            new Value('name', 'full_name'),
            new Relation('address', 'address', messageClass: AddressMessage::class),
            new Repeated('posts', 'posts', messageClass: PostMessage::class),
        ];
    }
}
