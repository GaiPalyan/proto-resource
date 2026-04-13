<?php

declare(strict_types=1);

namespace ProtoResource;

use Google\Protobuf\Internal\Message;
use ProtoResource\Mask\Mask;
use ProtoResource\Types\Relation;

class Builder
{
    public function build(
        mixed $source,
        array $fields,
        Mask $mask,
        Message $message
    ): Message {
        foreach ($fields as $field) {
            $field->apply($source, $mask, $message, $this);
        }

        return $message;
    }

    public function autoFill(
        Message $message,
        array|object $data,
        Mask $mask
    ): void {
        foreach ((array) $data as $key => $value) {
            if (is_null($value)) {
                continue;
            }

            if (! $mask->isEmpty() && ! $mask->has($key)) {
                continue;
            }

            if ($value instanceof Relation) {
                $this->fillRelation($message, $value->name(), $value, $mask);
                continue;
            }

            if (is_scalar($value)) {
                $this->fillScalar($message, $key, $value);
            }
        }
    }

    private function fillRelation(Message $message, string $key, Relation $relation, Mask $mask): void
    {
        $childMask = $mask->child($key) ?? Mask::all();
        $childMessage = new ($relation->messageClass())();

        $childValue = is_callable($relation->source())
            ? ($relation->source())()
            : $relation->source();

        $this->autoFill($childMessage, $childValue, $childMask);

        $message->{'set' . ucfirst($key)}($childMessage);
    }

    private function fillScalar(Message $message, string $key, mixed $value): void
    {
        $setter = 'set' . ucfirst($key);

        $message->$setter($value);
    }
}
