<?php

namespace Bakgul\FileContent\Tasks;

use Bakgul\FileContent\Helpers\Content;
use Bakgul\FileContent\Tasks\ExtendCodeBlock;
use Bakgul\FileContent\Tasks\ExtendCodeLine;
use Bakgul\FileContent\Tasks\GetCodeBlock;
use Bakgul\FileContent\Tasks\GetCodeLine;
use Bakgul\FileContent\Tasks\WriteToFile;
use Bakgul\Kernel\Functions\SetBlockSpecs;
use Bakgul\Kernel\Functions\SetLineSpecs;
use Bakgul\Kernel\Helpers\Arry;

class Register
{
    private static array $fileContent = [];
    private static array $request = [];

    public static function _(array $request, array $lineSpecs, array $blockSpecs, string $key, ?string $only = null)
    {
        self::$request = $request;

        self::getTargetFileContent();

        if (self::isNotRegisterable($lineSpecs)) return;

        self::insert($lineSpecs, $blockSpecs, $key, $only);

        self::write();
    }

    private static function getTargetFileContent()
    {
        self::$fileContent = Content::read(self::$request['attr']['target_file'], purify: false);
    }

    private static function isNotRegisterable(array $lineSpecs): bool
    {
        return self::isContentNotReady($lineSpecs) || self::isAlreadyImported();
    }

    private static function isContentNotReady(array $lineSpecs): bool
    {
        return !Arry::get($lineSpecs, 'isEmpty') && empty(self::$fileContent);
    }

    private static function isAlreadyImported(): bool
    {
        return self::$request['map']['imports'] && Arry::contains(self::$request['map']['imports'], self::$fileContent);
    }

    private static function insert($lineSpecs, $blockSpecs, $key, $only): void
    {
        if ($only != 'block') self::insertLines(SetLineSpecs::_($lineSpecs));

        if ($only != 'line') self::insertBlock(SetBlockSpecs::_($blockSpecs), key: $key);
    }

    private static function insertLines(array $specs): void
    {
        [$start, $end, $imports] = GetCodeLine::_(self::$fileContent, $specs);

        self::purifyContent($start, $end);

        self::regenerateContent($start, self::makeLines($imports));
    }

    private static function insertBlock(array $specs, string|array $add = '', string $key = ''): void
    {
        [$start, $indentation, $end, $block] = GetCodeBlock::_(self::$fileContent, $specs);
        ray([$start, $indentation, $end, $block, $add, $key, $specs]);
        self::purifyContent($start, $end);

        self::regenerateContent($start, self::makeBlock($block, $add, $key, $indentation, $specs));
    }

    private static function purifyContent(int $start, int $end): void
    {
        self::$fileContent = Content::purify(self::$fileContent, $start, $end);
    }

    private static function makeLines($imports)
    {
        return ExtendCodeLine::_($imports, self::$request['map']['imports']);
    }

    private static function makeBlock($block, $add, $key, $indentation, $specs)
    {
        return ExtendCodeBlock::_(
            $block,
            $add ?: self::$request['map'][$key],
            ['base' => $indentation, 'repeat' => $specs['repeat']],
            $specs
        );
    }

    private static function regenerateContent(int $start, array $insert)
    {
        self::$fileContent = Content::regenerate(self::$fileContent, $start, $insert);
    }

    private static function write()
    {
        WriteToFile::handle(self::$fileContent, self::$request['attr']['target_file']);
    }
}
