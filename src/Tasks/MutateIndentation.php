<?php

namespace Bakgul\FileContent\Tasks;

use Bakgul\Kernel\Helpers\Settings;

class MutateIndentation
{
    public static function set(array|string $indentation, int $repeat = null, int $increment = 0): string
    {
        return str_repeat(
            is_array($indentation) ? $indentation['base'] : $indentation,
            ($repeat ?? (is_array($indentation) ? $indentation['repeat'] : 0)) + $increment
        );
    }

    public static function get(array|string $content): string
    {
        return is_string($content) ? self::extract($content) : self::find($content);
    }

    private static function find(array $content)
    {
        foreach (self::setContent($content) as $line) {
            $indentation = self::obtain($line);

            if ($indentation) return $indentation;
        }

        return Settings::get('indentations.' . (str_contains($content[0], 'php') ? 'php' : 'other'));
    }

    private static function setContent(array $content): array
    {
        return array_filter(array_map(fn ($x) => preg_replace('/\R/', '', $x), $content));
    }


    private static function obtain($line): string
    {
        $characters = str_split($line);

        $indentation = [];

        foreach ($characters as $c) {
            if (in_array($c, [" ", "\t"])) {
                $indentation[] = $c;
            } else {
                return count($indentation) == count($characters) ? '' : implode('', $indentation);
            }
        }
    }

    private static function extract(string $content): string
    {
        return preg_replace('/[^ \t]/', '', preg_replace("/[a-z].*$/", "", $content));
    }
}
