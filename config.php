<?php

class Config
{
    public static $userId = 1;

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
        return get_option(WP_SYNC_OPTIONS_NAME);
    }
}
