<?php

namespace Bakgul\FileContent\Tasks;

use Bakgul\Kernel\Helpers\Arry;
use Bakgul\Kernel\Helpers\Text;
use Bakgul\FileContent\Tasks\MutateIndentation;

class FindIndex
{
    private static $content;
    private static $specs;

    public static function start(array $content, string $search, int $add = 0)
    {
        if (empty($content)) return 0;

        foreach ($content as $i => $line) {
            if (self::isStart($line, $search)) return $i + $add;
        }

        return 0;
    }

    public static function end(array $content, array $specs, int $ref = 0, string $indentation = ''): int
    {
        if (empty($content)) return 0;

        if (!$specs['end'][0]) return array_key_last($content);
        
        self::$content = $content;
        self::$specs = $specs;
        
        if (self::isLineFoundable()) self::findEndLine();
        
        if (self::isSingleLine($ref)) return $ref;

        $indentation = MutateIndentation::set($indentation, Arry::get($specs, 'repeat') ?? 0);

        $search = $indentation . self::$specs['end'][0];
        
        foreach ($content as $i => $line) {
            if (self::isEnd($line, $search, $indentation, $i, $ref, $specs['isStrict'])) return $i + self::$specs['end'][1];
        }

        return 0;
    }

    public static function isStart(string $line, string $search): bool
    {
        return str_contains($line, $search);
    }

    public static function isEnd(string $line, string $search, string $indentation, int $index, int $ref, bool $isStrict = true): bool
    {
        return self::isTheSame($line, $search, $indentation, $isStrict) && $index > $ref;
    }

    private static function isTheSame(string $line, string $search, string $indentation, bool $isStrict)
    {
        return $isStrict
            ? $indentation . Text::purify($line) == $search
            : str_contains($line, $search);
    }

    public static function isSingleLine(int $ref)
    {
        return str_contains(self::$content[$ref], self::$specs['end'][0]);
    }

    private static function isLineFoundable(): ?bool
    {
        return Arry::has('findEndBy', self::$specs);
    }

    private static function findEndLine()
    {
        $start = self::getEndingLine();

        self::$specs['end'] = [$start['line'], self::setAdd(self::$specs['findEndBy'], $start['index'])];
    }

    private static function getEndingLine()
    {
        $index = self::start(self::$content, self::$specs['end'][0]);

        return ['index' => $index, 'line' => self::setEndingLine($index)];
    }

    private static function setEndingLine(int $index): string
    {
        return Text::purify(Arry::get(self::$content, $index) ?? '');
    }

    private static function setAdd(string $search, int $index): int
    {
        $rollback = 0;

        foreach(self::getTop($index) as $i => $line) {
            if (Text::purify($line) == '') $rollback++;
            if (str_contains($line, $search)) return ($i + $rollback) * -1;
        }

        return 0;
    }

    private static function getTop(int $index)
    {
        return array_reverse(array_slice(self::$content, 0, $index));
    }
}
