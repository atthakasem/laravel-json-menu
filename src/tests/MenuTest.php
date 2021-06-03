<?php

namespace Tests;

use Atthakasem\LaravelJsonMenu\LaravelJsonMenu;
use Orchestra\Testbench\TestCase;

class MenuTest extends TestCase
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->l = new LaravelJsonMenu('main', __DIR__ . '/menus');
    }
}
