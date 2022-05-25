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
        $title = "Lorem Ipsum";
        if (array_key_exists("title", $attributes)) {
            $title = $attributes["title"];
        }

        Logger::debug("Title", $title);

        return "
            <style>
                summary::-webkit-details-marker {
                    color: #00ACF3;
                    font-size: 125%;
                    margin-right: 2px;
                }
                summary:focus {
                    outline-style: none;
                }
                article > details > summary {
                    font-size: 28px;
                    margin-top: 16px;
                }
                details > p {
                    margin-left: 24px;
                }
                details details {
                    margin-left: 36px;
                }
                details details summary {
                    font-size: 16px;
                }
            </style>

            <details>
                <summary>$title</summary>
                $content
            </details>
            ";
    }
}
