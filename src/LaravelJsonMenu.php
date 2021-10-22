<?php

namespace Atthakasem\LaravelJsonMenu;

use DOMDocument;
use DOMXPath;

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
    public function generateMenu(): string
    {
        return $this->menu->generateHtml();
    }

    /**
     * Generate the breadcrumb
     *
     * @return string
     */
    public function generateBreadcrumb(): string
    {
        $doc = new DOMDocument;
        $doc->loadHTML($this->menu->generateHtml());
        $xpath = new DOMXPath($doc);
        $activeLinks = $xpath->query('//a[contains(concat(" ", normalize-space(@class), " "), " active ")]');

        return $activeLinks === false ? '' : $this->menu->generateBreadcrumb($activeLinks);
    }
}
