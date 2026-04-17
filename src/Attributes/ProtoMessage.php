<?php

declare(strict_types=1);

namespace ProtoResource\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class ProtoMessage
{
    public function __construct(
        /** The fully-qualified proto message class name this resource maps to. */
        public readonly string $class,
    ) {}
}
