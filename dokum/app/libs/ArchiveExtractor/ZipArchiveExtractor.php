<?php

namespace Dokum\libs\ArchiveExtractor;

use ZipArchive;

/**
 * Class ZipArchiveExtractor
 * 
 * This class implements the ArchiveExtractorInterface for handling ZIP archives.
 * It provides functionality to extract the contents of a ZIP archive.
 */
class ZipArchiveExtractor implements ArchiveExtractorInterface
{
    /**
     * @inheritDoc
     */
    public function extract(string $tempDir, string $archivePath): void
    {
        $zip = new ZipArchive();
        if ($zip->open($archivePath) !== TRUE) {
            throw new ArchiveExtractionException("Failed to open zip archive");
        }

        if (!$zip->extractTo($tempDir)) {
            $zip->close();
            throw new ArchiveExtractionException("Failed to extract zip contents");
        }

        $zip->close();

        unlink($archivePath);
    }
}
