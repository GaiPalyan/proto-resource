<?php

declare(strict_types=1);

namespace ProtoResource\Types;

use Google\Protobuf\Internal\Message;
use ProtoResource\Builder;
use ProtoResource\Mask\Mask;

final readonly class Relation extends Field
{
    public function __construct(
        string $name,
        mixed $source,
        private ?string $resourceClass = null,
        private ?string $messageClass = null,
    ) {
        parent::__construct($name, $source);
    }

    public function messageClass(): string
    {
        return $this->messageClass ?? $this->resourceClass::messageClass();
    }

    public function apply(
        mixed $data,
        Mask $mask,
        Message $message,
        Builder $builder
    ): void {
        if (! $this->shouldInclude($mask)) {
            return;
        }

        if (is_null($value = $this->resolveValue($data))) {
            return;
        }

        $child = new ($this->messageClass())();
        $childMask = $mask->child($this->name()) ?? Mask::all();

        if ($this->resourceClass) {
            $builder->build(
                source: $value,
                fields: $this->resourceClass::fields(),
                mask: $childMask,
                message: $child
            );
        } else {
            $builder->autoFill(
                message: $child,
                data: $value,
                mask: $childMask
            );
        }

        $message->{'set' . ucfirst($this->name())}($child);
    }
}
