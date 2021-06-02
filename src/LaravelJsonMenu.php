<?php

namespace Atthakasem\LaravelJsonMenu;

use DOMDocument;
use DOMXPath;
use Exception;
use ErrorException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Str;

class LaravelJsonMenu
{
    protected $path = '';
    protected $files = [];
    protected $structure = [];

    public function __construct(?string $path = null)
    {
        $this->path = $path ?? config('laravel-json-menu.path');
        $this->rescanFiles();
    }

    /**
     * Load the menu (with or without a specified menu name)
     * and generate the html menu
     *
     * @param string|null $menuName
     * @return void html menu
     */
    public function load(?string $menuName = null): string
    {
        if (empty($menuName)) {
            return $this->loadOnly();
        } else {
            return $this->loadName($menuName);
        }
    }

    /**
     * Load the only existing menu without having to specify its name
     * and generate the html menu
     *
     * @return string html menu
     */
    protected function loadOnly(): string
    {
        $this->rescanFiles();

        if (count($this->files) < 1) {
            throw new FileNotFoundException("No menu files found in {$this->path}");
        } else if (count($this->files) > 1) {
            throw new ErrorException("Cannot call menu namelessly due to ambiguity. Multiple menu files detected.");
        }

        return $this->loadPath($this->files[0]);
    }

    /**
     * Load the menu by menu name and generate the html menu
     *
     * @param string $menuName
     * @return string html menu
     */
    protected function loadName(string $menuName): string
    {
        $menuName = trim($menuName, "\"'");
        $path = "{$this->path}/{$menuName}.json";

        return $this->loadPath($path);
    }

    /**
     * Load a menu file by path and generate the html menu
     *
     * @param string $path
     * @return string html menu
     */
    protected function loadPath(string $path): string
    {
        $this->structure = json_decode(file_get_contents($path));

        return $this->generate();
    }

    /**
     * Rescan menu files and reassign $this->files
     *
     * @return void
     */
    protected function rescanFiles(): void
    {
        $this->files = glob("{$this->path}/*.json");
    }

    /**
     * Generate the html menu
     *
     * @return string html menu
     */
    public function generate(): string
    {
        $html = '<ul>';
        foreach ($this->structure as $page) {
            $html .= $this->generateListElement($page);
        }
        $html .= '</ul>';
        return $this->addActiveClassToAncestors($html);
    }

    private function generateListElement($page, $parentUrl = ''): string
    {
        $obj         = $this->extractPageProperties($page);
        $name        = $obj->name;
        $url         = $obj->url;
        if ($parentUrl) {
            $relativeUrl = $parentUrl . "/" . $this->urlSlug($url);
        } else {
            $relativeUrl = $this->urlSlug($url);
        }
        $classString = $this->generateClassString($page, $relativeUrl);
        if (!property_exists($page, 'external')) {
            $url = url($relativeUrl);
        }
        // if (!Auth::check() && (!property_exists($page, 'public') || $page->public === false)) {
        //     $url = '#login';
        // }
        $html = '';
        $html .= $this->generateCurrentLevelMenu($name, $url, $classString, property_exists($page, 'external'));
        $html .= $this->generateDeeperLevelMenues($page, $relativeUrl);
        return $html;
    }

    private function addActiveClassToAncestors(string $html): string
    {
        $dom = new DOMDocument;
        $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $xpath = new DOMXPath($dom);
        $nodes = $xpath->query("//a[@class='active']/ancestor::li[not(@class='active')]/child::a");
        foreach ($nodes as $node) {
            $node->setAttribute('class', 'active');
        }
        return utf8_decode($dom->saveHTML($dom->documentElement));
    }

    private function extractPageProperties($page): object
    {
        $retval = (object) [
            'name' => null,
            'url'  => null,
        ];
        if (is_object($page)) {
            // --- Advanced navi object ---
            $this->checkForErrors($page);

            // Mandatory property: 'name'
            $retval->name = $page->name;
            $retval->url  = $page->name;

            // Optional properties: 'url', 'external', 'class'
            if (property_exists($page, 'url')) {
                $retval->url = $page->url;
            }
            if (property_exists($page, 'external')) {
                $retval->url = $page->external;
            }
        } else {
            // --- Simple navi object ---
            $retval->name = $page;
            $retval->url  = $page;
        }
        return $retval;
    }

    private function generateClassString($page, string $url): string
    {
        $active = '';
        if (!property_exists($page, 'external')) {
            $requestPathParts = explode("/", request()->path());
            unset($requestPathParts[count($requestPathParts) - 1]);
            $pathAbove = implode("/", $requestPathParts);
            if ($this->urlSlug($url) === request()->path() || $this->urlSlug($url) === $pathAbove) {
                $active = 'active';
            }
        }
        $customClasses = '';
        if (property_exists($page, 'class')) {
            $customClasses = trim($page->class);
        }
        if (!empty($active) && !empty($customClasses)) {
            $classSpacer = ' ';
        } else {
            $classSpacer = '';
        }
        return 'class="' . $active . $classSpacer . $customClasses . '"';
    }

    private function checkForErrors(object $page): void
    {
        if (property_exists($page, 'url') && property_exists($page, 'external')) {
            throw new Exception("Navigation properties 'url' and 'external' cannot co-exist. Please choose either one or none.");
        }
    }

    private function urlSlug(string $url): string
    {
        $separator = '/';
        return ltrim(array_reduce(explode($separator, $url), function ($carry, $item) use ($separator) {
            return $carry . $separator . Str::slug($item);
        }), $separator);
    }

    private function generateCurrentLevelMenu(string $name, string $url, string $classString, bool $isExternalLink = false): string
    {
        $additionalAttributes = '';
        if ($isExternalLink) {
            $additionalAttributes .= 'target="_blank"';
        }
        return "<li><a href=\"$url\" $classString $additionalAttributes>$name</a>";
    }

    private function generateDeeperLevelMenues($page, $parentUrl): string
    {
        $html = '';
        if (is_object($page) && property_exists($page, 'children')) {
            $html .= '<ul>';
            foreach ($page->children as $childPage) {
                $html .= $this->generateListElement($childPage, $parentUrl);
            }
            $html .= '</ul>';
        }
        $html .= '</li>';
        return $html;
    }

    /**
     * Get the value of structure
     */
    public function getStructure()
    {
        return $this->structure;
    }

    /**
     * Get the value of files
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Set the value of path
     *
     * @return  self
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }
}
