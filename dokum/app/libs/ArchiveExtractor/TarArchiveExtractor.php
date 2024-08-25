<?php

namespace Dokum\libs\ArchiveExtractor;

use Exception;
use PharData;

/**
 * Class TarArchiveExtractor
 * 
 * This class implements the ArchiveExtractorInterface for handling TAR archives.
 * It provides functionality to extract the contents of a TAR archive, including
 * those compressed with gzip (.tar.gz).
 */
class TarArchiveExtractor implements ArchiveExtractorInterface
{
    /**
     * @inheritDoc
     */
    public function extract(string $tempDir, string $archivePath): void
    {

        try {
            $phar = new PharData($archivePath);
            $phar->decompress(); // Creates .tar file
            $tarFile = $tempDir . '/archive.tar';
            $phar = new PharData($tarFile);
            $phar->extractTo($tempDir);
        } catch (Exception $e) {
            throw new ArchiveExtractionException("Failed to extract tar.gz archive: " . $e->getMessage());
        } finally {
            // Clean up temporary files
            if (file_exists($archivePath)) {
                unlink($archivePath);
            }
            if (file_exists($tarFile)) {
                unlink($tarFile);
            }
        }
    }
}
