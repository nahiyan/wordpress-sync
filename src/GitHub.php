<?php

namespace Vivasoft\WpSync;

class GitHub
{
    public static function sync()
    {
        $repo_url = Config::getGitHubRepoUrl();
        $repo_branch = Config::getGitHubRepoBranch();
        $repo_url_simplified = parse_url($repo_url, PHP_URL_PATH);
        $repo_name = explode("/", $repo_url_simplified)[2];

        // // * Clean the /tmp folder
        system("rm -rf " . path_join(Config::getBaseDir(), "tmp"));
        mkdir(path_join(Config::getBaseDir(), "tmp"));

        // * Download the repository
        $repo_compressed_url = $repo_url . "/archive/refs/heads/" . $repo_branch . ".zip";
        $copy_result = copy($repo_compressed_url, path_join(Config::getBaseDir(), "tmp/compressed.zip"));
        if (!$copy_result) {
            return false;
        }

        // * Decompress the repository
        $zip = new \ZipArchive;
        $res = $zip->open(path_join(Config::getBaseDir(), "tmp/compressed.zip"));
        if ($res) {
            $zip->extractTo(path_join(Config::getBaseDir(), "tmp"));
            $zip->close();
        } else {
            return false;
        }

        $decompressed_repo_path = Config::getBaseDir() . "tmp" . DIRECTORY_SEPARATOR . $repo_name . "-" . $repo_branch;

        // * Upsert pages from the content
        $pages = Page::upsertFromDir(path_join($decompressed_repo_path, "pages"));
        Logger::debugJson("Pages", $pages);

        // * Upsert menus from the content
        $menus = Page::upsertFromDir(path_join($decompressed_repo_path, "menus"));
        Logger::debugJson("Menus", $menus);

        return true;
    }
}
