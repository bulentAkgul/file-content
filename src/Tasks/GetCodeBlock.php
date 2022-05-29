<?php

namespace Bakgul\FileContent\Tasks;

class GetCodeBlock
{
    public static function _(array $content, array $specs): array
    {
        if (!array_key_exists('jump', $specs)) ray(debug_backtrace());

        return [
            $s = FindIndex::start($content, $specs['jump'], 0, ...$specs['start']),
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
