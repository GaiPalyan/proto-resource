<?php

declare(strict_types=1);

namespace ProtoResource\Types;

use Google\Protobuf\Internal\Message;
use ProtoResource\HasRaw;
use ProtoResource\Mask\Mask;

final readonly class Map extends Field
{
    use HasRaw;

    public function __construct(
        string $name,
        mixed $source = null,
        private ?string $resourceClass = null,
        private ?string $messageClass = null,
    ) {
        parent::__construct($name, $source ?? $name);
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

        $protoClass = $this->messageClass ?? ($this->resourceClass ? $this->resourceClass::messageClass() : null);
        $result = [];

        foreach ($items as $key => $value) {
            if ($protoClass !== null) {
                $itemMessage = new $protoClass();

                if ($this->resourceClass) {
                    foreach ($this->resourceClass::fields() as $field) {
                        $field->apply($value, Mask::all(), $itemMessage);
                    }
                } else {
                    $this->fillRaw(
                        message: $itemMessage,
                        data: $value,
                        mask: Mask::all()
                    );
                }

                $result[$key] = $itemMessage;
            } else {
                $result[$key] = $value;
            }
        }

        $message->{'set' . ucfirst($this->name)}($result);
    }
}
