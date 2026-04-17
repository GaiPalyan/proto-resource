<?php

declare(strict_types=1);

namespace ProtoResource\Types;

use ProtoResource\Mask\Mask;

abstract readonly class Field implements FieldDefinition
{
    public function __construct(
        /** The proto message field name this maps to. */
        protected string $name,
        /** Source key, dot-notation path, or callable to extract the value. */
        protected mixed $source,
    ) {}

    /** Returns the proto field name. */
    public function name(): string
    {
        return $this->name;
    }

    /** Returns the source key or callable used to extract the value. */
    public function source(): mixed
    {
        return $this->source;
    }

    /** Extracts the value from source data via key lookup or callable. */
    protected function resolveValue(mixed $data): mixed
    {
        return is_callable($this->source)
            ? ($this->source)($data)
            : data_get($data, $this->source);
    }

    /** Returns true if this field should be included given the current mask. */
    protected function shouldInclude(Mask $mask): bool
    {
        return $mask->isAll() || $mask->has($this->name);
    }
}
