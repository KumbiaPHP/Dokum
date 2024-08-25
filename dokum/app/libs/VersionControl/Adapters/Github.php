<?php

namespace Dokum\libs\VersionControl\Adapters;

use Dokum\libs\VersionControl\VersionControlInterface;
use Dokum\libs\VersionControl\FileDownloader;

/**
 * Class Github
 * 
 * This class implements the VersionControlInterface for GitHub repositories.
 * It provides functionality to download branches from GitHub repositories.
 *
 * @package Dokum\libs\VersionControl\Adapters
 * @implements VersionControlInterface
 */
class Github implements VersionControlInterface
{
    /**
     * @inheritDoc
     */
    public function downloadBranch(string $repo, string $branch, string $tempDir, ?string $apiKey = null): array
    {
        // Extract owner and repo name from the URL
        preg_match('/github\.com\/([^\/]+)\/([^\/]+)/', $repo, $matches);
        $owner = $matches[1];
        $repoName = $matches[2];

        // Construct the GitHub API URL
        $apiUrl = "https://api.github.com/repos/$owner/$repoName/zipball/$branch";

        // Prepare headers
        $headers = ["User-Agent: Dokum-App"];
        if ($apiKey) {
            $headers[] = "Authorization: Bearer $apiKey";
        }

        // Return the prepared CurlHandler
        return (new FileDownloader($apiUrl, $headers))->execute($tempDir);
    }
}
