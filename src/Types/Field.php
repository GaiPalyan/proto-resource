<?php

declare(strict_types=1);

namespace ProtoResource\Types;

use ProtoResource\Mask\Mask;

abstract readonly class Field implements FieldDefinition
{
    public function __construct(
        protected string $name,
        protected mixed $source,
    ) {}

    public function name(): string
    {
        return $this->name;
    }

    public function source(): mixed
    {
        return $this->source;
    }

    protected function resolveValue(mixed $data): mixed
    {
        return is_callable($this->source)
            ? ($this->source)($data)
            : data_get($data, $this->source);
    }

    protected function shouldInclude(Mask $mask): bool
    {
        return $mask->isAll() || $mask->has($this->name);
    }
}
