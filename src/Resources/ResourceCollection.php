<?php

declare(strict_types=1);

namespace ProtoResource\Resources;

use Google\Protobuf\FieldMask;
use ProtoResource\Mask\Mask;

final class ResourceCollection implements \IteratorAggregate
{
    private ?Mask $mask;

    public function __construct(
        private readonly iterable $sources,
        private readonly string $resourceClass,
        FieldMask|array|null $mask = null,
    ) {
        $this->mask = Mask::from($mask);
    }

    public function toProto(): array
    {
        $results = [];

        foreach ($this->sources as $source) {
            $resource = new $this->resourceClass($source, $this->mask);
            $results[] = $resource->toProto();
        }

        return $results;
    }

    public function getIterator(): \Traversable
    {
        foreach ($this->sources as $source) {
            yield new $this->resourceClass($source, $this->mask);
        }
    }
}
