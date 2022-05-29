<?php

namespace Bakgul\FileContent\Tasks;

class GetCodeLine
{
    public static function _(array $content, array $specs)
    {
        if (!array_key_exists('jump', $specs)) ray(debug_backtrace());
        return [
            $s = FindIndex::start($content, $specs['jump'], 0, ...$specs['start']),
            $e = FindIndex::end($content, $specs, $s),
            self::extract($content, $s, $e),
        ];
    }

    public static function extract(array $content, int $start, int $end): array
    {
        return self::stringify(self::chunk(self::obtain($content, $start, $end)));
    }

    public static function obtain(array $content, int $start, int $end)
    {
        return array_map('trim', array_slice($content, $start, $end - $start + 1));
    }

    public static function chunk(array $imports)
    {
        $chunks = [];

        foreach ($imports as $line) {
            if (str_contains($line, 'import')) $chunks[] = [$line];
            else $chunks[count($chunks) - 1][] = $line;
        }

        return $chunks;
    }

    public static function stringify($chunks)
    {
        return array_map(fn ($x) => implode(' ', $x), $chunks);
    }
}
