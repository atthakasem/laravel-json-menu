<?php

namespace Tests;

use Atthakasem\LaravelJsonMenu\LaravelJsonMenu;
use ErrorException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Orchestra\Testbench\TestCase;

class MenuTest extends TestCase
{
    protected $menu;
    protected $menuFolder = '';
    protected $emptyFolder = '';
    protected $multipleFolder = '';

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->menuFolder = __DIR__ . '/menus';
        $this->emptyFolder = __DIR__ . '/menus/purposely_empty';
        $this->multipleFolder = __DIR__ . '/menus/multiple_menu_files';
        $this->menu = new LaravelJsonMenu($this->menuFolder);
    }

    /** @test */
    public function it_detects_a_json_file()
    {
        $this->assertJson(file_get_contents($this->menu->getFiles()[0]));
    }

    /** @test */
    public function it_loads_the_json_file()
    {
        $this->menu->load('main');
        $this->assertIsArray($this->menu->getStructure());
        $this->assertNotEmpty($this->menu->getStructure());
    }

    /** @test */
    public function it_loads_a_json_without_having_to_specify_a_name()
    {
        $this->menu->load();
        $this->assertIsArray($this->menu->getStructure());
        $this->assertNotEmpty($this->menu->getStructure());
    }

    /** @test */
    public function it_fails_when_no_menu_files_exist()
    {
        $this->expectException(ErrorException::class);
        $this->menu->setPath($this->emptyFolder);
        $this->menu->load('main');
        $this->menu->setPath($this->menuFolder);
    }

    /** @test */
    public function it_fails_when_no_menu_files_exist_2()
    {
        $this->expectException(FileNotFoundException::class);
        $this->menu->setPath($this->emptyFolder);
        $this->menu->load();
        $this->menu->setPath($this->menuFolder);
    }

    /** @test */
    public function it_fails_when_calling_the_menu_namelessly_but_multiple_menu_files_exist()
    {
        $this->expectException(ErrorException::class);
        $this->menu->setPath($this->multipleFolder);
        $this->menu->load();
        $this->menu->setPath($this->menuFolder);
    }
}
