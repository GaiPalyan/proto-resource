<?php

declare(strict_types=1);

namespace ProtoResource\Mask;

final readonly class Mask
{
    public function __construct(private array $fields) {}

    public static function all(): self
    {
        return new self(['*' => true]);
    }

    public function has(string $field): bool
    {
        return isset($this->fields[$field]);
    }

    public function child(string $field): ?self
    {
        if (! isset($this->fields[$field]) || ! is_array($this->fields[$field])) {
            return null;
        }

        return new self($this->fields[$field]);
    }

    public function isEmpty(): bool
    {
        return array_key_exists('*', $this->fields);
    }
}
