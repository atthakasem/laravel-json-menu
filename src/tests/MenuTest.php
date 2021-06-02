<?php

namespace Tests;

use Atthakasem\LaravelJsonMenu\LaravelJsonMenu;
use ErrorException;
use Orchestra\Testbench\TestCase;

class MenuTest extends TestCase
{
    protected $menuFolder;
    protected $emptyFolder;
    protected $multipleFolder;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->menuFolder     = __DIR__ . '/menus';
        $this->emptyFolder    = __DIR__ . '/menus/purposely_empty';
        $this->multipleFolder = __DIR__ . '/menus/multiple_menu_files';
    }

    /** @test */
    public function it_loads_the_json_file()
    {
        $package = new LaravelJsonMenu('main', $this->menuFolder);
        $this->assertIsArray($package->menu->getStructure());
        $this->assertNotEmpty($package->menu->getStructure());
    }

    /** @test */
    public function it_loads_a_json_without_having_to_specify_a_name()
    {
        $package = new LaravelJsonMenu(null, $this->menuFolder);
        $this->assertIsArray($package->menu->getStructure());
        $this->assertNotEmpty($package->menu->getStructure());
    }

    /** @test */
    public function it_fails_when_no_menu_files_exist_during_named_call()
    {
        $this->expectException(ErrorException::class);
        new LaravelJsonMenu('main', $this->emptyFolder);
    }

    /** @test */
    public function it_fails_when_no_menu_files_exist_during_nameless_call()
    {
        $this->expectException(ErrorException::class);
        new LaravelJsonMenu(null, $this->emptyFolder);
    }

    /** @test */
    public function it_fails_when_calling_the_menu_namelessly_but_multiple_menu_files_exist()
    {
        $this->expectException(ErrorException::class);
        new LaravelJsonMenu(null, $this->multipleFolder);
    }
}
