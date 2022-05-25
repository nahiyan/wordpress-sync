<?php

namespace Vivasoft\WpSync;

class Shortcode
{
    public static function navMenu($attributes = [])
    {
        if (array_key_exists("name", $attributes)) {
            $name = $attributes["name"];
            wp_nav_menu(["menu" => $name]);
        }
        return null;
    }

    public static function collapsableSection($attributes = [], $content = "")
    {
        return $content;
    }
}
