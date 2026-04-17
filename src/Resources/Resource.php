<?php

declare(strict_types=1);

namespace ProtoResource\Resources;

use Google\Protobuf\FieldMask;
use Google\Protobuf\Internal\Message;
use ProtoResource\Attributes\ProtoMessage;
use ProtoResource\Mask\Mask;

abstract class Resource
{
    public readonly Mask $mask;

    public function __construct(
        public readonly object|array $source,
        Mask|FieldMask|array|null $mask = null,
    ) {
        $this->mask = Mask::from($mask);
    }

    public function toProto(): Message
    {
        $message = new (static::messageClass())();

        foreach (static::fields() as $field) {
            $field->apply($this->source, $this->mask, $message);
        }

        return $message;
    }

    public static function collection(iterable $sources, FieldMask|array|null $mask = null): ResourceCollection
    {
        return new ResourceCollection($sources, static::class, $mask);
    }

    public static function messageClass(): string
    {
        $attrs = new \ReflectionClass(static::class)->getAttributes(ProtoMessage::class);

        return $attrs[0]->newInstance()->class;
    }

    abstract public static function fields(): array;
}
