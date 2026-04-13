<?php

declare(strict_types=1);

namespace ProtoResource\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class ProtoMessage
{
    public function __construct(
        public readonly string $class,
    ) {}
}
