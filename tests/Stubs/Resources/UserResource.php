<?php

declare(strict_types=1);

namespace Tests\Stubs\Resources;

use ProtoResource\Attributes\ProtoMessage;
use ProtoResource\Resources\Resource;
use ProtoResource\Types\Map;
use ProtoResource\Types\Relation;
use ProtoResource\Types\Repeated;
use ProtoResource\Types\Value;
use Tests\Stubs\Messages\UserMessage;

#[ProtoMessage(UserMessage::class)]
class UserResource extends Resource
{
    /** @return array<int, \ProtoResource\Types\FieldDefinition> */
    public static function fields(): array
    {
        return [
            new Value('id'),
            new Value('name', 'full_name'),
            new Relation('address', 'address', AddressResource::class),
            new Repeated('posts', 'posts', PostResource::class),
            new Map('metadata'),
        ];
    }
}
