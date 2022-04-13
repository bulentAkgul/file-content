<?php

namespace Bakgul\FileContent\Tasks;

class GetCodeBlock
{
    public static function _(array $content, array $specs): array
    {
        return [
            $s = FindIndex::start($content, ...$specs['start']),
            $i = MutateIndentation::get($content),
            $e = FindIndex::end($content, $specs, $s, $i),
            self::extract($s, $e, $content)
        ];
    }

    private static function extract(int $start, int $end, array $content): array
    {
        return array_slice($content, $start, $end - $start + 1);
    }
}
