<?php

namespace Atthakasem\LaravelJsonMenu;

use DOMNodeList;
use Illuminate\Support\Collection;

class Menu
{
    protected $structure = [];
    public $pages;

    public function __construct($structure)
    {
        $this->structure = $structure;
        $this->pages = new Collection;

        foreach ($structure as $page) {
            $this->pages->add(new Page($page));
        }
    }

    public function generateBreadcrumb(DOMNodeList $activeLinks): string
    {
        $html = '<ul>';
        foreach ($activeLinks as $key => $link) {
            $active = $key === count($activeLinks) - 1 ? 'active' : '';
            $html .= '<li>
                    <a href="' . $link->getAttribute('href') . '" class="' . $active . '">' .
                        $link->nodeValue .
                    '</a>
                </li>';
        }
        $html .= '</ul>';

        return $this->stripWhitespaces($html);
    }

    public function generateHtml(): string
    {
        $html = '<ul>';
        foreach ($this->pages as $page) {
            $html .= $this->generateNode($page);
        }
        $html .= '</ul>';

        return $this->stripWhitespaces($html);
    }

    protected function generateNode(Page $page): string
    {
        $html = '';
        $html .= $this->generateCurrentLevel($page);
        $html .= $this->generateChildLevel($page->children);

        return $html;
    }

    protected function generateCurrentLevel(Page $page): string
    {
        $html = "<li>
                    <a href=\"{$page->url}\"
                        {$page->getClassString()}
                        {$page->getExternalString()}
                    >{$page->name}</a>";

        return $html;
    }

    protected function generateChildLevel(Collection $childPages): string
    {
        $html = '';

        if ($childPages->isNotEmpty()) {
            $html .= '<ul>';

            foreach ($childPages as $page) {
                $html .= $this->generateNode($page);
            }
            $html .= '</ul>';
        }
        $html .= '</li>';

        return $html;
    }

    protected function stripWhitespaces(string $html): string
    {
        $multiSpaceToSingleSpace = preg_replace('/\s+/', ' ', $html);

        return preg_replace('/ ([><])/', "$1", $multiSpaceToSingleSpace);
    }

    /**
     * Get the value of structure
     */
    public function getStructure()
    {
        return $this->structure;
    }
}
