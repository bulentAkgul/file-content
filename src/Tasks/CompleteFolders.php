<?php

namespace Bakgul\FileContent\Tasks;

use Bakgul\Kernel\Helpers\Package;
use Bakgul\Kernel\Helpers\Path;
use Bakgul\Kernel\Helpers\Settings;
use Bakgul\Kernel\Helpers\Text;
use Bakgul\FileHistory\Services\LogServices\ForUndoingLogService;

class CompleteFolders
{
    public static function _(array|string $path, bool $loggable = true): array
    {
        $folders = array_filter(is_string($path)
            ? self::createFromStringifiedPath($path)
            : self::createFromSerializedPath($path)
        );

        self::log($folders, $loggable);

        return $folders;
    }

    private static function createFromSerializedPath(array $path): array
    {
        $url = Path::stringify($path);

        $folders = [self::makeDir($url)];

        foreach (array_merge($path['mains'], $path['subs']) as $folder) {
            $url .= Text::append($folder);
            $folders[] = self::makeDir($url);
        }

        return $folders;
    }

    private static function createFromStringifiedPath(string $path): array
    {
        $folders = [];
        $folder = '';

        $base = self::setBase($path);

        $parts = Text::serialize(trim(str_replace($base, '', $path), DIRECTORY_SEPARATOR));

        foreach ($parts as $part) {
            $folder = Text::prepend($folder) . $part;
            $folders[] = self::makeDir(Path::glue([$base, $folder]));
        }

        return array_filter($folders);
    }

    private static function setBase(string $path)
    {
        foreach (['storage'] as $folder) {
            if (!str_contains($path, base_path($folder))) return Path::realBase();
        }

        return base_path();
    }

    private static function makeDir(string $path): string
    {
        if (file_exists($path)) return '';

        mkdir($path);
        
        return $path;
    }

    private static function log(array $folders, bool $loggable)
    {
        if (!$loggable) return;

        foreach ($folders as $path) {
            ForUndoingLogService::set($path, true, true);
        }
    }
}