<?php

namespace Atthakasem\LaravelJsonMenu;

use DOMDocument;
use DOMXPath;
use Exception;
use Illuminate\Support\Str;

class Menu
{
    protected $structure = [];

    public function __construct($structure)
    {
        $this->structure = $structure;
    }

    /**
     * Generate the html menu
     *
     * @return string html menu
     */
    public function generateHtml(): string
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
        $dom = new DOMDocument();
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
}
