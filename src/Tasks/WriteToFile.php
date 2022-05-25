<?php

namespace Bakgul\FileContent\Tasks;

use Bakgul\FileContent\Helpers\Content;
use Bakgul\FileHistory\Services\LogServices\ForUndoingLogService;

class WriteToFile
{
    public static function _($content, $file, $isJson = false)
    {
        self::writeLog($file);

        self::writeContent($content, $file, $isJson);
    }

    private static function writeLog(string $file)
    {
        if (self::isNotLoggable($file)) return;

        ForUndoingLogService::set($file, false, false);
    }

    private static function isNotLoggable($file): bool
    {
        return !file_exists($file);
    }

    private static function writeContent($content, $file, $isJson)
    {
        $isJson
            ? Content::writeJson($file, $content)
            : Content::write($file, $content, '');
    }
}
