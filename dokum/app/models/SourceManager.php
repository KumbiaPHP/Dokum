<?php

namespace Dokum\models;

use Dokum\libs\VersionControl\Exceptions\UnrecognizedSourceException;
use Dokum\libs\VersionControl\VersionControlFactory;

/**
 * Class SourceManager
 *
 * Manages downloading files from various configured sources and processing them.
 */
class SourceManager
{
    /**
     * Downloads all files from configured sources.
     *
     * @return void
     * @throws \KumbiaException If there's a general error during the download or processing of the source.     
     * @throws UnrecognizedSourceException If the adapter for service does not found
     */
    public static function downloadAll(): bool
    {
        $sources = \Config::get('sources');

        foreach ($sources as $sourceName => $sourceConfig) {
            SourceManager::download($sourceConfig, $sourceName);
        }

        return true;
    }

    /**
     * Downloads files from a specified source.
     *
     * @param string $sourceName The name of the source to download from.
     *
     * @return bool
     * @throws \KumbiaException If there's a general error during the download or processing of the source.
     * @throws UnrecognizedSourceException If the adapter for service does not found
     */
    public static function downloadBySource(string $sourceName): bool
    {
        $sources = \Config::get('sources');

        if (!isset($sources[$sourceName])) {
            throw new \KumbiaException(sprintf("Source '%s' not found", $sourceName));
        }

        $sourceConfig = $sources[$sourceName];
        return SourceManager::download($sourceConfig, $sourceName);
    }

    /**
     * Downloads and processes the specified source based on configuration.
     *
     * This method attempts to download and process files from a given source based on its configuration.
     *
     * @param array $sourceConfig The configuration details for the source, including URL, tags, and optional token.
     * @param string $sourceName The name of the source
     *
     * @return bool Returns true if the download and processing of the source was successful.
     * @throws \KumbiaException If there's a general error during the download or processing of the source.
     * @throws UnrecognizedSourceException If the adapter for the service does not found.
     */
    private static function download(array $sourceConfig, string $sourceName): bool
    {
        $versionControl = VersionControlFactory::createFromUrl($sourceConfig['url']);

        foreach ($sourceConfig['tags'] as $tag) {
            try {

                $branchDownloader = new RepositoryBranchProcessor($versionControl);
                $branchDownloader->execute(
                    $sourceConfig['url'],
                    $tag,
                    $sourceName,
                    $sourceConfig['token'] ?? null
                );
            } catch (\Exception $e) {
                throw new \KumbiaException(sprintf("Error downloading %s - %s: %s", $sourceName, $tag, $e->getMessage()));
            }
        }

        return true;
    }
}
