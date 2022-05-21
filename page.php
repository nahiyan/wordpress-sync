<?php

class Page
{
    public $id = 0;
    public $name;
    public $title;
    public $content;
    public $parentId = 0;
    public $parentPath;

    public static function getFromDir($dir, $parent_id = 0)
    {
        $dir_listing = scandir($dir);
        $pages = [];

        foreach ($dir_listing as $item) {
            if ($item == "." || $item == "..") {
                continue;
            }

            $path = $dir . DIRECTORY_SEPARATOR . $item;
            $is_dir = is_dir($path);
            $ext = pathinfo($path, PATHINFO_EXTENSION);

            // Process the page name
            if (strlen($ext) > 0) {
                $name = substr($item, 0, strlen($item) - strlen($ext) - 1);
            } else {
                $name = $item;
            }

            // Prepare the page
            $page = new Page();
            $page->name = $name;
            $page->title = $item;
            $page->content = "Blank page";
            $page->parentPath = Page::wpPathFromDir($dir);
            $page->parentId = $parent_id;

            // Fetch the page ID if it exists
            $page_ = Page::getFromPath($page->parentPath . DIRECTORY_SEPARATOR . $page->name);
            if ($page_ != null) {
                $page->id = $page_->id;
            }

            Logger::debug($name);
            Logger::debug($page->parentPath);

            if ($is_dir) {
                // If the item doesn't have an associated HTML file, create a blank page
                if (!is_file($path . DIRECTORY_SEPARATOR . $item . ".html")) {
                    array_push($pages, $page);
                }

                // Handle the children
                $children = Page::getFromDir($path, $page->id);
                foreach ($children as $child) {
                    array_push($pages, $child);
                }
            } else if ($ext == "html") {
                $page->content = file_get_contents($path);
                array_push($pages, $page);
            }
        }
        return $pages;
    }

    public static function getFromPath($path)
    {
        $result = get_page_by_path($path);

        if ($result == null) {
            return null;
        }

        return Page::getFromWPPost($result);
    }

    public static function getFromWPPost($wpPost)
    {
        $page = new Page();
        $page->id = $wpPost->ID;
        $page->name = $wpPost->post_name;
        $page->title = $wpPost->post_title;
        $page->content = $wpPost->post_content;
        $page->parentId = $wpPost->post_parent;

        return $page;
    }

    public function upsertWPPost()
    {
        $post = wp_insert_post([
            "ID" => $this->id,
            'post_title' => wp_strip_all_tags($this->title),
            'post_content' => $this->content,
            'post_status' => 'publish',
            'post_author' => Config::$userId,
            'post_type' => "page",
        ]);

        return $post;
    }

    public static function wpPathFromDir($wpPath)
    {
        $redundancy = BASE_DIR . "tmp" . DIRECTORY_SEPARATOR . "pages";
        if (str_starts_with($wpPath, $redundancy) . DIRECTORY_SEPARATOR) {
            $resultingPath = substr($wpPath, strlen($redundancy . DIRECTORY_SEPARATOR));
        } else if (str_starts_with($wpPath, $redundancy)) {
            $resultingPath = substr($wpPath, strlen($redundancy));
        } else {
            return $wpPath;
        }

        if (strlen($resultingPath) == 0) {
            return "/";
        }

        return $resultingPath;
    }

    public static function upsert($pages)
    {
        $pagesMap = [];

        foreach ($pages as $page) {
            if (str_ends_with($page->parentPath, $page->name)) {
                // If parent and current page's name are the same
                $full_path = $page->parentPath;
            } else {
                $full_path = $page->parentPath . DIRECTORY_SEPARATOR . $page->name;
            }

            $pagesMap[$full_path] = $page;

            if (array_key_exists($page->parentPath, $pagesMap)) {
                $page->parentId = $pagesMap[$page->parentPath]->id;
            }

            // Get ID if the page exists
            $page_ = Page::getFromPath($full_path);
            if ($page_ != null) {
                $page->id = $page_->id;
            }
        }

        return $pages;
    }
}
