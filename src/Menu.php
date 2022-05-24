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
                $menu_name = substr($item, 0, strlen($item) - strlen("." . $ext));
                $result = wp_get_nav_menu_object($menu_name);
                wp_delete_nav_menu($result);
                Logger::debug("Menu name", $menu_name);
                $created_menu = wp_create_nav_menu($menu_name);
                $menu_id = is_int($created_menu) ? $created_menu : (is_int($result) ? $result : 0);

                $menu = Menu::createFromFile($path, $menu_id);
                Logger::debugJson("menu", $menu);
            }
        }

        return $menus;
    }

    private static function createFromFile($filename, $menu_id)
    {
        $content = file_get_contents($filename);
        $menu_ = new \SimpleXMLElement($content);

        // Logger::debug("Menu", print_r($menu_->children(), true));
        $items = MenuItem::create($menu_->children(), "", $menu_id);

        $menu = new Menu();
        $menu->items = $items;

        return $menu;
    }

}
