<?php

namespace Bakgul\FileContent\Functions;

use Bakgul\Kernel\Helpers\Prevented;
use Bakgul\Kernel\Tasks\CompleteFolders;

class CreateFile
{
    public static function _($request)
    {
        if (Prevented::file($request['attr'])) return;
        
        CompleteFolders::_($request['attr']['path']);

        MakeFile::_($request);
    }
}