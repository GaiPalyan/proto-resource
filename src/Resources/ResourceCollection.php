<?php

declare(strict_types=1);

namespace ProtoResource\Resources;

use Google\Protobuf\FieldMask;
use ProtoResource\Mask\Mask;

/** @implements \IteratorAggregate<int, Resource> */
final class ResourceCollection implements \IteratorAggregate
{
    /** The active field mask applied to every item in the collection. */
    private Mask $mask;

    /**
     * @param iterable<mixed> $sources
     * @param array<string>|null $mask
     */
    public function __construct(
        /** The iterable of source objects to map. */
        private readonly iterable $sources,
        /** Fully-qualified resource class used to map each source. */
        private readonly string $resourceClass,
        FieldMask|array|null $mask = null,
    ) {
        $this->mask = Mask::from($mask);
    }

    /** @return array<int, \Google\Protobuf\Internal\Message> */
    public function toProto(): array
    {
        $results = [];

        foreach ($this->sources as $source) {
            $resource = new $this->resourceClass($source, $this->mask);
            $results[] = $resource->toProto();
        }

        return $results;
    }

    /** Iterates over Resource instances, one per source. */
    public function getIterator(): \Traversable
    {
        foreach ($this->sources as $source) {
            yield new $this->resourceClass($source, $this->mask);
        }
    }
}
