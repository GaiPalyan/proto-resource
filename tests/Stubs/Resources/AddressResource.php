<?php

declare(strict_types=1);

namespace Tests\Stubs\Resources;

use ProtoResource\Attributes\ProtoMessage;
use ProtoResource\Resources\Resource;
use ProtoResource\Types\Value;
use Tests\Stubs\Messages\AddressMessage;

#[ProtoMessage(AddressMessage::class)]
class AddressResource extends Resource
{
    /** @return array<int, \ProtoResource\Types\FieldDefinition> */
    public static function fields(): array
    {
        return [
            new Value('city'),
            new Value('street'),
        ];
    }
}
