<?php

declare(strict_types=1);

namespace ProtoResource\Resources;

use Google\Protobuf\FieldMask;
use ProtoResource\Mask\Mask;
use ProtoResource\Mask\MaskParser;

final class ResourceCollection implements \IteratorAggregate
{
    private ?Mask $mask;

    public function __construct(
        private readonly iterable $sources,
        private readonly string $resourceClass,
        FieldMask|array|null $inputMask = null,
    ) {
        $this->mask = MaskParser::from($inputMask);
    }

    public function toGrpc(): array
    {
        $results = [];

        foreach ($this->sources as $source) {
            $resource = new $this->resourceClass($source, $this->mask);
            $results[] = $resource->toGrpc();
        }

        return $results;
    }

    public function getIterator(): \Traversable
    {
        foreach ($this->sources as $source) {
            yield new $this->resourceClass($source, $this->mask);
        }
    }

    public function toArray(): array
    {
        return $this->toGrpc();
    }
}
