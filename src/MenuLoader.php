<?php

namespace Atthakasem\LaravelJsonMenu;

use ErrorException;

class MenuLoader
{
    /**
     * Load and provide the menu object
     *
     * @param string|null $menuName
     * @param string|null $directory
     * @return Menu
     */
    public static function load(?string $menuName = null, ?string $directory = null): Menu
    {
        $directory = $directory ?? config('laravel-json-menu.path');

        if (empty($menuName)) {
            return self::loadOnly($directory);
        } else {
            return self::loadName($directory, $menuName);
        }
    }

    /**
     * Load the only existing menu file within a given directory
     * and provide the menu object
     *
     * @param string $directory
     * @return Menu
     */
    protected static function loadOnly(string $directory): Menu
    {
        $files = glob("$directory/*.json");

        if (count($files) < 1) {
            throw new ErrorException("No menu files found in $directory");
        } else if (count($files) > 1) {
            throw new ErrorException('Cannot call menu namelessly due to ambiguity. Multiple menu files detected');
        }

        return self::loadPath($files[0]);
    }

    /**
     * Load a menu file within a given directory
     * and provide the menu object
     *
     * @param string $directory
     * @param string $menuName
     * @return Menu
     */
    protected static function loadName(string $directory, string $menuName): Menu
    {
        $menuName = trim($menuName, "\"'");
        $file = $directory . "/$menuName.json";

        return self::loadPath($file);
    }

    /**
     * Load a menu file within an absolute path
     * and provide the menu object
     *
     * @param string $path
     * @return Menu
     */
    protected static function loadPath(string $path): Menu
    {
        $structure = json_decode(file_get_contents($path));

        return new Menu($structure);
    }
}
