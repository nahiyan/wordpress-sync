<?php

require_once "config.php";

class Page
{
    public $id = 0;
    public $name;
    public $title;
    public $content;
    public $parentId = 0;
    public $parentPath;

    public static function upsertFromDir($dir, $parent_id = 0, $exclusion = "")
    {
        $dir_listing = scandir($dir);
        $pages = [];

        foreach ($dir_listing as $item) {
            $path = $dir . DIRECTORY_SEPARATOR . $item;

            if ($item == "." || $item == ".." || $path == $exclusion) {
                continue;
            }

            Logger::debug("Path", $path);

            $is_dir = is_dir($path);
            $ext = pathinfo($path, PATHINFO_EXTENSION);

            // * Process the page name
            if (strlen($ext) > 0) {
                $name = substr($item, 0, strlen($item) - strlen($ext) - 1);
            } else {
                $name = $item;
            }

            // * Prepare the page
            $page = new Page();
            $page->name = $name;
            $page->title = $item;
            $page->content = "Blank page";
            $page->parentPath = Page::wpPathFromDir($dir);
            $page->parentId = $parent_id;

            // * Fetch the page ID if it exists
            Logger::debug("Parent Path", $page->parentPath);
            $page_ = Page::getFromPath(path_join($page->parentPath, $page->name));
            if ($page_ != null) {
                $page->id = $page_->id;
            }

            // Logger::debug($name);
            // Logger::debug($page->parentPath);

            // * Prepare the page details
            if ($is_dir) {
                // If the item has an associated HTML file, create a blank page
                $pageDefinitionFilePath = path_join($path, $item . ".html");
                // Logger::debug("Page definition path", $pageDefinitionFilePath);
                if (is_file($pageDefinitionFilePath)) {
                    $page->content = file_get_contents($pageDefinitionFilePath);
                }
            } else if ($ext == "html") {
                $page->content = file_get_contents($path);
            }

            // * Upsert the page
            $page->id = $page->upsertWPPost();

            // Logger::debugJson("Page", $page);

            // * Handle the children
            if ($is_dir) {
                $children = Page::upsertFromDir($path, $page->id, $pageDefinitionFilePath);
                foreach ($children as $child) {
                    array_push($pages, $child);
                }
            }

            array_push($pages, $page);
        }
        return $pages;
    }

    public static function getFromPath($path)
    {
        Logger::debug("Get from Path", $path);
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
        $post_definition = [
            'post_title' => wp_strip_all_tags($this->title),
            'post_name' => $this->name,
            'post_content' => $this->content,
            'post_status' => 'publish',
            'post_author' => Config::$userId,
            'post_type' => "page",
            'post_parent' => $this->parentId,
        ];
        if ($this->id != 0) {
            $post_definition['ID'] = $this->id;
        }
        Logger::debugJson("Upsert", $post_definition);

        $post_id = wp_insert_post($post_definition);

        if (is_integer($post_id)) {
            return $post_id;
        }

        return 0;
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
}
