<?php

declare(strict_types=1);

namespace ProtoResource\Types;

use Google\Protobuf\Internal\Message;
use ProtoResource\Builder;
use ProtoResource\Mask\Mask;

final readonly class Map extends Field
{
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

        $protoClass = $this->messageClass ?? ($this->resourceClass ? $this->resourceClass::messageClass() : null);
        $result = [];

        foreach ($items as $key => $value) {
            if ($protoClass !== null) {
                $itemMessage = new $protoClass();

                if ($this->resourceClass) {
                    $builder->build(
                        source: $value,
                        fields: $this->resourceClass::fields(),
                        mask: Mask::all(),
                        message: $itemMessage
                    );
                } else {
                    $builder->autoFill(
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
