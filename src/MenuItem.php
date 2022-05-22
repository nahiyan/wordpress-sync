<?php

namespace Vivasoft\WpSync;

class MenuItem
{
    public $id = 0;
    public $title;
    public $link;
    public $page_id;
    public $page = "";

    public static function getItems($menu, $path = "")
    {
        $items = [];

        // Create new item
        $item = new MenuItem();
        $item->id = 0;
        $item->page_id = 0;

        // Process the attributes
        $attributes = (array) $menu->attributes();
        foreach ($attributes["@attributes"] as $key => $value) {
            switch ($key) {
                case "title":
                    $item->title = $value;
                    break;
                case "link":
                    $item->link = $value;
                case "page":
                    $item->page = $path . "/" . $value;
                    if (str_starts_with($item->page, "/")) {
                        $item->page = substr($item->page, 1);
                    }

                    $page = Page::getFromPath($item->page);
                    if ($page) {
                        $item->page_id = $page->id;
                    }
                    break;
            }

        }
        $items[] = $item;

        $children = $menu->children();
        foreach ($children as $child) {
            $items_ = MenuItem::getItems($child, $item->page);
            foreach ($items_ as $item) {
                $items[] = $item;
            }
        }

        return $items;
    }
}
