<?php

namespace App;

use Illuminate\Translation\FileLoader as BaseFileLoader;

class FileLoader extends BaseFileLoader
{
    public function save($items, $environment, $group, $namespace = null)
    {
        $path = app_path().'/config';
        var_dump($path);

        if (is_null($path)) {
            return;
        }

        $file = (!$environment || ($environment == 'production'))
            ? "{$path}/{$group}.php"
            : "{$path}/{$environment}/{$group}.php";


        $this->files->put($file, '<?php return ' . var_export($items, true) . ';');
    }
}
