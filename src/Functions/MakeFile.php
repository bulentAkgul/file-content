<?php

namespace Bakgul\FileContent\Functions;

use Bakgul\Kernel\Helpers\Path;
use Bakgul\Kernel\Tasks\MutateStub;
use Bakgul\FileHistory\Services\LogServices\ForUndoingLogService;
use Illuminate\Filesystem\Filesystem;

class MakeFile
{
    public static function _(array $request)
    {
        $file = Path::glue([$request['attr']['path'], $request['attr']['file']]);

        (new Filesystem)->put($file, MutateStub::get($request));

        ForUndoingLogService::set($file, false, true);
    }
}