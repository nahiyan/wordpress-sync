<?php

class Page
{
    public $name;
    public $children;
    public $content;
    public $parentId;
}

function get_pages($dir)
{
    $dir_listing = scandir($dir);

    foreach ($dir_listing as $item) {
        if ($item == "." || $item == "..") {
            continue;
        }

        $pages = [];

        $path = $dir . "/" . $item;
        $is_dir = is_dir($path);
        $ext = pathinfo($path, PATHINFO_EXTENSION);

        if ($ext == "html") {
            $page = new Page();
            $page->name = $item;
            array_push($pages, $page);
        }

        if ($is_dir) {
            $children = get_pages($path);
            array_push($pages, $children);
        }

        return $pages;
    }
}

$pages = get_pages("./data/pages");
print_r($pages);
