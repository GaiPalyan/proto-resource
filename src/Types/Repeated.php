<?php

declare(strict_types=1);

namespace ProtoResource\Types;

use Google\Protobuf\Internal\Message;
use ProtoResource\Builder;
use ProtoResource\Mask\Mask;

final readonly class Repeated extends Field
{
    public function __construct(
        string $name,
        mixed $source,
        private ?string $resourceClass = null,
        private ?string $messageClass = null,
    ) {
        parent::__construct($name, $source);
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

        $items = $this->resolveValue($data);

        if (! is_iterable($items) || empty($items)) {
            return;
        }

        $protoClass = $this->messageClass ?? $this->resourceClass::messageClass();
        $result = [];

        foreach ($items as $itemData) {
            $itemMessage = new $protoClass();

            if ($this->resourceClass) {
                $builder->build(
                    source: $itemData,
                    fields: $this->resourceClass::fields(),
                    mask: Mask::all(),
                    message: $itemMessage
                );
            } else {
                $builder->autoFill(
                    message: $itemMessage,
                    data: $itemData,
                    mask: Mask::all()
                );
            }

            $result[] = $itemMessage;
        }

        $message->{'set' . ucfirst($this->name)}($result);
    }
}
