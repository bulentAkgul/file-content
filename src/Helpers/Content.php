<?php

namespace Bakgul\FileContent\Helpers;

class Content
{
    public static function read(string $path, bool $isArray = true, bool $purify = true): array|string
    {
        return $isArray
            ? (file_exists($path) ? array_map(fn ($x) => $purify ? trim($x, "\n\r") : $x, file($path)) : [])
            : (file_exists($path) ? file_get_contents($path) : '');
    }

    public static function write(string $path, array|string $content = '', string $glue = PHP_EOL): void
    {
        file_put_contents($path, is_array($content) ? implode($glue, $content) : $content);
    }

    public static function writeJson(string $path, array $content = []): void
    {
        file_put_contents($path, json_encode($content, JSON_PRETTY_PRINT));
    }

    public static function purify(array $content, int $start, int $end): array
    {
        array_splice($content, $start, $end - $start + 1);

        return $content;
    }

    public static function regenerate(array $content, int $start, array $insert, array $covers = []): array
    {
        return array_merge(
            !empty($covers) || $start > 0 ? array_slice($content, 0, $start) : [],
            !empty($covers) ? [$covers['opener']] : [],
            array_map(fn ($x) => trim($x, "\n\r") . PHP_EOL, $insert),
            !empty($covers) ? [$covers['closer']] : [],
            array_slice($content, $start)
        );
    }
}
