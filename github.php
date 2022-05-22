<?php

require_once "config.php";

class GitHub
{
    public static function sync()
    {
        $repo_url = Config::getGitHubRepoUrl();
        $repo_branch = Config::getGitHubRepoBranch();
        $repo_url_simplified = parse_url($repo_url, PHP_URL_PATH);
        $repo_name = explode("/", $repo_url_simplified)[2];

        // // * Clean the /tmp folder
        system("rm -rf " . path_join(BASE_DIR, "tmp"));
        mkdir(path_join(BASE_DIR, "tmp"));

        // * Download the repository
        $repo_compressed_url = $repo_url . "/archive/refs/heads/" . $repo_branch . ".zip";
        $copy_result = copy($repo_compressed_url, path_join(BASE_DIR, "tmp/compressed.zip"));
        if (!$copy_result) {
            return false;
        }

        // * Decompress the repository
        $zip = new ZipArchive;
        $res = $zip->open(path_join(BASE_DIR, "tmp/compressed.zip"));
        if ($res) {
            $zip->extractTo(path_join(BASE_DIR, "tmp"));
            $zip->close();
        } else {
            return false;
        }

        // * Upsert pages from the content
        $pages = Page::upsertFromDir(BASE_DIR . "tmp" . DIRECTORY_SEPARATOR . $repo_name . "-" . $repo_branch . DIRECTORY_SEPARATOR . "pages");
        Logger::debugJson("Pages", $pages);

        return true;
    }
}
