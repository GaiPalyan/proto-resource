<?php

declare(strict_types=1);

namespace ProtoResource;

use Google\Protobuf\Internal\Message;
use ProtoResource\Mask\Mask;

trait HasRaw
{
    /**
     * Fills a proto message from raw source data without field definitions.
     * Scalar values are mapped directly by property name. Nested objects are
     * resolved via the setter's @param docblock to instantiate the child message.
     *
     * @param array<string, mixed>|object $data
     */
    protected function fillRaw(Message $message, array|object $data, Mask $mask): void
    {
        foreach ((array) $data as $key => $value) {
            if (is_null($value) || (! $mask->isAll() && ! $mask->has($key))) {
                continue;
            }

            if (is_object($value) || is_array($value)) {
                $setter = 'set' . ucfirst($key);
                $childMessage = $message->{'get' . ucfirst($key)}();

                if ($childMessage === null) {
                    $docComment = new \ReflectionMethod($message, $setter)->getDocComment();

                    if (! $docComment || ! preg_match('/@param\s+([\w\\\\]+)/', $docComment, $matches)) {
                        continue;
                    }

                    $className = ltrim($matches[1], '\\');

                    if (! class_exists($className)) {
                        continue;
                    }

                    $childMessage = new $className();
                }

                if ($childMessage instanceof Message) {
                    $this->fillRaw($childMessage, $value, $mask->nested($key) ?? Mask::all());
                    $message->$setter($childMessage);
                }

                continue;
            }

            if (is_scalar($value)) {
                $message->{'set' . ucfirst($key)}($value);
            }
        }
    }
}
