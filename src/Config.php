<?php

namespace Vivasoft\WpSync;

class Config
{
    public static $userId = 1;
    public static $optionsName = "wp_sync_options";
    public static $formats = ["html", "md"];

    public static function getGitHubRepoUrl()
    {
        $options = Config::options();
        if ($options != null) {
            return $options["github_repository_url_0"];
        }

        return "";
    }
    public static function getGitHubRepoBranch()
    {
        $options = Config::options();
        if ($options != null) {
            return $options["github_repository_branch_1"];
        }

        return "";
    }

    private static function options()
    {
        return get_option(Config::$optionsName);
    }

    public static function getBaseDir()
    {
        return path_join(plugin_dir_path(__FILE__), "../");
    }
}
