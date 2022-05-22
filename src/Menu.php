<?php

namespace Vivasoft\WpSync;

class Menu
{
    public $id = 0;
    public $name;
    public $items = [];

    public static function createFromDir($dir)
    {
        $dir_listing = scandir($dir);
        $menus = [];

        foreach ($dir_listing as $item) {
            $path = $dir . DIRECTORY_SEPARATOR . $item;

            if ($item == "." || $item == "..") {
                continue;
            }

            $ext = pathinfo($path, PATHINFO_EXTENSION);
            if ($ext == "xml") {
                $menu = Menu::createFromFile($path);
                Logger::debugJson("menu", $menu);
            }
        }

        return $menus;
    }

    private static function createFromFile($filename)
    {
        $items = [];
        $content = file_get_contents($filename);
        $menu_ = new \SimpleXMLElement($content);

        foreach ($menu_->children() as $child) {
            $items_ = MenuItem::getItems($child);
            foreach ($items_ as $item) {
                $items[] = $item;
            }
        }

        $menu = new Menu();
        $menu->items = $items;

        return $menu;
    }

}
