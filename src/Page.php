<?php

namespace Atthakasem\LaravelJsonMenu;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Page
{
    public $name;
    public $url;
    public $external = false;
    public $active = false;
    public $class;
    public $children;

    public function __construct($page, ?string $parentUrl = null)
    {
        $this->children = new Collection();
        $this->parse($page, $parentUrl);
    }

    public function getClassString(): string
    {
        $activeString = $this->isActive() ? 'active' : '';
        $userClassString = $this->generateUserClassesString();

        if (!$activeString && !$userClassString) {
            return '';
        }

        $spacer = '';
        if ($activeString && $userClassString) {
            $spacer = ' ';
        }

        return "class=\"$activeString$spacer$userClassString\"";
    }

    public function getExternalString(): string
    {
        return $this->external ? 'target="_blank"' : '';
    }

    protected function isActive(): bool
    {
        if (strpos(url()->current(), $this->url) !== false) {
            return true;
        }

        return false;
    }

    protected function generateUserClassesString(): string
    {
        if ($this->class !== null) {
            return $this->class;
        }

        return '';
    }

    protected function parse($page, ?string $parentUrl = null): void
    {
        if (is_object($page)) {
            $this->parseObject($page, $parentUrl);
        } else {
            $this->name = $page;

            $url = Str::slug($page);
            $this->url = $parentUrl ? "$parentUrl/$url" : $url;
            $this->url = url($this->url);
        }

        $this->active = $this->isActive();
    }

    protected function parseObject(object $page, ?string $parentUrl): void
    {
        $this->name = $page->name;
        $this->class = $page->class ?? null;

        $this->assignExternal(@$page->external);
        $this->assignUrl($page->name, @$page->route, @$page->url, $parentUrl);
        $this->assignChildren(@$page->children);
    }

    protected function assignExternal($external)
    {
        if (!empty($external) && is_bool($external)) {
            $this->external = $external;
        }
    }

    protected function assignUrl(string $pageName, ?string $pageRoute = null, ?string $pageUrl = null, ?string $parentUrl = null): void
    {
        if (!empty($pageRoute)) {
            $this->url = route($pageRoute);
        } else {
            $childUrl = $pageUrl ?? Str::slug($pageName);
            $this->url = $parentUrl && !$this->external ? "$parentUrl/$childUrl" : $childUrl;
            $this->url = url($this->url);
        }
    }

    protected function assignChildren(?array $childPages): void
    {
        if (!empty($childPages)) {
            foreach ($childPages as $childPage) {
                $this->children->add(new self($childPage, $this->url));
            }
        }
    }
}
