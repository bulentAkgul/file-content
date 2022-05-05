<?php

namespace Bakgul\FileContent;

use Bakgul\Kernel\Concerns\HasConfig;
use Illuminate\Support\ServiceProvider;

class FileContentServiceProvider extends ServiceProvider
{
    use HasConfig;
    
    public function boot()
    {
        //
    }

    public function register()
    {
        $this->registerConfigs(__DIR__ . DIRECTORY_SEPARATOR . '..');
    }
}
