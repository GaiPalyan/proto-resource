<?php

declare(strict_types=1);

namespace ProtoResource\Types;

use Google\Protobuf\Internal\Message;
use ProtoResource\Builder;
use ProtoResource\Mask\Mask;

interface FieldDefinition
{
    public function apply(
        mixed $data,
        Mask $mask,
        Message $message,
        Builder $builder
    ): void;
}
