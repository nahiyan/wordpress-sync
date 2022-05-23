<?php

namespace Vivasoft\WpSync;

class MenuItem
{
    public $id = 0;
    public $title;
    public $link;
    public $page_id;
    public $page = "";

    public static function parse($items, $base_path = "")
    {
        // Create new item
        foreach ($items as $item) {
            Logger::debug(null, "\nItem");
            $menu_item = new MenuItem();
            $menu_item->id = 0;
            $menu_item->page_id = 0;

            // Process the attributes
            $path = "";
            $attributes = $item->attributes();
            foreach ($attributes as $key => $value) {
                switch ($key) {
                    case "page":
                        $menu_item->page = $value;
                        $path = join("/", $base_path == "" ? [$menu_item->page] : [$base_path, $menu_item->page]);

                        // See if the page exists for the menu
                        $page = Page::getFromPath($path);
                        if ($page) {
                            $menu_item->page_id = $page->id;
                        }

                        break;
                    case "title":
                        $menu_item->title = $value;
                        break;
                }
            }

            Logger::debug("Path", $path);

            // Process the children
            $children = $item->children();
            MenuItem::parse($children, $path);
        }

        return $items;
    }
}
