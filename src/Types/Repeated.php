<?php

declare(strict_types=1);

namespace ProtoResource\Types;

use Google\Protobuf\Internal\Message;
use ProtoResource\HasRaw;
use ProtoResource\Mask\Mask;

final readonly class Repeated extends Field
{
    use HasRaw;

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
        Message $message
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
                foreach ($this->resourceClass::fields() as $field) {
                    $field->apply($itemData, Mask::all(), $itemMessage);
                }
            } else {
                $this->fillRaw(
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
