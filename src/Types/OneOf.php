<?php

declare(strict_types=1);

namespace ProtoResource\Types;

use Google\Protobuf\Internal\Message;
use ProtoResource\Mask\Mask;

final readonly class OneOf extends Field
{
    public function __construct(
        string $name,
        /** @param array<string, Field> $fields Map of field name to Field instance. */
        private array $fields,
        /** Callable that receives source data and returns the name of the field to apply. */
        callable $resolver,
    ) {
        parent::__construct($name, $resolver);
    }

    public function apply(
        mixed $data,
        Mask $mask,
        Message $message
    ): void {
        $selectedFieldName = $this->resolveValue($data);

        foreach ($this->fields as $field) {
            if ($selectedFieldName === $field->name()) {
                $field->apply($data, $mask, $message);

                return;
            }
        }
    }
}
