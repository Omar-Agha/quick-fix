<?php

namespace App\Core;

class FilesManager
{

    public static function getFileUrlOrNull($path)
    {
        return $path ? asset('storage/' . $path) : null;
    }
}
