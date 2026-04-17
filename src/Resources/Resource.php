<?php

declare(strict_types=1);

namespace ProtoResource\Resources;

use Google\Protobuf\FieldMask;
use Google\Protobuf\Internal\Message;
use ProtoResource\Attributes\ProtoMessage;
use ProtoResource\Mask\Mask;

abstract class Resource
{
    /** The active field mask applied during serialization. */
    public readonly Mask $mask;

    /**
     * @param object|array<string, mixed> $source
     * @param array<string>|null $mask
     */
    public function __construct(
        /** The source data object or array to map from. */
        public readonly object|array $source,
        Mask|FieldMask|array|null $mask = null,
    ) {
        $this->mask = Mask::from($mask);
    }

    /** Converts the source to a populated proto message applying the field mask. */
    public function toProto(): Message
    {
        $message = new (static::messageClass())();

        foreach (static::fields() as $field) {
            $field->apply($this->source, $this->mask, $message);
        }

        return $message;
    }

    /**
     * @param iterable<mixed> $sources
     * @param array<string>|null $mask
     */
    public static function collection(iterable $sources, FieldMask|array|null $mask = null): ResourceCollection
    {
        return new ResourceCollection($sources, static::class, $mask);
    }

    /** Returns the proto message class name from the #[ProtoMessage] attribute. */
    public static function messageClass(): string
    {
        $attrs = new \ReflectionClass(static::class)->getAttributes(ProtoMessage::class);

        return $attrs[0]->newInstance()->class;
    }

    /** @return array<int, \ProtoResource\Types\FieldDefinition> */
    abstract public static function fields(): array;
}
