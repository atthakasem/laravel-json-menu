<?php

namespace Atthakasem\LaravelJsonMenu;

class LaravelJsonMenu
{
    public $menu;

    public function __construct(?string $menuName = null, ?string $directory = null)
    {
        $this->menu = MenuLoader::load($menuName, $directory);
    }

    /**
     * Generate the html menu
     *
     * @return string
     */
    public function generate(): string
    {
        return $this->menu->generateHtml();
    }
}
