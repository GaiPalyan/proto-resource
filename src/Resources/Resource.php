<?php

declare(strict_types=1);

namespace ProtoResource\Resources;

use Google\Protobuf\FieldMask;
use Google\Protobuf\Internal\Message;
use ProtoResource\Attributes\ProtoMessage;
use ProtoResource\Builder;
use ProtoResource\Mask\Mask;
use ProtoResource\Mask\MaskParser;

abstract class Resource
{
    public readonly Mask $mask;

    public function __construct(
        public readonly object|array $source,
        object|array|null $inputMask = null,
    ) {
        $this->assertMask($inputMask);

        $this->mask = $inputMask instanceof Mask
            ? $inputMask
            : MaskParser::from($inputMask);
    }

    public function toGrpc(): Message
    {
        return new Builder()->build(
            source: $this->source,
            fields: static::fields(),
            mask: $this->mask,
            message: new (static::messageClass())()
        );
    }

    public static function collection(iterable $sources, FieldMask|array|null $inputMask = null): ResourceCollection
    {
        return new ResourceCollection($sources, static::class, $inputMask);
    }

    public static function messageClass(): string
    {
        $attrs = (new \ReflectionClass(static::class))->getAttributes(ProtoMessage::class);

        return $attrs[0]->newInstance()->class;
    }

    private function assertMask(object|array|null $inputMask): void
    {
        if (
            ! is_null($inputMask)
            && ! $inputMask instanceof Mask
            && ! $inputMask instanceof FieldMask
            && ! is_array($inputMask)
        ) {
            throw new \InvalidArgumentException('Invalid mask input');
        }
    }

    abstract public static function fields(): array;
}
