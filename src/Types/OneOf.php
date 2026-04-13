<?php

declare(strict_types=1);

namespace ProtoResource\Types;

use Google\Protobuf\Internal\Message;
use ProtoResource\Builder;
use ProtoResource\Mask\Mask;

final readonly class OneOf extends Field
{
    /**
     * @param  array<string, Field>  $fields  Массив возможных полей
     * @param  callable  $resolver  Функция, которая решает какое поле использовать
     */
    public function __construct(
        string $name,
        private array $fields,
        callable $resolver,
    ) {
        parent::__construct($name, $resolver);
    }

    public function apply(
        mixed $data,
        Mask $mask,
        Message $message,
        Builder $builder
    ): void {
        $selectedFieldName = $this->resolveValue($data);

        foreach ($this->fields as $field) {
            if ($selectedFieldName === $field->name()) {
                $field->apply($data, $mask, $message, $builder);

                return;
            }
        }
    }
}
