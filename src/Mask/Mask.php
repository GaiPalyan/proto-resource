<?php

declare(strict_types=1);

namespace ProtoResource\Mask;

use Google\Protobuf\FieldMask;

final readonly class Mask
{
    public function __construct(private array $fields) {}

    public static function from(mixed $input): self
    {
        return match (true) {
            $input instanceof self => $input,
            $input instanceof FieldMask => self::fromPaths(iterator_to_array($input->getPaths())),
            is_array($input) && ! empty($input) => self::fromPaths($input),
            is_null($input) || is_array($input) => self::all(),
            default => throw new \InvalidArgumentException('Invalid mask input'),
        };
    }

    public static function all(): self
    {
        return new self(['*' => true]);
    }

    public function has(string $field): bool
    {
        return isset($this->fields[$field]);
    }

    public function nested(string $field): ?self
    {
        if (! isset($this->fields[$field]) || ! is_array($this->fields[$field])) {
            return null;
        }

        return new self($this->fields[$field]);
    }

    public function isAll(): bool
    {
        return array_key_exists('*', $this->fields);
    }

    private static function fromPaths(array $paths): self
    {
        $fields = [];

        foreach ($paths as $path) {
            if (trim($path) === '') {
                continue;
            }

            data_set($fields, $path, true);
        }

        return new self($fields);
    }
}
