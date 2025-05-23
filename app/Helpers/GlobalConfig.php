<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;

class GlobalConfig
{
    protected static $path = 'config/global.json';

    public static function get($key = null)
    {
        $json = File::get(base_path(self::$path));
        $data = json_decode($json, true);

        return $key ? ($data[$key] ?? null) : $data;
    }

    public static function set($key, $value)
    {
        $json = File::get(base_path(self::$path));
        $data = json_decode($json, true);
        $data[$key] = $value;

        File::put(base_path(self::$path), json_encode($data, JSON_PRETTY_PRINT));

        return true;
    }
}
