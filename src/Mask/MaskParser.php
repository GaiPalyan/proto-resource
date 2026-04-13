<?php

declare(strict_types=1);

namespace ProtoResource\Mask;

use Google\Protobuf\FieldMask;

class MaskParser
{
    public static function from(array|FieldMask|null $input): Mask
    {
        if ($input instanceof FieldMask) {
            return self::fromPaths(iterator_to_array($input->getPaths()));
        }

        if (is_array($input) && ! empty($input)) {
            return self::fromPaths($input);
        }

        return Mask::all();
    }

    private static function fromPaths(array $paths): Mask
    {
        $fields = [];

        foreach ($paths as $path) {
            if (trim($path) === '') {
                continue;
            }

            data_set($fields, $path, true);
        }

        return new Mask($fields);
    }
}
