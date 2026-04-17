<?php

declare(strict_types=1);

namespace Tests\Stubs\Resources;

use ProtoResource\Attributes\ProtoMessage;
use ProtoResource\Resources\Resource;
use ProtoResource\Types\Value;
use Tests\Stubs\Messages\PostMessage;

#[ProtoMessage(PostMessage::class)]
class PostResource extends Resource
{
    /** @return array<int, \ProtoResource\Types\FieldDefinition> */
    public static function fields(): array
    {
        return [
            new Value('id'),
            new Value('title'),
        ];
    }
}
