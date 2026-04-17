<?php

declare(strict_types=1);

namespace ProtoResource\Types;

use Google\Protobuf\Internal\Message;
use ProtoResource\HasRaw;
use ProtoResource\Mask\Mask;

final readonly class Relation extends Field
{
    use HasRaw;

    public function __construct(
        string $name,
        mixed $source,
        /** Resource class used to define the nested field structure. */
        private ?string $resourceClass = null,
        /** Explicit proto message class override; inferred from resourceClass if omitted. */
        private ?string $messageClass = null,
    ) {
        parent::__construct($name, $source);
    }

    /** Returns the proto message class for this relation, resolved from resource or explicit override. */
    public function messageClass(): string
    {
        return $this->messageClass ?? $this->resourceClass::messageClass();
    }

    public function apply(
        mixed $data,
        Mask $mask,
        Message $message
    ): void {
        if (! $this->shouldInclude($mask)) {
            return;
        }

        if (is_null($value = $this->resolveValue($data))) {
            return;
        }

        $childMessage = new ($this->messageClass())();
        $childMask = $mask->nested($this->name()) ?? Mask::all();

        if ($this->resourceClass) {
            foreach ($this->resourceClass::fields() as $field) {
                $field->apply($value, $childMask, $childMessage);
            }
        } else {
            $this->fillRaw(
                message: $childMessage,
                data: $value,
                mask: $childMask
            );
        }

        $message->{'set' . ucfirst($this->name())}($childMessage);
    }
}
