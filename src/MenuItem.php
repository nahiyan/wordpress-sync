<?php

namespace Vivasoft\WpSync;

class MenuItem
{
    public $id = 0;
    public $title;
    public $link;
    public $page_id;
    public $page = "";

    public static function create($items, $base_path = "", $menu_id, $menu_item_id = 0)
    {
        $menu_items = [];
        // Create new item
        foreach ($items as $item) {
            // Logger::debug(null, "\nItem");
            $menu_item = new MenuItem();
            $menu_item->id = 0;
            $menu_item->page_id = 0;

            // Process the attributes
            $path = "";
            $attributes = $item->attributes();
            foreach ($attributes as $key => $value) {
                switch ($key) {
                    case "page":
                        $menu_item->page = $value->__toString();
                        $path = join("/", $base_path == "" ? [$menu_item->page] : [$base_path, $menu_item->page]);

                        // See if the page exists for the menu
                        $page = Page::getFromPath($path);
                        if ($page) {
                            $menu_item->page_id = $page->id;
                        }

                        break;
                    case "title":
                        $menu_item->title = $value->__toString();
                        break;
                    case "link":
                        $menu_item->link = $value->__toString();
                        break;
                }
            }

            // * Create the menu item
            $menu_item_details = [
                'menu-item-title' => $menu_item->title,
                // 'menu-item-classes' => 'home',
                'menu-item-url' => strlen($menu_item->page) > 0 ? home_url($menu_item->page) : $menu_item->link,
                'menu-item-status' => 'publish',
            ];
            if ($menu_item->page_id != 0) {
                $menu_item_details["menu-item-object"] = "page";
                $menu_item_details["menu-item-object-id"] = $menu_item->page_id;
                $menu_item_details["menu-item-type"] = "post_type";
                if (strlen($menu_item_details["menu-item-title"]) == 0) {
                    $menu_item_details["menu-item-title"] = $page->title;
                }
            }
            if ($menu_item_id != 0) {
                $menu_item_details["menu-item-parent-id"] = $menu_item_id;
            }
            $menu_item_id_ = wp_update_nav_menu_item($menu_id, 0, $menu_item_details);
            if (is_int($menu_item_id_)) {
                $menu_item->id = $menu_item_id_;
            }

            // Add the menu item to the list
            $menu_items[] = $menu_item;

            // Logger::debug("Path", $path);

            // Process the children
            $children = $item->children();
            $menu_items_ = MenuItem::create($children, $path, $menu_id, $menu_item->id);
            foreach ($menu_items_ as $menu_item_) {
                $menu_items[] = $menu_item_;
            }
        }

        return $menu_items;
    }
}
