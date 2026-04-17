<?php

declare(strict_types=1);

namespace ProtoResource\Types;

use Google\Protobuf\Internal\Message;
use ProtoResource\Mask\Mask;

final readonly class Value extends Field
{
    public function __construct(string $name, mixed $source = null)
    {
        parent::__construct($name, $source ?? $name);
    }

    public function apply(
        mixed $data,
        Mask $mask,
        Message $message
    ): void {
        if (! $this->shouldInclude($mask)) {
            return;
        }

        $value = $this->resolveValue($data);

        if (! is_null($value)) {
            $message->{'set' . ucfirst($this->name())}($value);
        }
    }
}
