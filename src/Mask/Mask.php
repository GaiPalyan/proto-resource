<?php

declare(strict_types=1);

namespace ProtoResource\Mask;

use Google\Protobuf\FieldMask;

final readonly class Mask
{
    /** @param array<string, mixed> $fields */
    public function __construct(private array $fields) {}

    /** Creates a Mask from a FieldMask object, an array of dot-notation paths, or null (all fields). */
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

    /** Creates a wildcard mask that includes all fields. */
    public static function all(): self
    {
        return new self(['*' => true]);
    }

    /** Returns true if the given field is explicitly listed in the mask. */
    public function has(string $field): bool
    {
        return isset($this->fields[$field]);
    }

    /** Returns a sub-mask for a nested field, or null if the field has no nested paths. */
    public function nested(string $field): ?self
    {
        if (! isset($this->fields[$field]) || ! is_array($this->fields[$field])) {
            return null;
        }

        return new self($this->fields[$field]);
    }

    /** Returns true if the mask includes all fields (wildcard). */
    public function isAll(): bool
    {
        return array_key_exists('*', $this->fields);
    }

    /** @param array<string> $paths */
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
