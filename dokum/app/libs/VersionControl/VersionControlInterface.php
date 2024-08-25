<?php

namespace Dokum\libs\VersionControl;

interface VersionControlInterface
{
    /**
     * Downloads a specific branch from a repository.
     *
     * @param string $repo The repository URL
     * @param string $branch The branch to download
     * @param string $tempDir The temporary directory to download the branch to
     * @param string|null $apiKey Optional API key for authentication
     * @return string|bool The downloaded branch content as a string on success, or false on failure
     */
    public function downloadBranch(string $repo, string $branch, string $tempDir, ?string $apiKey = null): array;
}
