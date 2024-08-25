<?php

namespace Dokum\models;

use Dokum\libs\ArchiveExtractor\ArchiveExtractor;
use Dokum\libs\VersionControl\VersionControlInterface;
use Dokum\models\Exceptions\InvalidContentException;

/**
 * Class BranchDownloader
 * 
 * This class is responsible for downloading and processing branches from version control repositories.
 * It utilizes a VersionControlInterface to perform the actual download and an ArchiveExtractor to handle
 * the downloaded content.
 *
 * @package Dokum\models
 */
class BranchDownloader
{    
    /**
     * Constructor for BranchDownloader.
     *
     * @param VersionControlInterface $vcs The version control system interface
     */
    public function __construct(
        private VersionControlInterface $vcs
    )
    { }

    /**
     * Downloads a specific branch from a repository.
     *
     * @param string $repo The repository URL
     * @param string $tag The branch to download
     * @param string $repositoryName The name of the repository
     * @param string|null $apiKey Optional API key for authentication
     *
     * @return bool Returns true if the download was successful
     * @throws \Exception If there's an error during the download process
     */
    public function execute(string $repo, string $tag, string $repositoryName, ?string $apiKey = null): bool
    {
        $tempDir = sys_get_temp_dir() . '/vcs_' . uniqid();

        if (!mkdir($tempDir, 0777, true)) {
            throw new \RuntimeException("Failed to create temporary directory: $tempDir");
        }

        try {
            // Prepare the FileDownloader
            $response = $this->vcs->downloadBranch($repo, $tag, $tempDir, $apiKey);

            // Extract the archives
            $archiveExtractor = ArchiveExtractor::getExtractorByPath($response['fullPath']);
            $archiveExtractor->extract($tempDir, $response['fullPath']);

            // Validate markdown files
            if (!MarkdownFileManager::validateMarkdownAndImageFiles($tempDir)) {
                throw new InvalidContentException("Invalid content: Not all files are markdown (.md) or image files.");
            }

            return true;
        } catch (\Exception $e) {
            // Log the error or handle it as needed
            throw $e;
        } finally {
            // Clean up: remove the temporary directory
            if (file_exists($tempDir)) {
                //MarkdownFileManager::removeDirectory($tempDir);
            }
        }
    }
}