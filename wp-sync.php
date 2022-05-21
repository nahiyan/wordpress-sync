<?php

/**
 * Plugin Name: WP Sync
 */

require_once 'consts.php';
require_once 'logger.php';
require_once 'page.php';

if (!function_exists("register_activation_hook")) {
    exit();
}

class WPSync
{
    public static function activate()
    {
        Logger::debug("Activation");
    }
}

register_activation_hook(__FILE__, function () {
    WPSync::activate();
});
$pages = Page::getFromDir(BASE_DIR . "tmp/pages");
// $pages = Page::upsert($pages);

Logger::debug("---------------------------------------");
Logger::debug(json_encode($pages, JSON_PRETTY_PRINT));
// foreach ($pages as $page) {
//     Logger::debug($page->id);
//     Logger::debug($page->name);
//     Logger::debug($page->parentPath);
//     Logger::debug($page->parentId);
//     Logger::debug("==");
//     // Logger::debug(json_encode($page, JSON_PRETTY_PRINT));
// }

// Logger::debug(json_encode(Page::getFromPath("bootcamp")));
