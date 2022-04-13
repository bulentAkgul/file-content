<?php

namespace Bakgul\FileContent\Tasks;

use Bakgul\Kernel\Helpers\Arry;
use Bakgul\Kernel\Helpers\Text;

class ExtendCodeLine
{
    public static function _(array $lines, string|array $add): array
    {
        $purifiedLines = array_map(fn ($x) => Text::purify($x), $lines);

        foreach (self::toArray($add, isPurified: false) as $item) {
            if (!in_array(Text::purify($item), $purifiedLines)) {
                array_splice($lines, 0, 0, $item);
            }
        };

        return self::sort(array_filter($lines));
    }

    public static function toArray(string|array $lines, string $seperator = "EOL", bool $isPurified = true)
    {
        return array_map(
            fn ($x) => $isPurified ? Text::purify($x) : $x,
            is_array($lines) ? $lines : ($seperator == 'EOL'
                ? Text::split($lines)
                : explode($seperator, $lines)
            )
        );
    }

    public static function sort(array $lines)
    {
        $defaults = [];
        $names = [];

        foreach ($lines as $import) {
            if (str_contains($import, '{')) $defaults[] = $import;
            else $names[] = $import;
        }

        return array_merge(Arry::sort($defaults), Arry::sort($names));
    }
}
