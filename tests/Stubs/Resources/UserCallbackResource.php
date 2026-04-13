<?php

declare(strict_types=1);

namespace Tests\Stubs\Resources;

use ProtoResource\Attributes\ProtoMessage;
use ProtoResource\Resources\Resource;
use ProtoResource\Types\Value;
use Tests\Stubs\Messages\UserMessage;

#[ProtoMessage(UserMessage::class)]
class UserCallbackResource extends Resource
{
    public static function fields(): array
    {
        return [
            new Value('id'),
            new Value('name', fn ($u) => strtoupper($u->full_name)),
        ];
    }
}
