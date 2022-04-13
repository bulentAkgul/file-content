<?php

namespace Bakgul\FileContent\Tasks;

use Bakgul\Kernel\Helpers\Arry;
use Bakgul\Kernel\Helpers\Isolation;
use Bakgul\Kernel\Helpers\Text;

class ExtendCodeBlock
{
    public static function _(array $object, string|array $add, array $indentation, array $specs): array
    {
        $object = self::prepare($object, $indentation, $specs);

        array_splice($object, 1, 0, self::format($add, $indentation, Arry::get($specs, 'eol') ?? ','));

        return Arry::get($specs, 'isSortable') ? self::sort($object) : $object;
    }

    private static function prepare(array $object, array $indentation, array $specs): array
    {
        return count($object) > 1
            ? Arry::purify($object, "\n\r", false)
            : self::divide($object, $indentation, $specs);
    }

    private static function divide(array $object, array $indentation, array $specs): array
    {
        $braket = Arry::get($specs, 'bracket') ?? '{}';
        $brakets = str_split($braket);

        return Text::split(str_replace(
            $braket,
            $brakets[0] . PHP_EOL . MutateIndentation::set($indentation) . $brakets[1],
            trim($object[0], "\n\r\t")
        ));
    }

    private static function format(string|array $add, array $indentation, string $eol)
    {
        $add = is_array($add) ? $add : [$add];

        $edges = [0, array_key_last($add)];

        return Arry::assocMap($add, fn ($k, $v) => self::setLine($v, $k, $indentation, $eol, $edges));
    }

    private static function setLine($value, $key, $indentation, $eol, $edges)
    {
        return MutateIndentation::set($indentation, increment: in_array($key, $edges) ? 1 : 2)
            . $value . (in_array($value, ['[', '{']) ? '' : $eol);
    }

    private static function sort(array $object)
    {
        [$first, $last, $rest] = Isolation::edges($object);

        return [$first, ...Arry::sort($rest), $last];
    }
}
