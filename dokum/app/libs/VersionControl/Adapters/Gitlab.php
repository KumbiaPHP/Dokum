<?php

namespace Dokum\libs\VersionControl\Adapters;

use Dokum\libs\VersionControl\VersionControlInterface;
use Dokum\libs\VersionControl\FileDownloader;

/**
 * Class Gitlab
 * 
 * This class implements the VersionControlInterface for GitLab repositories.
 * It provides functionality to download branches from GitLab repositories.
 *
 * @package Dokum\libs\VersionControl\Adapters
 * @implements VersionControlInterface
 */
class Gitlab implements VersionControlInterface
{
    /**
     * @inheritDoc
     */
    public function downloadBranch(string $repo, string $branch, string $tempDir, ?string $apiKey = null): array
    {
        // Extract owner and repo name from the URL
        preg_match('/gitlab\.com\/([^\/]+)\/([^\/]+)/', $repo, $matches);
        $owner = $matches[1];
        $repoName = $matches[2];

        // Construct the GitLab API URL
        $apiUrl = "https://gitlab.com/api/v4/projects/" . urlencode("$owner/$repoName") . "/repository/archive.zip?sha=$branch";

        // Prepare headers
        $headers = ["User-Agent: Dokum-App"];
        if ($apiKey) {
            $headers[] = "Authorization: Bearer $apiKey";
        }

        // Return the prepared CurlHandler
        return (new FileDownloader($apiUrl, $headers))->execute($tempDir);
    }
}
