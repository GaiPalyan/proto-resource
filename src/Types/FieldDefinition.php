<?php

declare(strict_types=1);

namespace ProtoResource\Types;

use Google\Protobuf\Internal\Message;
use ProtoResource\Mask\Mask;

interface FieldDefinition
{
    /** Applies the field to the proto message, writing the resolved value from source data. */
    public function apply(
        mixed $data,
        Mask $mask,
        Message $message
    ): void;
}
