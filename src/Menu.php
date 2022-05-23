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
                $result = wp_get_nav_menu_object("javascript-bootcamp");
                wp_delete_nav_menu($result);

                $menu = Menu::loadFromFile($path);
                // Logger::debugJson("menu", $menu);
            }
        }

        return $menus;
    }

    private static function loadFromFile($filename)
    {
        $content = file_get_contents($filename);
        $menu_ = new \SimpleXMLElement($content);

        // Logger::debug("Menu", print_r($menu_->children(), true));
        $items = MenuItem::parse($menu_->children());

        $menu = new Menu();
        $menu->items = $items;

        return $menu;
    }

}
